# Deploy

There is one environment on the server: **production**, at `/var/www/rocertsite`. Until launch it sits
behind a password wall (HTTP basic auth), so there is no separate stage to keep in sync.

## How a deploy runs

`.github/workflows/prod-deploy.yml` (same pattern as rocert / RespireBien), on a **published GitHub release**
or manually (*Run workflow*):

1. runs on the self-hosted runner labelled `rocert-site` — start it with `make start-runner` (uses `../gh-runners`);
2. builds `.env` from `deploy/.env.static` + the Infisical **`prod`** environment;
3. rsyncs the repo to `/var/www/rocertsite` — never `wordpress/` (core, plugins, uploads stay on the server);
4. runs `make start-prod` there: rebuilds the container (`docker-compose.prod.yml`, bound to `127.0.0.1:$APP_PORT`)
   and runs `provision.sh` (idempotent; seeds content only the very first time).

GitHub repository secrets: `SSH_PRIVATE_KEY`, `SSH_USER` (andumy), `REMOTE_SERVER`,
`INFISICAL_ID`, `INFISICAL_SECRET`. Set `PROJECT_ID` in the workflow to the Infisical project id.

The host's nginx (same vhost pattern as grouptherapy, TLS via `certbot --nginx`) forwards the domain to `127.0.0.1:$APP_PORT` (8090) and passes `X-Forwarded-For` / `X-Forwarded-Proto`; Apache restores the visitor's IP from `X-Forwarded-For`, trusting only private addresses and Cloudflare's ranges.

## Password wall (pre-launch)

`BASIC_AUTH_USER` + `BASIC_AUTH_PASSWORD` in Infisical `prod` → every page asks for credentials
(`/wp-cron.php` stays open so WordPress' scheduled jobs keep running). **To go live: delete both variables
and redeploy.** Nothing else changes.

While the wall is up, keep Cloudflare from caching HTML (Super Page Cache not connected / no HTML cache rule),
so a cached page can never be served to someone who did not log in.

## Going live

1. Delete `BASIC_AUTH_USER` / `BASIC_AUTH_PASSWORD`, redeploy.
2. If the domain changes (e.g. `rocert-site.andumy.ro` → `rocert.ro`): update `APP_URL` in Infisical, redeploy, then
   `docker exec -u rocert rocert-site-app wp search-replace 'https://rocert-site.andumy.ro' 'https://rocert.ro' --all-tables --skip-columns=guid`
   (page content uses relative links; this catches media URLs and plugin settings).
3. Cloudflare: SSL Full (strict), Always Use HTTPS, HTTP/3, Brotli, Early Hints; Rocket Loader **off**.
   Connect Super Page Cache with an API token (Zone: Cache Purge + Cache Rules edit) to cache HTML at the edge.
4. Site Kit: connect GA4 + Search Console, submit `/sitemap_index.xml`. Wordfence: run *Optimize the firewall*.
5. 301 redirects from the old site's `.htm` pages.

## Hand-over package for an FTP-only host (only if ever needed)

`PROD_URL=https://rocert.ro make export` → `build/rocert-prod/` with the full WordPress tree, a search-replaced
`database.sql` and a standalone `wp-config.php` to fill in. Upload by FTP and import the SQL in phpMyAdmin.
