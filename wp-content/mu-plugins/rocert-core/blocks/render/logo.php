<?php
/** @var array $attributes */
$a = $attributes;
$w = max(24, (int) ($a['width'] ?: 54));
$file = ($a['variant'] ?? 'white') === 'black' ? 'logo.svg' : 'logo-white.svg';
$img = sprintf('<img src="%s" alt="ROCERT" width="%d" height="%d"%s>', esc_url(get_stylesheet_directory_uri() . '/assets/img/' . $file), $w, (int) round($w * 0.809), $w > 80 ? ' loading="lazy"' : '');
echo !empty($a['link']) ? sprintf('<a class="rc-logo" href="%s" aria-label="%s">%s</a>', esc_url(rocert_home_url()), esc_attr(rocert_t('logo_label')), $img) : '<span class="rc-logo">' . $img . '</span>';
