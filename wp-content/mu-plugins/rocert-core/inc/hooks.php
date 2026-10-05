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
        'data' => array_diff_key($data, array_flip(['gdpr-agreement', 'cf-turnstile-response'])),
    ];
    if (!rocert_api_send_client($payload)) {
        $outbox = (array) get_option('rocert_api_outbox', []);
        $outbox[$entry_id] = ['payload' => $payload, 'attempts' => 1];
        update_option('rocert_api_outbox', $outbox, false);
    }
}, 10, 4);

/** POSTs a request to the rocert API; true on 200/201 */
function rocert_api_send_client(array $payload): bool
{
    $response = wp_remote_post(ROCERT_API_URL . '/api/site/clients', [
        'timeout' => 10,
        'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json', 'Authorization' => 'Bearer ' . ROCERT_API_TOKEN],
        'body' => wp_json_encode($payload, JSON_UNESCAPED_UNICODE),
    ]);
    $code = is_wp_error($response) ? 0 : (int) wp_remote_retrieve_response_code($response);
    if (!in_array($code, [200, 201], true)) {
        error_log(sprintf('[rocert-api] client request entry %d failed: %s', $payload['entry_id'] ?? 0, is_wp_error($response) ? $response->get_error_message() : $code . ' ' . wp_remote_retrieve_body($response)));
        return false;
    }
    return true;
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
            '8C1D4E7F-2A3B-4C5D-8E9F-0A1B2C3D4E5F' => ['valid' => false, 'organization' => 'EXEMPLU LOGISTIC SA (DEMO)', 'standard' => 'SR EN ISO 14001:2015', 'scope' => 'Transport rutier de mărfuri și depozitare'],
        ];
        return $result ?? ($demo[$serial] ?? false);
    }, 20, 2);
    add_filter('rocert/company/lookup', static function ($company, string $cui) {
        return $company ?? ['name' => 'EXEMPLU DEMO SRL', 'address' => 'Str. Exemplului nr. 10', 'city' => 'București', 'county' => 'București', 'postal_code' => '030000', 'reg_com' => 'J40/1234/2010'];
    }, 20, 2);
}
