<?php
/** @var array $attributes @var string $content */
rocert_enqueue_front();
$a = $attributes;
printf(
    '<div class="rc-carousel%s" data-rc-carousel data-autoplay="%s" data-interval="%d"><div class="rc-hscroll" tabindex="0" role="region" aria-roledescription="carousel" aria-label="%s">%s</div><div class="rc-carousel__nav rc-wrap"><button type="button" class="rc-carousel__btn" data-dir="-1" aria-label="%s">%s</button><button type="button" class="rc-carousel__btn" data-dir="1" aria-label="%s">%s</button></div></div>',
    !empty($a['bigNumbers']) ? ' rc-carousel--steps' : '',
    !empty($a['autoplay']) ? '1' : '0',
    max(2, (int) ($a['interval'] ?: 5)),
    esc_attr(rocert_t('carousel')),
    $content,
    esc_attr(rocert_t('prev')), rocert_svg('arrow-left', 20),
    esc_attr(rocert_t('next')), rocert_svg('arrow', 20)
);
