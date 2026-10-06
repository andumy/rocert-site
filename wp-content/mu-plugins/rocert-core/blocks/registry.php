<?php
/**
 * ROCERT block library. Each block is declared once here (attributes + editor controls); the generic
 * editor script (editor.js) builds the sidebar controls from this schema, and each block renders
 * server-side from blocks/render/{name}.php. Editors only ever change content, never markup.
 *
 * Control types: text, textarea, url, number, toggle, select, image, page, form, repeater.
 * 'inner' blocks hold editable core blocks (section, split, carousel, faq, intro).
 */
defined('ABSPATH') || exit;

require_once __DIR__ . '/examples.php';

const ROCERT_BG = ['' => 'Fundal pagină', 'white' => 'Alb', 'dark' => 'Închis', 'blue' => 'Albastru'];
const ROCERT_TONE = ['light' => 'Deschis', 'dark' => 'Închis', 'blue' => 'Albastru'];

function rocert_block_schema(): array
{
    $buttons = ['control' => 'repeater', 'label' => 'Butoane', 'fields' => ['text' => ['control' => 'text', 'label' => 'Text'], 'url' => ['control' => 'url', 'label' => 'Link'], 'style' => ['control' => 'select', 'label' => 'Stil', 'options' => ['fill' => 'Albastru', 'outline' => 'Contur']]]];
    $stats = ['control' => 'repeater', 'label' => 'Cifre', 'fields' => ['value' => ['control' => 'text', 'label' => 'Valoare'], 'label' => ['control' => 'text', 'label' => 'Descriere']]];
    $items_tt = ['control' => 'repeater', 'label' => 'Elemente', 'fields' => ['title' => ['control' => 'text', 'label' => 'Titlu'], 'text' => ['control' => 'textarea', 'label' => 'Text']]];

    return [
        /* ---- Layout containers ---- */
        'section' => ['title' => 'Secțiune', 'icon' => 'align-wide', 'description' => 'Bloc de secțiune: fundal, spațiere, lățime. Conținutul se adaugă înăuntru.',
            'inner' => ['allowed' => null],
            'attributes' => [
                'bg' => ['control' => 'select', 'label' => 'Fundal', 'options' => ROCERT_BG, 'default' => ''],
                'spacing' => ['control' => 'select', 'label' => 'Spațiere verticală', 'options' => ['normal' => 'Normală', 'tight' => 'Redusă', 'top0' => 'Fără sus', 'bottom0' => 'Fără jos', 'none' => 'Fără'], 'default' => 'normal'],
                'width' => ['control' => 'select', 'label' => 'Lățime conținut', 'options' => ['wrap' => 'Grilă (1240px)', 'narrow' => 'Text (860px)', 'full' => 'Toată lățimea'], 'default' => 'wrap'],
                'grid' => ['control' => 'toggle', 'label' => 'Grilă decorativă (fundal închis)', 'default' => false],
                'anchor' => ['control' => 'text', 'label' => 'Ancoră (id)', 'default' => ''],
            ]],
        'split' => ['title' => 'Imagine + conținut', 'icon' => 'columns', 'description' => 'Imagine până la marginea ecranului, conținut editabil alături.',
            'inner' => ['allowed' => null, 'template' => [['core/heading', ['placeholder' => 'Titlu']], ['core/paragraph', ['placeholder' => 'Text']]]],
            'attributes' => [
                'side' => ['control' => 'select', 'label' => 'Poziția imaginii', 'options' => ['left' => 'Stânga', 'right' => 'Dreapta'], 'default' => 'left'],
                'image' => ['control' => 'image', 'label' => 'Imagine', 'default' => 0],
                'alt' => ['control' => 'text', 'label' => 'Text alternativ imagine (gol = cel din Media)', 'default' => ''],
                'bg' => ['control' => 'select', 'label' => 'Fundal', 'options' => ROCERT_BG, 'default' => ''],
                'badgeValue' => ['control' => 'text', 'label' => 'Insignă: cifră (opțional)', 'default' => ''],
                'badgeText' => ['control' => 'text', 'label' => 'Insignă: text', 'default' => ''],
                'tall' => ['control' => 'toggle', 'label' => 'Imagine înaltă', 'default' => false],
                'hideMediaMobile' => ['control' => 'toggle', 'label' => 'Ascunde imaginea pe mobil', 'default' => false],
            ]],
        'carousel' => ['title' => 'Carusel de carduri', 'icon' => 'slides', 'description' => 'Carduri derulabile (tragere, săgeți, derulare automată).',
            'inner' => ['allowed' => ['rocert/tile'], 'template' => [['rocert/tile'], ['rocert/tile'], ['rocert/tile']], 'orientation' => 'horizontal'],
            'attributes' => [
                'autoplay' => ['control' => 'toggle', 'label' => 'Derulare automată', 'default' => true],
                'interval' => ['control' => 'number', 'label' => 'Interval (secunde)', 'default' => 5],
                'bigNumbers' => ['control' => 'toggle', 'label' => 'Numere mari (pași)', 'default' => false],
            ]],
        'tile' => ['title' => 'Card', 'icon' => 'id', 'parent' => ['rocert/carousel'], 'inline' => ['number', 'title', 'text'],
            'attributes' => [
                'number' => ['control' => 'text', 'label' => 'Număr / etichetă', 'default' => ''],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'text' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'tone' => ['control' => 'select', 'label' => 'Culoare', 'options' => ROCERT_TONE, 'default' => 'light'],
                'buttonText' => ['control' => 'text', 'label' => 'Buton: text (opțional)', 'default' => ''],
                'buttonUrl' => ['control' => 'url', 'label' => 'Buton: link', 'default' => ''],
            ]],
        'faq' => ['title' => 'Întrebări frecvente', 'icon' => 'editor-help', 'inline' => ['eyebrow', 'title'],
            'inner' => ['allowed' => ['core/details'], 'template' => [['core/details', ['summary' => 'Întrebare?']]]],
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => 'Întrebări frecvente'],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
            ]],
        'intro' => ['title' => 'Introducere în două coloane', 'icon' => 'text', 'inline' => ['eyebrow', 'title'],
            'inner' => ['allowed' => ['core/paragraph', 'core/list', 'core/heading'], 'template' => [['core/paragraph']]],
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''],
                'title' => ['control' => 'textarea', 'label' => 'Afirmație (titlu)', 'default' => ''],
            ]],

        /* ---- Content components ---- */
        'section-head' => ['title' => 'Titlu de secțiune', 'icon' => 'heading', 'inline' => ['eyebrow', 'title', 'text'],
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'text' => ['control' => 'textarea', 'label' => 'Text lateral (opțional)', 'default' => ''],
                'level' => ['control' => 'select', 'label' => 'Nivel titlu', 'options' => ['2' => 'H2', '3' => 'H3'], 'default' => '2'],
            ]],
        'hero' => ['title' => 'Hero acasă', 'icon' => 'cover-image',
            'attributes' => [
                'pill' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'titleAccent' => ['control' => 'text', 'label' => 'Titlu – partea albastră', 'default' => ''],
                'lead' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'note' => ['control' => 'textarea', 'label' => 'Notă sub căutare (HTML simplu permis)', 'default' => ''],
                'image' => ['control' => 'image', 'label' => 'Imagine', 'default' => 0],
                'alt' => ['control' => 'text', 'label' => 'Text alternativ imagine (gol = cel din Media)', 'default' => ''],
                'cardLabel' => ['control' => 'text', 'label' => 'Card: etichetă', 'default' => 'CERTIFICAT'],
                'cardStatus' => ['control' => 'text', 'label' => 'Card: status', 'default' => 'Valid'],
                'cardTitle' => ['control' => 'text', 'label' => 'Card: titlu', 'default' => 'ISO 9001:2015'],
                'cardMeta' => ['control' => 'text', 'label' => 'Card: detalii', 'default' => ''],
            ]],
        'page-hero' => ['title' => 'Antet de pagină', 'icon' => 'cover-image',
            'attributes' => [
                'variant' => ['control' => 'select', 'label' => 'Variantă', 'options' => ['dark' => 'Închis', 'light' => 'Deschis (titlu mare + text lateral)'], 'default' => 'dark'],
                'badge' => ['control' => 'text', 'label' => 'Insignă', 'default' => ''],
                'title' => ['control' => 'textarea', 'label' => 'Titlu (Enter = rând nou)', 'default' => ''],
                'dot' => ['control' => 'toggle', 'label' => 'Punct albastru după titlu', 'default' => true],
                'subtitle' => ['control' => 'text', 'label' => 'Subtitlu', 'default' => ''],
                'lead' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'outline' => ['control' => 'text', 'label' => 'Text decorativ mare (contur)', 'default' => ''],
                'deco' => ['control' => 'select', 'label' => 'Decor', 'options' => ['' => 'Niciunul', 'shield' => 'Scut'], 'default' => ''],
                'buttons' => $buttons + ['default' => [], 'max' => 2],
                'stats' => $stats + ['default' => [], 'max' => 3],
                'overlap' => ['control' => 'toggle', 'label' => 'Spațiu pentru conținut suprapus dedesubt', 'default' => false],
            ]],
        'marquee' => ['title' => 'Bandă derulantă', 'icon' => 'leftright',
            'attributes' => [
                'items' => ['control' => 'repeater', 'label' => 'Texte', 'fields' => ['text' => ['control' => 'text', 'label' => 'Text']], 'default' => []],
                'variant' => ['control' => 'select', 'label' => 'Stil', 'options' => ['tilt' => 'Albastru înclinat', 'blue' => 'Albastru', 'light' => 'Deschis'], 'default' => 'light'],
                'label' => ['control' => 'text', 'label' => 'Etichetă deasupra', 'default' => ''],
                'sep' => ['control' => 'text', 'label' => 'Separator', 'default' => '✦'],
            ]],
        'statement' => ['title' => 'Afirmație + cifre', 'icon' => 'format-quote',
            'attributes' => [
                'text' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'muted' => ['control' => 'textarea', 'label' => 'Continuare (gri)', 'default' => ''],
                'accent' => ['control' => 'text', 'label' => 'Final (albastru)', 'default' => ''],
                'offset' => ['control' => 'toggle', 'label' => 'Decalat spre dreapta', 'default' => false],
                'deco' => ['control' => 'select', 'label' => 'Grafică în stânga (când e decalat)', 'options' => ['' => 'Fără', 'swoosh' => 'Simbolul ROCERT (contur)'], 'default' => ''],
                'stats' => $stats + ['default' => [], 'max' => 4],
            ]],
        'category-index' => ['title' => 'Index domenii (acasă)', 'icon' => 'list-view', 'description' => 'Se completează automat din paginile de domenii.',
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'cta' => ['control' => 'text', 'label' => 'Text buton (gol = automat)', 'default' => ''],
            ]],
        'why' => ['title' => 'Imagine cu carduri suprapuse', 'icon' => 'images-alt2',
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'image' => ['control' => 'image', 'label' => 'Imagine', 'default' => 0],
                'alt' => ['control' => 'text', 'label' => 'Text alternativ imagine (gol = cel din Media)', 'default' => ''],
                'cards' => $items_tt + ['default' => [], 'max' => 4],
            ]],
        'verify-band' => ['title' => 'Bandă verificare certificat', 'icon' => 'shield',
            'attributes' => [
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'lead' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'cards' => ['control' => 'repeater', 'label' => 'Carduri exemplu', 'fields' => ['status' => ['control' => 'select', 'label' => 'Status', 'options' => ['valid' => 'Valid', 'invalid' => 'Nevalid']], 'title' => ['control' => 'text', 'label' => 'Organizație'], 'meta' => ['control' => 'text', 'label' => 'Detalii']], 'default' => [], 'max' => 2],
            ]],
        'verify' => ['title' => 'Verificare certificat', 'icon' => 'search', 'description' => 'Formularul de verificare după seria certificatului.', 'attributes' => []],
        'cta-band' => ['title' => 'Bandă îndemn (CTA)', 'icon' => 'megaphone',
            'attributes' => [
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'text' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'buttonText' => ['control' => 'text', 'label' => 'Buton', 'default' => ''],
                'buttonUrl' => ['control' => 'url', 'label' => 'Link', 'default' => ''],
                'tone' => ['control' => 'select', 'label' => 'Culoare', 'options' => ['blue' => 'Albastru', 'dark' => 'Închis'], 'default' => 'blue'],
            ]],
        'contact-panel' => ['title' => 'Contact + îndemn', 'icon' => 'phone',
            'attributes' => [
                'eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => 'Contact'],
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'phone' => ['control' => 'text', 'label' => 'Telefon', 'default' => '+40 21 224 26 39'],
                'email' => ['control' => 'text', 'label' => 'E-mail', 'default' => 'office@rocert.ro'],
                'address' => ['control' => 'textarea', 'label' => 'Adresă', 'default' => ''],
                'ctaEyebrow' => ['control' => 'text', 'label' => 'Îndemn: etichetă', 'default' => ''],
                'ctaTitle' => ['control' => 'text', 'label' => 'Îndemn: titlu', 'default' => ''],
                'ctaText' => ['control' => 'textarea', 'label' => 'Îndemn: text', 'default' => ''],
                'ctaButton' => ['control' => 'text', 'label' => 'Îndemn: buton', 'default' => ''],
                'ctaUrl' => ['control' => 'url', 'label' => 'Îndemn: link', 'default' => ''],
                'points' => ['control' => 'repeater', 'label' => 'Îndemn: puncte', 'fields' => ['text' => ['control' => 'text', 'label' => 'Text']], 'default' => [], 'max' => 4],
                'secondaryText' => ['control' => 'text', 'label' => 'Link secundar: text', 'default' => ''],
                'secondaryUrl' => ['control' => 'url', 'label' => 'Link secundar: link', 'default' => ''],
            ]],
        'facts' => ['title' => 'Bară de informații', 'icon' => 'info',
            'attributes' => ['items' => ['control' => 'repeater', 'label' => 'Informații', 'fields' => ['label' => ['control' => 'text', 'label' => 'Etichetă'], 'value' => ['control' => 'text', 'label' => 'Valoare']], 'default' => [], 'max' => 4]]],
        'numbered-grid' => ['title' => 'Grilă numerotată', 'icon' => 'grid-view', 'attributes' => ['items' => $items_tt + ['default' => []]]],
        'related' => ['title' => 'Standarde înrudite', 'icon' => 'screenoptions',
            'attributes' => [
                'pages' => ['control' => 'repeater', 'label' => 'Pagini', 'fields' => ['page' => ['control' => 'page', 'label' => 'Pagină', 'pageType' => 'standard']], 'default' => [], 'max' => 3],
                'ctaText' => ['control' => 'text', 'label' => 'Card albastru: text', 'default' => ''],
                'ctaUrl' => ['control' => 'url', 'label' => 'Card albastru: link', 'default' => ''],
            ]],
        'category-hero' => ['title' => 'Antet domeniu', 'icon' => 'format-image', 'description' => 'Etichetele standardelor se generează automat din subpagini.',
            'attributes' => ['image' => ['control' => 'image', 'label' => 'Imagine', 'default' => 0], 'alt' => ['control' => 'text', 'label' => 'Text alternativ imagine (gol = cel din Media)', 'default' => ''], 'title' => ['control' => 'text', 'label' => 'Titlu (gol = titlul paginii)', 'default' => ''], 'lead' => ['control' => 'textarea', 'label' => 'Text sub titlu', 'default' => '']]],
        'category-standards' => ['title' => 'Standardele domeniului', 'icon' => 'list-view', 'description' => 'Se completează automat din subpaginile acestei pagini.',
            'attributes' => [
                'heading' => ['control' => 'text', 'label' => 'Titlu deasupra (opțional)', 'default' => ''],
                'whoLabel' => ['control' => 'text', 'label' => 'Etichetă „Pentru cine”', 'default' => 'Pentru cine'],
                'who' => ['control' => 'repeater', 'label' => 'Pentru cine (primul standard)', 'fields' => ['text' => ['control' => 'text', 'label' => 'Text']], 'default' => [], 'max' => 6],
                'popular' => ['control' => 'text', 'label' => 'Mențiune primul standard', 'default' => 'cel mai solicitat'],
            ]],
        'category-strip' => ['title' => 'Bandă imagini domenii', 'icon' => 'images-alt', 'description' => 'Automat, din paginile de domenii.', 'attributes' => []],
        'category-zigzag' => ['title' => 'Domenii în zig-zag', 'icon' => 'excerpt-view', 'description' => 'Automat, din paginile de domenii.', 'attributes' => []],
        'standards-table' => ['title' => 'Tabel standarde cu filtru', 'icon' => 'editor-table', 'description' => 'Automat, din paginile de standarde.',
            'attributes' => ['eyebrow' => ['control' => 'text', 'label' => 'Etichetă', 'default' => ''], 'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => '']]],
        'legend' => ['title' => 'Legendă statusuri', 'icon' => 'flag',
            'attributes' => ['items' => ['control' => 'repeater', 'label' => 'Statusuri', 'fields' => ['title' => ['control' => 'text', 'label' => 'Titlu'], 'text' => ['control' => 'textarea', 'label' => 'Text'], 'tone' => ['control' => 'select', 'label' => 'Culoare', 'options' => ['green' => 'Verde', 'red' => 'Roșu', 'grey' => 'Gri']]], 'default' => [], 'max' => 4]]],
        'card' => ['title' => 'Card cu buton', 'icon' => 'excerpt-view',
            'attributes' => [
                'title' => ['control' => 'text', 'label' => 'Titlu', 'default' => ''],
                'text' => ['control' => 'textarea', 'label' => 'Text', 'default' => ''],
                'buttonText' => ['control' => 'text', 'label' => 'Buton', 'default' => ''],
                'buttonUrl' => ['control' => 'url', 'label' => 'Link', 'default' => ''],
                'tone' => ['control' => 'select', 'label' => 'Culoare', 'options' => ROCERT_TONE, 'default' => 'dark'],
            ]],
        'cards' => ['title' => 'Grilă de carduri', 'icon' => 'grid-view',
            'attributes' => ['items' => ['control' => 'repeater', 'label' => 'Carduri', 'fields' => ['title' => ['control' => 'text', 'label' => 'Titlu'], 'text' => ['control' => 'textarea', 'label' => 'Text'], 'tone' => ['control' => 'select', 'label' => 'Culoare', 'options' => ROCERT_TONE]], 'default' => []]]],
        'recognitions' => ['title' => 'Listă recunoașteri', 'icon' => 'awards',
            'attributes' => ['note' => ['control' => 'textarea', 'label' => 'Notă (opțional)', 'default' => ''], 'items' => $items_tt + ['default' => []]]],
        'form' => ['title' => 'Formular', 'icon' => 'feedback',
            'attributes' => ['form' => ['control' => 'form', 'label' => 'Formular', 'default' => 0], 'title' => ['control' => 'text', 'label' => 'Titlu ascuns (accesibilitate)', 'default' => ''], 'help' => ['control' => 'textarea', 'label' => 'Text ajutor sub formular (HTML simplu permis)', 'default' => '']]],

        /* ---- Site chrome ---- */
        'breadcrumbs' => ['title' => 'Breadcrumbs', 'icon' => 'arrow-right-alt', 'attributes' => []],
        'lang-switch' => ['title' => 'Comutator limbă', 'icon' => 'translation', 'attributes' => []],
        'logo' => ['title' => 'Logo ROCERT', 'icon' => 'star-filled', 'attributes' => ['variant' => ['control' => 'select', 'label' => 'Culoare', 'options' => ['white' => 'Alb', 'black' => 'Negru'], 'default' => 'white'], 'width' => ['control' => 'number', 'label' => 'Lățime (px)', 'default' => 54], 'link' => ['control' => 'toggle', 'label' => 'Link spre prima pagină', 'default' => true]]],
        'wordmark' => ['title' => 'ROCERT decorativ (contur)', 'icon' => 'editor-textcolor', 'attributes' => []],
        'cookie-settings' => ['title' => 'Link setări cookie-uri', 'icon' => 'admin-generic', 'attributes' => ['label' => ['control' => 'text', 'label' => 'Text', 'default' => 'Setări cookie-uri']]],
        'not-found' => ['title' => 'Conținut 404', 'icon' => 'warning', 'attributes' => []],
        'toc' => ['title' => 'Cuprins automat', 'icon' => 'list-view', 'description' => 'Listează titlurile H2 ale paginii.', 'attributes' => ['title' => ['control' => 'text', 'label' => 'Titlu', 'default' => 'Cuprins']]],
    ];
}

/** Attribute types for register_block_type, derived from the controls */
function rocert_block_attr_types(array $attrs): array
{
    $map = ['text' => 'string', 'textarea' => 'string', 'url' => 'string', 'select' => 'string', 'number' => 'number', 'image' => 'number', 'page' => 'number', 'form' => 'number', 'toggle' => 'boolean', 'repeater' => 'array'];
    $out = [];
    foreach ($attrs as $key => $a) {
        $out[$key] = ['type' => $map[$a['control']] ?? 'string', 'default' => $a['default'] ?? null];
    }
    return $out;
}

add_action('init', static function (): void {
    wp_register_script(
        'rocert-blocks-editor',
        ROCERT_CORE_URL . '/blocks/editor.js',
        ['wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-i18n', 'wp-data'],
        ROCERT_CORE_VERSION . '.' . filemtime(__DIR__ . '/editor.js'),
        true
    );
    wp_register_style('rocert-blocks-editor', ROCERT_CORE_URL . '/blocks/editor.css', [], ROCERT_CORE_VERSION . '.' . filemtime(__DIR__ . '/editor.css'));

    foreach (rocert_block_schema() as $name => $def) {
        $args = [
            'api_version' => 3,
            'title' => $def['title'],
            'category' => 'rocert',
            'attributes' => rocert_block_attr_types($def['attributes']) + ['className' => ['type' => 'string', 'default' => '']],
            'editor_script_handles' => ['rocert-blocks-editor'],
            'editor_style_handles' => ['rocert-blocks-editor'],
            'supports' => ['html' => false, 'customClassName' => false, 'reusable' => false],
            'render_callback' => static function (array $attributes, string $content, WP_Block $block) use ($name): string {
                $file = __DIR__ . "/render/{$name}.php";
                if (!is_file($file)) {
                    return '';
                }
                ob_start();
                include $file;
                return (string) ob_get_clean();
            },
        ];
        if (!empty($def['parent'])) {
            $args['parent'] = $def['parent'];
        }
        register_block_type('rocert/' . $name, $args);
    }
});

add_filter('block_categories_all', static function (array $categories): array {
    array_unshift($categories, ['slug' => 'rocert', 'title' => 'ROCERT', 'icon' => null]);
    return $categories;
});

/* Schema + choices (pages, forms) for the editor */
add_action('enqueue_block_editor_assets', static function (): void {
    $pages = [];
    foreach (get_posts(['post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => -1, 'lang' => '', 'meta_key' => '_rocert_type', 'orderby' => 'menu_order', 'order' => 'ASC']) as $p) {
        $pages[] = ['id' => $p->ID, 'title' => get_the_title($p) . (function_exists('pll_get_post_language') ? ' [' . strtoupper((string) pll_get_post_language($p->ID)) . ']' : ''), 'type' => get_post_meta($p->ID, '_rocert_type', true)];
    }
    $forms = [];
    if (class_exists('\FluentForm\App\Models\Form')) {
        foreach (\FluentForm\App\Models\Form::select(['id', 'title'])->orderBy('id')->get() as $f) {
            $forms[] = ['id' => (int) $f->id, 'title' => $f->title];
        }
    }
    $schema = rocert_block_schema();
    foreach (rocert_block_examples() as $name => $example) {
        if (isset($schema[$name])) {
            $schema[$name]['example'] = $example;
        }
    }
    wp_add_inline_script('rocert-blocks-editor', 'window.rocertBlocks = ' . wp_json_encode(['schema' => $schema, 'pages' => $pages, 'forms' => $forms, 'bg' => ROCERT_BG], JSON_UNESCAPED_UNICODE) . ';', 'before');
});

/* Editor-friendly paragraph/heading styles for content inside sections */
add_action('init', static function (): void {
    register_block_style('core/paragraph', ['name' => 'eyebrow', 'label' => 'Etichetă']);
    register_block_style('core/paragraph', ['name' => 'lead', 'label' => 'Text mare']);
    register_block_style('core/paragraph', ['name' => 'note', 'label' => 'Notă (galben)']);
    register_block_style('core/heading', ['name' => 'display', 'label' => 'Foarte mare']);
    register_block_style('core/list', ['name' => 'numbered', 'label' => 'Numerotată (01, 02…)']);
    register_block_style('core/list', ['name' => 'big', 'label' => 'Listă mare']);
    register_block_style('core/list', ['name' => 'chips', 'label' => 'Etichete']);
});

/* Helpers used by render templates */
function rocert_bg_class(string $bg): string
{
    return match ($bg) {
        'white' => 'rc-surface',
        'dark' => 'rc-dark',
        'blue' => 'rc-blue',
        default => '',
    };
}

function rocert_attr(array $a, string $k): string
{
    return trim((string) ($a[$k] ?? ''));
}

/** Simple inline HTML allowed in a few text fields */
function rocert_kses_inline(string $html): string
{
    return wp_kses($html, ['a' => ['href' => true, 'target' => true, 'rel' => true], 'strong' => [], 'em' => [], 'br' => [], 'span' => ['class' => true]]);
}

function rocert_buttons_html(array $buttons, string $outline_extra = ''): string
{
    $out = '';
    $count = 0;
    foreach ($buttons as $b) {
        if (empty($b['text']) || empty($b['url'])) {
            continue;
        }
        $outline = ($b['style'] ?? 'fill') === 'outline';
        if (++$count > 2) {
            break;
        }
        $out .= sprintf('<div class="wp-block-button%s"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div>', $outline ? ' is-style-outline ' . $outline_extra : '', esc_url($b['url']), esc_html($b['text']));
    }
    return $out ? '<div class="wp-block-buttons">' . $out . '</div>' : '';
}

/** Alt text for a block image: the block's own field, else the attachment's */
function rocert_block_alt(array $attributes, int $image_id): string
{
    $alt = trim((string) ($attributes['alt'] ?? ''));
    return $alt !== '' ? $alt : (string) get_post_meta($image_id, '_wp_attachment_image_alt', true);
}
