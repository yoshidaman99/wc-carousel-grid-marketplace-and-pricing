<?php

namespace WC_CGMP\WooCommerce;

defined('ABSPATH') || exit;

class Reports
{
    public function __construct()
    {
        add_filter('woocommerce_admin_reports', [$this, 'add_tier_reports']);
    }

    public function add_tier_reports(array $reports): array
    {
        $reports['tier_pricing'] = [
            'title' => __('Tier Pricing', 'wc-carousel-grid-marketplace-and-pricing'),
            'reports' => [
                'sales_by_tier' => [
                    'title' => __('Sales by Experience Level', 'wc-carousel-grid-marketplace-and-pricing'),
                    'description' => '',
                    'hide_title' => false,
                    'callback' => [$this, 'render_sales_by_tier_report'],
                ],
                'tier_by_product' => [
                    'title' => __('Tier Sales by Product', 'wc-carousel-grid-marketplace-and-pricing'),
                    'description' => '',
                    'hide_title' => false,
                    'callback' => [$this, 'render_tier_by_product_report'],
                ],
            ],
        ];

        return $reports;
    }

    public function render_sales_by_tier_report(): void
    {
        if (!current_user_can('view_woocommerce_reports') && !current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have permission to view this report.', 'wc-carousel-grid-marketplace-and-pricing'));
        }

        $range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '30day';

        $start_date = '';
        $end_date = date('Y-m-d');

        switch ($range) {
            case '7day':
                $start_date = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30day':
                $start_date = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'month':
                $start_date = date('Y-m-01');
                break;
            case 'year':
                $start_date = date('Y-01-01');
                break;
            default:
                $start_date = date('Y-m-d', strtotime('-30 days'));
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $sales = $repository->get_sales_by_tier([
            'start_date' => $start_date,
            'end_date' => $end_date,
            'group_by_price_type' => true,
        ]);

        $total_revenue = 0;
        $total_orders = 0;
        foreach ($sales as $sale) {
            $total_revenue += (float) $sale->total_revenue;
            $total_orders += (int) $sale->total_orders;
        }

        include WC_CGMP_PLUGIN_DIR . 'templates/admin/reports-sales-by-tier.php';
    }

    public function render_tier_by_product_report(): void
    {
        if (!current_user_can('view_woocommerce_reports') && !current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have permission to view this report.', 'wc-carousel-grid-marketplace-and-pricing'));
        }

        $range = isset($_GET['range']) ? sanitize_text_field($_GET['range']) : '30day';

        $start_date = '';
        $end_date = date('Y-m-d');

        switch ($range) {
            case '7day':
                $start_date = date('Y-m-d', strtotime('-7 days'));
                break;
            case '30day':
                $start_date = date('Y-m-d', strtotime('-30 days'));
                break;
            case 'month':
                $start_date = date('Y-m-01');
                break;
            case 'year':
                $start_date = date('Y-01-01');
                break;
            default:
                $start_date = date('Y-m-d', strtotime('-30 days'));
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');

        $product_id = isset($_GET['product_id']) ? (int) $_GET['product_id'] : 0;

        $args = [
            'start_date' => $start_date,
            'end_date' => $end_date,
        ];

        if ($product_id > 0) {
            $args['product_id'] = $product_id;
        }

        $sales = $repository->get_sales_by_product($args);

        include WC_CGMP_PLUGIN_DIR . 'templates/admin/reports-tier-by-product.php';
    }
}
