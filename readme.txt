=== WooCommerce Carousel/Grid Marketplace & Pricing ===
Contributors: jerelyoshida
Tags: woocommerce, marketplace, tiered pricing, elementor, carousel, grid, services
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.4.0
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

= 1.4.0 =
* New: Added minified JavaScript and CSS assets (.min.js, .min.css) for better performance
* New: Implemented performance caching with transients for product queries (5-15 minute TTL)
* New: Added pricing tier visibility controls - show/hide individual tiers per product
* Fix: Corrected WC_CGMP_VERSION constant mismatch (was 1.3.7, now 1.4.0)
* Performance: Optimized product queries with transient caching
* Performance: Reduced page load time with minified assets in production
* Enhancement: Added cache invalidation on product/tier updates
* Enhancement: Added database migration for tier visibility feature

= 1.3.8 =
* Feature: Tier name and price validation in admin metabox
* Enhancement: Improved error handling in AJAX handlers
* Enhancement: Added loading states for better UX
* Fix: Version constant mismatch documentation note

= 1.3.7 =
* Fix: Corrected version constant from 1.3.6 to 1.3.7
* Enhancement: Improved compatibility with WooCommerce 8.0+
* Enhancement: Added better error logging for database operations

= 1.3.6 =
* Fix: Corrected tier name field mapping in admin metabox
* Fix: Display custom tier name in product card badge

= 1.3.5 =
* Fix: Corrected text domain in product-card.php template

= 1.3.3 =
* Security: Fixed potential SQL injection via dynamic column names
* Security: Fixed XSS vulnerability by removing extract() in templates
* Security: Added WP_UNINSTALL_PLUGIN check to prevent unauthorized deletion
* Security: Fixed nonce bypass vulnerability in cart integration
* Security: Added proper input sanitization with wp_unslash()
* Security: Escaped table identifiers in database migrations
* Performance: Replaced N+1 queries with optimized single JOIN queries
* Performance: Added caching to marketplace product count queries
* Fix: Added null checks before accessing tier properties
* Fix: Added input length limits to prevent DoS attacks

= 1.3.1 =
* Fix: Add collapse functionality to Action Buttons section in product edit page

= 1.2.8 =
* Feature: Added Elementor builder preview with mock product cards for easier page building
* Enhancement: Rich placeholder content showing tier filters, pricing panels, and sidebar in editor

= 1.2.4 =
* Fix: Plugin activation error caused by early Plugin class instantiation
* Fix: Version header mismatch between file header and constant
* Added: GitHub Actions workflow for automated releases with validation
* Added: Build scripts for consistent ZIP package creation

= 1.2.3 =
* Changed: Cleaned up plugin folder - moved development files to builder

= 1.2.2 =
* Changed: Cleaned up plugin folder - moved dev files to builder

= 1.2.1 =
* Fix: IDE compatibility fixes for Intelephense
* Changed: Bumped version and updated changelog

= 1.2.0 =
* Feature: Added 54 Elementor style controls for buttons, toggles, tier filters, pricing panels

= 1.1.6 =
* Changed: Updated .gitignore to exclude local development files from distribution

= 1.1.5 =
* Feature: Redesigned product metabox with professional styling
* Feature: Added tier icons (medal, star, crown) for visual distinction
* Feature: Added live price preview in tier cards
* Feature: Added tooltips with help text on all fields
* Feature: Added collapsible Display Options section
* Feature: Added color-coded tier headers (green/blue/purple)

= 1.1.4 =
* Fix: Fixed fatal error in wc_cgmp_log() - added wc_cgmp_logger() helper function
* Fix: Removed trailing whitespace from template files

= 1.1.3 =
* Fix: Zip file now uses forward slashes for Linux server compatibility

= 1.1.2 =
* Fix: Added explicit require for Activator/Deactivator classes to prevent autoloader issues during plugin activation

= 1.1.1 =
* Update .gitignore with AI Builder patterns and improved organization

= 1.1.0 =
* Initial release - Merged WooCommerce Carousel/Grid Marketplace and WooCommerce Experience Level Pricing
* Combined tier pricing database with marketplace display
* Single unified product metabox
* Auto-migration from separate plugins
* Backward compatibility for existing integrations

== Upgrade Notice ==

= 1.3.3 =
Security release - fixes SQL injection, XSS vulnerabilities, and nonce bypass issues. Recommended for all users.

= 1.1.6 =
Maintenance release - updates .gitignore for cleaner distribution builds.

= 1.1.5 =
UI refresh with professional metabox styling, tier icons, live price preview, and tooltips.

= 1.1.4 =
Critical fix for wc_cgmp_log() fatal error - adds new wc_cgmp_logger() helper function.

= 1.1.3 =
Critical fix for zip file path separators - now works on Linux servers.

= 1.1.2 =
Critical fix for plugin activation errors on some server configurations.

= 1.1.1 =
Maintenance release with improved development file patterns.

= 1.1.0 =
Initial release of the combined plugin. If upgrading from separate WELP or CGM plugins, your data will be automatically migrated.
