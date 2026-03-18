<?php

namespace WC_CGMP\AJAX;

defined('ABSPATH') || exit;

class Handlers
{
    private const RATE_LIMIT_REQUESTS = 30;
    private const RATE_LIMIT_WINDOW = 60;

    public function __construct()
    {
        add_action('wp_ajax_wc_cgmp_save_tiers', [$this, 'handle_save_tiers']);
        add_action('wp_ajax_wc_cgmp_get_tiers', [$this, 'handle_get_tiers']);
        add_action('wp_ajax_nopriv_wc_cgmp_get_tier_price', [$this, 'handle_get_tier_price']);
        add_action('wp_ajax_wc_cgmp_get_tier_price', [$this, 'handle_get_tier_price']);
        add_action('wp_ajax_nopriv_wc_cgmp_get_modal_content', [$this, 'handle_get_modal_content']);
        add_action('wp_ajax_wc_cgmp_get_modal_content', [$this, 'handle_get_modal_content']);
        add_action('wp_ajax_nopriv_wc_cgmp_search_products', [$this, 'handle_search_products']);
        add_action('wp_ajax_wc_cgmp_search_products', [$this, 'handle_search_products']);
        add_action('wp_ajax_nopriv_wc_cgmp_filter_products', [$this, 'handle_filter_products']);
        add_action('wp_ajax_wc_cgmp_filter_products', [$this, 'handle_filter_products']);
    }

    private function check_rate_limit(string $action): bool
    {
        $ip = $this->get_client_ip();
        $transient_key = 'wc_cgmp_rl_' . $action . '_' . md5($ip);
        $count = (int) get_transient($transient_key);

        if ($count >= self::RATE_LIMIT_REQUESTS) {
            wp_send_json_error([
                'message' => __('Too many requests. Please wait and try again.', 'wc-carousel-grid-marketplace-and-pricing'),
                'code' => 'rate_limit_exceeded',
            ], 429);
            return false;
        }

        set_transient($transient_key, $count + 1, self::RATE_LIMIT_WINDOW);
        return true;
    }

    private function get_client_ip(): string
    {
        $headers = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR',
        ];

        foreach ($headers as $header) {
            if (!empty($_SERVER[$header])) {
                $ip = filter_var($_SERVER[$header], FILTER_VALIDATE_IP);
                if ($ip) {
                    return $ip;
                }
            }
        }

        return '0.0.0.0';
    }

    public function handle_save_tiers(): void
    {
        check_ajax_referer('wc_cgmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wc_cgmp_logger()->warning('Unauthorized save_tiers attempt', [
                'user_id' => get_current_user_id(),
            ]);
            wp_send_json_error(['message' => __('Unauthorized', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if (!$product_id) {
            wp_send_json_error(['message' => __('Invalid product ID', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        if (!wc_cgmp_is_enabled($product_id)) {
            wp_send_json_error(['message' => __('Experience Level Pricing not enabled for this product', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $tiers = wp_unslash($_POST['tiers'] ?? []);

        if (!is_array($tiers)) {
            wp_send_json_error(['message' => __('Invalid tier data', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $processed_tiers = [];
        foreach ($tiers as $tier) {
            $processed_tiers[] = [
                'tier_level' => (int) ($tier['tier_level'] ?? 0),
                'tier_name' => sanitize_text_field($tier['tier_name'] ?? ''),
                'monthly_price' => isset($tier['monthly_price']) && $tier['monthly_price'] !== '' ? (float) $tier['monthly_price'] : null,
                'hourly_price' => isset($tier['hourly_price']) && $tier['hourly_price'] !== '' ? (float) $tier['hourly_price'] : null,
                'description' => wp_kses_post($tier['description'] ?? ''),
            ];
        }

        $result = $repository->insert_tiers($product_id, $processed_tiers);

        if ($result) {
            wc_cgmp_logger()->info('Tiers saved successfully', [
                'product_id' => $product_id,
                'tier_count' => count($processed_tiers),
            ]);
            wp_send_json_success(['message' => __('Tiers saved successfully', 'wc-carousel-grid-marketplace-and-pricing')]);
        } else {
            wp_send_json_error(['message' => __('Failed to save tiers', 'wc-carousel-grid-marketplace-and-pricing')]);
        }
    }

    public function handle_get_tiers(): void
    {
        check_ajax_referer('wc_cgmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(['message' => __('Unauthorized', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if (!$product_id) {
            wp_send_json_error(['message' => __('Invalid product ID', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $tiers = $repository->get_tiers_by_product($product_id);

        wp_send_json_success(['tiers' => $tiers]);
    }

    public function handle_get_tier_price(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        if (!$this->check_rate_limit('get_tier_price')) {
            return;
        }

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $tier_level = isset($_POST['tier_level']) ? (int) $_POST['tier_level'] : 0;

        if (!$product_id || !$tier_level) {
            wp_send_json_error(['message' => __('Invalid parameters', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $tier = $repository->get_tier($product_id, $tier_level);

        if (!$tier) {
            wp_send_json_error(['message' => __('Tier not found', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        wp_send_json_success([
            'tier' => $tier,
            'formatted_monthly_price' => \wc_price($tier->monthly_price),
            'formatted_hourly_price' => \wc_price($tier->hourly_price),
        ]);
    }

    public function handle_get_modal_content(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        if (!$product_id) {
            wp_send_json_error(['message' => __('Invalid product ID', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $product = wc_get_product($product_id);

        if (!$product) {
            wp_send_json_error(['message' => __('Product not found', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $icon_color = sanitize_hex_color($_POST['modal_icon_color'] ?? '#dc2626');
        $icon_size = absint($_POST['modal_icon_size'] ?? 16);
        $title = sanitize_text_field($_POST['modal_responsibilities_title'] ?? __('Key Responsibilities', 'wc-carousel-grid-marketplace-and-pricing'));
        
        $cache_key = 'wc_cgmp_modal_' . $product_id . '_' . md5($icon_color . $icon_size . $title);
        $cached_html = get_transient($cache_key);
        
        if ($cached_html !== false) {
            wp_send_json_success(['html' => $cached_html, 'cached' => true]);
            return;
        }

        ob_start();
        
        $modal_description = get_post_meta($product_id, '_wc_cgmp_modal_description', true) ?: '';
        $key_responsibilities = get_post_meta($product_id, '_wc_cgmp_key_responsibilities', true);
        if (!is_array($key_responsibilities)) {
            $key_responsibilities = [];
        }
        
        $atts = [
            'modal_responsibilities_title' => $title,
            'modal_responsibilities_icon_html' => wc_cgmp_get_check_icon(),
            'modal_responsibilities_icon_color' => $icon_color,
            'modal_responsibilities_icon_size' => $icon_size,
        ];
        
        include WC_CGMP_PLUGIN_DIR . 'templates/marketplace/product-modal.php';
        
        $html = ob_get_clean();
        
        set_transient($cache_key, $html, HOUR_IN_SECONDS);

        wp_send_json_success(['html' => $html, 'cached' => false]);
    }

    public function handle_search_products(): void
    {
        try {
            check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

            if (!$this->check_rate_limit('search_products')) {
                wp_send_json_error([
                    'message' => __('Too many requests. Please wait a moment and try again.', 'wc-carousel-grid-marketplace-and-pricing'),
                ]);
                return;
            }

            $search = sanitize_text_field($_POST['search'] ?? '');
            $tier = isset($_POST['tier']) ? (int) $_POST['tier'] : 0;
            $limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 12;
            $orderby = sanitize_text_field($_POST['orderby'] ?? 'date');
            $order = sanitize_text_field($_POST['order'] ?? 'DESC');

            if (strlen($search) < 2) {
                wp_send_json_error(['message' => __('Please enter at least 2 characters', 'wc-carousel-grid-marketplace-and-pricing')]);
                return;
            }

            $plugin = wc_cgmp();
            $repository = $plugin->get_service('repository');
            $atts = $this->build_atts_from_request();

            $args = [
                'search' => $search,
                'tier' => $tier,
                'limit' => $limit > 0 ? $limit : -1,
                'orderby' => $orderby,
                'order' => $order,
                'marketplace_only' => true,
            ];

            $product_ids = $repository->get_marketplace_products($args);

            if (empty($product_ids)) {
                wp_send_json_success([
                    'html' => '<div class="wc-cgmp-no-results" style="padding:20px;text-align:center;color:#666;">' . __('No products found matching your search.', 'wc-carousel-grid-marketplace-and-pricing') . '</div>',
                    'count' => 0,
                    'columns' => (int) ($atts['columns'] ?? 3),
                ]);
                return;
            }

            $html = '';
            foreach ($product_ids as $product_id) {
                $product = wc_get_product($product_id);
                if ($product) {
                    $html .= \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
                }
            }

            wp_send_json_success([
                'html' => $html,
                'count' => count($product_ids),
                'columns' => (int) ($atts['columns'] ?? 3),
            ]);
        } catch (\Exception $e) {
            wp_send_json_error([
                'message' => __('An error occurred while searching. Please try again.', 'wc-carousel-grid-marketplace-and-pricing'),
            ]);
        }
    }

    public function handle_filter_products(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        if (!$this->check_rate_limit('filter_products')) {
            return;
        }

        $category = isset($_POST['category']) ? (int) $_POST['category'] : 0;
        $tier = isset($_POST['tier']) ? (int) $_POST['tier'] : 0;
        $limit = isset($_POST['limit']) ? (int) $_POST['limit'] : 12;
        $offset = isset($_POST['offset']) ? (int) $_POST['offset'] : 0;
        $orderby = sanitize_text_field($_POST['orderby'] ?? 'date');
        $order = sanitize_text_field($_POST['order'] ?? 'DESC');

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $atts = $this->build_atts_from_request();

        $args = [
            'category' => $category > 0 ? $category : '',
            'tier' => $tier,
            'limit' => $limit > 0 ? $limit : -1,
            'offset' => $offset,
            'orderby' => $orderby,
            'order' => $order,
            'marketplace_only' => true,
        ];

        $product_ids = $repository->get_marketplace_products($args);

        $html = '';
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                $html .= \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
            }
        }

        wp_send_json_success([
            'html' => $html,
            'count' => count($product_ids),
            'columns' => (int) ($atts['columns'] ?? 3),
        ]);
    }

    private function build_atts_from_request(): array
    {
        return [
            'show_tier_badge' => sanitize_text_field($_POST['show_tier_badge'] ?? 'true'),
            'show_tier_description' => sanitize_text_field($_POST['show_tier_description'] ?? 'true'),
            'show_popular_badge' => sanitize_text_field($_POST['show_popular_badge'] ?? 'true'),
            'popular_badge_text' => sanitize_text_field($_POST['popular_badge_text'] ?? 'Popular'),
            'price_display_mode' => sanitize_text_field($_POST['price_display_mode'] ?? 'both'),
            'show_price_prefix' => sanitize_text_field($_POST['show_price_prefix'] ?? 'false'),
            'price_prefix_text' => sanitize_text_field($_POST['price_prefix_text'] ?? ''),
            'price_prefix_separator' => sanitize_text_field($_POST['price_prefix_separator'] ?? '|'),
            'price_prefix_position' => sanitize_text_field($_POST['price_prefix_position'] ?? 'inline'),
            'columns' => sanitize_text_field($_POST['columns'] ?? '3'),
            'layout' => sanitize_text_field($_POST['layout'] ?? 'grid'),
            'show_headcount' => sanitize_text_field($_POST['show_headcount'] ?? 'true'),
            'show_total' => sanitize_text_field($_POST['show_total'] ?? 'true'),
            'enable_button_override' => sanitize_text_field($_POST['enable_button_override'] ?? 'false'),
            'override_button_text' => sanitize_text_field($_POST['override_button_text'] ?? 'Get Quote'),
            'override_button_url' => sanitize_text_field($_POST['override_button_url'] ?? ''),
            'include_total_param' => sanitize_text_field($_POST['include_total_param'] ?? 'true'),
            'total_url_param' => sanitize_text_field($_POST['total_url_param'] ?? 'total'),
            'open_in_new_tab' => sanitize_text_field($_POST['open_in_new_tab'] ?? 'true'),
            'enable_above_button_link' => sanitize_text_field($_POST['enable_above_button_link'] ?? 'false'),
            'above_link_icon' => sanitize_text_field($_POST['above_link_icon'] ?? ''),
            'above_link_text' => sanitize_text_field($_POST['above_link_text'] ?? ''),
            'above_link_url' => sanitize_text_field($_POST['above_link_url'] ?? ''),
            'above_link_highlight_text' => sanitize_text_field($_POST['above_link_highlight_text'] ?? ''),
            'above_link_open_new_tab' => sanitize_text_field($_POST['above_link_open_new_tab'] ?? 'true'),
            'orderby' => sanitize_text_field($_POST['orderby'] ?? 'date'),
            'order' => sanitize_text_field($_POST['order'] ?? 'DESC'),
            'remove_price_decimals' => sanitize_text_field($_POST['remove_price_decimals'] ?? 'false'),
            'card_desc_limit' => sanitize_text_field($_POST['card_desc_limit'] ?? '75'),
        ];
    }
}
