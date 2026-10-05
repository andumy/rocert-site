<?php
/** @var array $attributes */
$a = $attributes;
$button = $a['buttonText'] && $a['buttonUrl'] ? sprintf('<div class="wp-block-buttons"><div class="wp-block-button rc-btn-white"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div></div>', esc_url($a['buttonUrl']), esc_html($a['buttonText'])) : '';
printf(
    '<div class="rc-tile rc-tile--%s">%s<h3 class="wp-block-heading">%s</h3>%s%s</div>',
    esc_attr($a['tone'] ?: 'light'),
    $a['number'] !== '' ? '<p class="rc-tile__n">' . esc_html($a['number']) . '</p>' : '',
    esc_html($a['title']),
    $a['text'] !== '' ? '<p class="rc-tile__text">' . esc_html($a['text']) . '</p>' : '',
    $button
);
