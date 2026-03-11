<?php
defined('ABSPATH') || exit;

$products = $products ?? [];
$categories = $categories ?? [];
$atts = $atts ?? [];
$repository = $repository ?? null;
$admin_notice = $admin_notice ?? '';

$columns = (int) ($atts['columns'] ?? 3);
$show_sidebar = ($atts['show_sidebar'] ?? 'true') === 'true';
$show_filter = ($atts['show_filter'] ?? 'true') === 'true';
$show_search = ($atts['show_search'] ?? 'false') === 'true';
$layout = $atts['layout'] ?? 'grid';
$mobile_carousel = ($atts['mobile_carousel'] ?? 'true') === 'true';
$class = $atts['class'] ?? '';
$load_all = (bool) get_option('wc_cgmp_load_all_products', false);

// Batch preload tier data for all products to eliminate N+1 queries
if ($repository && !empty($products)) {
    $product_ids = [];
    foreach ($products as $post) {
        if (is_object($post) && isset($post->ID)) {
            $product_ids[] = (int) $post->ID;
        } elseif (is_numeric($post)) {
            $product_ids[] = (int) $post;
        }
    }
    if (!empty($product_ids)) {
        $repository->preload_tiers($product_ids);
    }
}
?>

<?php if (!empty($admin_notice)) : ?>
<?php echo wp_kses_post($admin_notice); ?>
<?php endif; ?>

<div class="wc-cgmp-marketplace wc-cgmp-loading <?php echo esc_attr($class); ?>"
     data-columns="<?php echo esc_attr($columns); ?>"
     data-layout="<?php echo esc_attr($layout); ?>"
     data-limit="<?php echo esc_attr(is_array($atts['limit'] ?? 12) ? ($atts['limit'][0] ?? 12) : ($atts['limit'] ?? 12)); ?>"
     data-load-all="<?php echo esc_attr($load_all ? 'true' : 'false'); ?>"
     data-mobile-carousel="<?php echo esc_attr($mobile_carousel ? 'true' : 'false'); ?>">

    <!-- Loading Overlay -->
    <div class="wc-cgmp-loading-overlay">
        <div class="wc-cgmp-loading-spinner"></div>
        <div class="wc-cgmp-loading-text"><?php esc_html_e('Loading services...', 'wc-carousel-grid-marketplace'); ?></div>
    </div>

    <?php if ($show_sidebar && !empty($categories)) : ?>
    <aside class="wc-cgmp-sidebar">
        <div class="wc-cgmp-sidebar-header">
            <h3><?php esc_html_e('Service Categories', 'wc-carousel-grid-marketplace'); ?></h3>
            <p><?php esc_html_e('Browse by expertise area', 'wc-carousel-grid-marketplace'); ?></p>
        </div>

        <?php echo \WC_CGMP\Frontend\Marketplace::render_sidebar($categories, $atts); ?>
    </aside>
    <?php endif; ?>

    <main class="wc-cgmp-content">
        <?php if ($show_search) : ?>
        <div class="wc-cgmp-search-bar">
            <input type="search"
                   class="wc-cgmp-search-input"
                   placeholder="<?php esc_attr_e('Search services...', 'wc-carousel-grid-marketplace'); ?>"
                   aria-label="<?php esc_attr_e('Search services', 'wc-carousel-grid-marketplace'); ?>">
            <button type="button" class="wc-cgmp-search-btn">
                <span class="dashicons dashicons-search"></span>
            </button>
        </div>
        <?php endif; ?>

        <?php if ($show_filter) : ?>
        <?php echo \WC_CGMP\Frontend\Marketplace::render_filter_bar($atts); ?>
        <?php endif; ?>

        <div class="wc-cgmp-section-header">
            <h2 class="wc-cgmp-section-title" id="wc-cgmp-section-title"><?php esc_html_e('Available Services', 'wc-carousel-grid-marketplace'); ?></h2>
            <p class="wc-cgmp-section-count">
                <?php
                printf(
                    _n('%s role available', '%s roles available', count($products), 'wc-carousel-grid-marketplace'),
                    number_format_i18n(count($products))
                );
                ?>
            </p>
        </div>

        <div class="wc-cgmp-grid <?php echo $layout === 'hybrid' ? 'wc-cgmp-hybrid' : ''; ?>"
             data-current-category="0"
             data-current-tier="<?php echo esc_attr($atts['selected_tier'] ?? $atts['tier'] ?? 0); ?>"
             data-show-tier-badge="<?php echo esc_attr($atts['show_tier_badge'] ?? 'true'); ?>"
             data-show-tier-description="<?php echo esc_attr($atts['show_tier_description'] ?? 'true'); ?>"
             data-show-search="<?php echo esc_attr($atts['show_search'] ?? 'false'); ?>"
             data-show-sidebar="<?php echo esc_attr($atts['show_sidebar'] ?? 'true'); ?>"
             data-show-filter="<?php echo esc_attr($atts['show_filter'] ?? 'true'); ?>"
             data-columns="<?php echo esc_attr($columns); ?>"
             data-layout="<?php echo esc_attr($layout); ?>"
              data-show-popular-badge="<?php echo esc_attr($atts['show_popular_badge'] ?? 'true'); ?>"
               data-popular-badge-text="<?php echo esc_attr(is_array($atts['popular_badge_text'] ?? 'Popular') ? ($atts['popular_badge_text']['text'] ?? $atts['popular_badge_text'][0] ?? 'Popular') : ($atts['popular_badge_text'] ?? 'Popular')); ?>"
              data-show-popular-mark="<?php echo esc_attr($atts['show_popular_mark'] ?? 'false'); ?>"
               data-popular-mark-text="<?php echo esc_attr(is_array($atts['popular_mark_text'] ?? '‹popular›') ? ($atts['popular_mark_text']['text'] ?? $atts['popular_mark_text'][0] ?? '‹popular›') : ($atts['popular_mark_text'] ?? '‹popular›')); ?>"
               data-price-display-mode="<?php echo esc_attr($atts['price_display_mode'] ?? 'both'); ?>"
              data-show-price-prefix="<?php echo esc_attr($atts['show_price_prefix'] ?? 'false'); ?>"
              data-price-prefix-text="<?php echo esc_attr(is_array($atts['price_prefix_text'] ?? '') ? ($atts['price_prefix_text']['text'] ?? $atts['price_prefix_text'][0] ?? '') : ($atts['price_prefix_text'] ?? '')); ?>"
             data-price-prefix-separator="<?php echo esc_attr($atts['price_prefix_separator'] ?? '|'); ?>"
             data-price-prefix-position="<?php echo esc_attr($atts['price_prefix_position'] ?? 'inline'); ?>"
             data-show-headcount="<?php echo esc_attr($atts['show_headcount'] ?? 'true'); ?>"
             data-show-total="<?php echo esc_attr($atts['show_total'] ?? 'true'); ?>"
             data-enable-button-override="<?php echo esc_attr($atts['enable_button_override'] ?? 'false'); ?>"
              data-override-button-text="<?php echo esc_attr(is_array($atts['override_button_text'] ?? 'Get Quote') ? ($atts['override_button_text']['text'] ?? $atts['override_button_text'][0] ?? 'Get Quote') : ($atts['override_button_text'] ?? 'Get Quote')); ?>"
             data-override-button-url="<?php echo esc_attr(is_array($atts['override_button_url'] ?? '') ? ($atts['override_button_url']['url'] ?? '') : ($atts['override_button_url'] ?? '')); ?>"
             data-include-total-param="<?php echo esc_attr($atts['include_total_param'] ?? 'true'); ?>"
             data-total-url-param="<?php echo esc_attr($atts['total_url_param'] ?? 'total'); ?>"
             data-open-in-new-tab="<?php echo esc_attr($atts['open_in_new_tab'] ?? 'true'); ?>"
              data-enable-above-button-link="<?php echo esc_attr($atts['enable_above_button_link'] ?? 'false'); ?>"
              data-above-link-icon="<?php echo esc_attr(is_string($atts['above_link_icon'] ?? '') ? $atts['above_link_icon'] : ''); ?>"
               data-above-link-text="<?php echo esc_attr(is_array($atts['above_link_text'] ?? '') ? ($atts['above_link_text']['text'] ?? $atts['above_link_text'][0] ?? '') : ($atts['above_link_text'] ?? '')); ?>"
              data-above-link-url="<?php echo esc_attr(is_array($atts['above_link_url'] ?? '') ? ($atts['above_link_url']['url'] ?? '') : ($atts['above_link_url'] ?? '')); ?>"
              data-above-link-highlight-text="<?php echo esc_attr(is_array($atts['above_link_highlight_text'] ?? '') ? ($atts['above_link_highlight_text']['text'] ?? $atts['above_link_highlight_text'][0] ?? '') : ($atts['above_link_highlight_text'] ?? '')); ?>"
             data-above-link-open-new-tab="<?php echo esc_attr($atts['above_link_open_new_tab'] ?? 'true'); ?>"
             data-orderby="<?php echo esc_attr($atts['orderby'] ?? 'date'); ?>"
             data-order="<?php echo esc_attr($atts['order'] ?? 'DESC'); ?>">

            <?php foreach ($products as $product_id) :
                $product = wc_get_product($product_id);
                if (!$product) continue;
                echo \WC_CGMP\Frontend\Marketplace::render_product_card($product, $atts, $repository);
            endforeach; ?>

        </div>

        <?php if (empty($products)) : ?>
        <div class="wc-cgmp-no-products">
            <p><?php esc_html_e('No services found matching your criteria.', 'wc-carousel-grid-marketplace'); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!$load_all && ($atts['infinite_scroll'] ?? 'false') !== 'true' && count($products) >= (int) $atts['limit']) : ?>
        <div class="wc-cgmp-load-more-wrap">
            <button type="button" class="wc-cgmp-load-more" data-offset="<?php echo esc_attr(count($products)); ?>">
                <?php esc_html_e('Load More', 'wc-carousel-grid-marketplace'); ?>
            </button>
        </div>
        <?php endif; ?>
    </main>
</div>
