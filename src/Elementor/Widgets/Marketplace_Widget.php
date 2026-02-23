<?php

namespace WC_CGMP\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || exit;

class Marketplace_Widget extends Widget_Base
{
    public function get_name(): string
    {
        return 'wc_cgmp_marketplace';
    }

    public function get_title(): string
    {
        return __('WC Marketplace', 'wc-carousel-grid-marketplace-and-pricing');
    }

    public function get_icon(): string
    {
        return 'eicon-products';
    }

    public function get_categories(): array
    {
        return ['yosh-tools'];
    }

    private function get_product_categories(): array
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
        ]);

        if (is_wp_error($categories)) {
            return [];
        }

        $options = [];
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }

        return $options;
    }

    private function get_marketplace_products(): array
    {
        $args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 100,
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => '_wc_cgmp_enabled',
                    'value' => 'yes',
                ],
                [
                    'key' => '_welp_enabled',
                    'value' => 'yes',
                ],
            ],
        ];

        $query = new \WP_Query($args);
        $options = [];

        foreach ($query->posts as $post) {
            $options[$post->ID] = $post->post_title;
        }

        return $options;
    }

    public function get_keywords(): array
    {
        return ['woocommerce', 'products', 'marketplace', 'carousel', 'grid', 'services', 'pricing', 'tier'];
    }

    public function get_style_depends(): array
    {
        $styles = [];
        
        if (wp_style_is('wc-cgmp-marketplace', 'registered')) {
            $styles[] = 'wc-cgmp-marketplace';
        }
        
        if (wp_style_is('wc-cgmp-frontend', 'registered')) {
            $styles[] = 'wc-cgmp-frontend';
        }
        
        return $styles;
    }

    public function get_script_depends(): array
    {
        $scripts = [];
        
        if (wp_script_is('wc-cgmp-marketplace', 'registered')) {
            $scripts[] = 'wc-cgmp-marketplace';
        }
        
        if (wp_script_is('wc-cgmp-frontend', 'registered')) {
            $scripts[] = 'wc-cgmp-frontend';
        }
        
        return $scripts;
    }

    protected function register_controls(): void
    {
        $this->start_controls_section('content_section', [
            'label' => __('Content', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('source_type', [
            'label' => __('Source', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'all',
            'options' => [
                'all' => __('All Products', 'wc-carousel-grid-marketplace-and-pricing'),
                'categories' => __('Specific Categories', 'wc-carousel-grid-marketplace-and-pricing'),
                'products' => __('Manual Selection', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        $this->add_control('categories', [
            'label' => __('Categories', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $this->get_product_categories(),
            'condition' => ['source_type' => 'categories'],
        ]);

        $this->add_control('products', [
            'label' => __('Products', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT2,
            'multiple' => true,
            'options' => $this->get_marketplace_products(),
            'condition' => ['source_type' => 'products'],
        ]);

        $this->add_control('products_per_page', [
            'label' => __('Products Per Page', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::NUMBER,
            'default' => 12,
            'min' => 1,
            'max' => 100,
        ]);

        $this->add_control('orderby', [
            'label' => __('Order By', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'date',
            'options' => [
                'date' => __('Date', 'wc-carousel-grid-marketplace-and-pricing'),
                'price' => __('Price', 'wc-carousel-grid-marketplace-and-pricing'),
                'popularity' => __('Popularity', 'wc-carousel-grid-marketplace-and-pricing'),
                'title' => __('Title', 'wc-carousel-grid-marketplace-and-pricing'),
                'rand' => __('Random', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        $this->add_control('order', [
            'label' => __('Order', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'DESC',
            'options' => [
                'ASC' => __('Ascending', 'wc-carousel-grid-marketplace-and-pricing'),
                'DESC' => __('Descending', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('layout_section', [
            'label' => __('Layout', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_CONTENT,
        ]);

        $this->add_control('layout_type', [
            'label' => __('Layout Type', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'grid',
            'options' => [
                'grid' => __('Grid', 'wc-carousel-grid-marketplace-and-pricing'),
                'carousel' => __('Carousel', 'wc-carousel-grid-marketplace-and-pricing'),
                'hybrid' => __('Hybrid (Grid - Carousel on Mobile)', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        $this->add_responsive_control('columns', [
            'label' => __('Columns', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => '3',
            'tablet_default' => '2',
            'mobile_default' => '1',
            'options' => [
                '1' => '1',
                '2' => '2',
                '3' => '3',
                '4' => '4',
            ],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-grid' => 'grid-template-columns: repeat({{VALUE}}, 1fr);',
            ],
        ]);

        $this->add_control('show_sidebar', [
            'label' => __('Show Category Sidebar', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('show_filter', [
            'label' => __('Show Tier Filter', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('default_tier', [
            'label' => __('Default Tier Filter', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => '1',
            'options' => [
                '0' => __('All Tiers', 'wc-carousel-grid-marketplace-and-pricing'),
                '1' => __('Entry', 'wc-carousel-grid-marketplace-and-pricing'),
                '2' => __('Mid', 'wc-carousel-grid-marketplace-and-pricing'),
                '3' => __('Expert', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
            'condition' => ['show_filter' => 'yes'],
        ]);

        $this->add_control('show_search', [
            'label' => __('Show Search', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('show_tier_description', [
            'label' => __('Show Tier Description', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('show_tier_badge', [
            'label' => __('Show Tier Badge', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('infinite_scroll', [
            'label' => __('Infinite Scroll', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('No', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'no',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('carousel_section', [
            'label' => __('Carousel Settings', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_CONTENT,
            'condition' => ['layout_type' => ['carousel', 'hybrid']],
        ]);

        $this->add_control('carousel_autoplay', [
            'label' => __('Autoplay', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Yes', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('No', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'no',
        ]);

        $this->add_control('carousel_speed', [
            'label' => __('Autoplay Speed (ms)', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::NUMBER,
            'default' => 3000,
            'min' => 500,
            'max' => 10000,
            'condition' => ['carousel_autoplay' => 'yes'],
        ]);

        $this->add_control('carousel_arrows', [
            'label' => __('Show Navigation Arrows', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->add_control('carousel_dots', [
            'label' => __('Show Pagination Dots', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SWITCHER,
            'label_on' => __('Show', 'wc-carousel-grid-marketplace-and-pricing'),
            'label_off' => __('Hide', 'wc-carousel-grid-marketplace-and-pricing'),
            'default' => 'yes',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('card_style_section', [
            'label' => __('Card Style', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('card_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-card' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('card_border_radius', [
            'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 30],
            ],
            'default' => ['size' => 12, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-card' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Border::get_type(), [
            'name' => 'card_border',
            'selector' => '{{WRAPPER}} .wc-cgmp-card',
        ]);

        $this->add_control('card_padding', [
            'label' => __('Padding', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::DIMENSIONS,
            'size_units' => ['px'],
            'default' => [
                'top' => 20, 'right' => 20, 'bottom' => 20, 'left' => 20, 'unit' => 'px', 'isLinked' => true,
            ],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]);

        $this->add_control('card_shadow', [
            'label' => __('Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'light',
            'options' => [
                'none' => __('None', 'wc-carousel-grid-marketplace-and-pricing'),
                'light' => __('Light', 'wc-carousel-grid-marketplace-and-pricing'),
                'medium' => __('Medium', 'wc-carousel-grid-marketplace-and-pricing'),
                'strong' => __('Strong', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('typography_section', [
            'label' => __('Typography', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('title_heading', [
            'label' => __('Title', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('title_color', [
            'label' => __('Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#1f2937',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-card-title' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'title_typography',
            'selector' => '{{WRAPPER}} .wc-cgmp-card-title',
            'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
        ]);

        $this->add_control('description_heading', [
            'label' => __('Description', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('description_color', [
            'label' => __('Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#6b7280',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-card-desc' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'description_typography',
            'selector' => '{{WRAPPER}} .wc-cgmp-card-desc',
            'global' => ['default' => Global_Typography::TYPOGRAPHY_TEXT],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('button_style_section', [
            'label' => __('Button', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('button_colors_heading', [
            'label' => __('Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('button_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'background-color: {{VALUE}}; --wc-cgmp-btn-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_hover_bg_color', [
            'label' => __('Hover Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#16a34a',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart:hover' => 'background-color: {{VALUE}}; --wc-cgmp-btn-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'color: {{VALUE}}; --wc-cgmp-btn-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_hover_text_color', [
            'label' => __('Hover Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_border_heading', [
            'label' => __('Border & Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('button_border_radius', [
            'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 30],
            ],
            'default' => ['size' => 8, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('button_hover_border_color', [
            'label' => __('Hover Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart:hover' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_hover_border_width', [
            'label' => __('Hover Border Width', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 5],
            ],
            'default' => ['size' => 0, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart:hover' => 'border-width: {{SIZE}}{{UNIT}}; border-style: solid;',
            ],
        ]);

        $this->add_control('button_effects_heading', [
            'label' => __('Hover Effects', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_group_control(\Elementor\Group_Control_Box_Shadow::get_type(), [
            'name' => 'button_hover_shadow',
            'selector' => '{{WRAPPER}} .wc-cgmp-add-to-cart:hover',
        ]);

        $this->add_control('button_hover_transform_y', [
            'label' => __('Hover Y Offset', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => -10, 'max' => 10],
            ],
            'default' => ['size' => -2, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => '--wc-cgmp-btn-hover-y: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('button_hover_transform_scale', [
            'label' => __('Hover Scale', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'range' => [
                'px' => ['min' => 0.9, 'max' => 1.2],
            ],
            'default' => ['size' => 1.02],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => '--wc-cgmp-btn-hover-scale: {{SIZE}};',
            ],
        ]);

        $this->add_control('button_transition_duration', [
            'label' => __('Transition Duration', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['s', 'ms'],
            'range' => [
                's' => ['min' => 0.1, 'max' => 1, 'step' => 0.1],
                'ms' => ['min' => 100, 'max' => 1000, 'step' => 50],
            ],
            'default' => ['size' => 0.3, 'unit' => 's'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'transition-duration: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('button_hover_animation', [
            'label' => __('Hover Animation', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'none',
            'options' => [
                'none' => __('None', 'wc-carousel-grid-marketplace-and-pricing'),
                'grow' => __('Grow', 'wc-carousel-grid-marketplace-and-pricing'),
                'float' => __('Float Up', 'wc-carousel-grid-marketplace-and-pricing'),
                'pulse' => __('Pulse', 'wc-carousel-grid-marketplace-and-pricing'),
                'bounce' => __('Bounce', 'wc-carousel-grid-marketplace-and-pricing'),
                'shine' => __('Shine', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
            'prefix_class' => 'wc-cgmp-btn-animation-',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('tier_filter_style_section', [
            'label' => __('Tier Filter', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_filter' => 'yes'],
        ]);

        $this->add_control('tier_filter_bg_heading', [
            'label' => __('Background Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('tier_button_bg_color', [
            'label' => __('Button Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn' => 'background-color: {{VALUE}}; --wc-cgmp-tier-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_hover_bg_color', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f9fafb',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn:hover' => 'background-color: {{VALUE}}; --wc-cgmp-tier-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_active_bg_color', [
            'label' => __('Active Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#a855f7',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn.active' => 'background-color: {{VALUE}}; --wc-cgmp-tier-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_filter_text_heading', [
            'label' => __('Text Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_button_text_color', [
            'label' => __('Button Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#6b7280',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn' => 'color: {{VALUE}}; --wc-cgmp-tier-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_hover_text_color', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#374151',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn:hover' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_active_text_color', [
            'label' => __('Active Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn.active' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_filter_border_heading', [
            'label' => __('Border & Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_button_border_radius', [
            'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 30],
            ],
            'default' => ['size' => 20, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('tier_button_border_color', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#e5e7eb',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-btn' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_filter_accent_heading', [
            'label' => __('Tier Accent Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_entry_color', [
            'label' => __('Entry Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="1"].active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="1"]:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_color', [
            'label' => __('Mid Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#3b82f6',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="2"].active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="2"]:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_color', [
            'label' => __('Expert Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#a855f7',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="3"].active' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
                '{{WRAPPER}} .wc-cgmp-tier-btn[data-tier="3"]:hover' => 'border-color: {{VALUE}}; color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('toggle_style_section', [
            'label' => __('Toggle Switch', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('toggle_bg_heading', [
            'label' => __('Background Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('toggle_bg_color', [
            'label' => __('Off State Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#e5e7eb',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-slider' => 'background-color: {{VALUE}}; --wc-cgmp-toggle-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_active_bg_color', [
            'label' => __('On State Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-input:checked + .wc-cgmp-switch-slider' => 'background-color: {{VALUE}}; --wc-cgmp-toggle-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_knob_heading', [
            'label' => __('Knob (Circle)', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('toggle_knob_color', [
            'label' => __('Knob Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-slider::before' => 'background-color: {{VALUE}}; --wc-cgmp-toggle-knob: {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_knob_shadow_color', [
            'label' => __('Knob Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => 'rgba(0, 0, 0, 0.2)',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-slider::before' => 'box-shadow: 0 2px 6px {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_label_heading', [
            'label' => __('Label Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('toggle_label_color', [
            'label' => __('Off Label', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#9ca3af',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-label' => 'color: {{VALUE}}; --wc-cgmp-toggle-label: {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_label_active_color', [
            'label' => __('Active Label', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-switch-label.active' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('toggle_size_heading', [
            'label' => __('Size', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('toggle_size', [
            'label' => __('Toggle Size', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SELECT,
            'default' => 'medium',
            'options' => [
                'small' => __('Small', 'wc-carousel-grid-marketplace-and-pricing'),
                'medium' => __('Medium', 'wc-carousel-grid-marketplace-and-pricing'),
                'large' => __('Large', 'wc-carousel-grid-marketplace-and-pricing'),
            ],
            'prefix_class' => 'wc-cgmp-toggle-size-',
        ]);

        $this->end_controls_section();

        $this->start_controls_section('pricing_panel_style_section', [
            'label' => __('Pricing Panel', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('pricing_panel_bg_heading', [
            'label' => __('Background Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('pricing_panel_bg_color', [
            'label' => __('Default Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f9fafb',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-pricing-panel' => 'background-color: {{VALUE}}; --wc-cgmp-panel-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('pricing_panel_entry_bg', [
            'label' => __('Entry Tier Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f0fdf4',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-pricing-panel[data-tier="1"]' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pricing_panel_mid_bg', [
            'label' => __('Mid Tier Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#eff6ff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-pricing-panel[data-tier="2"]' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pricing_panel_expert_bg', [
            'label' => __('Expert Tier Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#faf5ff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-pricing-panel[data-tier="3"]' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('pricing_panel_text_heading', [
            'label' => __('Text Colors', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('price_text_color', [
            'label' => __('Price Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#1f2937',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-price-main' => 'color: {{VALUE}}; --wc-cgmp-price-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('price_period_color', [
            'label' => __('Period Color (/mo, /hr)', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#6b7280',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-price-period' => 'color: {{VALUE}}; --wc-cgmp-price-period: {{VALUE}};',
            ],
        ]);

        $this->add_control('total_label_color', [
            'label' => __('"Total" Label Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#6b7280',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-total-label' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('total_price_color', [
            'label' => __('Total Price Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-total-price' => 'color: {{VALUE}}; --wc-cgmp-total-price: {{VALUE}};',
            ],
        ]);

        $this->add_group_control(Group_Control_Typography::get_type(), [
            'name' => 'price_typography',
            'selector' => '{{WRAPPER}} .wc-cgmp-price-main',
            'global' => ['default' => Global_Typography::TYPOGRAPHY_PRIMARY],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('filter_bar_style_section', [
            'label' => __('Filter & Search Bar', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control('filter_bar_heading', [
            'label' => __('Filter Bar', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('filter_bar_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-filter-bar' => 'background-color: {{VALUE}}; --wc-cgmp-filter-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('filter_bar_border_color', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-filter-bar' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('filter_bar_border_radius', [
            'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 20],
            ],
            'default' => ['size' => 8, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-filter-bar' => 'border-radius: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('search_bar_heading', [
            'label' => __('Search Bar', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('search_bar_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-search-input' => 'background-color: {{VALUE}}; --wc-cgmp-search-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('search_bar_focus_bg_color', [
            'label' => __('Focus Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-search-input:focus' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('search_bar_text_color', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#1f2937',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-search-input' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('search_bar_border_color', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#e5e7eb',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-search-input' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('search_bar_focus_border_color', [
            'label' => __('Focus Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-search-input:focus' => 'border-color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();

        $this->start_controls_section('sidebar_style_section', [
            'label' => __('Sidebar', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_sidebar' => 'yes'],
        ]);

        $this->add_control('sidebar_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-sidebar' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('sidebar_width', [
            'label' => __('Width', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 200, 'max' => 400],
            ],
            'default' => ['size' => 280, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-sidebar' => 'width: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('active_category_bg', [
            'label' => __('Active Category Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-category-item.active' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('active_category_text', [
            'label' => __('Active Category Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-category-item.active' => 'color: {{VALUE}};',
            ],
        ]);

        $this->end_controls_section();
    }

    protected function render(): void
    {
        $settings = $this->get_settings_for_display();

        $category = '';
        if (($settings['source_type'] ?? 'all') === 'categories' && !empty($settings['categories'])) {
            $category = implode(',', $settings['categories']);
        }

        $products = '';
        if (($settings['source_type'] ?? 'all') === 'products' && !empty($settings['products'])) {
            $products = implode(',', $settings['products']);
        }

        $shortcode_atts = [
            'columns' => $settings['columns'] ?? '3',
            'columns_tablet' => $settings['columns_tablet'] ?? '2',
            'columns_mobile' => $settings['columns_mobile'] ?? '1',
            'category' => $category,
            'products' => $products,
            'tier' => $settings['default_tier'] ?? '1',
            'limit' => $settings['products_per_page'] ?? 12,
            'orderby' => $settings['orderby'] ?? 'date',
            'order' => $settings['order'] ?? 'DESC',
            'show_sidebar' => ($settings['show_sidebar'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'show_filter' => ($settings['show_filter'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'show_search' => ($settings['show_search'] ?? 'no') === 'yes' ? 'true' : 'false',
            'show_tier_description' => ($settings['show_tier_description'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'show_tier_badge' => ($settings['show_tier_badge'] ?? 'yes') === 'yes' ? 'true' : 'false',
            'layout' => $settings['layout_type'] ?? 'grid',
            'mobile_carousel' => ($settings['layout_type'] ?? 'grid') === 'hybrid' ? 'true' : 'false',
            'infinite_scroll' => ($settings['infinite_scroll'] ?? 'no') === 'yes' ? 'true' : 'false',
            'marketplace_only' => 'true',
        ];

        $shadow_class = '';
        if (($settings['card_shadow'] ?? 'light') !== 'none') {
            $shadow_class = 'wc-cgmp-shadow-' . ($settings['card_shadow'] ?? 'light');
        }

        $wrapper_class = 'wc-cgmp-marketplace ' . $shadow_class;
        if (!empty($settings['_element_id'])) {
            $wrapper_class .= ' elementor-element-' . $settings['_element_id'];
        }

        echo '<div class="' . esc_attr($wrapper_class) . '">';
        echo do_shortcode('[wc_cgmp_marketplace ' . $this->build_shortcode_string($shortcode_atts) . ']');
        echo '</div>';
    }

    protected function content_template(): void
    {
        ?>
        <#
        var shadowClass = '';
        if (settings.card_shadow !== 'none') {
            shadowClass = 'wc-cgmp-shadow-' + settings.card_shadow;
        }
        #>
        <div class="wc-cgmp-marketplace elementor-placeholder {{shadowClass}}">
            <div class="wc-cgmp-placeholder-content">
                <span class="elementor-widget-empty-icon">
                    <i class="eicon-products"></i>
                </span>
                <span><?php esc_html_e('WC Marketplace', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                <small style="display: block; margin-top: 5px; color: #72777c;">
                    <?php esc_html_e('Showing marketplace-enabled products', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </small>
            </div>
        </div>
        <?php
    }

    private function build_shortcode_string(array $atts): string
    {
        $parts = [];
        foreach ($atts as $key => $value) {
            if (!empty($value)) {
                $parts[] = esc_attr($key) . '="' . esc_attr($value) . '"';
            }
        }
        return implode(' ', $parts);
    }
}
