#!/usr/bin/env bash
# Idempotent: installs/updates WordPress settings, theme and pinned plugins, then seeds content on first run.
# Runs inside rocert-site-app as the rocert user (see `make provision`).
set -euo pipefail

cd /var/www/html
DEPLOY=/opt/rocert/deploy
CONTENT=/opt/rocert/content
OLLIE_VERSION=1.6.3

log() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }

log "Waiting for WordPress files and database"
for i in $(seq 1 60); do
  if [ -f wp-includes/version.php ] && [ -f wp-config.php ] && wp db check --defaults --quiet >/dev/null 2>&1; then break; fi
  sleep 2
  [ "$i" = 60 ] && { echo "WordPress or the database did not come up"; exit 1; }
done

if ! wp core is-installed 2>/dev/null; then
  log "Installing WordPress"
  wp core install --url="${APP_URL}" --title="${WP_TITLE:-ROCERT}" \
    --admin_user="${WP_ADMIN_USER}" --admin_password="${WP_ADMIN_PASSWORD}" \
    --admin_email="${WP_ADMIN_EMAIL}" --skip-email
fi

log "Language"
wp language core install "${WP_LOCALE:-ro_RO}" --activate >/dev/null
wp language core install en_GB >/dev/null || true

log "Theme"
if [ "$(wp theme get ollie --field=version 2>/dev/null || true)" != "$OLLIE_VERSION" ]; then
  wp theme install ollie --version="$OLLIE_VERSION" --force
fi
wp theme activate rocert
for t in $(wp theme list --field=name); do
  case "$t" in ollie|rocert) ;; *) wp theme delete "$t" ;; esac
done

log "Plugins"
wp plugin delete akismet hello >/dev/null 2>&1 || true
grep -vE '^\s*(#|$)' "$DEPLOY/plugins.txt" | while read -r slug version; do
  current="$(wp plugin get "$slug" --field=version 2>/dev/null || true)"
  if [ "$current" != "$version" ]; then
    wp plugin install "$slug" --version="$version" --force
  fi
  # Wordfence protects stage/prod; locally it only adds latency (it DNS-resolves the site host,
  # and "localhost:<port>" never resolves, so every request waits for two DNS timeouts).
  if [ "$slug" = wordfence ] && [ "${APP_ENV}" = local ]; then
    wp plugin is-active "$slug" && wp plugin deactivate "$slug"
    continue
  fi
  wp plugin is-active "$slug" || wp plugin activate "$slug"
done
wp plugin auto-updates enable wordfence wordpress-seo wp-mail-smtp google-site-kit wp-cloudflare-page-cache webp-uploads fluentform >/dev/null || true
wp plugin auto-updates disable polylang >/dev/null || true
wp language plugin install --all ro_RO en_GB >/dev/null 2>&1 || true
wp language theme install --all ro_RO en_GB >/dev/null 2>&1 || true

log "Settings"
wp option update blogdescription "Organism de certificare a sistemelor de management"
wp option update timezone_string "Europe/Bucharest"
wp option update date_format "d.m.Y"
wp option update time_format "H:i"
wp option update start_of_week 1
wp option update default_comment_status closed
wp option update default_ping_status closed
wp option update comment_registration 1
wp option update uploads_use_yearmonth_folders 0
wp option update thumbnail_size_w 480
wp option update thumbnail_size_h 360
wp option update medium_size_w 960
wp option update medium_size_h 0
wp option update large_size_w 1600
wp option update large_size_h 0
wp option update blog_public "$([ "${APP_ENV}" = production ] && echo 1 || echo 0)"
wp rewrite structure '/%postname%/' --hard >/dev/null

log "Plugin configuration"
wp eval-file "$DEPLOY/configure.php"

if [ "$(wp option get rocert_seeded 2>/dev/null || true)" = "" ]; then
  log "Seeding content (first run)"
  wp eval-file "$CONTENT/seed.php"
fi

log "SEO: indexables + llms.txt"
wp eval-file "$CONTENT/post-seed.php"

wp rewrite flush --hard >/dev/null
wp cache flush >/dev/null
log "Done: ${APP_URL}"
