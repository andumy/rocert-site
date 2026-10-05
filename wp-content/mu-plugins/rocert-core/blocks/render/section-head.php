<?php
/** @var array $attributes */
$a = $attributes;
$tag = ($a['level'] ?? '2') === '3' ? 'h3' : 'h2';
if ($a['title'] === '') {
    /* Eyebrow-only heading: the label itself is the section heading. */
    printf('<div class="rc-section-head rc-section-head--solo"><%1$s class="wp-block-heading rc-eyebrow">%2$s</%1$s></div>', $tag, esc_html($a['eyebrow']));
    return;
}
printf(
    '<div class="rc-section-head%s"><div>%s<%s class="wp-block-heading rc-h2">%s</%s></div>%s</div>',
    $a['text'] ? '' : ' rc-section-head--solo',
    $a['eyebrow'] ? '<p class="rc-eyebrow">' . esc_html($a['eyebrow']) . '</p>' : '',
    $tag, esc_html($a['title']), $tag,
    $a['text'] ? '<p class="rc-section-head__text">' . esc_html($a['text']) . '</p>' : ''
);
