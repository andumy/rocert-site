<?php
/** @var array $attributes */
$items = '';
foreach ((array) $attributes['items'] as $r) {
    $items .= sprintf('<li><strong>%s</strong><span>%s</span></li>', esc_html($r['title'] ?? ''), esc_html($r['text'] ?? ''));
}
echo ($attributes['note'] ? '<p class="rc-note">' . esc_html($attributes['note']) . '</p>' : '') . '<ul class="rc-recog">' . $items . '</ul>';
