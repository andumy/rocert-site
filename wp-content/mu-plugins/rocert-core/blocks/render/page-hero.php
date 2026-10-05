<?php
/** @var array $attributes */
$a = $attributes;
$title = nl2br(esc_html($a['title'])) . (!empty($a['dot']) ? '<span class="rc-dot">.</span>' : '');
$stats = '';
foreach (array_slice((array) $a['stats'], 0, 3) as $s) {
    if (!empty($s['value'])) {
        $stats .= sprintf('<div><strong>%s</strong><span>%s</span></div>', esc_html($s['value']), esc_html($s['label'] ?? ''));
    }
}
$stats = $stats ? '<div class="rc-hero-stats">' . $stats . '</div>' : '';
$buttons = rocert_buttons_html((array) $a['buttons'], 'rc-btn-lg');
$crumbs = rocert_breadcrumbs_html();

if (($a['variant'] ?? 'dark') === 'light') : ?>
<section class="rc-hubhero">
	<div class="rc-wrap"><?php echo $crumbs; ?>
		<div class="rc-hubhero__row">
			<h1 class="wp-block-heading rc-display-xl"><?php echo $title; ?></h1>
			<div class="rc-hubhero__aside"><?php if ($a['lead']) : ?><p class="rc-lead"><?php echo esc_html($a['lead']); ?></p><?php endif; ?><?php echo $buttons; ?></div>
		</div>
	</div>
</section>
<?php else : ?>
<section class="rc-pagehero rc-dark rc-grid-bg<?php echo !empty($a['overlap']) ? ' rc-pagehero--overlap' : ''; ?>">
	<?php if ($a['deco'] === 'shield') : ?><svg class="rc-verify-shield" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="#2F2F2F" stroke-width=".5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6l-8-3z"/><path d="m9 12 2 2 4-4"/></svg><?php endif; ?>
	<?php if ($a['outline']) : ?><p class="rc-pagehero__outline" aria-hidden="true"><?php echo esc_html($a['outline']); ?></p><?php endif; ?>
	<div class="rc-wrap"><?php echo $crumbs; ?>
		<?php if ($a['badge']) : ?><span class="rc-badge"><?php echo esc_html($a['badge']); ?></span><?php endif; ?>
		<h1 class="wp-block-heading rc-display"><?php echo $title; ?></h1>
		<?php if ($a['subtitle']) : ?><div class="rc-subtitle"><?php echo esc_html($a['subtitle']); ?></div><?php endif; ?>
		<?php if ($a['lead']) : ?><p class="rc-lead"><?php echo esc_html($a['lead']); ?></p><?php endif; ?>
		<?php echo $stats . $buttons; ?>
	</div>
</section>
<?php endif;
