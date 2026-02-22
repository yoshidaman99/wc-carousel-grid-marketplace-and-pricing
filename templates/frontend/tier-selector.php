<?php
defined('ABSPATH') || exit;

$tier_colors = [
    1 => 'entry',
    2 => 'mid',
    3 => 'expert',
];
?>
<div class="wc-cgmp-tier-selector" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
    <?php foreach ($tiers as $tier) : 
        $monthly = (float) ($tier->monthly_price ?? 0);
        $hourly = (float) ($tier->hourly_price ?? 0);
        if ($monthly <= 0 && $hourly <= 0) continue;
        
        $tier_class = $tier_colors[$tier->tier_level] ?? 'default';
    ?>
    <div class="wc-cgmp-tier-option wc-cgmp-tier-<?php echo esc_attr($tier_class); ?>"
         data-tier="<?php echo esc_attr($tier->tier_level); ?>"
         data-monthly="<?php echo esc_attr($monthly); ?>"
         data-hourly="<?php echo esc_attr($hourly); ?>"
         data-name="<?php echo esc_attr($tier->tier_name); ?>">
        
        <div class="wc-cgmp-tier-header">
            <span class="wc-cgmp-tier-name"><?php echo esc_html($tier->tier_name); ?></span>
        </div>
        
        <div class="wc-cgmp-tier-prices">
            <?php if ($monthly > 0) : ?>
            <div class="wc-cgmp-tier-price wc-cgmp-monthly">
                <span class="wc-cgmp-price-amount"><?php echo wc_price($monthly); ?></span>
                <span class="wc-cgmp-price-suffix">/mo</span>
            </div>
            <?php endif; ?>
            
            <?php if ($hourly > 0) : ?>
            <div class="wc-cgmp-tier-price wc-cgmp-hourly">
                <span class="wc-cgmp-price-amount"><?php echo wc_price($hourly); ?></span>
                <span class="wc-cgmp-price-suffix">/hr</span>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($tier->description)) : ?>
        <div class="wc-cgmp-tier-description">
            <?php echo wp_kses_post($tier->description); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
</div>

<script>
jQuery(document).ready(function($) {
    $('.wc-cgmp-tier-option').on('click', function() {
        var $this = $(this);
        var tier = $this.data('tier');
        var monthly = $this.data('monthly');
        var hourly = $this.data('hourly');
        var name = $this.data('name');
        var priceType = $('.wc-cgmp-price-type-switch input').is(':checked') ? 'hourly' : 'monthly';
        var price = priceType === 'hourly' ? hourly : monthly;
        
        $('.wc-cgmp-tier-option').removeClass('selected');
        $this.addClass('selected');
        
        $('#wc_cgmp_selected_tier').val(tier);
        $('#wc_cgmp_tier_name').val(name);
        $('#wc_cgmp_tier_price').val(price);
    });
    
    $('.wc-cgmp-tier-option:first').trigger('click');
});
</script>
