<?php

namespace WC_CGMP\Core;

defined('ABSPATH') || exit;

class Deactivator
{
    public function deactivate(): void
    {
        $this->clear_scheduled_events();
        flush_rewrite_rules();
    }

    private function clear_scheduled_events(): void
    {
        wp_clear_scheduled_hook('wc_cgmp_daily_popular_check');
    }
}
