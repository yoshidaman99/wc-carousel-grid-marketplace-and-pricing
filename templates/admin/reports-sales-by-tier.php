<?php

defined('ABSPATH') || exit;
?>

<div class="wc-cgmp-report">
    <h2><?php esc_html_e('Sales by Experience Level', 'wc-experience-level-pricing'); ?></h2>

    <p class="wc-cgmp-report-summary">
        <strong><?php esc_html_e('Total Revenue:', 'wc-experience-level-pricing'); ?></strong>
        <?php echo wc_price($total_revenue); ?>
        <span style="margin-left: 20px;">
            <strong><?php esc_html_e('Total Orders:', 'wc-experience-level-pricing'); ?></strong>
            <?php echo esc_html($total_orders); ?>
        </span>
    </p>

    <?php if (empty($sales)) : ?>
        <p><?php esc_html_e('No tier sales data available for this period.', 'wc-experience-level-pricing'); ?></p>
    <?php else : ?>
        <table class="widefat" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th><?php esc_html_e('Experience Level', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Price Type', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Units Sold', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Total Revenue', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Orders', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('Avg. Price', 'wc-experience-level-pricing'); ?></th>
                    <th><?php esc_html_e('% of Revenue', 'wc-experience-level-pricing'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale) :
                    $percentage = $total_revenue > 0 ? ((float) $sale->total_revenue / $total_revenue) * 100 : 0;
                    $price_type_label = $sale->price_type === 'hourly' ? 'Hourly' : 'Monthly';
                    $price_type_badge_class = $sale->price_type === 'hourly' ? 'wc-cgmp-type-hourly' : 'wc-cgmp-type-monthly';
                ?>
                <tr>
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
                    <td><?php echo esc_html($sale->total_orders); ?></td>
                    <td><?php echo wc_price($sale->avg_price); ?></td>
                    <td><?php echo esc_html(number_format($percentage, 1)); ?>%</td>
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
