<?php
/**
 * Yoast analyses the raw editor content, where server-rendered ROCERT blocks are just attribute
 * comments — so it saw no text, images or links. Here the editor hands Yoast the rendered page instead.
 */
defined('ABSPATH') || exit;

add_action('rest_api_init', static function (): void {
    register_rest_route('rocert/v1', '/rendered/(?P<id>\d+)', [
        'methods' => 'GET',
        'permission_callback' => static fn (WP_REST_Request $r) => current_user_can('edit_post', (int) $r['id']),
        'callback' => static function (WP_REST_Request $r): WP_REST_Response {
            global $post;
            $post = get_post((int) $r['id']);
            if (!$post) {
                return new WP_REST_Response(['html' => ''], 404);
            }
            setup_postdata($post);
            $html = do_blocks($post->post_content);
            wp_reset_postdata();
            /* Drop markup Yoast should not count as content: hidden duplicates and decorative text. */
            $html = preg_replace('#<(script|style|svg)\b.*?</\1>#s', '', $html);
            $html = preg_replace('#<p class="rc-sr-only">.*?</p>#s', '', $html);
            $html = preg_replace('#<div class="rc-marquee__track"[^>]*>.*?</div>#s', '', $html);
            /* Navigation and labels are not the page's prose (otherwise Yoast takes them for the introduction). */
            $html = preg_replace('#<nav class="rc-crumbs".*?</nav>#s', '', $html);
            $html = preg_replace('#<span class="rc-(pill|badge)">.*?</span>#s', '', $html);
            $html = preg_replace('#<p class="rc-(pagehero__outline|outline-num)"[^>]*>.*?</p>#s', '', $html);
            $html = preg_replace('#<(p|div) class="rc-float-card__(top|title|meta)[^"]*"[^>]*>.*?</\1>#s', '', $html);
            return new WP_REST_Response(['html' => $html], 200);
        },
    ]);
});

add_action('enqueue_block_editor_assets', static function (): void {
    if (!defined('WPSEO_VERSION')) {
        return;
    }
    wp_enqueue_script('rocert-yoast', ROCERT_CORE_URL . '/assets/js/yoast-content.js', ['wp-data', 'wp-api-fetch'], ROCERT_CORE_VERSION . '.' . filemtime(ROCERT_CORE_DIR . '/assets/js/yoast-content.js'), true);
});

/* Pages are not news: Google shows no date for them, so the snippet preview (and its length check) shouldn't either. */
add_filter('wpseo_post_edit_values', static function ($values, $post) {
    if ($post instanceof WP_Post && $post->post_type === 'page') {
        $values['metaDescriptionDate'] = '';
    }
    return $values;
}, 10, 2);
