<?php
return [
    'key' => 'public-info',
    'ro' => [
        'slug' => 'informatii-publice',
        'title' => 'Informații publice',
        'seo_title' => 'Informații publice despre procesul de certificare',
        'meta' => 'Procesul de certificare, condițiile de menținere, suspendare și retragere, regulile de utilizare a mărcii, reclamații, contestații și imparțialitate.',
        'updated' => '05.10.2026',
        'intro' => '<strong>[DE VALIDAT DE ROCERT — text propus, trebuie aliniat cu procedurile interne]</strong><br>În calitate de organism de certificare a sistemelor de management, ROCERT SRL pune la dispoziția publicului informațiile cerute de standardul SR EN ISO/IEC 17021-1: descrierea procesului de certificare, condițiile de acordare, menținere, extindere, restrângere, suspendare și retragere a certificării, drepturile și obligațiile clienților certificați, regulile de utilizare a certificatelor și a mărcii de certificare, modul de tratare a reclamațiilor și contestațiilor, precum și angajamentul nostru privind imparțialitatea. Detaliile complete sunt stabilite prin contractul de certificare și prin regulamentele ROCERT, disponibile la cerere.',
        'sections' => [
            ['heading' => '1. Procesul de certificare', 'blocks' => [
                ['type' => 'p', 'text' => 'Certificarea se desfășoară în etapele de mai jos. Durata auditurilor se stabilește în funcție de standardul de referință, numărul de personal, numărul de locații, complexitatea activităților și riscurile asociate, conform regulilor de acreditare aplicabile.'],
                ['type' => 'list', 'items' => [
                    '<strong>Cererea de certificare</strong> — organizația transmite formularul de cerere, cu informații despre activitate, domeniul de certificare dorit, locații, personal și eventualele procese externalizate.',
                    '<strong>Analiza cererii și oferta</strong> — ROCERT verifică dacă are competența și capacitatea de a efectua certificarea, stabilește durata auditului și transmite oferta. După acceptare, se încheie contractul de certificare.',
                    '<strong>Auditul de etapa 1</strong> — evaluează documentația sistemului de management, condițiile specifice amplasamentului, stadiul de implementare și pregătirea organizației pentru etapa 2, inclusiv auditul intern și analiza efectuată de management.',
                    '<strong>Auditul de etapa 2</strong> — evaluează la fața locului implementarea și eficacitatea sistemului de management. Eventualele neconformități sunt comunicate în scris, iar organizația propune și implementează corecții și acțiuni corective în termenele stabilite [DE CONFIRMAT: termenele interne pentru tratarea neconformităților majore și minore].',
                    '<strong>Decizia de certificare</strong> — este luată de persoane competente din cadrul ROCERT care <strong>nu au participat la audit</strong>, pe baza analizei raportului de audit și a acțiunilor corective. Certificatul are o valabilitate de 3 ani.',
                    '<strong>Auditurile de supraveghere</strong> — se efectuează cel puțin o dată pe an calendaristic (cu excepția anilor de recertificare); primul audit de supraveghere are loc în cel mult 12 luni de la data deciziei de certificare.',
                    '<strong>Recertificarea</strong> — se realizează printr-un audit planificat și efectuat înainte de expirarea certificatului, pentru a confirma continuitatea conformității și eficacității sistemului de management.',
                    '<strong>Audituri speciale</strong> — pot fi efectuate pentru extinderea domeniului, ca urmare a unor reclamații sau modificări importante la client, ori pentru clienți suspendați; acestea pot fi și audituri anunțate cu preaviz scurt.',
                ]],
            ]],
            ['heading' => '2. Acordarea, menținerea, extinderea, restrângerea, suspendarea și retragerea certificării', 'blocks' => [
                ['type' => 'list', 'items' => [
                    '<strong>Acordarea</strong> — certificarea este acordată atunci când auditurile de etapa 1 și 2 au demonstrat conformitatea cu cerințele standardului și toate neconformitățile majore au fost tratate și verificate.',
                    '<strong>Menținerea</strong> — certificarea se menține dacă organizația continuă să îndeplinească cerințele standardului, acceptă și trece auditurile de supraveghere la termen, respectă obligațiile contractuale și regulile de utilizare a certificatului și a mărcii.',
                    '<strong>Extinderea</strong> — domeniul certificării poate fi extins (activități, locații, standarde) la cererea clientului, în urma analizei cererii și, după caz, a unui audit suplimentar.',
                    '<strong>Restrângerea</strong> — domeniul este restrâns atunci când clientul nu mai îndeplinește cerințele pentru anumite părți ale acestuia, fie la cererea clientului.',
                    '<strong>Suspendarea</strong> — poate interveni, de exemplu, când sistemul de management nu mai îndeplinește în mod persistent sau grav cerințele, când clientul nu permite efectuarea auditurilor la termen, la cererea voluntară a clientului sau în cazul nerespectării obligațiilor contractuale. Pe durata suspendării certificarea este temporar nevalabilă. Suspendarea nu poate depăși 6 luni [DE CONFIRMAT: durata maximă conform procedurii ROCERT].',
                    '<strong>Retragerea</strong> — certificarea este retrasă dacă motivele suspendării nu sunt rezolvate în termenul stabilit, în cazul utilizării înșelătoare a certificatului sau a mărcii, la cererea clientului sau la încetarea activității certificate.',
                ]],
                ['type' => 'p', 'text' => 'Deciziile de suspendare, restrângere sau retragere sunt comunicate clientului în scris, iar statutul certificării este actualizat în registrul ROCERT al organizațiilor certificate.'],
            ]],
            ['heading' => '3. Drepturile și obligațiile clienților certificați', 'blocks' => [
                ['type' => 'p', 'text' => '<strong>Clienții certificați au dreptul:</strong>'],
                ['type' => 'list', 'items' => [
                    'să facă referire la certificare și să folosească certificatul și marca de certificare ROCERT, în conformitate cu regulile de mai jos;',
                    'să primească informații privind procesul de certificare, planurile de audit și componența echipei de audit și să poată obiecta, motivat, la numirea unui anumit auditor;',
                    'să depună reclamații și contestații;',
                    'să solicite extinderea, restrângerea, suspendarea voluntară sau încetarea certificării;',
                    'la confidențialitatea informațiilor obținute în cursul activităților de certificare.',
                ]],
                ['type' => 'p', 'text' => '<strong>Clienții certificați au obligația:</strong>'],
                ['type' => 'list', 'items' => [
                    'să mențină sistemul de management conform cerințelor standardului și ale schemei de certificare;',
                    'să asigure accesul echipei de audit la documente, înregistrări, locații, personal și, după caz, subcontractanți, inclusiv pentru observatorii organismului de acreditare;',
                    'să permită efectuarea auditurilor la termenele stabilite;',
                    'să țină evidența reclamațiilor primite cu privire la conformitatea cu cerințele certificării și a acțiunilor întreprinse și să le pună la dispoziția ROCERT;',
                    'să informeze fără întârziere ROCERT despre modificări care pot afecta certificarea (statut juridic, proprietate, conducere, adresă, locații, domeniul activității, modificări importante ale sistemului de management sau ale proceselor);',
                    'să achite tarifele prevăzute în contract;',
                    'să respecte regulile de utilizare a certificatului și a mărcii de certificare.',
                ]],
            ]],
            ['heading' => '4. Utilizarea certificatului și a mărcii de certificare ROCERT', 'blocks' => [
                ['type' => 'list', 'items' => [
                    'Certificatul și marca pot fi folosite numai pentru domeniul, locațiile și standardul pentru care a fost acordată certificarea și numai pe durata valabilității acesteia.',
                    'Marca de certificare a sistemului de management <strong>nu poate fi aplicată pe produse sau pe ambalajul produselor</strong> și nici pe rapoarte de încercare, buletine de analiză, certificate de calibrare sau de inspecție, deoarece acestea ar sugera certificarea produsului.',
                    'Orice declarație pe ambalaj sau în documentele însoțitoare trebuie să precizeze clar că organizația are sistemul de management certificat și nu sugereze că produsul, procesul sau serviciul sunt certificate.',
                    'Certificarea nu poate fi folosită într-un mod care ar putea discredita ROCERT sau sistemul de certificare ori care ar putea induce în eroare publicul, de exemplu prin extinderea la activități sau locații necertificate.',
                    'Marca trebuie reprodusă fără modificări, conform modelului și regulilor grafice furnizate de ROCERT. Marca organismului de acreditare poate fi folosită numai în condițiile stabilite de acesta [DE CONFIRMAT: regulile ROCERT privind utilizarea mărcii de acreditare].',
                    'La <strong>suspendarea sau retragerea</strong> certificării, clientul trebuie să înceteze imediat utilizarea certificatului și a mărcii în orice material publicitar sau documente care fac referire la certificare; la restrângerea domeniului, materialele trebuie modificate corespunzător.',
                ]],
                ['type' => 'p', 'text' => 'Utilizarea incorectă a certificatului sau a mărcii poate conduce la acțiuni corective, suspendare, retragerea certificării, publicarea abaterii sau alte măsuri legale.'],
            ]],
            ['heading' => '5. Reclamații și contestații', 'blocks' => [
                ['type' => 'p', 'text' => '<strong>Reclamațiile</strong> pot fi formulate de orice persoană sau organizație cu privire la activitatea ROCERT sau la un client certificat de ROCERT. <strong>Contestațiile</strong> pot fi formulate de clienți sau solicitanți împotriva unei decizii privind certificarea (de exemplu refuzul, suspendarea, restrângerea sau retragerea certificării).'],
                ['type' => 'list', 'items' => [
                    '<strong>Transmitere</strong> — în scris, prin e-mail la <a href="mailto:office@rocert.ro">office@rocert.ro</a> sau prin poștă la adresa ROCERT SRL, Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43, Sector 1, București 011571. Vă rugăm să includeți datele de contact, descrierea situației și documentele relevante.',
                    '<strong>Confirmarea primirii</strong> — ROCERT confirmă în scris primirea și verifică dacă reclamația se referă la activitățile de certificare de care este responsabil.',
                    '<strong>Investigarea</strong> — reclamația sau contestația este analizată de persoane care <strong>nu au fost implicate</strong> în activitățile de audit și certificare care fac obiectul acesteia. Tratarea nu dă naștere la acțiuni discriminatorii împotriva celui care a formulat-o.',
                    '<strong>Decizia și răspunsul</strong> — decizia este luată, sau analizată și aprobată, de persoane neimplicate anterior; reclamantul primește un răspuns scris privind rezultatul și încheierea procesului [DE CONFIRMAT: termenele de răspuns stabilite prin procedura ROCERT].',
                    '<strong>Reclamațiile privind clienții certificați</strong> sunt analizate și sub aspectul eficacității sistemului de management certificat și pot fi transmise clientului în cauză, în condițiile păstrării confidențialității.',
                ]],
                ['type' => 'p', 'text' => 'Procedura completă de tratare a reclamațiilor și contestațiilor este disponibilă la cerere.'],
            ]],
            ['heading' => '6. Angajamentul privind imparțialitatea', 'blocks' => [
                ['type' => 'p', 'text' => 'Conducerea ROCERT se angajează să desfășoare activitățile de certificare în mod imparțial, obiectiv și independent, fără a permite presiunilor comerciale, financiare sau de altă natură să compromită imparțialitatea.'],
                ['type' => 'list', 'items' => [
                    'ROCERT <strong>nu oferă consultanță</strong> pentru sisteme de management și nu efectuează audituri interne pentru clienții săi certificați.',
                    'ROCERT nu recomandă și nu promovează firme de consultanță și nu sugerează că certificarea ar fi mai simplă sau mai ieftină dacă se apelează la un anumit consultant.',
                    'Riscurile privind imparțialitatea sunt identificate, analizate și tratate în mod continuu; personalul și colaboratorii declară orice relație care ar putea genera un conflict de interese.',
                    'Auditorii nu participă la auditarea organizațiilor cărora le-au oferit consultanță sau cu care au avut relații de natură să afecteze imparțialitatea, pe perioada stabilită de regulile aplicabile (cel puțin 2 ani).',
                    'Imparțialitatea este monitorizată de un comitet pentru protejarea imparțialității, în care sunt reprezentate părțile interesate [DE CONFIRMAT: existența, denumirea și componența comitetului].',
                ]],
            ]],
            ['heading' => '7. Informații despre organizațiile certificate', 'blocks' => [
                ['type' => 'p', 'text' => 'Statutul certificării unei organizații (valabil, suspendat, retras), standardul și domeniul certificat pot fi verificate cu ajutorul instrumentului <a href="/verifica-certificat">„Verifică certificat”</a> [DE CONFIRMAT: URL-ul paginii]. La cerere, ROCERT confirmă în scris validitatea unui certificat, numele organizației, standardul de referință, domeniul de aplicare și locațiile certificate. Cererile se transmit la <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
            ]],
            ['heading' => '8. Modificări ale cerințelor de certificare', 'blocks' => [
                ['type' => 'p', 'text' => 'ROCERT informează clienții certificați, în timp util, despre orice modificare a cerințelor de certificare — de exemplu publicarea unei noi versiuni a unui standard, schimbări ale regulilor de acreditare sau ale propriilor reguli de certificare — și despre perioadele de tranziție aplicabile. ROCERT verifică apoi, de regulă în cadrul auditurilor de supraveghere sau recertificare, dacă fiecare client s-a conformat noilor cerințe.'],
            ]],
            ['heading' => '9. Acreditare și contact', 'blocks' => [
                ['type' => 'p', 'text' => 'Statutul de acreditare al ROCERT, organismul de acreditare și standardele acoperite: [DE CONFIRMAT: de exemplu acreditare RENAR, numărul certificatului de acreditare și domeniul acreditat].'],
                ['type' => 'p', 'text' => 'Pentru orice informații suplimentare: ROCERT SRL, Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43, Sector 1, București 011571 · Tel./fax <a href="tel:+40212242639">+40 21 224 26 39</a> · E-mail <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
            ]],
        ],
    ],
    'en' => [
        'slug' => 'public-information',
        'title' => 'Public information',
        'seo_title' => 'Public Information on the Certification Process',
        'meta' => 'The certification process, conditions for maintaining, suspending and withdrawing certification, mark use rules, complaints, appeals and impartiality.',
        'updated' => '05.10.2026',
        'intro' => '<strong>[TO BE VALIDATED BY ROCERT — proposed text, must be aligned with internal procedures]</strong><br>As a management system certification body, ROCERT SRL makes publicly available the information required by ISO/IEC 17021-1: a description of the certification process, the conditions for granting, maintaining, extending, reducing, suspending and withdrawing certification, the rights and obligations of certified clients, the rules for using certificates and the certification mark, how complaints and appeals are handled, and our commitment to impartiality. Full details are set out in the certification contract and in ROCERT\'s rules, available on request.',
        'sections' => [
            ['heading' => '1. The certification process', 'blocks' => [
                ['type' => 'p', 'text' => 'Certification follows the stages below. Audit duration is determined by the reference standard, number of staff, number of sites, complexity of activities and associated risks, in line with the applicable accreditation rules.'],
                ['type' => 'list', 'items' => [
                    '<strong>Application</strong> — the organisation submits the application form with information about its activities, the requested certification scope, sites, staff and any outsourced processes.',
                    '<strong>Application review and offer</strong> — ROCERT checks that it has the competence and capacity to perform the certification, determines the audit time and sends an offer. Once accepted, the certification contract is signed.',
                    '<strong>Stage 1 audit</strong> — assesses the management system documentation, site-specific conditions, the state of implementation and the organisation\'s readiness for stage 2, including internal audit and management review.',
                    '<strong>Stage 2 audit</strong> — assesses on site the implementation and effectiveness of the management system. Any nonconformities are reported in writing, and the organisation proposes and implements corrections and corrective actions within the set deadlines [TO BE CONFIRMED: internal deadlines for major and minor nonconformities].',
                    '<strong>Certification decision</strong> — taken by competent ROCERT personnel who <strong>did not take part in the audit</strong>, based on a review of the audit report and corrective actions. The certificate is valid for 3 years.',
                    '<strong>Surveillance audits</strong> — carried out at least once each calendar year (except in recertification years); the first surveillance audit takes place no later than 12 months from the date of the certification decision.',
                    '<strong>Recertification</strong> — carried out through an audit planned and performed before the certificate expires, to confirm the continued conformity and effectiveness of the management system.',
                    '<strong>Special audits</strong> — may be carried out for scope extensions, following complaints or significant changes at the client, or for suspended clients; these may include short-notice audits.',
                ]],
            ]],
            ['heading' => '2. Granting, maintaining, extending, reducing, suspending and withdrawing certification', 'blocks' => [
                ['type' => 'list', 'items' => [
                    '<strong>Granting</strong> — certification is granted when the stage 1 and stage 2 audits have demonstrated conformity with the standard and all major nonconformities have been addressed and verified.',
                    '<strong>Maintaining</strong> — certification is maintained as long as the organisation continues to meet the requirements of the standard, accepts and passes surveillance audits on time, and complies with its contractual obligations and the rules for using the certificate and mark.',
                    '<strong>Extending</strong> — the scope of certification may be extended (activities, sites, standards) at the client\'s request, following review of the request and, where needed, an additional audit.',
                    '<strong>Reducing</strong> — the scope is reduced when the client no longer meets the requirements for certain parts of it, or at the client\'s request.',
                    '<strong>Suspension</strong> — may occur, for example, when the management system persistently or seriously fails to meet requirements, when the client does not allow audits to be carried out on time, at the client\'s voluntary request, or when contractual obligations are not met. During suspension the certification is temporarily invalid. Suspension may not exceed 6 months [TO BE CONFIRMED: maximum duration under ROCERT\'s procedure].',
                    '<strong>Withdrawal</strong> — certification is withdrawn if the reasons for suspension are not resolved within the set period, in case of misleading use of the certificate or mark, at the client\'s request, or when the certified activity ceases.',
                ]],
                ['type' => 'p', 'text' => 'Decisions to suspend, reduce or withdraw certification are communicated to the client in writing, and the certification status is updated in ROCERT\'s register of certified organisations.'],
            ]],
            ['heading' => '3. Rights and obligations of certified clients', 'blocks' => [
                ['type' => 'p', 'text' => '<strong>Certified clients have the right to:</strong>'],
                ['type' => 'list', 'items' => [
                    'refer to their certification and use the certificate and the ROCERT certification mark in accordance with the rules below;',
                    'receive information about the certification process, audit plans and audit team composition, and object, with reasons, to the appointment of a particular auditor;',
                    'submit complaints and appeals;',
                    'request extension, reduction, voluntary suspension or termination of certification;',
                    'confidentiality of information obtained during certification activities.',
                ]],
                ['type' => 'p', 'text' => '<strong>Certified clients must:</strong>'],
                ['type' => 'list', 'items' => [
                    'maintain their management system in line with the requirements of the standard and the certification scheme;',
                    'give the audit team access to documents, records, sites, personnel and, where applicable, subcontractors, including for accreditation body observers;',
                    'allow audits to be carried out at the scheduled times;',
                    'keep records of complaints received regarding conformity with certification requirements and of the actions taken, and make them available to ROCERT;',
                    'inform ROCERT without delay of changes that may affect certification (legal status, ownership, management, address, sites, scope of activities, significant changes to the management system or processes);',
                    'pay the fees set out in the contract;',
                    'comply with the rules for using the certificate and certification mark.',
                ]],
            ]],
            ['heading' => '4. Use of the certificate and the ROCERT certification mark', 'blocks' => [
                ['type' => 'list', 'items' => [
                    'The certificate and mark may be used only for the scope, sites and standard for which certification has been granted, and only while it is valid.',
                    'The management system certification mark <strong>must not be applied to products or product packaging</strong>, nor to test reports, laboratory analysis reports, calibration or inspection certificates, as this would imply product certification.',
                    'Any statement on packaging or accompanying documents must clearly state that the organisation\'s management system is certified and must not imply that the product, process or service is certified.',
                    'Certification must not be used in a way that may bring ROCERT or the certification system into disrepute or mislead the public, for example by extending it to non-certified activities or sites.',
                    'The mark must be reproduced without alteration, according to the template and graphic rules provided by ROCERT. The accreditation body\'s mark may only be used under the conditions it sets [TO BE CONFIRMED: ROCERT\'s rules on use of the accreditation mark].',
                    'Upon <strong>suspension or withdrawal</strong> of certification, the client must immediately stop using the certificate and mark in any advertising material or documents referring to certification; upon reduction of scope, materials must be amended accordingly.',
                ]],
                ['type' => 'p', 'text' => 'Incorrect use of the certificate or mark may lead to corrective action, suspension, withdrawal of certification, publication of the transgression or other legal action.'],
            ]],
            ['heading' => '5. Complaints and appeals', 'blocks' => [
                ['type' => 'p', 'text' => '<strong>Complaints</strong> may be made by any person or organisation regarding ROCERT\'s activities or a client certified by ROCERT. <strong>Appeals</strong> may be lodged by clients or applicants against a certification decision (for example refusal, suspension, reduction or withdrawal of certification).'],
                ['type' => 'list', 'items' => [
                    '<strong>Submission</strong> — in writing, by e-mail to <a href="mailto:office@rocert.ro">office@rocert.ro</a> or by post to ROCERT SRL, Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43, Sector 1, Bucharest 011571, Romania. Please include your contact details, a description of the situation and any relevant documents.',
                    '<strong>Acknowledgement</strong> — ROCERT acknowledges receipt in writing and checks whether the complaint relates to certification activities for which it is responsible.',
                    '<strong>Investigation</strong> — the complaint or appeal is investigated by persons who were <strong>not involved</strong> in the audit and certification activities concerned. Handling a complaint or appeal does not result in any discriminatory action against the person who submitted it.',
                    '<strong>Decision and response</strong> — the decision is made, or reviewed and approved, by persons not previously involved; the complainant receives a written response on the outcome and the end of the process [TO BE CONFIRMED: response times set by ROCERT\'s procedure].',
                    '<strong>Complaints about certified clients</strong> are also examined in terms of the effectiveness of the certified management system and may be forwarded to the client concerned, subject to confidentiality.',
                ]],
                ['type' => 'p', 'text' => 'The full complaints and appeals procedure is available on request.'],
            ]],
            ['heading' => '6. Commitment to impartiality', 'blocks' => [
                ['type' => 'p', 'text' => 'ROCERT\'s management is committed to carrying out certification activities impartially, objectively and independently, and does not allow commercial, financial or other pressures to compromise impartiality.'],
                ['type' => 'list', 'items' => [
                    'ROCERT <strong>does not provide consultancy</strong> on management systems and does not carry out internal audits for its certified clients.',
                    'ROCERT does not recommend or promote consultancy firms and does not suggest that certification would be simpler or cheaper if a particular consultant were used.',
                    'Risks to impartiality are continuously identified, analysed and addressed; staff and external auditors declare any relationship that could create a conflict of interest.',
                    'Auditors do not audit organisations to which they have provided consultancy, or with which they have had relationships that could affect impartiality, for the period set by the applicable rules (at least 2 years).',
                    'Impartiality is monitored by a committee for safeguarding impartiality in which interested parties are represented [TO BE CONFIRMED: existence, name and composition of the committee].',
                ]],
            ]],
            ['heading' => '7. Information on certified organisations', 'blocks' => [
                ['type' => 'p', 'text' => 'The certification status of an organisation (valid, suspended, withdrawn), the standard and the certified scope can be checked using the <a href="/en/verify-certificate">"Verify certificate"</a> tool [TO BE CONFIRMED: page URL]. On request, ROCERT confirms in writing the validity of a certificate, the organisation\'s name, the reference standard, the scope and the certified sites. Requests should be sent to <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
            ]],
            ['heading' => '8. Changes to certification requirements', 'blocks' => [
                ['type' => 'p', 'text' => 'ROCERT informs certified clients in good time of any change to certification requirements — for example the publication of a new version of a standard, changes to accreditation rules or to its own certification rules — and of the applicable transition periods. ROCERT then verifies, usually during surveillance or recertification audits, that each client has complied with the new requirements.'],
            ]],
            ['heading' => '9. Accreditation and contact', 'blocks' => [
                ['type' => 'p', 'text' => 'ROCERT\'s accreditation status, accreditation body and the standards covered: [TO BE CONFIRMED: e.g. RENAR accreditation, accreditation certificate number and accredited scope].'],
                ['type' => 'p', 'text' => 'For further information: ROCERT SRL, Str. Iani Buzoiani nr. 1, bl. 16A, ap. 43, Sector 1, Bucharest 011571, Romania · Phone/fax <a href="tel:+40212242639">+40 21 224 26 39</a> · E-mail <a href="mailto:office@rocert.ro">office@rocert.ro</a>.'],
            ]],
        ],
    ],
];
