<?php
defined('ABSPATH') || exit;

$product = $product ?? null;
$product_id = $product ? $product->get_id() : 0;
$is_popular = $is_popular ?? false;
$specialization = $specialization ?? '';
$tiers = $tiers ?? [];
$atts = $atts ?? [];

$selected_tier = isset($atts['selected_tier']) ? (int) $atts['selected_tier'] : 0;
$tier_badges = [1 => 'Entry', 2 => 'Mid', 3 => 'Expert'];
$tier_classes = [1 => 'entry', 2 => 'mid', 3 => 'expert'];

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
?>

<div class="wc-cgmp-card" data-product-id="<?php echo esc_attr($product_id); ?>" <?php echo !empty($tiers) ? 'data-has-tiers="true"' : ''; ?>>

    <?php if ($is_popular) : ?>
    <span class="wc-cgmp-badge-popular">
        <?php esc_html_e('Popular', 'wc-carousel-grid-marketplace-and-pricing'); ?>
    </span>
    <?php endif; ?>

    <h3 class="wc-cgmp-card-title">
        <?php echo esc_html($product->get_name()); ?>
        <?php
        $show_tier_badge = ($atts['show_tier_badge'] ?? 'true') === 'true';
        if ($show_tier_badge && !empty($tiers) && $default_tier && isset($tier_badges[$default_tier->tier_level])) : ?>
        <span class="wc-cgmp-tier-badge <?php echo esc_attr($tier_classes[$default_tier->tier_level] ?? 'default'); ?>">
            <?php echo esc_html($tier_badges[$default_tier->tier_level]); ?>
        </span>
        <?php endif; ?>
    </h3>

    <p class="wc-cgmp-card-desc">
        <?php
        $description = $product->get_description();
        if ($description) {
            echo esc_html(wp_trim_words($description, 30, '...'));
        } else {
            echo esc_html(wp_trim_words($product->get_short_description(), 30, '...'));
        }
        ?>
    </p>

    <?php echo \WC_CGMP\Frontend\Marketplace::render_pricing_panel($product, $tiers, $atts); ?>

    <?php if (wc_cgmp_is_action_buttons_enabled($product_id)) :
        $learn_more_url = wc_cgmp_get_learn_more_url($product_id);
        $apply_now_url = wc_cgmp_get_apply_now_url($product_id);

        if ($learn_more_url || $apply_now_url) : ?>
    <div class="wc-cgmp-action-buttons">
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
        <?php endif;
    endif; ?>
</div>
