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
- **WooCommerce Reports** - Sales breakdown by tier
- **Popular Badges** - Automatic or manual highlighting of popular services

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

### Elementor Widget

1. Edit a page with Elementor
2. Search for **"Marketplace"** in the widgets panel
3. Drag the **WooCommerce Marketplace** widget to your page
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

## Support

- **GitHub Issues**: [https://github.com/Jerel-R-Yoshida/wc-carousel-grid-marketplace-and-pricing/issues](https://github.com/Jerel-R-Yoshida/wc-carousel-grid-marketplace-and-pricing/issues)

## Changelog

### 1.4.0
- Added rate limiting to public AJAX endpoints
- Improved cache invalidation on tier updates
- Fixed ZIP path separators for cross-platform compatibility

### 1.3.9
- Stable release with Elementor widget improvements

### 1.3.8
- Bug fixes and performance improvements

## License

GPL v2 or later. See [LICENSE](LICENSE) for more information.

## Author

**Jerel Yoshida**
- GitHub: [https://github.com/Jerel-R-Yoshida](https://github.com/Jerel-R-Yoshida)
