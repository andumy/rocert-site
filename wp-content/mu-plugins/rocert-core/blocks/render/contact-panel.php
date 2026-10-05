<?php
/** @var array $attributes */
$a = $attributes;
$tel = preg_replace('/[^\d+]/', '', $a['phone']);
$points = '';
foreach (array_slice((array) $a['points'], 0, 4) as $p) {
    if (!empty($p['text'])) {
        $points .= '<li>' . esc_html($p['text']) . '</li>';
    }
}
?>
<section class="rc-contact rc-split">
	<div class="rc-contact__info rc-grid-bg">
		<div class="rc-contact__info-inner">
			<div><?php if ($a['eyebrow']) : ?><p class="rc-eyebrow"><?php echo esc_html($a['eyebrow']); ?></p><?php endif; ?><h2 class="wp-block-heading rc-h2-s"><?php echo esc_html($a['title']); ?></h2></div>
			<p class="rc-contact__big"><a href="tel:<?php echo esc_attr($tel); ?>"><?php echo esc_html($a['phone']); ?></a><br><a class="rc-accent-link" href="mailto:<?php echo esc_attr($a['email']); ?>"><?php echo esc_html($a['email']); ?></a></p>
			<?php if ($a['address']) : ?><p class="rc-contact__addr"><?php echo nl2br(esc_html($a['address'])); ?></p><?php endif; ?>
		</div>
	</div>
	<div class="rc-contact__cta">
		<?php if ($a['ctaEyebrow']) : ?><p class="rc-eyebrow"><?php echo esc_html($a['ctaEyebrow']); ?></p><?php endif; ?>
		<h2 class="wp-block-heading rc-h2-s"><?php echo esc_html($a['ctaTitle']); ?></h2>
		<?php if ($a['ctaText']) : ?><p class="rc-lead"><?php echo esc_html($a['ctaText']); ?></p><?php endif; ?>
		<?php if ($points) : ?><ul class="rc-checklist"><?php echo $points; ?></ul><?php endif; ?>
		<div class="wp-block-buttons">
			<?php if ($a['ctaButton'] && $a['ctaUrl']) : ?><div class="wp-block-button rc-btn-lg"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($a['ctaUrl']); ?>"><?php echo esc_html($a['ctaButton']); ?></a></div><?php endif; ?>
			<?php if ($a['secondaryText'] && $a['secondaryUrl']) : ?><div class="wp-block-button is-style-outline rc-btn-lg"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url($a['secondaryUrl']); ?>"><?php echo esc_html($a['secondaryText']); ?></a></div><?php endif; ?>
		</div>
	</div>
</section>
