<?php
/** @var array $attributes */
$a = $attributes;
printf(
    '<div class="rc-darkcard rc-darkcard--%s"><h3 class="wp-block-heading">%s</h3>%s%s</div>',
    esc_attr($a['tone'] ?: 'dark'), esc_html($a['title']),
    $a['text'] ? '<p>' . esc_html($a['text']) . '</p>' : '',
    $a['buttonText'] && $a['buttonUrl'] ? sprintf('<div class="wp-block-buttons"><div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div></div>', esc_url($a['buttonUrl']), esc_html($a['buttonText'])) : ''
);
