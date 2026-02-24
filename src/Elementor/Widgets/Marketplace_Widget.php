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

        $this->start_controls_tabs('tier_filter_tabs');

        $this->start_controls_tab('tier_filter_default_tab', [
            'label' => __('Default', 'wc-carousel-grid-marketplace-and-pricing'),
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
                '{{WRAPPER}} .wc-cgmp-tier-btn' => 'border-color: {{VALUE}}; --wc-cgmp-tier-border: {{VALUE}};',
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
            ],
        ]);

        $this->add_control('tier_mid_color', [
            'label' => __('Mid Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#3b82f6',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_color', [
            'label' => __('Expert Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#a855f7',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_color', [
            'label' => __('All Tiers Button', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#6b7280',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->start_controls_tab('tier_filter_hover_tab', [
            'label' => __('Hover & Active', 'wc-carousel-grid-marketplace-and-pricing'),
        ]);

        $this->add_control('tier_hover_general_heading', [
            'label' => __('General Hover Effects', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('tier_button_hover_bg_color', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f9fafb',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_hover_text_color', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#374151',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_hover_border_color', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#d1d5db',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_button_hover_transform', [
            'label' => __('Hover Y Offset', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => -10, 'max' => 10],
            ],
            'default' => ['size' => -1, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-hover-transform: translateY({{SIZE}}{{UNIT}});',
            ],
        ]);

        $this->add_control('tier_button_transition', [
            'label' => __('Transition Duration', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['s', 'ms'],
            'range' => [
                's' => ['min' => 0.1, 'max' => 1, 'step' => 0.05],
                'ms' => ['min' => 100, 'max' => 1000, 'step' => 50],
            ],
            'default' => ['size' => 0.3, 'unit' => 's'],
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-transition: {{SIZE}}{{UNIT}};',
            ],
        ]);

        $this->add_control('tier_active_general_heading', [
            'label' => __('General Active State', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_button_active_text_color', [
            'label' => __('Active Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-active-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_heading', [
            'label' => __('Entry Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_entry_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_hover_shadow', [
            'label' => __('Hover Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(34, 197, 94, 0.3)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-hover-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_active_bg', [
            'label' => __('Active Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_active_border', [
            'label' => __('Active Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-active-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_entry_active_shadow', [
            'label' => __('Active Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(34, 197, 94, 0.4)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-entry-active-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_heading', [
            'label' => __('Mid Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_mid_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_hover_shadow', [
            'label' => __('Hover Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(59, 130, 246, 0.3)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-hover-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_active_bg', [
            'label' => __('Active Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_active_border', [
            'label' => __('Active Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-active-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_mid_active_shadow', [
            'label' => __('Active Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(59, 130, 246, 0.4)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-mid-active-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_heading', [
            'label' => __('Expert Tier', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_expert_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_hover_shadow', [
            'label' => __('Hover Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(168, 85, 247, 0.3)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-hover-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_active_bg', [
            'label' => __('Active Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_active_border', [
            'label' => __('Active Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-active-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_expert_active_shadow', [
            'label' => __('Active Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '0 4px 12px -4px rgba(168, 85, 247, 0.4)',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-expert-active-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_heading', [
            'label' => __('All Tiers Button', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_all_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#374151',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_hover_shadow', [
            'label' => __('Hover Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-hover-shadow: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_active_bg', [
            'label' => __('Active Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-active-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_active_border', [
            'label' => __('Active Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-active-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_active_text', [
            'label' => __('Active Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-active-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_all_active_shadow', [
            'label' => __('Active Box Shadow', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::TEXT,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-tier-all-active-shadow: {{VALUE}};',
            ],
        ]);

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section('tier_badge_style_section', [
            'label' => __('Tier Badges', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
            'condition' => ['show_tier_badge' => 'yes'],
        ]);

        $this->add_control('tier_badge_entry_heading', [
            'label' => __('Entry Tier Badge', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
        ]);

        $this->add_control('tier_badge_entry_bg', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#dcfce7',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.entry' => 'background-color: {{VALUE}}; --wc-cgmp-badge-entry-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_entry_text', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#166534',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.entry' => 'color: {{VALUE}}; --wc-cgmp-badge-entry-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_entry_border', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#86efac',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.entry' => 'border-color: {{VALUE}}; --wc-cgmp-badge-entry-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_entry_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-entry-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_entry_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-entry-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_entry_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-entry-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_heading', [
            'label' => __('Mid Tier Badge', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_badge_mid_bg', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#dbeafe',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.mid' => 'background-color: {{VALUE}}; --wc-cgmp-badge-mid-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_text', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#1e40af',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.mid' => 'color: {{VALUE}}; --wc-cgmp-badge-mid-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_border', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#93c5fd',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.mid' => 'border-color: {{VALUE}}; --wc-cgmp-badge-mid-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-mid-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-mid-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_mid_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-mid-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_heading', [
            'label' => __('Expert Tier Badge', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_badge_expert_bg', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#f3e8ff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.expert' => 'background-color: {{VALUE}}; --wc-cgmp-badge-expert-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_text', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#7c3aed',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.expert' => 'color: {{VALUE}}; --wc-cgmp-badge-expert-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_border', [
            'label' => __('Border Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#c4b5fd',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge.expert' => 'border-color: {{VALUE}}; --wc-cgmp-badge-expert-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_hover_bg', [
            'label' => __('Hover Background', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-expert-hover-bg: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_hover_text', [
            'label' => __('Hover Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-expert-hover-text: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_expert_hover_border', [
            'label' => __('Hover Border', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
                '{{WRAPPER}}' => '--wc-cgmp-badge-expert-hover-border: {{VALUE}};',
            ],
        ]);

        $this->add_control('tier_badge_transition_heading', [
            'label' => __('Transition Effects', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::HEADING,
            'separator' => 'before',
        ]);

        $this->add_control('tier_badge_transition', [
            'label' => __('Transition Duration', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['s', 'ms'],
            'range' => [
                's' => ['min' => 0.1, 'max' => 1, 'step' => 0.05],
                'ms' => ['min' => 100, 'max' => 1000, 'step' => 50],
            ],
            'default' => ['size' => 0.2, 'unit' => 's'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-tier-badge' => 'transition: all {{SIZE}}{{UNIT}} ease;',
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
        if (settings.card_shadow && settings.card_shadow !== 'none') {
            shadowClass = 'wc-cgmp-shadow-' + settings.card_shadow;
        }

        var showSidebar = settings.show_sidebar === 'yes';
        var showFilter = settings.show_filter === 'yes';
        var showSearch = settings.show_search === 'yes';
        var showTierDesc = settings.show_tier_description !== 'no';
        var showTierBadge = settings.show_tier_badge !== 'no';
        var columns = settings.columns || '3';
        var defaultTier = settings.default_tier || '1';

        var tierLabels = { '0': 'All', '1': 'Entry', '2': 'Mid', '3': 'Expert' };
        var tierColors = {
            '1': '#22c55e',
            '2': '#3b82f6',
            '3': '#a855f7'
        };

        var mockProducts = [
            { name: 'Senior Developer', tier: 1, price: 1500, hourly: 25, popular: true, desc: 'Experienced full-stack developer with expertise in modern frameworks.' },
            { name: 'UX Designer', tier: 2, price: 2500, hourly: 40, popular: false, desc: 'Creative designer specializing in user experience and interface design.' },
            { name: 'Solutions Architect', tier: 3, price: 4000, hourly: 65, popular: true, desc: 'Expert architect for enterprise-level system design and cloud infrastructure.' }
        ];
        #>
        <div class="wc-cgmp-marketplace wc-cgmp-editor-preview {{shadowClass}}" style="display: flex; gap: 20px;">
            <#
            if (showSidebar) {
            #>
            <div class="wc-cgmp-sidebar elementor-editor-sidebar" style="width: 220px; flex-shrink: 0; background: #fff; border-radius: 12px; padding: 16px; border: 1px solid #e5e7eb;">
                <h4 style="margin: 0 0 12px 0; font-size: 14px; font-weight: 600; color: #374151;"><?php esc_html_e('Categories', 'wc-carousel-grid-marketplace-and-pricing'); ?></h4>
                <div class="wc-cgmp-category-item active" style="padding: 8px 12px; margin-bottom: 4px; border-radius: 6px; background: #22c55e; color: #fff; font-size: 13px; cursor: pointer;"><?php esc_html_e('All Services', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
                <div class="wc-cgmp-category-item" style="padding: 8px 12px; margin-bottom: 4px; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 13px; cursor: pointer;"><?php esc_html_e('Development', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
                <div class="wc-cgmp-category-item" style="padding: 8px 12px; margin-bottom: 4px; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 13px; cursor: pointer;"><?php esc_html_e('Design', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
                <div class="wc-cgmp-category-item" style="padding: 8px 12px; margin-bottom: 4px; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 13px; cursor: pointer;"><?php esc_html_e('Marketing', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
                <div class="wc-cgmp-category-item" style="padding: 8px 12px; border-radius: 6px; background: #f3f4f6; color: #374151; font-size: 13px; cursor: pointer;"><?php esc_html_e('Consulting', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
            </div>
            <#
            }
            #>
            <div class="wc-cgmp-main-content" style="flex: 1;">
                <#
                if (showFilter || showSearch) {
                #>
                <div class="wc-cgmp-filter-bar elementor-editor-filter" style="display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding: 12px 16px; background: #fff; border-radius: 8px; border: 1px solid #e5e7eb; flex-wrap: wrap;">
                    <#
                    if (showFilter) {
                    #>
                    <div class="wc-cgmp-tier-filter" style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <button class="wc-cgmp-tier-btn elementor-editor-btn" data-tier="0" style="padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #f3f4f6; color: #6b7280; font-size: 12px; font-weight: 500; cursor: pointer;"><?php esc_html_e('All', 'wc-carousel-grid-marketplace-and-pricing'); ?></button>
                        <button class="wc-cgmp-tier-btn elementor-editor-btn active" data-tier="1" style="padding: 6px 14px; border-radius: 20px; border: 1px solid #22c55e; background: #22c55e; color: #fff; font-size: 12px; font-weight: 500; cursor: pointer;"><?php esc_html_e('Entry', 'wc-carousel-grid-marketplace-and-pricing'); ?></button>
                        <button class="wc-cgmp-tier-btn elementor-editor-btn" data-tier="2" style="padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #fff; color: #6b7280; font-size: 12px; font-weight: 500; cursor: pointer;"><?php esc_html_e('Mid', 'wc-carousel-grid-marketplace-and-pricing'); ?></button>
                        <button class="wc-cgmp-tier-btn elementor-editor-btn" data-tier="3" style="padding: 6px 14px; border-radius: 20px; border: 1px solid #e5e7eb; background: #fff; color: #6b7280; font-size: 12px; font-weight: 500; cursor: pointer;"><?php esc_html_e('Expert', 'wc-carousel-grid-marketplace-and-pricing'); ?></button>
                    </div>
                    <#
                    }
                    if (showSearch) {
                    #>
                    <div class="wc-cgmp-search" style="margin-left: auto;">
                        <input type="text" placeholder="<?php esc_attr_e('Search services...', 'wc-carousel-grid-marketplace-and-pricing'); ?>" style="padding: 8px 12px; border: 1px solid #e5e7eb; border-radius: 6px; font-size: 13px; width: 180px; outline: none;" disabled>
                    </div>
                    <#
                    }
                    #>
                </div>
                <#
                }
                #>
                <div class="wc-cgmp-grid elementor-editor-grid" style="display: grid; grid-template-columns: repeat({{columns}}, 1fr); gap: 20px;">
                    <#
                    mockProducts.forEach(function(product, index) {
                        var tierColor = tierColors[product.tier] || '#6b7280';
                        var tierLabel = tierLabels[product.tier.toString()] || 'Entry';
                    #>
                    <div class="wc-cgmp-card elementor-editor-card" style="background: #fff; border-radius: 12px; padding: 20px; border: 1px solid #e5e7eb; position: relative;">
                        <#
                        if (product.popular) {
                        #>
                        <span class="wc-cgmp-badge-popular" style="position: absolute; top: 12px; right: 12px; background: #f59e0b; color: #fff; font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 4px; text-transform: uppercase;"><?php esc_html_e('Popular', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                        <#
                        }
                        #>
                        <h3 class="wc-cgmp-card-title" style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                            {{product.name}}
                            <#
                            if (showTierBadge) {
                            #>
                            <span class="wc-cgmp-tier-badge" style="font-size: 10px; padding: 2px 8px; border-radius: 4px; background: {{tierColor}}; color: #fff; font-weight: 500;">{{tierLabel}}</span>
                            <#
                            }
                            #>
                        </h3>
                        <p class="wc-cgmp-card-desc" style="margin: 0 0 16px 0; font-size: 13px; color: #6b7280; line-height: 1.5;">{{product.desc}}</p>
                        <div class="wc-cgmp-pricing-panel" data-tier="{{product.tier}}" style="background: {{product.tier === 1 ? '#f0fdf4' : product.tier === 2 ? '#eff6ff' : '#faf5ff'}}; border-radius: 8px; padding: 12px; margin-bottom: 12px;">
                            <div class="wc-cgmp-pricing-row" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                                <span class="wc-cgmp-price-main" style="font-size: 20px; font-weight: 700; color: #1f2937;">${{product.price.toLocaleString()}}<span class="wc-cgmp-price-period" style="font-size: 12px; color: #6b7280; font-weight: 400;">/mo</span></span>
                                <span class="wc-cgmp-price-hourly" style="font-size: 14px; color: #6b7280;">${{product.hourly}}/hr</span>
                            </div>
                            <div class="wc-cgmp-headcount" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                                <span style="font-size: 12px; color: #6b7280;"><?php esc_html_e('Headcount:', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <button style="width: 24px; height: 24px; border-radius: 4px; border: 1px solid #d1d5db; background: #fff; cursor: pointer; font-size: 14px;">-</button>
                                    <span style="font-weight: 500; min-width: 20px; text-align: center;">1</span>
                                    <button style="width: 24px; height: 24px; border-radius: 4px; border: 1px solid #d1d5db; background: #fff; cursor: pointer; font-size: 14px;">+</button>
                                </div>
                            </div>
                            <div class="wc-cgmp-total-row" style="display: flex; justify-content: space-between; align-items: center; padding-top: 8px; border-top: 1px solid rgba(0,0,0,0.05);">
                                <span class="wc-cgmp-total-label" style="font-size: 12px; color: #6b7280;"><?php esc_html_e('Total:', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                                <span class="wc-cgmp-total-price" style="font-size: 16px; font-weight: 600; color: #22c55e;">${{product.price.toLocaleString()}}</span>
                            </div>
                        </div>
                        <button class="wc-cgmp-add-to-cart elementor-editor-btn" style="width: 100%; padding: 10px 16px; background: #22c55e; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;">
                            <?php esc_html_e('Add to Cart', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                        </button>
                    </div>
                    <#
                    });
                    #>
                </div>
            </div>
        </div>
        <style>
        .wc-cgmp-editor-preview .elementor-editor-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }
        .wc-cgmp-editor-preview .elementor-editor-sidebar .wc-cgmp-category-item:hover {
            background: #e5e7eb !important;
        }
        .wc-cgmp-editor-preview .elementor-editor-sidebar .wc-cgmp-category-item.active:hover {
            background: #16a34a !important;
        }
        </style>
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
