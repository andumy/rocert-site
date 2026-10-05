<?php
/**
 * Plugin Name: ROCERT Core
 * Description: Comportamentul site-ului ROCERT: biblioteca de blocuri ROCERT, verificare certificat, preluare date ANAF, cârlige formulare, consimțământ cookie-uri, performanță și securitate.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

define('ROCERT_CORE_DIR', __DIR__ . '/rocert-core');
define('ROCERT_CORE_URL', content_url('mu-plugins/rocert-core'));
define('ROCERT_CORE_VERSION', '1.0.0');

foreach (['i18n', 'helpers', 'performance', 'security', 'polylang', 'seo', 'consent', 'components', 'rest', 'forms', 'hooks', 'yoast'] as $file) {
    require_once ROCERT_CORE_DIR . "/inc/{$file}.php";
}
require_once ROCERT_CORE_DIR . '/blocks/registry.php';
