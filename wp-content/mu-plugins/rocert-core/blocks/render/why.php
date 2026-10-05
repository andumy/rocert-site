<?php
/** @var array $attributes */
$a = $attributes;
$img = (int) $a['image'];
$cards = '';
foreach (array_slice((array) $a['cards'], 0, 4) as $i => $c) {
    $cards .= sprintf('<div class="rc-why-card"><p class="rc-card-n">%02d</p><h3 class="wp-block-heading">%s</h3><p>%s</p></div>', $i + 1, esc_html($c['title'] ?? ''), esc_html($c['text'] ?? ''));
}
?>
<section class="rc-why">
	<figure class="rc-why__image"><?php echo rocert_img($img, 'full', ['sizes' => '100vw', 'alt' => rocert_block_alt($a, $img)]); ?></figure>
	<div class="rc-why__overlay"><div class="rc-wrap"><?php if ($a['eyebrow']) : ?><p class="rc-eyebrow"><?php echo esc_html($a['eyebrow']); ?></p><?php endif; ?><h2 class="wp-block-heading"><?php echo esc_html($a['title']); ?></h2></div></div>
	<div class="rc-wrap"><div class="rc-why__cards"><?php echo $cards; ?></div></div>
</section>
