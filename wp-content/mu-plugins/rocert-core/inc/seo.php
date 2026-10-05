<?php
defined('ABSPATH') || exit;

/* Richer Organization schema than Yoast's settings allow. */
add_filter('wpseo_schema_organization', static function (array $data): array {
    $data['@type'] = ['Organization', 'ProfessionalService'];
    $data['legalName'] = 'ROCERT SRL';
    $data['alternateName'] = 'Societatea Română pentru Certificare';
    $data['foundingDate'] = '1997';
    $data['taxID'] = 'RO9754830';
    $data['vatID'] = 'RO9754830';
    $data['telephone'] = '+40 21 224 26 39';
    $data['email'] = 'office@rocert.ro';
    $data['address'] = [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43',
        'addressLocality' => 'București',
        'addressRegion' => 'Sector 1',
        'postalCode' => '011571',
        'addressCountry' => 'RO',
    ];
    $data['areaServed'] = ['@type' => 'Country', 'name' => 'Romania'];
    $data['knowsAbout'] = ['ISO 9001', 'ISO 14001', 'ISO 45001', 'ISO/IEC 27001', 'ISO 22000', 'ISO 50001', 'ISO 37001', 'ISO 13485', 'ISO/IEC 20000-1', 'ISO 39001'];
    return $data;
});

/* Standard pages describe a certification Service offered by the organization. */
add_filter('wpseo_schema_webpage', static function (array $data): array {
    if (is_singular('page') && rocert_meta(get_queried_object_id(), 'type') === 'standard') {
        $id = get_queried_object_id();
        $data['about'] = [
            '@type' => 'Service',
            'name' => get_the_title($id),
            'serviceType' => 'Certificare ' . rocert_meta($id, 'code'),
            'provider' => ['@id' => home_url('/#organization')],
            'areaServed' => 'RO',
        ];
    }
    return $data;
});

add_filter('wpseo_breadcrumb_separator', static fn () => '<span aria-hidden="true">/</span>');

/* Breadcrumb "home" label and link follow the current language. */
add_filter('wpseo_breadcrumb_links', static function (array $links): array {
    if (isset($links[0])) {
        $links[0]['text'] = rocert_t('home');
        $links[0]['url'] = rocert_home_url();
    }
    return $links;
});

/* Build Yoast indexables (breadcrumbs, schema, sitemaps) on local/stage too, so they match production. */
add_filter('Yoast\WP\SEO\should_index_indexables', '__return_true');

/*
 * /llms.txt — built live from the page tree (both languages), so it never drifts from the site.
 * Replaces Yoast's generator, which writes a static file and loses URLs outside production.
 */
add_action('init', static function (): void {
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
    if ($path !== '/llms.txt') {
        return;
    }
    $desc = static fn (WP_Post $p) => trim((string) get_post_meta($p->ID, '_yoast_wpseo_metadesc', true));
    $line = static fn (WP_Post $p, string $title = '') => sprintf('- [%s](%s)%s', $title ?: get_the_title($p), get_permalink($p), ($d = $desc($p)) ? ': ' . $d : '');
    $by_key = static function (string $key, string $lang): ?WP_Post {
        $posts = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => 1, 'lang' => '', 'meta_query' => [['key' => '_rocert_key', 'value' => $key], ['key' => '_rocert_lang', 'value' => $lang]]]);
        return $posts[0] ?? null;
    };

    $out = ["# ROCERT — Societatea Română pentru Certificare", '', '> ROCERT SRL (CUI RO9754830, București, România) este un organism independent de certificare a sistemelor de management, activ din 1997. Certifică organizații după ISO 9001, ISO 14001, ISO 45001, ISO/IEC 27001, ISO 22000, ISO 50001, ISO 37001 și alte standarde. Contact: office@rocert.ro, +40 21 224 26 39.', ''];
    foreach (['ro' => ['Certificări', 'Pagini principale'], 'en' => ['Certifications (English)', 'Main pages (English)']] as $lang => [$h_certs, $h_main]) {
        $out[] = '## ' . $h_certs;
        $cats = get_posts(['post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'lang' => '', 'meta_query' => [['key' => '_rocert_type', 'value' => 'category'], ['key' => '_rocert_lang', 'value' => $lang]]]);
        foreach ($cats as $cat) {
            $out[] = $line($cat);
            foreach (get_posts(['post_type' => 'page', 'post_status' => 'publish', 'post_parent' => $cat->ID, 'posts_per_page' => -1, 'orderby' => 'menu_order', 'order' => 'ASC', 'lang' => '']) as $std) {
                $out[] = '  ' . $line($std);
            }
        }
        $out[] = '';
        $out[] = '## ' . $h_main;
        foreach (['verify', 'request', 'about', 'accreditations', 'public-info', 'contact'] as $key) {
            if ($p = $by_key($key, $lang)) {
                $out[] = $line($p);
            }
        }
        $out[] = '';
    }
    $out[] = '## Optional';
    $out[] = '- [Sitemap](' . home_url('/sitemap_index.xml') . ')';
    foreach (['privacy', 'cookies', 'terms'] as $key) {
        if ($p = $by_key($key, 'ro')) {
            $out[] = $line($p);
        }
    }

    header('Content-Type: text/plain; charset=utf-8');
    header('Cache-Control: public, max-age=3600');
    if (wp_get_environment_type() !== 'production') {
        header('X-Robots-Tag: noindex, nofollow');
    }
    echo implode("\n", $out) . "\n";
    exit;
}, 0);
