#!/usr/bin/env bash
# Pre-launch password wall: with BASIC_AUTH_USER and BASIC_AUTH_PASSWORD set, the whole site asks for
# credentials (wp-cron.php stays open so WordPress' own scheduled jobs keep running). Unset them to go live.
set -euo pipefail

CONF=/etc/apache2/conf-enabled/zz-rocert-basic-auth.conf
if [ -n "${BASIC_AUTH_USER:-}" ] && [ -n "${BASIC_AUTH_PASSWORD:-}" ]; then
  # Apache checks the hash on every request (assets included), so minimum bcrypt cost: ~1 ms instead of ~250 ms.
  # A slow hash buys nothing here, the plain password already sits in this container's environment.
  php -r 'echo getenv("BASIC_AUTH_USER"), ":", password_hash(getenv("BASIC_AUTH_PASSWORD"), PASSWORD_BCRYPT, ["cost" => 4]), PHP_EOL;' > /etc/apache2/rocert.htpasswd
  chmod 644 /etc/apache2/rocert.htpasswd
  cat > "$CONF" <<'CONF'
<Location />
    AuthType Basic
    AuthName "ROCERT"
    AuthUserFile /etc/apache2/rocert.htpasswd
    <RequireAny>
        Require valid-user
        Require expr %{REQUEST_URI} =~ m#^/wp-cron\.php#
    </RequireAny>
</Location>
CONF
else
  rm -f "$CONF" /etc/apache2/rocert.htpasswd
fi

exec docker-entrypoint.sh "$@"
