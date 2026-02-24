# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.3.8] - 2026-02-25

### Fixed
- Fixed Elementor hover effect for tier badges (Entry, Mid, Expert)
- Added Elementor controls for badge hover colors (background, text, border)
- Removed `pointer-events: none;` which was blocking hover effects
- Added transition duration control for smooth badge animations
- Consolidated CSS rules for cleaner code

### Technical Details
- The tier badges now properly respond to hover states when used with Elementor widgets
- New controls in Elementor widget panel for customizing hover appearance
- Improved CSS organization and performance

## [1.3.7] - 2026-02-25

### Fixed
- Corrected tier name display in admin metabox and product card
- Improved data handling for tier pricing information

## [1.3.6] - 2026-02-24

### Fixed
- Fixed pricing display issue in marketplace cards
- Improved responsive design for mobile devices

## [1.3.5] - 2026-02-24

### Fixed
- Corrected text domain in product-card.php for proper translations
- Fixed display issues with tier pricing information

## [1.3.4] - 2026-02-23

### Added
- Added Elementor widget for marketplace integration
- Added hover effects for product cards
- Added filtering options for marketplace categories

### Fixed
- Fixed compatibility issues with latest WooCommerce version
- Improved performance of marketplace loading

## [1.3.3] - 2026-02-22

### Added
- Initial release with marketplace functionality
- Carousel and grid layout options
- Tiered pricing system (Entry/Mid/Expert)
- Elementor integration