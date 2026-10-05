<?php
/**
 * Loaded from wp-config.php (WORDPRESS_CONFIG_EXTRA). Everything environment-specific comes from env vars.
 */

$rocert_env = static fn (string $key, $default = '') => ($v = getenv($key)) !== false && $v !== '' ? $v : $default;

define('WP_ENVIRONMENT_TYPE', $rocert_env('APP_ENV', 'local'));

if ($rocert_env('APP_URL')) {
    define('WP_HOME', rtrim($rocert_env('APP_URL'), '/'));
    define('WP_SITEURL', rtrim($rocert_env('APP_URL'), '/'));
}

if (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

/* The shared MariaDB runs with ANSI_QUOTES, which turns "text" into a column name and breaks Yoast/Wordfence
   queries. WordPress already strips other incompatible modes per connection; add this one (pre-initialised
   hook, since the database connects before any plugin loads). */
$GLOBALS['wp_filter']['incompatible_sql_modes'][10][] = [
    'function' => static fn (array $modes): array => array_merge($modes, ['ANSI_QUOTES']),
    'accepted_args' => 1,
];

/* Super Page Cache's disk cache (advanced-cache.php drop-in, enabled by deploy/configure.php). */
define('WP_CACHE', WP_ENVIRONMENT_TYPE !== 'local');

define('DISALLOW_FILE_EDIT', true);
define('WP_POST_REVISIONS', 15);
define('AUTOSAVE_INTERVAL', 120);
define('EMPTY_TRASH_DAYS', 30);
define('FS_METHOD', 'direct');
define('WP_AUTO_UPDATE_CORE', 'minor');
define('FORCE_SSL_ADMIN', WP_ENVIRONMENT_TYPE !== 'local');
define('WP_DEBUG_LOG', WP_ENVIRONMENT_TYPE === 'local');
define('WP_DEBUG_DISPLAY', false);

/* WP Mail SMTP, configured entirely from env */
define('WPMS_ON', true);
define('WPMS_MAILER', 'smtp');
define('WPMS_SMTP_HOST', $rocert_env('SMTP_HOST', 'mailpit'));
define('WPMS_SMTP_PORT', (int) $rocert_env('SMTP_PORT', 1025));
define('WPMS_SSL', $rocert_env('SMTP_SECURE', ''));
define('WPMS_SMTP_AUTH', $rocert_env('SMTP_USER') !== '');
define('WPMS_SMTP_USER', $rocert_env('SMTP_USER'));
define('WPMS_SMTP_PASS', $rocert_env('SMTP_PASSWORD'));
define('WPMS_SMTP_AUTOTLS', $rocert_env('SMTP_SECURE') !== '');
define('WPMS_MAIL_FROM', $rocert_env('SMTP_FROM', 'noreply@rocert.ro'));
define('WPMS_MAIL_FROM_FORCE', true);
define('WPMS_MAIL_FROM_NAME', $rocert_env('SMTP_FROM_NAME', 'ROCERT'));
define('WPMS_MAIL_FROM_NAME_FORCE', true);

/* ROCERT integrations */
define('ROCERT_FORM_RECIPIENT', $rocert_env('FORM_RECIPIENT', 'formulare@example.com'));
define('ROCERT_API_URL', rtrim($rocert_env('ROCERT_API_URL'), '/'));
define('ROCERT_API_TOKEN', $rocert_env('ROCERT_API_TOKEN'));
define('ROCERT_TURNSTILE_SITE_KEY', $rocert_env('TURNSTILE_SITE_KEY'));
define('ROCERT_TURNSTILE_SECRET', $rocert_env('TURNSTILE_SECRET'));
