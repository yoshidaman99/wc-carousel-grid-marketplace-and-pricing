<?php
/**
 * Plugin Name: WooCommerce Carousel/Grid Marketplace & Pricing
 * Plugin URI: https://github.com/Jerel-R-Yoshida/wc-carousel-grid-marketplace-and-pricing
 * Description: Service marketplace with carousel/grid layout and tiered pricing (Entry/Mid/Expert) with monthly/hourly rates.
 * Version: 1.2.5
 * Author: Jerel Yoshida
 * Author URI: https://github.com/Jerel-R-Yoshida
 * Text Domain: wc-carousel-grid-marketplace-and-pricing
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 6.0
 * WC tested up to: 8.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

defined('ABSPATH') || exit;

define('WC_CGMP_VERSION', '1.2.5');
define('WC_CGMP_PLUGIN_FILE', __FILE__);
define('WC_CGMP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WC_CGMP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WC_CGMP_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WC_CGMP_TABLE_TIERS', 'cgmp_product_tiers');
define('WC_CGMP_TABLE_SALES', 'cgmp_order_tier_sales');
define('WC_CGMP_DB_VERSION', '1.2.5');

if (!function_exists('wc_cgmp_autoloader')) {
    function wc_cgmp_autoloader($class) {
        $prefix = 'WC_CGMP\\';
        $base_dir = WC_CGMP_PLUGIN_DIR . 'src/';

        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $relative_class = substr($class, $len);
        $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

        if (file_exists($file)) {
            require $file;
        }
    }

    spl_autoload_register('wc_cgmp_autoloader');
}

function wc_cgmp(): WC_CGMP\Core\Plugin {
    return WC_CGMP\Core\Plugin::get_instance();
}

function wc_cgmp_is_enabled(int $product_id): bool {
    return get_post_meta($product_id, '_wc_cgmp_enabled', true) === 'yes'
        || get_post_meta($product_id, '_welp_enabled', true) === 'yes';
}

function wc_cgmp_get_tiers(int $product_id): array {
    return wc_cgmp()->get_service('repository')->get_tiers_by_product($product_id);
}

function wc_cgmp_is_popular(int $product_id): bool {
    $method = get_option('wc_cgmp_popular_method', 'auto');

    if ($method === 'manual' || $method === 'both') {
        if (get_post_meta($product_id, '_wc_cgmp_popular', true) === 'yes') {
            return true;
        }
    }

    if ($method === 'auto' || $method === 'both') {
        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        if ($repository) {
            return $repository->is_popular_auto($product_id);
        }
    }

    return false;
}

function wc_cgmp_format_price(float $price, string $type = ''): string {
    $formatted = wc_price($price);

    if ($type === 'monthly') {
        return $formatted . '<span class="wc-cgmp-price-period">/mo</span>';
    } elseif ($type === 'hourly') {
        return $formatted . '<span class="wc-cgmp-price-period">/hr</span>';
    }

    return $formatted;
}

function wc_cgmp_log(string $message, array $context = []): void {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $logger = wc_cgmp()->get_service('logger');
        if ($logger) {
            $logger->debug($message, $context);
        }
    }
}

function wc_cgmp_logger(): ?\WC_CGMP\Core\Debug_Logger {
    return \WC_CGMP\Core\Debug_Logger::get_instance();
}

function wc_cgmp_get_help_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
}

function wc_cgmp_get_tier_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20V10"/><path d="M18 20V4"/><path d="M6 20v-4"/></svg>';
}

function wc_cgmp_get_entry_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>';
}

function wc_cgmp_get_mid_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>';
}

function wc_cgmp_get_expert_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m2 4 3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>';
}

function wc_cgmp_get_settings_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>';
}

function wc_cgmp_get_chevron_icon(): string {
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>';
}

function welp_is_enabled(int $product_id): bool {
    return wc_cgmp_is_enabled($product_id);
}

function welp_get_instance() {
    return wc_cgmp();
}

function welp_log() {
    return wc_cgmp()->get_service('logger');
}

function welp_debug(string $message, array $context = []): void {
    wc_cgmp_log($message, $context);
}

function welp_db_error(string $operation, string $error, array $context = []): void {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $logger = wc_cgmp()->get_service('logger');
        if ($logger) {
            $logger->db_error($operation, $error, $context);
        }
    }
}

function wc_cgm(): WC_CGMP\Core\Plugin {
    return wc_cgmp();
}

function wc_cgm_is_marketplace_product(int $product_id): bool {
    return wc_cgmp_is_enabled($product_id);
}

function wc_cgm_get_tiers(int $product_id): array {
    return wc_cgmp_get_tiers($product_id);
}

function wc_cgm_log(string $message, array $context = []): void {
    wc_cgmp_log($message, $context);
}

function wc_cgmp_check_elementor(): bool {
    return class_exists('\Elementor\Plugin') 
        || (did_action('elementor/loaded') && class_exists('\Elementor\Widget_Base'));
}

add_action('plugins_loaded', 'wc_cgmp_init', 11);

function wc_cgmp_init() {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function() {
            echo '<div class="error"><p>';
            echo '<strong>WooCommerce Carousel/Grid Marketplace & Pricing</strong> requires WooCommerce to be installed and active.';
            echo '</p></div>';
        });
        return;
    }

    wc_cgmp();
}

add_action('plugins_loaded', 'wc_cgmp_init_elementor', 5);

function wc_cgmp_init_elementor(): void {
    if (!did_action('elementor/loaded') && !class_exists('\Elementor\Plugin')) {
        return;
    }

    require_once WC_CGMP_PLUGIN_DIR . 'src/Elementor/Elementor_Integration.php';
    \WC_CGMP\Elementor\Elementor_Integration::get_instance();
}

register_activation_hook(__FILE__, function() {
    if (!class_exists('WooCommerce')) {
        deactivate_plugins(WC_CGMP_PLUGIN_BASENAME);
        wp_die('WooCommerce Carousel/Grid Marketplace & Pricing requires WooCommerce to be installed and active.');
    }

    require_once WC_CGMP_PLUGIN_DIR . 'src/Core/Activator.php';
    $activator = new WC_CGMP\Core\Activator();
    $activator->activate();
});

register_deactivation_hook(__FILE__, function() {
    require_once WC_CGMP_PLUGIN_DIR . 'src/Core/Deactivator.php';
    $deactivator = new WC_CGMP\Core\Deactivator();
    $deactivator->deactivate();
});
