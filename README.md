# ROCERT — site WordPress

Public website for ROCERT (rocert.ro). WordPress 7.1 on PHP 8.5, Ollie + a `rocert` child theme, RO (default) + EN via Polylang.

| Environment | URL | How it runs |
|---|---|---|
| local | http://localhost:8090 (Mailpit: http://localhost:8026) | `docker-compose.dev.yml`: app + MySQL 8.4 + Mailpit |
| production | https://rocert-site.andumy.ro (later rocert.ro) | `docker-compose.prod.yml` on our server behind Cloudflare; deployed on a published GitHub release; password wall until launch — see [deploy/README.md](deploy/README.md) |

## Local

```bash
make start      # .env from deploy/.env.static + deploy/.env.local, build, boot, provision, seed (first run)
make bash       # shell in the app container (WP-CLI: `wp …`)
make reseed     # re-apply seed content over existing pages — overwrites wp-admin edits
make down       # destroy the local DB and the downloaded WordPress tree
```

Admin: http://localhost:8090/wp-admin — `admin` / `admin` (local only).

## How it is put together

```
rocert-site/
├─ wordpress/                      ← NOT in git. WordPress core, created by the Docker image on first boot
│  └─ wp-content/
│     ├─ plugins/                  ← third-party plugins, installed by provision.sh (pinned in deploy/plugins.txt)
│     ├─ uploads/                  ← media
│     ├─ themes/ollie/             ← parent theme, installed by provision.sh
│     ├─ themes/rocert/            ← (mount point) ─┐ the folders below are bind-mounted here by docker-compose,
│     └─ mu-plugins/               ← (mount point) ─┘ so on the host they look empty, inside the container they are ours
├─ wp-content/themes/rocert/       ← child theme of Ollie (style.css: "Template: ollie")
├─ wp-content/mu-plugins/          ← our code ("must-use": always loaded, cannot be deactivated from wp-admin)
│  ├─ rocert-core.php              ← loader
│  └─ rocert-core/
│     ├─ blocks/registry.php       ← the ROCERT block library: one schema per block (fields + editor controls)
│     ├─ blocks/editor.js          ← generic editor UI built from that schema (no build step)
│     ├─ blocks/render/*.php       ← one server-side template per block
│     ├─ inc/                      ← components, REST endpoints, forms glue, consent, SEO, performance, security
│     └─ assets/                   ← front-end JS (carousel, verify, ANAF autofill), CookieConsent
├─ content/                        ← initial seed: page builders + RO/EN copy + media (see manifest.json)
├─ deploy/                         ← provision.sh, configure.php, env templates, prod export
└─ docker/                         ← Dockerfile, PHP/Apache config
```

- **WordPress core is never committed.** The official image copies it into `./wordpress/` on first boot.
- **Why mu-plugins and not plugins:** `plugins/` holds third-party code we install and update, but don't own. Our site behaviour sits in `mu-plugins/` because must-use plugins load automatically and cannot be switched off from wp-admin, so the client cannot break the site by deactivating "a plugin".
- **Pages are made of blocks, never raw HTML.** Every custom part of the design is a ROCERT block (category *ROCERT* in the inserter): editors change its content and options in the sidebar, or inline for cards and headings, and the markup lives in `blocks/render/*.php`, versioned in git. Container blocks (Section, Image + content, Carousel, FAQ, Two-column intro) hold normal paragraphs, headings, lists and buttons. Dynamic blocks (category index, zig-zag, standards table, category standards, related) build themselves from the page tree.
- **Content is code only for the first seed.** After that, the database is the source of truth.
- **Plugins** (pinned in `deploy/plugins.txt`, auto-updates on except Polylang): Wordfence (stage/prod only), Yoast SEO, WP Mail SMTP, Site Kit, Polylang, Super Page Cache, Modern Image Formats, Fluent Forms.

### Adding a block

1. Add its schema to `rocert_block_schema()` in `blocks/registry.php` (fields: text, textarea, url, number, toggle, select, image, page, form, repeater).
2. Add `blocks/render/<name>.php` (receives `$attributes`, and `$content` for containers).
3. Style it in the theme's `style.css`. The editor picks it up automatically.

## Integration hooks (rocert API)

All in `wp-content/mu-plugins/rocert-core/inc/hooks.php`:

```php
// Every certification-request submission, after the notification e-mail. Already sends it to the rocert API (below).
add_action('rocert/form/submitted', function (string $form, array $data, int $entry_id, string $lang) { … }, 10, 4);

// Certificate lookup by serial (the UUID printed on the certificate), for /verifica-certificat/.
// Return array{valid, organization, standard, scope}, false (not found) or null/WP_Error (unavailable).
add_filter('rocert/certificate/lookup', function ($result, string $serial) { /* call API */ }, 10, 2);

// Company data by CUI (ANAF). Already forwards to {ROCERT_API_URL}/api/public/companies/{cui} when ROCERT_API_URL is set.
add_filter('rocert/company/lookup', function ($company, string $cui) { … }, 10, 2);
```

Expected rocert API contract (set `ROCERT_API_URL`, optionally `ROCERT_API_TOKEN` for a Bearer token):

| Call | Response |
|---|---|
| `GET /api/public/certificates/{series}` | 200 `CertificateLookup::toArray()` — `series, client, valid, reason, reason_label, standard, number` (+ `scope` if added); 404 when unknown |
| `GET /api/public/companies/{cui}` | 200 `{name, address, city, county, postal_code, reg_com, phone, iban}` (or wrapped in `data`); 404 when unknown |
| `POST /api/site/clients` (`Authorization: Bearer ROCERT_API_TOKEN`) | body `{source, form, language, entry_id, submitted_at, data: {…form fields}}` → 201/200; creates a prospect client. Failures are queued in the `rocert_api_outbox` option and retried hourly (48 tries); Fluent Forms keeps every entry anyway |

`ROCERT_API_TOKEN` (site, Infisical) must equal `SITE_API_KEY` (rocert API, Infisical).

While `ROCERT_API_URL` is empty and the environment is not production, demo data answers both lookups (try serial `3F2A9C1E-4B7D-4E2A-9C51-7A1D2E3F4B5C` (valid) or `8C1D4E7F-2A3B-4C5D-8E9F-0A1B2C3D4E5F` (not valid), and any CUI).

## Environment variables

Secrets live in Infisical (`prod`); `deploy/.env.local` has throwaway local values. See `docker/wp/config.php` for how each is used:
`APP_ENV`, `APP_URL`, `APP_PORT`, `DB_*`, `WP_ADMIN_*`, `SMTP_*`, `FORM_RECIPIENT`, `ROCERT_API_URL`, `ROCERT_API_TOKEN`, `TURNSTILE_SITE_KEY`, `TURNSTILE_SECRET`, `BASIC_AUTH_USER` / `BASIC_AUTH_PASSWORD` (pre-launch wall), optional `WORDPRESS_*` salts.
