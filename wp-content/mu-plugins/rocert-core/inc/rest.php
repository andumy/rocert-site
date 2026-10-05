<?php
/**
 * Public REST endpoints used by the front end. No nonces on purpose: pages are served from the
 * Cloudflare cache, so a nonce would be stale; abuse is limited per IP instead.
 */
defined('ABSPATH') || exit;

add_action('rest_api_init', static function (): void {
    register_rest_route('rocert/v1', '/certificates/verify', [
        'methods' => 'POST',
        'permission_callback' => '__return_true',
        'args' => [
            'serial' => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field'],
        ],
        'callback' => 'rocert_rest_verify',
    ]);
    register_rest_route('rocert/v1', '/company', [
        'methods' => 'GET',
        'permission_callback' => '__return_true',
        'args' => ['cui' => ['type' => 'string', 'required' => true, 'sanitize_callback' => 'sanitize_text_field']],
        'callback' => 'rocert_rest_company',
    ]);
});

function rocert_rest_verify(WP_REST_Request $request): WP_REST_Response
{
    if (!rocert_rate_limit('verify', 20)) {
        return new WP_REST_Response(['status' => 'rate_limited'], 429);
    }
    $serial = strtoupper(trim(preg_replace('/\s+/', '', (string) $request->get_param('serial'))));
    if (!preg_match('/^[0-9A-F]{8}(-?[0-9A-F]{4}){3}-?[0-9A-F]{12}$/', $serial)) {
        return new WP_REST_Response(['status' => 'invalid'], 200);
    }
    $hex = str_replace('-', '', $serial);
    $serial = sprintf('%s-%s-%s-%s-%s', substr($hex, 0, 8), substr($hex, 8, 4), substr($hex, 12, 4), substr($hex, 16, 4), substr($hex, 20));

    /**
     * Certificate lookup by serial (the unique code printed on the certificate) — connect the rocert API here.
     *
     * @param array|false|null|WP_Error $result null = no lookup configured; false = not found;
     *                                          array{valid: bool, organization: string, standard: string, scope: string}
     * @param string $serial normalised serial: upper-case UUID with dashes
     */
    $result = apply_filters('rocert/certificate/lookup', null, $serial);

    if ($result === null || is_wp_error($result)) {
        return new WP_REST_Response(['status' => 'unavailable'], 503);
    }
    if ($result === false) {
        return new WP_REST_Response(['status' => 'not_found'], 200);
    }
    return new WP_REST_Response([
        'status' => 'found',
        'valid' => (bool) ($result['valid'] ?? false),
        'reason' => in_array($result['reason'] ?? '', ['expirat', 'suspendat', 'retras'], true) ? $result['reason'] : '',
        'serial' => $serial,
        'organization' => (string) ($result['organization'] ?? ''),
        'standard' => (string) ($result['standard'] ?? ''),
        'scope' => (string) ($result['scope'] ?? ''),
    ], 200);
}

function rocert_rest_company(WP_REST_Request $request): WP_REST_Response
{
    if (!rocert_rate_limit('company', 10)) {
        return new WP_REST_Response(['status' => 'rate_limited'], 429);
    }
    $cui = preg_replace('/^RO/i', '', preg_replace('/\s+/', '', (string) $request->get_param('cui')));
    if (!preg_match('/^[0-9]{2,10}$/', $cui)) {
        return new WP_REST_Response(['status' => 'invalid'], 400);
    }

    $cache_key = 'rocert_company_' . $cui;
    $company = get_transient($cache_key);
    if ($company === false) {
        /**
         * Company data by CUI (ANAF) — served by the rocert API, the single ANAF integration.
         *
         * @param array|false|null|WP_Error $company null = not configured; false = unknown CUI;
         *        array{name: string, address?: string, county?: string, city?: string, postal_code?: string, reg_com?: string, phone?: string}
         */
        $company = apply_filters('rocert/company/lookup', null, $cui);
        if (is_array($company)) {
            set_transient($cache_key, $company, DAY_IN_SECONDS);
        }
    }

    if ($company === null || is_wp_error($company)) {
        return new WP_REST_Response(['status' => 'unavailable'], 503);
    }
    if ($company === false) {
        return new WP_REST_Response(['status' => 'not_found'], 404);
    }
    return new WP_REST_Response(['status' => 'found', 'company' => array_map('strval', $company)], 200);
}
