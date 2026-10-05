<?php
/** @var array $attributes @var string $content */
$a = $attributes;
$classes = ['rc-section', 'rc-section--' . ($a['spacing'] ?: 'normal'), rocert_bg_class($a['bg'] ?? ''), !empty($a['grid']) ? 'rc-grid-bg' : ''];
$inner = ($a['width'] ?? 'wrap') === 'full' ? $content : sprintf('<div class="%s">%s</div>', ($a['width'] ?? '') === 'narrow' ? 'rc-narrow' : 'rc-wrap', $content);
printf('<section%s class="%s">%s</section>', $a['anchor'] ? ' id="' . esc_attr($a['anchor']) . '"' : '', esc_attr(trim(implode(' ', array_filter($classes)))), $inner);
