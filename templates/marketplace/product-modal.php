<?php
defined('ABSPATH') || exit;

$product = $product ?? null;
$product_id = $product ? $product->get_id() : 0;
$atts = $atts ?? [];

$modal_description = get_post_meta($product_id, '_wc_cgmp_modal_description', true) ?: '';
$key_responsibilities = get_post_meta($product_id, '_wc_cgmp_key_responsibilities', true);
if (!is_array($key_responsibilities)) {
    $key_responsibilities = [];
}

$responsibilities_title = $atts['modal_responsibilities_title'] ?? __('Key Responsibilities', 'wc-carousel-grid-marketplace-and-pricing');
$icon_html = $atts['modal_responsibilities_icon_html'] ?? wc_cgmp_get_check_icon();
$icon_color = $atts['modal_responsibilities_icon_color'] ?? '#dc2626';
$icon_size = $atts['modal_responsibilities_icon_size'] ?? 16;
?>

<div class="wc-cgmp-modal-overlay" data-product-id="<?php echo esc_attr($product_id); ?>">
    <div class="wc-cgmp-modal">
        <button type="button" class="wc-cgmp-modal-close" aria-label="<?php esc_attr_e('Close modal', 'wc-carousel-grid-marketplace-and-pricing'); ?>">
            <span class="wc-cgmp-modal-close-icon">&times;</span>
        </button>
        
        <div class="wc-cgmp-modal-content">
            <h2 class="wc-cgmp-modal-title"><?php echo esc_html($product->get_name()); ?></h2>
            
            <?php if (!empty($modal_description)) : ?>
            <div class="wc-cgmp-modal-description">
                <?php echo wp_kses_post($modal_description); ?>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($key_responsibilities)) : ?>
            <div class="wc-cgmp-modal-responsibilities">
                <h3 class="wc-cgmp-modal-section-title"><?php echo esc_html($responsibilities_title); ?></h3>
                <ul class="wc-cgmp-responsibilities-list">
                    <?php foreach ($key_responsibilities as $item) : ?>
                    <li class="wc-cgmp-responsibility-item">
                        <span class="wc-cgmp-responsibility-icon" style="color: <?php echo esc_attr($icon_color); ?>; width: <?php echo esc_attr($icon_size); ?>px; height: <?php echo esc_attr($icon_size); ?>px;">
                            <?php echo $icon_html; ?>
                        </span>
                        <span class="wc-cgmp-responsibility-text"><?php echo esc_html($item); ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
