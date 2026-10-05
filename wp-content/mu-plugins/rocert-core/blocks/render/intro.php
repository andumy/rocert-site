<?php
/** @var array $attributes @var string $content */
$a = $attributes;
printf(
    '<div class="rc-twocol"><div>%s<h2 class="wp-block-heading rc-h2-s">%s</h2></div><div class="rc-twocol__body">%s</div></div>',
    $a['eyebrow'] ? '<p class="rc-eyebrow">' . esc_html($a['eyebrow']) . '</p>' : '',
    esc_html($a['title']),
    $content
);
