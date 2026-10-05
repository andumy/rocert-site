#!/usr/bin/env bash
# Stage → prod hand-over for an FTP-only host. Produces build/rocert-prod/ with:
#   public_html/        the complete WordPress tree (core, plugins, theme, mu-plugins, uploads)
#   database.sql        the database, with every stage URL rewritten to PROD_URL (serialisation-safe)
#   wp-config.php       a standalone config to fill in with the host's DB credentials and salts
# Upload public_html/ by FTP, import database.sql in phpMyAdmin, then follow deploy/README.md.
set -euo pipefail
cd "$(dirname "$0")/.."

PROD_URL="${PROD_URL:-https://rocert.ro}"
OUT=build/rocert-prod
SRC_URL="$(docker exec -u rocert rocert-site-app wp option get home)"

rm -rf "$OUT" && mkdir -p "$OUT/public_html"
echo "==> Database ($SRC_URL → $PROD_URL)"
docker exec -u rocert rocert-site-app bash -c "wp search-replace '$SRC_URL' '$PROD_URL' --all-tables-with-prefix --skip-columns=guid --export=/tmp/database.sql --defaults >/dev/null && cat /tmp/database.sql && rm /tmp/database.sql" > "$OUT/database.sql"

echo "==> Files"
rsync -a --delete --exclude wp-config.php --exclude wp-config-docker.php --exclude 'wp-content/wflogs/' --exclude 'wp-content/cache/' wordpress/ "$OUT/public_html/"
rsync -a --delete wp-content/themes/rocert/ "$OUT/public_html/wp-content/themes/rocert/"
rsync -a --delete wp-content/mu-plugins/ "$OUT/public_html/wp-content/mu-plugins/"
mkdir -p "$OUT/public_html/wp-content/rocert-config"
cp docker/wp/config.php "$OUT/public_html/wp-content/rocert-config/config.php"

cat > "$OUT/public_html/wp-config.php" <<'PHP'
<?php
/* Production config for a host without environment variables: fill in the values below. */
putenv('APP_ENV=production');
putenv('APP_URL=https://rocert.ro');
putenv('SMTP_HOST=');
putenv('SMTP_PORT=587');
putenv('SMTP_SECURE=tls');
putenv('SMTP_USER=');
putenv('SMTP_PASSWORD=');
putenv('SMTP_FROM=noreply@rocert.ro');
putenv('FORM_RECIPIENT=office@rocert.ro');
putenv('ROCERT_API_URL=');
putenv('ROCERT_API_TOKEN=');
putenv('TURNSTILE_SITE_KEY=');
putenv('TURNSTILE_SECRET=');

define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');
$table_prefix = 'rc_';

/* https://api.wordpress.org/secret-key/1.1/salt/ */
define('AUTH_KEY', '');
define('SECURE_AUTH_KEY', '');
define('LOGGED_IN_KEY', '');
define('NONCE_KEY', '');
define('AUTH_SALT', '');
define('SECURE_AUTH_SALT', '');
define('LOGGED_IN_SALT', '');
define('NONCE_SALT', '');

define('WP_DEBUG', false);
require_once __DIR__ . '/wp-content/rocert-config/config.php';

if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once ABSPATH . 'wp-settings.php';
PHP
echo "==> Done: $OUT  ($(du -sh "$OUT" | cut -f1))"
