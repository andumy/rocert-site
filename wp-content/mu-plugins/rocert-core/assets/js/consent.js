/* Cookie consent (CookieConsent v3) wired to Google Consent Mode v2. */
(function () {
	if (!window.CookieConsent) return;
	var cfg = window.rocertConsent || { lang: 'ro' };

	function updateConsent() {
		if (typeof window.gtag !== 'function') return;
		var analytics = CookieConsent.acceptedCategory('analytics');
		window.gtag('consent', 'update', { analytics_storage: analytics ? 'granted' : 'denied' });
	}

	/* The cookies each category sets, as declared on the cookie policy page (keep the two in sync) */
	function table(lang, rows) {
		return {
			headers: lang === 'en'
				? { name: 'Cookie', provider: 'Provider', desc: 'Purpose', duration: 'Duration' }
				: { name: 'Cookie', provider: 'Furnizor', desc: 'Scop', duration: 'Durată' },
			body: rows.map(function (r) { return { name: r[0], provider: r[1], desc: r[2], duration: r[3] }; })
		};
	}
	var cookies = {
		ro: {
			necessary: table('ro', [
				['cc_cookie', 'rocert.ro', 'Memorează opțiunile tale privind cookie-urile', '6 luni'],
				['__cf_bm', 'Cloudflare', 'Distinge vizitatorii reali de traficul automatizat', '30 de minute'],
				['cf_clearance', 'Cloudflare', 'Confirmă trecerea unei verificări de securitate', 'până la 1 an'],
				['wfwaf-authcookie-*', 'rocert.ro (Wordfence)', 'Securitate; doar pentru administratorii autentificați', 'sesiunea de administrare']
			]),
			analytics: table('ro', [
				['_ga', 'Google (Analytics 4)', 'Distinge vizitatorii unici în statisticile agregate', '2 ani'],
				['_ga_&lt;ID&gt;', 'Google (Analytics 4)', 'Păstrează starea sesiunii pentru statisticile agregate', '2 ani']
			])
		},
		en: {
			necessary: table('en', [
				['cc_cookie', 'rocert.ro', 'Stores your cookie preferences', '6 months'],
				['__cf_bm', 'Cloudflare', 'Distinguishes real visitors from automated traffic', '30 minutes'],
				['cf_clearance', 'Cloudflare', 'Confirms that a security challenge has been passed', 'up to 1 year'],
				['wfwaf-authcookie-*', 'rocert.ro (Wordfence)', 'Security; only for logged-in administrators', 'administration session']
			]),
			analytics: table('en', [
				['_ga', 'Google (Analytics 4)', 'Distinguishes unique visitors in aggregated statistics', '2 years'],
				['_ga_&lt;ID&gt;', 'Google (Analytics 4)', 'Keeps session state for aggregated statistics', '2 years']
			])
		}
	};

	CookieConsent.run({
		revision: 1,
		cookie: { name: 'cc_cookie', expiresAfterDays: 182, sameSite: 'Lax' },
		guiOptions: {
			consentModal: { layout: 'box inline', position: 'bottom left', equalWeightButtons: true, flipButtons: false },
			preferencesModal: { layout: 'box', equalWeightButtons: true }
		},
		onFirstConsent: updateConsent,
		onConsent: updateConsent,
		onChange: function () {
			updateConsent();
			if (!CookieConsent.acceptedCategory('analytics')) {
				CookieConsent.eraseCookies(/^_ga/);
			}
		},
		categories: {
			necessary: { enabled: true, readOnly: true },
			analytics: {
				autoClear: { cookies: [{ name: /^_ga/ }] }
			}
		},
		language: {
			default: cfg.lang === 'en' ? 'en' : 'ro',
			translations: {
				ro: {
					consentModal: {
						title: 'Folosim cookie-uri',
						description: 'Folosim cookie-uri necesare pentru funcționarea site-ului și, doar cu acordul dumneavoastră, cookie-uri de analiză (Google Analytics) pentru a înțelege cum este folosit site-ul. Puteți schimba oricând alegerea din pictograma din colțul din stânga jos.',
						acceptAllBtn: 'Accept toate',
						acceptNecessaryBtn: 'Doar necesare',
						showPreferencesBtn: 'Setări',
						footer: '<a href="' + cfg.privacy + '">Politica de confidențialitate</a> <a href="' + cfg.cookies + '">Politica de cookie-uri</a>'
					},
					preferencesModal: {
						title: 'Setări cookie-uri',
						acceptAllBtn: 'Accept toate',
						acceptNecessaryBtn: 'Doar necesare',
						savePreferencesBtn: 'Salvează alegerea',
						closeIconLabel: 'Închide',
						sections: [
							{ description: 'Alegeți ce categorii de cookie-uri acceptați. Detalii în <a href="' + cfg.cookies + '">Politica de cookie-uri</a>.' },
							{ title: 'Necesare', description: 'Asigură funcționarea și securitatea site-ului (inclusiv protecția Cloudflare și memorarea acestei alegeri). Nu pot fi dezactivate.', linkedCategory: 'necessary', cookieTable: cookies.ro.necessary },
							{ title: 'Analiză', description: 'Google Analytics 4 ne ajută să înțelegem, agregat și anonimizat, cum este folosit site-ul. Se activează doar cu acordul dumneavoastră.', linkedCategory: 'analytics', cookieTable: cookies.ro.analytics }
						]
					}
				},
				en: {
					consentModal: {
						title: 'We use cookies',
						description: 'We use cookies that are necessary for the site to work and, only with your consent, analytics cookies (Google Analytics) to understand how the site is used. You can change your choice at any time from the icon in the bottom-left corner.',
						acceptAllBtn: 'Accept all',
						acceptNecessaryBtn: 'Necessary only',
						showPreferencesBtn: 'Settings',
						footer: '<a href="' + cfg.privacy + '">Privacy policy</a> <a href="' + cfg.cookies + '">Cookie policy</a>'
					},
					preferencesModal: {
						title: 'Cookie settings',
						acceptAllBtn: 'Accept all',
						acceptNecessaryBtn: 'Necessary only',
						savePreferencesBtn: 'Save my choice',
						closeIconLabel: 'Close',
						sections: [
							{ description: 'Choose which cookie categories you accept. Details in our <a href="' + cfg.cookies + '">Cookie policy</a>.' },
							{ title: 'Necessary', description: 'Keep the site working and secure (including Cloudflare protection and remembering this choice). They cannot be disabled.', linkedCategory: 'necessary', cookieTable: cookies.en.necessary },
							{ title: 'Analytics', description: 'Google Analytics 4 helps us understand, in aggregate, how the site is used. Enabled only with your consent.', linkedCategory: 'analytics', cookieTable: cookies.en.analytics }
						]
					}
				}
			}
		}
	});

	/* Permanent cookie-settings button, bottom left (same pattern as respirebien.ro): hidden while a consent
	   dialog is open (CSS, via the library's html classes), opens the preferences, gets focus back on close. */
	var pill = document.createElement('button');
	pill.type = 'button';
	pill.className = 'rc-cc-pill';
	pill.setAttribute('aria-label', cfg.lang === 'en' ? 'Cookie settings' : 'Setări cookie-uri');
	pill.title = pill.getAttribute('aria-label');
	pill.innerHTML = '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>';
	var fromPill = false;
	pill.addEventListener('click', function () { fromPill = true; CookieConsent.showPreferences(); });
	window.addEventListener('cc:onModalHide', function () {
		if (fromPill && !document.documentElement.classList.contains('show--preferences')) { fromPill = false; pill.focus(); }
	});
	document.body.appendChild(pill);
})();
