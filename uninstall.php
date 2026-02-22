<?php
defined('ABSPATH') || exit;

if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

define('WC_CGMP_TABLE_TIERS', 'cgmp_product_tiers');
define('WC_CGMP_TABLE_SALES', 'cgmp_order_tier_sales');

require_once __DIR__ . '/src/Core/Uninstaller.php';

WC_CGMP\Core\Uninstaller::uninstall();
