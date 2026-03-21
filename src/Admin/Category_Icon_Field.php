<?php

namespace WC_CGMP\Admin;

defined('ABSPATH') || exit;

class Category_Icon_Field
{
    private const META_ICON_TYPE = 'wc_cgmp_icon_type';
    private const META_ICON_DASHICON = 'wc_cgmp_icon_dashicon';
    private const META_ICON_FONTAWESOME = 'wc_cgmp_icon_fontawesome';

    public function __construct()
    {
        add_action('product_cat_add_form_fields', [$this, 'add_form_fields']);
        add_action('product_cat_edit_form_fields', [$this, 'edit_form_fields']);
        add_action('created_product_cat', [$this, 'save_term_meta']);
        add_action('edited_product_cat', [$this, 'save_term_meta']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function enqueue_assets(string $hook): void
    {
        $screen = get_current_screen();
        
        if (!$screen || strpos($screen->id, 'product_cat') === false) {
            return;
        }

        wp_enqueue_style(
            'fontawesome-free',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
            [],
            '6.5.1'
        );

        wp_enqueue_style(
            'wc-cgmp-category-icon',
            WC_CGMP_PLUGIN_URL . 'assets/css/admin/category-icon.css',
            ['fontawesome-free'],
            WC_CGMP_VERSION
        );

        wp_enqueue_script(
            'wc-cgmp-category-icon',
            WC_CGMP_PLUGIN_URL . 'assets/js/admin/category-icon.js',
            ['jquery'],
            WC_CGMP_VERSION,
            true
        );

        wp_localize_script('wc-cgmp-category-icon', 'wcCgmpCategoryIcon', [
            'dashicons' => \WC_CGMP\Frontend\Category_Icon::get_available_dashicons(),
            'fontawesomeIcons' => self::get_available_fontawesome_icons(),
            'searchPlaceholder' => __('Search icons...', 'wc-carousel-grid-marketplace-and-pricing'),
            'noResults' => __('No icons found', 'wc-carousel-grid-marketplace-and-pricing'),
        ]);
    }

    public static function get_available_fontawesome_icons(): array
    {
        return [
            'solid' => [
                'label' => __('Solid', 'wc-carousel-grid-marketplace-and-pricing'),
                'icons' => [
                    'fa-solid fa-house' => __('House', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-building' => __('Building', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-store' => __('Store', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cart-shopping' => __('Shopping Cart', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bag-shopping' => __('Shopping Bag', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-shirt' => __('T-Shirt', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-shoe-prints' => __('Shoes', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-gem' => __('Gem', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-ring' => __('Ring', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-glasses' => __('Glasses', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-hat-wizard' => __('Hat', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-computer' => __('Computer', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-laptop' => __('Laptop', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-mobile-screen' => __('Mobile Phone', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-tablet-screen-button' => __('Tablet', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-headphones' => __('Headphones', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-camera' => __('Camera', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-gamepad' => __('Gamepad', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-tv' => __('TV', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-music' => __('Music', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-guitar' => __('Guitar', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-futbol' => __('Football', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-basketball' => __('Basketball', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-dumbbell' => __('Dumbbell', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bicycle' => __('Bicycle', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-car' => __('Car', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-motorcycle' => __('Motorcycle', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-truck' => __('Truck', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-plane' => __('Plane', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-utensils' => __('Utensils', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-pizza-slice' => __('Pizza', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-burger' => __('Burger', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-mug-hot' => __('Coffee', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-wine-glass' => __('Wine Glass', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cake-candles' => __('Cake', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-ice-cream' => __('Ice Cream', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-apple-whole' => __('Apple', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-carrot' => __('Carrot', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-seedling' => __('Seedling', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-leaf' => __('Leaf', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-tree' => __('Tree', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-paw' => __('Paw', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-dog' => __('Dog', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cat' => __('Cat', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-fish' => __('Fish', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-spider' => __('Spider', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-heart' => __('Heart', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-star' => __('Star', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-crown' => __('Crown', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-gift' => __('Gift', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-box' => __('Box', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-box-open' => __('Box Open', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-tags' => __('Tags', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-percent' => __('Percent', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-dollar-sign' => __('Dollar', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-coins' => __('Coins', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-credit-card' => __('Credit Card', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-wallet' => __('Wallet', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-piggy-bank' => __('Piggy Bank', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-book' => __('Book', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-book-open' => __('Book Open', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-pen' => __('Pen', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-paintbrush' => __('Paintbrush', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-palette' => __('Palette', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-film' => __('Film', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-clapperboard' => __('Clapperboard', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-image' => __('Image', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-images' => __('Images', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-video' => __('Video', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-microphone' => __('Microphone', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-screwdriver-wrench' => __('Tools', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-hammer' => __('Hammer', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-toolbox' => __('Toolbox', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-couch' => __('Couch', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-chair' => __('Chair', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bed' => __('Bed', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-lamp' => __('Lamp', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-lightbulb' => __('Lightbulb', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-clock' => __('Clock', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-calendar' => __('Calendar', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-map' => __('Map', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-location-dot' => __('Location', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-globe' => __('Globe', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-earth-americas' => __('Americas', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-earth-europe' => __('Europe', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-earth-asia' => __('Asia', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-flag' => __('Flag', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-trophy' => __('Trophy', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-medal' => __('Medal', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-award' => __('Award', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-certificate' => __('Certificate', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-check' => __('Check', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-check-circle' => __('Check Circle', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-thumbs-up' => __('Thumbs Up', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-handshake' => __('Handshake', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-users' => __('Users', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-user' => __('User', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-user-tie' => __('Business User', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-briefcase' => __('Briefcase', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-envelope' => __('Envelope', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-phone' => __('Phone', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-comment' => __('Comment', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-comments' => __('Comments', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-shield' => __('Shield', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-lock' => __('Lock', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-key' => __('Key', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-fire' => __('Fire', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bolt' => __('Bolt', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-sun' => __('Sun', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-moon' => __('Moon', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cloud' => __('Cloud', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-umbrella' => __('Umbrella', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-snowflake' => __('Snowflake', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-droplet' => __('Droplet', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-wind' => __('Wind', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-rocket' => __('Rocket', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-paper-plane' => __('Paper Plane', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-helicopter' => __('Helicopter', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-ship' => __('Ship', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-anchor' => __('Anchor', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-campground' => __('Campground', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-mountain' => __('Mountain', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-water' => __('Water', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-binoculars' => __('Binoculars', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-compass' => __('Compass', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-magnifying-glass' => __('Search', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-filter' => __('Filter', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-sliders' => __('Sliders', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-gear' => __('Gear', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cogs' => __('Cogs', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-wrench' => __('Wrench', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bug' => __('Bug', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-code' => __('Code', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-terminal' => __('Terminal', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-database' => __('Database', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-server' => __('Server', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cloud-arrow-up' => __('Cloud Upload', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cloud-arrow-down' => __('Cloud Download', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-download' => __('Download', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-upload' => __('Upload', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-share-nodes' => __('Share', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-link' => __('Link', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-qrcode' => __('QR Code', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-barcode' => __('Barcode', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-chart-line' => __('Chart Line', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-chart-bar' => __('Chart Bar', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-chart-pie' => __('Chart Pie', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-gauge-high' => __('Gauge', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bell' => __('Bell', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-info' => __('Info', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-question' => __('Question', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-exclamation' => __('Exclamation', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-plus' => __('Plus', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-minus' => __('Minus', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-xmark' => __('X Mark', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-bars' => __('Bars', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-ellipsis' => __('Ellipsis', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-arrow-up' => __('Arrow Up', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-arrow-down' => __('Arrow Down', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-arrow-left' => __('Arrow Left', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-arrow-right' => __('Arrow Right', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-arrows-rotate' => __('Refresh', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-rotate' => __('Rotate', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-recycle' => __('Recycle', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-leaf' => __('Eco Leaf', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-atom' => __('Atom', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-dna' => __('DNA', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-microscope' => __('Microscope', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-flask' => __('Flask', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-pills' => __('Pills', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-syringe' => __('Syringe', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-heart-pulse' => __('Heart Pulse', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-stethoscope' => __('Stethoscope', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-user-doctor' => __('Doctor', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-hospital' => __('Hospital', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-graduation-cap' => __('Graduation Cap', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-school' => __('School', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-chalkboard-user' => __('Teacher', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-baby' => __('Baby', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-child' => __('Child', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-person' => __('Person', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-people-group' => __('People Group', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-icons' => __('Icons', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-shapes' => __('Shapes', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-puzzle-piece' => __('Puzzle', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cube' => __('Cube', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-cubes' => __('Cubes', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-solid fa-layer-group' => __('Layers', 'wc-carousel-grid-marketplace-and-pricing'),
                ],
            ],
            'brands' => [
                'label' => __('Brands', 'wc-carousel-grid-marketplace-and-pricing'),
                'icons' => [
                    'fa-brands fa-wordpress' => __('WordPress', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-wordpress-simple' => __('WordPress Simple', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-shopify' => __('Shopify', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-stripe' => __('Stripe', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-paypal' => __('PayPal', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-cc-visa' => __('Visa', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-cc-mastercard' => __('Mastercard', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-cc-amex' => __('Amex', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-amazon' => __('Amazon', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-ebay' => __('eBay', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-etsy' => __('Etsy', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-facebook' => __('Facebook', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-facebook-f' => __('Facebook F', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-instagram' => __('Instagram', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-twitter' => __('Twitter', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-youtube' => __('YouTube', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-tiktok' => __('TikTok', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-pinterest' => __('Pinterest', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-linkedin' => __('LinkedIn', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-github' => __('GitHub', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-dribbble' => __('Dribbble', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-behance' => __('Behance', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-skype' => __('Skype', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-slack' => __('Slack', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-trello' => __('Trello', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-google' => __('Google', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-apple' => __('Apple', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-microsoft' => __('Microsoft', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-android' => __('Android', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-chrome' => __('Chrome', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-firefox' => __('Firefox', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-safari' => __('Safari', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-edge' => __('Edge', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-docker' => __('Docker', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-aws' => __('AWS', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-google-play' => __('Google Play', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-app-store' => __('App Store', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-spotify' => __('Spotify', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-soundcloud' => __('SoundCloud', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-vimeo' => __('Vimeo', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-twitch' => __('Twitch', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-discord' => __('Discord', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-reddit' => __('Reddit', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-quora' => __('Quora', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-medium' => __('Medium', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-whatsapp' => __('WhatsApp', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-telegram' => __('Telegram', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-snapchat' => __('Snapchat', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-weixin' => __('WeChat', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-line' => __('Line', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-viber' => __('Viber', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-flickr' => __('Flickr', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-foursquare' => __('Foursquare', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-yelp' => __('Yelp', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-tripadvisor' => __('TripAdvisor', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-airbnb' => __('Airbnb', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-uber' => __('Uber', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-lyft' => __('Lyft', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-gratipay' => __('Gratipay', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-product-hunt' => __('Product Hunt', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-kickstarter' => __('Kickstarter', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-gofundme' => __('GoFundMe', 'wc-carousel-grid-marketplace-and-pricing'),
                    'fa-brands fa-patreon' => __('Patreon', 'wc-carousel-grid-marketplace-and-pricing'),
                ],
            ],
        ];
    }

    public function add_form_fields(): void
    {
        wp_nonce_field('wc_cgmp_save_category_icon', 'wc_cgmp_category_nonce');
        ?>
        <div class="form-field wc-cgmp-icon-field-wrapper">
            <label for="wc_cgmp_icon_type"><?php esc_html_e('Category Icon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
            <div class="wc-cgmp-icon-type-selector">
                <label class="wc-cgmp-icon-type-option">
                    <input type="radio" name="wc_cgmp_icon_type" value="dashicon" checked="checked" />
                    <span><?php esc_html_e('Dashicon', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                </label>
                <label class="wc-cgmp-icon-type-option">
                    <input type="radio" name="wc_cgmp_icon_type" value="fontawesome" />
                    <span><?php esc_html_e('Font Awesome', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                </label>
            </div>

            <div class="wc-cgmp-icon-field wc-cgmp-dashicon-field" data-type="dashicon">
                <label for="wc_cgmp_icon_dashicon"><?php esc_html_e('Select Dashicon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                <div class="wc-cgmp-dashicon-picker">
                    <input type="hidden" name="wc_cgmp_icon_dashicon" id="wc_cgmp_icon_dashicon" value="grid" />
                    <div class="wc-cgmp-dashicon-grid">
                        <?php foreach (\WC_CGMP\Frontend\Category_Icon::get_available_dashicons() as $icon => $label) : ?>
                            <button type="button" class="wc-cgmp-dashicon-btn" data-icon="<?php echo esc_attr($icon); ?>" title="<?php echo esc_attr($label); ?>">
                                <span class="dashicons dashicons-<?php echo esc_attr($icon); ?>"></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="wc-cgmp-dashicon-preview">
                        <?php esc_html_e('Selected:', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                        <span class="wc-cgmp-preview-icon"><span class="dashicons dashicons-grid"></span></span>
                        <code class="wc-cgmp-preview-name">grid</code>
                    </div>
                </div>
            </div>

            <div class="wc-cgmp-icon-field wc-cgmp-fontawesome-field" data-type="fontawesome" style="display:none;">
                <label for="wc_cgmp_icon_fontawesome"><?php esc_html_e('Select Font Awesome Icon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                <div class="wc-cgmp-fontawesome-picker">
                    <input type="hidden" name="wc_cgmp_icon_fontawesome" id="wc_cgmp_icon_fontawesome" value="fa-solid fa-store" />
                    <div class="wc-cgmp-fontawesome-search">
                        <input type="text" class="wc-cgmp-fontawesome-search-input" placeholder="<?php esc_attr_e('Search icons...', 'wc-carousel-grid-marketplace-and-pricing'); ?>" />
                    </div>
                    <div class="wc-cgmp-fontawesome-categories">
                        <?php foreach (self::get_available_fontawesome_icons() as $category => $category_data) : ?>
                            <button type="button" class="wc-cgmp-fontawesome-category-btn" data-category="<?php echo esc_attr($category); ?>">
                                <?php echo esc_html($category_data['label']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                    <div class="wc-cgmp-fontawesome-grid" data-active-category="solid">
                        <?php foreach (self::get_available_fontawesome_icons() as $category => $category_data) : ?>
                            <?php foreach ($category_data['icons'] as $icon => $label) : ?>
                                <button type="button" class="wc-cgmp-fontawesome-btn" data-icon="<?php echo esc_attr($icon); ?>" data-category="<?php echo esc_attr($category); ?>" data-label="<?php echo esc_attr($label); ?>" title="<?php echo esc_attr($label); ?>">
                                    <i class="<?php echo esc_attr($icon); ?>"></i>
                                </button>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="wc-cgmp-fontawesome-preview">
                        <?php esc_html_e('Selected:', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                        <span class="wc-cgmp-preview-icon"><i class="fa-solid fa-store"></i></span>
                        <code class="wc-cgmp-preview-name">fa-solid fa-store</code>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function edit_form_fields($term): void
    {
        $icon_type = get_term_meta($term->term_id, self::META_ICON_TYPE, true) ?: 'dashicon';
        $dashicon = get_term_meta($term->term_id, self::META_ICON_DASHICON, true) ?: 'grid';
        $fontawesome = get_term_meta($term->term_id, self::META_ICON_FONTAWESOME, true) ?: 'fa-solid fa-store';
        
        $legacy_icon = get_term_meta($term->term_id, 'wc_cgmp_icon', true);
        ?>
        <tr class="form-field wc-cgmp-icon-field-wrapper">
            <th scope="row" valign="top">
                <label for="wc_cgmp_icon_type"><?php esc_html_e('Category Icon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
            </th>
            <td>
                <?php wp_nonce_field('wc_cgmp_save_category_icon', 'wc_cgmp_category_nonce'); ?>
                <div class="wc-cgmp-icon-type-selector">
                    <label class="wc-cgmp-icon-type-option">
                        <input type="radio" name="wc_cgmp_icon_type" value="dashicon" <?php checked($icon_type, 'dashicon'); ?> />
                        <span><?php esc_html_e('Dashicon', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                    </label>
                    <label class="wc-cgmp-icon-type-option">
                        <input type="radio" name="wc_cgmp_icon_type" value="fontawesome" <?php checked($icon_type, 'fontawesome'); ?> />
                        <span><?php esc_html_e('Font Awesome', 'wc-carousel-grid-marketplace-and-pricing'); ?></span>
                    </label>
                </div>

                <div class="wc-cgmp-icon-field wc-cgmp-dashicon-field" data-type="dashicon" <?php echo $icon_type !== 'dashicon' ? 'style="display:none;"' : ''; ?>>
                    <label for="wc_cgmp_icon_dashicon"><?php esc_html_e('Select Dashicon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <div class="wc-cgmp-dashicon-picker">
                        <input type="hidden" name="wc_cgmp_icon_dashicon" id="wc_cgmp_icon_dashicon" value="<?php echo esc_attr($dashicon); ?>" />
                        <div class="wc-cgmp-dashicon-grid">
                            <?php foreach (\WC_CGMP\Frontend\Category_Icon::get_available_dashicons() as $icon => $label) : ?>
                                <button type="button" class="wc-cgmp-dashicon-btn <?php echo $icon === $dashicon ? 'selected' : ''; ?>" data-icon="<?php echo esc_attr($icon); ?>" title="<?php echo esc_attr($label); ?>">
                                    <span class="dashicons dashicons-<?php echo esc_attr($icon); ?>"></span>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="wc-cgmp-dashicon-preview">
                            <?php esc_html_e('Selected:', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                            <span class="wc-cgmp-preview-icon"><span class="dashicons dashicons-<?php echo esc_attr($dashicon); ?>"></span></span>
                            <code class="wc-cgmp-preview-name"><?php echo esc_html($dashicon); ?></code>
                        </div>
                    </div>
                </div>

                <div class="wc-cgmp-icon-field wc-cgmp-fontawesome-field" data-type="fontawesome" <?php echo $icon_type !== 'fontawesome' ? 'style="display:none;"' : ''; ?>>
                    <label for="wc_cgmp_icon_fontawesome"><?php esc_html_e('Select Font Awesome Icon', 'wc-carousel-grid-marketplace-and-pricing'); ?></label>
                    <div class="wc-cgmp-fontawesome-picker">
                        <input type="hidden" name="wc_cgmp_icon_fontawesome" id="wc_cgmp_icon_fontawesome" value="<?php echo esc_attr($fontawesome); ?>" />
                        <div class="wc-cgmp-fontawesome-search">
                            <input type="text" class="wc-cgmp-fontawesome-search-input" placeholder="<?php esc_attr_e('Search icons...', 'wc-carousel-grid-marketplace-and-pricing'); ?>" />
                        </div>
                        <div class="wc-cgmp-fontawesome-categories">
                            <?php foreach (self::get_available_fontawesome_icons() as $category => $category_data) : ?>
                                <button type="button" class="wc-cgmp-fontawesome-category-btn" data-category="<?php echo esc_attr($category); ?>">
                                    <?php echo esc_html($category_data['label']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="wc-cgmp-fontawesome-grid" data-active-category="solid">
                            <?php foreach (self::get_available_fontawesome_icons() as $category => $category_data) : ?>
                                <?php foreach ($category_data['icons'] as $icon => $label) : ?>
                                    <button type="button" class="wc-cgmp-fontawesome-btn <?php echo $icon === $fontawesome ? 'selected' : ''; ?>" data-icon="<?php echo esc_attr($icon); ?>" data-category="<?php echo esc_attr($category); ?>" data-label="<?php echo esc_attr($label); ?>" title="<?php echo esc_attr($label); ?>">
                                        <i class="<?php echo esc_attr($icon); ?>"></i>
                                    </button>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                        <div class="wc-cgmp-fontawesome-preview">
                            <?php esc_html_e('Selected:', 'wc-carousel-grid-marketplace-and-pricing'); ?>
                            <span class="wc-cgmp-preview-icon"><i class="<?php echo esc_attr($fontawesome); ?>"></i></span>
                            <code class="wc-cgmp-preview-name"><?php echo esc_html($fontawesome); ?></code>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <?php
    }

    public function save_term_meta(int $term_id): void
    {
        if (!current_user_can('manage_categories') && !current_user_can('manage_woocommerce')) {
            return;
        }

        if (!isset($_POST['wc_cgmp_category_nonce']) || !wp_verify_nonce(wp_unslash($_POST['wc_cgmp_category_nonce']), 'wc_cgmp_save_category_icon')) {
            return;
        }

        if (!isset($_POST['wc_cgmp_icon_type'])) {
            return;
        }

        $icon_type = sanitize_text_field(wp_unslash($_POST['wc_cgmp_icon_type']));
        
        if (!in_array($icon_type, ['dashicon', 'fontawesome'], true)) {
            $icon_type = 'dashicon';
        }

        update_term_meta($term_id, self::META_ICON_TYPE, $icon_type);

        if ($icon_type === 'dashicon') {
            $dashicon = isset($_POST['wc_cgmp_icon_dashicon']) 
                ? sanitize_text_field(wp_unslash($_POST['wc_cgmp_icon_dashicon'])) 
                : 'grid';
            
            $available = \WC_CGMP\Frontend\Category_Icon::get_available_dashicons();
            if (!isset($available[$dashicon])) {
                $dashicon = 'grid';
            }
            
            update_term_meta($term_id, self::META_ICON_DASHICON, $dashicon);
            delete_term_meta($term_id, self::META_ICON_FONTAWESOME);
        } elseif ($icon_type === 'fontawesome') {
            $fontawesome = isset($_POST['wc_cgmp_icon_fontawesome']) 
                ? sanitize_text_field(wp_unslash($_POST['wc_cgmp_icon_fontawesome'])) 
                : 'fa-solid fa-store';
            
            $fontawesome = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $fontawesome);
            
            update_term_meta($term_id, self::META_ICON_FONTAWESOME, $fontawesome);
            delete_term_meta($term_id, self::META_ICON_DASHICON);
        }

        wp_cache_delete('wc_cgmp_categories_with_counts', 'wc_cgmp');
    }
}
