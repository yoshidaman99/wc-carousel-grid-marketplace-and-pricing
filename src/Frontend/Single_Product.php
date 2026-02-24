<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

class Single_Product
{
    public function __construct()
    {
        add_action('woocommerce_single_product_summary', [$this, 'display_tier_selector'], 25);
        add_filter('woocommerce_get_price_html', [$this, 'filter_price_html'], 10, 2);
        add_action('woocommerce_before_add_to_cart_button', [$this, 'add_tier_hidden_fields']);
        add_action('woocommerce_after_add_to_cart_button', [$this, 'display_action_buttons']);
    }

    public function display_tier_selector(): void
    {
        global $product;

        if (!$product || !wc_cgmp_is_enabled($product->get_id())) {
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $tiers = $repository->get_tiers_by_product($product->get_id());
        $available_types = $repository->get_available_price_types($product->get_id());

        if (empty($tiers)) {
            return;
        }

        $this->load_template('tier-selector.php', [
            'tiers' => $tiers,
            'product' => $product,
            'available_types' => $available_types,
        ]);
    }

    public function filter_price_html(string $price, $product): string
    {
        if (!is_a($product, 'WC_Product')) {
            return $price;
        }

        if (!wc_cgmp_is_enabled($product->get_id())) {
            return $price;
        }

        if (!is_product()) {
            return '<span class="wc-cgmp-price-multiple">' . __('Multiple Prices', 'wc-carousel-grid-marketplace-and-pricing') . '</span>';
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $available_types = $repository->get_available_price_types($product->get_id());

        if (empty($available_types)) {
            return $price;
        }

        $default_type = $available_types[0];
        $range = $repository->get_price_range($product->get_id(), $default_type);

        if ($range['min'] <= 0) {
            return $price;
        }

        $suffix = $default_type === 'hourly' ? '/hr' : '/mo';

        if ($range['min'] === $range['max']) {
            return sprintf(
                '<span class="wc-cgmp-price-range" data-price-type="%s">%s<span class="wc-cgmp-price-suffix">%s</span></span>',
                esc_attr($default_type),
                \wc_price($range['min']),
                esc_html($suffix)
            );
        }

        if (count($available_types) > 1) {
            $parts = [];
            foreach ($available_types as $type) {
                $type_range = $repository->get_price_range($product->get_id(), $type);
                if ($type_range['min'] > 0) {
                    $type_suffix = $type === 'hourly' ? '/hr' : '/mo';
                    if ($type_range['min'] === $type_range['max']) {
                        $parts[] = sprintf('%s%s', \wc_price($type_range['min']), esc_html($type_suffix));
                    } else {
                        $parts[] = sprintf('%s - %s%s', \wc_price($type_range['min']), \wc_price($type_range['max']), esc_html($type_suffix));
                    }
                }
            }
            if (!empty($parts)) {
                return sprintf('<span class="wc-cgmp-price-range">%s</span>', implode(' | ', $parts));
            }
        }

        return sprintf(
            '<span class="wc-cgmp-price-range" data-price-type="%s">%s - %s<span class="wc-cgmp-price-suffix">%s</span></span>',
            esc_attr($default_type),
            \wc_price($range['min']),
            \wc_price($range['max']),
            esc_html($suffix)
        );
    }

    public function add_tier_hidden_fields(): void
    {
        global $product;

        if (!$product || !wc_cgmp_is_enabled($product->get_id())) {
            return;
        }

        $plugin = wc_cgmp();
        $repository = $plugin->get_service('repository');
        $tiers = $repository->get_tiers_by_product($product->get_id());
        $available_types = $repository->get_available_price_types($product->get_id());

        if (empty($tiers)) {
            return;
        }

        $default_type = $available_types[0] ?? 'monthly';
        $default_tier = null;

        foreach ($tiers as $tier) {
            $price_column = $default_type === 'hourly' ? 'hourly_price' : 'monthly_price';
            if ($tier->$price_column !== null) {
                $default_tier = $tier;
                break;
            }
        }

        if (!$default_tier) {
            return;
        }
        
        $default_level = $default_tier->tier_level;
        $default_price = $default_type === 'hourly' ? $default_tier->hourly_price : $default_tier->monthly_price;

        $nonce = wp_create_nonce('wc_cgmp_cart_submit');
        echo '<input type="hidden" name="wc_cgmp_cart_nonce" value="' . esc_attr($nonce) . '">';
        echo '<input type="hidden" name="wc_cgmp_selected_tier" id="wc_cgmp_selected_tier" value="' . esc_attr($default_level) . '">';
        echo '<input type="hidden" name="wc_cgmp_tier_name" id="wc_cgmp_tier_name" value="' . esc_attr($default_tier->tier_name ?? '') . '">';
        echo '<input type="hidden" name="wc_cgmp_tier_price" id="wc_cgmp_tier_price" value="' . esc_attr($default_price ?? 0) . '">';
        echo '<input type="hidden" name="wc_cgmp_price_type" id="wc_cgmp_price_type" value="' . esc_attr($default_type) . '">';
    }

    public function display_action_buttons(): void
    {
        global $product;

        if (!$product || !wc_cgmp_is_enabled($product->get_id())) {
            return;
        }

        $product_id = $product->get_id();
        $learn_more_url = wc_cgmp_get_learn_more_url($product_id);
        $apply_now_url = wc_cgmp_get_apply_now_url($product_id);

        if (!$learn_more_url && !$apply_now_url) {
            return;
        }
        ?>
        <div class="wc-cgmp-single-product-buttons">
            <?php if ($learn_more_url) : ?>
                <a href="<?php echo esc_url($learn_more_url); ?>"
                   class="wc-cgmp-button wc-cgmp-button-learn-more"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php esc_html_e('Learn More', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </a>
            <?php endif; ?>

            <?php if ($apply_now_url) : ?>
                <a href="<?php echo esc_url($apply_now_url); ?>"
                   class="wc-cgmp-button wc-cgmp-button-apply-now"
                   target="_blank"
                   rel="noopener noreferrer">
                    <?php esc_html_e('Apply Now', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </a>
            <?php endif; ?>
        </div>
        <?php
    }

    private function load_template(string $template_name, array $data = []): void
    {
        $tiers = $data['tiers'] ?? [];
        $product = $data['product'] ?? null;
        $available_types = $data['available_types'] ?? [];

        $theme_template = get_stylesheet_directory() . '/wc-carousel-grid-marketplace-and-pricing/frontend/' . $template_name;

        if (file_exists($theme_template)) {
            include $theme_template;
        } else {
            include WC_CGMP_PLUGIN_DIR . 'templates/frontend/' . $template_name;
        }
    }
}
