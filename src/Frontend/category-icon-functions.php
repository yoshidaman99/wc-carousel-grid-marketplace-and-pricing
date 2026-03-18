<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

function wc_cgmp_get_category_icon(int $term_id = 0, string $slug = ''): ?Category_Icon
{
    static $instance = null;

    if (null === $instance) {
        $instance = new Category_Icon();
    }

    return $instance;
}

function wc_cgmp_category_icon_shortcode($atts): string
{
    return Shortcodes::get_instance()->category_icon_shortcode($atts);
}

function wc_cgmp_render_category_icon(int $term_id, array $args = [], bool $echo = true): string
{
    $icon = wc_cgmp_get_category_icon();
    $html = $icon->render($term_id, $args);
    
    if ($echo) {
        echo $html;
        return '';
    }
    
    return $html;
}

function wc_cgmp_get_category_icon_data(int $term_id): array
{
    $icon = wc_cgmp_get_category_icon();
    return $icon->get_icon($term_id);
}

function wc_cgmp_get_category_icon_url(int $term_id): string
{
    $icon = wc_cgmp_get_category_icon();
    return $icon->get_url($term_id);
}
