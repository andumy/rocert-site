<?php
/**
 * Markup for the dynamic parts of the blocks (built from the page tree, or with behaviour).
 * Called from blocks/render/*.php.
 */
defined('ABSPATH') || exit;

function rocert_enqueue_front(): void
{
    wp_enqueue_script('rocert', ROCERT_CORE_URL . '/assets/js/rocert.js', [], ROCERT_CORE_VERSION . '.' . filemtime(ROCERT_CORE_DIR . '/assets/js/rocert.js'), ['strategy' => 'defer', 'in_footer' => true]);
    $keys = ['valid', 'valid_text', 'invalid', 'invalid_text', 'standard_label', 'scope', 'not_found', 'not_found_text', 'unavailable', 'unavailable_text', 'too_many', 'source_note', 'searching', 'verify', 'serial', 'serial_hint', 'serial_placeholder', 'serial_invalid', 'anaf_button', 'anaf_loading', 'anaf_ok', 'anaf_fail', 'anaf_invalid'];
    wp_localize_script('rocert', 'rocertCfg', ['rest' => esc_url_raw(rest_url('rocert/v1/')), 'i18n' => array_combine($keys, array_map('rocert_t', $keys))]);
}

function rocert_breadcrumbs_html(): string
{
    if (!function_exists('yoast_breadcrumb') || is_front_page()) {
        return '';
    }
    return yoast_breadcrumb('<nav class="rc-crumbs" aria-label="Breadcrumb">', '</nav>', false) ?: '';
}

function rocert_verify_page_url(): string
{
    $page = rocert_page_by_key('verify');
    return $page ? get_permalink($page) : home_url('/');
}

/** Compact search pill (home hero, verify band): submits to the verify page with ?serie= */
function rocert_verify_form_html(string $variant = 'hero', string $note = ''): string
{
    $light = $variant === 'band';
    $id = 'rc-qv-' . wp_unique_id();
    $html = sprintf(
        '<form class="rc-qverify%s" action="%s" method="get" role="search"><label class="rc-sr-only" for="%s">%s</label>%s<input id="%s" name="serie" type="text" autocomplete="off" spellcheck="false" placeholder="%s" required maxlength="64"><button type="submit">%s</button></form>',
        $light ? ' rc-qverify--light' : '',
        esc_url(rocert_verify_page_url()),
        $id,
        esc_html(rocert_t('serial')),
        $light ? '' : '<span class="rc-qverify__icon">' . rocert_svg('search', 20) . '</span>',
        $id,
        esc_attr(rocert_t('serial_placeholder')),
        esc_html(rocert_t('verify'))
    );
    if ($note) {
        $html .= '<p class="rc-qverify-note">' . rocert_kses_inline($note) . '</p>';
    }
    return $html;
}

/** Verify page: lookup by certificate serial (the unique code printed on the certificate) */
function rocert_verify_html(): string
{
    rocert_enqueue_front();
    $q = isset($_GET['serie']) ? mb_substr(sanitize_text_field(wp_unslash($_GET['serie'])), 0, 64) : '';
    return sprintf(
        '<div class="rc-verify rc-wrap" data-rc-verify%s><form class="rc-verify__form" novalidate><div class="rc-verify__top"><label class="rc-verify__label" for="rc-verify-q">%s</label><p class="rc-verify__hint">%s</p></div><div class="rc-verify__row"><input id="rc-verify-q" name="serie" type="text" autocomplete="off" spellcheck="false" maxlength="64" required value="%s" placeholder="%s"><button class="rc-btn" type="submit">%s%s</button></div></form><div class="rc-verify__result" data-result aria-live="polite" hidden></div></div>',
        $q ? ' data-autorun="1"' : '',
        esc_html(rocert_t('serial')),
        esc_html(rocert_t('serial_hint')),
        esc_attr($q),
        esc_attr(rocert_t('serial_placeholder')),
        rocert_svg('search', 22),
        esc_html(rocert_t('verify'))
    );
}

function rocert_category_index_html(string $eyebrow, string $title, string $cta): string
{
    $cats = rocert_category_pages();
    if (!$cats) {
        return '';
    }
    rocert_enqueue_front();
    $hub = rocert_page_by_key('hub');
    $total = 0;
    $list = '';
    $panels = '';
    foreach ($cats as $i => $cat) {
        $stds = rocert_standard_pages($cat->ID);
        $total += count($stds);
        $n = sprintf('%02d', $i + 1);
        $active = $i === 0 ? ' is-active' : '';
        $count = count($stds) . ' ' . (count($stds) === 1 ? rocert_t('standard') : rocert_t('standards'));
        $list .= sprintf(
            '<li><a class="rc-catindex__link%s" href="%s" data-panel="%d"><span class="rc-catindex__n">%s</span><span class="rc-catindex__title">%s</span><span class="rc-catindex__count">%s</span><span class="rc-catindex__arrow">%s</span></a></li>',
            $active, esc_url(get_permalink($cat)), $i, $n, esc_html(get_the_title($cat)), esc_html($count), rocert_svg('arrow')
        );
        $chips = '';
        foreach ($stds as $s) {
            $chips .= '<li>' . esc_html(rocert_meta($s->ID, 'code')) . '</li>';
        }
        $panels .= sprintf(
            '<div class="rc-catindex__panel%s" data-panel="%d" aria-hidden="%s">%s<div class="rc-catindex__caption"><p class="rc-outline-num">%s</p><h3>%s</h3><p>%s</p><ul class="rc-chips rc-chips--glass">%s</ul></div></div>',
            $active, $i, $i === 0 ? 'false' : 'true',
            rocert_img((int) rocert_meta($cat->ID, 'image'), 'large', ['sizes' => '(max-width: 860px) 100vw, 50vw', 'alt' => '']),
            $n, esc_html(get_the_title($cat)), esc_html(rocert_meta($cat->ID, 'card')), $chips
        );
    }
    $button = $hub ? sprintf('<div class="wp-block-buttons"><div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div></div>', esc_url(get_permalink($hub)), esc_html($cta ?: sprintf(rocert_t('see_all'), $total))) : '';
    return sprintf(
        '<div class="rc-split rc-split--media-right rc-catindex" data-rc-catindex><div class="rc-split__content">%s%s<ol class="rc-catindex__list">%s</ol>%s</div><div class="rc-split__media rc-rounded rc-catindex__media">%s</div></div>',
        $eyebrow ? '<p class="rc-eyebrow">' . esc_html($eyebrow) . '</p>' : '',
        $title ? '<h2 class="wp-block-heading rc-h2">' . esc_html($title) . '</h2>' : '',
        $list, $button, $panels
    );
}

function rocert_category_strip_html(): string
{
    $out = '';
    foreach (rocert_category_pages() as $i => $cat) {
        $out .= sprintf(
            '<a class="rc-strip__item" href="#%s">%s<span class="rc-strip__cap"><small>%02d</small><span>%s</span></span></a>',
            esc_attr($cat->post_name), rocert_img((int) rocert_meta($cat->ID, 'image'), 'medium_large', ['sizes' => '420px', 'alt' => '']), $i + 1, esc_html(get_the_title($cat))
        );
    }
    return $out ? '<div class="rc-strip">' . $out . '</div>' : '';
}

function rocert_stdrows(array $stds): string
{
    $rows = '';
    foreach ($stds as $s) {
        $rows .= sprintf(
            '<li><a class="rc-stdrow" href="%s"><span class="rc-stdrow__code">%s</span><span class="rc-stdrow__name">%s</span><span class="rc-stdrow__arrow">%s</span></a></li>',
            esc_url(get_permalink($s)), esc_html(rocert_meta($s->ID, 'code')), esc_html(rocert_meta($s->ID, 'name')), rocert_svg('arrow')
        );
    }
    return '<ul class="rc-stdrows">' . $rows . '</ul>';
}

function rocert_category_zigzag_html(): string
{
    $out = '';
    foreach (rocert_category_pages() as $i => $cat) {
        $left = $i % 2 === 0;
        $media = sprintf(
            '<a class="rc-split__media rc-rounded" href="%s" tabindex="-1" aria-hidden="true">%s<span class="rc-outline-num">%02d</span></a>',
            esc_url(get_permalink($cat)), rocert_img((int) rocert_meta($cat->ID, 'image'), 'large', ['alt' => '']), $i + 1
        );
        $extra = rocert_meta($cat->ID, 'extra');
        $content = sprintf(
            '<div class="rc-split__content"><h2 class="wp-block-heading rc-h2-s"><a href="%s" class="rc-plain-link">%s</a></h2><p class="rc-lead" style="margin-top:14px">%s</p>%s%s</div>',
            esc_url(get_permalink($cat)), esc_html(get_the_title($cat)), esc_html(rocert_meta($cat->ID, 'card')), rocert_stdrows(rocert_standard_pages($cat->ID)),
            $extra ? '<p class="rc-zig__extra">' . esc_html($extra) . '</p>' : ''
        );
        $out .= sprintf('<div id="%s" class="rc-split rc-zig %s">%s</div>', esc_attr($cat->post_name), $left ? 'rc-split--media-left' : 'rc-split--media-right', $left ? $media . $content : $content . $media);
    }
    return $out;
}

function rocert_standards_table_html(string $eyebrow, string $title): string
{
    rocert_enqueue_front();
    $filters = sprintf('<button type="button" aria-pressed="true" data-filter="*">%s</button>', esc_html(rocert_t('all')));
    $rows = '';
    foreach (rocert_category_pages() as $cat) {
        $filters .= sprintf('<button type="button" aria-pressed="false" data-filter="%s">%s</button>', esc_attr($cat->post_name), esc_html(get_the_title($cat)));
        foreach (rocert_standard_pages($cat->ID) as $s) {
            $rows .= sprintf(
                '<a class="rc-stdtable__row" href="%s" data-cat="%s"><span class="rc-stdrow__code">%s</span><span class="rc-stdrow__name">%s</span><span class="rc-stdtable__cat">%s</span><span class="rc-stdrow__arrow">%s</span></a>',
                esc_url(get_permalink($s)), esc_attr($cat->post_name), esc_html(rocert_meta($s->ID, 'code')), esc_html(rocert_meta($s->ID, 'name')), esc_html(get_the_title($cat)), rocert_svg('arrow')
            );
        }
    }
    return sprintf(
        '<div class="rc-stdtable" data-rc-filter><div class="rc-stdtable__side">%s%s<div class="rc-stdtable__filters" role="group" aria-label="%s">%s</div></div><div class="rc-stdtable__rows">%s</div></div>',
        $eyebrow ? '<p class="rc-eyebrow">' . esc_html($eyebrow) . '</p>' : '',
        $title ? '<h2 class="wp-block-heading rc-h2-s">' . esc_html($title) . '</h2>' : '',
        esc_attr(rocert_t('filter_label')), $filters, $rows
    );
}

function rocert_category_chips_html(int $page_id): string
{
    $out = '';
    foreach (rocert_standard_pages($page_id) as $s) {
        $out .= sprintf('<li><a href="#%s">%s</a></li>', esc_attr($s->post_name), esc_html(rocert_meta($s->ID, 'code')));
    }
    return $out ? '<ul class="rc-chips rc-chips--glass rc-chips--nav">' . $out . '</ul>' : '';
}

/** @param int[] $ids */
function rocert_related_html(array $ids, string $cta, string $url): string
{
    $out = '';
    foreach (array_slice(array_filter($ids), 0, 3) as $i => $id) {
        $p = get_post($id);
        if (!$p || $p->post_status !== 'publish') {
            continue;
        }
        if (function_exists('pll_get_post') && ($tr = pll_get_post($p->ID, rocert_lang()))) {
            $p = get_post($tr);
        }
        $img = (int) rocert_meta($p->ID, 'image') ?: (int) rocert_meta((int) $p->post_parent, 'image');
        if ($i < 2) {
            $out .= sprintf('<a class="rc-bento__item" href="%s">%s<span class="rc-bento__code">%s</span><span class="rc-bento__name">%s</span></a>', esc_url(get_permalink($p)), rocert_img($img, 'large', ['alt' => '', 'sizes' => '(max-width: 860px) 100vw, 66vw']), esc_html(rocert_meta($p->ID, 'code')), esc_html(rocert_meta($p->ID, 'name')));
        } else {
            $out .= sprintf('<a class="rc-bento__item rc-bento__item--plain" href="%s"><span class="rc-bento__code">%s</span><span class="rc-bento__name">%s</span></a>', esc_url(get_permalink($p)), esc_html(rocert_meta($p->ID, 'code')), esc_html(rocert_meta($p->ID, 'name')));
        }
    }
    if ($cta && $url) {
        $out .= sprintf('<a class="rc-bento__item rc-bento__item--plain rc-bento__item--blue" href="%s"><span class="rc-bento__icon">%s</span><span class="rc-bento__code rc-bento__code--s">%s</span></a>', esc_url($url), rocert_svg('arrow-ur', 22), esc_html($cta));
    }
    return $out ? '<div class="rc-bento">' . $out . '</div>' : '';
}

function rocert_not_found_html(): string
{
    $links = [[rocert_t('home'), rocert_home_url()]];
    if ($hub = rocert_page_by_key('hub')) {
        $links[] = [rocert_t('certifications'), get_permalink($hub)];
    }
    $links[] = [rocert_t('verify_cert'), rocert_verify_page_url()];
    $btns = '';
    foreach ($links as $i => [$label, $url]) {
        $btns .= sprintf('<div class="wp-block-button%s"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div>', $i ? ' is-style-outline' : '', esc_url($url), esc_html($label));
    }
    return sprintf('<p class="rc-outline-num">404</p><h1 class="wp-block-heading rc-h2">%s</h1><p class="rc-lead">%s</p><div class="wp-block-buttons">%s</div>', esc_html(rocert_t('404_title')), esc_html(rocert_t('404_text')), $btns);
}
