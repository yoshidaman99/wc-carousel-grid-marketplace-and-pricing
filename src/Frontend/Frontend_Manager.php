<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

class Frontend_Manager
{
    public function __construct()
    {
        add_action('init', [$this, 'register_shortcodes']);
    }

    public function register_shortcodes(): void
    {
        add_shortcode('wc_cgmp_marketplace', [$this, 'render_marketplace_shortcode']);
        add_shortcode('wc_cgm_marketplace', [$this, 'render_marketplace_shortcode']);
    }

    public function render_marketplace_shortcode(array $atts = []): string
    {
        $this->ensure_frontend_assets();

        $defaults = [
            'columns' => (int) get_option('wc_cgmp_grid_columns', 3),
            'columns_tablet' => 2,
            'columns_mobile' => 1,
            'category' => '',
            'exclude_category' => '',
            'products' => '',
            'tier' => '',
            'limit' => (int) get_option('wc_cgmp_cards_per_page', 12),
            'orderby' => 'date',
            'order' => 'DESC',
            'show_sidebar' => get_option('wc_cgmp_show_sidebar', 'true'),
            'show_filter' => get_option('wc_cgmp_show_filter_bar', 'true'),
            'show_search' => 'false',
            'show_tier_description' => get_option('wc_cgmp_show_tier_description', 'true'),
            'show_tier_badge' => get_option('wc_cgmp_show_tier_badge', 'true'),
            'layout' => 'grid',
            'mobile_carousel' => get_option('wc_cgmp_mobile_carousel', 'false'),
            'infinite_scroll' => get_option('wc_cgmp_infinite_scroll', 'false'),
            'marketplace_only' => 'false',
            'popular_only' => 'false',
            'price_display_mode' => 'both',
            'show_price_prefix' => 'false',
            'price_prefix_text' => '',
            'price_prefix_separator' => '|',
            'price_prefix_position' => 'inline',
            'show_popular_badge' => 'true',
            'popular_badge_text' => 'Popular',
            'show_popular_mark' => 'true',
            'popular_mark_text' => '‹popular›',
            'show_headcount' => 'true',
            'show_total' => 'true',
            'enable_button_override' => 'false',
            'override_button_text' => 'Get Quote',
            'override_button_url' => '',
            'include_total_param' => 'true',
            'total_url_param' => 'total',
            'open_in_new_tab' => 'true',
            'enable_above_button_link' => 'false',
            'above_link_icon' => '',
            'above_link_text' => '',
            'above_link_url' => '',
            'above_link_highlight_text' => '',
            'above_link_open_new_tab' => 'true',
            'enable_modal' => 'true',
            'modal_responsibilities_title' => 'Key Responsibilities',
            'modal_responsibilities_icon_html' => wc_cgmp_get_check_icon(),
            'modal_icon_color' => '#dc2626',
            'modal_icon_size' => '16',
            'remove_price_decimals' => 'false',
            'card_desc_limit' => 75,
        ];

        $atts = shortcode_atts($defaults, $atts, 'wc_cgmp_marketplace');

        $args = [
            'category' => $atts['category'],
            'exclude_category' => $atts['exclude_category'],
            'products' => $atts['products'],
            'tier' => (int) $atts['tier'],
            'limit' => (int) $atts['limit'],
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'popular_only' => $atts['popular_only'] === 'true',
            'marketplace_only' => $atts['marketplace_only'] === 'true',
        ];

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $products = $repository->get_marketplace_products($args);
        $categories = $repository->get_categories_with_product_counts();

        $admin_notice = '';
        if (current_user_can('manage_woocommerce') && empty($products)) {
            $admin_notice = $this->get_empty_products_notice($args['marketplace_only']);
        }

        $data = [
            'products' => $products,
            'categories' => $categories,
            'atts' => $atts,
            'repository' => $repository,
            'admin_notice' => $admin_notice,
        ];

        return $this->load_template('marketplace/marketplace.php', $data);
    }

    private function ensure_frontend_assets(): void
    {
        wp_enqueue_style(
            'fontawesome-free',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            [],
            '6.5.1'
        );

        wp_enqueue_style(
            'wc-cgmp-marketplace',
            WC_CGMP_PLUGIN_URL . 'assets/css/marketplace.css',
            ['fontawesome-free', 'dashicons'],
            WC_CGMP_VERSION
        );

        wp_enqueue_style(
            'wc-cgmp-frontend',
            WC_CGMP_PLUGIN_URL . 'assets/css/frontend.css',
            ['wc-cgmp-marketplace'],
            WC_CGMP_VERSION
        );

        if (!wp_script_is('wc-cgmp-marketplace', 'enqueued')) {
            wp_enqueue_script(
                'wc-cgmp-marketplace',
                WC_CGMP_PLUGIN_URL . 'assets/js/marketplace.js',
                ['jquery'],
                WC_CGMP_VERSION,
                true
            );

            wp_localize_script('wc-cgmp-marketplace', 'wc_cgmp_ajax', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wc_cgmp_frontend_nonce'),
                'debug' => (defined('WP_DEBUG') && WP_DEBUG) || (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG),
                'load_all' => (bool) get_option('wc_cgmp_load_all_products', false),
                'i18n' => [
                    'added_to_cart' => __('Added to cart!', 'wc-carousel-grid-marketplace-and-pricing'),
                    'error' => __('An error occurred. Please try again.', 'wc-carousel-grid-marketplace-and-pricing'),
                    'select_tier' => __('Please select an experience level before adding to cart.', 'wc-carousel-grid-marketplace-and-pricing'),
                    'invalid_tier' => __('Invalid experience level.', 'wc-carousel-grid-marketplace-and-pricing'),
                    'invalid_price_type' => __('Invalid pricing option.', 'wc-carousel-grid-marketplace-and-pricing'),
                ],
            ]);
        }

        if (!wp_script_is('wc-cgmp-frontend', 'enqueued')) {
            wp_enqueue_script(
                'wc-cgmp-frontend',
                WC_CGMP_PLUGIN_URL . 'assets/js/frontend.js',
                ['jquery', 'wc-cgmp-marketplace'],
                WC_CGMP_VERSION,
                true
            );
        }
    }

    private function get_empty_products_notice(bool $marketplace_only): string
    {
        if ($marketplace_only) {
            $message = __('No marketplace products found. Enable products for marketplace in the product editor.', 'wc-carousel-grid-marketplace-and-pricing');
            $link_text = __('Edit Products', 'wc-carousel-grid-marketplace-and-pricing');
            $link_url = admin_url('edit.php?post_type=product');
        } else {
            $message = __('No WooCommerce products found.', 'wc-carousel-grid-marketplace-and-pricing');
            $link_text = __('Add Products', 'wc-carousel-grid-marketplace-and-pricing');
            $link_url = admin_url('post-new.php?post_type=product');
        }

        return sprintf(
            '<div class="wc-cgmp-admin-notice" style="background:#fff3cd;border:1px solid #ffc107;padding:12px;margin:10px 0;border-radius:4px;"><p style="margin:0;">%s <a href="%s">%s</a></p></div>',
            esc_html($message),
            esc_url($link_url),
            esc_html($link_text)
        );
    }

    private function load_template(string $template_name, array $data = []): string
    {
        $products = $data['products'] ?? [];
        $categories = $data['categories'] ?? [];
        $atts = $data['atts'] ?? [];
        $repository = $data['repository'] ?? null;
        $admin_notice = $data['admin_notice'] ?? '';
        
        ob_start();

        $theme_template = get_stylesheet_directory() . '/wc-carousel-grid-marketplace-and-pricing/' . $template_name;

        if (file_exists($theme_template)) {
            include $theme_template;
        } else {
            include WC_CGMP_PLUGIN_DIR . 'templates/' . $template_name;
        }

        return ob_get_clean() ?: '';
    }
}
