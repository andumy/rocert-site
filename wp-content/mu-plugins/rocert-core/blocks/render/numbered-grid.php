<?php
/** @var array $attributes */
$out = '';
foreach ((array) $attributes['items'] as $i => $r) {
    $out .= sprintf('<div class="rc-req"><p class="rc-req__n">%02d</p><h3 class="wp-block-heading">%s</h3><p>%s</p></div>', $i + 1, esc_html($r['title'] ?? ''), esc_html($r['text'] ?? ''));
}
echo '<div class="rc-reqs">' . $out . '</div>';
