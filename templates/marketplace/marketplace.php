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
?>

<?php if (!empty($admin_notice)) : ?>
<?php echo $admin_notice; ?>
<?php endif; ?>

<div class="wc-cgmp-marketplace wc-cgmp-loading <?php echo esc_attr($class); ?>"
     data-columns="<?php echo esc_attr($columns); ?>"
     data-layout="<?php echo esc_attr($layout); ?>"
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
            <h2 class="wc-cgmp-section-title"><?php esc_html_e('Available Services', 'wc-carousel-grid-marketplace'); ?></h2>
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
             data-current-tier="0"
             data-show-tier-badge="<?php echo esc_attr($atts['show_tier_badge'] ?? 'true'); ?>"
             data-show-tier-description="<?php echo esc_attr($atts['show_tier_description'] ?? 'true'); ?>"
             data-show-search="<?php echo esc_attr($atts['show_search'] ?? 'false'); ?>"
             data-show-sidebar="<?php echo esc_attr($atts['show_sidebar'] ?? 'true'); ?>"
             data-show-filter="<?php echo esc_attr($atts['show_filter'] ?? 'true'); ?>">

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

        <?php if (($atts['infinite_scroll'] ?? 'false') !== 'true' && count($products) >= (int) $atts['limit']) : ?>
        <div class="wc-cgmp-load-more-wrap">
            <button type="button" class="wc-cgmp-load-more" data-offset="<?php echo esc_attr(count($products)); ?>">
                <?php esc_html_e('Load More', 'wc-carousel-grid-marketplace'); ?>
            </button>
        </div>
        <?php endif; ?>
    </main>
</div>
