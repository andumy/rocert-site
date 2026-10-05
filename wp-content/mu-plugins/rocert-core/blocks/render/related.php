<?php
/** @var array $attributes */
$ids = array_map(static fn ($p) => (int) ($p['page'] ?? 0), (array) $attributes['pages']);
echo rocert_related_html($ids, $attributes['ctaText'], $attributes['ctaUrl']);
