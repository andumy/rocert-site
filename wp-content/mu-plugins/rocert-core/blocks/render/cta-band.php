<?php
/** @var array $attributes */
$a = $attributes;
$tone = $a['tone'] === 'dark' ? 'rc-dark' : 'rc-blue';
printf(
    '<div class="rc-ctaband %s"><div class="rc-ctaband__text"><h2 class="wp-block-heading rc-h2-s">%s</h2>%s</div>%s</div>',
    $tone, esc_html($a['title']),
    $a['text'] ? '<p class="rc-lead">' . esc_html($a['text']) . '</p>' : '',
    $a['buttonText'] && $a['buttonUrl'] ? sprintf('<div class="wp-block-buttons"><div class="wp-block-button %s rc-btn-lg"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div></div>', $tone === 'rc-blue' ? 'rc-btn-dark' : '', esc_url($a['buttonUrl']), esc_html($a['buttonText'])) : ''
);
