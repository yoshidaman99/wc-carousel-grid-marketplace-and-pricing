<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

class Shortcodes
{
    private static $instance = null;

    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    private function init(): void
    {
        add_shortcode('wc_cgmp_category_icon', [$this, 'category_icon_shortcode']);
    }

    public function category_icon_shortcode($atts): string
    {
        $atts = shortcode_atts([
            'category' => '',
            'id' => 0,
            'size' => 24,
            'class' => '',
            'link' => 'false',
            'return' => 'html',
        ], $atts, 'wc_cgmp_category_icon');

        $category_identifier = $atts['category'];
        $category_id = (int) $atts['id'];

        if (empty($category_identifier) && empty($category_id)) {
            return '';
        }

        if (empty($category_id) && !empty($category_identifier)) {
            if (is_numeric($category_identifier)) {
                $category_id = (int) $category_identifier;
            } else {
                $term = get_term_by('slug', $category_identifier, 'product_cat');
                if ($term && !is_wp_error($term)) {
                    $category_id = $term->term_id;
                }
            }
        }

        if (empty($category_id)) {
            return '';
        }

        $category_icon = \WC_CGMP\Frontend\Category_Icon::get_instance();
        
        $args = [
            'size' => (int) $atts['size'],
            'class' => sanitize_html_class($atts['class']),
            'link' => $atts['link'] === 'true',
        ];

        $html = $category_icon->render($category_id, $args);

        if ($atts['return'] === 'url') {
                $url = $category_icon->get_url($category_id);
                return $url ?: $html;
            }

        return $html;
    }
}
