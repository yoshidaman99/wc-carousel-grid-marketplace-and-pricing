<?php

namespace WC_CGMP\Elementor\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

defined('ABSPATH') || exit;

class Category_Icon_Widget extends Widget_Base
{
    public function get_name(): string
    {
        return 'wc_cgmp_category_icon';
    }

    public function get_title(): string
    {
        return __('Category Icon', 'wc-carousel-grid-marketplace-and-pricing');
    }

    public function get_icon(): string
    {
        return 'eicon-icon';
    }

    public function get_categories(): array
    {
        return ['yosh-tools'];
    }

    public function get_keywords(): array
    {
        return ['woocommerce', 'category', 'icon', 'image', 'dashicon'];
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

    protected function register_controls(): void
    {
        $this->start_controls_section('content_section', [
            'label' => __('Category Icon', 'wc-carousel-grid-marketplace-and-pricing'),
        ]);

        $this->add_control(
            'category',
            [
                'label' => __('Choose Category', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => $this->get_product_categories(),
            ]
        );

        $this->add_control(
            'icon_size',
            [
                'label' => __('Icon Size', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 24,
                    'sizes' => [
                        'min' => 16,
                        'max' => 128,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wc-cgmp-cat-icon' => 'font-size: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'link_to_category',
            [
                'label' => __('Link to Category', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'wc-carousel-grid-marketplace-and-pricing'),
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'custom_class',
            [
                'label' => __('Custom CSS Class', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::TEXT,
                'default' => '',
            ]
        );

        $this->start_controls_section('style_section', [
            'label' => __('Style', 'wc-carousel-grid-marketplace-and-pricing'),
            'tab' => Controls_Manager::TAB_STYLE,
        ]);

        $this->add_control(
            'icon_color',
            [
                'label' => __('Icon Color', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wc-cgmp-cat-icon' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_bg_color',
            [
                'label' => __('Background Color', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .wc-cgmp-cat-icon' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'icon_padding',
            [
                'label' => __('Padding', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 10,
                    'sizes' => [
                        'min' => 0,
                        'max' => 40,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wc-cgmp-cat-icon' => 'padding: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_control(
            'icon_border_radius',
            [
                'label' => __('Border Radius', 'wc-carousel-grid-marketplace-and-pricing'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 8,
                    'sizes' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .wc-cgmp-cat-icon' => 'border-radius: {{SIZE}}px;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'icon_border',
                'label' => __('Border', 'wc-carousel-grid-marketplace-and-pricing'),
                'selector' => '{{WRAPPER}} .wc-cgmp-cat-icon',
                'fields_options' => [
                    'border' => [
                        'border_type' => 'solid',
                        'width' => 1,
                    ],
                ],
            ]
        );
    }

    protected function render(): void
    {
        $category_id = $this->get_settings_for_display('category');
        
        if (empty($category_id)) {
            echo '<p>' . esc_html_e('Please select a category.', 'wc-carousel-grid-marketplace-and-pricing') . '</p>';
            return;
        }

        $category_icon = \WC_CGMP\Frontend\Category_Icon::get_instance();
        $icon_data = $category_icon->get_icon($category_id);

        if (empty($icon_data['type'])) {
            return;
        }

        $settings = $this->get_settings_for_display();
        $size = (int) $settings['icon_size'];
        $link = 'yes' === $settings['link_to_category'];
        $custom_class = sanitize_html_class($settings['custom_class']);

        $link_url = $link ? get_term_link($category_id, 'product_cat') : '';
        $wrapper_class = 'wc-cgmp-cat-icon-widget ' . $custom_class;
        ?>
        <div class="<?php echo esc_attr(trim($wrapper_class)); ?>">
            <?php if ($link && !is_wp_error($link_url)) : ?>
                <a href="<?php echo esc_url($link_url); ?>" class="wc-cgmp-cat-icon-link">
                    <?php echo $category_icon->render($category_id, ['size' => $size, 'class' => '']); ?>
                </a>
            <?php else : ?>
                <?php echo $category_icon->render($category_id, ['size' => $size, 'class' => '']); ?>
            <?php endif; ?>
        </div>
        <?php
    }
}
