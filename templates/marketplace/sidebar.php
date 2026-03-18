<?php
defined('ABSPATH') || exit;

$categories = $categories ?? [];
$atts = $atts ?? [];
?>

<ul class="wc-cgmp-category-nav">
    <?php foreach ($categories as $category) :
        $is_active = $category['id'] === 0;
        $icon_type = $category['icon_type'] ?? 'dashicon';
        $icon = $category['icon'] ?? 'grid';
        $icon_image_url = $category['icon_image_url'] ?? '';
        $icon_fontawesome = $category['icon_fontawesome'] ?? '';
        $icon_svg_code = $category['icon_svg_code'] ?? '';
    ?>
    <li class="wc-cgmp-category-item <?php echo $is_active ? 'active' : ''; ?>"
        data-category="<?php echo esc_attr($category['id']); ?>">

        <?php if ($icon_type === 'image' && !empty($icon_image_url)) : ?>
        <span class="wc-cgmp-category-icon wc-cgmp-category-icon-image">
            <img src="<?php echo esc_url($icon_image_url); ?>" alt="<?php echo esc_attr($category['name']); ?>" />
        </span>
        <?php elseif ($icon_type === 'fontawesome' && !empty($icon_fontawesome)) : ?>
        <span class="wc-cgmp-category-icon wc-cgmp-category-icon-fontawesome">
            <i class="<?php echo esc_attr($icon_fontawesome); ?>"></i>
        </span>
        <?php elseif ($icon_type === 'svg' && !empty($icon_svg_code)) : ?>
        <span class="wc-cgmp-category-icon wc-cgmp-category-icon-svg">
            <?php echo $icon_svg_code; ?>
        </span>
        <?php elseif ($icon) : ?>
        <span class="wc-cgmp-category-icon">
            <span class="dashicons dashicons-<?php echo esc_attr($icon); ?>"></span>
        </span>
        <?php endif; ?>

        <span class="wc-cgmp-category-name"><?php echo esc_html($category['name']); ?></span>

        <span class="wc-cgmp-category-count"><?php echo esc_html($category['count']); ?></span>
    </li>
    <?php endforeach; ?>
</ul>
