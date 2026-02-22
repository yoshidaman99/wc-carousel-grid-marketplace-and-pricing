<?php

defined('ABSPATH') || exit;
?>

<div class="wc-cgmp-report">
    <h2><?php esc_html_e('Tier Sales by Product', 'wc-experience-level-pricing'); ?></h2>

    <?php if (empty($sales)) : ?>
        <p><?php esc_html_e('No tier sales data available for this period.', 'wc-experience-level-pricing'); ?></p>
    <?php else : ?>
        <table class="widefat" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Product', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Experience Level', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Price Type', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Units Sold', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Total Revenue', 'wc-experience-level-pricing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) :
                    $price_type_label = ($sale->price_type ?? 'monthly') === 'hourly' ? 'Hourly' : 'Monthly';
                    $price_type_badge_class = ($sale->price_type ?? 'monthly') === 'hourly' ? 'wc-cgmp-type-hourly' : 'wc-cgmp-type-monthly';
                ?>
                <tr>
                    <td>
                        <?php if ($sale->product_name) : ?>
                            <a href="<?php echo esc_url(get_edit_post_link($sale->product_id)); ?>">
                                <?php echo esc_html($sale->product_name); ?>
                            </a>
                        <?php else : ?>
                            <em><?php esc_html_e('Unknown Product', 'wc-experience-level-pricing'); ?></em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="wc-cgmp-tier-badge wc-cgmp-tier-<?php echo esc_attr($sale->tier_level); ?>">
                            <?php echo esc_html($sale->tier_name); ?>
                        </span>
                    </td>
                    <td>
                        <span class="wc-cgmp-type-badge <?php echo esc_attr($price_type_badge_class); ?>">
                            <?php echo esc_html($price_type_label); ?>
                        </span>
                    </td>
                    <td><?php echo esc_html($sale->total_quantity); ?></td>
                    <td><?php echo wc_price($sale->total_revenue); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <style>
            .wc-cgmp-tier-badge {
                display: inline-block;
                padding: 3px 10px;
                border-radius: 12px;
                font-size: 12px;
                font-weight: 600;
                color: #fff;
            }
            .wc-cgmp-tier-1 { background-color: #28a745; }
            .wc-cgmp-tier-2 { background-color: #ffc107; color: #333; }
            .wc-cgmp-tier-3 { background-color: #dc3545; }
            .wc-cgmp-type-badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 500;
            }
            .wc-cgmp-type-monthly {
                background-color: #e3f2fd;
                color: #1565c0;
            }
            .wc-cgmp-type-hourly {
                background-color: #fff3e0;
                color: #e65100;
            }
        </style>
    <?php endif; ?>
</div>
