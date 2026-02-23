<?php

namespace WC_CGMP\Database;

defined('ABSPATH') || exit;

class Repository
{
    private $wpdb;
    private string $tiers_table;
    private string $sales_table;

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->tiers_table = $wpdb->prefix . WC_CGMP_TABLE_TIERS;
        $this->sales_table = $wpdb->prefix . WC_CGMP_TABLE_SALES;
    }

    public function insert_tiers(int $product_id, array $tiers): bool
    {
        $success = true;
        wc_cgmp_logger()->start_timer('insert_tiers');

        foreach ($tiers as $tier) {
            $existing = $this->get_tier($product_id, (int) $tier['tier_level']);

            $data = [
                'product_id' => $product_id,
                'tier_level' => (int) $tier['tier_level'],
                'tier_name' => sanitize_text_field($tier['tier_name']),
                'monthly_price' => isset($tier['monthly_price']) && $tier['monthly_price'] !== '' ? (float) $tier['monthly_price'] : null,
                'hourly_price' => isset($tier['hourly_price']) && $tier['hourly_price'] !== '' ? (float) $tier['hourly_price'] : null,
                'description' => isset($tier['description']) ? wp_kses_post($tier['description']) : '',
            ];

            if ($existing) {
                $result = $this->wpdb->update(
                    $this->tiers_table,
                    $data,
                    ['id' => $existing->id],
                    ['%d', '%d', '%s', '%f', '%f', '%s'],
                    ['%d']
                );
                $operation = 'UPDATE';
            } else {
                $result = $this->wpdb->insert(
                    $this->tiers_table,
                    $data,
                    ['%d', '%d', '%s', '%f', '%f', '%s']
                );
                $operation = 'INSERT';
            }

            if ($result === false) {
                $success = false;
                wc_cgmp_logger()->db_error(
                    $operation . ' tier',
                    $this->wpdb->last_error ?: 'Unknown database error',
                    [
                        'product_id' => $product_id,
                        'tier_level' => $tier['tier_level'] ?? 0,
                    ]
                );
            }
        }

        if ($success) {
            wc_cgmp_logger()->stop_timer('insert_tiers', 'insert_tiers completed');
        }

        return $success;
    }

    public function get_tiers_by_product(int $product_id): array
    {
        $results = $this->wpdb->get_results(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->tiers_table} WHERE product_id = %d ORDER BY tier_level ASC",
                $product_id
            )
        );

        if ($results === false) {
            wc_cgmp_logger()->db_error('SELECT tiers by product', $this->wpdb->last_error, [
                'product_id' => $product_id,
            ]);
            return [];
        }

        return $results ? $results : [];
    }

    public function get_tier(int $product_id, int $tier_level): ?object
    {
        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->tiers_table} WHERE product_id = %d AND tier_level = %d",
                $product_id,
                $tier_level
            )
        );

        if ($result === false) {
            wc_cgmp_logger()->db_error('SELECT tier', $this->wpdb->last_error, [
                'product_id' => $product_id,
                'tier_level' => $tier_level,
            ]);
            return null;
        }

        return $result;
    }

    public function delete_tiers(int $product_id): bool
    {
        $result = $this->wpdb->delete(
            $this->tiers_table,
            ['product_id' => $product_id],
            ['%d']
        );

        if ($result === false) {
            wc_cgmp_logger()->db_error('DELETE tiers', $this->wpdb->last_error, [
                'product_id' => $product_id,
            ]);
            return false;
        }

        wc_cgmp_logger()->info('Tiers deleted', ['product_id' => $product_id]);
        return true;
    }

    public function get_price_range(int $product_id, string $price_type = 'monthly'): array
    {
        $column = $price_type === 'hourly' ? 'hourly_price' : 'monthly_price';

        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT MIN({$column}) as min_price, MAX({$column}) as max_price FROM {$this->tiers_table} WHERE product_id = %d AND {$column} IS NOT NULL",
                $product_id
            )
        );

        if ($result === false) {
            wc_cgmp_logger()->db_error('SELECT price range', $this->wpdb->last_error, [
                'product_id' => $product_id,
                'price_type' => $price_type,
            ]);
            return ['min' => 0, 'max' => 0];
        }

        if (!$result || $result->min_price === null) {
            return ['min' => 0, 'max' => 0];
        }

        return [
            'min' => (float) $result->min_price,
            'max' => (float) $result->max_price,
        ];
    }

    public function get_available_price_types(int $product_id): array
    {
        $result = $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT SUM(CASE WHEN monthly_price IS NOT NULL THEN 1 ELSE 0 END) as monthly_count, SUM(CASE WHEN hourly_price IS NOT NULL THEN 1 ELSE 0 END) as hourly_count FROM {$this->tiers_table} WHERE product_id = %d",
                $product_id
            )
        );

        if ($result === false) {
            wc_cgmp_logger()->db_error('SELECT available price types', $this->wpdb->last_error, [
                'product_id' => $product_id,
            ]);
            return [];
        }

        $types = [];
        if ($result && $result->monthly_count > 0) {
            $types[] = 'monthly';
        }
        if ($result && $result->hourly_count > 0) {
            $types[] = 'hourly';
        }

        return $types;
    }

    public function record_tier_sale(
        int $order_id,
        int $product_id,
        int $tier_level,
        string $tier_name,
        float $price,
        string $price_type,
        int $quantity
    ): bool {
        $order = \wc_get_order($order_id);
        if (!$order) {
            wc_cgmp_logger()->warning('Order not found for tier sale recording', [
                'order_id' => $order_id,
                'product_id' => $product_id,
            ]);
            return false;
        }

        $order_date = $order->get_date_created();
        if ($order_date instanceof \WC_DateTime) {
            $order_date = $order_date->date('Y-m-d H:i:s');
        } else {
            $order_date = current_time('mysql');
        }

        $result = $this->wpdb->insert(
            $this->sales_table,
            [
                'order_id' => $order_id,
                'product_id' => $product_id,
                'tier_level' => $tier_level,
                'tier_name' => sanitize_text_field($tier_name),
                'price' => $price,
                'price_type' => sanitize_text_field($price_type),
                'quantity' => $quantity,
                'total' => $price * $quantity,
                'order_date' => $order_date,
            ],
            ['%d', '%d', '%d', '%s', '%f', '%s', '%d', '%f', '%s']
        );

        if ($result === false) {
            wc_cgmp_logger()->db_error('INSERT tier sale', $this->wpdb->last_error, [
                'order_id' => $order_id,
                'product_id' => $product_id,
                'tier_level' => $tier_level,
            ]);
            return false;
        }

        wc_cgmp_logger()->info('Tier sale recorded', [
            'order_id' => $order_id,
            'product_id' => $product_id,
            'tier_level' => $tier_level,
            'price' => $price,
            'price_type' => $price_type,
            'quantity' => $quantity,
        ]);

        return true;
    }

    public function get_sales_by_tier(array $args = []): array
    {
        $defaults = [
            'start_date' => '',
            'end_date' => '',
            'product_id' => 0,
            'group_by_price_type' => false,
        ];
        $args = \wp_parse_args($args, $defaults);

        $where = ['1=1'];
        $values = [];

        if (!empty($args['start_date'])) {
            $where[] = 'order_date >= %s';
            $values[] = $args['start_date'] . ' 00:00:00';
        }

        if (!empty($args['end_date'])) {
            $where[] = 'order_date <= %s';
            $values[] = $args['end_date'] . ' 23:59:59';
        }

        if (!empty($args['product_id'])) {
            $where[] = 'product_id = %d';
            $values[] = (int) $args['product_id'];
        }

        $groupBy = 'tier_level, tier_name';
        if (!empty($args['group_by_price_type'])) {
            $groupBy .= ', price_type';
        }

        $sql = "SELECT tier_level, tier_name, price_type,
                SUM(quantity) as total_quantity,
                SUM(total) as total_revenue,
                COUNT(DISTINCT order_id) as total_orders,
                AVG(price) as avg_price
                FROM {$this->sales_table}
                WHERE " . implode(' AND ', $where) . "
                GROUP BY {$groupBy}
                ORDER BY tier_level ASC, price_type ASC";

        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }

        $results = $this->wpdb->get_results($sql);

        if ($results === false) {
            wc_cgmp_logger()->db_error('SELECT sales by tier', $this->wpdb->last_error, $args);
            return [];
        }

        return $results;
    }

    public function get_sales_by_product(array $args = []): array
    {
        $defaults = [
            'start_date' => '',
            'end_date' => '',
            'tier_level' => 0,
            'product_id' => 0,
            'price_type' => '',
        ];
        $args = \wp_parse_args($args, $defaults);

        $where = ['1=1'];
        $values = [];

        if (!empty($args['start_date'])) {
            $where[] = 's.order_date >= %s';
            $values[] = $args['start_date'] . ' 00:00:00';
        }

        if (!empty($args['end_date'])) {
            $where[] = 's.order_date <= %s';
            $values[] = $args['end_date'] . ' 23:59:59';
        }

        if (!empty($args['tier_level'])) {
            $where[] = 's.tier_level = %d';
            $values[] = (int) $args['tier_level'];
        }

        if (!empty($args['product_id'])) {
            $where[] = 's.product_id = %d';
            $values[] = (int) $args['product_id'];
        }

        if (!empty($args['price_type'])) {
            $where[] = 's.price_type = %s';
            $values[] = sanitize_text_field($args['price_type']);
        }

        $sql = "SELECT s.product_id, p.post_title as product_name, s.tier_level, s.tier_name, s.price_type,
                SUM(s.quantity) as total_quantity,
                SUM(s.total) as total_revenue
                FROM {$this->sales_table} s
                LEFT JOIN {$this->wpdb->posts} p ON s.product_id = p.ID
                WHERE " . implode(' AND ', $where) . "
                GROUP BY s.product_id, s.tier_level, s.price_type
                ORDER BY total_revenue DESC";

        if (!empty($values)) {
            $sql = $this->wpdb->prepare($sql, $values);
        }

        $results = $this->wpdb->get_results($sql);

        if ($results === false) {
            wc_cgmp_logger()->db_error('SELECT sales by product', $this->wpdb->last_error, $args);
            return [];
        }

        return $results;
    }

    public function get_all_tier_names(): array
    {
        $results = $this->wpdb->get_col(
            "SELECT DISTINCT tier_name FROM {$this->tiers_table} ORDER BY tier_level ASC"
        );

        if ($results === false) {
            wc_cgmp_logger()->db_error('SELECT tier names', $this->wpdb->last_error);
            return [];
        }

        return $results;
    }

    public function has_tiers(int $product_id): bool
    {
        $count = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->tiers_table} WHERE product_id = %d",
                $product_id
            )
        );

        if ($count === false) {
            wc_cgmp_logger()->db_error('COUNT tiers', $this->wpdb->last_error, [
                'product_id' => $product_id,
            ]);
            return false;
        }

        return (int) $count > 0;
    }

    public function get_products_with_tier(int $tier_level): array
    {
        $results = $this->wpdb->get_col($this->wpdb->prepare(
            "SELECT DISTINCT product_id FROM {$this->tiers_table} WHERE tier_level = %d",
            $tier_level
        ));

        return array_map('intval', $results);
    }

    public function get_marketplace_products(array $args = []): array
    {
        $defaults = [
            'category' => '',
            'exclude_category' => '',
            'products' => '',
            'tier' => 0,
            'limit' => 12,
            'offset' => 0,
            'orderby' => 'date',
            'order' => 'DESC',
            'popular_only' => false,
            'search' => '',
            'marketplace_only' => false,
        ];

        $args = wp_parse_args($args, $defaults);
        $cache_key = 'wc_cgmp_products_' . md5(wp_json_encode($args));
        $cached = wp_cache_get($cache_key, 'wc_cgmp');

        if (false !== $cached) {
            return $cached;
        }

        $query_args = [
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => (int) $args['limit'],
            'offset' => (int) $args['offset'],
            'orderby' => $args['orderby'],
            'order' => $args['order'],
        ];

        if (!empty($args['marketplace_only'])) {
            $query_args['meta_query'] = [
                'relation' => 'OR',
                [
                    'key' => '_wc_cgmp_enabled',
                    'value' => 'yes',
                    'compare' => '=',
                ],
                [
                    'key' => '_welp_enabled',
                    'value' => 'yes',
                    'compare' => '=',
                ],
            ];
        }

        if (!empty($args['category'])) {
            $categories = is_array($args['category']) ? $args['category'] : explode(',', $args['category']);
            $query_args['tax_query'] = [
                [
                    'taxonomy' => 'product_cat',
                    'field' => 'term_id',
                    'terms' => array_map('intval', $categories),
                ],
            ];
        }

        if (!empty($args['exclude_category'])) {
            $exclude = is_array($args['exclude_category']) ? $args['exclude_category'] : explode(',', $args['exclude_category']);
            $query_args['tax_query']['relation'] = 'AND';
            $query_args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => array_map('intval', $exclude),
                'operator' => 'NOT IN',
            ];
        }

        if (!empty($args['products'])) {
            $product_ids = is_array($args['products']) ? $args['products'] : explode(',', $args['products']);
            $query_args['post__in'] = array_map('intval', $product_ids);
            $query_args['orderby'] = 'post__in';
        }

        if (!empty($args['search'])) {
            $query_args['s'] = sanitize_text_field($args['search']);
        }

        if ($args['orderby'] === 'popularity') {
            $query_args['meta_key'] = 'total_sales';
            $query_args['orderby'] = 'meta_value_num';
        } elseif ($args['orderby'] === 'price') {
            $query_args['meta_key'] = '_price';
            $query_args['orderby'] = 'meta_value_num';
        }

        if ($args['popular_only']) {
            $method = get_option('wc_cgmp_popular_method', 'auto');
            if ($method === 'manual' || $method === 'both') {
                $query_args['meta_query'][] = [
                    'key' => '_wc_cgmp_popular',
                    'value' => 'yes',
                    'compare' => '=',
                ];
            } else {
                $popular_ids = $this->get_popular_product_ids(
                    (int) get_option('wc_cgmp_popular_threshold', 5),
                    (int) get_option('wc_cgmp_popular_days', 30)
                );
                if (!empty($popular_ids)) {
                    $query_args['post__in'] = isset($query_args['post__in'])
                        ? array_intersect($query_args['post__in'], $popular_ids)
                        : $popular_ids;
                } else {
                    return [];
                }
            }
        }

        if ($args['tier'] > 0) {
            $product_ids = $this->get_products_with_tier((int) $args['tier']);
            if (!empty($product_ids)) {
                $query_args['post__in'] = isset($query_args['post__in'])
                    ? array_intersect($query_args['post__in'] ?? [], $product_ids)
                    : $product_ids;
            } else {
                return [];
            }
        }

        $query = new \WP_Query($query_args);
        $products = $query->posts;

        wp_cache_set($cache_key, $products, 'wc_cgmp', HOUR_IN_SECONDS);

        return $products;
    }

    public function get_products_by_category(int $category_id, array $args = []): array
    {
        $args['category'] = $category_id;
        return $this->get_marketplace_products($args);
    }

    public function get_products_by_tier(int $tier, array $args = []): array
    {
        $args['tier'] = $tier;
        return $this->get_marketplace_products($args);
    }

    public function get_popular_products(int $limit = 10, int $days = 30): array
    {
        $method = get_option('wc_cgmp_popular_method', 'auto');
        $products = [];

        if ($method === 'auto' || $method === 'both') {
            $products = $this->get_popular_product_ids(
                (int) get_option('wc_cgmp_popular_threshold', 5),
                $days
            );
        }

        if ($method === 'manual' || $method === 'both') {
            $manual_ids = get_posts([
                'post_type' => 'product',
                'posts_per_page' => $limit,
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => '_wc_cgmp_popular',
                        'value' => 'yes',
                        'compare' => '=',
                    ],
                    [
                        'key' => '_wc_cgm_popular',
                        'value' => 'yes',
                        'compare' => '=',
                    ],
                ],
                'fields' => 'ids',
            ]);

            $products = array_unique(array_merge($products, $manual_ids));
        }

        return array_slice($products, 0, $limit);
    }

    public function get_popular_product_ids(int $threshold = 5, int $days = 30): array
    {
        $date_from = gmdate('Y-m-d H:i:s', strtotime("-{$days} days"));

        $results = $this->wpdb->get_col($this->wpdb->prepare(
            "SELECT product_id
            FROM {$this->sales_table}
            WHERE order_date >= %s
            GROUP BY product_id
            HAVING COUNT(*) >= %d
            ORDER BY COUNT(*) DESC",
            $date_from,
            $threshold
        ));

        return array_map('intval', $results);
    }

    public function is_popular_auto(int $product_id): bool
    {
        $threshold = (int) get_option('wc_cgmp_popular_threshold', 5);
        $days = (int) get_option('wc_cgmp_popular_days', 30);
        $popular_ids = $this->get_popular_product_ids($threshold, $days);

        return in_array($product_id, $popular_ids, true);
    }

    public function get_categories_with_product_counts(): array
    {
        $categories = get_terms([
            'taxonomy' => 'product_cat',
            'hide_empty' => true,
            'orderby' => 'name',
            'order' => 'ASC',
        ]);

        if (is_wp_error($categories)) {
            return [];
        }

        $result = [];
        $total_count = 0;

        foreach ($categories as $category) {
            $args = [
                'post_type' => 'product',
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'fields' => 'ids',
                'tax_query' => [
                    [
                        'taxonomy' => 'product_cat',
                        'field' => 'term_id',
                        'terms' => $category->term_id,
                    ],
                ],
                'meta_query' => [
                    'relation' => 'OR',
                    [
                        'key' => '_wc_cgmp_enabled',
                        'value' => 'yes',
                        'compare' => '=',
                    ],
                    [
                        'key' => '_welp_enabled',
                        'value' => 'yes',
                        'compare' => '=',
                    ],
                ],
            ];

            $query = new \WP_Query($args);
            $count = $query->found_posts;

            if ($count > 0) {
                $result[] = [
                    'id' => $category->term_id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'count' => $count,
                    'icon' => get_term_meta($category->term_id, 'wc_cgmp_icon', true) ?: '',
                ];
                $total_count += $count;
            }
        }

        array_unshift($result, [
            'id' => 0,
            'name' => __('All Services', 'wc-carousel-grid-marketplace-and-pricing'),
            'slug' => 'all',
            'count' => $total_count,
            'icon' => 'grid',
        ]);

        return $result;
    }

    public function search_products(string $query, array $args = []): array
    {
        $args['search'] = $query;
        return $this->get_marketplace_products($args);
    }

    public function get_specialization(int $product_id): string
    {
        $value = get_post_meta($product_id, '_wc_cgmp_specialization', true);
        if (empty($value)) {
            $value = get_post_meta($product_id, '_wc_cgm_specialization', true);
        }
        return $value ?: '';
    }

    public function get_total_marketplace_products(): int
    {
        $query = new \WP_Query([
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => [
                'relation' => 'OR',
                [
                    'key' => '_wc_cgmp_enabled',
                    'value' => 'yes',
                ],
                [
                    'key' => '_welp_enabled',
                    'value' => 'yes',
                ],
            ],
        ]);

        return $query->found_posts;
    }
}
