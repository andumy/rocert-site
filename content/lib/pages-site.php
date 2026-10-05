<?php
/** Builders for the hand-written pages — composed from ROCERT blocks + core blocks. */

function rs_contact_panel_attrs(string $lang): array
{
    return [
        'ro' => [
            'eyebrow' => 'Contact', 'title' => 'Să discutăm despre certificarea dumneavoastră', 'address' => "Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43\nSector 1, București 011571",
            'ctaEyebrow' => 'Cerere de ofertă', 'ctaTitle' => 'Oferta pornește de la cererea de certificare',
            'ctaText' => 'Pentru a stabili durata auditului și a vă trimite o ofertă corectă avem nevoie de câteva informații despre organizație. Completați cererea online — durează aproximativ 15 minute.',
            'points' => rb_rows(['Ofertă personalizată în maximum 3 zile lucrătoare', 'Pentru certificare, migrare, extindere, restrângere sau recertificare', 'Datele firmei se preiau automat după CUI']),
            'ctaButton' => 'Completează cererea', 'ctaUrl' => rs_url('ro', 'request'), 'secondaryText' => 'Verifică un certificat', 'secondaryUrl' => rs_url('ro', 'verify'),
        ],
        'en' => [
            'eyebrow' => 'Contact', 'title' => 'Let’s talk about your certification', 'address' => "Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43\nSector 1, Bucharest 011571, Romania",
            'ctaEyebrow' => 'Request a quote', 'ctaTitle' => 'Every quote starts with the certification request',
            'ctaText' => 'To determine the audit duration and send you an accurate quote we need some information about your organisation. Fill in the request online — it takes about 15 minutes.',
            'points' => rb_rows(['A tailored quote within 3 working days', 'For certification, transfer, extension, reduction or recertification', 'Company details are fetched automatically from the tax ID']),
            'ctaButton' => 'Fill in the request', 'ctaUrl' => rs_url('en', 'request'), 'secondaryText' => 'Verify a certificate', 'secondaryUrl' => rs_url('en', 'verify'),
        ],
    ][$lang];
}

function rs_home_page(string $lang, array $media): string
{
    $t = [
        'ro' => [
            'pill' => 'Organism de certificare a sistemelor de management', 'alt' => 'Certificare ISO pentru industrie: instalație de rafinare', 'title' => 'Certificăm sisteme de management.', 'accent' => 'Construim încredere.',
            'lead' => 'Certificare ISO pentru organizații din toată România: evaluăm sisteme de management după standarde ISO și scheme sectoriale, cu auditori experimentați și decizii imparțiale.',
            'note' => 'sau <a href="' . rs_url('ro', 'request') . '">solicitați o ofertă</a> — răspundem în maximum 3 zile lucrătoare.', 'card_meta' => 'EXEMPLU PRODUCȚIE SRL',
            'statement' => 'De aproape trei decenii, ROCERT oferă certificare ISO organizațiilor din toată România.', 'muted' => 'Independent, riguros și cu un singur scop: un certificat care', 'accent2' => 'înseamnă ceva.',
            'stats' => [['1997', 'anul înființării ROCERT'], ['18', 'standarde și scheme de certificare'], ['[NR]', 'certificate active în registru'], ['3 zile', 'termen maxim pentru ofertă']],
            'cat_eyebrow' => 'Domenii de certificare', 'cat_title' => 'Certificare ISO pentru fiecare domeniu de activitate',
            'why_eyebrow' => 'De ce ROCERT', 'why_title' => 'Rigoare în audit. Claritate în relație.',
            'whys' => [['Imparțialitate garantată', 'Decizia de certificare este luată independent de echipa de audit. Nu oferim consultanță organizațiilor pe care le certificăm.'], ['Auditori cu experiență sectorială', 'Echipa de audit este aleasă după domeniul dumneavoastră de activitate (coduri EA și CAEN).'], ['Audit integrat sau combinat', 'Mai multe standarde, un singur program de audit. Mai puțin timp alocat, aceeași rigoare.'], ['Ofertă în maximum 3 zile', 'Personalizată pentru organizația dumneavoastră, din momentul primirii cererii complete.']],
            'steps_eyebrow' => 'Cum decurge certificarea', 'steps_title' => 'De la cerere la certificat', 'steps_note' => 'Procesul de certificare ISO are aceleași etape generale pentru orice standard. Durata și detaliile diferă — le primiți odată cu oferta.',
            'steps' => [['Cerere și ofertă', 'Completați cererea de certificare; primiți oferta personalizată și contractul.'], ['Audit etapa 1', 'Evaluăm documentația și gradul de pregătire al sistemului de management.'], ['Audit etapa 2', 'Verificăm la fața locului implementarea și eficacitatea sistemului.'], ['Decizia de certificare', 'După închiderea neconformităților, emitem certificatul, valabil 3 ani.'], ['Supraveghere și recertificare', 'Audituri anuale de supraveghere și recertificare la finalul ciclului.']],
            'steps_cta' => 'Începeți cu cererea de certificare', 'steps_cta_p' => 'Ofertă personalizată în maximum 3 zile lucrătoare.', 'steps_cta_btn' => 'Cerere de certificare',
            'vb_title' => 'Verificați un certificat ROCERT', 'vb_lead' => 'Introduceți seria de pe certificat și aflați instant dacă este valid, pentru ce organizație, standard și domeniu.',
            'vb_cards' => [['valid', 'EXEMPLU PRODUCȚIE SRL', 'ISO 14001:2015 · Producția de construcții metalice'], ['invalid', 'EXEMPLU LOGISTIC SA', 'ISO 45001:2023 · Transport rutier de mărfuri']],
            'faq_eyebrow' => 'Întrebări frecvente', 'faq_title' => 'Ce ne întreabă clienții',
            'faqs' => [['Cât durează obținerea certificării?', 'Depinde de mărimea organizației, de numărul de locații și de standard. După acceptarea ofertei, auditurile din etapele 1 și 2 se planifică de comun acord, iar calendarul estimativ vi-l comunicăm odată cu oferta.'], ['Cât timp este valabil un certificat?', 'Certificatul este valabil 3 ani, cu condiția finalizării cu succes a auditurilor anuale de supraveghere. La finalul ciclului urmează auditul de recertificare.'], ['Pot certifica mai multe standarde în același timp?', 'Da. Pentru un sistem de management integrat organizăm un audit integrat sau combinat, care reduce numărul total de zile de audit.'], ['Pot transfera certificatul de la alt organism la ROCERT?', 'Da, prin migrare. Alegeți „Migrare” în cererea de certificare și menționați certificatul actual; nu reluați ciclul de certificare de la zero.'], ['Cum verific dacă un certificat ROCERT este valid?', 'Folosiți pagina Verifică certificat: introduceți seria tipărită pe certificat și aflați dacă este valid, pentru ce organizație, standard și domeniu.'], ['Ce este un organism de certificare acreditat?', 'Este un organism evaluat de organismul național de acreditare — în România, <a href="https://www.renar.ro/" target="_blank" rel="noopener">RENAR</a> — pentru competența și imparțialitatea cu care certifică sisteme de management după standardele <a href="https://www.iso.org/" target="_blank" rel="noopener">ISO</a>.']],
        ],
        'en' => [
            'pill' => 'Management system certification body', 'alt' => 'ISO certification for industry: refinery plant', 'title' => 'We certify management systems.', 'accent' => 'We build trust.',
            'lead' => 'ISO certification for organisations across Romania: we assess management systems against ISO standards and sector schemes, with experienced auditors and impartial decisions.',
            'note' => 'or <a href="' . rs_url('en', 'request') . '">request a quote</a> — we reply within 3 working days.', 'card_meta' => 'EXAMPLE MANUFACTURING SRL',
            'statement' => 'For almost three decades, ROCERT has provided ISO certification to organisations across Romania.', 'muted' => 'Independent, rigorous and with one goal: a certificate that', 'accent2' => 'means something.',
            'stats' => [['1997', 'the year ROCERT was founded'], ['18', 'certification standards and schemes'], ['[NR]', 'active certificates in our register'], ['3 days', 'maximum quote turnaround']],
            'cat_eyebrow' => 'Certification areas', 'cat_title' => 'ISO certification for every field of activity',
            'why_eyebrow' => 'Why ROCERT', 'why_title' => 'Rigorous audits. Clear relationships.',
            'whys' => [['Guaranteed impartiality', 'Certification decisions are taken independently of the audit team. We never provide consultancy to organisations we certify.'], ['Sector-experienced auditors', 'The audit team is chosen for your field of activity (EA and NACE codes).'], ['Integrated or combined audits', 'Several standards, one audit programme. Less time spent, the same rigour.'], ['A quote within 3 days', 'Tailored to your organisation, counted from the moment we receive the complete request.']],
            'steps_eyebrow' => 'How certification works', 'steps_title' => 'From request to certificate', 'steps_note' => 'The ISO certification process follows the same general stages for every standard. Duration and details differ — you receive them with the quote.',
            'steps' => [['Request and quote', 'Fill in the certification request; receive a tailored quote and contract.'], ['Stage 1 audit', 'We review the documentation and how ready the management system is.'], ['Stage 2 audit', 'On site, we verify the system’s implementation and effectiveness.'], ['Certification decision', 'Once nonconformities are closed, we issue the certificate, valid for 3 years.'], ['Surveillance and recertification', 'Yearly surveillance audits and recertification at the end of the cycle.']],
            'steps_cta' => 'Start with the certification request', 'steps_cta_p' => 'A tailored quote within 3 working days.', 'steps_cta_btn' => 'Certification request',
            'vb_title' => 'Verify a ROCERT certificate', 'vb_lead' => 'Enter the serial printed on the certificate to see instantly whether it is valid, and for which organisation, standard and scope.',
            'vb_cards' => [['valid', 'EXAMPLE MANUFACTURING SRL', 'ISO 14001:2015 · Steel structures manufacturing'], ['invalid', 'EXAMPLE LOGISTICS SA', 'ISO 45001:2023 · Road freight transport']],
            'faq_eyebrow' => 'FAQ', 'faq_title' => 'What clients ask us',
            'faqs' => [['How long does certification take?', 'It depends on the size of the organisation, the number of sites and the standard. Once the quote is accepted, stage 1 and stage 2 audits are scheduled together with you; we share an indicative timeline with the quote.'], ['How long is a certificate valid?', 'Three years, provided the yearly surveillance audits are completed successfully. A recertification audit follows at the end of the cycle.'], ['Can I certify several standards at the same time?', 'Yes. For an integrated management system we run an integrated or combined audit, which reduces the total number of audit days.'], ['Can I transfer my certificate from another body to ROCERT?', 'Yes. Choose “Transfer” in the certification request and tell us about your current certificate; the certification cycle does not restart from scratch.'], ['How do I check whether a ROCERT certificate is valid?', 'Use the Verify a certificate page: enter the serial printed on the certificate to see whether it is valid, and for which organisation, standard and scope.'], ['What is an accredited certification body?', 'A body assessed by the national accreditation body — in Romania, <a href="https://www.renar.ro/" target="_blank" rel="noopener">RENAR</a> — for the competence and impartiality with which it certifies management systems against <a href="https://www.iso.org/" target="_blank" rel="noopener">ISO</a> standards.']],
        ],
    ][$lang];

    $out = rb_block('hero', ['alt' => $t['alt'], 'pill' => $t['pill'], 'title' => $t['title'], 'titleAccent' => $t['accent'], 'lead' => $t['lead'], 'note' => $t['note'], 'image' => $media['hero-industry'] ?? 0, 'cardLabel' => $lang === 'ro' ? 'CERTIFICAT' : 'CERTIFICATE', 'cardStatus' => 'Valid', 'cardTitle' => 'ISO 9001:2015', 'cardMeta' => $t['card_meta']]);
    $out .= rb_block('marquee', ['variant' => 'tilt', 'items' => rb_rows(['ISO 9001', 'ISO 14001', 'ISO 45001', 'ISO/IEC 27001', 'ISO 22000', 'ISO 50001', 'ISO 37001', 'ISO 13485', 'ISO/IEC 20000-1', 'ISO 39001', 'ISO 37301', 'ISO 20400', 'ISO 19650', 'GDP'])]);
    $out .= rs_section(rb_block('statement', ['text' => $t['statement'], 'muted' => $t['muted'], 'accent' => $t['accent2'], 'stats' => array_map(static fn ($s) => ['value' => $s[0], 'label' => $s[1]], $t['stats'])]));
    $out .= rb_block('category-index', ['eyebrow' => $t['cat_eyebrow'], 'title' => $t['cat_title']]);
    $out .= rb_block('why', ['eyebrow' => $t['why_eyebrow'], 'title' => $t['why_title'], 'image' => $media['meeting'] ?? 0, 'cards' => array_map(static fn ($w) => ['title' => $w[0], 'text' => $w[1]], $t['whys'])]);

    $tiles = '';
    foreach ($t['steps'] as $i => [$h, $p]) {
        $tiles .= rb_block('tile', ['number' => sprintf('%02d', $i + 1), 'title' => $h, 'text' => $p, 'tone' => $i === 2 ? 'dark' : 'light']);
    }
    $tiles .= rb_block('tile', ['title' => $t['steps_cta'], 'text' => $t['steps_cta_p'], 'tone' => 'blue', 'buttonText' => $t['steps_cta_btn'], 'buttonUrl' => rs_url($lang, 'request')]);
    $out .= rs_section(
        rb_block('section-head', ['eyebrow' => $t['steps_eyebrow'], 'title' => $t['steps_title'], 'text' => $t['steps_note']])
        . rb_block('carousel', ['autoplay' => true, 'interval' => 5, 'bigNumbers' => true], $tiles),
        ['width' => 'full']
    );

    $out .= rb_block('verify-band', ['title' => $t['vb_title'], 'lead' => $t['vb_lead'], 'cards' => array_map(static fn ($c) => ['status' => $c[0], 'title' => $c[1], 'meta' => $c[2]], $t['vb_cards'])]);
    $out .= rs_section(rs_faq_block(array_map(static fn ($f) => ['q' => $f[0], 'a' => $f[1]], $t['faqs']), $t['faq_eyebrow'], $t['faq_title']));
    $out .= rb_block('contact-panel', rs_contact_panel_attrs($lang));
    return $out;
}

function rs_verify_page(string $lang, array $media): string
{
    $t = [
        'ro' => ['title' => 'Verifică un certificat', 'lead' => 'Verificare certificat ROCERT: introduceți seria tipărită pe certificat și aflați în câteva secunde dacă este valid, pentru ce organizație, standard și domeniu de activitate.', 'eyebrow' => 'Ce înseamnă statusul', 'valid' => 'Valid', 'valid_d' => 'Certificatul este în vigoare, iar organizația își menține certificarea prin audituri de supraveghere.', 'invalid' => 'Invalid', 'invalid_d' => 'Certificatul nu mai este în vigoare — a fost suspendat, retras sau a expirat. Pentru detalii, contactați ROCERT.', 'where' => 'Verificare certificat: unde găsesc seria?', 'where_p' => 'Seria este codul unic tipărit pe certificat (format: 8-4-4-4-12 caractere, de ex. 3F2A9C1E-4B7D-4E2A-9C51-7A1D2E3F4B5C). Copiați-o exact așa cum apare.', 'own' => 'Doriți propriul certificat?', 'own_p' => 'Ofertă personalizată în maximum 3 zile lucrătoare.', 'own_btn' => 'Cerere de certificare'],
        'en' => ['title' => 'Verify a certificate', 'lead' => 'Certificate verification for ROCERT certificates: enter the serial printed on the certificate to see in seconds whether it is valid, and for which organisation, standard and scope.', 'eyebrow' => 'What the status means', 'valid' => 'Valid', 'valid_d' => 'The certificate is in force and the organisation maintains its certification through surveillance audits.', 'invalid' => 'Invalid', 'invalid_d' => 'The certificate is no longer in force — it was suspended, withdrawn or has expired. Contact ROCERT for details.', 'where' => 'Certificate verification: where is the serial?', 'where_p' => 'The serial is the unique code printed on the certificate (format 8-4-4-4-12 characters, e.g. 3F2A9C1E-4B7D-4E2A-9C51-7A1D2E3F4B5C). Copy it exactly as shown.', 'own' => 'Want your own certificate?', 'own_p' => 'A tailored quote within 3 working days.', 'own_btn' => 'Certification request'],
    ][$lang];
    $out = rb_block('page-hero', ['title' => $t['title'], 'lead' => $t['lead'], 'deco' => 'shield', 'overlap' => true]);
    $out .= rb_block('verify');
    $out .= rs_section(
        rb_block('section-head', ['eyebrow' => $t['eyebrow']])
        . rb_block('legend', ['items' => [['title' => $t['valid'], 'text' => $t['valid_d'], 'tone' => 'green'], ['title' => $t['invalid'], 'text' => $t['invalid_d'], 'tone' => 'red']]]),
        ['spacing' => 'bottom0', 'width' => 'full']
    );
    $content = rb_heading(esc_html($t['where']), 2) . rb_p(esc_html($t['where_p']), 'is-style-lead')
        . rb_block('card', ['title' => $t['own'], 'text' => $t['own_p'], 'buttonText' => $t['own_btn'], 'buttonUrl' => rs_url($lang, 'request'), 'tone' => 'dark']);
    $out .= rb_block('split', ['side' => 'left', 'image' => $media['certificate'] ?? 0, 'alt' => $lang === 'ro' ? 'Verificare certificat ROCERT: seria tipărită pe certificat' : 'Certificate verification: the serial printed on a ROCERT certificate'], $content);
    return $out;
}

function rs_request_page(string $lang, array $forms): string
{
    $t = [
        'ro' => ['title' => "Cerere de\ncertificare", 'lead' => 'Cerere de certificare online: completați datele pentru o ofertă personalizată. Informațiile sunt tratate confidențial și ne ajută să stabilim durata auditului.', 'stats' => [['3 zile', 'termen maxim pentru ofertă'], ['~15 min', 'timp de completare']], 'form_title' => 'Formular: cerere de certificare', 'help' => 'Aveți întrebări despre formular? Sunați-ne la <a href="tel:+40212242639">+40 21 224 26 39</a> sau scrieți la <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
        'en' => ['title' => "Certification\nrequest", 'lead' => 'Certification request online: fill in the details for a tailored quote. Information is treated confidentially and helps us determine the audit duration.', 'stats' => [['3 days', 'maximum quote turnaround'], ['~15 min', 'to complete']], 'form_title' => 'Form: certification request', 'help' => 'Questions about the form? Call us on <a href="tel:+40212242639">+40 21 224 26 39</a> or e-mail <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
    ][$lang];
    $out = rb_block('page-hero', ['title' => $t['title'], 'lead' => $t['lead'], 'outline' => 'C02', 'overlap' => true, 'stats' => array_map(static fn ($s) => ['value' => $s[0], 'label' => $s[1]], $t['stats'])]);
    $out .= rs_section(rb_block('form', ['form' => $forms['request_' . $lang] ?? 0, 'title' => $t['form_title'], 'help' => $t['help']]), ['spacing' => 'top0']);
    return $out;
}

function rs_contact_page(string $lang): string
{
    $t = [
        'ro' => ['title' => 'Contact', 'lead' => 'Contact ROCERT: pentru oferte, informații despre certificare sau confirmarea unui certificat, suntem la un telefon sau un e-mail distanță.', 'company_p' => "ROCERT SRL · CUI RO9754830 · J1997006680404\nIBAN RO30BTRLRONCRT0CU8405501 · Banca Transilvania"],
        'en' => ['title' => 'Contact', 'lead' => 'ROCERT contact details: for quotes, certification questions or confirming a certificate, we are one call or e-mail away.', 'company_p' => "ROCERT SRL · VAT RO9754830 · J1997006680404\nIBAN RO30BTRLRONCRT0CU8405501 · Banca Transilvania"],
    ][$lang];
    $panel = rs_contact_panel_attrs($lang);
    $panel['title'] = $lang === 'ro' ? 'Suntem în București, Sector 1' : 'We are in Bucharest, Sector 1';
    $panel['address'] .= "\n\n" . $t['company_p'];
    return rb_block('page-hero', ['title' => $t['title'], 'lead' => $t['lead']]) . rb_block('contact-panel', $panel);
}

function rs_about_page(string $lang, array $media): string
{
    $t = [
        'ro' => [
            'title' => 'Despre ROCERT', 'lead' => 'Societatea Română pentru Certificare — organism independent de certificare a sistemelor de management, activ din 1997.',
            'statement' => 'Credem că un certificat are valoare doar dacă auditul din spatele lui este', 'accent' => 'riguros, imparțial și făcut de oameni care înțeleg domeniul clientului.',
            'story_h' => 'Aproape trei decenii de certificare', 'story' => ['ROCERT a fost înființată în 1997, în primii ani în care standardele ISO pentru sisteme de management au început să fie adoptate pe scară largă în România. De atunci, am auditat și certificat organizații din producție, construcții, servicii, sănătate, IT, transport și administrație publică.', 'Activitatea noastră urmează cerințele aplicabile organismelor de certificare a sistemelor de management. Deciziile de certificare sunt luate independent de echipa de audit, iar ROCERT nu oferă servicii de consultanță organizațiilor pe care le certifică.', '[DE COMPLETAT de ROCERT: repere din istoric, acreditări curente, echipă.]'],
            'values_h' => 'Principiile după care lucrăm',
            'values' => [['Imparțialitate', 'Gestionăm activ orice potențial conflict de interese. Certificăm, nu consultăm.', 'blue'], ['Competență', 'Auditori calificați, aleși în funcție de domeniul tehnic al fiecărui client.', 'light'], ['Confidențialitate', 'Informațiile obținute în audit sunt protejate și folosite doar în scopul certificării.', 'light'], ['Transparență', 'Proces, reguli și decizii comunicate clar — de la ofertă la recertificare.', 'light'], ['Responsabilitate', 'Ne asumăm deciziile de certificare și le susținem cu dovezi obiective.', 'dark'], ['Receptivitate', 'Răspundem rapid la cereri, reclamații și apeluri, într-un proces documentat.', 'light']],
            'links' => [['Acreditări și recunoașteri', rs_url('ro', 'accreditations')], ['Informații publice', rs_url('ro', 'public')]],
        ],
        'en' => [
            'title' => 'About ROCERT', 'lead' => 'The Romanian Society for Certification — an independent management system certification body, active since 1997.',
            'statement' => 'We believe a certificate only has value if the audit behind it is', 'accent' => 'rigorous, impartial and carried out by people who understand the client’s field.',
            'story_h' => 'Almost three decades of certification', 'story' => ['ROCERT was founded in 1997, in the early years when ISO management system standards began to be widely adopted in Romania. Since then we have audited and certified organisations in manufacturing, construction, services, healthcare, IT, transport and public administration.', 'Our work follows the requirements that apply to management system certification bodies. Certification decisions are taken independently of the audit team, and ROCERT does not provide consultancy to the organisations it certifies.', '[TO BE COMPLETED by ROCERT: history milestones, current accreditations, team.]'],
            'values_h' => 'The principles we work by',
            'values' => [['Impartiality', 'We actively manage any potential conflict of interest. We certify; we do not consult.', 'blue'], ['Competence', 'Qualified auditors, chosen for each client’s technical field.', 'light'], ['Confidentiality', 'Information obtained during audits is protected and used for certification only.', 'light'], ['Transparency', 'Process, rules and decisions communicated clearly — from quote to recertification.', 'light'], ['Responsibility', 'We stand behind our certification decisions with objective evidence.', 'dark'], ['Responsiveness', 'We handle requests, complaints and appeals quickly, through a documented process.', 'light']],
            'links' => [['Accreditations and recognitions', rs_url('en', 'accreditations')], ['Public information', rs_url('en', 'public')]],
        ],
    ][$lang];
    $out = rb_block('page-hero', ['title' => $t['title'], 'lead' => $t['lead'], 'outline' => '1997']);
    $out .= rs_section(rb_block('statement', ['text' => $t['statement'], 'accent' => $t['accent']]));
    $story = rb_heading(esc_html($t['story_h']), 2);
    foreach ($t['story'] as $p) {
        $story .= rb_p(esc_html($p));
    }
    $story .= rb_buttons(array_map(static fn ($l) => [$l[0], $l[1], 'is-style-outline'], $t['links']));
    $out .= rb_block('split', ['side' => 'left', 'image' => $media['meeting'] ?? 0], $story);
    $out .= rs_section(rb_block('section-head', ['title' => $t['values_h']]) . rb_block('cards', ['items' => array_map(static fn ($v) => ['title' => $v[0], 'text' => $v[1], 'tone' => $v[2]], $t['values'])]), ['spacing' => 'top0']);
    return $out;
}

function rs_accreditations_page(string $lang): string
{
    $t = [
        'ro' => ['title' => 'Acreditări și recunoașteri', 'lead' => 'Acreditări ROCERT și recunoașteri care confirmă competența și imparțialitatea noastră ca organism de certificare.', 'note' => '[DE ACTUALIZAT de ROCERT: lista de mai jos provine de pe vechiul site și trebuie confirmată — număr și valabilitate acreditare, domeniu acreditat, anexe.]',
            'items' => [['RENAR', 'Acreditare ca organism de certificare a sistemelor de management — certificat nr. [DE CONFIRMAT], domeniu și valabilitate [DE CONFIRMAT].'], ['IQNet', 'Posibilitatea emiterii certificatelor IQNet, la cerere [DE CONFIRMAT].'], ['MApN – OMCAS', 'Abilitare pentru certificarea sistemelor de management ale furnizorilor Ministerului Apărării Naționale (certificat nr. A3/434/05.02.2004) [DE CONFIRMAT dacă este în vigoare].'], ['Ministerul Transporturilor', 'Agrement pentru certificarea sistemelor de management al calității în construcții (certificat nr. 10/28.04.2004) [DE CONFIRMAT dacă este în vigoare].'], ['Electrica SA', 'Abilitare pentru certificarea furnizorilor (certificat nr. 428/14.02.2001) [DE CONFIRMAT dacă este în vigoare].']]],
        'en' => ['title' => 'Accreditations and recognitions', 'lead' => 'ROCERT accreditations and recognitions that confirm our competence and impartiality as a certification body.', 'note' => '[TO BE UPDATED by ROCERT: the list below comes from the old website and must be confirmed — accreditation number and validity, accredited scope, annexes.]',
            'items' => [['RENAR', 'Accreditation as a management system certification body — certificate no. [TO BE CONFIRMED], scope and validity [TO BE CONFIRMED].'], ['IQNet', 'IQNet certificates can be issued on request [TO BE CONFIRMED].'], ['Ministry of National Defence – OMCAS', 'Approval to certify the management systems of Ministry of National Defence suppliers (certificate no. A3/434/05.02.2004) [TO BE CONFIRMED whether still in force].'], ['Ministry of Transport', 'Approval to certify quality management systems in construction (certificate no. 10/28.04.2004) [TO BE CONFIRMED whether still in force].'], ['Electrica SA', 'Approval to certify suppliers (certificate no. 428/14.02.2001) [TO BE CONFIRMED whether still in force].']]],
    ][$lang];
    return rb_block('page-hero', ['title' => $t['title'], 'lead' => $t['lead']])
        . rs_section(rb_block('recognitions', ['note' => $t['note'], 'items' => array_map(static fn ($i) => ['title' => $i[0], 'text' => $i[1]], $t['items'])]));
}

function rs_legal_page(array $page, string $lang): string
{
    $d = $page[$lang];
    $out = '';
    if (!empty($d['updated'])) {
        $out .= rb_p(esc_html(($lang === 'ro' ? 'Ultima actualizare: ' : 'Last updated: ') . $d['updated']), 'rc-updated');
    }
    if (!empty($d['intro'])) {
        $out .= rb_p($d['intro'], 'is-style-lead');
    }
    foreach ($d['sections'] as $section) {
        $out .= rb_heading(esc_html($section['heading']), 2);
        foreach ($section['blocks'] as $b) {
            $out .= match ($b['type']) {
                'p' => rb_p($b['text']),
                'list' => rb_list($b['items']),
                'table' => rb_table($b['head'], $b['rows']),
                default => '',
            };
        }
    }
    return $out;
}
