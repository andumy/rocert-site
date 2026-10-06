<?php
/**
 * Integration points with the rocert API. Everything the site sends to or reads from the API goes
 * through these three hooks:
 *
 *   do_action('rocert/form/submitted', string $form, array $data, int $entry_id, string $lang)
 *       after a form submission (and after the notification e-mail); $form is 'request'.
 *   apply_filters('rocert/certificate/lookup', null, string $serial)
 *   apply_filters('rocert/company/lookup', null, string $cui)
 */
defined('ABSPATH') || exit;

/* Company data: forwarded to the rocert API (which owns the ANAF integration) once ROCERT_API_URL is set. */
add_filter('rocert/company/lookup', static function ($company, string $cui) {
    if ($company !== null || !ROCERT_API_URL) {
        return $company;
    }
    $response = wp_remote_get(ROCERT_API_URL . '/api/public/companies/' . rawurlencode($cui), [
        'timeout' => 8,
        'headers' => ['Accept' => 'application/json', 'Authorization' => 'Bearer ' . ROCERT_API_TOKEN],
    ]);
    if (is_wp_error($response)) {
        return $response;
    }
    $code = wp_remote_retrieve_response_code($response);
    if ($code === 404) {
        return false;
    }
    $body = json_decode(wp_remote_retrieve_body($response), true);
    if ($code !== 200 || !is_array($body)) {
        return new WP_Error('rocert_api', 'Unexpected API response', ['status' => $code]);
    }
    $data = $body['data'] ?? $body;
    return array_filter([
        'name' => $data['name'] ?? '',
        'address' => $data['address'] ?? '',
        'county' => $data['county'] ?? '',
        'city' => $data['city'] ?? '',
        'postal_code' => $data['postal_code'] ?? '',
        'reg_com' => $data['reg_com'] ?? ($data['registration_number'] ?? ''),
        'phone' => $data['phone'] ?? '',
        'iban' => $data['iban'] ?? '',
    ], 'strlen') ?: false;
}, 10, 2);

/*
 * Certificate lookup: the rocert API, once ROCERT_API_URL is set.
 * Expected contract — GET {ROCERT_API_URL}/api/public/certificates/{series} → 200 with
 * CertificateLookup::toArray() ({series, client, valid, reason, reason_label, standard, number}, plus
 * `scope` when the API adds it), or 404 for an unknown or malformed series.
 */
add_filter('rocert/certificate/lookup', static function ($result, string $serial) {
    if ($result !== null || !ROCERT_API_URL) {
        return $result;
    }
    $response = wp_remote_get(ROCERT_API_URL . '/api/public/certificates/' . rawurlencode(strtolower($serial)), [
        'timeout' => 8,
        'headers' => array_filter(['Accept' => 'application/json', 'Authorization' => ROCERT_API_TOKEN ? 'Bearer ' . ROCERT_API_TOKEN : null]),
    ]);
    if (is_wp_error($response)) {
        return $response;
    }
    $code = wp_remote_retrieve_response_code($response);
    if ($code === 404) {
        return false;
    }
    $body = json_decode(wp_remote_retrieve_body($response), true);
    $data = is_array($body) ? ($body['data'] ?? $body) : null;
    if ($code !== 200 || !is_array($data) || !array_key_exists('valid', $data)) {
        return new WP_Error('rocert_api', 'Unexpected API response', ['status' => $code]);
    }
    return [
        'valid' => (bool) $data['valid'],
        'reason' => strtolower((string) ($data['reason'] ?? '')),
        'organization' => (string) ($data['client'] ?? ''),
        'standard' => (string) ($data['standard'] ?? ''),
        'scope' => (string) ($data['scope'] ?? ''),
    ];
}, 10, 2);

/*
 * Certification requests become prospect clients in the rocert API:
 * POST {ROCERT_API_URL}/api/site/clients, authorised with ROCERT_API_TOKEN (Bearer).
 * Sent server-side right after the submission; failures go to an outbox retried hourly by WP-Cron,
 * and Fluent Forms keeps every entry regardless.
 */
add_action('rocert/form/submitted', static function (string $form, array $data, int $entry_id, string $lang = 'ro'): void {
    if ($form !== 'request' || !ROCERT_API_URL || !ROCERT_API_TOKEN) {
        return;
    }
    $payload = [
        'source' => 'website',
        'form' => 'request',
        'language' => $lang,
        'entry_id' => $entry_id,
        'submitted_at' => wp_date('c'),
        'data' => rocert_api_request_data($data),
    ];
    if (!rocert_api_send_client($payload)) {
        $outbox = (array) get_option('rocert_api_outbox', []);
        $outbox[$entry_id] = ['payload' => $payload, 'attempts' => 1];
        update_option('rocert_api_outbox', $outbox, false);
    }
}, 10, 4);

/*
 * The request payload contract with the rocert API (POST /api/site/clients, documented in the rocert repo):
 * exactly these keys, every one always present (null when empty), nothing else. The API rejects unknown
 * keys, so a new form field means updating this map, the API's validation and its DTO together.
 */
const ROCERT_API_REQUEST_FIELDS = [
    'request_type' => 'string', 'cui' => 'string', 'company_name' => 'string', 'address' => 'string', 'city' => 'string',
    'county' => 'string', 'postal_code' => 'string', 'reg_com' => 'string', 'iban' => 'string', 'bank' => 'string',
    'phone' => 'string', 'mobile' => 'string', 'fax' => 'string', 'email' => 'string', 'website' => 'string',
    'manager_name' => 'string', 'manager_role' => 'string', 'manager_phone' => 'string',
    'contact_name' => 'string', 'contact_role' => 'string', 'contact_phone' => 'string', 'contact_email' => 'string', 'contact_fax' => 'string',
    'standards' => 'list', 'standard_other' => 'string', 'integrated_system' => 'string', 'integration_level' => 'list',
    'scope_description' => 'string', 'ea_codes' => 'list',
    'total_employees' => 'int', 'loc0_address' => 'string', 'loc0_activity' => 'string',
    'loc0_staff_p' => 'int', 'loc0_staff_t' => 'int', 'loc0_staff_r' => 'int', 'loc0_shift_1' => 'int', 'loc0_shift_2' => 'int', 'loc0_shift_3' => 'int',
    'locations' => 'rows', 'job_roles' => 'rows',
    'advanced_technology' => 'string', 'regulated_field' => 'string', 'many_processes' => 'string', 'design_development' => 'string',
    'design_staff' => 'int', 'shift_differences' => 'string', 'repetitive_processes' => 'string', 'unique_processes' => 'string',
    'shift_details' => 'string', 'outsourced_processes' => 'string',
    'existing_standards' => 'string', 'existing_certificate' => 'string', 'existing_body' => 'string',
    'particularities' => 'list', 'particularities_details' => 'string', 'iqnet' => 'string', 'audit_mode' => 'string',
    'implementation' => 'string', 'consultant' => 'string', 'planned_audit_date' => 'string', 'source' => 'list',
    'filled_by_name' => 'string', 'filled_by_role' => 'string', 'signature_filler' => 'string',
    'signer_manager_name' => 'string', 'signer_manager_role' => 'string', 'signature_manager' => 'string',
];

/** Row columns per repeater field: 'string' or 'int' */
const ROCERT_API_ROW_FIELDS = [
    'locations' => ['address' => 'string', 'activity' => 'string', 'staff_p' => 'int', 'staff_t' => 'int', 'staff_r' => 'int', 'shift_1' => 'int', 'shift_2' => 'int', 'shift_3' => 'int'],
    'job_roles' => ['role' => 'string', 'count' => 'int'],
];

/** @param mixed $value */
function rocert_api_value(string $type, $value)
{
    if ($type === 'list') {
        return array_values(array_unique(array_filter(array_map('strval', (array) $value), 'strlen')));
    }
    $value = is_scalar($value) ? trim((string) $value) : '';
    if ($value === '') {
        return null;
    }
    return $type === 'int' ? (is_numeric($value) ? max(0, (int) $value) : null) : $value;
}

/** Submitted form data reduced to the API contract: every contract key, typed, nothing else. */
function rocert_api_request_data(array $data): array
{
    $out = [];
    foreach (ROCERT_API_REQUEST_FIELDS as $key => $type) {
        if ($type !== 'rows') {
            $out[$key] = rocert_api_value($type, $data[$key] ?? null);
            continue;
        }
        $rows = [];
        foreach (rocert_form_rows((string) ($data[$key] ?? '')) ?? [] as $row) {
            $clean = [];
            foreach (ROCERT_API_ROW_FIELDS[$key] as $column => $column_type) {
                $clean[$column] = rocert_api_value($column_type, $row[$column] ?? null);
            }
            if (array_filter($clean, static fn ($v) => $v !== null)) {
                $rows[] = $clean;
            }
        }
        $out[$key] = $rows;
    }
    return $out;
}

/**
 * POSTs a request to the rocert API. True when there is nothing left to retry: accepted (200/201), or rejected
 * for good (4xx other than 408/429, e.g. 422 when the payload breaks the contract; logged, the entry stays in Fluent Forms).
 */
function rocert_api_send_client(array $payload): bool
{
    $response = wp_remote_post(ROCERT_API_URL . '/api/site/clients', [
        'timeout' => 10,
        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . ROCERT_API_TOKEN],
        'body' => wp_json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
    if (in_array($code, [200, 201], true)) {
        return true;
    }
    $detail = is_wp_error($response) ? $response->get_error_message() : $code . ' ' . mb_substr(wp_remote_retrieve_body($response), 0, 2000);
    if ($code >= 400 && $code < 500 && !in_array($code, [408, 429], true)) {
        error_log(sprintf('[rocert-api] client request entry %d REJECTED, not retried: %s', $payload['entry_id'] ?? 0, $detail));
        return true;
    }
    error_log(sprintf('[rocert-api] client request entry %d failed, will retry: %s', $payload['entry_id'] ?? 0, $detail));
    return false;
}

add_action('init', static function (): void {
    /* Not during `wp core install`: the options table does not exist yet. */
    if (wp_installing() || !is_blog_installed()) {
        return;
    }
    if (!wp_next_scheduled('rocert_api_outbox_retry')) {
        wp_schedule_event(time() + HOUR_IN_SECONDS, 'hourly', 'rocert_api_outbox_retry');
    }
});
add_action('rocert_api_outbox_retry', static function (): void {
    $outbox = (array) get_option('rocert_api_outbox', []);
    if (!$outbox || !ROCERT_API_URL || !ROCERT_API_TOKEN) {
        return;
    }
    foreach ($outbox as $entry_id => $item) {
        if (rocert_api_send_client($item['payload'])) {
            unset($outbox[$entry_id]);
        } elseif (++$outbox[$entry_id]['attempts'] >= 48) {
            error_log(sprintf('[rocert-api] giving up on entry %d after 48 attempts (still in Fluent Forms entries)', $entry_id));
            unset($outbox[$entry_id]);
        }
    }
    update_option('rocert_api_outbox', $outbox, false);
});

/*
 * Demo data so the verification page and the ANAF autofill can be shown on local/stage before the
 * API endpoints exist. Never active in production or once ROCERT_API_URL is configured.
 */
if (wp_get_environment_type() !== 'production' && !ROCERT_API_URL) {
    add_filter('rocert/certificate/lookup', static function ($result, string $serial) {
        $demo = [
            '3F2A9C1E-4B7D-4E2A-9C51-7A1D2E3F4B5C' => ['valid' => true, 'organization' => 'EXEMPLU PRODUCȚIE SRL (DEMO)', 'standard' => 'SR EN ISO 9001:2015', 'scope' => 'Producția de construcții metalice și prelucrări mecanice'],
            '8C1D4E7F-2A3B-4C5D-8E9F-0A1B2C3D4E5F' => ['valid' => false, 'reason' => 'expirat', 'organization' => 'EXEMPLU LOGISTIC SA (DEMO)', 'standard' => 'SR EN ISO 14001:2015', 'scope' => 'Transport rutier de mărfuri și depozitare'],
        ];
        return $result ?? ($demo[$serial] ?? false);
    }, 20, 2);
    add_filter('rocert/company/lookup', static function ($company, string $cui) {
        return $company ?? ['name' => 'EXEMPLU DEMO SRL', 'address' => 'Str. Exemplului nr. 10', 'city' => 'București', 'county' => 'București', 'postal_code' => '030000', 'reg_com' => 'J40/1234/2010'];
    }, 20, 2);
}
