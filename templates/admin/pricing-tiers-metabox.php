<?php

defined('ABSPATH') || exit;
?>

<div class="wc-cgmp-metabox-container">

    <p class="wc-cgmp-enable-wrapper" style="margin-bottom: 15px;">
        <label for="_welp_enabled" style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
            <input type="checkbox"
                   id="_welp_enabled"
                   name="_welp_enabled"
                   value="yes"
                   <?php checked($welp_enabled, true); ?>>
            <strong><?php esc_html_e('Enable Experience Level Pricing', 'wc-experience-level-pricing'); ?></strong>
        </label>
        <span class="description" style="display: block; margin-left: 28px; margin-top: 5px;">
            <?php esc_html_e('Check this to add tiered pricing options (Entry, Mid, Expert) to this product.', 'wc-experience-level-pricing'); ?>
        </span>
    </p>

    <div id="welp_tiers_container" class="wc-cgmp-tiers-container" style="<?php echo $welp_enabled ? '' : 'display: none;'; ?>">

        <p class="description" style="margin-bottom: 15px;">
            <?php esc_html_e('Set up to 3 pricing tiers for this product. Each tier can have monthly and/or hourly pricing.', 'wc-experience-level-pricing'); ?>
        </p>

        <table class="widefat wc-cgmp-tiers-table" style="border: none;">
            <thead>
                <tr>
                    <th style="width: 60px;"><?php esc_html_e('Level', 'wc-experience-level-pricing'); ?></th>
                    <th style="width: 130px;"><?php esc_html_e('Tier Name', 'wc-experience-level-pricing'); ?></th>
                    <th style="width: 130px;"><?php esc_html_e('Monthly Price', 'wc-experience-level-pricing'); ?></th>
                    <th style="width: 130px;"><?php esc_html_e('Hourly Price', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Description', 'wc-experience-level-pricing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 1; $i <= 3; $i++) :
                    $tier = $tier_data[$i] ?? ['name' => '', 'monthly_price' => '', 'hourly_price' => '', 'description' => ''];
                ?>
                <tr class="wc-cgmp-tier-row wc-cgmp-tier-<?php echo esc_attr($i); ?>" data-tier="<?php echo esc_attr($i); ?>">
                    <td>
                        <strong class="wc-cgmp-tier-badge"><?php echo esc_html($i); ?></strong>
                        <input type="hidden" name="welp_tiers[<?php echo esc_attr($i); ?>][level]"
                               value="<?php echo esc_attr($i); ?>">
                    </td>
                    <td>
                        <input type="text"
                               name="welp_tiers[<?php echo esc_attr($i); ?>][name]"
                               value="<?php echo esc_attr($tier['name']); ?>"
                               class="widefat wc-cgmp-tier-name"
                               placeholder="<?php esc_attr_e('Tier name', 'wc-experience-level-pricing'); ?>">
                    </td>
                    <td>
                        <div class="wc-cgmp-price-input">
                            <span class="wc-cgmp-currency"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></span>
                            <input type="number"
                                   name="welp_tiers[<?php echo esc_attr($i); ?>][monthly_price]"
                                   value="<?php echo esc_attr($tier['monthly_price']); ?>"
                                   class="wc-cgmp-tier-price wc-cgmp-monthly-price"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00">
                            <span class="wc-cgmp-price-suffix">/mo</span>
                        </div>
                    </td>
                    <td>
                        <div class="wc-cgmp-price-input">
                            <span class="wc-cgmp-currency"><?php echo esc_html(get_woocommerce_currency_symbol()); ?></span>
                            <input type="number"
                                   name="welp_tiers[<?php echo esc_attr($i); ?>][hourly_price]"
                                   value="<?php echo esc_attr($tier['hourly_price']); ?>"
                                   class="wc-cgmp-tier-price wc-cgmp-hourly-price"
                                   step="0.01"
                                   min="0"
                                   placeholder="0.00">
                            <span class="wc-cgmp-price-suffix">/hr</span>
                        </div>
                    </td>
                    <td>
                        <textarea name="welp_tiers[<?php echo esc_attr($i); ?>][description]"
                                  class="widefat wc-cgmp-tier-description"
                                  rows="3"
                                  placeholder="<?php esc_attr_e('Description for this tier (supports HTML and shortcodes)', 'wc-experience-level-pricing'); ?>"><?php echo esc_textarea($tier['description']); ?></textarea>
                    </td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>

        <p class="description" style="margin-top: 10px;">
            <strong><?php esc_html_e('Note:', 'wc-experience-level-pricing'); ?></strong>
            <?php esc_html_e('Monthly and hourly prices are optional. Set at least one price per tier. The description field supports HTML and WordPress shortcodes.', 'wc-experience-level-pricing'); ?>
        </p>
    </div>
</div>
