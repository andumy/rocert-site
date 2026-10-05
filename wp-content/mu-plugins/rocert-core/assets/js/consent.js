/* Cookie consent (CookieConsent v3) wired to Google Consent Mode v2. */
(function () {
	if (!window.CookieConsent) return;
	var cfg = window.rocertConsent || { lang: 'ro' };

	function updateConsent() {
		if (typeof window.gtag !== 'function') return;
		var analytics = CookieConsent.acceptedCategory('analytics');
		window.gtag('consent', 'update', { analytics_storage: analytics ? 'granted' : 'denied' });
	}

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
						description: 'Folosim cookie-uri necesare pentru funcționarea site-ului și, doar cu acordul dumneavoastră, cookie-uri de analiză (Google Analytics) pentru a înțelege cum este folosit site-ul. Puteți schimba oricând alegerea din subsolul paginii.',
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
							{ title: 'Necesare', description: 'Asigură funcționarea și securitatea site-ului (inclusiv protecția Cloudflare și memorarea acestei alegeri). Nu pot fi dezactivate.', linkedCategory: 'necessary' },
							{ title: 'Analiză', description: 'Google Analytics 4 ne ajută să înțelegem, agregat și anonimizat, cum este folosit site-ul. Se activează doar cu acordul dumneavoastră.', linkedCategory: 'analytics' }
						]
					}
				},
				en: {
					consentModal: {
						title: 'We use cookies',
						description: 'We use cookies that are necessary for the site to work and, only with your consent, analytics cookies (Google Analytics) to understand how the site is used. You can change your choice at any time from the page footer.',
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
							{ title: 'Necessary', description: 'Keep the site working and secure (including Cloudflare protection and remembering this choice). They cannot be disabled.', linkedCategory: 'necessary' },
							{ title: 'Analytics', description: 'Google Analytics 4 helps us understand, in aggregate, how the site is used. Enabled only with your consent.', linkedCategory: 'analytics' }
						]
					}
				}
			}
		}
	});
})();
