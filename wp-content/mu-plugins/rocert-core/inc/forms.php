<?php
/**
 * Fluent Forms glue: every submission is e-mailed by Fluent Forms itself (notification configured on
 * the form, recipient = ROCERT_FORM_RECIPIENT) and then handed to `rocert/form/submitted`.
 */
defined('ABSPATH') || exit;

/** @return array<string, int> 'contact_ro' | 'request_en' … => Fluent Forms form id (written by the seed) */
function rocert_forms(): array
{
    return (array) get_option('rocert_forms', []);
}

function rocert_form_key(int $form_id): ?string
{
    $key = array_search($form_id, rocert_forms(), true);
    return $key === false ? null : preg_replace('/_(ro|en)$/', '', (string) $key);
}

/** Language a form belongs to ('ro' | 'en'), from its seed key */
function rocert_form_lang(int $form_id): string
{
    $key = (string) array_search($form_id, rocert_forms(), true);
    return str_ends_with($key, '_en') ? 'en' : 'ro';
}

add_action('fluentform/submission_inserted', static function ($entry_id, $form_data, $form): void {
    $key = rocert_form_key((int) $form->id);
    if (!$key) {
        return;
    }
    $data = array_filter((array) $form_data, static fn ($k) => !str_starts_with((string) $k, '_') && $k !== 'cf-turnstile-response', ARRAY_FILTER_USE_KEY);
    do_action('rocert/form/submitted', $key, $data, (int) $entry_id, rocert_form_lang((int) $form->id));
}, 20, 3);

/* The request form gets the ANAF autofill. */
add_action('fluentform/before_form_render', static function ($form): void {
    if (rocert_form_key((int) $form->id) !== 'request') {
        return;
    }
    rocert_enqueue_front();
    wp_enqueue_script('rocert-anaf', ROCERT_CORE_URL . '/assets/js/anaf.js', ['rocert'], ROCERT_CORE_VERSION . '.' . filemtime(ROCERT_CORE_DIR . '/assets/js/anaf.js'), ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('rocert-request', ROCERT_CORE_URL . '/assets/js/request.js', [], ROCERT_CORE_VERSION . '.' . filemtime(ROCERT_CORE_DIR . '/assets/js/request.js'), ['strategy' => 'defer', 'in_footer' => true]);
});

/*
 * Request form fields that hold structured values (see content/lib/forms.php): rows saved as JSON by the
 * repeaters, and signatures saved as PNG data URLs by the signature pads.
 */
const ROCERT_ROW_FIELDS = ['locations', 'job_roles'];
const ROCERT_SIGNATURE_FIELDS = ['signature_filler', 'signature_manager'];

/** @return list<array<string, string>>|null rows, or null when $value is not a valid row list */
function rocert_form_rows(string $value): ?array
{
    if (trim($value) === '') {
        return [];
    }
    /* Decoded as sent; failing that, without the slashes WordPress adds to request data */
    $rows = json_decode($value, true) ?? json_decode(wp_unslash($value), true);
    if (!is_array($rows) || !array_is_list($rows) || count($rows) > 30) {
        return null;
    }
    foreach ($rows as $row) {
        if (!is_array($row) || count($row) > 12) {
            return null;
        }
        foreach ($row as $key => $cell) {
            if (!is_string($key) || !is_string($cell) || mb_strlen($cell) > 255) {
                return null;
            }
        }
    }
    return $rows;
}

/** Empty, or a PNG data URL within the rocert API contract: at most 150,000 characters and 300×100 px. */
function rocert_form_signature_ok(string $value): bool
{
    if ($value === '') {
        return true;
    }
    if (strlen($value) > 150000 || preg_match('#^data:image/png;base64,([A-Za-z0-9+/]+=*)$#', $value, $m) !== 1) {
        return false;
    }
    $size = @getimagesizefromstring((string) base64_decode($m[1], true));
    return $size !== false && $size[2] === IMAGETYPE_PNG && $size[0] <= 300 && $size[1] <= 100;
}

add_filter('fluentform/validation_errors', static function ($errors, $data, $form) {
    if (rocert_form_key((int) $form->id) !== 'request') {
        return $errors;
    }
    $en = rocert_form_lang((int) $form->id) === 'en';
    $invalid = $en ? 'Invalid value.' : 'Valoare invalidă.';
    /* The rocert API's contract limits, checked here so the visitor sees them instead of the API rejecting the request */
    if (!preg_match('/^\s*(RO)?\s*\d{2,10}\s*$/i', (string) ($data['cui'] ?? ''))) {
        $errors['cui'] = ['format' => $en ? 'Enter a valid tax ID, e.g. RO12345678.' : 'Introduceți un CUI valid, ex. RO12345678.'];
    }
    $long = ['scope_description', 'repetitive_processes', 'unique_processes', 'shift_details', 'outsourced_processes', 'particularities_details'];
    foreach (ROCERT_API_REQUEST_FIELDS as $name => $type) {
        $max = in_array($name, $long, true) ? 5000 : 255;
        if ($type === 'string' && !in_array($name, ROCERT_SIGNATURE_FIELDS, true) && is_string($data[$name] ?? null) && mb_strlen(trim($data[$name])) > $max) {
            $errors[$name] = ['max' => $en ? "At most {$max} characters." : "Maximum {$max} de caractere."];
        }
    }
    foreach (ROCERT_ROW_FIELDS as $name) {
        if (rocert_form_rows((string) ($data[$name] ?? '')) === null) {
            $errors[$name] = ['format' => $invalid];
        }
    }
    foreach (ROCERT_SIGNATURE_FIELDS as $name) {
        if (!rocert_form_signature_ok((string) ($data[$name] ?? ''))) {
            $errors[$name] = ['format' => $invalid];
        }
    }
    return $errors;
}, 10, 3);

/* Entries and e-mails: rows as a table, signatures as an image in wp-admin and a note in e-mails
   (mail clients block inline images; the signature stays on the entry). */
add_filter('fluentform/response_render_input_text', static function ($value, $field, $form_id, $is_html = false) {
    $name = $field['raw']['attributes']['name'] ?? '';
    if (in_array($name, ROCERT_SIGNATURE_FIELDS, true)) {
        if (!is_string($value) || $value === '') {
            return '';
        }
        $en = rocert_form_lang((int) $form_id) === 'en';
        /* Images for people who can open entries (wp-admin, also over AJAX/REST); e-mails go out in the visitor's request */
        return current_user_can('fluentform_entries_viewer') || current_user_can('manage_options')
            ? (rocert_form_signature_ok($value) ? '<img src="' . esc_attr($value) . '" alt="" style="max-width:320px;height:auto;border:1px solid #ddd;border-radius:6px;background:#fff">' : '')
            : ($en ? 'Signed (the signature is on the entry in wp-admin)' : 'Semnat (semnătura se vede la înregistrare, în wp-admin)');
    }
    if (!in_array($name, ROCERT_ROW_FIELDS, true) || !is_string($value)) {
        return $value;
    }
    $rows = rocert_form_rows($value);
    if (!$rows) {
        return $rows === [] ? '' : $value;
    }
    $keys = array_keys(array_merge(...$rows));
    $labels = rocert_form_lang((int) $form_id) === 'en'
        ? ['address' => 'Address', 'activity' => 'Activity', 'staff_p' => 'P', 'staff_t' => 'T', 'staff_r' => 'R', 'shift_1' => 'Shift 1', 'shift_2' => 'Shift 2', 'shift_3' => 'Shift 3', 'role' => 'Job title', 'count' => 'Employees']
        : ['address' => 'Adresa', 'activity' => 'Activitate', 'staff_p' => 'P', 'staff_t' => 'T', 'staff_r' => 'R', 'shift_1' => 'Schimb 1', 'shift_2' => 'Schimb 2', 'shift_3' => 'Schimb 3', 'role' => 'Meserie', 'count' => 'Nr. angajați'];
    if (!$is_html) {
        return implode('; ', array_map(static fn ($r) => implode(', ', array_map(static fn ($k) => ($labels[$k] ?? $k) . ': ' . ($r[$k] ?? ''), array_filter($keys, static fn ($k) => ($r[$k] ?? '') !== ''))), $rows));
    }
    $head = '<tr>' . implode('', array_map(static fn ($k) => '<th style="text-align:left;padding:4px 8px;border-bottom:1px solid #ddd">' . esc_html($labels[$k] ?? $k) . '</th>', $keys)) . '</tr>';
    $body = implode('', array_map(static fn ($r) => '<tr>' . implode('', array_map(static fn ($k) => '<td style="padding:4px 8px;border-bottom:1px solid #eee">' . esc_html($r[$k] ?? '') . '</td>', $keys)) . '</tr>', $rows));
    return '<table style="border-collapse:collapse;font-size:13px">' . $head . $body . '</table>';
}, 20, 4);

/* Notification recipients come from the environment, not the database, so stage/prod can differ. */
add_filter('fluentform/email_to', static function ($address, $notification, $submitted_data, $form) {
    return rocert_form_key((int) $form->id) ? ROCERT_FORM_RECIPIENT : $address;
}, 10, 4);

/* Pages are served from the Cloudflare cache, where a nonce would go stale: never require it. Spam is handled by Turnstile. */
add_filter('fluentform/nonce_verify', '__return_false');
