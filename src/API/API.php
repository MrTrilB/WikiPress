<?php

namespace TrilBDev\WikiPress\API;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Functions\Functions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class API {
    public static function list_wikis( array $args = [] ): array {
        $query = new \WP_Query( wp_parse_args( $args, [ 'post_type' => PostType::WIKI, 'posts_per_page' => 20, 'paged' => 1, 'post_status' => 'publish' ] ) );
        return [ 'items' => array_map( [ self::class, 'format_wiki' ], $query->posts ), 'total' => (int) $query->found_posts, 'page' => (int) ( $args['paged'] ?? 1 ) ];
    }

    public static function get_wiki( int $id ): ?array {
        $post = get_post( $id );
        return $post && $post->post_type === PostType::WIKI ? self::format_wiki( $post ) : null;
    }

    public static function create_wiki( array $payload ): \WP_Error|array {
        $title = sanitize_text_field( $payload['title'] ?? '' );
        if ( $title === '' ) {
            return new \WP_Error( 'missing_title', __( 'A Wiki name is required.', 'wikipress' ) );
        }
        $id = wp_insert_post( [
            'post_type' => PostType::WIKI,
            'post_title' => $title,
            'post_content' => wp_kses_post( $payload['description'] ?? '' ),
            'post_status' => self::sanitize_status( $payload['status'] ?? 'publish' ),
        ], true );
        return is_wp_error( $id ) ? $id : self::get_wiki( (int) $id );
    }

    public static function update_wiki( int $id, array $payload ): \WP_Error|array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== PostType::WIKI ) {
            return new \WP_Error( 'not_found', __( 'Wiki not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        $title = sanitize_text_field( $payload['title'] ?? '' );
        if ( $title === '' ) {
            return new \WP_Error( 'missing_title', __( 'A Wiki name is required.', 'wikipress' ) );
        }
        $updated = wp_update_post( [
            'ID' => $id,
            'post_title' => $title,
            'post_content' => wp_kses_post( $payload['description'] ?? '' ),
            'post_status' => self::sanitize_status( $payload['status'] ?? $post->post_status ),
        ], true );
        return is_wp_error( $updated ) ? $updated : self::get_wiki( $id );
    }

    public static function delete_wiki( int $id, bool $force = false ): \WP_Error|bool {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== PostType::WIKI ) {
            return new \WP_Error( 'not_found', __( 'Wiki not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        return (bool) wp_delete_post( $id, $force );
    }

    public static function list_pages( array $args = [] ): array {
        $query = new \WP_Query( wp_parse_args( $args, [ 'post_type' => PostType::PAGE, 'posts_per_page' => 20, 'paged' => 1, 'post_status' => 'publish' ] ) );
        return [ 'items' => array_map( [ self::class, 'format_post' ], $query->posts ), 'total' => (int) $query->found_posts, 'page' => (int) ( $args['paged'] ?? 1 ) ];
    }

    public static function get_page( int $id ): ?array {
        $post = get_post( $id );
        return $post && $post->post_type === PostType::PAGE ? self::format_post( $post ) : null;
    }

    public static function create_page( array $payload ): \WP_Error|array {
        $payload = Functions::sanitize_wiki_payload( $payload );
        if ( $payload['title'] === '' ) {
            return new \WP_Error( 'missing_title', __( 'A page title is required.', 'wikipress' ) );
        }
        $id = wp_insert_post( [
            'post_type' => PostType::PAGE,
            'post_title' => $payload['title'],
            'post_content' => $payload['content'],
            'post_excerpt' => $payload['excerpt'],
            'post_status' => $payload['status'],
        ], true );
        if ( is_wp_error( $id ) ) {
            return $id;
        }
        self::save_relationships( (int) $id, $payload );
        return self::get_page( (int) $id );
    }

    public static function update_page( int $id, array $payload ): \WP_Error|array {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== PostType::PAGE ) {
            return new \WP_Error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        $payload = Functions::sanitize_wiki_payload( $payload );
        if ( $payload['title'] === '' ) {
            return new \WP_Error( 'missing_title', __( 'A page title is required.', 'wikipress' ) );
        }
        $updated = wp_update_post( [
            'ID' => $id,
            'post_title' => $payload['title'],
            'post_content' => $payload['content'],
            'post_excerpt' => $payload['excerpt'],
            'post_status' => $payload['status'],
        ], true );
        if ( is_wp_error( $updated ) ) {
            return $updated;
        }
        self::save_relationships( $id, $payload );
        return self::get_page( $id );
    }

    public static function delete_page( int $id, bool $force = false ): \WP_Error|bool {
        $post = get_post( $id );
        if ( ! $post || $post->post_type !== PostType::PAGE ) {
            return new \WP_Error( 'not_found', __( 'Wiki page not found.', 'wikipress' ), [ 'status' => 404 ] );
        }
        return (bool) wp_delete_post( $id, $force );
    }

    private static function save_relationships( int $id, array $payload ): void {
        $wiki_id = absint( $payload['wiki_id'] ?? 0 );
        $wiki = $wiki_id > 0 ? get_post( $wiki_id ) : null;
        if ( $wiki && $wiki->post_type === PostType::WIKI ) {
            update_post_meta( $id, '_wikipress_wiki_id', $wiki_id );
        } else {
            delete_post_meta( $id, '_wikipress_wiki_id' );
        }
        wp_set_post_terms( $id, $payload['categories'], Taxonomy::CATEGORY, false );
        wp_set_post_terms( $id, $payload['tags'], Taxonomy::TAG, false );
    }

    public static function format_post( \WP_Post $post ): array {
        return [
            'id' => $post->ID,
            'title' => get_the_title( $post ),
            'content' => $post->post_content,
            'excerpt' => $post->post_excerpt,
            'status' => $post->post_status,
            'author' => (int) $post->post_author,
            'wiki_id' => (int) get_post_meta( $post->ID, '_wikipress_wiki_id', true ),
            'categories' => wp_get_post_terms( $post->ID, Taxonomy::CATEGORY, [ 'fields' => 'ids' ] ),
            'tags' => wp_get_post_terms( $post->ID, Taxonomy::TAG, [ 'fields' => 'ids' ] ),
            'date' => $post->post_date_gmt,
            'link' => get_permalink( $post ),
        ];
    }

    public static function format_wiki( \WP_Post $post ): array {
        $pages = new \WP_Query( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => '_wikipress_wiki_id', 'meta_value' => $post->ID ] );
        return [
            'id' => $post->ID,
            'name' => get_the_title( $post ),
            'description' => $post->post_content,
            'status' => $post->post_status,
            'author' => (int) $post->post_author,
            'page_count' => (int) $pages->found_posts,
            'date' => $post->post_date_gmt,
        ];
    }

    private static function sanitize_status( string $status ): string {
        return in_array( $status, [ 'publish', 'draft', 'private' ], true ) ? $status : 'draft';
    }
}
