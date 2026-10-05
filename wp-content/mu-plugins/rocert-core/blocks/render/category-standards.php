<?php
/** @var array $attributes */
$a = $attributes;
$stds = rocert_standard_pages((int) get_the_ID());
if (!$stds) {
    return;
}
$details = static fn (string $code) => sprintf(rocert_t('details_for'), $code);
$request = rocert_page_by_key('request');
$request_url = $request ? get_permalink($request) : '';
$featured = array_shift($stds);
$code = rocert_meta($featured->ID, 'code');
$who = '';
foreach (array_slice((array) $a['who'], 0, 6) as $w) {
    if (!empty($w['text'])) {
        $who .= '<li>' . esc_html($w['text']) . '</li>';
    }
}
$buttons = static function (WP_Post $p, string $code, bool $dark) use ($details, $request_url): string {
    $request_url = $request_url ? add_query_arg('standard', rocert_meta($p->ID, 'key'), $request_url) : '';
    return sprintf(
        '<div class="wp-block-buttons"><div class="wp-block-button%s"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div>%s</div>',
        $dark ? '' : ' is-style-outline', esc_url(get_permalink($p)), esc_html($details($code)),
        $request_url ? sprintf('<div class="wp-block-button%s"><a class="wp-block-button__link wp-element-button" href="%s">%s</a></div>', $dark ? ' is-style-outline' : '', esc_url($request_url), esc_html(rocert_t('request_quote'))) : ''
    );
};
?>
<?php if (!empty($a['heading'])) : ?><div class="rc-wrap rc-catstd-head"><h2 class="wp-block-heading rc-h2-s"><?php echo esc_html($a['heading']); ?></h2></div><?php endif; ?>
<section id="<?php echo esc_attr($featured->post_name); ?>" class="rc-split rc-featured rc-section rc-section--top0">
	<div class="rc-featured__panel rc-dark rc-grid-bg">
		<p class="rc-outline-num" aria-hidden="true"><?php echo esc_html(rocert_meta($featured->ID, 'outline') ?: $code); ?></p>
		<div class="rc-featured__inner">
			<p class="rc-badge"><?php echo esc_html(rocert_meta($featured->ID, 'edition') . ($stds && $a['popular'] ? ' · ' . $a['popular'] : '')); ?></p>
			<h2 class="wp-block-heading rc-h2"><?php echo esc_html($code); ?></h2>
			<p class="rc-subtitle"><?php echo esc_html(rocert_meta($featured->ID, 'name')); ?></p>
			<p class="rc-lead"><?php echo esc_html(rocert_meta($featured->ID, 'lead')); ?></p>
			<?php echo $buttons($featured, $code, true); ?>
		</div>
	</div>
	<?php if ($who) : ?><div class="rc-featured__side"><p class="rc-eyebrow"><?php echo esc_html($a['whoLabel']); ?></p><ul class="rc-biglist"><?php echo $who; ?></ul></div><?php endif; ?>
</section>
<?php if ($stds) : ?>
<section class="rc-section rc-section--top0"><div class="rc-wrap"><div class="rc-stdlist">
	<?php foreach ($stds as $s) :
        $s_code = rocert_meta($s->ID, 'code');
        $s_who = json_decode(rocert_meta($s->ID, 'who') ?: '[]', true) ?: []; ?>
	<article id="<?php echo esc_attr($s->post_name); ?>" class="rc-stdarticle">
		<div><p class="rc-badge on-light"><?php echo esc_html(rocert_meta($s->ID, 'edition')); ?></p><h2 class="wp-block-heading"><?php echo esc_html($s_code); ?></h2><p class="rc-stdarticle__name"><?php echo esc_html(rocert_meta($s->ID, 'name')); ?></p></div>
		<div class="rc-stdarticle__body">
			<p><?php echo esc_html(rocert_meta($s->ID, 'lead')); ?></p>
			<?php if ($s_who) : ?><ul class="rc-chips rc-chips--outline"><?php foreach (array_slice($s_who, 0, 5) as $w) { echo '<li>' . esc_html($w) . '</li>'; } ?></ul><?php endif; ?>
			<?php echo $buttons($s, $s_code, false); ?>
		</div>
	</article>
	<?php endforeach; ?>
</div></div></section>
<?php endif;
