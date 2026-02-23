=== WooCommerce Carousel/Grid Marketplace & Pricing ===
Contributors: jerelyoshida
Tags: woocommerce, marketplace, tiered pricing, elementor, carousel, grid, services
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Service marketplace with carousel/grid layout and tiered pricing (Entry/Mid/Expert) with monthly/hourly rates.

== Description ==

A powerful WordPress plugin that combines a modern service marketplace with tiered pricing. Features carousel/grid layouts, Elementor support, and comprehensive WooCommerce integration.

= Key Features =

* **Modern Card Design** - Beautiful service cards with pricing panels and headcount selectors
* **3-Tier Pricing System** - Entry, Mid, Expert levels with monthly and hourly rates
* **Grid or Carousel Layout** - Choose the display that fits your site
* **Elementor Widget** - Full visual controls for design customization
* **Shortcode Support** - Use [wc_cgmp_marketplace] anywhere
* **AJAX Filtering** - Fast filtering without page reloads
* **WooCommerce Reports** - Sales breakdown by tier

= How It Works =

1. Install and activate the plugin
2. Edit WooCommerce products and enable for marketplace
3. Configure tier pricing (Entry, Mid, Expert with monthly/hourly rates)
4. Use shortcode or Elementor widget to display marketplace
5. Customers select tier and add to cart

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/wc-carousel-grid-marketplace-and-pricing/`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Ensure WooCommerce is active
4. Configure settings at WooCommerce > Marketplace & Pricing

== Frequently Asked Questions ==

= How do I enable tier pricing for a product? =

Edit any product and check "Enable for Marketplace" in the Marketplace & Pricing metabox. Configure your three tiers with names, prices, and descriptions.

= Does this work with any WooCommerce product type? =

Yes! Experience Level Pricing works with ALL WooCommerce product types including Simple, Variable, Grouped, and External/Affiliate products.

= Where is the pricing data stored? =

Pricing tiers are stored in separate custom database tables, ensuring your WooCommerce core data remains unaffected.

= Can I use this without Elementor? =

Yes! The shortcode `[wc_cgmp_marketplace]` works on any page or post.

== Screenshots ==

1. Marketplace grid view with tier pricing
2. Product metabox with tier configuration
3. Elementor widget settings
4. WooCommerce reports by tier

== Changelog ==

= 1.1.1 =
* Update .gitignore with AI Builder patterns and improved organization

= 1.1.0 =
* Initial release - Merged WooCommerce Carousel/Grid Marketplace and WooCommerce Experience Level Pricing
* Combined tier pricing database with marketplace display
* Single unified product metabox
* Auto-migration from separate plugins
* Backward compatibility for existing integrations

== Upgrade Notice ==

= 1.1.1 =
Maintenance release with improved development file patterns.

= 1.1.0 =
Initial release of the combined plugin. If upgrading from separate WELP or CGM plugins, your data will be automatically migrated.
