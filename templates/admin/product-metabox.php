<?php
defined('ABSPATH') || exit;

$tooltips = [
    'enable' => __('When enabled, this product will appear in the marketplace with tiered pricing options.', 'wc-carousel-grid-marketplace-and-pricing'),
    'tier_name' => __('Customize the tier name or leave empty to use the default.', 'wc-carousel-grid-marketplace-and-pricing'),
    'monthly' => __('Monthly price for this tier. Leave empty if not offering monthly pricing.', 'wc-carousel-grid-marketplace-and-pricing'),
    'hourly' => __('Hourly rate for this tier. Leave empty if not offering hourly pricing.', 'wc-carousel-grid-marketplace-and-pricing'),
    'description' => __('Brief description shown to customers when selecting this tier.', 'wc-carousel-grid-marketplace-and-pricing'),
    'popular' => __('Displays a "Popular" badge on this product in the marketplace view.', 'wc-carousel-grid-marketplace-and-pricing'),
    'specialization' => __('Optional text displayed on the product card (e.g., "Senior Developer").', 'wc-carousel-grid-marketplace-and-pricing'),
];
?>
<div class="wc-cgmp-metabox">
    <div class="wc-cgmp-enable-section">
        <div class="wc-cgmp-toggle-wrap">
            <label class="wc-cgmp-toggle">
                <input type="checkbox" id="_wc_cgmp_enabled" name="_wc_cgmp_enabled" value="yes" <?php checked($enabled, true); ?>>
                <span class="wc-cgmp-toggle-slider"></span>
            </label>
        </div>
        <div class="wc-cgmp-toggle-content">
            <p class="wc-cgmp-toggle-title">
                <?php esc_html_e('Enable for Marketplace', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                <span class="wc-cgmp-tooltip">
                    <?php echo wc_cgmp_get_help_icon(); ?>
                    <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['enable']); ?></span>
                </span>
            </p>
            <p class="wc-cgmp-toggle-desc">
                <?php esc_html_e('Enable this product to appear in the marketplace with tiered pricing (Entry, Mid, Expert levels).', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </p>
        </div>
    </div>

    <div class="wc-cgmp-tier-pricing-section" <?php echo $enabled ? '' : 'style="display:none;"'; ?>>
        <div class="wc-cgmp-section-header">
            <h4 class="wc-cgmp-section-title">
                <?php echo wc_cgmp_get_tier_icon(); ?>
                <?php esc_html_e('Tier Pricing', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </h4>
        </div>

        <div class="wc-cgmp-tiers-grid">
            <?php for ($i = 1; $i <= 3; $i++) :
                $tier_icons = [
                    1 => wc_cgmp_get_entry_icon(),
                    2 => wc_cgmp_get_mid_icon(),
                    3 => wc_cgmp_get_expert_icon(),
                ];
            ?>
            <div class="wc-cgmp-tier-card wc-cgmp-tier-<?php echo $i; ?>" data-tier="<?php echo $i; ?>">
                <div class="wc-cgmp-tier-header">
                    <span class="wc-cgmp-tier-icon">
                        <?php echo $tier_icons[$i]; ?>
                    </span>
                    <h5 class="wc-cgmp-tier-name"><?php echo esc_html($tier_data[$i]['name'] ?: $default_names[$i]); ?></h5>
                </div>

                <div class="wc-cgmp-tier-body">
                    <div class="wc-cgmp-field">
                        <label for="wc_cgmp_tiers_<?php echo $i; ?>_name">
                            <?php esc_html_e('Name', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                            <span class="wc-cgmp-tooltip">
                                <?php echo wc_cgmp_get_help_icon(); ?>
                                <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['tier_name']); ?></span>
                            </span>
                        </label>
                        <input type="text"
                               id="wc_cgmp_tiers_<?php echo $i; ?>_name"
                               name="wc_cgmp_tiers[<?php echo $i; ?>][tier_name]"
                               value="<?php echo esc_attr($tier_data[$i]['name']); ?>"
                               placeholder="<?php echo esc_attr($default_names[$i]); ?>"
                               class="wc-cgmp-tier-name-input"
                               data-tier="<?php echo $i; ?>">
                        <input type="hidden" name="wc_cgmp_tiers[<?php echo $i; ?>][tier_level]" value="<?php echo $i; ?>">
                    </div>

                    <div class="wc-cgmp-price-row">
                        <div class="wc-cgmp-field wc-cgmp-price-field">
                            <label for="wc_cgmp_tiers_<?php echo $i; ?>_monthly">
                                <?php esc_html_e('Monthly', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                <span class="wc-cgmp-tooltip">
                                    <?php echo wc_cgmp_get_help_icon(); ?>
                                    <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['monthly']); ?></span>
                                </span>
                            </label>
                            <span class="currency">$</span>
                            <input type="number"
                                   id="wc_cgmp_tiers_<?php echo $i; ?>_monthly"
                                   name="wc_cgmp_tiers[<?php echo $i; ?>][monthly_price]"
                                   value="<?php echo esc_attr($tier_data[$i]['monthly_price']); ?>"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="wc-cgmp-price-input"
                                   data-tier="<?php echo $i; ?>"
                                   data-type="monthly">
                        </div>

                        <div class="wc-cgmp-field wc-cgmp-price-field">
                            <label for="wc_cgmp_tiers_<?php echo $i; ?>_hourly">
                                <?php esc_html_e('Hourly', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                <span class="wc-cgmp-tooltip">
                                    <?php echo wc_cgmp_get_help_icon(); ?>
                                    <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['hourly']); ?></span>
                                </span>
                            </label>
                            <span class="currency">$</span>
                            <input type="number"
                                   id="wc_cgmp_tiers_<?php echo $i; ?>_hourly"
                                   name="wc_cgmp_tiers[<?php echo $i; ?>][hourly_price]"
                                   value="<?php echo esc_attr($tier_data[$i]['hourly_price']); ?>"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00"
                                   class="wc-cgmp-price-input"
                                   data-tier="<?php echo $i; ?>"
                                   data-type="hourly">
                        </div>
                    </div>

                    <div class="wc-cgmp-price-preview" data-tier="<?php echo $i; ?>">
                        <div class="wc-cgmp-preview-label"><?php esc_html_e('Preview', 'wc-carousel-grid-marketplace-and-pricing'); ?></div>
                        <div class="wc-cgmp-preview-prices">
                            <?php if (!empty($tier_data[$i]['monthly_price'])) : ?>
                                <span class="wc-cgmp-preview-price monthly-preview">
                                    $<?php echo number_format((float)$tier_data[$i]['monthly_price'], 2); ?><span class="period">/mo</span>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($tier_data[$i]['hourly_price'])) : ?>
                                <span class="wc-cgmp-preview-price hourly-preview">
                                    $<?php echo number_format((float)$tier_data[$i]['hourly_price'], 2); ?><span class="period">/hr</span>
                                </span>
                            <?php endif; ?>
                            <?php if (empty($tier_data[$i]['monthly_price']) && empty($tier_data[$i]['hourly_price'])) : ?>
                                <span class="wc-cgmp-preview-price" style="color: #999; font-weight: 400;">
                                    <?php esc_html_e('Enter prices to preview', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="wc-cgmp-field">
                        <label for="wc_cgmp_tiers_<?php echo $i; ?>_description">
                            <?php esc_html_e('Description', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                            <span class="wc-cgmp-tooltip">
                                <?php echo wc_cgmp_get_help_icon(); ?>
                                <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['description']); ?></span>
                            </span>
                        </label>
                        <textarea id="wc_cgmp_tiers_<?php echo $i; ?>_description"
                                  name="wc_cgmp_tiers[<?php echo $i; ?>][description]"
                                  rows="2"
                                  placeholder="<?php esc_attr_e('Brief description of this tier...', 'wc-carousel-grid-marketplace-and-pricing'); ?>"><?php echo esc_textarea($tier_data[$i]['description']); ?></textarea>
                    </div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="wc-cgmp-display-section <?php echo $enabled ? '' : 'collapsed'; ?>" <?php echo $enabled ? '' : 'style="display:none;"'; ?>>
        <div class="wc-cgmp-display-header">
            <span class="wc-cgmp-display-title">
                <?php echo wc_cgmp_get_settings_icon(); ?>
                <?php esc_html_e('Display Options', 'wc-carousel-grid-marketplace-and-pricing'); ?>
            </span>
            <span class="wc-cgmp-display-toggle-icon">
                <?php echo wc_cgmp_get_chevron_icon(); ?>
            </span>
        </div>
        <div class="wc-cgmp-display-body">
            <div class="wc-cgmp-checkbox-field">
                <input type="checkbox" id="_wc_cgmp_popular" name="_wc_cgmp_popular" value="yes" <?php checked($popular, true); ?>>
                <div class="wc-cgmp-checkbox-label">
                    <span class="label-text">
                        <?php esc_html_e('Mark as Popular', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                        <span class="wc-cgmp-tooltip">
                            <?php echo wc_cgmp_get_help_icon(); ?>
                            <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['popular']); ?></span>
                        </span>
                    </span>
                    <span class="label-desc"><?php esc_html_e('Display a popular badge on this product in the marketplace.', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                </div>
            </div>

            <div class="wc-cgmp-spec-field">
                <div class="wc-cgmp-field">
                    <label for="_wc_cgmp_specialization">
                        <?php esc_html_e('Specialization', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                        <span class="wc-cgmp-tooltip">
                            <?php echo wc_cgmp_get_help_icon(); ?>
                            <span class="wc-cgmp-tooltip-text"><?php echo esc_html($tooltips['specialization']); ?></span>
                        </span>
                    </label>
                    <input type="text"
                           id="_wc_cgmp_specialization"
                           name="_wc_cgmp_specialization"
                           value="<?php echo esc_attr($specialization); ?>"
                           placeholder="<?php esc_attr_e('e.g., Senior Developer, Full-Stack Developer', 'wc-carousel-grid-marketplace-and-pricing'); ?>">
                </div>
            </div>
        </div>
    </div>
</div>
