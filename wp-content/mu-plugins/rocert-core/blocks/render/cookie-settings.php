<?php
/** @var array $attributes */
printf('<button type="button" class="rc-cookie-settings" data-cc="show-preferencesModal">%s</button>', esc_html($attributes['label'] ?: rocert_t('cookie_settings')));
