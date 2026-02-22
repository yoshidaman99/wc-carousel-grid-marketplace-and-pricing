<?php

namespace WC_CGMP\AJAX;

defined('ABSPATH') || exit;

class Handlers
{
    public function __construct()
    {
        add_action('wp_ajax_wc_cgmp_save_tiers', [$this, 'handle_save_tiers']);
        add_action('wp_ajax_wc_cgmp_get_tiers', [$this, 'handle_get_tiers']);
        add_action('wp_ajax_nopriv_wc_cgmp_get_tier_price', [$this, 'handle_get_tier_price']);
        add_action('wp_ajax_wc_cgmp_get_tier_price', [$this, 'handle_get_tier_price']);
    }

    public function handle_save_tiers(): void
    {
        check_ajax_referer('wc_cgmp_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wc_cgmp_log()->warning('Unauthorized save_tiers attempt', [
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

        $tiers = isset($_POST['tiers']) ? $_POST['tiers'] : [];

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
            wc_cgmp_log()->info('Tiers saved successfully', [
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
}
