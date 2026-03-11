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

## Documentation

- [Wiki](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki)
- [Installation Guide](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Installation)
- [Shortcode Reference](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Shortcode-Reference)
- [Hooks and Filters](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/wiki/Hooks-and-Filters)

## Support

- **GitHub Issues**: [https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/issues](https://github.com/yoshidaman99/wc-carousel-grid-marketplace-and-pricing/issues)

## Changelog

### 1.7.9
- Fixed: Icon not displaying in "above button link" on marketplace cards
- Improved icon data handling using base64 encoding for shortcode attribute passing
- Added SVG icon support in fallback rendering
- Removed `position: static` from mobile Popular badge for proper positioning

### 1.7.8
- Added individual border radius controls for Popular badge (Top-Left, Top-Right, Bottom-Right, Bottom-Left)
- Fixed padding control for Popular badge - now works correctly with responsive settings
- Default radius: TL=0, TR=0, BR=20px, BL=0
- Default padding: 0px 12px 0px 12px

### 1.7.7
- Updated Popular badge position to top-left corner (0, 0)
- Changed border-radius to 0 0 20px 0 (rounded bottom-right only)
- Reduced padding to 0px 12px and font-size to 10px
- Removed text-transform uppercase styling

### 1.7.6
- Removed bottom margin from .wc-cgmp-pricing-amount for tighter layout

### 1.7.5
- Fixed: Icon not showing in "above button link" section
- Added fallback icon rendering when Elementor Icons_Manager is unavailable
- Improved icon data handling for better JSON decode reliability

### 1.7.4
- Version bump for release

### 1.7.3
- Redesigned pricing panel layout: "Starting at $X/hr" now displays first
- Tier selector moved outside pricing panel div for cleaner structure
- Simplified price display - always shows hourly rate with /hr suffix
- "Starting at" prefix enabled by default

### 1.7.2
- Moved Popular badge from right to left side of the card

### 1.7.1
- Added "Debug Popular Status" toggle in Elementor widget (Popular Badge section)
- Debug info now hidden by default - enable via Elementor when troubleshooting
- Shows: Popular status, Method setting, and Meta value for each product

### 1.7.0
- Added debug info below card title to troubleshoot Popular badge display issues
- Shows Popular status (true/false), Method setting, and Meta value
- Fixed: Popular badge not showing when method is set to 'auto' (now requires 'both' or 'manual')
- Note: Existing sites need to update `wc_cgmp_popular_method` option to 'both' in database

### 1.6.10
- Added debug text below title showing Popular status, method, and meta value
- Helps troubleshoot why popular badge may not be showing on live site

### 1.6.9
- Fixed: Popular badge not showing when "Mark as Popular" checkbox enabled on products
- Changed default popular_method from 'auto' to 'both' - now respects manual checkbox AND auto sales detection
- New installations will automatically check both methods for popular products

### 1.6.8
- Added documentation for Popular badge database structure and SQL queries
- Documented `_wc_cgmp_popular` meta key for manual popular marking
- Added SQL examples for marking products as popular via database
- Clarified popular method settings: auto (order-based), manual (meta-based), or both

### 1.6.7
- Changed show_popular_mark default to true - popular mark now shows automatically
- Popular products will display ‹popular› text next to title by default

### 1.6.6
- Added popular mark text next to service title for popular products
- New Elementor controls: show/hide mark, customize text, color, and font size
- Default mark text: ‹popular› with customizable styling
- Works alongside existing Popular badge
- Available via shortcode: show_popular_mark, popular_mark_text

### 1.6.5
- Version bump for release

### 1.6.4
- Added Link Above Button style controls in Elementor widget
- New style options: text color, hover color, typography (font settings)
- Icon color, hover color, and size controls (10-40px)
- Highlight text color and hover color controls
- Icon gap and margin bottom spacing controls
- Fixed icon not showing - proper JSON encoding/decoding for Elementor icons
- Added SVG icon support in CSS

### 1.6.3
- Fix: Array to string conversion warning in wp-includes/formatting.php line 1128
- Fix: Added array check in build_shortcode_string() for Elementor icon controls
- Fix: Added safeSanitize helper in Cart_Integration.php to handle array values from AJAX
- Fix: Added array check for data-limit attribute in marketplace template

### 1.6.2
- Fix: Array to string conversion warning for text attributes (popular_badge_text, override_button_text, above_link_text, etc.)
- Fix: Proper handling of Elementor text control arrays across all templates

### 1.6.1
- Fix: Array to string conversion error when using Elementor URL control arrays
- Fix: Proper handling of URL attributes in templates

### 1.6.0
- Toggle to include/exclude total parameter in button override URL
- Link above button with icon, text, highlight text, and URL (e.g., "Need 5+ headcount? Get volume pricing")
- Comprehensive sidebar category style controls in Elementor (colors, fonts, padding, radius, icons)
- Popular badge now also shows for WooCommerce featured products
- Fixed category click to properly pass all button override settings
- Fixed search and load more to include all button override settings

### 1.5.9
- Added Display Options section in Elementor widget
- Toggle to show/hide headcount selector on product cards
- Toggle to show/hide total display on product cards
- Button override feature: Replace Add to Cart with custom button
- Custom button URL with dynamic total value parameter
- Configurable URL parameter name for total (default: "total")
- Open custom button link in new tab option
- Dynamic URL updates when quantity, tier, or price type changes

### 1.5.8
- Added remove cart item functionality for mini cart widget
- Added quantity update controls (increase/decrease) for cart items
- New AJAX endpoints: `wc_cgmp_remove_cart_item`, `wc_cgmp_update_cart_quantity`
- Refactored cart data preparation into shared `prepare_cart_data()` helper
- Added i18n strings for cart operations (confirm_remove, item_removed, quantity_updated)

### 1.5.7
- Fixed layout sync between initial load and AJAX category filtering
- Added missing data attributes to grid container for AJAX requests
- Fixed missing tier badges and descriptions on category click
- Fixed missing popular badges on AJAX filtered results
- Fixed missing price prefix options on category filter
- Added `getGridAtts()` helper to collect all grid attributes consistently
- Updated all AJAX handlers (filter, load-more, search) to pass complete `$atts` array

### 1.5.6
- Fixed WP_Post object to int conversion warning in marketplace template (line 21)
- Fixed popular products aggregation query running once per request instead of per product
- Batch preload tier data with `preload_tiers()``
- Assets only load on pages with marketplace
- Conditional assets loading on pages with shortcode/Elementor widget
- Moved stale path repair to admin only (### 1.5.5
- Major performance optimization for loading 100+ products
- Fixed N+1 query problem: batch preload all tier data in a single SQL query instead of 1 query per product
- Fixed popular products aggregation query running once per product (now cached per request)
- Added in-memory tier cache to avoid duplicate queries within a single request
- Conditional asset loading: CSS/JS only loads on pages with the marketplace shortcode or Elementor widget
- Moved stale path repair to admin-only context (no longer runs on every frontend page)
- Removed duplicate get_specialization() call in pricing panel rendering
- Added preload_tiers() to all AJAX handlers (filter, load-more, search)
- Query reduction: ~500-800 queries for 100 products reduced to ~5-10 queries

### 1.5.6
- Fixed WP_Post object to int conversion warning in marketplace template (line 21)
- Properly extracts IDs from WP_Post objects
- Fixed wp_post object to int conversion in preload_tiers()
- Updated version and README to 1.5.6
- Updated readme.txt stable tag to 1.5.6
- Fixed price alignment - price now displays on far right with prefix on left
- Improved pricing panel layout with space-between justification
- Fixed price and period (/hr) spacing - now closer together

### 1.5.3
- Fixed price prefix display to align inline with price
- Fixed separator display when only one price type (hourly or monthly) exists
- Improved price prefix CSS styling for better alignment

### 1.5.2
- Fixed undefined array key warnings for exclude_category and popular_only
- Fixed secondary price showing $0.00 when only hourly or monthly price set

### 1.5.1
- Fixed undefined array key warnings in Frontend_Manager.php
- Fixed price display alignment for inline prefix

### 1.5.0
- Major update with enhanced pricing panel
- Added price prefix support (inline and above positions)
- Improved Elementor widget styling controls

### 1.4.8
- Dynamic section title based on category selection
- Enhanced marketplace functionality
- Multiple component improvements

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
- GitHub: [https://github.com/yoshidaman99](https://github.com/yoshidaman99)
