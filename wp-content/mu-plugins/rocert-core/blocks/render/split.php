<?php
/** @var array $attributes @var string $content */
$a = $attributes;
$right = ($a['side'] ?? 'left') === 'right';
$badge = $a['badgeValue'] !== '' ? sprintf('<div class="rc-float-badge"><strong>%s</strong><span>%s</span></div>', esc_html($a['badgeValue']), esc_html($a['badgeText'])) : '';
$media = sprintf('<div class="rc-split__media rc-rounded%s">%s%s</div>', $badge ? ' rc-split__media--badge' : '', rocert_img((int) $a['image'], 'large', ['sizes' => '(max-width: 860px) 100vw, 50vw', 'alt' => rocert_block_alt($a, (int) $a['image'])]), $badge);
$body = '<div class="rc-split__content rc-stack">' . $content . '</div>';
$classes = ['rc-split', 'rc-split--media-' . ($right ? 'right' : 'left'), 'rc-section', rocert_bg_class($a['bg'] ?? ''), !empty($a['tall']) ? 'rc-split--tall' : ''];
printf('<section class="%s">%s</section>', esc_attr(implode(' ', array_filter($classes))), $right ? $body . $media : $media . $body);
