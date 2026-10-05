<?php
/** @var array $attributes */
$a = $attributes;
$img = (int) $a['image'];
$card = $a['cardTitle'] ? sprintf(
    '<div class="rc-float-card rc-float-card--hero" aria-hidden="true"><div class="rc-float-card__top"><span>%s</span><span class="rc-float-card__status">✓ %s</span></div><p class="rc-float-card__title">%s</p><p class="rc-float-card__meta">%s</p></div>',
    esc_html($a['cardLabel']), esc_html($a['cardStatus']), esc_html($a['cardTitle']), esc_html($a['cardMeta'])
) : '';
?>
<section class="rc-hero rc-dark rc-grid-bg rc-split rc-split--media-right">
	<div class="rc-split__content rc-hero__content">
		<?php if ($a['pill']) : ?><span class="rc-pill"><?php echo esc_html($a['pill']); ?></span><?php endif; ?>
		<h1 class="wp-block-heading rc-hero__title"><?php echo esc_html($a['title']); ?><?php if ($a['titleAccent']) : ?> <span class="rc-accent"><?php echo esc_html($a['titleAccent']); ?></span><?php endif; ?></h1>
		<?php if ($a['lead']) : ?><p class="rc-lead"><?php echo esc_html($a['lead']); ?></p><?php endif; ?>
		<?php echo rocert_verify_form_html('hero', $a['note']); ?>
	</div>
	<div class="rc-split__media rc-hero__media"><?php echo rocert_img($img, 'large', ['loading' => 'eager', 'fetchpriority' => 'low', 'sizes' => '(max-width: 860px) 100vw, 50vw', 'alt' => rocert_block_alt($a, $img)]); ?><?php echo $card; ?></div>
</section>
