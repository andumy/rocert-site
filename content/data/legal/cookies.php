<?php
return [
    'key' => 'cookies',
    'ro' => [
        'slug' => 'politica-cookie-uri',
        'title' => 'Politica de cookie-uri',
        'seo_title' => 'Politica de cookie-uri a site-ului',
        'meta' => 'Ce cookie-uri folosește site-ul, în ce scop și cât timp sunt păstrate, cum funcționează consimțământul și cum îți poți modifica oricând preferințele.',
        'updated' => '05.10.2026',
        'intro' => 'Această politică explică ce sunt cookie-urile, ce cookie-uri folosește site-ul rocert.ro, operat de ROCERT SRL, și cum îți poți gestiona preferințele. Politica se aplică împreună cu <a href="/politica-de-confidentialitate">Politica de confidențialitate</a> și respectă prevederile Legii nr. 506/2004 și ale Regulamentului (UE) 2016/679 (GDPR).',
        'sections' => [
            ['heading' => '1. Ce sunt cookie-urile', 'blocks' => [
                ['type' => 'p', 'text' => 'Cookie-urile sunt fișiere text de mici dimensiuni pe care un site le stochează în browserul tău atunci când îl vizitezi. Ele permit site-ului să funcționeze corect, să rețină anumite opțiuni (de exemplu, alegerea privind cookie-urile) sau să ne ofere statistici despre modul de utilizare.'],
            ]],
            ['heading' => '2. Categorii de cookie-uri folosite', 'blocks' => [
                ['type' => 'list', 'items' => [
                    '<strong>Necesare</strong> (mereu active) — indispensabile pentru funcționarea și securitatea site-ului și pentru memorarea opțiunii tale privind cookie-urile. Nu necesită consimțământ, conform art. 4 alin. (6) din Legea nr. 506/2004.',
                    '<strong>Analiză/Statistică</strong> (opționale) — ne ajută să înțelegem cum este folosit site-ul, pentru a-l îmbunătăți. Sunt plasate <strong>doar după ce îți exprimi acordul</strong>.',
                ]],
                ['type' => 'p', 'text' => 'Nu folosim cookie-uri de publicitate sau de marketing.'],
            ]],
            ['heading' => '3. Lista cookie-urilor', 'blocks' => [
                ['type' => 'table', 'head' => ['Cookie', 'Furnizor', 'Scop', 'Durată', 'Categorie'], 'rows' => [
                    ['cc_cookie', 'rocert.ro', 'Memorează opțiunile tale privind cookie-urile', '6 luni', 'Necesare'],
                    ['__cf_bm', 'Cloudflare', 'Distinge vizitatorii reali de traficul automatizat (bot management)', '30 de minute', 'Necesare'],
                    ['cf_clearance', 'Cloudflare', 'Confirmă trecerea unei verificări de securitate', 'până la 1 an', 'Necesare'],
                    ['wfwaf-authcookie-*', 'rocert.ro (Wordfence)', 'Securitate; setat doar pentru administratorii autentificați', 'sesiunea de administrare', 'Necesare'],
                    ['_ga', 'Google (Google Analytics 4)', 'Distinge vizitatorii unici în statisticile agregate', '2 ani', 'Analiză/Statistică'],
                    ['_ga_&lt;ID&gt;', 'Google (Google Analytics 4)', 'Păstrează starea sesiunii pentru statisticile agregate', '2 ani', 'Analiză/Statistică'],
                ]],
                ['type' => 'p', 'text' => 'Formularele site-ului sunt protejate de <strong>Cloudflare Turnstile</strong>, un serviciu anti-spam care verifică faptul că trimiterea este făcută de o persoană, fără a folosi cookie-uri de urmărire. Serviciul <strong>Google Search Console</strong>, pe care îl folosim pentru monitorizarea prezenței în căutări, nu plasează cookie-uri.'],
                ['type' => 'p', 'text' => 'Duratele indicate sunt cele maxime; unele cookie-uri pot expira mai devreme. Lista poate fi actualizată dacă furnizorii modifică denumirile sau duratele cookie-urilor.'],
            ]],
            ['heading' => '4. Cum funcționează consimțământul', 'blocks' => [
                ['type' => 'p', 'text' => 'La prima vizită, un banner îți permite să accepți sau să refuzi cookie-urile de analiză ori să alegi în detaliu. Până la exprimarea acordului, folosim Google Consent Mode v2 cu stocarea pentru analiză setată implicit pe „refuzat”, astfel încât Google Analytics nu plasează cookie-uri și nu colectează date de identificare a vizitatorului.'],
                ['type' => 'p', 'text' => 'Îți poți modifica sau retrage <strong>oricând</strong> consimțământul din linkul <strong>„Setări cookie-uri”</strong> din subsolul fiecărei pagini. Retragerea consimțământului nu afectează legalitatea prelucrării efectuate înainte de retragere.'],
            ]],
            ['heading' => '5. Gestionarea cookie-urilor din browser', 'blocks' => [
                ['type' => 'p', 'text' => 'Poți de asemenea să ștergi sau să blochezi cookie-urile din setările browserului (Chrome, Firefox, Safari, Edge etc.). Blocarea cookie-urilor necesare poate afecta funcționarea unor părți ale site-ului, de exemplu trimiterea formularelor.'],
            ]],
            ['heading' => '6. Furnizori terți', 'blocks' => [
                ['type' => 'p', 'text' => 'Informații despre modul în care furnizorii terți prelucrează datele găsești în politicile acestora: <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener">Cloudflare</a> și <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google</a>.'],
            ]],
            ['heading' => '7. Contact', 'blocks' => [
                ['type' => 'p', 'text' => 'Pentru întrebări despre această politică ne poți scrie la <a href="mailto:office@rocert.ro">office@rocert.ro</a> sau ne poți suna la <a href="tel:+40212242639">+40 21 224 26 39</a>.'],
            ]],
        ],
    ],
    'en' => [
        'slug' => 'cookie-policy',
        'title' => 'Cookie Policy',
        'seo_title' => 'Cookie Policy for Our Website',
        'meta' => 'Which cookies our website uses, for what purpose and for how long, how consent works and how you can change your cookie preferences at any time you wish.',
        'updated' => '05.10.2026',
        'intro' => 'This policy explains what cookies are, which cookies are used on rocert.ro, operated by ROCERT SRL, and how you can manage your preferences. It applies together with our <a href="/en/privacy-policy">Privacy Policy</a> and complies with Romanian Law no. 506/2004 and Regulation (EU) 2016/679 (GDPR).',
        'sections' => [
            ['heading' => '1. What cookies are', 'blocks' => [
                ['type' => 'p', 'text' => 'Cookies are small text files that a website stores in your browser when you visit it. They allow the site to work properly, to remember certain choices (such as your cookie preferences) or to give us statistics about how it is used.'],
            ]],
            ['heading' => '2. Categories of cookies we use', 'blocks' => [
                ['type' => 'list', 'items' => [
                    '<strong>Necessary</strong> (always active) — essential for the website to work securely and to remember your cookie choice. They do not require consent under Art. 4(6) of Law no. 506/2004.',
                    '<strong>Analytics/Statistics</strong> (optional) — help us understand how the website is used so we can improve it. They are set <strong>only after you give your consent</strong>.',
                ]],
                ['type' => 'p', 'text' => 'We do not use advertising or marketing cookies.'],
            ]],
            ['heading' => '3. List of cookies', 'blocks' => [
                ['type' => 'table', 'head' => ['Cookie', 'Provider', 'Purpose', 'Duration', 'Category'], 'rows' => [
                    ['cc_cookie', 'rocert.ro', 'Stores your cookie preferences', '6 months', 'Necessary'],
                    ['__cf_bm', 'Cloudflare', 'Distinguishes real visitors from automated traffic (bot management)', '30 minutes', 'Necessary'],
                    ['cf_clearance', 'Cloudflare', 'Confirms that a security challenge has been passed', 'up to 1 year', 'Necessary'],
                    ['wfwaf-authcookie-*', 'rocert.ro (Wordfence)', 'Security; set only for logged-in administrators', 'administration session', 'Necessary'],
                    ['_ga', 'Google (Google Analytics 4)', 'Distinguishes unique visitors in aggregated statistics', '2 years', 'Analytics/Statistics'],
                    ['_ga_&lt;ID&gt;', 'Google (Google Analytics 4)', 'Keeps session state for aggregated statistics', '2 years', 'Analytics/Statistics'],
                ]],
                ['type' => 'p', 'text' => 'The website\'s forms are protected by <strong>Cloudflare Turnstile</strong>, an anti-spam service that checks that a submission is made by a human, without using tracking cookies. <strong>Google Search Console</strong>, which we use to monitor our presence in search results, does not set cookies.'],
                ['type' => 'p', 'text' => 'The durations shown are maximum values; some cookies may expire earlier. The list may be updated if providers change cookie names or durations.'],
            ]],
            ['heading' => '4. How consent works', 'blocks' => [
                ['type' => 'p', 'text' => 'On your first visit, a banner lets you accept or reject analytics cookies or choose in detail. Until you consent, we use Google Consent Mode v2 with analytics storage set to "denied" by default, so Google Analytics does not set cookies or collect visitor identifiers.'],
                ['type' => 'p', 'text' => 'You can change or withdraw your consent <strong>at any time</strong> via the <strong>"Cookie settings"</strong> link in the footer of every page. Withdrawing consent does not affect the lawfulness of processing carried out before withdrawal.'],
            ]],
            ['heading' => '5. Managing cookies in your browser', 'blocks' => [
                ['type' => 'p', 'text' => 'You can also delete or block cookies in your browser settings (Chrome, Firefox, Safari, Edge, etc.). Blocking necessary cookies may affect how parts of the website work, for example submitting forms.'],
            ]],
            ['heading' => '6. Third-party providers', 'blocks' => [
                ['type' => 'p', 'text' => 'Information on how third-party providers process data is available in their policies: <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener">Cloudflare</a> and <a href="https://policies.google.com/privacy" target="_blank" rel="noopener">Google</a>.'],
            ]],
            ['heading' => '7. Contact', 'blocks' => [
                ['type' => 'p', 'text' => 'For questions about this policy, write to us at <a href="mailto:office@rocert.ro">office@rocert.ro</a> or call <a href="tel:+40212242639">+40 21 224 26 39</a>.'],
            ]],
        ],
    ],
];
