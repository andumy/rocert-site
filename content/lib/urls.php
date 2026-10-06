<?php
/** Root-relative URLs, so seeded content stays valid on local, stage and prod alike. */

const RS_LANGS = ['ro', 'en'];

const RS_SLUGS = [
    'ro' => ['home' => '/', 'hub' => '/certificari/', 'verify' => '/verifica-certificat/', 'request' => '/cerere-de-certificare/', 'contact' => '/contact/', 'about' => '/despre-noi/', 'accreditations' => '/despre-noi/acreditari-si-recunoasteri/', 'public' => '/informatii-publice/', 'privacy' => '/politica-de-confidentialitate/', 'cookies' => '/politica-cookie-uri/', 'terms' => '/termeni-si-conditii/'],
    'en' => ['home' => '/en/', 'hub' => '/en/certifications/', 'verify' => '/en/verify-certificate/', 'request' => '/en/certification-request/', 'contact' => '/en/contact/', 'about' => '/en/about-us/', 'accreditations' => '/en/about-us/accreditations-and-recognitions/', 'public' => '/en/public-information/', 'privacy' => '/en/privacy-policy/', 'cookies' => '/en/cookie-policy/', 'terms' => '/en/terms-of-use/'],
];

function rs_url(string $lang, string $key): string
{
    return RS_SLUGS[$lang][$key];
}

function rs_cat_url(string $lang, array $cat): string
{
    return rs_url($lang, 'hub') . $cat[$lang]['slug'] . '/';
}

function rs_std_url(string $lang, array $cat, array $std): string
{
    return rs_cat_url($lang, $cat) . $std[$lang]['slug'] . '/';
}

/** Short UI labels shared by the page builders */
function rs_l(string $lang, string $key): string
{
    static $l = [
        'ro' => [
            'request' => 'Solicită ofertă', 'request_for' => 'Solicită ofertă %s', 'verify' => 'Verifică un certificat', 'details' => 'Detalii %s',
            'fact_std' => 'Standard de referință', 'fact_valid' => 'Valabilitatea certificatului', 'fact_valid_v' => '3 ani', 'fact_surv' => 'Audituri de supraveghere', 'fact_surv_v' => 'Anual', 'fact_offer' => 'Termen pentru ofertă', 'fact_offer_v' => 'Max. 3 zile lucrătoare',
            'benefits' => 'Beneficii', 'benefits_h' => 'De ce %s', 'scroll' => 'Derulați orizontal →', 'reqs' => 'Cerințe-cheie', 'prep_eyebrow' => 'Pregătirea auditului', 'prep_h' => 'Ce pregătiți înainte de audit', 'prep_badge' => 'elemente verificate în auditul de etapa 1',
            'faq_h' => 'Întrebări despre %s', 'cta_h' => 'Pregătit pentru certificarea %s?', 'cta_p' => 'Trimiteți cererea și primiți oferta personalizată în maximum 3 zile lucrătoare.', 'related' => 'Standarde înrudite', 'related_cta' => 'Solicită ofertă pentru un sistem integrat',
            'who' => 'Pentru cine', 'popular' => 'cel mai solicitat', 'combos' => 'Combinații frecvente pentru audit integrat', 'migrate_h' => 'Aveți deja un certificat?', 'migrate_p' => 'Îl puteți transfera la ROCERT prin migrare, fără să reluați ciclul de certificare de la zero.', 'migrate_btn' => 'Solicită migrarea',
        ],
        'en' => [
            'request' => 'Request a quote', 'request_for' => 'Request an %s quote', 'verify' => 'Verify a certificate', 'details' => '%s details',
            'fact_std' => 'Reference standard', 'fact_valid' => 'Certificate validity', 'fact_valid_v' => '3 years', 'fact_surv' => 'Surveillance audits', 'fact_surv_v' => 'Yearly', 'fact_offer' => 'Quote turnaround', 'fact_offer_v' => 'Max. 3 working days',
            'benefits' => 'Benefits', 'benefits_h' => 'Why %s', 'scroll' => 'Scroll sideways →', 'reqs' => 'Key requirements', 'prep_eyebrow' => 'Audit preparation', 'prep_h' => 'What to prepare before the audit', 'prep_badge' => 'items reviewed in the stage 1 audit',
            'faq_h' => '%s questions', 'cta_h' => 'Ready for %s certification?', 'cta_p' => 'Send your request and receive a tailored quote within 3 working days.', 'related' => 'Related standards', 'related_cta' => 'Request a quote for an integrated system',
            'who' => 'Who it is for', 'popular' => 'most requested', 'combos' => 'Common integrated audit combinations', 'migrate_h' => 'Already certified?', 'migrate_p' => 'You can transfer your certificate to ROCERT without restarting the certification cycle.', 'migrate_btn' => 'Request a transfer',
        ],
    ];
    return $l[$lang][$key];
}
