<?php
defined('ABSPATH') || exit;

$atts = $atts ?? [];
$default_tier = (int) ($atts['selected_tier'] ?? $atts['tier'] ?? 1);
if ($default_tier === 0) {
    $default_tier = 1;
}
?>

<div class="wc-cgmp-filter-bar">
    <span class="wc-cgmp-filter-label"><?php esc_html_e('Experience Level:', 'wc-carousel-grid-marketplace'); ?></span>

    <div class="wc-cgmp-tier-filters">
        <button type="button"
                class="wc-cgmp-tier-btn wc-cgmp-tier-entry <?php echo $default_tier === 1 ? 'active' : ''; ?>"
                data-tier="1">
            <?php esc_html_e('Entry', 'wc-carousel-grid-marketplace'); ?>
        </button>

        <button type="button"
                class="wc-cgmp-tier-btn wc-cgmp-tier-mid <?php echo $default_tier === 2 ? 'active' : ''; ?>"
                data-tier="2">
            <?php esc_html_e('Mid', 'wc-carousel-grid-marketplace'); ?>
        </button>

        <button type="button"
                class="wc-cgmp-tier-btn wc-cgmp-tier-expert <?php echo $default_tier === 3 ? 'active' : ''; ?>"
                data-tier="3">
            <?php esc_html_e('Expert', 'wc-carousel-grid-marketplace'); ?>
        </button>
    </div>
</div>
