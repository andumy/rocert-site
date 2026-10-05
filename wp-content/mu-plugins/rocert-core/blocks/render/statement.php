<?php
/** @var array $attributes */
$a = $attributes;
$text = esc_html($a['text']);
if ($a['muted']) {
    $text .= ' <span class="rc-muted">' . esc_html($a['muted']) . '</span>';
}
if ($a['accent']) {
    $text .= ' <span class="rc-accent">' . esc_html($a['accent']) . '</span>';
}
$stats = '';
foreach (array_slice((array) $a['stats'], 0, 4) as $s) {
    if (!empty($s['value'])) {
        $stats .= sprintf('<div class="rc-stat"><p class="rc-stat__value">%s</p><p class="rc-stat__label">%s</p></div>', esc_html($s['value']), esc_html($s['label'] ?? ''));
    }
}
$stats = $stats ? '<div class="rc-stats">' . $stats . '</div>' : '';
if (!empty($a['offset']) && ($a['deco'] ?? '') === 'swoosh') {
    printf('<div class="rc-statement-wrap"><div class="rc-statement-deco">%s</div><p class="rc-statement">%s</p></div>%s', file_get_contents(dirname(__DIR__) . '/swoosh.svg'), $text, $stats);
    return;
}
printf('<p class="rc-statement%s">%s</p>%s', !empty($a['offset']) ? ' rc-offset' : '', $text, $stats);
