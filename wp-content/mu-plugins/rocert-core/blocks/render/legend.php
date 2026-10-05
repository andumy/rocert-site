<?php
/** @var array $attributes */
$out = '';
foreach (array_slice((array) $attributes['items'], 0, 4) as $l) {
    $out .= sprintf('<div class="rc-legend__item rc-legend__item--%s"><h3 class="wp-block-heading">%s</h3><p>%s</p></div>', esc_attr($l['tone'] ?? 'green'), esc_html($l['title'] ?? ''), esc_html($l['text'] ?? ''));
}
echo '<div class="rc-legend">' . $out . '</div>';
