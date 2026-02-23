<?php
defined('ABSPATH') || exit;
?>
<div class="wc-cgmp-metabox">
    <p class="wc-cgmp-field">
        <label for="_wc_cgmp_enabled">
            <input type="checkbox" id="_wc_cgmp_enabled" name="_wc_cgmp_enabled" value="yes" <?php checked($enabled, true); ?>>
            <?php esc_html_e('Enable for Marketplace', 'wc-carousel-grid-marketplace-and-pricing'); ?>
        </label>
        <span class="wc-cgmp-description">
            <?php esc_html_e('Enable this product to appear in the marketplace with tiered pricing.', 'wc-carousel-grid-marketplace-and-pricing'); ?>
        </span>
    </p>

    <div class="wc-cgmp-tier-pricing-section" <?php echo $enabled ? '' : 'style="display:none;"'; ?>>
        <h4><?php esc_html_e('Tier Pricing', 'wc-carousel-grid-marketplace-and-pricing'); ?></h4>

        <div class="wc-cgmp-tiers-grid">
            <?php for ($i = 1; $i <= 3; $i++) : ?>
            <div class="wc-cgmp-tier-card wc-cgmp-tier-<?php echo $i; ?>">
                <h5><?php echo esc_html($tier_data[$i]['name'] ?: $default_names[$i]); ?></h5>

                <p class="wc-cgmp-field">
                    <label for="wc_cgmp_tiers_<?php echo $i; ?>_name"><?php esc_html_e('Name', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <input type="text"
                           id="wc_cgmp_tiers_<?php echo $i; ?>_name"
                           name="wc_cgmp_tiers[<?php echo $i; ?>][name]"
                           value="<?php echo esc_attr($tier_data[$i]['name']); ?>"
                           placeholder="<?php echo esc_attr($default_names[$i]); ?>">
                </p>

                <p class="wc-cgmp-field wc-cgmp-half">
                    <label for="wc_cgmp_tiers_<?php echo $i; ?>_monthly"><?php esc_html_e('Monthly Price ($)', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <input type="number"
                           id="wc_cgmp_tiers_<?php echo $i; ?>_monthly"
                           name="wc_cgmp_tiers[<?php echo $i; ?>][monthly_price]"
                           value="<?php echo esc_attr($tier_data[$i]['monthly_price']); ?>"
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                </p>

                <p class="wc-cgmp-field wc-cgmp-half">
                    <label for="wc_cgmp_tiers_<?php echo $i; ?>_hourly"><?php esc_html_e('Hourly Price ($)', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <input type="number"
                           id="wc_cgmp_tiers_<?php echo $i; ?>_hourly"
                           name="wc_cgmp_tiers[<?php echo $i; ?>][hourly_price]"
                           value="<?php echo esc_attr($tier_data[$i]['hourly_price']); ?>"
                           step="0.01"
                           min="0"
                           placeholder="0.00">
                </p>

                <p class="wc-cgmp-field">
                    <label for="wc_cgmp_tiers_<?php echo $i; ?>_description"><?php esc_html_e('Description', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <textarea id="wc_cgmp_tiers_<?php echo $i; ?>_description"
                              name="wc_cgmp_tiers[<?php echo $i; ?>][description]"
                              rows="2"
                              placeholder="<?php esc_attr_e('Brief description of this tier...', 'wc-carousel-grid-marketplace-and-pricing'); ?>"><?php echo esc_textarea($tier_data[$i]['description']); ?></textarea>
                </p>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="wc-cgmp-display-options-section" <?php echo $enabled ? '' : 'style="display:none;"'; ?>>
        <h4><?php esc_html_e('Display Options', 'wc-carousel-grid-marketplace-and-pricing'); ?></h4>

        <p class="wc-cgmp-field">
            <label for="_wc_cgmp_popular">
                <input type="checkbox" id="_wc_cgmp_popular" name="_wc_cgmp_popular" value="yes" <?php checked($popular, true); ?>>
                <?php esc_html_e('Mark as Popular', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </label>
            <span class="wc-cgmp-description">
                <?php esc_html_e('Display a popular badge on this product in the marketplace.', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </span>
        </p>

        <p class="wc-cgmp-field">
            <label for="_wc_cgmp_specialization"><?php esc_html_e('Specialization', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
            <input type="text"
                   id="_wc_cgmp_specialization"
                   name="_wc_cgmp_specialization"
                   value="<?php echo esc_attr($specialization); ?>"
                   placeholder="<?php esc_attr_e('e.g., Senior Developer, Full-Stack Developer', 'wc-carousel-grid-marketplace-and-pricing'); ?>">
            <span class="wc-cgmp-description">
                <?php esc_html_e('Optional specialization text displayed on the product card.', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </span>
        </p>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#_wc_cgmp_enabled').on('change', function() {
        var checked = $(this).is(':checked');
        $('.wc-cgmp-tier-pricing-section, .wc-cgmp-display-options-section').toggle(checked);
    });
});
</script>
