<?php
/** @var array $attributes */
$id = get_the_ID();
$img = (int) $attributes['image'];
$title = $attributes['title'] ?: get_the_title($id);
?>
<section class="rc-cathero">
	<figure class="rc-cathero__bg"><?php echo rocert_img($img, 'full', ['sizes' => '100vw', 'loading' => 'eager', 'alt' => rocert_block_alt($attributes, $img)]); ?></figure>
	<div class="rc-wrap rc-cathero__content rc-on-image">
		<?php echo rocert_breadcrumbs_html(); ?>
		<h1 class="wp-block-heading rc-display-xl"><?php echo esc_html($title); ?></h1>
		<?php if (!empty($attributes['lead'])) : ?><p class="rc-cathero__lead"><?php echo esc_html($attributes['lead']); ?></p><?php endif; ?>
		<?php echo rocert_category_chips_html((int) $id); ?>
	</div>
</section>
