<?php
/** @var array $attributes */
$post = get_post();
if (!$post) {
    return;
}
preg_match_all('/<!-- wp:heading[^>]*-->\s*<h2[^>]*>(.*?)<\/h2>/s', $post->post_content, $m);
if (!$m[1]) {
    return;
}
$items = '';
foreach ($m[1] as $heading) {
    $text = wp_strip_all_tags($heading);
    $items .= sprintf('<li><a href="#%s">%s</a></li>', esc_attr(rocert_heading_id($text)), esc_html($text));
}
printf('<nav class="rc-toc" aria-label="%1$s"><p class="rc-eyebrow">%1$s</p><ol>%2$s</ol></nav>', esc_html($attributes['title'] ?: rocert_t('toc')), $items);
