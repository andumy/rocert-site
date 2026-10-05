<?php
/** @var array $attributes */
$a = $attributes;
$items = array_values(array_filter(array_map(static fn ($i) => trim((string) ($i['text'] ?? '')), (array) $a['items'])));
if (!$items) {
    return;
}
$one = '';
foreach ($items as $item) {
    $one .= sprintf('<span class="rc-marquee__item">%s<span class="rc-marquee__sep" aria-hidden="true">%s</span></span>', esc_html($item), esc_html($a['sep'] ?: '✦'));
}
$variant = $a['variant'] ?: 'light';
printf(
    '<div class="rc-marquee rc-marquee--%s%s">%s<p class="rc-sr-only">%s</p><div class="rc-marquee__track" aria-hidden="true">%s%s</div></div>',
    esc_attr($variant), $variant !== 'tilt' ? ' rc-marquee--slow' : '',
    $a['label'] ? '<p class="rc-marquee__label">' . esc_html($a['label']) . '</p>' : '',
    esc_html(implode(', ', $items)), $one, $one
);
