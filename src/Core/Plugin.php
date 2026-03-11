<?php

namespace WC_CGMP\Core;

defined('ABSPATH') || exit;

class Plugin
{
    private static ?Plugin $instance = null;
    private array $services = [];
    private string $version = WC_CGMP_VERSION;
    private bool $woocommerce_services_loaded = false;

    public static function get_instance(): Plugin
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->register_core_services();
        $this->init_hooks();
        $this->schedule_woocommerce_services();
        $this->ensure_woocommerce_services_loaded_on_ajax();
    }

    private function register_core_services(): void
    {
        $this->services = [
            'logger' => Debug_Logger::get_instance(),
            'repository' => new \WC_CGMP\Database\Repository(),
            'settings' => new \WC_CGMP\Admin\Settings(),
        ];
    }

    private function schedule_woocommerce_services(): void
    {
        if (class_exists('WooCommerce', false)) {
            $this->register_woocommerce_services();
        } else {
            add_action('woocommerce_loaded', [$this, 'register_woocommerce_services']);
            add_action('plugins_loaded', [$this, 'register_woocommerce_services_fallback'], 20);
        }
    }

    public function ensure_woocommerce_services_loaded_on_ajax(): void
    {
        if (wp_doing_ajax()) {
            add_action('plugins_loaded', [$this, 'register_woocommerce_services'], 5);
        }
    }

    public function register_woocommerce_services(): void
    {
        if ($this->woocommerce_services_loaded) {
            return;
        }

        $this->woocommerce_services_loaded = true;

        $this->services['admin'] = new \WC_CGMP\Admin\Admin_Manager();
        $this->services['frontend'] = new \WC_CGMP\Frontend\Frontend_Manager();
        $this->services['cart_integration'] = new \WC_CGMP\WooCommerce\Cart_Integration();
        $this->services['reports'] = new \WC_CGMP\WooCommerce\Reports();
        $this->services['ajax'] = new \WC_CGMP\AJAX\Handlers();
        $this->services['product_metabox'] = new \WC_CGMP\Admin\Product_Metabox();
        $this->services['single_product'] = new \WC_CGMP\Frontend\Single_Product();
    }

    public function register_woocommerce_services_fallback(): void
    {
        if (!class_exists('WooCommerce', false)) {
            return;
        }
        $this->register_woocommerce_services();
    }

    private function init_hooks(): void
    {
        add_action('init', [$this, 'load_textdomain']);
        add_action('wp_enqueue_scripts', [$this, 'maybe_enqueue_frontend']);
        add_action('admin_enqueue_scripts', [$this, 'maybe_enqueue_admin']);
    }

    public function load_textdomain(): void
    {
        load_plugin_textdomain(
            'wc-carousel-grid-marketplace-and-pricing',
            false,
            dirname(WC_CGMP_PLUGIN_BASENAME) . '/languages'
        );
    }

    public function maybe_enqueue_frontend(): void
    {
        if (is_admin()) {
            return;
        }

        // Only load assets on pages that use our shortcode or Elementor widget
        global $post;
        $should_load = false;

        if ($post instanceof \WP_Post) {
            // Check for shortcode in post content
            if (has_shortcode($post->post_content, 'wc_cgmp_marketplace')
                || has_shortcode($post->post_content, 'wc_cgm_marketplace')) {
                $should_load = true;
            }

            // Check for Elementor widget
            if (!$should_load && strpos($post->post_content, 'wc_cgmp_marketplace') !== false) {
                $should_load = true;
            }
        }

        // Allow other code to force-load assets (e.g., template tags, widgets)
        if (apply_filters('wc_cgmp_force_load_assets', false)) {
            $should_load = true;
        }

        // On AJAX requests, always load (needed for fragment refresh)
        if (wp_doing_ajax()) {
            $should_load = true;
        }

        if ($should_load) {
            $this->enqueue_frontend_assets();
        }
    }

    public function maybe_enqueue_admin(string $hook): void
    {
        $this->enqueue_admin_assets($hook);
    }

    private function enqueue_frontend_assets(): void
    {
        wp_enqueue_style(
            'wc-cgmp-marketplace',
            WC_CGMP_PLUGIN_URL . 'assets/css/marketplace.css',
            [],
            WC_CGMP_VERSION
        );

        wp_enqueue_style(
            'wc-cgmp-frontend',
            WC_CGMP_PLUGIN_URL . 'assets/css/frontend.css',
            ['wc-cgmp-marketplace'],
            WC_CGMP_VERSION
        );

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
                'invalid_tier' => __('Selected experience level is not available for this product.', 'wc-carousel-grid-marketplace-and-pricing'),
                'invalid_price_type' => __('Pricing option is not available for this experience level. Please select a different option.', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        wp_enqueue_script(
            'wc-cgmp-frontend',
            WC_CGMP_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery', 'wc-cgmp-marketplace'],
            WC_CGMP_VERSION,
            true
        );
    }

    private function enqueue_admin_assets(string $hook): void
    {
        $screen = get_current_screen();

        if ($screen && $screen->post_type === 'product') {
            wp_enqueue_style(
                'wc-cgmp-admin',
                WC_CGMP_PLUGIN_URL . 'assets/css/admin.css',
                [],
                WC_CGMP_VERSION
            );

            wp_enqueue_script(
                'wc-cgmp-admin',
                WC_CGMP_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                WC_CGMP_VERSION,
                true
            );

            wp_localize_script('wc-cgmp-admin', 'wc_cgmp_admin', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wc_cgmp_admin_nonce'),
                'i18n' => [
                    'saveError' => __('Error saving tier data.', 'wc-carousel-grid-marketplace-and-pricing'),
                    'saveSuccess' => __('Tier data saved.', 'wc-carousel-grid-marketplace-and-pricing'),
                ],
            ]);
        }

        if (strpos($hook, 'wc-carousel-grid-marketplace-and-pricing') !== false) {
            wp_enqueue_style('wc-cgmp-admin', WC_CGMP_PLUGIN_URL . 'assets/css/admin.css', [], WC_CGMP_VERSION);
        }
    }

    public function get_service(string $name): ?object
    {
        return $this->services[$name] ?? null;
    }

    public function get_version(): string
    {
        return $this->version;
    }

    public function is_woocommerce_loaded(): bool
    {
        return $this->woocommerce_services_loaded;
    }
}
