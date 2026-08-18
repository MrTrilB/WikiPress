<?php

namespace WikiPress\Includes\Analytics;

use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Analytics {
    public static function table_name(): string {
        return Database::table_name( 'analytics' );
    }

    public static function track_view(): void {
        if ( ! is_singular( PostType::PAGE ) || ! is_main_query() || is_admin() ) {
            return;
        }
        $post_id = get_queried_object_id();
        if ( $post_id > 0 ) {
            global $wpdb;
            $wpdb->insert(
                self::table_name(),
                [
                    'post_id'   => $post_id,
                    'user_id'   => get_current_user_id(),
                    'viewed_at' => current_time( 'mysql', true ),
                ],
                [ '%d', '%d', '%s' ]
            );
        }
    }

    public static function views( int $post_id ): int {
        global $wpdb;
        return absint( $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . self::table_name() . ' WHERE post_id = %d', $post_id ) ) );
    }

    public static function total_views(): int {
        global $wpdb;
        return absint( $wpdb->get_var( 'SELECT COUNT(*) FROM ' . self::table_name() ) );
    }

    public static function top_pages( int $limit = 10 ): array {
        global $wpdb;
        $limit = max( 1, absint( $limit ) );
        $posts_table = $wpdb->posts;
        $analytics_table = self::table_name();
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT p.ID, p.post_title, COUNT(a.id) AS views
            FROM {$posts_table} p
            INNER JOIN {$analytics_table} a ON a.post_id = p.ID
            WHERE p.post_type = %s AND p.post_status = 'publish'
            GROUP BY p.ID, p.post_title
            ORDER BY views DESC
            LIMIT %d",
            PostType::PAGE,
            $limit
        ) );

        return array_map( static fn( $row ) => [
            'id'    => (int) $row->ID,
            'title' => get_the_title( (int) $row->ID ),
            'views' => (int) $row->views,
            'link'  => get_permalink( (int) $row->ID ),
        ], $rows ?: [] );
    }
}
