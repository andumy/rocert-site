<?php
/**
 * Plugin and site configuration that must hold in every environment. Idempotent; run by provision.sh
 * via `wp eval-file`. Content (pages, forms, media) is the seed's job, not this file's.
 */

/* Polylang: Romanian (default, no URL prefix) + English under /en/ */
if (function_exists('PLL') && PLL()->model) {
    $existing = wp_list_pluck(PLL()->model->get_languages_list(), 'slug');
    $languages = [
        ['name' => 'Română', 'slug' => 'ro', 'locale' => 'ro_RO', 'rtl' => false, 'term_group' => 0, 'flag' => 'ro'],
        ['name' => 'English', 'slug' => 'en', 'locale' => 'en_GB', 'rtl' => false, 'term_group' => 1, 'flag' => 'gb'],
    ];
    foreach ($languages as $lang) {
        if (!in_array($lang['slug'], $existing, true)) {
            $result = PLL()->model->languages->add($lang + ['no_default_cat' => true]);
            WP_CLI::log(is_wp_error($result) ? 'Polylang: ' . $result->get_error_message() : "Polylang: added {$lang['slug']}");
        }
    }
    /* Through Polylang's options object: it keeps its own copy and writes it back at shutdown. */
    foreach (['default_lang' => 'ro', 'force_lang' => 1, 'hide_default' => true, 'rewrite' => true, 'redirect_lang' => true, 'browser' => false, 'media_support' => false] as $key => $value) {
        PLL()->options->set($key, $value);
    }
    if (method_exists(PLL()->options, 'save')) {
        PLL()->options->save();
    }
    PLL()->model->clean_languages_cache();
}

/* Yoast SEO */
if (class_exists('WPSEO_Options')) {
    WPSEO_Options::set('enable_llms_txt', false); /* llms.txt is served by rocert-core (inc/seo.php) */
    WPSEO_Options::set('enable_xml_sitemap', true);
    WPSEO_Options::set('enable_admin_bar_menu', false);
    WPSEO_Options::set('enable_ai_generator', false);
    WPSEO_Options::set('enable_index_now', true);
    WPSEO_Options::set('separator', 'sc-pipe');
    WPSEO_Options::set('title-page', '%%title%% %%sep%% %%sitename%%');
    WPSEO_Options::set('title-home-wpseo', '%%sitename%% %%sep%% %%sitedesc%%');
    WPSEO_Options::set('company_or_person', 'company');
    WPSEO_Options::set('company_name', 'ROCERT');
    WPSEO_Options::set('company_alternate_name', 'Societatea Română pentru Certificare');
    WPSEO_Options::set('website_name', 'ROCERT');
    WPSEO_Options::set('breadcrumbs-enable', true);
    WPSEO_Options::set('breadcrumbs-home', 'Acasă');
    WPSEO_Options::set('breadcrumbs-sep', '/');
    WPSEO_Options::set('disable-author', true);
    WPSEO_Options::set('disable-date', true);
    WPSEO_Options::set('disable-attachment', true);
    WPSEO_Options::set('noindex-author-wpseo', true);
    WPSEO_Options::set('noindex-archive-wpseo', true);
    WPSEO_Options::set('display-metabox-pt-post', false);
}

/* Modern Image Formats: AVIF with JPEG fallback, served through <picture> */
update_option('perflab_modern_image_format', 'avif');
update_option('perflab_generate_webp_and_jpeg', '1');
update_option('webp_uploads_use_picture_element', '1');
update_option('perflab_generate_all_fallback_sizes', '1');

/* Fluent Forms: Cloudflare Turnstile keys come from the environment when present */
if (ROCERT_TURNSTILE_SITE_KEY && ROCERT_TURNSTILE_SECRET) {
    update_option('_fluentform_turnstile_details', [
        'siteKey' => ROCERT_TURNSTILE_SITE_KEY,
        'secretKey' => ROCERT_TURNSTILE_SECRET,
        'invisible' => 'no',
        'theme' => 'light',
    ], false);
    update_option('_fluentform_turnstile_keys_status', true, false);
}

/* WP core housekeeping */
update_option('show_avatars', 0);
update_option('default_pingback_flag', 0);

WP_CLI::success('Configuration applied');
