<?php
defined('ABSPATH') || exit;

/** @return WP_Post[] category pages (children of the certifications hub) in the current language, ordered */
function rocert_category_pages(): array
{
    return get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_key' => '_rocert_type',
        'meta_value' => 'category',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'lang' => rocert_lang(),
    ]);
}

/** @return WP_Post[] standard pages under a category page */
function rocert_standard_pages(int $category_id): array
{
    return get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_parent' => $category_id,
        'posts_per_page' => -1,
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'lang' => rocert_lang(),
    ]);
}

function rocert_page_by_key(string $key): ?WP_Post
{
    $posts = get_posts([
        'post_type' => 'page',
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'meta_key' => '_rocert_key',
        'meta_value' => $key,
        'lang' => rocert_lang(),
    ]);
    return $posts[0] ?? null;
}

function rocert_meta(int $post_id, string $key): string
{
    return (string) get_post_meta($post_id, '_rocert_' . $key, true);
}

/** Responsive <img> for an attachment, lazy by default */
function rocert_img(int $attachment_id, string $size = 'large', array $attr = []): string
{
    if (!$attachment_id) {
        return '';
    }
    $attr += ['loading' => 'lazy', 'decoding' => 'async', 'sizes' => '(max-width: 860px) 100vw, 50vw'];
    return wp_get_attachment_image($attachment_id, $size, false, $attr);
}

function rocert_svg(string $name, int $size = 18): string
{
    $paths = [
        'arrow' => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
        'arrow-left' => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
        'arrow-ur' => '<path d="M7 17 17 7"/><path d="M8 7h9v9"/>',
        'search' => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'check' => '<path d="M20 6 9 17l-5-5"/>',
        'x' => '<path d="M18 6 6 18M6 6l12 12"/>',
        'shield' => '<path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6l-8-3z"/><path d="m9 12 2 2 4-4"/>',
    ];
    return sprintf('<svg width="%1$d" height="%1$d" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%2$s</svg>', $size, $paths[$name] ?? '');
}

/** Per-IP fixed-window rate limit; returns false when the limit is exceeded */
function rocert_rate_limit(string $bucket, int $max, int $window = 60): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $key = 'rocert_rl_' . $bucket . '_' . md5($ip);
    $count = (int) get_transient($key);
    if ($count >= $max) {
        return false;
    }
    set_transient($key, $count + 1, $window);
    return true;
}

function rocert_home_url(): string
{
    return function_exists('pll_home_url') ? pll_home_url() : home_url('/');
}

/** Stable anchor for a heading text (used by the table of contents and the headings themselves) */
function rocert_heading_id(string $text): string
{
    return 'h-' . sanitize_title(remove_accents(wp_strip_all_tags($text)));
}

/* H2 headings in page content get ids so the table of contents can link to them. */
add_filter('render_block_core/heading', static function (string $html, array $block): string {
    if (($block['attrs']['level'] ?? 2) !== 2 || str_contains($html, ' id=')) {
        return $html;
    }
    return preg_replace('/<h2\b/', '<h2 id="' . esc_attr(rocert_heading_id($html)) . '"', $html, 1);
}, 10, 2);
