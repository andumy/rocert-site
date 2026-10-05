<?php
defined('ABSPATH') || exit;

add_action('init', static function (): void {
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    add_filter('emoji_svg_url', '__return_false');

    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
    remove_action('wp_head', 'feed_links_extra', 3);
});

add_filter('wp_default_scripts', static function (WP_Scripts $scripts): void {
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $scripts->registered['jquery']->deps = array_diff($scripts->registered['jquery']->deps, ['jquery-migrate']);
    }
});

add_action('wp_enqueue_scripts', static function (): void {
    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        wp_deregister_script('wp-embed');
    }
}, 100);

/* Only the blocks actually used on a page get their CSS. */
add_filter('should_load_separate_core_block_assets', '__return_true');

/* Heartbeat only where it is useful. */
add_action('init', static function (): void {
    if (!is_admin()) {
        wp_deregister_script('heartbeat');
    }
}, 1);

/* jQuery (only needed by Fluent Forms) goes to the footer so it never blocks rendering. */
add_action('wp_enqueue_scripts', static function (): void {
    if (is_admin()) {
        return;
    }
    foreach (['jquery', 'jquery-core', 'jquery-migrate'] as $handle) {
        wp_scripts()->add_data($handle, 'group', 1);
    }
}, 1);

/*
 * Responsive image hints. Seeded content uses 'large' for half-width split media and 'full' for
 * full-bleed backgrounds; the default sizes attribute would make phones download the largest file.
 */
add_filter('wp_calculate_image_sizes', static function (string $sizes, $size): string {
    $width = is_array($size) ? (int) $size[0] : 0;
    if (is_admin() || $width < 1200) {
        return $sizes;
    }
    return $width >= 2000 ? '100vw' : '(max-width: 860px) 100vw, 50vw';
}, 10, 2);

/* AVIF at 60 is visually indistinguishable here and roughly 40% lighter than the default. */
add_filter('wp_editor_set_quality', static fn (int $quality, string $mime) => $mime === 'image/avif' ? 60 : ($mime === 'image/webp' ? 75 : $quality), 10, 2);

/* On the home page the form is far below the fold: load its styles without blocking the first paint. */
add_filter('style_loader_tag', static function (string $tag, string $handle): string {
    if (in_array($handle, ['fluent-form-styles', 'fluentform-public-default'], true) && is_front_page()) {
        $tag = preg_replace("/media='[^']*'/", "media='print' onload=\"this.media='all'\"", $tag);
    }
    return $tag;
}, 10, 2);

/*
 * The home hero's largest element is the headline, not the photo (which sits below the fold on phones),
 * so the photo must not compete with the fonts for bandwidth.
 */
add_filter('wp_content_img_tag', static function (string $img, string $context, int $attachment_id): string {
    $hero = (int) (get_option('rocert_media')['hero-industry'] ?? 0);
    if ($attachment_id && $attachment_id === $hero) {
        $img = str_replace('fetchpriority="high"', 'fetchpriority="low"', $img);
    }
    return $img;
}, 5, 3);
