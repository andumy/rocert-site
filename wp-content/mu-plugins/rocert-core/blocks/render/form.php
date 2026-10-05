<?php
/** @var array $attributes */
$a = $attributes;
$id = (int) $a['form'];
if (function_exists('pll_current_language') && $id) {
    $forms = rocert_forms();
    $key = array_search($id, $forms, true);
    if ($key !== false) {
        $base = preg_replace('/_(ro|en)$/', '', (string) $key);
        $id = (int) ($forms[$base . '_' . rocert_lang()] ?? $id);
    }
}
printf(
    '<div class="rc-form rc-form-card rc-request">%s%s</div>%s',
    $a['title'] ? '<h2 class="rc-sr-only">' . esc_html($a['title']) . '</h2>' : '',
    $id ? do_shortcode('[fluentform id="' . $id . '"]') : '',
    $a['help'] ? '<p class="rc-request-help">' . rocert_kses_inline($a['help']) . '</p>' : ''
);
