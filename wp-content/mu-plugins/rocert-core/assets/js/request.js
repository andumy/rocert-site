/* Certification request form: row repeaters (extra sites, job roles) and signature pads. Each keeps its value in
   an ordinary Fluent Forms text field (JSON rows / PNG data URL), so Fluent's validation, entries and e-mails
   keep working; this script only draws the UI around that field. */
(function () {
	function sync(input) {
		input.dispatchEvent(new Event('input', { bubbles: true }));
		input.dispatchEvent(new Event('change', { bubbles: true }));
		if (window.jQuery) window.jQuery(input).trigger('change');
	}
	function el(tag, cls, text) {
		var e = document.createElement(tag);
		if (cls) e.className = cls;
		if (text) e.textContent = text;
		return e;
	}

	/* ---- Draft: saved on this device while it is filled in, restored on return, cleared once sent ----
	   Runs before the repeaters and signature pads, which build themselves from the restored values. */
	(function draft() {
		var anchor = document.querySelector('[data-rc-repeater]');
		var form = anchor && anchor.closest('form');
		if (!form) return;
		var en = (document.documentElement.lang || '').indexOf('en') === 0;
		var KEY = 'rocert-request-draft:' + location.pathname, DAYS = 30, sent = false, timer = null;
		var storage = null;
		try { storage = window.localStorage; storage.getItem(KEY); } catch (e) { storage = null; }
		if (!storage) return;
		function skip(name) { return !name || name.charAt(0) === '_' || name === 'gdpr-agreement' || name === 'cf-turnstile-response' || name.indexOf('nonce') !== -1; }
		function collect() {
			var v = {};
			[].forEach.call(form.elements, function (f) {
				if (skip(f.name) || f.type === 'submit' || f.type === 'button' || f.type === 'file') return;
				if (f.type === 'checkbox') { (v[f.name] = v[f.name] || []); if (f.checked) v[f.name].push(f.value); }
				else if (f.type === 'radio') { if (f.checked) v[f.name] = f.value; }
				else if (f.value !== '') v[f.name] = f.value;
			});
			return v;
		}
		function save() {
			if (sent) return;
			clearTimeout(timer);
			timer = setTimeout(function () {
				try { storage.setItem(KEY, JSON.stringify({ t: Date.now(), v: collect() })); } catch (e) { /* full or blocked: the form still works */ }
			}, 400);
		}
		function clear() { try { storage.removeItem(KEY); } catch (e) {} }

		var saved = null;
		try { saved = JSON.parse(storage.getItem(KEY) || 'null'); } catch (e) { saved = null; }
		if (saved && (Date.now() - saved.t > DAYS * 864e5)) { clear(); saved = null; }
		var restored = false;
		if (saved && saved.v) {
			[].forEach.call(form.elements, function (f) {
				if (skip(f.name) || !(f.name in saved.v)) return;
				var val = saved.v[f.name];
				if (f.type === 'checkbox') f.checked = [].concat(val).indexOf(f.value) !== -1;
				else if (f.type === 'radio') f.checked = f.value === val;
				else f.value = val;
				restored = true;
			});
		}

		var note = el('div', 'rc-draft-note');
		note.setAttribute('role', 'status');
		var text = el('span', '', restored
			? (en ? 'We restored what you entered earlier on this device.' : 'Am restaurat datele completate anterior pe acest dispozitiv.')
			: (en ? 'Your progress is saved on this device until you send the request.' : 'Progresul se salvează pe acest dispozitiv până la trimiterea cererii.'));
		note.appendChild(text);
		if (restored) {
			var reset = el('button', 'rc-draft-note__reset', en ? 'Start over' : 'Începe din nou');
			reset.type = 'button';
			reset.addEventListener('click', function () { sent = true; clear(); location.reload(); });
			note.appendChild(reset);
			note.classList.add('is-restored');
		}
		form.insertBefore(note, form.firstChild);

		form.addEventListener('input', save);
		form.addEventListener('change', save);
		if (window.jQuery) {
			window.jQuery(form).on('fluentform_submission_success', function () { sent = true; clearTimeout(timer); clear(); note.remove(); });
			/* Fluent applies its conditional logic on load; re-run it for the restored answers */
			if (restored) window.jQuery(function () { window.jQuery(form).find('input:checked, select').trigger('change'); });
		}
	})();

	/* ---- Row repeaters ---- */
	document.querySelectorAll('[data-rc-repeater]').forEach(function (host) {
		var form = host.closest('form');
		var input = form && form.querySelector('[name="' + host.dataset.rcRepeater + '"]');
		if (!input) return;
		var cfg = JSON.parse(host.dataset.config);
		var list = el('div', 'rc-repeater__rows');
		var add = el('button', 'rc-repeater__add');
		add.type = 'button';
		add.innerHTML = '<span aria-hidden="true">+</span> ';
		add.appendChild(document.createTextNode(cfg.add));
		host.append(list, add);
		var uid = 0;

		function save() {
			var rows = [].map.call(list.children, function (row) {
				var o = {};
				row.querySelectorAll('[data-key]').forEach(function (i) { o[i.dataset.key] = i.value.trim(); });
				return o;
			}).filter(function (o) { return Object.keys(o).some(function (k) { return o[k] !== ''; }); });
			input.value = rows.length ? JSON.stringify(rows) : '';
			sync(input);
		}
		function renumber() {
			[].forEach.call(list.children, function (row, i) {
				var t = row.querySelector('.rc-repeater__title');
				if (t) t.textContent = cfg.rowTitle.replace('%d', i + 1);
				row.querySelector('.rc-repeater__remove').setAttribute('aria-label', cfg.remove + (cfg.rowTitle ? ' ' + (i + 1) : ''));
			});
		}
		function addRow(values) {
			var row = el('div', 'rc-repeater__row' + (cfg.rowTitle ? '' : ' rc-repeater__row--compact'));
			if (cfg.rowTitle) row.appendChild(el('p', 'rc-repeater__title'));
			var grid = el('div', 'rc-repeater__grid');
			cfg.columns.forEach(function (c) {
				var id = 'rc-' + host.dataset.rcRepeater + '-' + (++uid);
				var cell = el('div', 'rc-repeater__cell rc-repeater__cell--' + c.type);
				var label = el('label', 'rc-repeater__label', c.label);
				label.htmlFor = id;
				var field = el('input', 'ff-el-form-control');
				field.id = id;
				field.dataset.key = c.key;
				field.type = c.type === 'number' ? 'number' : 'text';
				if (c.type === 'number') { field.min = '0'; field.inputMode = 'numeric'; } else { field.maxLength = 255; }
				field.value = values[c.key] || '';
				field.addEventListener('input', save);
				cell.append(label, field);
				grid.appendChild(cell);
			});
			var remove = el('button', 'rc-repeater__remove');
			remove.type = 'button';
			remove.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true"><path d="M18 6 6 18M6 6l12 12"/></svg>';
			remove.addEventListener('click', function () { row.remove(); renumber(); save(); add.focus(); });
			row.append(grid, remove);
			list.appendChild(row);
			renumber();
			return row;
		}

		var initial = [];
		try { initial = input.value ? JSON.parse(input.value) : []; } catch (e) { initial = []; }
		if (initial.length) initial.forEach(addRow); else if (cfg.startWithRow) addRow({});
		add.addEventListener('click', function () { addRow({}).querySelector('input').focus(); });
		form.addEventListener('reset', function () {
			setTimeout(function () { list.textContent = ''; if (cfg.startWithRow) addRow({}); save(); });
		});
	});

	/* ---- Signature pads ---- */
	document.querySelectorAll('[data-rc-sign]').forEach(function (host) {
		var form = host.closest('form');
		var input = form && form.querySelector('[name="' + host.dataset.rcSign + '"]');
		if (!input) return;
		/* The pad goes inside the field's own group, so Fluent's label, asterisk and error message frame it */
		input.classList.add('rc-proxy-input');
		input.tabIndex = -1;
		input.setAttribute('aria-hidden', 'true');
		var label = input.closest('.ff-el-group') && input.closest('.ff-el-group').querySelector('label');
		var canvas = el('canvas', 'rc-sign__pad');
		canvas.setAttribute('role', 'img');
		canvas.setAttribute('aria-label', (label ? label.textContent.trim() + '. ' : '') + host.dataset.hint);
		var bar = el('div', 'rc-sign__bar');
		var clear = el('button', 'rc-sign__clear', host.dataset.clear);
		clear.type = 'button';
		bar.append(el('span', 'rc-sign__hint', host.dataset.hint), clear);
		host.append(canvas, bar);
		input.parentNode.insertBefore(host, input);
		if (input.value) host.classList.add('is-signed'); /* restored from a draft */

		var ctx = canvas.getContext('2d');
		/* The pen strokes are kept as points: the signature sent is redrawn from them, cropped to what was drawn
		   and scaled to fit 300×100 px (the API contract's limit) with a constant 2 px line, so it stays legible. */
		var MAX_W = 300, MAX_H = 100, PAD = 4, LINE = 2;
		var strokes = [], drawing = false, w = 0, h = 0, restored = !!input.value;
		function pen(c, width) { c.lineWidth = width; c.lineCap = 'round'; c.lineJoin = 'round'; c.strokeStyle = '#14171C'; c.fillStyle = '#14171C'; }
		function trace(c, list) {
			list.forEach(function (s) {
				c.beginPath();
				if (s.length === 1) { c.arc(s[0].x, s[0].y, c.lineWidth / 2, 0, Math.PI * 2); c.fill(); return; }
				c.moveTo(s[0].x, s[0].y);
				for (var i = 1; i < s.length; i++) c.lineTo(s[i].x, s[i].y);
				c.stroke();
			});
		}
		function redraw() {
			ctx.clearRect(0, 0, w, h);
			if (restored && input.value) {
				/* A signature restored from a draft (already small) is shown at its proportions, centred */
				var img = new Image();
				img.onload = function () {
					var k = Math.min(w * 0.9 / img.width, h * 0.8 / img.height);
					ctx.drawImage(img, (w - img.width * k) / 2, (h - img.height * k) / 2, img.width * k, img.height * k);
				};
				img.src = input.value;
			}
			pen(ctx, 2.4);
			trace(ctx, strokes);
		}
		function setup() {
			var r = canvas.getBoundingClientRect(), dpr = window.devicePixelRatio || 1;
			if (!r.width || (Math.round(r.width) === w && Math.round(r.height) === h)) return;
			w = Math.round(r.width); h = Math.round(r.height);
			canvas.width = w * dpr; canvas.height = h * dpr;
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
			redraw();
		}
		function point(e) { var r = canvas.getBoundingClientRect(); return { x: e.clientX - r.left, y: e.clientY - r.top }; }
		function store() {
			var minX = Infinity, minY = Infinity, maxX = -Infinity, maxY = -Infinity;
			strokes.forEach(function (s) { s.forEach(function (p) { minX = Math.min(minX, p.x); minY = Math.min(minY, p.y); maxX = Math.max(maxX, p.x); maxY = Math.max(maxY, p.y); }); });
			if (maxX < minX) return;
			var bw = Math.max(1, maxX - minX), bh = Math.max(1, maxY - minY);
			var k = Math.min(1, (MAX_W - 2 * PAD) / bw, (MAX_H - 2 * PAD) / bh);
			var out = document.createElement('canvas');
			out.width = Math.min(MAX_W, Math.ceil(bw * k) + 2 * PAD);
			out.height = Math.min(MAX_H, Math.ceil(bh * k) + 2 * PAD);
			var o = out.getContext('2d');
			o.setTransform(k, 0, 0, k, PAD - minX * k, PAD - minY * k);
			pen(o, LINE / k);
			trace(o, strokes);
			input.value = out.toDataURL('image/png');
			host.classList.add('is-signed');
			sync(input);
		}
		function reset() { strokes = []; restored = false; redraw(); }
		canvas.addEventListener('pointerdown', function (e) {
			if (e.button !== 0) return;
			e.preventDefault();
			setup();
			if (restored) reset(); /* drawing over a restored signature means signing again */
			drawing = true;
			canvas.setPointerCapture(e.pointerId);
			strokes.push([point(e)]);
			redraw();
		});
		canvas.addEventListener('pointermove', function (e) {
			if (!drawing) return;
			strokes[strokes.length - 1].push(point(e));
			redraw();
		});
		function end() { if (drawing) { drawing = false; store(); } }
		canvas.addEventListener('pointerup', end);
		canvas.addEventListener('pointercancel', end);
		clear.addEventListener('click', function () {
			reset();
			input.value = '';
			host.classList.remove('is-signed');
			sync(input);
		});
		form.addEventListener('reset', function () { setTimeout(function () { reset(); host.classList.remove('is-signed'); }); });
		if ('ResizeObserver' in window) new ResizeObserver(setup).observe(canvas); else setup();
	});

	/* ---- The manager's name and position default to the legal representative from section 1 ---- */
	[['manager_name', 'signer_manager_name'], ['manager_role', 'signer_manager_role']].forEach(function (pair) {
		document.querySelectorAll('form [name="' + pair[0] + '"]').forEach(function (source) {
			var target = source.form.querySelector('[name="' + pair[1] + '"]');
			if (!target) return;
			target.addEventListener('input', function () { target.dataset.edited = '1'; });
			/* 'change' only: an 'input' event would mark the target as edited by the visitor */
			source.addEventListener('input', function () {
				if (!target.dataset.edited) { target.value = source.value; target.dispatchEvent(new Event('change', { bubbles: true })); }
			});
		});
	});
})();
