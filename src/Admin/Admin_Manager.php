<?php

namespace WC_CGMP\Admin;

defined('ABSPATH') || exit;

class Admin_Manager
{
    public function __construct()
    {
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function add_admin_menu(): void
    {
        add_submenu_page(
            'woocommerce',
            __('Marketplace & Pricing Settings', 'wc-carousel-grid-marketplace-and-pricing'),
            __('Marketplace & Pricing', 'wc-carousel-grid-marketplace-and-pricing'),
            'manage_woocommerce',
            'wc-carousel-grid-marketplace-and-pricing',
            [$this, 'render_settings_page']
        );
    }

    public function enqueue_assets(string $hook): void
    {
        if ($hook !== 'woocommerce_page_wc-carousel-grid-marketplace-and-pricing') {
            return;
        }

        wp_enqueue_style(
            'wc-cgmp-admin',
            WC_CGMP_PLUGIN_URL . 'assets/css/admin.css',
            [],
            WC_CGMP_VERSION
        );

        wp_enqueue_script(
            'wc-cgmp-admin-settings',
            WC_CGMP_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery'],
            WC_CGMP_VERSION,
            true
        );
    }

    public function render_settings_page(): void
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'wc-carousel-grid-marketplace-and-pricing'));
        }

        if (isset($_POST['wc_cgmp_save_settings']) && check_admin_referer('wc_cgmp_settings_nonce')) {
            $this->save_settings();
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Settings saved successfully.', 'wc-carousel-grid-marketplace-and-pricing') . '</p></div>';
        }

        $this->render_settings_form();
    }

    private function save_settings(): void
    {
        $settings = [
            'wc_cgmp_grid_columns' => absint($_POST['wc_cgmp_grid_columns'] ?? 3),
            'wc_cgmp_cards_per_page' => absint($_POST['wc_cgmp_cards_per_page'] ?? 12),
            'wc_cgmp_mobile_carousel' => isset($_POST['wc_cgmp_mobile_carousel']),
            'wc_cgmp_show_sidebar' => isset($_POST['wc_cgmp_show_sidebar']),
            'wc_cgmp_show_filter_bar' => isset($_POST['wc_cgmp_show_filter_bar']),
            'wc_cgmp_enable_infinite_scroll' => isset($_POST['wc_cgmp_enable_infinite_scroll']),
            'wc_cgmp_card_style' => sanitize_text_field($_POST['wc_cgmp_card_style'] ?? 'default'),
            'wc_cgmp_popular_method' => sanitize_text_field($_POST['wc_cgmp_popular_method'] ?? 'auto'),
            'wc_cgmp_popular_threshold' => absint($_POST['wc_cgmp_popular_threshold'] ?? 5),
            'wc_cgmp_popular_days' => absint($_POST['wc_cgmp_popular_days'] ?? 30),
            'wc_cgmp_remove_data_on_uninstall' => isset($_POST['wc_cgmp_remove_data_on_uninstall']),
        ];

        foreach ($settings as $option => $value) {
            update_option($option, $value);
        }
    }

    private function render_settings_form(): void
    {
        ?>
        <div class="wrap wc-cgmp-admin-wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

            <div class="wc-cgmp-admin-header">
                <p class="description">
                    <?php esc_html_e('Configure the WooCommerce Carousel/Grid Marketplace & Pricing settings.', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </p>
            </div>

            <form method="post" action="">
                <?php wp_nonce_field('wc_cgmp_settings_nonce'); ?>

                <div class="wc-cgmp-settings-section">
                    <h2><?php esc_html_e('General Settings', 'wc-carousel-grid-marketplace-and-pricing'); ?></h2>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Grid Columns', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <input type="number" name="wc_cgmp_grid_columns" value="<?php echo esc_attr(get_option('wc_cgmp_grid_columns', 3)); ?>" min="1" max="6">
                                <p class="description"><?php esc_html_e('Number of columns in grid layout (1-6).', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Products per Page', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <input type="number" name="wc_cgmp_cards_per_page" value="<?php echo esc_attr(get_option('wc_cgmp_cards_per_page', 12)); ?>" min="1" max="100">
                                <p class="description"><?php esc_html_e('Maximum number of products to display per page.', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Mobile Carousel', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wc_cgmp_mobile_carousel" value="1" <?php checked(get_option('wc_cgmp_mobile_carousel', true)); ?>>
                                    <?php esc_html_e('Enable carousel on mobile devices', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Show Sidebar', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wc_cgmp_show_sidebar" value="1" <?php checked(get_option('wc_cgmp_show_sidebar', true)); ?>>
                                    <?php esc_html_e('Display category sidebar', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Show Filter Bar', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wc_cgmp_show_filter_bar" value="1" <?php checked(get_option('wc_cgmp_show_filter_bar', true)); ?>>
                                    <?php esc_html_e('Display tier filter bar', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Infinite Scroll', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wc_cgmp_enable_infinite_scroll" value="1" <?php checked(get_option('wc_cgmp_enable_infinite_scroll', false)); ?>>
                                    <?php esc_html_e('Enable infinite scroll instead of pagination', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Card Style', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <select name="wc_cgmp_card_style">
                                    <option value="default" <?php selected(get_option('wc_cgmp_card_style', 'default'), 'default'); ?>><?php esc_html_e('Default', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                    <option value="compact" <?php selected(get_option('wc_cgmp_card_style'), 'compact'); ?>><?php esc_html_e('Compact', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                    <option value="detailed" <?php selected(get_option('wc_cgmp_card_style'), 'detailed'); ?>><?php esc_html_e('Detailed', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="wc-cgmp-settings-section">
                    <h2><?php esc_html_e('Popular Badge', 'wc-carousel-grid-marketplace-and-pricing'); ?></h2>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Popular Method', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <select name="wc_cgmp_popular_method">
                                    <option value="auto" <?php selected(get_option('wc_cgmp_popular_method', 'auto'), 'auto'); ?>><?php esc_html_e('Automatic (based on sales)', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                    <option value="manual" <?php selected(get_option('wc_cgmp_popular_method'), 'manual'); ?>><?php esc_html_e('Manual (set per product)', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                    <option value="both" <?php selected(get_option('wc_cgmp_popular_method'), 'both'); ?>><?php esc_html_e('Both (auto + manual)', 'wc-carousel-grid-marketplace-and-pricing'); ?></option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Sales Threshold', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <input type="number" name="wc_cgmp_popular_threshold" value="<?php echo esc_attr(get_option('wc_cgmp_popular_threshold', 5)); ?>" min="1" max="100">
                                <p class="description"><?php esc_html_e('Minimum sales required for auto-popular badge.', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e('Lookback Period (Days)', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <input type="number" name="wc_cgmp_popular_days" value="<?php echo esc_attr(get_option('wc_cgmp_popular_days', 30)); ?>" min="1" max="365">
                                <p class="description"><?php esc_html_e('Number of days to look back for sales count.', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="wc-cgmp-settings-section">
                    <h2><?php esc_html_e('Shortcode Usage', 'wc-carousel-grid-marketplace-and-pricing'); ?></h2>

                    <div class="wc-cgmp-shortcode-info">
                        <p><strong><?php esc_html_e('Basic Usage:', 'wc-carousel-grid-marketplace-and-pricing'); ?></strong></p>
                        <code>[wc_cgmp_marketplace]</code>

                        <p class="wc-cgmp-mt-2"><strong><?php esc_html_e('With Parameters:', 'wc-carousel-grid-marketplace-and-pricing'); ?></strong></p>
                        <code>[wc_cgmp_marketplace columns="4" category="15" limit="8" show_sidebar="false"]</code>

                        <p class="wc-cgmp-mt-2"><strong><?php esc_html_e('Elementor:', 'wc-carousel-grid-marketplace-and-pricing'); ?></strong></p>
                        <p><?php esc_html_e('Use the native "WC Marketplace" widget in Elementor for full visual controls.', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                    </div>
                </div>

                <div class="wc-cgmp-settings-section">
                    <h2><?php esc_html_e('Data Management', 'wc-carousel-grid-marketplace-and-pricing'); ?></h2>

                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php esc_html_e('Remove Data on Uninstall', 'wc-carousel-grid-marketplace-and-pricing'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wc_cgmp_remove_data_on_uninstall" value="1" <?php checked(get_option('wc_cgmp_remove_data_on_uninstall', false)); ?>>
                                    <?php esc_html_e('Delete all plugin data when uninstalling', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </label>
                                <p class="description"><?php esc_html_e('Warning: This will permanently delete all marketplace settings, tier data, and post meta.', 'wc-carousel-grid-marketplace-and-pricing'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>

                <p class="submit">
                    <input type="submit" name="wc_cgmp_save_settings" class="button button-primary" value="<?php esc_attr_e('Save Settings', 'wc-carousel-grid-marketplace-and-pricing'); ?>">
                </p>
            </form>
        </div>
        <?php
    }
}
