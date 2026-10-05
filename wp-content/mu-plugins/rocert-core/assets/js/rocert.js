/* ROCERT front-end behaviour: category index, standards filter, certificate verification, carousels. */
(function () {
	'use strict';
	var cfg = window.rocertCfg || { rest: '/wp-json/rocert/v1/', i18n: {} };
	var t = function (k) { return cfg.i18n[k] || k; };
	var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };

	/* Home: hovering a certification area swaps the large image panel */
	document.querySelectorAll('[data-rc-catindex]').forEach(function (root) {
		var links = root.querySelectorAll('.rc-catindex__link');
		var panels = root.querySelectorAll('.rc-catindex__panel');
		function activate(i) {
			links.forEach(function (l) { l.classList.toggle('is-active', l.dataset.panel === String(i)); });
			panels.forEach(function (p) {
				var on = p.dataset.panel === String(i);
				p.classList.toggle('is-active', on);
				p.setAttribute('aria-hidden', on ? 'false' : 'true');
			});
		}
		links.forEach(function (l) {
			l.addEventListener('mouseenter', function () { activate(l.dataset.panel); });
			l.addEventListener('focus', function () { activate(l.dataset.panel); });
		});
	});

	/* Hub: filter the standards list by area */
	document.querySelectorAll('[data-rc-filter]').forEach(function (root) {
		var buttons = root.querySelectorAll('[data-filter]');
		var rows = root.querySelectorAll('[data-cat]');
		buttons.forEach(function (b) {
			b.addEventListener('click', function () {
				var f = b.dataset.filter;
				buttons.forEach(function (x) { x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
				rows.forEach(function (r) { r.hidden = f !== '*' && r.dataset.cat !== f; });
			});
		});
	});

	/* Verify page: lookup by certificate serial */
	document.querySelectorAll('[data-rc-verify]').forEach(function (root) {
		var form = root.querySelector('form');
		var input = form.querySelector('input[name="serie"]');
		var result = root.querySelector('[data-result]');

		function render(html) { result.innerHTML = html; result.hidden = false; }
		function empty(title, text) { render('<div class="rc-result__empty"><strong>' + esc(title) + '</strong>' + (text ? '<p>' + esc(text) + '</p>' : '') + '</div>'); }

		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var q = input.value.trim();
			if (!q) { input.focus(); return; }
			var btn = form.querySelector('button[type="submit"]');
			btn.disabled = true;
			render('<div class="rc-result__empty"><p>' + esc(t('searching')) + '</p></div>');
			fetch(cfg.rest + 'certificates/verify', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({ serial: q })
			}).then(function (r) { return r.json().then(function (d) { return { code: r.status, data: d }; }); })
				.then(function (res) {
					var d = res.data || {};
					if (res.code === 429) return empty(t('too_many'), '');
					if (d.status === 'invalid') return empty(t('serial_invalid'), t('serial_hint'));
					if (d.status === 'found') {
						var ok = !!d.valid;
						var icon = ok ? '<path d="M20 6 9 17l-5-5"/>' : '<path d="M18 6 6 18M6 6l12 12"/>';
						render('<div class="rc-result' + (ok ? '' : ' rc-result--invalid') + '">' +
							'<div class="rc-result__status"><span class="rc-result__icon"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' + icon + '</svg></span>' +
							'<div><p class="rc-result__label">' + esc(t(ok ? 'valid' : 'invalid')) + '</p><p class="rc-result__text">' + esc(ok ? t('valid_text') : (d.reason ? t('reason_' + d.reason) : t('invalid_text'))) + '</p></div></div>' +
							'<div class="rc-result__body"><p class="rc-result__num">' + esc(d.serial) + '</p><p class="rc-result__org">' + esc(d.organization) + '</p>' +
							'<dl><div><dt>' + esc(t('standard_label')) + '</dt><dd>' + esc(d.standard) + '</dd></div>' +
							(d.scope ? '<div><dt>' + esc(t('scope')) + '</dt><dd>' + esc(d.scope) + '</dd></div>' : '') + '</dl>' +
							'<p class="rc-result__source">' + esc(t('source_note')) + '</p></div></div>');
					} else if (d.status === 'not_found') {
						empty(t('not_found'), t('not_found_text'));
					} else {
						empty(t('unavailable'), t('unavailable_text'));
					}
				})
				.catch(function () { empty(t('unavailable'), t('unavailable_text')); })
				.finally(function () { btn.disabled = false; result.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); });
		});

		if (root.dataset.autorun) { form.requestSubmit ? form.requestSubmit() : form.dispatchEvent(new Event('submit')); }
	});

	/* Mouse drag to scroll a horizontal track (touch devices already swipe natively). A drag never counts as a
	   click on the links inside. onRelease(moved) runs when the mouse is let go. */
	function dragScroll(track, onRelease) {
		var dragging = false, moved = false, startX = 0, startLeft = 0;
		track.addEventListener('pointerdown', function (e) {
			if (e.pointerType !== 'mouse' || e.button !== 0) return;
			dragging = true; moved = false; startX = e.clientX; startLeft = track.scrollLeft;
		});
		window.addEventListener('pointermove', function (e) {
			if (!dragging) return;
			var dx = e.clientX - startX;
			/* Drag mode (no snapping, links inert) only once the mouse really moves, so a plain click still clicks */
			if (!moved && Math.abs(dx) > 4) { moved = true; track.classList.add('is-dragging'); }
			if (moved) track.scrollLeft = startLeft - dx;
		});
		window.addEventListener('pointerup', function () {
			if (!dragging) return;
			dragging = false;
			track.classList.remove('is-dragging');
			if (onRelease) onRelease(moved);
		});
		track.addEventListener('click', function (e) { if (moved) { e.preventDefault(); e.stopPropagation(); moved = false; } }, true);
		track.addEventListener('dragstart', function (e) { e.preventDefault(); });
	}

	/* Image strips (certification hub): drag, then let scroll-snap settle on the nearest card */
	document.querySelectorAll('.rc-strip').forEach(function (track) { dragScroll(track); });

	/* Carousels: drag to scroll, arrow buttons, optional autoplay that rewinds at the end */
	var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	document.querySelectorAll('[data-rc-carousel]').forEach(function (root) {
		var track = root.querySelector('.rc-hscroll');
		if (!track) return;
		var buttons = root.querySelectorAll('[data-dir]');
		var step = function () {
			var card = track.firstElementChild;
			return card ? card.getBoundingClientRect().width + 20 : track.clientWidth * 0.8;
		};
		var atEnd = function () { return track.scrollLeft + track.clientWidth >= track.scrollWidth - 4; };
		var atStart = function () { return track.scrollLeft <= 4; };
		var go = function (dir) {
			if (dir > 0 && atEnd()) { track.scrollTo({ left: 0, behavior: 'smooth' }); return; }
			if (dir < 0 && atStart()) { track.scrollTo({ left: track.scrollWidth, behavior: 'smooth' }); return; }
			track.scrollBy({ left: dir * step(), behavior: 'smooth' });
		};
		var sync = function () {
			var scrollable = track.scrollWidth > track.clientWidth + 4;
			root.classList.toggle('is-static', !scrollable);
		};
		buttons.forEach(function (b) { b.addEventListener('click', function () { go(Number(b.dataset.dir)); restart(); }); });
		track.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
			if (e.key === 'ArrowLeft') { e.preventDefault(); go(-1); }
		});

		dragScroll(track, function (moved) {
			if (moved) {
				var w = step();
				track.scrollTo({ left: Math.round(track.scrollLeft / w) * w, behavior: 'smooth' });
			}
			restart();
		});

		/* Autoplay: pauses on hover, focus, drag and when off-screen */
		var timer = null, hovering = false, visible = false;
		var auto = root.dataset.autoplay === '1' && !reduceMotion;
		var interval = Math.max(2, Number(root.dataset.interval) || 5) * 1000;
		function stop() { if (timer) { clearInterval(timer); timer = null; } }
		function restart() {
			stop();
			if (auto && visible && !hovering && !root.classList.contains('is-static')) { timer = setInterval(function () { go(1); }, interval); }
		}
		root.addEventListener('mouseenter', function () { hovering = true; stop(); });
		root.addEventListener('mouseleave', function () { hovering = false; restart(); });
		root.addEventListener('focusin', function () { hovering = true; stop(); });
		root.addEventListener('focusout', function () { hovering = false; restart(); });
		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) { visible = entries[0].isIntersecting; restart(); }, { threshold: 0.4 }).observe(root);
		}
		window.addEventListener('resize', sync);
		sync();
	});
})();
