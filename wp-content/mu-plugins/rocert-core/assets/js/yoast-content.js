/* Gives Yoast SEO the rendered page (ROCERT blocks render on the server) so its analysis sees real text, images and links. */
(function (wp) {
	'use strict';
	var html = null;
	var postId = null;
	var registered = false;

	function load() {
		if (!postId) return;
		wp.apiFetch({ path: '/rocert/v1/rendered/' + postId }).then(function (res) {
			html = res && res.html ? res.html : null;
			if (registered && window.YoastSEO && YoastSEO.app && YoastSEO.app.pluginReloaded) YoastSEO.app.pluginReloaded('rocert');
		}).catch(function () {});
	}

	function register() {
		if (registered || !window.YoastSEO || !YoastSEO.app || !YoastSEO.app.registerPlugin) return false;
		YoastSEO.app.registerPlugin('rocert', { status: 'ready' });
		YoastSEO.app.registerModification('content', function (content) { return html !== null ? html : content; }, 'rocert', 5);
		registered = true;
		YoastSEO.app.pluginReloaded('rocert');
		return true;
	}

	wp.data.subscribe(function () {
		var editor = wp.data.select('core/editor');
		if (!editor) return;
		if (!postId) {
			postId = editor.getCurrentPostId();
			if (postId) load();
		}
		if (!registered) register();
	});

	/* Re-render after every save, so the analysis follows what was just published. */
	var wasSaving = false;
	wp.data.subscribe(function () {
		var editor = wp.data.select('core/editor');
		if (!editor) return;
		var saving = editor.isSavingPost() && !editor.isAutosavingPost();
		if (wasSaving && !saving) load();
		wasSaving = saving;
	});
	window.addEventListener('YoastSEO:ready', register);
})(window.wp);
