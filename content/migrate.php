<?php
/**
 * One-off content fixes for sites that were already seeded (the seed runs only once).
 * Each migration runs once per site, tracked in the `rocert_migrations` option. Run by provision.sh.
 * Append new migrations at the end; never rename or reorder existing keys.
 */
if (!defined('WP_CLI')) {
    return;
}

/** Literal replacements in the content of the pages with these slugs. */
function rocert_migrate_replace(array $slugs, array $replacements): void
{
    foreach ($slugs as $slug) {
        $page = get_page_by_path($slug);
        if (!$page) {
            continue;
        }
        $content = strtr($page->post_content, $replacements);
        if ($content !== $page->post_content) {
            wp_update_post(['ID' => $page->ID, 'post_content' => wp_slash($content)]);
            WP_CLI::log("  updated /{$slug}/");
        }
    }
}

/**
 * Edit block attributes on pages: $fn receives each block (recursively) by reference and returns true when it
 * changed it. $slugs = null means every page.
 */
function rocert_migrate_blocks(?array $slugs, callable $fn): void
{
    $pages = $slugs === null
        ? get_posts(['post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1])
        : array_filter(array_map('get_page_by_path', $slugs));
    $walk = static function (array &$blocks) use (&$walk, $fn): bool {
        $changed = false;
        foreach ($blocks as &$block) {
            $changed = $fn($block) || $changed;
            if (!empty($block['innerBlocks'])) {
                $changed = $walk($block['innerBlocks']) || $changed;
            }
        }
        return $changed;
    };
    foreach ($pages as $page) {
        $blocks = parse_blocks($page->post_content);
        if ($walk($blocks)) {
            wp_update_post(['ID' => $page->ID, 'post_content' => wp_slash(serialize_blocks($blocks))]);
            WP_CLI::log("  updated /{$page->post_name}/");
        }
    }
}

/** Whether a block or anything inside it contains $needle in its saved HTML. */
function rocert_block_contains(array $block, string $needle): bool
{
    return str_contains(serialize_block($block), $needle);
}

$migrations = [
    '2026-10-06-verify-legend-invalid' => static fn () => rocert_migrate_replace(
        ['verifica-certificat', 'verify-certificate'],
        ['"title":"Nevalid"' => '"title":"Invalid"', '"title":"Not valid"' => '"title":"Invalid"']
    ),
    /* Mobile: no image under the "already certified?" and "want your own certificate?" CTAs */
    '2026-10-06-split-hide-media-mobile' => static fn () => rocert_migrate_blocks(null, static function (array &$b): bool {
        if ($b['blockName'] !== 'rocert/split' || !empty($b['attrs']['hideMediaMobile'])) {
            return false;
        }
        if (!rocert_block_contains($b, 'Aveți deja un certificat?') && !rocert_block_contains($b, 'Already certified?')
            && !rocert_block_contains($b, 'unde găsesc seria?') && !rocert_block_contains($b, 'where is the serial')) {
            return false;
        }
        $b['attrs']['hideMediaMobile'] = true;
        return true;
    }),
    /* Home stats: the [NR] placeholder and the founding year become clients and years of experience */
    '2026-10-06-home-stats' => static fn () => rocert_migrate_blocks(['acasa', 'home'], static function (array &$b): bool {
        if ($b['blockName'] !== 'rocert/statement' || empty($b['attrs']['stats'])) {
            return false;
        }
        $en = str_contains(serialize_block($b), 'the year ROCERT was founded') || str_contains(serialize_block($b), 'active certificates in our register');
        $changed = false;
        foreach ($b['attrs']['stats'] as &$stat) {
            if ($stat['value'] === '1997') {
                $stat = ['value' => '29', 'label' => $en ? 'years of experience' : 'ani de experiență'];
                $changed = true;
            } elseif ($stat['value'] === '[NR]') {
                $stat = ['value' => '+2000', 'label' => $en ? 'certified clients' : 'clienți certificați'];
                $changed = true;
            }
        }
        return $changed;
    }),
    /* Request form v2: site/job-role rows, three shifts, total staff, process types, split names, signatures */
    '2026-10-06-request-form-v2' => static function (): void {
        require_once __DIR__ . '/lib/urls.php';
        require_once __DIR__ . '/lib/forms.php';
        foreach (ff_save_request_forms() as $key => $id) {
            WP_CLI::log("  form {$key} → {$id}");
        }
    },
    /* Cookie policy: the request-form draft in local storage, and the cookie-settings icon */
    '2026-10-07-cookie-policy-draft-and-icon' => static fn () => rocert_migrate_replace(['politica-cookie-uri', 'cookie-policy'], [
        '<td>sesiunea de administrare</td><td>Necesare</td></tr>' => '<td>sesiunea de administrare</td><td>Necesare</td></tr><tr><td>rocert-request-draft:* (stocare locală)</td><td>rocert.ro</td><td>Ciorna cererii de certificare, păstrată doar în browserul tău, pe acest dispozitiv, până la trimitere</td><td>până la trimitere, max. 30 de zile</td><td>Necesare</td></tr>',
        '<td>administration session</td><td>Necessary</td></tr>' => '<td>administration session</td><td>Necessary</td></tr><tr><td>rocert-request-draft:* (local storage)</td><td>rocert.ro</td><td>Draft of the certification request, kept only in your browser on this device until it is sent</td><td>until sent, max. 30 days</td><td>Necessary</td></tr>',
        'consimțământul din linkul <strong>„Setări cookie-uri”</strong> din subsolul fiecărei pagini.' => 'consimțământul din pictograma cu scut din colțul din stânga jos sau din linkul <strong>„Setări cookie-uri”</strong> din subsolul fiecărei pagini.',
        'via the <strong>"Cookie settings"</strong> link in the footer of every page.' => 'via the shield icon in the bottom-left corner or the <strong>"Cookie settings"</strong> link in the footer of every page.',
    ]),
    /* Request form: fax for the organisation and the contact person, EA sectors required */
    '2026-10-06-request-form-v3' => static function (): void {
        require_once __DIR__ . '/lib/urls.php';
        require_once __DIR__ . '/lib/forms.php';
        ff_save_request_forms();
    },
];

$done = (array) get_option('rocert_migrations', []);
foreach ($migrations as $key => $migration) {
    if (in_array($key, $done, true)) {
        continue;
    }
    WP_CLI::log("Migration {$key}");
    try {
        $migration();
    } catch (Throwable $e) {
        /* Not marked done, and the deploy fails loudly: it runs again on the next deploy once fixed */
        WP_CLI::error("Migration {$key} failed: " . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')');
    }
    $done[] = $key;
    update_option('rocert_migrations', $done, false);
}
