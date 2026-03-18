# WooCommerce Carousel/Grid Marketplace & Pricing

A powerful WordPress plugin that combines a modern service marketplace with tiered pricing. Features carousel/grid layouts, Elementor support, and comprehensive WooCommerce integration.

## Features

- **Modern Card Design** - Beautiful service cards with pricing panels and headcount selectors
- **3-Tier Pricing System** - Entry, Mid, Expert levels with monthly and hourly rates
- **Grid or Carousel Layout** - Choose the display that fits your site
- **Hybrid Layout** - Grid on desktop, carousel on mobile
- **Elementor Widget** - Full visual controls for design customization
- **Shortcode Support** - Use `[wc_cgmp_marketplace]` anywhere
- **AJAX Filtering** - Fast filtering without page reloads
- **Category Sidebar** - Filter services by WooCommerce product categories
- **Category Icons** - Custom dashicons, Font Awesome, images, or SVG for product categories
- **WooCommerce Reports** - Sales breakdown by tier
- **Popular Badges** - Automatic or manual highlighting of popular services
- **Dynamic Section Titles** - Category-based titles that update automatically

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- WooCommerce 6.0 or higher (tested up to 8.0)
- Elementor (optional, for widget functionality)

## Installation

### WordPress Admin (Recommended)

1. Go to **WordPress Admin > Plugins > Add New**
2. Click **"Upload Plugin"**
3. Select the ZIP file
4. Click **"Install Now"**
5. Activate the plugin

### Manual Installation

1. Extract the ZIP file
2. Upload the `wc-carousel-grid-marketplace-and-pricing` folder to `/wp-content/plugins/`
3. Activate the plugin through the **Plugins** menu in WordPress

## Usage

### Shortcode

Display the marketplace anywhere using the shortcode:

```
[wc_cgmp_marketplace]
```

#### Shortcode Attributes

| Attribute | Default | Description |
|-----------|---------|-------------|
| `columns` | 3 | Grid columns (1-6) |
| `category` | (all) | Filter by category ID |
| `tier` | 0 | Filter by tier (0=all, 1=Entry, 2=Mid, 3=Expert) |
| `limit` | 12 | Products per page |
| `orderby` | date | Sort field (date, price, popularity, title, rand) |
| `order` | DESC | Sort direction (ASC, DESC) |
| `layout` | grid | Layout type (grid, carousel, hybrid) |
| `show_sidebar` | true | Show category sidebar |
| `show_filter` | true | Show tier filter bar |
| `show_search` | true | Show search input |
| `show_tier_badge` | true | Show tier badge on cards |

### Category Icon Shortcode

Display category icons anywhere:

```
[wc_cgmp_category_icon category="design" size="48" class="my-icon"]
```

#### Category Icon Attributes

| Attribute | Default | Description |
|-----------|---------|-------------|
| `category` | (required) | Category slug or ID |
| `size` | 24 | Icon size in pixels |
| `class` | | Additional CSS class |
| `link` | false | Wrap in category link |
| `return` | html | Return type (html or url) |

### Elementor Widget

1. Edit a page with Elementor
2. Search for **"Marketplace"** or **"Category Icon"** in the widgets panel
3. Drag the widget to your page
4. Configure settings in the Content and Style tabs

### Product Setup

1. Edit a WooCommerce product
2. Find the **Marketplace & Pricing** metabox
3. Check **"Enable for Marketplace"**
4. Configure pricing tiers:
   - **Entry Level** - Starting tier with lowest pricing
   - **Mid Level** - Intermediate tier
   - **Expert Level** - Premium tier with highest pricing
5. Set monthly and/or hourly rates for each tier
6. Optionally add descriptions for each tier

### Category Icon Setup

1. Go to **Products > Categories** in WordPress admin
2. Edit a category
3. Find the **Category Icon** section
4. Choose **Dashicon** or **Custom Image**
5. Select a dashicon from the visual picker OR upload a custom image
6. Click **Update Category**

## Tiered Pricing

Each product can have three experience levels:

| Level | Default Name | Color |
|-------|--------------|-------|
| 1 | Entry | Green |
| 2 | Mid | Blue |
| 3 | Expert | Purple |

### Pricing Options

- **Monthly Rate** - Recurring monthly pricing
- **Hourly Rate** - Per-hour pricing

Both can be enabled per tier, allowing customers to choose their preferred pricing model.

## Cart Integration

When customers add products to cart:

1. Selected tier price overrides product price
2. Tier information displays in cart and checkout
3. Order meta preserves tier details
4. Sales are tracked for reporting

## Reports

Access tier-based sales reports:

1. Go to **WooCommerce > Reports**
2. Find **Sales by Tier** and **Tier by Product** reports
3. View revenue breakdown by experience level

## Action Buttons

Products can have optional action buttons:

- **Learn More** - Link to more information
- **Apply Now** - Link to application form

Configure in the product metabox under **Action Buttons**.

## Template Functions

```php
// Render category icon
echo wc_cgmp_render_category_icon($category_id, ['size' => 48, 'link' => true]);

// Get icon data
$icon_data = wc_cgmp_get_category_icon_data($category_id);

// Get icon URL (for images)
$url = wc_cgmp_get_category_icon_url($category_id);
```

## Hooks

### Filters

```php
// Modify tier pricing
add_filter('wc_cgmp_tier_price', function($price, $product_id, $tier_level) {
    return $price;
}, 10, 3);

// Customize marketplace query args
add_filter('wc_cgmp_marketplace_query_args', function($args) {
    return $args;
});
```

### Actions

```php
// Before tier sale is recorded
do_action('wc_cgmp_before_record_tier_sale', $order_id, $product_id, $tier);

// After tier sale is recorded
do_action('wc_cgmp_after_record_tier_sale', $order_id, $product_id, $tier);
```

## Security

- All AJAX requests verify nonces
- Input sanitization on all user data
- Output escaping in templates
- Capability checks for admin actions
- Rate limiting on public endpoints (30 requests/minute)

## Cache

The plugin uses WordPress object cache for:

- Product listings
- Category counts
- Tier data
- Price ranges

Cache is automatically invalidated when tiers are updated.

## Uninstallation

When uninstalling the plugin:

1. Go to **Plugins > Installed Plugins**
2. Deactivate the plugin
3. Delete the plugin

Database tables and options are removed on uninstall if enabled in settings.

## Documentation

- [Wiki](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki)
- [Installation Guide](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Installation)
- [Shortcode Reference](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Shortcode-Reference)
- [Hooks and Filters](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Hooks-and-Filters)

## Support

- **GitHub Issues**: [https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/issues](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/issues)

## Changelog

### 1.7.59
- Fix: Category SVG icons now save and display correctly on frontend
- Fix: Added missing META_ICON_SVG_CODE constant to Frontend Category_Icon class
- Fix: SVG icon type now properly handled in get_icon() and render() methods

### 1.7.58
- Performance: Modal ("?" button) now loads near-instantly with client-side caching
- Performance: Added server-side transient caching for modal content (1 hour)
- Performance: CSS variables are now cached per marketplace instance
- Performance: Preload modal content on hover for instant display on click

### 1.7.57
- Fix: "All Services" sidebar item now uses a static gear icon that won't be overridden

### 1.7.56
- Feature: Replaced favicon URL option with Font Awesome icon picker for category icons
- Feature: Added 200+ free Font Awesome icons (Solid + Brands categories)
- Feature: Added search functionality to filter icons by name
- Feature: Added category tabs (Solid/Brands) for easy icon browsing
- Enhancement: Icon picker now includes visual preview with Font Awesome CDN
- Enhancement: Frontend now loads Font Awesome CSS for proper icon display

### 1.7.55
- Fix: Added missing `get_instance()` singleton method to `Category_Icon` class
- Fix: Fixed `Shortcodes` service registration in Plugin.php
- Fix: Removed duplicate service registration in `register_woocommerce_services_fallback()`
- Fix: Security - capability check now runs before nonce verification in `Category_Icon_Field`
- Fix: Removed unnecessary base64 encoding of JSON data in Marketplace_Widget

### 1.7.53
- Feature: Added SVG code option for category icons - paste your SVG code directly
- Feature: Category icons now support 4 types: Dashicon, Custom Image, Favicon URL, and SVG Code
- Enhancement: Improved admin UI for category icon selection

### 1.7.50
- Feature: Added favicon URL option for category icons - enter any favicon or image URL
- Fix: Category icon upload button now properly allows changing icons multiple times
- Fix: Media frame now properly disposes and recreates on each upload click
- Fix: Admin scripts now load correctly on both category add and edit pages

### 1.7.49
- Feature: Added PHP 8.1+ compatibility fix for `sanitize_email()` null deprecation warning
- Feature: New "Compatibility Fixes" settings section in WooCommerce > Marketplace & Pricing
- Feature: Toggle to enable/disable the sanitize_email null fix (enabled by default)
- Fix: Suppresses "Deprecated: strlen(): Passing null to parameter #1 ($string) is deprecated" warning in wp-includes/formatting.php

### 1.7.45
- Fixed: Category icon save now properly works with nonce verification and capability checks
- Fixed: Security enhancement - added nonce fields to category add/edit forms
- Enhancement: Expanded dashicon selection from ~126 to 300+ icons
- Enhancement: Added social media icons (Facebook, Instagram, Twitter, YouTube, LinkedIn, etc.)
- Enhancement: Added arrow icons, editor formatting icons, media icons, and admin icons

### 1.7.44
- Feature: Category icons system - select dashicons or custom images for product categories
- Feature: Admin UI on category edit page for icon selection with visual dashicon picker
- Feature: Image upload support via WordPress media library for category icons
- Feature: Shortcode `[wc_cgmp_category_icon]` for displaying category icons anywhere
- Feature: Elementor Category Icon widget with styling controls
- Feature: Template function `wc_cgmp_render_category_icon()` for theme integration
- Enhancement: Category sidebar now supports both dashicons and custom images

### 1.7.43
- Moved typography CSS variables to .wc-cgmp-responsibility-item class for proper styling

### 1.7.42
- Added font-style control for responsibility text in Elementor widget
- Fixed CSS with !important flags and proper var() syntax for all typography properties

### 1.7.41
- Fixed: Responsibility text CSS variables now properly copied to modal via JavaScript

### 1.7.40
- Added Elementor typography controls for modal responsibility list text (font size, color, line-height, letter-spacing, font family)
- Updated modal responsibility text CSS to use CSS variables for full customization

### 1.7.39
- Fixed: Modal title CSS now uses CSS variables with !important
- Added Elementor controls for Responsibility Item padding and icon gap
- Modal responsibilities section padding and margin now use CSS variables via JavaScript

### 1.7.38
- Added section title margin control
- Updated .wc-cgmp-modal-section-title CSS to use CSS variables
- Added !important to modal section title CSS
- Updated CSS with consolidated responsibility items
- Fixed duplicate CSS blocks
- Added Elementor controls for list item padding, icon gap, icon padding
- Updated version to 1.7.38 and changelog
- Cleaned up and built the release ZIP
- Confirmed --wc-cgmp-modal-title-padding CSS variable is included in JavaScript

## License

GPL v2 or later. See [LICENSE](LICENSE) for more information.

## Author

**Jerel Yoshida**
- GitHub: [https://github.com/yoshidaman99](https://github.com/yoshidaman99)
