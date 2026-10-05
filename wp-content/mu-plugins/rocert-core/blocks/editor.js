/**
 * ROCERT blocks — editor side. No build step: plain JS on the wp.* globals.
 * Leaf blocks: sidebar controls generated from the PHP schema + live server-side preview.
 * Containers (section, split, carousel, faq, intro) and inline blocks (tile, section-head): custom edit views
 * using the same CSS classes as the front end, so they look like the page while editing.
 */
(function (wp, data) {
	'use strict';
	if (!wp || !data) return;

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var c = wp.components;
	var SSR = wp.serverSideRender;
	var useSelect = wp.data.useSelect;

	/* ---------- Controls ---------- */

	function opts(map) {
		return Object.keys(map).map(function (k) { return { value: k, label: map[k] }; });
	}

	function ImageControl(props) {
		var id = props.value || 0;
		var media = useSelect(function (select) { return id ? select('core').getMedia(id) : null; }, [id]);
		return el(c.BaseControl, { label: props.label, __nextHasNoMarginBottom: true },
			el('div', { className: 'rocert-image-control' },
				media && media.source_url ? el('img', { src: (media.media_details && media.media_details.sizes && media.media_details.sizes.medium ? media.media_details.sizes.medium.source_url : media.source_url), alt: '' }) : null,
				el(be.MediaUploadCheck, null,
					el(be.MediaUpload, {
						onSelect: function (m) { props.onChange(m.id); },
						allowedTypes: ['image'],
						value: id,
						render: function (o) {
							return el('div', { className: 'rocert-image-control__buttons' },
								el(c.Button, { variant: 'secondary', onClick: o.open }, id ? 'Schimbă imaginea' : 'Alege imaginea'),
								id ? el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { props.onChange(0); } }, 'Elimină') : null
							);
						}
					})
				)
			)
		);
	}

	function field(def, value, onChange, key) {
		var label = def.label || key;
		switch (def.control) {
			case 'textarea':
				return el(c.TextareaControl, { key: key, label: label, value: value || '', onChange: onChange, rows: 3, __nextHasNoMarginBottom: true });
			case 'url':
				return el(c.TextControl, { key: key, label: label, value: value || '', onChange: onChange, type: 'text', placeholder: '/pagina/ sau https://…', __nextHasNoMarginBottom: true });
			case 'number':
				return el(c.TextControl, { key: key, label: label, value: value === undefined || value === null ? '' : String(value), onChange: function (v) { onChange(v === '' ? 0 : Number(v)); }, type: 'number', __nextHasNoMarginBottom: true });
			case 'toggle':
				return el(c.ToggleControl, { key: key, label: label, checked: !!value, onChange: onChange, __nextHasNoMarginBottom: true });
			case 'select':
				return el(c.SelectControl, { key: key, label: label, value: value === undefined ? '' : String(value), options: opts(def.options || {}), onChange: onChange, __nextHasNoMarginBottom: true });
			case 'image':
				return el(ImageControl, { key: key, label: label, value: value, onChange: onChange });
			case 'page':
				return el(c.SelectControl, {
					key: key, label: label, value: String(value || ''), __nextHasNoMarginBottom: true,
					options: [{ value: '', label: '— alege —' }].concat(data.pages.filter(function (p) { return !def.pageType || p.type === def.pageType; }).map(function (p) { return { value: String(p.id), label: p.title }; })),
					onChange: function (v) { onChange(v ? Number(v) : 0); }
				});
			case 'form':
				return el(c.SelectControl, {
					key: key, label: label, value: String(value || ''), __nextHasNoMarginBottom: true,
					options: [{ value: '', label: '— alege —' }].concat(data.forms.map(function (f) { return { value: String(f.id), label: f.title }; })),
					onChange: function (v) { onChange(v ? Number(v) : 0); }
				});
			case 'repeater':
				return el(Repeater, { key: key, def: def, value: value || [], onChange: onChange });
			default:
				return el(c.TextControl, { key: key, label: label, value: value || '', onChange: onChange, __nextHasNoMarginBottom: true });
		}
	}

	function Repeater(props) {
		var items = Array.isArray(props.value) ? props.value : [];
		var fields = props.def.fields || {};
		function update(i, k, v) {
			var next = items.map(function (it, j) { if (j !== i) return it; var o = Object.assign({}, it); o[k] = v; return o; });
			props.onChange(next);
		}
		function move(i, d) {
			var next = items.slice(); var t = next[i]; next[i] = next[i + d]; next[i + d] = t; props.onChange(next);
		}
		function remove(i) { props.onChange(items.filter(function (_, j) { return j !== i; })); }
		var max = props.def.max || 0;
		function add() {
			var blank = {}; Object.keys(fields).forEach(function (k) { blank[k] = fields[k].control === 'select' ? Object.keys(fields[k].options)[0] : ''; });
			props.onChange(items.concat([blank]));
		}
		return el('div', { className: 'rocert-repeater' },
			el('p', { className: 'rocert-repeater__label' }, props.def.label),
			items.map(function (it, i) {
				var first = fields[Object.keys(fields)[0]];
				var summary = it[Object.keys(fields)[0]];
				if (first && first.control === 'page') { var pg = data.pages.filter(function (p) { return p.id === summary; })[0]; summary = pg ? pg.title : ''; }
				return el(c.PanelBody, { key: i, title: (i + 1) + '. ' + (summary || '(gol)'), initialOpen: false, className: 'rocert-repeater__item' },
					Object.keys(fields).map(function (k) { return field(fields[k], it[k], function (v) { update(i, k, v); }, k); }),
					el('div', { className: 'rocert-repeater__actions' },
						el(c.Button, { icon: 'arrow-up-alt2', label: 'Sus', disabled: i === 0, onClick: function () { move(i, -1); } }),
						el(c.Button, { icon: 'arrow-down-alt2', label: 'Jos', disabled: i === items.length - 1, onClick: function () { move(i, 1); } }),
						el(c.Button, { icon: 'trash', label: 'Șterge', isDestructive: true, onClick: function () { remove(i); } })
					)
				);
			}),
			max && items.length >= max
				? el('p', { className: 'rocert-help' }, 'Maximum ' + max + ' elemente pentru acest bloc.')
				: el(c.Button, { variant: 'secondary', icon: 'plus', onClick: add }, 'Adaugă' + (max ? ' (' + items.length + '/' + max + ')' : ''))
		);
	}

	function Inspector(props) {
		var schema = props.schema;
		var keys = Object.keys(schema.attributes).filter(function (k) { return !(schema.inline || []).includes(k) || schema.attributes[k].control === 'repeater'; });
		if (!keys.length && !schema.description) return null;
		return el(be.InspectorControls, null,
			el(c.PanelBody, { title: 'Setări', initialOpen: true },
				schema.description ? el('p', { className: 'rocert-help' }, schema.description) : null,
				keys.map(function (k) {
					return field(schema.attributes[k], props.attributes[k], function (v) { var o = {}; o[k] = v; props.setAttributes(o); }, k);
				})
			)
		);
	}

	/* Inline editable text (no formatting) */
	function inline(props, key, tag, className, placeholder) {
		return el(be.RichText, {
			tagName: tag, className: className, value: props.attributes[key] || '', placeholder: placeholder,
			allowedFormats: [], withoutInteractiveFormatting: true,
			onChange: function (v) { var o = {}; o[key] = v; props.setAttributes(o); }
		});
	}

	function bgClass(bg) { return bg === 'white' ? 'rc-surface' : bg === 'dark' ? 'rc-dark' : bg === 'blue' ? 'rc-blue' : ''; }

	/* ---------- Custom edit views ---------- */

	var customEdit = {
		section: function (props) {
			var a = props.attributes;
			var cls = ['rc-section', 'rc-section--' + (a.spacing || 'normal'), bgClass(a.bg), a.grid ? 'rc-grid-bg' : ''].join(' ');
			var inner = el(be.InnerBlocks, { templateLock: false });
			return el('section', be.useBlockProps({ className: cls }),
				a.width === 'full' ? inner : el('div', { className: a.width === 'narrow' ? 'rc-narrow' : 'rc-wrap' }, inner));
		},
		split: function (props) {
			var a = props.attributes;
			var media = useSelect(function (select) { return a.image ? select('core').getMedia(a.image) : null; }, [a.image]);
			var cls = ['rc-split', 'rc-split--media-' + (a.side === 'right' ? 'right' : 'left'), 'rc-section', bgClass(a.bg), a.tall ? 'rc-split--tall' : ''].join(' ');
			var mediaEl = el('div', { className: 'rc-split__media rc-rounded' },
				media && media.source_url ? el('img', { src: media.source_url, alt: '' }) : el('div', { className: 'rocert-placeholder' }, 'Alegeți imaginea din bara laterală'),
				a.badgeValue ? el('div', { className: 'rc-float-badge' }, el('strong', null, a.badgeValue), el('span', null, a.badgeText)) : null);
			var content = el('div', { className: 'rc-split__content rc-stack' }, el(be.InnerBlocks, { templateLock: false, template: props.schema.inner.template }));
			return el('section', be.useBlockProps({ className: cls }), a.side === 'right' ? [content, mediaEl] : [mediaEl, content]);
		},
		carousel: function (props) {
			var a = props.attributes;
			return el('div', be.useBlockProps({ className: 'rc-carousel' + (a.bigNumbers ? ' rc-carousel--steps' : '') }),
				el('div', { className: 'rc-hscroll rocert-hscroll-edit' },
					el(be.InnerBlocks, { allowedBlocks: ['rocert/tile'], template: props.schema.inner.template, orientation: 'horizontal', renderAppender: be.InnerBlocks.ButtonBlockAppender })));
		},
		tile: function (props) {
			var a = props.attributes;
			return el('div', be.useBlockProps({ className: 'rc-tile rc-tile--' + (a.tone || 'light') }),
				inline(props, 'number', 'p', 'rc-tile__n', '01'),
				inline(props, 'title', 'h3', '', 'Titlu card'),
				inline(props, 'text', 'p', 'rc-tile__text', 'Text card'),
				a.buttonText ? el('span', { className: 'rocert-fake-button' }, a.buttonText) : null);
		},
		faq: function (props) {
			return el('section', be.useBlockProps({ className: 'rc-faq' }),
				el('div', { className: 'rc-faq__head' }, inline(props, 'eyebrow', 'p', 'rc-eyebrow', 'Etichetă'), inline(props, 'title', 'h2', 'rc-h2-s', 'Titlu')),
				el('div', { className: 'rc-faq__list' }, el(be.InnerBlocks, { allowedBlocks: ['core/details'], template: props.schema.inner.template })));
		},
		intro: function (props) {
			return el('div', be.useBlockProps({ className: 'rc-twocol' }),
				el('div', null, inline(props, 'eyebrow', 'p', 'rc-eyebrow', 'Etichetă'), inline(props, 'title', 'h2', 'rc-h2-s', 'Afirmație')),
				el('div', { className: 'rc-twocol__body' }, el(be.InnerBlocks, { allowedBlocks: props.schema.inner.allowed, template: props.schema.inner.template })));
		},
		'section-head': function (props) {
			var a = props.attributes;
			return el('div', be.useBlockProps({ className: 'rc-section-head' + (a.text ? '' : ' rc-section-head--solo') }),
				el('div', null, inline(props, 'eyebrow', 'p', 'rc-eyebrow', 'Etichetă'), inline(props, 'title', a.level === '3' ? 'h3' : 'h2', 'rc-h2', 'Titlu secțiune')),
				inline(props, 'text', 'p', 'rc-section-head__text', 'Text lateral (opțional)'));
		}
	};

	/* ---------- Registration ---------- */

	Object.keys(data.schema).forEach(function (name) {
		var schema = data.schema[name];
		var attributes = { className: { type: 'string', default: '' } };
		var types = { text: 'string', textarea: 'string', url: 'string', select: 'string', number: 'number', image: 'number', page: 'number', form: 'number', toggle: 'boolean', repeater: 'array' };
		Object.keys(schema.attributes).forEach(function (k) {
			var d = schema.attributes[k];
			attributes[k] = { type: types[d.control] || 'string', default: d.default === undefined ? null : d.default };
		});

		var toInner = function (list) {
			return (list || []).map(function (b) { return { name: b[0], attributes: b[1] || {}, innerBlocks: toInner(b[2]) }; });
		};
		var example = schema.example ? { attributes: schema.example.attributes || {}, innerBlocks: toInner(schema.example.innerBlocks), viewportWidth: 1400 } : undefined;

		wp.blocks.registerBlockType('rocert/' + name, {
			example: example,
			apiVersion: 3,
			title: schema.title,
			description: schema.description || '',
			icon: schema.icon || 'block-default',
			category: 'rocert',
			parent: schema.parent || undefined,
			attributes: attributes,
			supports: { html: false, customClassName: false, reusable: false },
			edit: function (props) {
				props.schema = schema;
				var view = customEdit[name]
					? customEdit[name](props)
					: el('div', be.useBlockProps({ className: 'rocert-ssr' }), el(SSR, { block: 'rocert/' + name, attributes: props.attributes, httpMethod: 'POST', skipBlockSupportAttributes: true, EmptyResponsePlaceholder: function () { return el('div', { className: 'rocert-empty' }, schema.title + ' — ' + (schema.description || 'fără conținut încă')); } }));
				return el(Fragment, null, el(Inspector, { schema: schema, attributes: props.attributes, setAttributes: props.setAttributes }), view);
			},
			save: function () {
				return schema.inner ? el(be.InnerBlocks.Content) : null;
			}
		});
	});
})(window.wp, window.rocertBlocks);
