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
    wp_enqueue_script('rocert-anaf', ROCERT_CORE_URL . '/assets/js/anaf.js', ['rocert'], ROCERT_CORE_VERSION, ['strategy' => 'defer', 'in_footer' => true]);
});

/* Notification recipients come from the environment, not the database, so stage/prod can differ. */
add_filter('fluentform/email_to', static function ($address, $notification, $submitted_data, $form) {
    return rocert_form_key((int) $form->id) ? ROCERT_FORM_RECIPIENT : $address;
}, 10, 4);

/* Pages are served from the Cloudflare cache, where a nonce would go stale: never require it. Spam is handled by Turnstile. */
add_filter('fluentform/nonce_verify', '__return_false');
