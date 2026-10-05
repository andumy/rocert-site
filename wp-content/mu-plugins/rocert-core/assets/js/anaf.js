/* Certification request form: company data by CUI (ANAF, via the rocert API) and preselection from the URL. */
(function () {
	'use strict';
	var cfg = window.rocertCfg || { rest: '/wp-json/rocert/v1/', i18n: {} };
	var t = function (k) { return cfg.i18n[k] || k; };
	var map = { name: 'company_name', address: 'address', city: 'city', county: 'county', postal_code: 'postal_code', reg_com: 'reg_com', phone: 'phone', iban: 'iban' };

	function init(form) {
		var cui = form.querySelector('input[name="cui"]');
		if (!cui || cui.dataset.rcAnaf) return;
		cui.dataset.rcAnaf = '1';
		var group = cui.closest('.ff-el-group');
		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = 'rc-btn rc-anaf-btn';
		btn.textContent = t('anaf_button');
		var msg = document.createElement('p');
		msg.className = 'rc-anaf-msg';
		msg.setAttribute('aria-live', 'polite');
		group.appendChild(btn);
		group.parentNode.insertBefore(msg, group.nextSibling);

		function say(text, ok) { msg.textContent = text; msg.className = 'rc-anaf-msg ' + (ok ? 'is-ok' : 'is-err'); }

		btn.addEventListener('click', function () {
			var value = cui.value.replace(/\s+/g, '').replace(/^RO/i, '');
			if (!/^\d{2,10}$/.test(value)) { say(t('anaf_invalid'), false); cui.focus(); return; }
			btn.disabled = true;
			btn.textContent = t('anaf_loading');
			fetch(cfg.rest + 'company?cui=' + encodeURIComponent(value))
				.then(function (r) { return r.json(); })
				.then(function (d) {
					if (d.status !== 'found') { say(t('anaf_fail'), false); return; }
					Object.keys(map).forEach(function (k) {
						var input = form.querySelector('[name="' + map[k] + '"]');
						if (input && d.company[k]) {
							input.value = d.company[k];
							input.dispatchEvent(new Event('input', { bubbles: true }));
							input.dispatchEvent(new Event('change', { bubbles: true }));
						}
					});
					say(t('anaf_ok'), true);
				})
				.catch(function () { say(t('anaf_fail'), false); })
				.finally(function () { btn.disabled = false; btn.textContent = t('anaf_button'); });
		});
	}

	/* ?standard=iso-9001 and ?tip=migration (links from standard and category pages) preselect the form */
	function preselect(form) {
		var params = new URLSearchParams(window.location.search);
		var check = function (selector) {
			var input = form.querySelector(selector);
			if (!input || input.checked) return;
			input.checked = true;
			input.dispatchEvent(new Event('change', { bubbles: true }));
		};
		params.getAll('standard').forEach(function (key) {
			if (/^[a-z0-9-]{2,40}$/.test(key)) check('input[name="standards[]"][value="' + key + '"]');
		});
		var tip = params.get('tip');
		if (tip && /^[a-z]{3,20}$/.test(tip)) check('input[name="request_type"][value="' + tip + '"]');
	}

	function scan() { document.querySelectorAll('form.frm-fluent-form').forEach(function (form) { init(form); preselect(form); }); }
	if (document.readyState !== 'loading') scan(); else document.addEventListener('DOMContentLoaded', scan);
})();
