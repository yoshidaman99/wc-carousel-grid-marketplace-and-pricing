<?php

namespace WC_CGMP\Frontend;

defined('ABSPATH') || exit;

class Category_Icon
{
    private static $instance = null;
    private const META_ICON_TYPE = 'wc_cgmp_icon_type';
    private const META_ICON_DASHICON = 'wc_cgmp_icon_dashicon';
    private const META_ICON_IMAGE_ID = 'wc_cgmp_icon_image_id';
    private const META_ICON_FONTAWESOME = 'wc_cgmp_icon_fontawesome';
    private const META_ICON_SVG_CODE = 'wc_cgmp_icon_svg_code';

    public static function get_instance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function get_icon(int $term_id): array
    {
        $type = get_term_meta($term_id, self::META_ICON_TYPE, true) ?: 'dashicon';
        
        $icon = [
            'type' => $type,
            'dashicon' => '',
            'image_id' => 0,
            'image_url' => '',
            'image_alt' => '',
            'fontawesome' => '',
            'svg_code' => '',
        ];

        if ($type === 'image') {
            $image_id = (int) get_term_meta($term_id, self::META_ICON_IMAGE_ID, true);
            if ($image_id) {
                $icon['image_id'] = $image_id;
                $icon['image_url'] = wp_get_attachment_image_url($image_id, 'thumbnail') ?: '';
                $icon['image_alt'] = get_post_meta($image_id, '_wp_attachment_image_alt', true) ?: '';
            }
        } elseif ($type === 'fontawesome') {
            $icon['fontawesome'] = get_term_meta($term_id, self::META_ICON_FONTAWESOME, true) ?: 'fa-solid fa-store';
        } elseif ($type === 'svg') {
            $icon['svg_code'] = get_term_meta($term_id, self::META_ICON_SVG_CODE, true) ?: '';
        } else {
            $dashicon = get_term_meta($term_id, self::META_ICON_DASHICON, true) ?: 'grid';
            $icon['dashicon'] = $dashicon;
        }

        return $icon;
    }

    public function render(int $term_id, array $args = []): string
    {
        $defaults = [
            'size' => 24,
            'class' => '',
            'link' => false,
            'link_url' => '',
            'echo' => false,
        ];

        $args = wp_parse_args($args, $defaults);
        $icon = $this->get_icon($term_id);

        if (empty($icon['type'])) {
            return '';
        }

        $size = (int) $args['size'];
        $class = sanitize_html_class($args['class']);
        $wrapper_class = 'wc-cgmp-cat-icon ' . $class;

        $html = '<span class="' . esc_attr(trim($wrapper_class)) . '">';

        if ($icon['type'] === 'image' && !empty($icon['image_url'])) {
            $html .= sprintf(
                '<img src="%s" alt="%s" width="%d" height="%d" class="wc-cgmp-cat-icon-img" />',
                esc_url($icon['image_url']),
                esc_attr($icon['image_alt']),
                $size,
                $size
            );
        } elseif ($icon['type'] === 'fontawesome' && !empty($icon['fontawesome'])) {
            $html .= sprintf(
                '<i class="%s" style="font-size:%dpx;"></i>',
                esc_attr($icon['fontawesome']),
                $size
            );
        } elseif ($icon['type'] === 'svg' && !empty($icon['svg_code'])) {
            $html .= $icon['svg_code'];
        } else {
            $dashicon = $icon['dashicon'] ?: 'grid';
            $html .= sprintf(
                '<span class="dashicons dashicons-%s" style="font-size:%dpx;width:%dpx;height:%dpx;"></span>',
                esc_attr($dashicon),
                $size,
                $size,
                $size
            );
        }

        $html .= '</span>';

        if ($args['link']) {
            $url = $args['link_url'];
            if (empty($url) && $term_id > 0) {
                $url = get_term_link($term_id, 'product_cat');
            }
            
            if (!is_wp_error($url) && !empty($url)) {
                $html = sprintf(
                    '<a href="%s" class="wc-cgmp-cat-icon-link">%s</a>',
                    esc_url($url),
                    $html
                );
            }
        }

        if ($args['echo']) {
            echo $html;
            return '';
        }

        return $html;
    }

    public function get_url(int $term_id): string
    {
        $icon = $this->get_icon($term_id);

        if ($icon['type'] === 'image' && !empty($icon['image_url'])) {
            return $icon['image_url'];
        }

        return '';
    }

    public function get_dashicon(int $term_id): string
    {
        $icon = $this->get_icon($term_id);

        if ($icon['type'] === 'dashicon') {
            return $icon['dashicon'];
        }

        return '';
    }

    public function get_icon_html(int $term_id, int $size = 24): string
    {
        return $this->render($term_id, ['size' => $size]);
    }

    public static function get_available_dashicons(): array
    {
        return [
            'grid' => __('Grid', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-tools' => __('Tools', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-settings' => __('Settings', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-users' => __('Users', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-home' => __('Home', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-site' => __('Site', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-generic' => __('Generic', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-collapse' => __('Collapse', 'wc-carousel-grid-marketplace-and-pricing'),
            'analytics' => __('Analytics', 'wc-carousel-grid-marketplace-and-pricing'),
            'art' => __('Art', 'wc-carousel-grid-marketplace-and-pricing'),
            'awards' => __('Awards', 'wc-carousel-grid-marketplace-and-pricing'),
            'backup' => __('Backup', 'wc-carousel-grid-marketplace-and-pricing'),
            'book' => __('Book', 'wc-carousel-grid-marketplace-and-pricing'),
            'book-alt' => __('Book Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'building' => __('Building', 'wc-carousel-grid-marketplace-and-pricing'),
            'businessperson' => __('Business Person', 'wc-carousel-grid-marketplace-and-pricing'),
            'calendar' => __('Calendar', 'wc-carousel-grid-marketplace-and-pricing'),
            'calendar-alt' => __('Calendar Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'camera' => __('Camera', 'wc-carousel-grid-marketplace-and-pricing'),
            'cart' => __('Cart', 'wc-carousel-grid-marketplace-and-pricing'),
            'category' => __('Category', 'wc-carousel-grid-marketplace-and-pricing'),
            'chart-area' => __('Chart Area', 'wc-carousel-grid-marketplace-and-pricing'),
            'chart-bar' => __('Chart Bar', 'wc-carousel-grid-marketplace-and-pricing'),
            'chart-line' => __('Chart Line', 'wc-carousel-grid-marketplace-and-pricing'),
            'chart-pie' => __('Chart Pie', 'wc-carousel-grid-marketplace-and-pricing'),
            'clock' => __('Clock', 'wc-carousel-grid-marketplace-and-pricing'),
            'cloud' => __('Cloud', 'wc-carousel-grid-marketplace-and-pricing'),
            'color-picker' => __('Color Picker', 'wc-carousel-grid-marketplace-and-pricing'),
            'cover-image' => __('Cover Image', 'wc-carousel-grid-marketplace-and-pricing'),
            'dashboard' => __('Dashboard', 'wc-carousel-grid-marketplace-and-pricing'),
            'desktop' => __('Desktop', 'wc-carousel-grid-marketplace-and-pricing'),
            'download' => __('Download', 'wc-carousel-grid-marketplace-and-pricing'),
            'edit' => __('Edit', 'wc-carousel-grid-marketplace-and-pricing'),
            'email' => __('Email', 'wc-carousel-grid-marketplace-and-pricing'),
            'email-alt' => __('Email Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'external' => __('External', 'wc-carousel-grid-marketplace-and-pricing'),
            'feedback' => __('Feedback', 'wc-carousel-grid-marketplace-and-pricing'),
            'filter' => __('Filter', 'wc-carousel-grid-marketplace-and-pricing'),
            'flag' => __('Flag', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-image' => __('Image', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-video' => __('Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-audio' => __('Audio', 'wc-carousel-grid-marketplace-and-pricing'),
            'groups' => __('Groups', 'wc-carousel-grid-marketplace-and-pricing'),
            'hammer' => __('Hammer', 'wc-carousel-grid-marketplace-and-pricing'),
            'heart' => __('Heart', 'wc-carousel-grid-marketplace-and-pricing'),
            'id' => __('ID', 'wc-carousel-grid-marketplace-and-pricing'),
            'id-alt' => __('ID Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'images-alt' => __('Images', 'wc-carousel-grid-marketplace-and-pricing'),
            'info' => __('Info', 'wc-carousel-grid-marketplace-and-pricing'),
            'laptop' => __('Laptop', 'wc-carousel-grid-marketplace-and-pricing'),
            'layout' => __('Layout', 'wc-carousel-grid-marketplace-and-pricing'),
            'lightbulb' => __('Lightbulb', 'wc-carousel-grid-marketplace-and-pricing'),
            'list-view' => __('List View', 'wc-carousel-grid-marketplace-and-pricing'),
            'location' => __('Location', 'wc-carousel-grid-marketplace-and-pricing'),
            'location-alt' => __('Location Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'lock' => __('Lock', 'wc-carousel-grid-marketplace-and-pricing'),
            'marker' => __('Marker', 'wc-carousel-grid-marketplace-and-pricing'),
            'megaphone' => __('Megaphone', 'wc-carousel-grid-marketplace-and-pricing'),
            'menu' => __('Menu', 'wc-carousel-grid-marketplace-and-pricing'),
            'migrate' => __('Migrate', 'wc-carousel-grid-marketplace-and-pricing'),
            'money' => __('Money', 'wc-carousel-grid-marketplace-and-pricing'),
            'money-alt' => __('Money Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'move' => __('Move', 'wc-carousel-grid-marketplace-and-pricing'),
            'nametag' => __('Name Tag', 'wc-carousel-grid-marketplace-and-pricing'),
            'networking' => __('Networking', 'wc-carousel-grid-marketplace-and-pricing'),
            'no' => __('No', 'wc-carousel-grid-marketplace-and-pricing'),
            'no-alt' => __('No Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'palmtree' => __('Palm Tree', 'wc-carousel-grid-marketplace-and-pricing'),
            'paperclip' => __('Paperclip', 'wc-carousel-grid-marketplace-and-pricing'),
            'phone' => __('Phone', 'wc-carousel-grid-marketplace-and-pricing'),
            'playlist-audio' => __('Playlist Audio', 'wc-carousel-grid-marketplace-and-pricing'),
            'playlist-video' => __('Playlist Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'plugins-checked' => __('Plugins', 'wc-carousel-grid-marketplace-and-pricing'),
            'portfolio' => __('Portfolio', 'wc-carousel-grid-marketplace-and-pricing'),
            'post-status' => __('Post Status', 'wc-carousel-grid-marketplace-and-pricing'),
            'pressthis' => __('Press This', 'wc-carousel-grid-marketplace-and-pricing'),
            'products' => __('Products', 'wc-carousel-grid-marketplace-and-pricing'),
            'randomize' => __('Randomize', 'wc-carousel-grid-marketplace-and-pricing'),
            'redo' => __('Redo', 'wc-carousel-grid-marketplace-and-pricing'),
            'rest-api' => __('REST API', 'wc-carousel-grid-marketplace-and-pricing'),
            'rss' => __('RSS', 'wc-carousel-grid-marketplace-and-pricing'),
            'saved' => __('Saved', 'wc-carousel-grid-marketplace-and-pricing'),
            'schedule' => __('Schedule', 'wc-carousel-grid-marketplace-and-pricing'),
            'screenoptions' => __('Screen Options', 'wc-carousel-grid-marketplace-and-pricing'),
            'search' => __('Search', 'wc-carousel-grid-marketplace-and-pricing'),
            'share' => __('Share', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-alt' => __('Share Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'shield' => __('Shield', 'wc-carousel-grid-marketplace-and-pricing'),
            'smartphone' => __('Smartphone', 'wc-carousel-grid-marketplace-and-pricing'),
            'smiley' => __('Smiley', 'wc-carousel-grid-marketplace-and-pricing'),
            'sorting' => __('Sorting', 'wc-carousel-grid-marketplace-and-pricing'),
            'sos' => __('SOS', 'wc-carousel-grid-marketplace-and-pricing'),
            'star-empty' => __('Star Empty', 'wc-carousel-grid-marketplace-and-pricing'),
            'star-filled' => __('Star Filled', 'wc-carousel-grid-marketplace-and-pricing'),
            'star-half' => __('Star Half', 'wc-carousel-grid-marketplace-and-pricing'),
            'store' => __('Store', 'wc-carousel-grid-marketplace-and-pricing'),
            'tablet' => __('Tablet', 'wc-carousel-grid-marketplace-and-pricing'),
            'tag' => __('Tag', 'wc-carousel-grid-marketplace-and-pricing'),
            'tagcloud' => __('Tag Cloud', 'wc-carousel-grid-marketplace-and-pricing'),
            'testimonial' => __('Testimonial', 'wc-carousel-grid-marketplace-and-pricing'),
            'text' => __('Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'thumbs-up' => __('Thumbs Up', 'wc-carousel-grid-marketplace-and-pricing'),
            'thumbs-down' => __('Thumbs Down', 'wc-carousel-grid-marketplace-and-pricing'),
            'tickets' => __('Tickets', 'wc-carousel-grid-marketplace-and-pricing'),
            'tickets-alt' => __('Tickets Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'translation' => __('Translation', 'wc-carousel-grid-marketplace-and-pricing'),
            'trash' => __('Trash', 'wc-carousel-grid-marketplace-and-pricing'),
            'twitter' => __('Twitter', 'wc-carousel-grid-marketplace-and-pricing'),
            'undo' => __('Undo', 'wc-carousel-grid-marketplace-and-pricing'),
            'universal-access' => __('Universal Access', 'wc-carousel-grid-marketplace-and-pricing'),
            'update' => __('Update', 'wc-carousel-grid-marketplace-and-pricing'),
            'upload' => __('Upload', 'wc-carousel-grid-marketplace-and-pricing'),
            'vault' => __('Vault', 'wc-carousel-grid-marketplace-and-pricing'),
            'video' => __('Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'warning' => __('Warning', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-add-page' => __('Add Page', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-comments' => __('Comments', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-learn-more' => __('Learn More', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-view-site' => __('View Site', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-widgets-menus' => __('Widgets/Menus', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-write-blog' => __('Write Blog', 'wc-carousel-grid-marketplace-and-pricing'),
            'wordpress' => __('WordPress', 'wc-carousel-grid-marketplace-and-pricing'),
            'wordpress-alt' => __('WordPress Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'yes' => __('Yes', 'wc-carousel-grid-marketplace-and-pricing'),
            'yes-alt' => __('Yes Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-left' => __('Align Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-center' => __('Align Center', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-right' => __('Align Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-none' => __('Align None', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-full-width' => __('Full Width', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-pull-left' => __('Pull Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'align-pull-right' => __('Pull Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-up' => __('Arrow Up', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-down' => __('Arrow Down', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-left' => __('Arrow Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-right' => __('Arrow Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-up-alt' => __('Arrow Up Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-down-alt' => __('Arrow Down Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-left-alt' => __('Arrow Left Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-right-alt' => __('Arrow Right Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-up-alt2' => __('Arrow Up Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-down-alt2' => __('Arrow Down Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-left-alt2' => __('Arrow Left Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-right-alt2' => __('Arrow Right Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-up-double' => __('Arrow Up Double', 'wc-carousel-grid-marketplace-and-pricing'),
            'arrow-down-double' => __('Arrow Down Double', 'wc-carousel-grid-marketplace-and-pricing'),
            'sort' => __('Sort', 'wc-carousel-grid-marketplace-and-pricing'),
            'sortable' => __('Sortable', 'wc-carousel-grid-marketplace-and-pricing'),
            'collapse' => __('Collapse', 'wc-carousel-grid-marketplace-and-pricing'),
            'expand' => __('Expand', 'wc-carousel-grid-marketplace-and-pricing'),
            'menu-alt' => __('Menu Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'menu-alt2' => __('Menu Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'menu-alt3' => __('Menu Alt3', 'wc-carousel-grid-marketplace-and-pricing'),
            'plus' => __('Plus', 'wc-carousel-grid-marketplace-and-pricing'),
            'plus-alt' => __('Plus Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'plus-alt2' => __('Plus Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'minus' => __('Minus', 'wc-carousel-grid-marketplace-and-pricing'),
            'dismiss' => __('Dismiss', 'wc-carousel-grid-marketplace-and-pricing'),
            'insert' => __('Insert', 'wc-carousel-grid-marketplace-and-pricing'),
            'remove' => __('Remove', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-bold' => __('Bold', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-italic' => __('Italic', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-ul' => __('Unordered List', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-ol' => __('Ordered List', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-quote' => __('Quote', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-alignleft' => __('Align Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-aligncenter' => __('Align Center', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-alignright' => __('Align Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-spellcheck' => __('Spellcheck', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-expand' => __('Expand Editor', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-contract' => __('Contract Editor', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-kitchensink' => __('Kitchen Sink', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-underline' => __('Underline', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-justify' => __('Justify', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-textcolor' => __('Text Color', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-paste-word' => __('Paste from Word', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-paste-text' => __('Paste as Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-removeformatting' => __('Remove Formatting', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-video' => __('Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-customchar' => __('Custom Character', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-outdent' => __('Outdent', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-indent' => __('Indent', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-help' => __('Help', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-strikethrough' => __('Strikethrough', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-unlink' => __('Unlink', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-rtl' => __('RTL', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-break' => __('Break', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-code' => __('Code', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-paragraph' => __('Paragraph', 'wc-carousel-grid-marketplace-and-pricing'),
            'editor-table' => __('Table', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-aside' => __('Aside', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-chat' => __('Chat', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-gallery' => __('Gallery', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-links' => __('Links', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-status' => __('Status', 'wc-carousel-grid-marketplace-and-pricing'),
            'format-quote' => __('Quote Format', 'wc-carousel-grid-marketplace-and-pricing'),
            'images-alt2' => __('Images Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-archive' => __('Media Archive', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-audio' => __('Media Audio', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-code' => __('Media Code', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-default' => __('Media Default', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-document' => __('Media Document', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-interactive' => __('Media Interactive', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-spreadsheet' => __('Media Spreadsheet', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-text' => __('Media Text', 'wc-carousel-grid-marketplace-and-pricing'),
            'media-video' => __('Media Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-crop' => __('Crop', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-rotate' => __('Rotate', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-rotate-left' => __('Rotate Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-rotate-right' => __('Rotate Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-flip-vertical' => __('Flip Vertical', 'wc-carousel-grid-marketplace-and-pricing'),
            'image-flip-horizontal' => __('Flip Horizontal', 'wc-carousel-grid-marketplace-and-pricing'),
            'edit-page' => __('Edit Page', 'wc-carousel-grid-marketplace-and-pricing'),
            'edit-large' => __('Edit Large', 'wc-carousel-grid-marketplace-and-pricing'),
            'edit-small' => __('Edit Small', 'wc-carousel-grid-marketplace-and-pricing'),
            'visibility' => __('Visibility', 'wc-carousel-grid-marketplace-and-pricing'),
            'hidden' => __('Hidden', 'wc-carousel-grid-marketplace-and-pricing'),
            'lock-dims' => __('Lock Dimensions', 'wc-carousel-grid-marketplace-and-pricing'),
            'unlock-dims' => __('Unlock Dimensions', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-alt2' => __('Share Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-alt3' => __('Share Alt3', 'wc-carousel-grid-marketplace-and-pricing'),
            'twitter-alt' => __('Twitter Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'facebook' => __('Facebook', 'wc-carousel-grid-marketplace-and-pricing'),
            'facebook-alt' => __('Facebook Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'googleplus' => __('Google Plus', 'wc-carousel-grid-marketplace-and-pricing'),
            'linkedin' => __('LinkedIn', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-play' => __('Play', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-pause' => __('Pause', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-forward' => __('Forward', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-skipforward' => __('Skip Forward', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-back' => __('Back', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-skipback' => __('Skip Back', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-repeat' => __('Repeat', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-volumeon' => __('Volume On', 'wc-carousel-grid-marketplace-and-pricing'),
            'controls-volumeoff' => __('Volume Off', 'wc-carousel-grid-marketplace-and-pricing'),
            'performance' => __('Performance', 'wc-carousel-grid-marketplace-and-pricing'),
            'privacy' => __('Privacy', 'wc-carousel-grid-marketplace-and-pricing'),
            'security' => __('Security', 'wc-carousel-grid-marketplace-and-pricing'),
            'dashboard' => __('Dashboard', 'wc-carousel-grid-marketplace-and-pricing'),
            'archive' => __('Archive', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-appearance' => __('Appearance', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-comments' => __('Comments', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-links' => __('Links', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-media' => __('Media', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-multisite' => __('Multisite', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-network' => __('Network', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-page' => __('Page', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-plugins' => __('Plugins', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-post' => __('Post', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-customizer' => __('Customizer', 'wc-carousel-grid-marketplace-and-pricing'),
            'admin-users' => __('Users', 'wc-carousel-grid-marketplace-and-pricing'),
            'album' => __('Album', 'wc-carousel-grid-marketplace-and-pricing'),
            'audio' => __('Audio', 'wc-carousel-grid-marketplace-and-pricing'),
            'bell' => __('Bell', 'wc-carousel-grid-marketplace-and-pricing'),
            'block-default' => __('Block', 'wc-carousel-grid-marketplace-and-pricing'),
            'book-alt' => __('Book Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'box' => __('Box', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-activity' => __('Activity', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-bbpress-logo' => __('bbPress', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-buddypress-logo' => __('BuddyPress', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-community' => __('Community', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-forums' => __('Forums', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-friends' => __('Friends', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-groups' => __('Groups', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-pm' => __('Private Message', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-replies' => __('Replies', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-topics' => __('Topics', 'wc-carousel-grid-marketplace-and-pricing'),
            'buddicons-tracking' => __('Tracking', 'wc-carousel-grid-marketplace-and-pricing'),
            'businesswoman' => __('Business Woman', 'wc-carousel-grid-marketplace-and-pricing'),
            'calculator' => __('Calculator', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot' => __('Carrot', 'wc-carousel-grid-marketplace-and-pricing'),
            'cart-3' => __('Cart 3', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-down' => __('Carrot Down', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-left' => __('Carrot Left', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-right' => __('Carrot Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-up' => __('Carrot Up', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-down-alt' => __('Carrot Down Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-left-alt' => __('Carrot Left Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-right-alt' => __('Carrot Right Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'carrot-up-alt' => __('Carrot Up Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'cloud-saved' => __('Cloud Saved', 'wc-carousel-grid-marketplace-and-pricing'),
            'cloud-upload' => __('Cloud Upload', 'wc-carousel-grid-marketplace-and-pricing'),
            'database-add' => __('Database Add', 'wc-carousel-grid-marketplace-and-pricing'),
            'database-export' => __('Database Export', 'wc-carousel-grid-marketplace-and-pricing'),
            'database-import' => __('Database Import', 'wc-carousel-grid-marketplace-and-pricing'),
            'database-remove' => __('Database Remove', 'wc-carousel-grid-marketplace-and-pricing'),
            'database-view' => __('Database View', 'wc-carousel-grid-marketplace-and-pricing'),
            'embed-audio' => __('Embed Audio', 'wc-carousel-grid-marketplace-and-pricing'),
            'embed-generic' => __('Embed Generic', 'wc-carousel-grid-marketplace-and-pricing'),
            'embed-photo' => __('Embed Photo', 'wc-carousel-grid-marketplace-and-pricing'),
            'embed-post' => __('Embed Post', 'wc-carousel-grid-marketplace-and-pricing'),
            'embed-video' => __('Embed Video', 'wc-carousel-grid-marketplace-and-pricing'),
            'exit' => __('Exit', 'wc-carousel-grid-marketplace-and-pricing'),
            'feedback-2' => __('Feedback 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'filter-1' => __('Filter 1', 'wc-carousel-grid-marketplace-and-pricing'),
            'filter-2' => __('Filter 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'flag-2' => __('Flag 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'flickr' => __('Flickr', 'wc-carousel-grid-marketplace-and-pricing'),
            'foursquare' => __('Foursquare', 'wc-carousel-grid-marketplace-and-pricing'),
            'github' => __('GitHub', 'wc-carousel-grid-marketplace-and-pricing'),
            'google' => __('Google', 'wc-carousel-grid-marketplace-and-pricing'),
            'grid-view' => __('Grid View', 'wc-carousel-grid-marketplace-and-pricing'),
            'hammer-2' => __('Hammer 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'heart-2' => __('Heart 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'hourglass' => __('Hourglass', 'wc-carousel-grid-marketplace-and-pricing'),
            'html' => __('HTML', 'wc-carousel-grid-marketplace-and-pricing'),
            'instagram' => __('Instagram', 'wc-carousel-grid-marketplace-and-pricing'),
            'laptop-2' => __('Laptop 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'left-right' => __('Left Right', 'wc-carousel-grid-marketplace-and-pricing'),
            'list-view-2' => __('List View 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'location-2' => __('Location 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'lock-2' => __('Lock 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'login' => __('Login', 'wc-carousel-grid-marketplace-and-pricing'),
            'logout' => __('Logout', 'wc-carousel-grid-marketplace-and-pricing'),
            'magnet' => __('Magnet', 'wc-carousel-grid-marketplace-and-pricing'),
            'megaphone-2' => __('Megaphone 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'microphone' => __('Microphone', 'wc-carousel-grid-marketplace-and-pricing'),
            'networking-2' => __('Networking 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'no-alt-2' => __('No Alt 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'paypal' => __('PayPal', 'wc-carousel-grid-marketplace-and-pricing'),
            'pinterest' => __('Pinterest', 'wc-carousel-grid-marketplace-and-pricing'),
            'podio' => __('Podio', 'wc-carousel-grid-marketplace-and-pricing'),
            'print' => __('Print', 'wc-carousel-grid-marketplace-and-pricing'),
            'randomize-2' => __('Randomize 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'reddit' => __('Reddit', 'wc-carousel-grid-marketplace-and-pricing'),
            'redo-2' => __('Redo 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'resize' => __('Resize', 'wc-carousel-grid-marketplace-and-pricing'),
            'rss-2' => __('RSS 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'screenoptions-2' => __('Screen Options 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'search-2' => __('Search 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-2' => __('Share 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-3' => __('Share 3', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-4' => __('Share 4', 'wc-carousel-grid-marketplace-and-pricing'),
            'share-5' => __('Share 5', 'wc-carousel-grid-marketplace-and-pricing'),
            'shield-2' => __('Shield 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'shield-3' => __('Shield 3', 'wc-carousel-grid-marketplace-and-pricing'),
            'shield-4' => __('Shield 4', 'wc-carousel-grid-marketplace-and-pricing'),
            'smartphone-2' => __('Smartphone 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'smiley-2' => __('Smiley 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'snapchat' => __('Snapchat', 'wc-carousel-grid-marketplace-and-pricing'),
            'soundcloud' => __('SoundCloud', 'wc-carousel-grid-marketplace-and-pricing'),
            'spotify' => __('Spotify', 'wc-carousel-grid-marketplace-and-pricing'),
            'star-empty-2' => __('Star Empty 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'star-filled-2' => __('Star Filled 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-col-after' => __('Table Column After', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-col-before' => __('Table Column Before', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-col-delete' => __('Table Column Delete', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-row-after' => __('Table Row After', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-row-before' => __('Table Row Before', 'wc-carousel-grid-marketplace-and-pricing'),
            'table-row-delete' => __('Table Row Delete', 'wc-carousel-grid-marketplace-and-pricing'),
            'tablet-2' => __('Tablet 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'tag-2' => __('Tag 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'tagcloud-2' => __('Tag Cloud 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'testimonial-2' => __('Testimonial 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'text-page' => __('Text Page', 'wc-carousel-grid-marketplace-and-pricing'),
            'thumbs-up-2' => __('Thumbs Up 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'thumbs-down-2' => __('Thumbs Down 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'tickets-2' => __('Tickets 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'title' => __('Title', 'wc-carousel-grid-marketplace-and-pricing'),
            'tide' => __('Tide', 'wc-carousel-grid-marketplace-and-pricing'),
            'twitch' => __('Twitch', 'wc-carousel-grid-marketplace-and-pricing'),
            'twitter-2' => __('Twitter 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'undo-2' => __('Undo 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'universal-access-2' => __('Universal Access 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'update-2' => __('Update 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'upload-2' => __('Upload 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'vault-2' => __('Vault 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'video-2' => __('Video 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'video-alt' => __('Video Alt', 'wc-carousel-grid-marketplace-and-pricing'),
            'video-alt2' => __('Video Alt2', 'wc-carousel-grid-marketplace-and-pricing'),
            'video-alt3' => __('Video Alt3', 'wc-carousel-grid-marketplace-and-pricing'),
            'warning-2' => __('Warning 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-add-page-2' => __('Add Page 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-comments-2' => __('Comments 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-learn-more-2' => __('Learn More 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-view-site-2' => __('View Site 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-widgets-menus-2' => __('Widgets/Menus 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'welcome-write-blog-2' => __('Write Blog 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'whatsapp' => __('WhatsApp', 'wc-carousel-grid-marketplace-and-pricing'),
            'wordpress-2' => __('WordPress 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'wordpress-alt-2' => __('WordPress Alt 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'xing' => __('Xing', 'wc-carousel-grid-marketplace-and-pricing'),
            'yes-2' => __('Yes 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'yes-alt-2' => __('Yes Alt 2', 'wc-carousel-grid-marketplace-and-pricing'),
            'youtube' => __('YouTube', 'wc-carousel-grid-marketplace-and-pricing'),
            'youtube-alt' => __('YouTube Alt', 'wc-carousel-grid-marketplace-and-pricing'),
        ];
    }
}
