<?php
/**
 * ROCERT child theme: styles and font preloading. Site behaviour lives in mu-plugins/rocert-core.
 */

add_action('after_setup_theme', static function (): void {
    add_editor_style('style.css');
    add_theme_support('responsive-embeds');
});

add_action('wp_enqueue_scripts', static function (): void {
    $theme = wp_get_theme();
    wp_enqueue_style('rocert', get_stylesheet_uri(), ['ollie'], $theme->get('Version') . '.' . filemtime(get_stylesheet_directory() . '/style.css'));
}, 20);

add_action('wp_head', static function (): void {
    $base = get_stylesheet_directory_uri() . '/assets/fonts/';
    /* Romanian diacritics (ă î ș ț) live in the latin-ext subsets, so both are needed for the first paint. */
    foreach (['manrope-latin.woff2', 'manrope-latin-ext.woff2', 'opensans-latin.woff2', 'opensans-latin-ext.woff2'] as $font) {
        printf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url($base . $font));
    }
}, 1);
