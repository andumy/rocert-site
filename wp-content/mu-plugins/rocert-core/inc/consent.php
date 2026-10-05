<?php
defined('ABSPATH') || exit;

/* Google Consent Mode v2 defaults: everything denied until the visitor accepts analytics. Must precede any gtag. */
add_action('wp_head', static function (): void {
    ?>
<script>
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
gtag('consent','default',{ad_storage:'denied',ad_user_data:'denied',ad_personalization:'denied',analytics_storage:'denied',functionality_storage:'granted',security_storage:'granted',wait_for_update:500});
(function(){try{var m=document.cookie.match(/(?:^|; )cc_cookie=([^;]*)/);if(m&&JSON.parse(decodeURIComponent(m[1])).categories.indexOf('analytics')>-1){gtag('consent','update',{analytics_storage:'granted'});}}catch(e){}})();
</script>
    <?php
}, 0);

add_action('wp_enqueue_scripts', static function (): void {
    $base = ROCERT_CORE_URL . '/assets/';
    wp_enqueue_style('rocert-cookieconsent', $base . 'vendor/cookieconsent/cookieconsent.css', [], '3.1.0', 'print');
    wp_style_add_data('rocert-cookieconsent', 'onload', true);
    wp_enqueue_script('rocert-cookieconsent', $base . 'vendor/cookieconsent/cookieconsent.umd.js', [], '3.1.0', ['strategy' => 'defer', 'in_footer' => true]);
    wp_enqueue_script('rocert-consent', $base . 'js/consent.js', ['rocert-cookieconsent'], ROCERT_CORE_VERSION . '.' . filemtime(ROCERT_CORE_DIR . '/assets/js/consent.js'), ['strategy' => 'defer', 'in_footer' => true]);
    wp_localize_script('rocert-consent', 'rocertConsent', [
        'lang' => rocert_lang(),
        'privacy' => rocert_lang() === 'en' ? home_url('/en/privacy-policy/') : home_url('/politica-de-confidentialitate/'),
        'cookies' => rocert_lang() === 'en' ? home_url('/en/cookie-policy/') : home_url('/politica-cookie-uri/'),
    ]);
});

/* Non-blocking cookie banner CSS: media=print, switched to all on load. */
add_filter('style_loader_tag', static function (string $tag, string $handle): string {
    if ($handle === 'rocert-cookieconsent') {
        $tag = str_replace("media='print'", "media='print' onload=\"this.media='all'\"", $tag);
    }
    return $tag;
}, 10, 2);
