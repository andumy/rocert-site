<?php
/** @var array $attributes */
$a = $attributes;
$cards = '';
foreach (array_slice((array) $a['cards'], 0, 2) as $i => $c) {
    $valid = ($c['status'] ?? 'valid') === 'valid';
    $cards .= sprintf(
        '<div class="rc-float-card%s"><p class="rc-float-card__top %s">● %s</p><p class="rc-float-card__title">%s</p><p class="rc-float-card__meta">%s</p></div>',
        $i === 1 ? ' rc-float-card--dark' : '', $valid ? 'is-valid' : 'is-invalid',
        esc_html(mb_strtoupper(rocert_t($valid ? 'valid' : 'invalid'))), esc_html($c['title'] ?? ''), esc_html($c['meta'] ?? '')
    );
}
?>
<section class="rc-verifyband rc-blue rc-split rc-split--media-right">
	<div class="rc-split__content">
		<h2 class="wp-block-heading rc-h2"><?php echo esc_html($a['title']); ?></h2>
		<?php if ($a['lead']) : ?><p class="rc-lead"><?php echo esc_html($a['lead']); ?></p><?php endif; ?>
		<?php echo rocert_verify_form_html('band'); ?>
	</div>
	<div class="rc-split__media"><div class="rc-verifyband__deco" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.22)" stroke-width=".6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3 4 6v6c0 5 3.4 8.3 8 9 4.6-.7 8-4 8-9V6l-8-3z"/><path d="m9 12 2 2 4-4"/></svg><?php echo $cards; ?></div></div>
</section>
