<?php
/**
 * Initial content seed: media, forms, all RO/EN pages and their translations.
 *
 *   wp eval-file content/seed.php          first run only (skipped once `rocert_seeded` is set)
 *   wp eval-file content/seed.php force    re-apply over existing pages (overwrites wp-admin edits)
 */

if (!defined('WP_CLI')) {
    exit;
}

$force = in_array('force', $args ?? [], true);
if (get_option('rocert_seeded') && !$force) {
    WP_CLI::log('Already seeded; use `make reseed` to overwrite.');
    return;
}

require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
foreach (['blocks', 'urls', 'forms', 'pages-certs', 'pages-site'] as $lib) {
    require_once __DIR__ . "/lib/{$lib}.php";
}

const RS_LANGS = ['ro', 'en'];
const RS_STD_IMAGES = [
    'iso-9001' => 'quality', 'sr-en-15224' => 'medical', 'iso-13485' => 'medical-devices', 'gdp' => 'logistics-warehouse',
    'iso-14001' => 'environment', 'iso-50001' => 'energy', 'iso-45001' => 'safety', 'iso-39001' => 'road-safety',
    'iso-27001' => 'infosec', 'iso-20000-1' => 'it-services', 'iso-22000' => 'food', 'iso-ts-22002-1' => 'food',
    'iso-ts-22002-4' => 'food-packaging', 'iso-37001' => 'governance', 'iso-37301' => 'compliance', 'iso-20400' => 'procurement',
    'iso-19650' => 'bim',
];

/* ---------------- Media ---------------- */
WP_CLI::log('Media…');
$media = (array) get_option('rocert_media', []);
$manifest = json_decode((string) file_get_contents(__DIR__ . '/media/manifest.json'), true) ?: [];
$manifest[] = ['key' => 'logo', 'file' => 'logo.png', 'alt_ro' => 'Logo ROCERT', 'alt_en' => 'ROCERT logo'];
foreach ($manifest as $item) {
    $key = $item['key'];
    if (!empty($media[$key]) && get_post($media[$key])) {
        continue;
    }
    $src = __DIR__ . '/media/' . $item['file'];
    if (!is_file($src)) {
        WP_CLI::warning("Missing media file {$item['file']}");
        continue;
    }
    $tmp = wp_tempnam($item['file']);
    copy($src, $tmp);
    $id = media_handle_sideload(['name' => 'rocert-' . $item['file'], 'tmp_name' => $tmp], 0, $item['alt_ro']);
    if (is_wp_error($id)) {
        WP_CLI::warning($key . ': ' . $id->get_error_message());
        continue;
    }
    update_post_meta($id, '_wp_attachment_image_alt', $item['alt_ro']);
    update_post_meta($id, '_rocert_media_key', $key);
    $media[$key] = $id;
    WP_CLI::log("  {$key} → {$id}");
}
update_option('rocert_media', $media, false);

/* ---------------- Forms ---------------- */
WP_CLI::log('Forms…');
$forms = (array) get_option('rocert_forms', []);
foreach (['contact_ro', 'contact_en'] as $obsolete) {
    if (!empty($forms[$obsolete])) {
        $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->prefix . 'fluentform_forms', ['id' => (int) $forms[$obsolete]]);
        $GLOBALS['wpdb']->delete($GLOBALS['wpdb']->prefix . 'fluentform_form_meta', ['form_id' => (int) $forms[$obsolete]]);
        unset($forms[$obsolete]);
    }
}
foreach (RS_LANGS as $lang) {
    $ro = $lang === 'ro';
    [$fields, $submit] = ff_request_form($lang);
    $forms['request_' . $lang] = ff_save(
        $ro ? 'Cerere de certificare C02/ROC (RO)' : 'Certification request C02/ROC (EN)', $fields, $submit,
        $ro ? 'Mulțumim! Am primit cererea de certificare. Vă trimitem oferta în maximum 3 zile lucrătoare.' : 'Thank you! We received your certification request. You will receive our quote within 3 working days.',
        ($ro ? '[ROCERT] Cerere de certificare — ' : '[ROCERT] Certification request — ') . '{inputs.company_name} ({inputs.request_type})', (int) ($forms['request_' . $lang] ?? 0)
    );
}
update_option('rocert_forms', $forms, false);

/* ---------------- Pages ---------------- */
$translations = [];

/**
 * @param array{key:string, lang:string, title:string, slug:string, parent?:int, content:string, template?:string, order?:int, meta?:array, seo_title?:string, seo_desc?:string, bc?:string} $p
 */
function rs_upsert_page(array $p, bool $force, array &$translations): int
{
    $existing = get_posts(['post_type' => 'page', 'post_status' => 'any', 'posts_per_page' => 1, 'lang' => '', 'meta_query' => [['key' => '_rocert_key', 'value' => $p['key']], ['key' => '_rocert_lang', 'value' => $p['lang']]]]);
    $postarr = [
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => $p['title'],
        'post_name' => $p['slug'],
        'post_parent' => $p['parent'] ?? 0,
        'menu_order' => $p['order'] ?? 0,
        'post_content' => $p['content'] ?? '',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
        'post_author' => 1,
    ];
    if ($existing) {
        $id = $existing[0]->ID;
        if ($force) {
            unset($postarr['post_content']);
            wp_update_post(wp_slash(['ID' => $id] + $postarr));
        }
    } else {
        $id = wp_insert_post(wp_slash($postarr), true);
        if (is_wp_error($id)) {
            WP_CLI::error($p['key'] . ': ' . $id->get_error_message());
        }
    }
    update_post_meta($id, '_rocert_key', $p['key']);
    update_post_meta($id, '_rocert_lang', $p['lang']);
    update_post_meta($id, '_wp_page_template', $p['template'] ?? '');
    foreach ($p['meta'] ?? [] as $k => $v) {
        update_post_meta($id, '_rocert_' . $k, $v);
    }
    if (!empty($p['seo_title'])) {
        update_post_meta($id, '_yoast_wpseo_title', $p['seo_title'] . ' %%sep%% %%sitename%%');
    }
    if (!empty($p['seo_desc'])) {
        update_post_meta($id, '_yoast_wpseo_metadesc', $p['seo_desc']);
    }
    if (!empty($p['kw'])) {
        update_post_meta($id, '_yoast_wpseo_focuskw', $p['kw']);
    }
    if (!empty($p['bc'])) {
        update_post_meta($id, '_yoast_wpseo_bctitle', $p['bc']);
    }
    if (function_exists('pll_set_post_language')) {
        pll_set_post_language($id, $p['lang']);
    }
    $translations[$p['key']][$p['lang']] = $id;
    return (int) $id;
}

$categories = require __DIR__ . '/data/categories.php';
$standards = [];
foreach (glob(__DIR__ . '/data/standards/*.php') as $file) {
    $s = require $file;
    $s['image'] = RS_STD_IMAGES[$s['key']] ?? 'quality';
    $standards[$s['key']] = $s;
}
$legal = [];
foreach (['public-info', 'privacy', 'cookies', 'terms'] as $k) {
    $legal[$k] = require __DIR__ . "/data/legal/{$k}.php";
}

$site = [
    'ro' => [
        'home' => ['Acasă', 'acasa', 'Certificare ISO în România – organism de certificare', 'Certificare ISO în România din 1997: ROCERT auditează sisteme ISO 9001, 14001, 45001, 27001 și 22000. Ofertă în 3 zile. Verificați un certificat.', 'certificare ISO'],
        'hub' => ['Certificări', 'certificari', 'Certificări ISO și scheme sectoriale', 'Certificări ISO și scheme sectoriale: 18 standarde pentru calitate, mediu, energie, SSM, securitatea informației, siguranță alimentară și BIM.', 'certificări ISO'],
        'verify' => ['Verifică certificat', 'verifica-certificat', 'Verificare certificat ROCERT – valid sau nu', 'Verificare certificat online: introduceți seria de pe certificatul ROCERT și aflați dacă este valid, pentru ce organizație și standard.', 'verificare certificat'],
        'request' => ['Cerere de certificare', 'cerere-de-certificare', 'Cerere de certificare și ofertă', 'Cerere de certificare online: ofertă, certificare, migrare, extindere, restrângere sau recertificare. Răspuns în maximum 3 zile lucrătoare.', 'cerere de certificare'],
        'contact' => ['Contact', 'contact', 'Contact ROCERT – București, Sector 1', 'Contact ROCERT: Str. Iani Buzoiani nr. 1, Sector 1, București. Telefon +40 21 224 26 39, office@rocert.ro. Oferte în maximum 3 zile.', 'contact ROCERT'],
        'about' => ['Despre noi', 'despre-noi', 'Organism de certificare din 1997 – despre ROCERT', 'ROCERT – Societatea Română pentru Certificare: organism de certificare independent pentru sisteme de management, din 1997. Imparțialitate și rigoare.', 'organism de certificare'],
        'accreditations' => ['Acreditări și recunoașteri', 'acreditari-si-recunoasteri', 'Acreditări ROCERT și recunoașteri', 'Acreditări ROCERT și recunoașteri ca organism de certificare a sistemelor de management: RENAR, IQNet și abilitări ministeriale.', 'acreditări ROCERT'],
    ],
    'en' => [
        'home' => ['Home', 'home', 'ISO certification in Romania – certification body', 'ISO certification in Romania since 1997: ROCERT audits ISO 9001, 14001, 45001, 27001 and 22000 systems. A quote in 3 days. Verify a certificate.', 'ISO certification'],
        'hub' => ['Certifications', 'certifications', 'ISO certifications and sector schemes', 'ISO certifications and sector schemes: 18 standards for quality, environment, energy, OH&S, information security, food safety and BIM.', 'ISO certifications'],
        'verify' => ['Verify a certificate', 'verify-certificate', 'Certificate verification – ROCERT certificates', 'Certificate verification online: enter the serial printed on a ROCERT certificate to see if it is valid, and for which organisation and standard.', 'certificate verification'],
        'request' => ['Certification request', 'certification-request', 'Certification request and quote', 'Certification request online: quote, certification, transfer, extension, reduction or recertification. A reply within 3 working days.', 'certification request'],
        'contact' => ['Contact', 'contact', 'ROCERT contact details – Bucharest', 'ROCERT contact details: Str. Iani Buzoiani nr. 1, Sector 1, Bucharest. Phone +40 21 224 26 39, office@rocert.ro. Quotes within 3 working days.', 'ROCERT contact details'],
        'about' => ['About us', 'about-us', 'Certification body since 1997 – about ROCERT', 'ROCERT, the Romanian Society for Certification: an independent certification body for management systems since 1997. Impartiality and rigour.', 'certification body'],
        'accreditations' => ['Accreditations and recognitions', 'accreditations-and-recognitions', 'ROCERT accreditations and recognitions', 'ROCERT accreditations and recognitions as a management system certification body: RENAR, IQNet and ministerial approvals.', 'ROCERT accreditations'],
    ],
];

/*
 * Pass 1 creates every page (tree, meta, SEO) so pass 2 can reference page ids in block attributes
 * (e.g. related standards). Pass 2 writes the block content.
 */
$GLOBALS['rs_page_ids'] = [];
$content = [];
foreach (RS_LANGS as $lang) {
    WP_CLI::log("Pages ({$lang})…");
    $s = $site[$lang];
    $page = static function ($key, $extra = []) use ($lang, $s, $force, &$translations) {
        return rs_upsert_page(['key' => $key, 'lang' => $lang, 'title' => $s[$key][0], 'slug' => $s[$key][1], 'seo_title' => $s[$key][2], 'seo_desc' => $s[$key][3], 'kw' => $s[$key][4]] + $extra, $force, $translations);
    };

    $content[$lang]['home'] = $page('home');
    $hub = $content[$lang]['hub'] = $page('hub', ['order' => 1]);
    foreach ($categories as $ci => $cat) {
        $cd = $cat[$lang];
        $cat_id = $content[$lang][$cat['key']] = rs_upsert_page([
            'key' => $cat['key'], 'lang' => $lang, 'title' => $cd['title'], 'slug' => $cd['slug'], 'parent' => $hub, 'order' => $ci,
            'meta' => ['type' => 'category', 'image' => $media[$cat['image']] ?? 0, 'card' => $cd['card'], 'extra' => $cd['extra'] ?? ''],
            'seo_title' => $cd['seo_title'], 'seo_desc' => $cd['meta'], 'kw' => $cd['kw'],
        ], $force, $translations);
        foreach ($cat['standards'] as $si => $sk) {
            $std = $standards[$sk];
            $sd = $std[$lang];
            $content[$lang][$sk] = rs_upsert_page([
                'key' => $sk, 'lang' => $lang, 'title' => $sd['title'], 'slug' => $sd['slug'], 'parent' => $cat_id, 'order' => $si,
                'meta' => ['type' => 'standard', 'code' => $std['code'], 'name' => $sd['name'], 'card' => $sd['card'], 'image' => $media[$std['image']] ?? 0, 'edition' => $std['edition'], 'outline' => $std['outline'], 'lead' => $sd['lead'], 'who' => wp_json_encode($sd['who'], JSON_UNESCAPED_UNICODE)],
                'seo_title' => $sd['seo_title'], 'seo_desc' => $sd['meta'], 'bc' => $std['code'], 'kw' => rs_std_keyphrase($std, $lang),
            ], $force, $translations);
        }
    }
    $content[$lang]['verify'] = $page('verify', ['order' => 2]);
    $content[$lang]['request'] = $page('request', ['order' => 3]);
    $about = $content[$lang]['about'] = $page('about', ['order' => 4]);
    $content[$lang]['accreditations'] = $page('accreditations', ['parent' => $about]);
    $content[$lang]['contact'] = $page('contact', ['order' => 6]);
    foreach ($legal as $lk => $ld) {
        $d = $ld[$lang];
        $content[$lang][$lk] = rs_upsert_page(['key' => $lk, 'lang' => $lang, 'title' => $d['title'], 'slug' => $d['slug'], 'template' => 'page-text', 'order' => 10, 'seo_title' => $d['seo_title'], 'seo_desc' => $d['meta'], 'kw' => mb_strtolower($d['title'])], $force, $translations);
    }
}
foreach ($translations as $key => $pair) {
    $GLOBALS['rs_page_ids'][$key] = $pair;
}

WP_CLI::log('Content…');
foreach (RS_LANGS as $lang) {
    $ids = $content[$lang];
    $html = [
        'home' => rs_home_page($lang, $media),
        'hub' => rs_hub_page($lang),
        'verify' => rs_verify_page($lang, $media),
        'request' => rs_request_page($lang, $forms),
        'about' => rs_about_page($lang, $media),
        'accreditations' => rs_accreditations_page($lang),
        'contact' => rs_contact_page($lang),
    ];
    foreach ($categories as $cat) {
        $html[$cat['key']] = rs_category_page($cat, array_map(static fn ($k) => $standards[$k], $cat['standards']), $lang, $media);
        foreach ($cat['standards'] as $sk) {
            $html[$sk] = rs_standard_page($standards[$sk], $cat, $lang, $media);
        }
    }
    foreach ($legal as $lk => $ld) {
        $html[$lk] = rs_legal_page($ld, $lang);
    }
    foreach ($html as $key => $body) {
        wp_update_post(wp_slash(['ID' => $ids[$key], 'post_content' => $body]));
    }
}

if (function_exists('pll_save_post_translations')) {
    foreach ($translations as $pair) {
        if (count($pair) > 1) {
            pll_save_post_translations($pair);
        }
    }
}

/* ---------------- Site settings that depend on content ---------------- */
update_option('show_on_front', 'page');
update_option('page_on_front', $translations['home']['ro']);
update_option('page_for_posts', 0);
foreach (['sample-page', 'privacy-policy'] as $slug) {
    $default = get_page_by_path($slug);
    if ($default && !get_post_meta($default->ID, '_rocert_key', true)) {
        wp_delete_post($default->ID, true);
    }
}
foreach (get_posts(['post_type' => 'post', 'posts_per_page' => -1, 'post_status' => 'any', 'lang' => '']) as $post) {
    wp_delete_post($post->ID, true);
}
update_option('wp_page_for_privacy_policy', $translations['privacy']['ro']);

if (class_exists('WPSEO_Options') && !empty($media['logo'])) {
    WPSEO_Options::set('company_logo', wp_get_attachment_url($media['logo']));
    WPSEO_Options::set('company_logo_id', $media['logo']);
    WPSEO_Options::set('og_default_image', wp_get_attachment_url($media['hero-industry']));
    WPSEO_Options::set('og_default_image_id', $media['hero-industry']);
}

if (function_exists('PLL')) {
    PLL()->model->clean_languages_cache();
}
update_option('rocert_seeded', gmdate('c'));
WP_CLI::success(sprintf('Seeded %d pages (%d forms, %d images).', array_sum(array_map('count', $translations)), count($forms), count($media)));
