<?php
defined('ABSPATH') || exit;

add_filter('xmlrpc_enabled', '__return_false');
add_filter('wp_headers', static function (array $headers): array {
    unset($headers['X-Pingback']);
    if (wp_get_environment_type() !== 'production') {
        $headers['X-Robots-Tag'] = 'noindex, nofollow';
    }
    return $headers;
});

/* No username discovery through the REST API or ?author=N */
add_filter('rest_endpoints', static function (array $endpoints): array {
    if (!is_user_logged_in()) {
        unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    }
    return $endpoints;
});
add_action('template_redirect', static function (): void {
    if (is_author() || (isset($_GET['author']) && !is_admin())) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
});

add_filter('login_errors', static fn () => __('Datele de autentificare nu sunt corecte.', 'rocert'));
