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
        return ['wc-cgmp-marketplace', 'wc-cgmp-frontend'];
    }

    public function get_script_depends(): array
    {
        return ['wc-cgmp-marketplace', 'wc-cgmp-frontend'];
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

        $this->add_control('button_bg_color', [
            'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#22c55e',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_hover_bg_color', [
            'label' => __('Hover Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#16a34a',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart:hover' => 'background-color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_text_color', [
            'label' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::COLOR,
            'default' => '#ffffff',
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'color: {{VALUE}};',
            ],
        ]);

        $this->add_control('button_border_radius', [
            'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
            'type' => Controls_Manager::SLIDER,
            'size_units' => ['px'],
            'range' => [
                'px' => ['min' => 0, 'max' => 20],
            ],
            'default' => ['size' => 8, 'unit' => 'px'],
            'selectors' => [
                '{{WRAPPER}} .wc-cgmp-add-to-cart' => 'border-radius: {{SIZE}}{{UNIT}};',
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
