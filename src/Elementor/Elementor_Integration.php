<?php

namespace WC_CGMP\Elementor;

defined('ABSPATH') || exit;

class Elementor_Integration
{
    private static ?self $instance = null;

    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->init_hooks();
    }

    private function init_hooks(): void
    {
        add_action('elementor/init', [$this, 'register_category'], 5);
        add_action('elementor/widgets/register', [$this, 'register_widgets'], 5);
        
        add_action('elementor/editor/after_enqueue_styles', [$this, 'enqueue_editor_styles']);
        add_action('elementor/editor/after_enqueue_scripts', [$this, 'enqueue_editor_scripts']);
        
        add_action('elementor/frontend/after_register_styles', [$this, 'register_styles']);
        add_action('elementor/frontend/after_register_scripts', [$this, 'register_scripts']);
    }

    public function register_category(): void
    {
        $elements_manager = \Elementor\Plugin::instance()->elements_manager;

        if (!$elements_manager || !method_exists($elements_manager, 'add_category')) {
            return;
        }

        $elements_manager->add_category(
            'yosh-tools',
            [
                'title' => __('Yosh Tools', 'wc-carousel-grid-marketplace-and-pricing'),
                'icon'  => 'fa fa-plug',
            ]
        );
    }

    public function register_widgets($widgets_manager): void
    {
        if (!class_exists('\Elementor\Widget_Base')) {
            return;
        }

        require_once WC_CGMP_PLUGIN_DIR . 'src/Elementor/Widgets/Marketplace_Widget.php';
        $widgets_manager->register(new Widgets\Marketplace_Widget());
    }

    public function register_styles(): void
    {
        \wp_register_style(
            'wc-cgmp-marketplace',
            WC_CGMP_PLUGIN_URL . 'assets/css/marketplace.css',
            [],
            WC_CGMP_VERSION
        );

        \wp_register_style(
            'wc-cgmp-frontend',
            WC_CGMP_PLUGIN_URL . 'assets/css/frontend.css',
            ['wc-cgmp-marketplace'],
            WC_CGMP_VERSION
        );
    }

    public function register_scripts(): void
    {
        \wp_register_script(
            'wc-cgmp-marketplace',
            WC_CGMP_PLUGIN_URL . 'assets/js/marketplace.js',
            ['jquery'],
            WC_CGMP_VERSION,
            true
        );

        \wp_localize_script('wc-cgmp-marketplace', 'wc_cgmp_ajax', [
            'ajax_url' => \admin_url('admin-ajax.php'),
            'nonce' => \wp_create_nonce('wc_cgmp_frontend_nonce'),
            'debug' => (\defined('WP_DEBUG') && \WP_DEBUG) || (\defined('SCRIPT_DEBUG') && \SCRIPT_DEBUG),
            'i18n' => [
                'added_to_cart' => \__('Added to cart!', 'wc-carousel-grid-marketplace-and-pricing'),
                'error' => \__('An error occurred.', 'wc-carousel-grid-marketplace-and-pricing'),
                'select_tier' => \__('Please select an experience level.', 'wc-carousel-grid-marketplace-and-pricing'),
                'invalid_tier' => \__('Invalid experience level.', 'wc-carousel-grid-marketplace-and-pricing'),
                'invalid_price_type' => \__('Invalid pricing option.', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        \wp_register_script(
            'wc-cgmp-frontend',
            WC_CGMP_PLUGIN_URL . 'assets/js/frontend.js',
            ['jquery', 'wc-cgmp-marketplace'],
            WC_CGMP_VERSION,
            true
        );
    }

    public function enqueue_editor_styles(): void
    {
        $this->register_styles();
        \wp_enqueue_style('wc-cgmp-marketplace');
        \wp_enqueue_style('wc-cgmp-frontend');
    }

    public function enqueue_editor_scripts(): void
    {
        $this->register_scripts();
        \wp_enqueue_script('wc-cgmp-marketplace');
        \wp_enqueue_script('wc-cgmp-frontend');
    }
}
