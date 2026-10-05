<?php
/**
 * After seeding (and safe to re-run): build Yoast indexables (breadcrumbs, schema, sitemaps).
 * Yoast's own `wp yoast index` refuses to run outside production.
 */
if (!defined('WP_CLI') || !function_exists('YoastSEO')) {
    return;
}

foreach ([
    \Yoast\WP\SEO\Actions\Indexing\Indexable_Post_Indexation_Action::class,
    \Yoast\WP\SEO\Actions\Indexing\Indexable_General_Indexation_Action::class,
    \Yoast\WP\SEO\Actions\Indexing\Indexable_Indexing_Complete_Action::class,
] as $class) {
    $action = YoastSEO()->classes->get($class);
    if (method_exists($action, 'get_total_unindexed')) {
        while ($action->get_total_unindexed() > 0 && $action->index()) {
        }
    } else {
        $action->complete();
    }
}

WP_CLI::success('Yoast indexables built.');
