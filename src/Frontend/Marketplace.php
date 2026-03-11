<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

class Marketplace
{
    public static function render_sidebar(array $categories, array $atts): string
    {
        ob_start();
        include WC_CGMP_PLUGIN_DIR . 'templates/marketplace/sidebar.php';
        return ob_get_clean() ?: '';
    }

    public static function render_filter_bar(array $atts): string
    {
        ob_start();
        include WC_CGMP_PLUGIN_DIR . 'templates/marketplace/filter-bar.php';
        return ob_get_clean() ?: '';
    }

    public static function render_product_card(\WC_Product $product, array $atts, $repository): string
    {
        $product_id = $product->get_id();
        $is_popular = wc_cgmp_is_popular($product_id);
        $specialization = $repository->get_specialization($product_id);
        $tiers = $repository->get_tiers_by_product($product_id);

        ob_start();
        include WC_CGMP_PLUGIN_DIR . 'templates/marketplace/product-card.php';
        return ob_get_clean() ?: '';
    }

    public static function render_pricing_panel(\WC_Product $product, array $tiers, array $atts): string
    {
        $product_id = $product->get_id();
        $tiers = $tiers ?? [];

        $tier_data = [
            1 => ['hourly' => 0, 'monthly' => 0, 'name' => 'Entry', 'description' => ''],
            2 => ['hourly' => 0, 'monthly' => 0, 'name' => 'Mid', 'description' => ''],
            3 => ['hourly' => 0, 'monthly' => 0, 'name' => 'Expert', 'description' => ''],
        ];

        foreach ($tiers as $tier) {
            $tier_data[$tier->tier_level] = [
                'hourly' => (float) ($tier->hourly_price ?? 0),
                'monthly' => (float) ($tier->monthly_price ?? 0),
                'name' => $tier->tier_name ?? '',
                'description' => $tier->description ?? '',
            ];
        }

        $selected_tier = !empty($atts['selected_tier']) ? (int) $atts['selected_tier'] : 0;
        $default_tier = null;
        
        if ($selected_tier > 0) {
            foreach ($tiers as $tier) {
                if ((int) $tier->tier_level === $selected_tier && (($tier->hourly_price ?? 0) > 0 || ($tier->monthly_price ?? 0) > 0)) {
                    $default_tier = $tier;
                    break;
                }
            }
        }
        
        if (!$default_tier) {
            foreach ($tiers as $tier) {
                if (($tier->hourly_price ?? 0) > 0 || ($tier->monthly_price ?? 0) > 0) {
                    $default_tier = $tier;
                    break;
                }
            }
        }
        if (!$default_tier && !empty($tiers)) {
            $default_tier = $tiers[0];
        }
        $default_tier_level = $default_tier ? (int) $default_tier->tier_level : 0;

        $tiers_with_prices = 0;
        foreach ($tiers as $tier) {
            if (($tier->hourly_price ?? 0) > 0 || ($tier->monthly_price ?? 0) > 0) {
                $tiers_with_prices++;
            }
        }
        $has_tiers = $tiers_with_prices > 0;
        $has_multiple_tiers = $tiers_with_prices > 1;

        $price_types = [];
        foreach ($tiers as $tier) {
            if ($tier->monthly_price > 0) $price_types['monthly'] = true;
            if ($tier->hourly_price > 0) $price_types['hourly'] = true;
        }
        $price_types = array_keys($price_types);
        $default_price_type = in_array('monthly', $price_types) ? 'monthly' : ($price_types[0] ?? 'monthly');

        $default_tier_description = $default_tier ? ($default_tier->description ?? '') : '';

        $default_price = $default_tier ? ($default_price_type === 'monthly' ? $default_tier->monthly_price : $default_tier->hourly_price) : 0;
        $monthly_price = $default_tier ? (float) $default_tier->monthly_price : 0;

        if (!$has_tiers) {
            $wc_price = (float) $product->get_price();
            if ($wc_price > 0) {
                $default_price = $wc_price;
                $monthly_price = $wc_price;
                $price_types = ['monthly'];
                $default_price_type = 'monthly';
            }
        }

        ob_start();
        ?>
        <div class="wc-cgmp-pricing-panel"
             data-product-id="<?php echo esc_attr($product_id); ?>"
             data-has-tiers="<?php echo $has_tiers ? 'true' : 'false'; ?>"
             data-has-multiple-tiers="<?php echo $has_multiple_tiers ? 'true' : 'false'; ?>"
             data-product-price="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>"
             data-default-tier="<?php echo esc_attr($default_tier_level); ?>"
             data-default-price-type="<?php echo esc_attr($default_price_type); ?>"
             <?php foreach ([1, 2, 3] as $level) : ?>
             data-tier-<?php echo esc_attr($level); ?>-hourly="<?php echo esc_attr($tier_data[$level]['hourly']); ?>"
             data-tier-<?php echo esc_attr($level); ?>-monthly="<?php echo esc_attr($tier_data[$level]['monthly']); ?>"
             data-tier-<?php echo esc_attr($level); ?>-name="<?php echo esc_attr($tier_data[$level]['name']); ?>"
             data-tier-<?php echo esc_attr($level); ?>-description="<?php echo esc_attr($tier_data[$level]['description'] ?? ''); ?>"
             <?php endforeach; ?>>

            <?php
            $show_tier_description = ($atts['show_tier_description'] ?? 'true') === 'true';
            if ($has_tiers && $show_tier_description && !empty($default_tier_description)) :
            ?>
            <h4 class="wc-cgmp-tier-description"><?php echo esc_html($default_tier_description); ?></h4>
            <?php endif; ?>

            <?php 
            $price_display_mode = $atts['price_display_mode'] ?? 'both';
            $show_price_prefix = ($atts['show_price_prefix'] ?? 'false') === 'true';
            $price_prefix_text = $atts['price_prefix_text'] ?? '';
            $price_prefix_separator = $atts['price_prefix_separator'] ?? '|';
            $price_prefix_position = $atts['price_prefix_position'] ?? 'inline';
            $prefix_text = $show_price_prefix && !empty($price_prefix_text) ? esc_html__($price_prefix_text, 'wc-carousel-grid-marketplace-and-pricing') : '';
            ?>

            <?php if ($has_tiers && count($price_types) > 1 && $price_display_mode === 'both') : ?>
            <div class="wc-cgmp-price-type-switch">
                <span class="wc-cgmp-switch-label <?php echo $default_price_type === 'monthly' ? 'active' : ''; ?>">
                    <?php esc_html_e('Monthly', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </span>
                <label class="wc-cgmp-switch">
                    <input type="checkbox" class="wc-cgmp-switch-input" <?php checked($default_price_type, 'hourly'); ?>>
                    <span class="wc-cgmp-switch-slider"></span>
                </label>
                <span class="wc-cgmp-switch-label <?php echo $default_price_type === 'hourly' ? 'active' : ''; ?>">
                    <?php esc_html_e('Hourly', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                </span>
            </div>
            <?php endif; ?>

            <div class="wc-cgmp-pricing-amount">
                <?php if (!empty($prefix_text) && $price_prefix_position === 'inline') : ?>
                <span class="wc-cgmp-price-prefix"><?php echo esc_html($prefix_text); ?></span>
                <?php if (!empty($price_prefix_separator)) : ?>
                <span class="wc-cgmp-price-prefix-separator"><?php echo esc_html($price_prefix_separator); ?></span>
                <?php endif; ?>
                <?php endif; ?>
                
                <?php if ($price_display_mode === 'both' || $price_display_mode === 'monthly_only') : ?>
                <span class="wc-cgmp-price-main" data-price="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>">
                    <?php echo wc_price(number_format($default_price, 2, '.', '')); ?>
                </span>
                <?php endif; ?>
                
                <?php 
                $secondary_price = 0;
                $secondary_label = '';
                if ($price_display_mode === 'both') :
                    if ($default_price_type === 'monthly') :
                        $secondary_price = $default_tier->hourly_price ?? 0;
                        $secondary_label = '/hr';
                    else :
                        $secondary_price = $default_tier->monthly_price ?? 0;
                        $secondary_label = '/mo';
                    endif;
                    
                    if ($secondary_price > 0) :
                ?>
                <span class="wc-cgmp-price-sub">
                    <?php echo wc_price(number_format($secondary_price, 2, '.', '')) . $secondary_label; ?>
                </span>
                <?php 
                    endif;
                elseif ($price_display_mode === 'hourly_only') : 
                ?>
                <?php 
                $display_hourly_price = $default_tier->hourly_price ?? 0;
                if ($display_hourly_price <= 0) {
                    $display_hourly_price = $default_price;
                }
                ?>
                <span class="wc-cgmp-price-wrapper">
                    <span class="wc-cgmp-price-main" data-price="<?php echo esc_attr(number_format($display_hourly_price, 2, '.', '')); ?>">
                        <?php echo wc_price(number_format($display_hourly_price, 2, '.', '')); ?>
                    </span>
                    <span class="wc-cgmp-price-period">/hr</span>
                </span>
                <?php endif; ?>
            </div>

             <?php if ($has_multiple_tiers) : ?>
             <div class="wc-cgmp-tier-selector-mini">
                 <select class="wc-cgmp-tier-select" name="wc_cgmp_tier_level">
                     <?php foreach ($tiers as $tier) :
                         $hourly = $tier->hourly_price ?? 0;
                         $monthly = $tier->monthly_price ?? 0;
                         $show_price = $default_price_type === 'monthly' ? $monthly : $hourly;
                         if ($show_price <= 0) continue;
                     ?>
                     <option value="<?php echo esc_attr($tier->tier_level); ?>"
                         data-tier-name="<?php echo esc_attr($tier->tier_name); ?>"
                         data-hourly="<?php echo esc_attr($hourly); ?>"
                         data-monthly="<?php echo esc_attr($monthly); ?>"
                         <?php selected($tier->tier_level, $default_tier_level); ?>>
                         <?php echo esc_html($tier->tier_name); ?> - <?php echo wc_price(number_format($show_price, 2, '.', '')); ?>/<?php echo $default_price_type === 'monthly' ? 'mo' : 'hr'; ?>
                     </option>
                     <?php endforeach; ?>
                 </select>
             </div>
             <?php endif; ?>

              <?php 
              $show_headcount = ($atts['show_headcount'] ?? 'true') === 'true';
              $show_total = ($atts['show_total'] ?? 'true') === 'true';
              $enable_button_override = ($atts['enable_button_override'] ?? 'false') === 'true';
              $override_button_text = $atts['override_button_text'] ?? 'Get Quote';
              $override_button_url = $atts['override_button_url'] ?? '';
              $include_total_param = ($atts['include_total_param'] ?? 'true') === 'true';
              $total_url_param = $atts['total_url_param'] ?? 'total';
              $open_in_new_tab = ($atts['open_in_new_tab'] ?? 'true') === 'true';
              $enable_above_button_link = ($atts['enable_above_button_link'] ?? 'false') === 'true';
              $above_link_icon = $atts['above_link_icon'] ?? '';
              $above_link_text = $atts['above_link_text'] ?? '';
              $above_link_url = $atts['above_link_url'] ?? '';
              $above_link_highlight_text = $atts['above_link_highlight_text'] ?? '';
              $above_link_open_new_tab = ($atts['above_link_open_new_tab'] ?? 'true') === 'true';
              ?>

             <?php if ($show_headcount) : ?>
             <div class="wc-cgmp-headcount">
                 <span class="wc-cgmp-headcount-label"><?php esc_html_e('Headcount:', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                 <button type="button" class="wc-cgmp-headcount-btn wc-cgmp-btn-minus" data-action="decrease">-</button>
                 <input type="number"
                        class="wc-cgmp-quantity-input"
                        name="quantity"
                        value="1"
                        min="1"
                        max="99"
                        aria-label="<?php esc_attr_e('Quantity', 'wc-carousel-grid-marketplace-and-pricing'); ?>">
                 <button type="button" class="wc-cgmp-headcount-btn wc-cgmp-btn-plus" data-action="increase">+</button>
             </div>
             <?php endif; ?>

              <?php if ($show_total) : ?>
              <div class="wc-cgmp-total">
                  <span class="wc-cgmp-total-label"><?php esc_html_e('Total', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                  <span class="wc-cgmp-total-price"
                        data-total="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>"
                        data-monthly-price="<?php echo esc_attr(number_format($monthly_price, 2, '.', '')); ?>">
                      <?php echo wc_price(number_format($default_price, 2, '.', '')); ?><?php echo $has_tiers ? '/mo' : ''; ?>
                  </span>
              </div>
              <?php endif; ?>

              <?php if ($enable_button_override && !empty($override_button_url)) : ?>
              <?php 
              $separator = (strpos($override_button_url, '?') !== false) ? '&' : '?';
              $override_url_base = $override_button_url;
              $override_url_with_param = '';
              
              if ($include_total_param) {
                  $override_url_with_param = $override_button_url . $separator . $total_url_param . '=';
              }
              ?>
              <?php if ($enable_above_button_link && !empty($above_link_url)) : ?>
              <div class="wc-cgmp-above-button-link">
                  <a href="<?php echo esc_url($above_link_url); ?>" 
                     class="wc-cgmp-link-above-btn"
                     <?php echo $above_link_open_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                      <?php if (!empty($above_link_icon)) : ?>
                          <span class="wc-cgmp-link-icon"><?php \Elementor\Icons_Manager::render_icon($above_link_icon, ['aria-hidden' => 'true']); ?></span>
                      <?php endif; ?>
                      <span class="wc-cgmp-link-text"><?php echo esc_html($above_link_text); ?> <?php if (!empty($above_link_highlight_text)) : ?><span class="wc-cgmp-link-highlight"><?php echo esc_html($above_link_highlight_text); ?></span><?php endif; ?></span>
                  </a>
              </div>
              <?php endif; ?>
              <a href="<?php echo esc_url($include_total_param ? $override_url_with_param . number_format($default_price, 2, '.', '') : $override_url_base); ?>"
                 class="wc-cgmp-add-to-cart wc-cgmp-override-button"
                 data-include-total-param="<?php echo $include_total_param ? 'true' : 'false'; ?>"
                 <?php if ($include_total_param) : ?>
                 data-override-url="<?php echo esc_url($override_url_with_param); ?>"
                 data-total-param="<?php echo esc_attr($total_url_param); ?>"
                 <?php endif; ?>
                 <?php echo $open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
                  <span class="wc-cgmp-btn-text"><?php echo esc_html($override_button_text); ?></span>
              </a>
             <?php else : ?>
             <button type="button"
                      class="wc-cgmp-add-to-cart"
                      data-product-id="<?php echo esc_attr($product_id); ?>"
                      data-tier-level="<?php echo esc_attr($default_tier_level); ?>"
                      data-price-type="<?php echo esc_attr($default_price_type); ?>">
                 <span class="dashicons dashicons-cart"></span>
                 <span class="wc-cgmp-btn-text"><?php esc_html_e('Add to Cart', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
             </button>
             <?php endif; ?>
        </div>
        <?php
        return ob_get_clean() ?: '';
    }

    public static function get_tier_color_class(int $tier_level): string
    {
        $colors = [
            1 => 'wc-cgmp-tier-entry',
            2 => 'wc-cgmp-tier-mid',
            3 => 'wc-cgmp-tier-expert',
        ];

        return $colors[$tier_level] ?? 'wc-cgmp-tier-default';
    }

    public static function calculate_tier_total(float $price, int $quantity, string $price_type): float
    {
        return $price * $quantity;
    }

    public static function get_default_tier(array $tiers): ?object
    {
        foreach ($tiers as $tier) {
            if ($tier->monthly_price > 0 || $tier->hourly_price > 0) {
                return $tier;
            }
        }
        return !empty($tiers) ? $tiers[0] : null;
    }
}
