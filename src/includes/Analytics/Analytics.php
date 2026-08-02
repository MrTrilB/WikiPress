<?php

namespace TrilBDev\WikiPress\Includes\Analytics;

use TrilBDev\WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Analytics {
    public static function track_view(): void {
        if ( ! is_singular( PostType::PAGE ) || ! is_main_query() || is_admin() ) {
            return;
        }
        $post_id = get_queried_object_id();
        if ( $post_id > 0 ) {
            update_post_meta( $post_id, '_wikipress_view_count', self::views( $post_id ) + 1 );
        }
    }

    public static function views( int $post_id ): int {
        return absint( get_post_meta( $post_id, '_wikipress_view_count', true ) );
    }

    public static function total_views(): int {
        $query = new \WP_Query( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => -1, 'fields' => 'ids' ] );
        $total = 0;
        foreach ( $query->posts as $post_id ) {
            $total += self::views( (int) $post_id );
        }
        return $total;
    }

    public static function top_pages( int $limit = 10 ): array {
        $query = new \WP_Query( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => max( 1, $limit ), 'meta_key' => '_wikipress_view_count', 'orderby' => 'meta_value_num', 'order' => 'DESC' ] );
        return array_map( static fn( $post ) => [ 'id' => $post->ID, 'title' => get_the_title( $post ), 'views' => self::views( $post->ID ), 'link' => get_permalink( $post ) ], $query->posts );
    }
}
