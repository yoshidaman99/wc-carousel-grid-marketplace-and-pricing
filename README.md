# WooCommerce Carousel/Grid Marketplace & Pricing

[![License](https://img.shields.io/badge/license-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/gpl-2.0.html)
[![WordPress](https://img.shields.io/badge/WordPress-5.8%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net/)
[![WooCommerce](https://img.shields.io/badge/WooCommerce-6.0%2B-purple.svg)](https://woocommerce.com/)

A powerful WordPress plugin that combines a modern service marketplace with tiered pricing (Entry, Mid, Expert levels). Features carousel/grid layouts, Elementor support, and comprehensive WooCommerce integration.

## Features

### Marketplace Display
- **Modern Card Design** - Beautiful service cards with pricing panels, headcount selectors, and popular badges
- **Grid or Carousel Layout** - Choose between static grid, sliding carousel, or hybrid (grid on desktop, carousel on mobile)
- **Category Sidebar** - Filter services by WooCommerce product categories
- **AJAX Filtering** - Fast filtering without page reloads
- **Elementor Native Widget** - Full visual controls for design customization
- **Shortcode Support** - Use `[wc_cgmp_marketplace]` anywhere

### Tiered Pricing
- **3-Tier Pricing System** - Entry, Mid, Expert levels with customizable names, prices, and descriptions
- **Dual Pricing** - Monthly and hourly rates for each tier
- **Separate Database Storage** - Pricing tiers stored in dedicated tables (doesn't affect WooCommerce core)
- **Dynamic Cart Integration** - Selected tier price overrides product price in cart/checkout
- **WooCommerce Reports** - Sales breakdown by tier in WC Reports section

### Cart & Checkout
- **Headcount Selector** - Quantity controls with automatic total calculation
- **Mini-Cart Refresh** - Instant cart updates without page reload
- **Order Meta Storage** - Tier information saved to order line items

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- WooCommerce 6.0 or higher

## Installation

1. Download the latest release
2. Upload to `/wp-content/plugins/wc-carousel-grid-marketplace-and-pricing/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Ensure WooCommerce is installed and active
5. Go to **WooCommerce > Marketplace & Pricing** to configure settings

## Usage

### Shortcode

Basic usage:
```
[wc_cgmp_marketplace]
```

With parameters:
```
[wc_cgmp_marketplace columns="4" category="15" limit="8" show_sidebar="false"]
```

### Shortcode Parameters

| Parameter | Default | Description |
|-----------|---------|-------------|
| `columns` | `3` | Number of columns (1-6) |
| `category` | - | Product category ID to filter |
| `limit` | `-1` | Maximum products to display (-1 for all) |
| `show_sidebar` | `true` | Show category sidebar |
| `show_filter` | `true` | Show tier filter bar |
| `layout` | `grid` | Layout type: `grid`, `carousel`, or `hybrid` |

### Elementor

1. Edit a page with Elementor
2. Search for "WC Marketplace" widget
3. Drag and configure the widget settings

### Enabling Products

1. Edit a WooCommerce product
2. Check "Enable for Marketplace" in the Marketplace & Pricing metabox
3. Configure tier pricing (Entry, Mid, Expert)
4. Optionally mark as Popular or add Specialization

## Configuration

Navigate to **WooCommerce > Marketplace & Pricing** to access:

- **Layout Type**: Grid, Carousel, or Hybrid
- **Columns**: Number of products per row
- **Popular Badge Method**: Auto (sales-based) or Manual
- **Popular Threshold**: Minimum sales for auto-popular

## Reports

Access tier sales reports at **WooCommerce > Reports > Tier Pricing**:

- **Sales by Experience Level**: Revenue breakdown by tier
- **Tier Sales by Product**: Individual product tier performance

## Tier Selection UI

```
+-------------------------------------------------------------+
|  Experience Level:  [Entry] [Mid] [Expert]                     |
+-------------------------------------------------------------+
|  +------------------+  +------------------+                  |
|  | [Entry] Senior   |  | [Entry] Full-Stack|                 |
|  |     Developer    |  |     Developer     |                 |
|  |   $1,500/mo      |  |   $2,000/mo       |                 |
|  |  Headcount: [-1+] |  |  Headcount: [-1+] |                 |
|  |  Total: $1,500   |  |  Total: $2,000    |                 |
|  |  [Add to Cart]   |  |  [Add to Cart]    |                 |
|  +------------------+  +------------------+                  |
+-------------------------------------------------------------+
```

### Experience Level Colors

| Level | Color | Description |
|-------|-------|-------------|
| Entry | Green | Budget-friendly option |
| Mid | Blue | Standard option |
| Expert | Purple | Premium option |

## Migration

If upgrading from the separate plugins:

- WELP (WooCommerce Experience Level Pricing) tier data is automatically migrated
- CGM (WooCommerce Carousel/Grid Marketplace) settings are preserved
- All existing post meta is updated to new naming conventions

## Changelog

### 1.1.4 - 2026-02-23
* Fix: Fixed fatal error in wc_cgmp_log() - added wc_cgmp_logger() helper function
* Fix: Removed trailing whitespace from template files

### 1.1.3 - 2026-02-23
* Fix: Zip file now uses forward slashes for Linux server compatibility

### 1.1.2 - 2026-02-23
* Fix: Added explicit require for Activator/Deactivator classes to prevent autoloader issues during plugin activation

### 1.1.1 - 2026-02-23
* Update .gitignore with AI Builder patterns and improved organization

### 1.1.0 - 2026-02-23
* **Initial Release** - Merged WooCommerce Carousel/Grid Marketplace and WooCommerce Experience Level Pricing into a single unified plugin
* Combined tier pricing database with marketplace display
* Single unified product metabox for marketplace and pricing settings
* Backward compatibility functions for existing integrations
* Auto-migration from separate WELP and CGM plugins
* Unified namespace `WC_CGMP\` for all classes
* Merged CSS/JS assets with consistent naming

## Credits

- **Author**: [Jerel Yoshida](https://github.com/Jerel-R-Yoshida)
- **License**: GPL v2 or later

## Support

For bug reports and feature requests, please use the [GitHub Issues](https://github.com/Jerel-R-Yoshida/wc-carousel-grid-marketplace-and-pricing/issues) page.
