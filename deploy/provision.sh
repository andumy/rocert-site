#!/usr/bin/env bash
# Idempotent: installs/updates WordPress settings, theme and pinned plugins, then seeds content on first run.
# Runs inside rocert-site-app as the rocert user (see `make provision`).
set -euo pipefail

cd /var/www/html
DEPLOY=/opt/rocert/deploy
CONTENT=/opt/rocert/content
OLLIE_VERSION=1.6.3

log() { printf '\n\033[1;34m==> %s\033[0m\n' "$*"; }
# Pinned versions are minimums: true when $1 (installed) is missing or older than $2. Never downgrade anything that
# updated past the pin on the server.
needs_install() { [ -z "$1" ] || [ "$(printf '%s\n' "$1" "$2" | sort -V | tail -n1)" != "$1" ]; }

# Installed version of a plugin/theme ($1 = plugin|theme, $2 = slug): empty when it is not installed. Aborts when
# wp-cli answers anything but a plain version, so a broken read never turns into a reinstall.
installed_version() {
  local v dir="wp-content/$1s/$2"
  if ! wp "$1" is-installed "$2" </dev/null 2>/dev/null; then
    # wp-cli failing for another reason also lands here: only call it missing when its folder is empty or absent.
    [ -z "$(ls -A "$dir" 2>/dev/null)" ] && return 0
    echo "wp-cli does not see $1 $2 but $dir has files; not touching it" >&2
    exit 1
  fi
  v="$(wp "$1" get "$2" --field=version </dev/null 2>/dev/null || true)"
  [[ "$v" =~ ^[0-9][0-9A-Za-z.+-]*$ ]] || { echo "Cannot read the installed version of $1 $2 (got: '$v'); not touching it" >&2; exit 1; }
  printf '%s' "$v"
}

# wp <plugin|theme> install --force deletes the old copy before it unpacks the new one, so a failed install leaves
# nothing behind. Keep a copy of the folder and put it back when the install fails.
safe_install() {
  local type="$1" slug="$2" version="$3" dir="wp-content/$1s/$2" backup
  backup="$(mktemp -d)"
  [ -d "$dir" ] && cp -a "$dir" "$backup/"
  if wp "$type" install "$slug" --version="$version" --force </dev/null \
    && [ "$(installed_version "$type" "$slug")" = "$version" ]; then
    { chmod -R u+w "$backup"; rm -rf "$backup"; } 2>/dev/null || true
    return 0
  fi
  if [ -d "$backup/$slug" ]; then
    rm -rf "$dir" 2>/dev/null || true
    mkdir -p "$dir" && cp -a "$backup/$slug/." "$dir/"
    echo "Installing $type $slug $version failed; restored the previous copy"
  fi
  { chmod -R u+w "$backup"; rm -rf "$backup"; } 2>/dev/null || true
  exit 1
}

log "Waiting for WordPress files and database"
for i in $(seq 1 60); do
  if [ -f wp-includes/version.php ] && [ -f wp-config.php ] && wp db check --defaults --quiet >/dev/null 2>&1; then break; fi
  sleep 2
  [ "$i" = 60 ] && { echo "WordPress or the database did not come up"; exit 1; }
done

# WordPress' background updater (wp-cron) may start right after the container restarts; two installers working on
# the same plugin folder delete each other's files. Wait for its lock, then hold it ourselves until provision ends.
log "Waiting for WordPress' background updates"
for i in $(seq 1 120); do
  wp option add auto_updater.lock "$(date +%s)" >/dev/null 2>&1 && break
  # Like WordPress, treat a lock older than an hour as left behind by a run that died.
  held="$(wp option get auto_updater.lock 2>/dev/null || true)"
  [[ "$held" =~ ^[0-9]+$ ]] && [ $(( $(date +%s) - held )) -gt 3600 ] && wp option delete auto_updater.lock >/dev/null && continue
  sleep 5
  [ "$i" = 120 ] && { echo "WordPress' background updater still holds auto_updater.lock after 10 minutes"; exit 1; }
done
trap 'wp option delete auto_updater.lock >/dev/null 2>&1 || true' EXIT

if ! wp core is-installed 2>/dev/null; then
  log "Installing WordPress"
  # Without WP_ADMIN_PASSWORD (production) the admin gets a random password that is never printed or stored:
  # set your own with "Am uitat parola" on wp-login.php, or over SSH (see deploy/README.md).
  wp core install --url="${APP_URL}" --title="${WP_TITLE:-ROCERT}" \
    --admin_user="${WP_ADMIN_USER}" --admin_password="${WP_ADMIN_PASSWORD:-$(openssl rand -base64 33)}" \
    --admin_email="${WP_ADMIN_EMAIL}" --skip-email >/dev/null
fi

log "Language"
wp language core install "${WP_LOCALE:-ro_RO}" --activate >/dev/null
wp language core install en_GB >/dev/null || true

log "Theme"
current="$(installed_version theme ollie)"
if needs_install "$current" "$OLLIE_VERSION"; then
  safe_install theme ollie "$OLLIE_VERSION"
fi
wp theme activate rocert
for t in $(wp theme list --field=name); do
  case "$t" in ollie|rocert) ;; *) wp theme delete "$t" ;; esac
done

log "Plugins"
wp plugin delete akismet hello >/dev/null 2>&1 || true
grep -vE '^\s*(#|$)' "$DEPLOY/plugins.txt" | while read -r slug version; do
  current="$(installed_version plugin "$slug")"
  if needs_install "$current" "$version"; then
    safe_install plugin "$slug" "$version"
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

log "Content migrations"
wp eval-file "$CONTENT/migrate.php"

log "SEO: indexables + llms.txt"
wp eval-file "$CONTENT/post-seed.php"

wp rewrite flush --hard >/dev/null
wp cache flush >/dev/null
log "Done: ${APP_URL}"
