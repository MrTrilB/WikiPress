<?php

namespace TrilBDev\WikiPress\API;

use TrilBDev\WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class API {
    public static function list_pages( array $args = [] ): array {
        $query = new \WP_Query( wp_parse_args( $args, [ 'post_type' => PostType::PAGE, 'posts_per_page' => 20, 'paged' => 1, 'post_status' => 'publish' ] ) );
        return [ 'items' => array_map( [ self::class, 'format_post' ], $query->posts ), 'total' => (int) $query->found_posts, 'page' => (int) ( $args['paged'] ?? 1 ) ];
    }

    public static function get_page( int $id ): ?array {
        $post = get_post( $id );
        return $post && $post->post_type === PostType::PAGE ? self::format_post( $post ) : null;
    }

    public static function create_page( array $payload ): \WP_Error|array {
        $title = sanitize_text_field( $payload['title'] ?? '' );
        if ( $title === '' ) {
            return new \WP_Error( 'missing_title', __( 'A page title is required.', 'wikipress' ) );
        }
        $id = wp_insert_post( [ 'post_type' => PostType::PAGE, 'post_title' => $title, 'post_content' => wp_kses_post( $payload['content'] ?? '' ), 'post_status' => sanitize_key( $payload['status'] ?? 'draft' ) ], true );
        return is_wp_error( $id ) ? $id : self::get_page( (int) $id );
    }

    public static function format_post( \WP_Post $post ): array {
        return [ 'id' => $post->ID, 'title' => get_the_title( $post ), 'content' => $post->post_content, 'status' => $post->post_status, 'author' => (int) $post->post_author, 'date' => $post->post_date_gmt, 'link' => get_permalink( $post ) ];
    }
}
