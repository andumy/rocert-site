<?php
/** Builders for the data-driven certification pages: hub, category, standard — composed from ROCERT blocks. */

/** Splits "text <span class=\"rc-accent\">accent</span>" into the statement block's fields */
function rs_statement_attrs(string $html): array
{
    if (preg_match('/^(.*?)<span class="rc-accent">(.*?)<\/span>\s*$/s', $html, $m)) {
        return ['text' => trim(wp_strip_all_tags($m[1])), 'accent' => trim(wp_strip_all_tags($m[2]))];
    }
    return ['text' => wp_strip_all_tags($html)];
}

function rs_section(string $inner, array $attrs = []): string
{
    return rb_block('section', $attrs, $inner);
}

function rs_faq_block(array $faqs, string $eyebrow, string $title): string
{
    $items = '';
    foreach ($faqs as $f) {
        $items .= rb_details($f['q'], wp_kses($f['a'], ['a' => ['href' => true, 'target' => true, 'rel' => true]]));
    }
    return rb_block('faq', ['eyebrow' => $eyebrow, 'title' => $title], $items);
}

function rs_standard_page(array $std, array $cat, string $lang, array $media): string
{
    $d = $std[$lang];
    $code = $std['code'];

    $out = rb_block('page-hero', [
        'badge' => $std['edition'], 'title' => $d['title'], 'dot' => false, 'subtitle' => $d['name'], 'lead' => $d['lead'], 'outline' => $std['outline'], 'overlap' => true,
        'buttons' => [['text' => sprintf(rs_l($lang, 'request_for'), $code), 'url' => rs_url($lang, 'request') . '?standard=' . $std['key'], 'style' => 'fill'], ['text' => rs_l($lang, 'verify'), 'url' => rs_url($lang, 'verify'), 'style' => 'outline']],
    ]);
    $out .= rb_block('facts', ['items' => [
        ['label' => rs_l($lang, 'fact_std'), 'value' => $std['edition']],
        ['label' => rs_l($lang, 'fact_valid'), 'value' => rs_l($lang, 'fact_valid_v')],
        ['label' => rs_l($lang, 'fact_surv'), 'value' => rs_l($lang, 'fact_surv_v')],
        ['label' => rs_l($lang, 'fact_offer'), 'value' => rs_l($lang, 'fact_offer_v')],
    ]]);

    $intro = '';
    foreach ($d['intro'] as $para) {
        $intro .= rb_p(esc_html($para));
    }
    $kw = rs_std_keyphrase($std, $lang);
    $kw_inline = $lang === 'ro' ? mb_strtolower(mb_substr($kw, 0, 1)) . mb_substr($kw, 1) : $kw;
    $intro .= rb_p($lang === 'ro'
        ? 'Pentru ' . esc_html($kw_inline) . ', ROCERT auditează sistemul după ediția în vigoare a standardului. Textul oficial al standardului este publicat de <a href="https://www.iso.org/" target="_blank" rel="noopener">Organizația Internațională de Standardizare (ISO)</a>, iar versiunea română de <a href="https://www.asro.ro/" target="_blank" rel="noopener">ASRO</a>.'
        : 'For ' . esc_html($kw) . ', ROCERT audits your system against the edition in force. The official text of the standard is published by the <a href="https://www.iso.org/" target="_blank" rel="noopener">International Organization for Standardization (ISO)</a>; the Romanian edition by <a href="https://www.asro.ro/" target="_blank" rel="noopener">ASRO</a>.');
    $out .= rs_section(rb_block('intro', ['eyebrow' => $d['intro_heading'], 'title' => $d['statement']], $intro));

    $tiles = '';
    foreach ($d['benefits'] as $i => $b) {
        $tiles .= rb_block('tile', ['number' => sprintf('%02d', $i + 1), 'title' => $b['title'], 'text' => $b['text'], 'tone' => $i === 1 ? 'blue' : ($i === 3 ? 'dark' : 'light')]);
    }
    $out .= rs_section(
        rb_block('section-head', ['eyebrow' => rs_l($lang, 'benefits'), 'title' => sprintf(rs_l($lang, 'benefits_h'), $code)])
        . rb_block('carousel', ['autoplay' => true, 'interval' => 5], $tiles),
        ['spacing' => 'top0', 'width' => 'full']
    );

    $out .= rs_section(
        rb_block('section-head', ['eyebrow' => rs_l($lang, 'reqs'), 'title' => $d['requirements_heading']])
        . rb_block('numbered-grid', ['items' => array_map(static fn ($r) => ['title' => $r['title'], 'text' => $r['text']], $d['requirements'])]),
        ['spacing' => 'top0']
    );

    $out .= rb_block('marquee', ['variant' => 'light', 'sep' => '●', 'items' => rb_rows($d['who'])]);

    $prep = rb_p(esc_html(rs_l($lang, 'prep_eyebrow')), 'is-style-eyebrow')
        . rb_heading(esc_html(rs_l($lang, 'prep_h')), 2)
        . rb_list(array_map('esc_html', $d['prep']), 'is-style-numbered', true);
    $out .= rb_block('split', ['side' => 'left', 'image' => $media[$std['image']] ?? 0, 'alt' => ($lang === 'ro' ? 'Pregătirea auditului pentru ' : 'Audit preparation for ') . $kw_inline, 'badgeValue' => (string) count($d['prep']), 'badgeText' => rs_l($lang, 'prep_badge'), 'tall' => true], $prep);

    $out .= rs_section(rs_faq_block($d['faqs'], 'FAQ', $kw . ($lang === 'ro' ? ': întrebări frecvente' : ': frequently asked questions')), ['spacing' => 'top0']);
    $out .= rs_section(rb_block('cta-band', ['title' => sprintf(rs_l($lang, 'cta_h'), $code), 'text' => rs_l($lang, 'cta_p'), 'buttonText' => rs_l($lang, 'request'), 'buttonUrl' => rs_url($lang, 'request') . '?standard=' . $std['key']]), ['spacing' => 'top0']);
    $out .= rs_section(
        rb_block('section-head', ['title' => rs_l($lang, 'related')])
        . rb_block('related', ['pages' => array_map(static fn ($k) => ['page' => (int) ($GLOBALS['rs_page_ids'][$k][$lang] ?? 0)], $std['related']), 'ctaText' => rs_l($lang, 'related_cta'), 'ctaUrl' => rs_url($lang, 'request')]),
        ['spacing' => 'top0']
    );
    return $out;
}

function rs_category_page(array $cat, array $stds, string $lang, array $media): string
{
    $d = $cat[$lang];
    $featured = $stds[0] ?? null;

    $codes = implode(', ', array_map(static fn ($st) => $st['code'], $stds));
    $lead = mb_strtoupper(mb_substr($d['kw'], 0, 1)) . mb_substr($d['kw'], 1) . ($lang === 'ro' ? ' pentru organizații din toată România: ' : ' for organisations across Romania: ') . $codes . '.';
    $kw_title = mb_strtoupper(mb_substr($d['kw'], 0, 1)) . mb_substr($d['kw'], 1);
    $out = rb_block('category-hero', ['image' => $media[$cat['image']] ?? 0, 'alt' => $kw_title, 'lead' => $lead]);
    $out .= rs_section(rb_block('statement', rs_statement_attrs($d['statement']) + ['offset' => true, 'deco' => 'swoosh']));
    $out .= rb_block('category-standards', ['heading' => $kw_title . ($lang === 'ro' ? ': standardele din acest domeniu' : ': the standards in this area'), 'whoLabel' => rs_l($lang, 'who'), 'who' => rb_rows(array_slice($d['who'], 0, 5)), 'popular' => count($stds) > 1 ? rs_l($lang, 'popular') : '']);
    $out .= rb_block('marquee', ['variant' => 'blue', 'sep' => '+', 'label' => rs_l($lang, 'combos'), 'items' => rb_rows($cat['combos'])]);

    $mig = rb_heading(esc_html(rs_l($lang, 'migrate_h')), 2)
        . rb_p(esc_html(rs_l($lang, 'migrate_p')), 'is-style-lead')
        . rb_buttons([[rs_l($lang, 'migrate_btn'), rs_url($lang, 'request') . '?tip=migration' . ($featured ? '&standard=' . $featured['key'] : '')]]);
    $out .= rb_block('split', ['side' => 'right', 'image' => $media[$featured['image'] ?? $cat['image']] ?? 0, 'hideMediaMobile' => true], $mig);
    return $out;
}

function rs_hub_page(string $lang): string
{
    $t = [
        'ro' => ['title' => 'Certificări', 'lead' => 'Certificări ISO și scheme sectoriale: 18 standarde, grupate în șapte domenii. Combinați mai multe într-un singur audit integrat.', 'eyebrow' => 'Toate standardele', 'table' => 'Certificări ISO după domeniu', 'cta_h' => 'Mai multe standarde, un singur audit', 'cta_p' => 'Dacă aveți un sistem de management integrat, auditul integrat sau combinat reduce timpul alocat auditului, fără compromisuri de rigoare.', 'cta' => 'Cerere de certificare'],
        'en' => ['title' => 'Certifications', 'lead' => 'ISO certifications and sector schemes: 18 standards, grouped into seven areas. Combine several of them in a single integrated audit.', 'eyebrow' => 'All standards', 'table' => 'ISO certifications by area', 'cta_h' => 'Several standards, one audit', 'cta_p' => 'If you run an integrated management system, an integrated or combined audit reduces audit time without compromising on rigour.', 'cta' => 'Certification request'],
    ][$lang];
    $out = rb_block('page-hero', ['variant' => 'light', 'title' => $t['title'], 'lead' => $t['lead'], 'buttons' => [['text' => rs_l($lang, 'request'), 'url' => rs_url($lang, 'request'), 'style' => 'fill']]]);
    $out .= rb_block('category-strip');
    $out .= rs_section(rb_block('category-zigzag'), ['spacing' => 'tight', 'width' => 'full']);
    $out .= rs_section(rb_block('standards-table', ['eyebrow' => $t['eyebrow'], 'title' => $t['table']]), ['bg' => 'dark']);
    $out .= rs_section(rb_block('cta-band', ['title' => $t['cta_h'], 'text' => $t['cta_p'], 'buttonText' => $t['cta'], 'buttonUrl' => rs_url($lang, 'request'), 'tone' => 'dark']));
    return $out;
}

/** Focus keyphrase: the SEO title up to the dash ("Certificare ISO 9001"), kept distinct between languages */
function rs_std_keyphrase(array $std, string $lang): string
{
    $kw = static fn (string $l) => trim(explode('–', $std[$l]['seo_title'])[0]);
    $phrase = $kw($lang);
    if ($lang === 'en' && mb_strtolower($phrase) === mb_strtolower($kw('ro'))) {
        $phrase .= ' assessment';
    }
    return $phrase;
}
