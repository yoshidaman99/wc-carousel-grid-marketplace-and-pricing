<?php

namespace WC_CGMP\Core;

defined('ABSPATH') || exit;

class Activator
{
    public function activate(): void
    {
        $this->create_tables();
        
        try {
            $this->migrate_from_welp();
        } catch (\Throwable $e) {
            $this->log_error('WELP migration failed: ' . $e->getMessage());
        }
        
        try {
            $this->migrate_from_cgm();
        } catch (\Throwable $e) {
            $this->log_error('CGM migration failed: ' . $e->getMessage());
        }
        
        $this->set_default_options();
        $this->schedule_events();
        $this->update_db_version();
        flush_rewrite_rules();
    }
    
    private function log_error(string $message): void
    {
        if (defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG) {
            error_log('[WC_CGMP] ' . $message);
        }
    }

    private function create_tables(): void
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $tiers_table = $wpdb->prefix . WC_CGMP_TABLE_TIERS;
        $sales_table = $wpdb->prefix . WC_CGMP_TABLE_SALES;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $sql_tiers = "CREATE TABLE $tiers_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            product_id bigint(20) unsigned NOT NULL,
            tier_level tinyint(1) unsigned NOT NULL,
            tier_name varchar(100) NOT NULL,
            monthly_price decimal(10,2) DEFAULT NULL,
            hourly_price decimal(10,2) DEFAULT NULL,
            description longtext DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY product_tier (product_id, tier_level),
            KEY product_id (product_id),
            KEY tier_level (tier_level)
        ) $charset_collate;";

        dbDelta($sql_tiers);

        $sql_sales = "CREATE TABLE $sales_table (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned NOT NULL,
            product_id bigint(20) unsigned NOT NULL,
            tier_level tinyint(1) unsigned NOT NULL,
            tier_name varchar(100) NOT NULL,
            price decimal(10,2) NOT NULL DEFAULT '0.00',
            price_type varchar(20) NOT NULL DEFAULT 'monthly',
            quantity int(11) NOT NULL DEFAULT 1,
            total decimal(10,2) NOT NULL DEFAULT '0.00',
            order_date datetime NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY order_id (order_id),
            KEY product_id (product_id),
            KEY tier_level (tier_level),
            KEY order_date (order_date),
            KEY price_type (price_type)
        ) $charset_collate;";

        dbDelta($sql_sales);
    }

    private function migrate_from_welp(): void
    {
        global $wpdb;

        if (get_option('wc_cgmp_migrated_from_welp', false)) {
            return;
        }

        $old_tiers_table = $wpdb->prefix . 'welp_product_tiers';
        $old_sales_table = $wpdb->prefix . 'welp_order_tier_sales';
        $new_tiers_table = $wpdb->prefix . WC_CGMP_TABLE_TIERS;
        $new_sales_table = $wpdb->prefix . WC_CGMP_TABLE_SALES;
        
        $old_tiers_escaped = '`' . str_replace('`', '``', $old_tiers_table) . '`';
        $old_sales_escaped = '`' . str_replace('`', '``', $old_sales_table) . '`';
        $new_tiers_escaped = '`' . str_replace('`', '``', $new_tiers_table) . '`';
        $new_sales_escaped = '`' . str_replace('`', '``', $new_sales_table) . '`';

        if ($wpdb->get_var("SHOW TABLES LIKE '$old_tiers_table'") === $old_tiers_table) {
            $wpdb->query("INSERT IGNORE INTO $new_tiers_escaped SELECT * FROM $old_tiers_escaped");

            $existing_meta = $wpdb->get_results(
                "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_welp_enabled' AND meta_value = 'yes'"
            );

            foreach ($existing_meta as $meta) {
                update_post_meta($meta->post_id, '_wc_cgmp_enabled', 'yes');
            }

            if ($wpdb->get_var("SHOW TABLES LIKE '$old_sales_table'") === $old_sales_table) {
                $wpdb->query("INSERT IGNORE INTO $new_sales_escaped SELECT * FROM $old_sales_escaped");
            }

            update_option('wc_cgmp_migrated_from_welp', true);

            $this->log_error('Migration from WELP completed. Tiers migrated: ' . count($existing_meta));
        }
    }

    private function migrate_from_cgm(): void
    {
        global $wpdb;

        if (get_option('wc_cgmp_migrated_from_cgm', false)) {
            return;
        }

        $popular_meta = $wpdb->get_results(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wc_cgm_popular'"
        );

        foreach ($popular_meta as $meta) {
            update_post_meta($meta->post_id, '_wc_cgmp_popular', $meta->meta_value);
        }

        $specialization_meta = $wpdb->get_results(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wc_cgm_specialization'"
        );

        foreach ($specialization_meta as $meta) {
            update_post_meta($meta->post_id, '_wc_cgmp_specialization', $meta->meta_value);
        }

        $options_to_migrate = [
            'wc_cgm_grid_columns' => 'wc_cgmp_grid_columns',
            'wc_cgm_cards_per_page' => 'wc_cgmp_cards_per_page',
            'wc_cgm_mobile_carousel' => 'wc_cgmp_mobile_carousel',
            'wc_cgm_show_sidebar' => 'wc_cgmp_show_sidebar',
            'wc_cgm_show_filter_bar' => 'wc_cgmp_show_filter_bar',
            'wc_cgm_enable_infinite_scroll' => 'wc_cgmp_enable_infinite_scroll',
            'wc_cgm_card_style' => 'wc_cgmp_card_style',
            'wc_cgm_popular_method' => 'wc_cgmp_popular_method',
            'wc_cgm_popular_threshold' => 'wc_cgmp_popular_threshold',
            'wc_cgm_popular_days' => 'wc_cgmp_popular_days',
            'wc_cgm_remove_data_on_uninstall' => 'wc_cgmp_remove_data_on_uninstall',
        ];

        foreach ($options_to_migrate as $old_key => $new_key) {
            $old_value = get_option($old_key);
            if ($old_value !== false && get_option($new_key) === false) {
                update_option($new_key, $old_value);
            }
        }

        update_option('wc_cgmp_migrated_from_cgm', true);
    }

    private function set_default_options(): void
    {
        $defaults = [
            'wc_cgmp_grid_columns' => 3,
            'wc_cgmp_mobile_carousel' => 'yes',
            'wc_cgmp_show_sidebar' => 'yes',
            'wc_cgmp_show_filter_bar' => 'yes',
            'wc_cgmp_cards_per_page' => 12,
            'wc_cgmp_enable_infinite_scroll' => 'no',
            'wc_cgmp_card_style' => 'default',
            'wc_cgmp_popular_method' => 'auto',
            'wc_cgmp_popular_threshold' => 5,
            'wc_cgmp_popular_days' => 30,
            'wc_cgmp_default_tier_names' => [
                1 => 'Entry',
                2 => 'Mid',
                3 => 'Expert',
            ],
        ];

        foreach ($defaults as $option => $value) {
            if (get_option($option) === false) {
                add_option($option, $value);
            }
        }
    }

    private function schedule_events(): void
    {
        if (!wp_next_scheduled('wc_cgmp_daily_popular_check')) {
            wp_schedule_event(time(), 'daily', 'wc_cgmp_daily_popular_check');
        }
    }

    private function update_db_version(): void
    {
        update_option('wc_cgmp_version', WC_CGMP_VERSION);
        update_option('wc_cgmp_db_version', WC_CGMP_DB_VERSION);
    }
}
