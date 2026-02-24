<?php

namespace WC_CGMP\Core;

defined('ABSPATH') || exit;

class Uninstaller
{
    public static function uninstall(): void
    {
        if (!defined('WP_UNINSTALL_PLUGIN')) {
            return;
        }
        
        if (get_option('wc_cgmp_remove_data_on_uninstall', false)) {
            self::drop_tables();
            self::remove_options();
            self::remove_post_meta();
        }
    }

    private static function drop_tables(): void
    {
        global $wpdb;

        $tiers_table = $wpdb->prefix . WC_CGMP_TABLE_TIERS;
        $sales_table = $wpdb->prefix . WC_CGMP_TABLE_SALES;
        
        $tiers_table_escaped = '`' . str_replace('`', '``', $tiers_table) . '`';
        $sales_table_escaped = '`' . str_replace('`', '``', $sales_table) . '`';

        $wpdb->query("DROP TABLE IF EXISTS $tiers_table_escaped");
        $wpdb->query("DROP TABLE IF EXISTS $sales_table_escaped");
    }

    private static function remove_options(): void
    {
        global $wpdb;

        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wc_cgmp_%'"
        );

        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'wc_cgm_%'"
        );

        $wpdb->query(
            "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'welp_%'"
        );
    }

    private static function remove_post_meta(): void
    {
        global $wpdb;

        $meta_keys = [
            '_wc_cgmp_enabled',
            '_wc_cgmp_popular',
            '_wc_cgmp_specialization',
            '_wc_cgmp_learn_more_url',
            '_wc_cgmp_apply_now_url',
            '_wc_cgmp_action_buttons_enabled',
            '_welp_enabled',
            '_wc_cgm_popular',
            '_wc_cgm_specialization',
        ];

        foreach ($meta_keys as $key) {
            $wpdb->query(
                $wpdb->prepare(
                    "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s",
                    $key
                )
            );
        }
    }
}
