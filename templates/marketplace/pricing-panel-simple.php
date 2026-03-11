<?php
defined('ABSPATH') || exit;

$product = $product ?? null;
$product_id = $product ? $product->get_id() : 0;
$atts = $atts ?? [];

$plugin = wc_cgm();
$repository = $plugin ? $plugin->get_service('repository') : null;
$tiers = $repository ? $repository->get_tiers_by_product($product_id) : [];

$default_tier = null;
foreach ($tiers as $tier) {
    if ($tier->hourly_price > 0 || $tier->monthly_price > 0) {
        $default_tier = $tier;
        break;
    }
}

$tiers_with_prices = 0;
foreach ($tiers as $tier) {
    if (($tier->hourly_price ?? 0) > 0 || ($tier->monthly_price ?? 0) > 0) {
        $tiers_with_prices++;
    }
}
$has_tiers = $tiers_with_prices > 0;
$has_multiple_tiers = $tiers_with_prices > 1;

$hourly_price = $default_tier->hourly_price ?? 0;
$monthly_price = $default_tier->monthly_price ?? 0;

if (!$has_tiers && $product) {
    $wc_price = (float) $product->get_price();
    if ($wc_price > 0) {
        $monthly_price = $wc_price;
        $default_price = $wc_price;
        $price_types = ['monthly'];
        $default_price_type = 'monthly';
    }
} else {
    $price_types = [];
    if ($monthly_price > 0) $price_types['monthly'] = true;
    if ($hourly_price > 0) $price_types['hourly'] = true;
    $price_types = array_keys($price_types);

    $default_price_type = in_array('monthly', $price_types) ? 'monthly' : ($price_types[0] ?? 'hourly');
    $default_price = $default_price_type === 'monthly' ? $monthly_price : $hourly_price;
}

$show_headcount = ($atts['show_headcount'] ?? 'true') === 'true';
$show_total = ($atts['show_total'] ?? 'true') === 'true';
$enable_button_override = ($atts['enable_button_override'] ?? 'false') === 'true';
$override_button_text = $atts['override_button_text'] ?? 'Get Quote';
$override_button_url = $atts['override_button_url'] ?? '';
$total_url_param = $atts['total_url_param'] ?? 'total';
$open_in_new_tab = ($atts['open_in_new_tab'] ?? 'true') === 'true';
?>

<div class="wc-cgmp-pricing-panel wc-cgmp-simple"
     data-product-id="<?php echo esc_attr($product_id); ?>"
     data-has-tiers="<?php echo $has_tiers ? 'true' : 'false'; ?>"
     data-has-multiple-tiers="<?php echo $has_multiple_tiers ? 'true' : 'false'; ?>"
     data-product-price="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>"
     data-default-tier="<?php echo esc_attr($default_tier->tier_level ?? 0); ?>"
     data-default-price-type="<?php echo esc_attr($default_price_type); ?>"
     <?php foreach ([1, 2, 3] as $level) :
        $tier_hourly = 0;
        $tier_monthly = 0;
        foreach ($tiers as $t) {
            if ($t->tier_level == $level) {
                $tier_hourly = $t->hourly_price ?? 0;
                $tier_monthly = $t->monthly_price ?? 0;
                break;
            }
        }
     ?>
     data-tier-<?php echo esc_attr($level); ?>-hourly="<?php echo esc_attr($tier_hourly); ?>"
     data-tier-<?php echo esc_attr($level); ?>-monthly="<?php echo esc_attr($tier_monthly); ?>"
     <?php endforeach; ?>>

    <?php if ($has_tiers && count($price_types) > 1) : ?>
    <div class="wc-cgmp-price-type-switch">
        <span class="wc-cgmp-switch-label <?php echo $default_price_type === 'monthly' ? 'active' : ''; ?>">
            <?php esc_html_e('Monthly', 'wc-carousel-grid-marketplace'); ?>
        </span>
        <label class="wc-cgmp-switch">
            <input type="checkbox" class="wc-cgmp-switch-input" <?php checked($default_price_type, 'hourly'); ?>>
            <span class="wc-cgmp-switch-slider"></span>
        </label>
        <span class="wc-cgmp-switch-label <?php echo $default_price_type === 'hourly' ? 'active' : ''; ?>">
            <?php esc_html_e('Hourly', 'wc-carousel-grid-marketplace'); ?>
        </span>
    </div>
    <?php endif; ?>

    <div class="wc-cgmp-pricing-amount">
        <span class="wc-cgmp-price-main" data-price="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>">
            <?php echo wc_price(number_format($default_price, 2, '.', '')); ?>
        </span>
        <span class="wc-cgmp-price-sub">
            <?php if ($default_price_type === 'monthly' && $hourly_price > 0) : ?>
                <?php echo wc_price(number_format($hourly_price, 2, '.', '')); ?>/hr
            <?php elseif ($default_price_type === 'hourly' && $monthly_price > 0) : ?>
                <?php echo wc_price(number_format($monthly_price, 2, '.', '')); ?>/mo
            <?php endif; ?>
        </span>
    </div>

    <?php if ($show_headcount) : ?>
    <div class="wc-cgmp-headcount">
        <span class="wc-cgmp-headcount-label"><?php esc_html_e('Headcount:', 'wc-carousel-grid-marketplace'); ?></span>
        <button type="button" class="wc-cgmp-headcount-btn wc-cgmp-btn-minus" data-action="decrease">-</button>
        <input type="number"
               class="wc-cgmp-quantity-input"
               name="quantity"
               value="1"
               min="1"
               max="99"
               aria-label="<?php esc_attr_e('Quantity', 'wc-carousel-grid-marketplace'); ?>">
        <button type="button" class="wc-cgmp-headcount-btn wc-cgmp-btn-plus" data-action="increase">+</button>
    </div>
    <?php endif; ?>

    <?php if ($show_total) : ?>
    <div class="wc-cgmp-total">
        <span class="wc-cgmp-total-label"><?php esc_html_e('Total', 'wc-carousel-grid-marketplace'); ?></span>
        <span class="wc-cgmp-total-price"
              data-total="<?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>"
              data-monthly-price="<?php echo esc_attr(number_format($monthly_price, 2, '.', '')); ?>">
            <?php echo wc_price(number_format($default_price, 2, '.', '')); ?><?php echo $has_tiers ? '/' . ($default_price_type === 'monthly' ? 'mo' : 'hr') : ''; ?>
        </span>
    </div>
    <?php endif; ?>

    <?php if ($enable_button_override && !empty($override_button_url)) : ?>
    <?php 
    $separator = (strpos($override_button_url, '?') !== false) ? '&' : '?';
    $override_url_with_param = $override_button_url . $separator . $total_url_param . '=';
    ?>
    <a href="<?php echo esc_url($override_url_with_param); ?><?php echo esc_attr(number_format($default_price, 2, '.', '')); ?>"
       class="wc-cgmp-add-to-cart wc-cgmp-override-button"
       data-override-url="<?php echo esc_url($override_url_with_param); ?>"
       data-total-param="<?php echo esc_attr($total_url_param); ?>"
       data-product-id="<?php echo esc_attr($product_id); ?>"
       <?php echo $open_in_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
        <span class="wc-cgmp-btn-text"><?php echo esc_html($override_button_text); ?></span>
    </a>
    <?php else : ?>
    <button type="button"
            class="wc-cgmp-add-to-cart"
            data-product-id="<?php echo esc_attr($product_id); ?>"
            data-tier-level="<?php echo esc_attr($default_tier->tier_level ?? 1); ?>"
            data-price-type="<?php echo esc_attr($default_price_type); ?>">
        <span class="dashicons dashicons-cart"></span>
        <span class="wc-cgmp-btn-text"><?php esc_html_e('Add to Cart', 'wc-carousel-grid-marketplace'); ?></span>
    </button>
    <?php endif; ?>
</div>
