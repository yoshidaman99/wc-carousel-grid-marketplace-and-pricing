<?php

namespace WC_CGMP\WooCommerce;

defined('ABSPATH') || exit;

class Cart_Integration
{
    public function __construct()
    {
        add_filter('woocommerce_add_cart_item_data', [$this, 'add_tier_to_cart'], 10, 3);
        add_filter('woocommerce_get_cart_item_from_session', [$this, 'restore_cart_tier'], 10, 2);
        add_action('woocommerce_before_calculate_totals', [$this, 'override_cart_price']);
        add_filter('woocommerce_cart_item_price', [$this, 'format_cart_price'], 10, 3);
        add_filter('woocommerce_cart_item_name', [$this, 'append_tier_name'], 10, 3);
        add_action('woocommerce_checkout_create_order_line_item', [$this, 'save_order_tier_meta'], 10, 4);
        add_action('woocommerce_order_status_completed', [$this, 'record_tier_sale']);
        add_action('woocommerce_order_status_processing', [$this, 'record_tier_sale']);

        add_action('wp_ajax_wc_cgmp_filter_products', [$this, 'ajax_filter_products']);
        add_action('wp_ajax_nopriv_wc_cgmp_filter_products', [$this, 'ajax_filter_products']);
        add_action('wp_ajax_wc_cgmp_add_to_cart', [$this, 'ajax_add_to_cart']);
        add_action('wp_ajax_nopriv_wc_cgmp_add_to_cart', [$this, 'ajax_add_to_cart']);
        add_action('wp_ajax_wc_cgmp_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_nopriv_wc_cgmp_load_more', [$this, 'ajax_load_more']);
        add_action('wp_ajax_wc_cgmp_search_products', [$this, 'ajax_search_products']);
        add_action('wp_ajax_nopriv_wc_cgmp_search_products', [$this, 'ajax_search_products']);
    }

    private function log(string $message, array $context = []): void
    {
        wc_cgmp_log($message, $context);
    }

    public function add_tier_to_cart(array $cart_item_data, int $product_id, int $variation_id): array
    {
        if (!wc_cgmp_is_enabled($product_id)) {
            return $cart_item_data;
        }

        if (isset($_POST['wc_cgmp_cart_nonce'])) {
            if (!wp_verify_nonce(sanitize_text_field($_POST['wc_cgmp_cart_nonce']), 'wc_cgmp_cart_submit')) {
                $this->log('Cart nonce verification failed', ['product_id' => $product_id]);
                return $cart_item_data;
            }
        }

        if (!isset($_POST['wc_cgmp_selected_tier'])) {
            $this->log('No tier selected for product', ['product_id' => $product_id]);
            return $cart_item_data;
        }

        $tier_level = (int) $_POST['wc_cgmp_selected_tier'];
        $tier_name = isset($_POST['wc_cgmp_tier_name']) ? sanitize_text_field($_POST['wc_cgmp_tier_name']) : '';
        $tier_price = isset($_POST['wc_cgmp_tier_price']) ? (float) $_POST['wc_cgmp_tier_price'] : 0;
        $price_type = isset($_POST['wc_cgmp_price_type']) ? sanitize_text_field($_POST['wc_cgmp_price_type']) : 'monthly';

        $cart_item_data['wc_cgmp_tier'] = [
            'level' => $tier_level,
            'name' => $tier_name,
            'price' => $tier_price,
            'price_type' => $price_type,
        ];

        $this->log('Tier added to cart', [
            'product_id' => $product_id,
            'tier_level' => $tier_level,
            'tier_name' => $tier_name,
            'price' => $tier_price,
            'price_type' => $price_type,
        ]);

        return $cart_item_data;
    }

    public function restore_cart_tier(array $cart_item, array $session_data): array
    {
        if (isset($session_data['wc_cgmp_tier'])) {
            $cart_item['wc_cgmp_tier'] = $session_data['wc_cgmp_tier'];
        }

        return $cart_item;
    }

    public function override_cart_price(\WC_Cart $cart): void
    {
        foreach ($cart->get_cart() as $cart_item) {
            if (isset($cart_item['wc_cgmp_tier']) && isset($cart_item['data'])) {
                $tier_price = (float) $cart_item['wc_cgmp_tier']['price'];
                if ($tier_price > 0) {
                    $cart_item['data']->set_price($tier_price);
                }
            }
        }
    }

    public function format_cart_price(string $price, array $cart_item, int $cart_item_key): string
    {
        if (!isset($cart_item['wc_cgmp_tier'])) {
            return $price;
        }

        $tier_price = (float) $cart_item['wc_cgmp_tier']['price'];
        $price_type = $cart_item['wc_cgmp_tier']['price_type'] ?? 'monthly';

        if ($tier_price > 0) {
            $suffix = $price_type === 'hourly' ? '/hr' : '/mo';
            return \wc_price($tier_price) . '<span class="wc-cgmp-cart-price-suffix">' . esc_html($suffix) . '</span>';
        }

        return $price;
    }

    public function append_tier_name(string $name, array $cart_item, int $cart_item_key): string
    {
        if (!isset($cart_item['wc_cgmp_tier'])) {
            return $name;
        }

        $tier_name = $cart_item['wc_cgmp_tier']['name'];
        $price_type = $cart_item['wc_cgmp_tier']['price_type'] ?? 'monthly';
        $price_label = $price_type === 'hourly' ? 'Hourly' : 'Monthly';

        if (!empty($tier_name)) {
            $name .= sprintf(
                ' <span class="wc-cgmp-cart-tier-name">(%s - %s)</span>',
                esc_html($tier_name),
                esc_html($price_label)
            );
        }

        return $name;
    }

    public function save_order_tier_meta(\WC_Order_Item_Product $item, string $cart_item_key, array $values, \WC_Order $order): void
    {
        if (!isset($values['wc_cgmp_tier'])) {
            return;
        }

        $tier = $values['wc_cgmp_tier'];
        $price_type = $tier['price_type'] ?? 'monthly';
        $price_label = $price_type === 'hourly' ? 'Hourly' : 'Monthly';

        $item->add_meta_data('_wc_cgmp_tier_level', $tier['level']);
        $item->add_meta_data('_wc_cgmp_tier_name', $tier['name']);
        $item->add_meta_data('_wc_cgmp_tier_price', $tier['price']);
        $item->add_meta_data('_wc_cgmp_price_type', $price_type);

        $item->add_meta_data(__('Experience Level', 'wc-carousel-grid-marketplace-and-pricing'), $tier['name'] . ' (' . $price_label . ')');

        $this->log('Order tier meta saved', [
            'order_id' => $order->get_id(),
            'tier_level' => $tier['level'],
            'tier_name' => $tier['name'],
            'price_type' => $price_type,
        ]);
    }

    public function record_tier_sale(int $order_id): void
    {
        $order = \wc_get_order($order_id);

        if (!$order) {
            $this->log('Cannot record tier sale - order not found', ['order_id' => $order_id]);
            return;
        }

        if ($order->get_meta('_wc_cgmp_tiers_recorded')) {
            $this->log('Tier sales already recorded for order', ['order_id' => $order_id]);
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $recorded_count = 0;
        $failed_count = 0;

        foreach ($order->get_items() as $item_id => $item) {
            $tier_level = $item->get_meta('_wc_cgmp_tier_level');
            $tier_name = $item->get_meta('_wc_cgmp_tier_name');
            $tier_price = $item->get_meta('_wc_cgmp_tier_price');
            $price_type = $item->get_meta('_wc_cgmp_price_type') ?: 'monthly';

            if ($tier_level && $tier_name && $tier_price) {
                $product_id = $item->get_product_id();
                $quantity = $item->get_quantity();

                $result = $repository->record_tier_sale(
                    $order_id,
                    $product_id,
                    (int) $tier_level,
                    $tier_name,
                    (float) $tier_price,
                    $price_type,
                    $quantity
                );

                if ($result) {
                    $recorded_count++;
                } else {
                    $failed_count++;
                }
            }
        }

        if ($recorded_count > 0 || $failed_count > 0) {
            $this->log('Tier sale recording completed', [
                'order_id' => $order_id,
                'recorded' => $recorded_count,
                'failed' => $failed_count,
            ]);
        }

        $order->update_meta_data('_wc_cgmp_tiers_recorded', true);
        $order->save();
    }

    public function ajax_filter_products(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        $category = isset($_POST['category']) ? absint($_POST['category']) : 0;
        $tier = isset($_POST['tier']) ? absint($_POST['tier']) : 0;
        $orderby = isset($_POST['orderby']) ? sanitize_text_field($_POST['orderby']) : 'date';
        $order = isset($_POST['order']) ? sanitize_text_field($_POST['order']) : 'DESC';
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 12;
        $offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $args = [
            'category' => $category > 0 ? $category : '',
            'tier' => $tier,
            'orderby' => $orderby,
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset,
            'marketplace_only' => true,
        ];

        $products = $repository->get_marketplace_products($args);

        $atts = [
            'show_tier_badge' => sanitize_text_field($_POST['show_tier_badge'] ?? 'true'),
            'show_tier_description' => sanitize_text_field($_POST['show_tier_description'] ?? 'true'),
        ];

        ob_start();
        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                echo \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
            }
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html, 'count' => count($products)]);
    }

    public function ajax_add_to_cart(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        $product_id = isset($_POST['product_id']) ? absint($_POST['product_id']) : 0;
        $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;
        $tier_level = isset($_POST['tier_level']) ? absint($_POST['tier_level']) : 0;
        $price_type = isset($_POST['price_type']) ? sanitize_text_field($_POST['price_type']) : 'monthly';

        $this->log('AJAX add_to_cart START', [
            'product_id' => $product_id,
            'quantity' => $quantity,
            'tier_level' => $tier_level,
            'price_type' => $price_type,
        ]);

        if ($product_id <= 0) {
            wp_send_json_error(['message' => __('Invalid product.', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        if (!WC()->cart) {
            wp_send_json_error(['message' => __('Cart not available.', 'wc-carousel-grid-marketplace-and-pricing')]);
            return;
        }

        $cart_item_data = [];

        if (wc_cgmp_is_enabled($product_id)) {
            if ($tier_level <= 0) {
                wp_send_json_error([
                    'message' => __('Please select an experience level before adding to cart.', 'wc-carousel-grid-marketplace-and-pricing')
                ]);
                return;
            }

            $plugin = wc_cgmp();
            $repository = $plugin->get_service('repository');
            $tier = $repository->get_tier($product_id, $tier_level);

            if (!$tier) {
                wp_send_json_error([
                    'message' => __('Selected experience level is not available for this product.', 'wc-carousel-grid-marketplace-and-pricing')
                ]);
                return;
            }

            $price = $price_type === 'monthly' ? $tier->monthly_price : $tier->hourly_price;

            if ($price <= 0) {
                wp_send_json_error([
                    'message' => __('Selected pricing option is not available for this experience level.', 'wc-carousel-grid-marketplace-and-pricing')
                ]);
                return;
            }

            $cart_item_data['wc_cgmp_tier'] = [
                'level' => $tier_level,
                'name' => $tier->tier_name,
                'price' => (float) $price,
                'price_type' => $price_type,
            ];
        }

        try {
            $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, 0, [], $cart_item_data);

            if (is_wp_error($cart_item_key)) {
                wp_send_json_error(['message' => $cart_item_key->get_error_message()]);
                return;
            }

            if (!$cart_item_key) {
                wp_send_json_error(['message' => __('Could not add to cart.', 'wc-carousel-grid-marketplace-and-pricing')]);
                return;
            }

            WC()->cart->calculate_totals();

            $cart_count = WC()->cart->get_cart_contents_count();
            $cart_total = WC()->cart->get_cart_total();

            $cart_items = [];
            foreach (WC()->cart->get_cart() as $item_key => $cart_item) {
                $product = $cart_item['data'];

                $tier_data = isset($cart_item['wc_cgmp_tier']) ? [
                    'tier_level' => $cart_item['wc_cgmp_tier']['level'] ?? '',
                    'tier_name' => $cart_item['wc_cgmp_tier']['name'] ?? '',
                    'monthly_price' => (float) ($cart_item['wc_cgmp_tier']['price'] ?? 0),
                ] : null;

                $cart_items[] = [
                    'key' => $item_key,
                    'product_id' => $cart_item['product_id'],
                    'product_name' => $product->get_name(),
                    'product_url' => $product->get_permalink(),
                    'product_image' => $product->get_image(),
                    'quantity' => $cart_item['quantity'],
                    'price' => $product->get_price_html(),
                    'line_total' => wc_price($cart_item['line_total']),
                    'tier_data' => $tier_data,
                ];
            }

            $cart_data = [
                'items' => $cart_items,
                'count' => $cart_count,
                'subtotal' => WC()->cart->get_cart_subtotal(),
                'is_empty' => WC()->cart->is_empty(),
            ];

            wp_send_json_success([
                'message' => __('Product added to cart!', 'wc-carousel-grid-marketplace-and-pricing'),
                'cart_item_key' => $cart_item_key,
                'cart_count' => $cart_count,
                'cart_total' => $cart_total,
                'cart_hash' => WC()->cart->get_cart_hash(),
                'cart_data' => $cart_data,
            ]);
        } catch (\Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    public function ajax_load_more(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        $offset = isset($_POST['offset']) ? absint($_POST['offset']) : 0;
        $category = isset($_POST['category']) ? absint($_POST['category']) : 0;
        $tier = isset($_POST['tier']) ? absint($_POST['tier']) : 0;
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 12;

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $args = [
            'category' => $category > 0 ? $category : '',
            'tier' => $tier,
            'limit' => $limit,
            'offset' => $offset,
            'marketplace_only' => true,
        ];

        $products = $repository->get_marketplace_products($args);

        $atts = [
            'show_tier_badge' => sanitize_text_field($_POST['show_tier_badge'] ?? 'true'),
            'show_tier_description' => sanitize_text_field($_POST['show_tier_description'] ?? 'true'),
        ];

        ob_start();
        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                echo \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
            }
        }
        $html = ob_get_clean();

        wp_send_json_success([
            'html' => $html,
            'count' => count($products),
            'has_more' => count($products) === $limit,
        ]);
    }

    public function ajax_search_products(): void
    {
        check_ajax_referer('wc_cgmp_frontend_nonce', 'nonce');

        $search = isset($_POST['search']) ? sanitize_text_field($_POST['search']) : '';
        $limit = isset($_POST['limit']) ? absint($_POST['limit']) : 12;

        if (strlen($search) < 2) {
            wp_send_json_success(['html' => '', 'count' => 0]);
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $products = $repository->search_products($search, ['limit' => $limit, 'marketplace_only' => true]);

        $atts = [
            'show_tier_badge' => sanitize_text_field($_POST['show_tier_badge'] ?? 'true'),
            'show_tier_description' => sanitize_text_field($_POST['show_tier_description'] ?? 'true'),
        ];

        ob_start();
        foreach ($products as $product_id) {
            $product = wc_get_product($product_id);
            if ($product) {
                echo \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
            }
        }
        $html = ob_get_clean();

        wp_send_json_success(['html' => $html, 'count' => count($products)]);
    }
}
