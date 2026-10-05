<?php
/**
 * Inserter previews: sample content for every ROCERT block, shown when hovering a block in the
 * inserter so editors see what they are adding. Inner blocks use the [name, attributes, inner] shape.
 */
defined('ABSPATH') || exit;

function rocert_block_examples(): array
{
    $media = (array) get_option('rocert_media', []);
    $img = static fn (string $key) => (int) ($media[$key] ?? 0);
    $standard = get_posts(['post_type' => 'page', 'posts_per_page' => 3, 'fields' => 'ids', 'lang' => '', 'meta_key' => '_rocert_type', 'meta_value' => 'standard']);
    $p = static fn (string $text) => ['core/paragraph', ['content' => $text]];
    $tile = static fn (string $n, string $title, string $text, string $tone = 'light') => ['rocert/tile', ['number' => $n, 'title' => $title, 'text' => $text, 'tone' => $tone]];

    return [
        'section' => ['attributes' => ['bg' => 'white'], 'innerBlocks' => [['core/heading', ['content' => 'Titlul secțiunii']], $p('Orice conținut: paragrafe, liste, butoane sau alte blocuri ROCERT.')]],
        'split' => ['attributes' => ['side' => 'left', 'image' => $img('meeting'), 'badgeValue' => '6', 'badgeText' => 'elemente verificate'], 'innerBlocks' => [['core/heading', ['content' => 'Ce pregătiți înainte de audit']], $p('Text alături de o imagine care pornește de la marginea ecranului.')]],
        'carousel' => ['attributes' => ['bigNumbers' => true], 'innerBlocks' => [$tile('01', 'Cerere și ofertă', 'Completați cererea; primiți oferta.'), $tile('02', 'Audit etapa 1', 'Evaluăm documentația.'), $tile('03', 'Audit etapa 2', 'Verificăm la fața locului.', 'dark')]],
        'tile' => ['attributes' => ['number' => '01', 'title' => 'Titlu card', 'text' => 'Text scurt care descrie cardul.', 'tone' => 'light']],
        'faq' => ['attributes' => ['eyebrow' => 'Întrebări frecvente', 'title' => 'Ce ne întreabă clienții'], 'innerBlocks' => [['core/details', ['summary' => 'Cât timp este valabil un certificat?'], [$p('3 ani, cu audituri anuale de supraveghere.')]], ['core/details', ['summary' => 'Pot certifica mai multe standarde deodată?'], [$p('Da, printr-un audit integrat.')]]]],
        'intro' => ['attributes' => ['eyebrow' => 'Ce este ISO 9001?', 'title' => 'Un standard. Aceeași exigență pentru orice organizație.'], 'innerBlocks' => [$p('ISO 9001 stabilește cerințele pentru un sistem de management al calității.')]],
        'section-head' => ['attributes' => ['eyebrow' => 'Cum decurge certificarea', 'title' => 'De la cerere la certificat', 'text' => 'Text lateral opțional.']],
        'hero' => ['attributes' => ['pill' => 'Organism de certificare', 'title' => 'Certificăm sisteme de management.', 'titleAccent' => 'Construim încredere.', 'lead' => 'Evaluăm și certificăm organizații din toată România.', 'image' => $img('hero-industry'), 'cardMeta' => 'EXEMPLU SRL']],
        'page-hero' => ['attributes' => ['badge' => 'SR EN ISO 9001:2015', 'title' => 'Certificare ISO 9001', 'dot' => false, 'subtitle' => 'Sisteme de management al calității', 'lead' => 'Antet de pagină închis, cu text decorativ mare în fundal.', 'outline' => '9001', 'buttons' => [['text' => 'Solicită ofertă', 'url' => '#', 'style' => 'fill']]]],
        'marquee' => ['attributes' => ['variant' => 'blue', 'items' => [['text' => 'ISO 9001'], ['text' => 'ISO 14001'], ['text' => 'ISO 45001'], ['text' => 'ISO/IEC 27001']]]],
        'statement' => ['attributes' => ['text' => 'De aproape trei decenii auditem și certificăm organizații.', 'muted' => 'Independent și riguros:', 'accent' => 'un certificat care înseamnă ceva.', 'stats' => [['value' => '1997', 'label' => 'anul înființării'], ['value' => '18', 'label' => 'standarde']]]],
        'category-index' => ['attributes' => ['eyebrow' => 'Domenii de certificare', 'title' => 'Standardul potrivit pentru fiecare organizație']],
        'why' => ['attributes' => ['eyebrow' => 'De ce ROCERT', 'title' => 'Rigoare în audit.', 'image' => $img('meeting'), 'cards' => [['title' => 'Imparțialitate', 'text' => 'Decizii independente.'], ['title' => 'Competență', 'text' => 'Auditori cu experiență.'], ['title' => 'Audit integrat', 'text' => 'Un singur program.'], ['title' => 'Ofertă rapidă', 'text' => 'În maximum 3 zile.']]]],
        'verify-band' => ['attributes' => ['title' => 'Verificați un certificat ROCERT', 'lead' => 'Introduceți seria de pe certificat.', 'cards' => [['status' => 'valid', 'title' => 'EXEMPLU SRL', 'meta' => 'ISO 9001:2015'], ['status' => 'invalid', 'title' => 'EXEMPLU SA', 'meta' => 'ISO 14001:2015']]]],
        'verify' => ['attributes' => []],
        'cta-band' => ['attributes' => ['title' => 'Pregătit pentru certificare?', 'text' => 'Primiți oferta în maximum 3 zile lucrătoare.', 'buttonText' => 'Solicită ofertă', 'buttonUrl' => '#']],
        'contact-panel' => ['attributes' => ['title' => 'Să discutăm despre certificare', 'address' => 'Str. Iani Buzoiani nr. 1, București', 'ctaTitle' => 'Oferta pornește de la cerere', 'ctaText' => 'Completați cererea online.', 'ctaButton' => 'Completează cererea', 'ctaUrl' => '#', 'points' => [['text' => 'Ofertă în 3 zile lucrătoare']]]],
        'facts' => ['attributes' => ['items' => [['label' => 'Standard', 'value' => 'SR EN ISO 9001:2015'], ['label' => 'Valabilitate', 'value' => '3 ani'], ['label' => 'Supraveghere', 'value' => 'Anual'], ['label' => 'Ofertă', 'value' => 'Max. 3 zile']]]],
        'numbered-grid' => ['attributes' => ['items' => [['title' => 'Contextul organizației', 'text' => 'Factori interni și externi.'], ['title' => 'Leadership', 'text' => 'Implicarea conducerii.'], ['title' => 'Planificare', 'text' => 'Riscuri și oportunități.']]]],
        'related' => ['attributes' => ['pages' => array_map(static fn ($id) => ['page' => $id], $standard), 'ctaText' => 'Solicită ofertă', 'ctaUrl' => '#']],
        'category-hero' => ['attributes' => ['image' => $img('audit-documents'), 'title' => 'Calitate']],
        'category-standards' => ['attributes' => ['who' => [['text' => 'Orice organizație'], ['text' => 'Producție']]]],
        'category-strip' => ['attributes' => []],
        'category-zigzag' => ['attributes' => []],
        'standards-table' => ['attributes' => ['eyebrow' => 'Toate standardele', 'title' => 'Căutați după domeniu']],
        'legend' => ['attributes' => ['items' => [['title' => 'Valid', 'text' => 'Certificatul este în vigoare.', 'tone' => 'green'], ['title' => 'Nevalid', 'text' => 'Suspendat, retras sau expirat.', 'tone' => 'red']]]],
        'card' => ['attributes' => ['title' => 'Doriți propriul certificat?', 'text' => 'Ofertă în maximum 3 zile.', 'buttonText' => 'Cerere de certificare', 'buttonUrl' => '#']],
        'cards' => ['attributes' => ['items' => [['title' => 'Imparțialitate', 'text' => 'Certificăm, nu consultăm.', 'tone' => 'blue'], ['title' => 'Competență', 'text' => 'Auditori calificați.', 'tone' => 'light'], ['title' => 'Responsabilitate', 'text' => 'Decizii susținute de dovezi.', 'tone' => 'dark']]]],
        'recognitions' => ['attributes' => ['items' => [['title' => 'RENAR', 'text' => 'Acreditare ca organism de certificare.'], ['title' => 'IQNet', 'text' => 'Certificate IQNet la cerere.']]]],
        'form' => ['attributes' => ['form' => (int) (((array) get_option('rocert_forms', []))['request_ro'] ?? 0)]],
        'breadcrumbs' => ['attributes' => []],
        'lang-switch' => ['attributes' => []],
        'logo' => ['attributes' => ['variant' => 'black', 'width' => 120, 'link' => false]],
        'wordmark' => ['attributes' => []],
        'cookie-settings' => ['attributes' => []],
        'not-found' => ['attributes' => []],
        'toc' => ['attributes' => []],
    ];
}
