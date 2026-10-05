<?php
/** @var array $attributes */
$out = '';
foreach (array_slice((array) $attributes['items'], 0, 4) as $f) {
    $out .= sprintf('<div class="rc-fact"><p class="rc-fact__k">%s</p><p class="rc-fact__v">%s</p></div>', esc_html($f['label'] ?? ''), esc_html($f['value'] ?? ''));
}
echo '<div class="rc-facts-wrap"><div class="rc-wrap"><div class="rc-facts">' . $out . '</div></div></div>';
