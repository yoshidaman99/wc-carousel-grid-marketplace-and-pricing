<?php

namespace WC_CGMP\Admin;

defined('ABSPATH') || exit;

class Product_Metabox
{
    public function __construct()
    {
        add_action('add_meta_boxes', [$this, 'add_metabox']);
        add_action('save_post_product', [$this, 'save_metabox'], 10, 2);
        add_action('delete_post', [$this, 'cleanup_tier_data']);
    }

    public function add_metabox(): void
    {
        add_meta_box(
            'wc_cgmp_marketplace_pricing',
            __('Marketplace & Pricing', 'wc-carousel-grid-marketplace-and-pricing'),
            [$this, 'render_metabox'],
            'product',
            'normal',
            'high'
        );
    }

    public function render_metabox(\WP_Post $post): void
    {
        wp_nonce_field('wc_cgmp_save_metabox', 'wc_cgmp_metabox_nonce');

        $enabled = get_post_meta($post->ID, '_wc_cgmp_enabled', true) === 'yes'
            || get_post_meta($post->ID, '_welp_enabled', true) === 'yes';

        $popular = get_post_meta($post->ID, '_wc_cgmp_popular', true) === 'yes'
            || get_post_meta($post->ID, '_wc_cgm_popular', true) === 'yes';

        $specialization = get_post_meta($post->ID, '_wc_cgmp_specialization', true)
            ?: get_post_meta($post->ID, '_wc_cgm_specialization', true);

        $learn_more_url = get_post_meta($post->ID, WC_CGMP_META_LEARN_MORE_URL, true) ?: '';
        $apply_now_url = get_post_meta($post->ID, WC_CGMP_META_APPLY_NOW_URL, true) ?: '';

        $repository = wc_cgmp()->get_service('repository');
        $tiers = $repository->get_tiers_by_product($post->ID);

        $default_names = [
            1 => 'Entry',
            2 => 'Mid',
            3 => 'Expert',
        ];

        $tier_data = [];
        for ($i = 1; $i <= 3; $i++) {
            $existing = array_filter($tiers, function ($t) use ($i) {
                return (int) $t->tier_level === $i;
            });
            $existing = reset($existing);

            $tier_data[$i] = [
                'name' => $existing ? $existing->tier_name : $default_names[$i],
                'monthly_price' => $existing ? $existing->monthly_price : '',
                'hourly_price' => $existing ? $existing->hourly_price : '',
                'description' => $existing ? $existing->description : '',
            ];
        }

        include WC_CGMP_PLUGIN_DIR . 'templates/admin/product-metabox.php';
    }

    public function save_metabox(int $post_id, \WP_Post $post): void
    {
        if (!isset($_POST['wc_cgmp_metabox_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['wc_cgmp_metabox_nonce'], 'wc_cgmp_save_metabox')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $enabled = isset($_POST['_wc_cgmp_enabled']) && $_POST['_wc_cgmp_enabled'] === 'yes';
        update_post_meta($post_id, '_wc_cgmp_enabled', $enabled ? 'yes' : 'no');

        $popular = isset($_POST['_wc_cgmp_popular']) && $_POST['_wc_cgmp_popular'] === 'yes';
        update_post_meta($post_id, '_wc_cgmp_popular', $popular ? 'yes' : 'no');

        if (isset($_POST['_wc_cgmp_specialization'])) {
            update_post_meta($post_id, '_wc_cgmp_specialization', sanitize_text_field($_POST['_wc_cgmp_specialization']));
        }

        if (isset($_POST['_wc_cgmp_learn_more_url']) && $_POST['_wc_cgmp_learn_more_url'] !== '') {
            update_post_meta($post_id, WC_CGMP_META_LEARN_MORE_URL, esc_url_raw($_POST['_wc_cgmp_learn_more_url']));
        } else {
            delete_post_meta($post_id, WC_CGMP_META_LEARN_MORE_URL);
        }

        if (isset($_POST['_wc_cgmp_apply_now_url']) && $_POST['_wc_cgmp_apply_now_url'] !== '') {
            update_post_meta($post_id, WC_CGMP_META_APPLY_NOW_URL, esc_url_raw($_POST['_wc_cgmp_apply_now_url']));
        } else {
            delete_post_meta($post_id, WC_CGMP_META_APPLY_NOW_URL);
        }

        if (!$enabled) {
            return;
        }

        if (!isset($_POST['wc_cgmp_tiers']) || !is_array($_POST['wc_cgmp_tiers'])) {
            return;
        }

        $repository = wc_cgmp()->get_service('repository');

        $tiers = [];
        for ($i = 1; $i <= 3; $i++) {
            if (isset($_POST['wc_cgmp_tiers'][$i])) {
                $tier = $_POST['wc_cgmp_tiers'][$i];
                $tiers[] = [
                    'tier_level' => $i,
                    'tier_name' => sanitize_text_field($tier['name'] ?? ''),
                    'monthly_price' => isset($tier['monthly_price']) && $tier['monthly_price'] !== '' ? floatval($tier['monthly_price']) : null,
                    'hourly_price' => isset($tier['hourly_price']) && $tier['hourly_price'] !== '' ? floatval($tier['hourly_price']) : null,
                    'description' => wp_kses_post($tier['description'] ?? ''),
                ];
            }
        }

        $repository->insert_tiers($post_id, $tiers);
    }

    public function cleanup_tier_data(int $post_id): void
    {
        if (!current_user_can('delete_post', $post_id)) {
            return;
        }

        $post = get_post($post_id);
        if (!$post || $post->post_type !== 'product') {
            return;
        }

        $repository = wc_cgmp()->get_service('repository');
        $repository->delete_tiers($post_id);
    }
}
