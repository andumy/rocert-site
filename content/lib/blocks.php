<?php
/**
 * Serialises core blocks exactly as the block editor saves them, so seeded pages open in the editor
 * without "unexpected content" warnings. Styling is class-driven (rc-* in the child theme).
 */

function rb_attrs(array $attrs): string
{
    $attrs = array_filter($attrs, static fn ($v) => $v !== null && $v !== '' && $v !== []);
    return $attrs ? ' ' . wp_json_encode($attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
}

function rb_class(string $base, string $extra = ''): string
{
    return trim($base . ' ' . $extra);
}

function rb_group(string $inner, string $class = '', string $tag = 'div', array $attrs = []): string
{
    $a = ['tagName' => $tag !== 'div' ? $tag : null, 'anchor' => $attrs['anchor'] ?? null, 'className' => $class ?: null];
    $id = !empty($attrs['anchor']) ? ' id="' . esc_attr($attrs['anchor']) . '"' : '';
    return sprintf("<!-- wp:group%s -->\n<%s%s class=\"%s\">%s</%s>\n<!-- /wp:group -->\n", rb_attrs($a), $tag, $id, rb_class('wp-block-group', $class), $inner, $tag);
}

function rb_heading(string $html, int $level = 2, string $class = ''): string
{
    $a = ['level' => $level !== 2 ? $level : null, 'className' => $class ?: null];
    return sprintf("<!-- wp:heading%s -->\n<h%d class=\"%s\">%s</h%d>\n<!-- /wp:heading -->\n", rb_attrs($a), $level, rb_class('wp-block-heading', $class), $html, $level);
}

function rb_p(string $html, string $class = ''): string
{
    return sprintf("<!-- wp:paragraph%s -->\n<p%s>%s</p>\n<!-- /wp:paragraph -->\n", rb_attrs(['className' => $class ?: null]), $class ? ' class="' . $class . '"' : '', $html);
}

function rb_eyebrow(string $text): string
{
    return rb_p(esc_html($text), 'rc-eyebrow');
}

/** @param array{0:string,1:string,2?:string}[] $buttons [text, url, extra classes] */
function rb_buttons(array $buttons, string $class = ''): string
{
    $inner = '';
    foreach ($buttons as $b) {
        [$text, $url] = $b;
        $cls = $b[2] ?? '';
        $inner .= sprintf(
            "<!-- wp:button%s -->\n<div class=\"%s\"><a class=\"wp-block-button__link wp-element-button\" href=\"%s\">%s</a></div>\n<!-- /wp:button -->\n",
            rb_attrs(['className' => $cls ?: null]), rb_class('wp-block-button', $cls), esc_url($url), esc_html($text)
        );
    }
    return sprintf("<!-- wp:buttons%s -->\n<div class=\"%s\">%s</div>\n<!-- /wp:buttons -->\n", rb_attrs(['className' => $class ?: null]), rb_class('wp-block-buttons', $class), $inner);
}

function rb_image(int $id, string $class = '', string $alt = '', string $size = 'large'): string
{
    $src = wp_get_attachment_image_url($id, $size) ?: '';
    $alt = $alt !== '' ? $alt : (string) get_post_meta($id, '_wp_attachment_image_alt', true);
    return sprintf(
        "<!-- wp:image%s -->\n<figure class=\"%s\"><img src=\"%s\" alt=\"%s\" class=\"wp-image-%d\"/></figure>\n<!-- /wp:image -->\n",
        rb_attrs(['id' => $id, 'sizeSlug' => $size, 'linkDestination' => 'none', 'className' => $class ?: null]),
        rb_class('wp-block-image size-' . $size, $class), esc_url($src), esc_attr($alt), $id
    );
}

/** @param string[] $items inline HTML */
function rb_list(array $items, string $class = '', bool $ordered = false): string
{
    $tag = $ordered ? 'ol' : 'ul';
    $inner = '';
    foreach ($items as $item) {
        $inner .= "<!-- wp:list-item -->\n<li>{$item}</li>\n<!-- /wp:list-item -->\n";
    }
    return sprintf("<!-- wp:list%s -->\n<%s class=\"%s\">%s</%s>\n<!-- /wp:list -->\n", rb_attrs(['ordered' => $ordered ?: null, 'className' => $class ?: null]), $tag, rb_class('wp-block-list', $class), $inner, $tag);
}

function rb_details(string $summary, string $answer): string
{
    return sprintf("<!-- wp:details -->\n<details class=\"wp-block-details\"><summary>%s</summary>%s</details>\n<!-- /wp:details -->\n", esc_html($summary), rb_p($answer));
}

function rb_shortcode(string $code): string
{
    return "<!-- wp:shortcode -->\n{$code}\n<!-- /wp:shortcode -->\n";
}

function rb_html(string $html): string
{
    return "<!-- wp:html -->\n{$html}\n<!-- /wp:html -->\n";
}

function rb_table(array $head, array $rows): string
{
    $th = implode('', array_map(static fn ($h) => '<th>' . $h . '</th>', $head));
    $tb = '';
    foreach ($rows as $r) {
        $tb .= '<tr>' . implode('', array_map(static fn ($c) => '<td>' . $c . '</td>', $r)) . '</tr>';
    }
    return "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table><thead><tr>{$th}</tr></thead><tbody>{$tb}</tbody></table></figure>\n<!-- /wp:table -->\n";
}

function rb_wrap(string $inner, string $extra = ''): string
{
    return rb_group($inner, rb_class('rc-wrap', $extra));
}

/** Shortcode attribute value: shortcodes cannot contain quotes or brackets. */
function rb_sc_attr(string $value): string
{
    return str_replace(['"', '[', ']'], ['&quot;', '&#91;', '&#93;'], $value);
}

/** A ROCERT block: self-closing, or wrapping inner blocks for the container types */
function rb_block(string $name, array $attrs = [], ?string $inner = null): string
{
    $json = $attrs ? ' ' . wp_json_encode($attrs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
    if ($inner === null) {
        return "<!-- wp:rocert/{$name}{$json} /-->\n";
    }
    return "<!-- wp:rocert/{$name}{$json} -->\n{$inner}<!-- /wp:rocert/{$name} -->\n";
}

/** Repeater rows from a list of strings: [['text' => …], …] */
function rb_rows(array $items, string $key = 'text'): array
{
    return array_map(static fn ($i) => [$key => $i], array_values($items));
}
