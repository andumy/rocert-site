<?php
defined('ABSPATH') || exit;

/* Polylang (free) cannot translate block-theme template parts: render header-en/footer-en for English. */
add_filter('render_block_data', static function (array $block): array {
    if (($block['blockName'] ?? '') === 'core/template-part' && rocert_lang() === 'en') {
        $slug = $block['attrs']['slug'] ?? '';
        if (in_array($slug, ['header', 'footer'], true)) {
            $block['attrs']['slug'] = $slug . '-en';
        }
    }
    return $block;
});

/** Compact RO · EN switcher pointing at the translation of the current page */
function rocert_lang_switch(): string
{
    if (!function_exists('pll_the_languages')) {
        return '';
    }
    $langs = pll_the_languages(['raw' => 1, 'hide_if_no_translation' => 0, 'hide_if_empty' => 0]);
    if (!$langs) {
        return '';
    }
    $out = [];
    foreach ($langs as $lang) {
        $label = strtoupper($lang['slug']);
        if (!empty($lang['current_lang'])) {
            $out[] = sprintf('<span aria-current="true" lang="%s">%s</span>', esc_attr($lang['slug']), $label);
        } else {
            $out[] = sprintf('<a href="%s" hreflang="%s" lang="%s" title="%s">%s</a>', esc_url($lang['url']), esc_attr($lang['locale'] ? str_replace('_', '-', $lang['locale']) : $lang['slug']), esc_attr($lang['slug']), esc_attr($lang['name']), $label);
        }
    }
    return '<nav class="rc-lang" aria-label="Language">' . implode('<span aria-hidden="true">·</span>', $out) . '</nav>';
}

/* x-default points at the Romanian version (the default language). */
add_filter('pll_rel_hreflang_attributes', static function (array $hreflangs): array {
    if (isset($hreflangs['ro']) && !isset($hreflangs['x-default'])) {
        $hreflangs['x-default'] = $hreflangs['ro'];
    }
    return $hreflangs;
});
