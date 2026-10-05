<?php
/** @var array $attributes */
$out = '';
foreach ((array) $attributes['items'] as $i => $c) {
    $tone = $c['tone'] ?? 'light';
    $out .= sprintf('<div class="rc-card%s"><p class="rc-card-n">%02d</p><h3 class="wp-block-heading">%s</h3><p>%s</p></div>', $tone === 'light' ? '' : ' rc-card--' . esc_attr($tone), $i + 1, esc_html($c['title'] ?? ''), esc_html($c['text'] ?? ''));
}
echo '<div class="rc-cards3">' . $out . '</div>';
