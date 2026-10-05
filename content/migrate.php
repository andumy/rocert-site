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

$migrations = [
    '2026-10-06-verify-legend-invalid' => static fn () => rocert_migrate_replace(
        ['verifica-certificat', 'verify-certificate'],
        ['"title":"Nevalid"' => '"title":"Invalid"', '"title":"Not valid"' => '"title":"Invalid"']
    ),
];

$done = (array) get_option('rocert_migrations', []);
foreach ($migrations as $key => $migration) {
    if (in_array($key, $done, true)) {
        continue;
    }
    WP_CLI::log("Migration {$key}");
    $migration();
    $done[] = $key;
    update_option('rocert_migrations', $done, false);
}
