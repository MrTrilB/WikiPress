<?php
/**
 * TrilB.Dev Plugin - Wiki Pages
 *
 * Encapsulates Wiki page CRUD and query logic for the Wiki module.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Pages
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Pages;

use MrTrilB\TrilBDevPlugin\Includes\Wiki\Functions\Functions;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\Settings\Settings as WikiSettings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Pages {
    public static function list_pages( array $query_args = [] ): array {
        $defaults = [
            'post_type'      => 'wiki',
            'posts_per_page' => 50,
            'paged'          => 1,
            'post_status'    => [ 'publish', 'draft', 'private' ],
            'orderby'        => 'date',
            'order'          => 'DESC',
        ];

        $args = wp_parse_args( $query_args, $defaults );
        $query = new \WP_Query( $args );

        $pages = [];
        while ( $query->have_posts() ) {
            $query->the_post();
            $pages[] = self::hydrate_post( get_post() );
        }

        wp_reset_postdata();

        return [
            'success' => true,
            'data' => $pages,
            'total' => intval( $query->found_posts ),
            'page' => intval( $args['paged'] ),
        ];
    }

    public static function get_by_id( int $page_id ): array {
        $post = get_post( $page_id );

        if ( ! Functions::is_wiki_post( $post ) ) {
            return [
                'success' => false,
                'message' => 'Wiki page not found.',
                'data' => [],
            ];
        }

        return [ 'success' => true, 'data' => self::hydrate_post( $post ) ];
    }

    public static function get_by_slug( string $slug ): array {
        $args = [
            'post_type' => 'wiki',
            'name'      => $slug,
            'post_status' => [ 'publish', 'draft', 'private' ],
        ];

        $query = new \WP_Query( $args );
        if ( $query->have_posts() ) {
            $query->the_post();
            $post = get_post();
            wp_reset_postdata();

            return [ 'success' => true, 'data' => self::hydrate_post( $post ) ];
        }

        wp_reset_postdata();

        return [ 'success' => false, 'message' => 'Wiki page not found.', 'data' => [] ];
    }

    public static function create_from_payload( array $payload ): array {
        $payload = Functions::sanitize_wiki_payload( $payload );

        $post_id = wp_insert_post( [
            'post_type'    => 'wiki',
            'post_title'   => $payload['title'] ?? '',
            'post_content' => $payload['content'] ?? '',
            'post_excerpt' => $payload['excerpt'] ?? '',
            'post_status'  => $payload['status'] ?? 'publish',
        ], true );

        if ( is_wp_error( $post_id ) ) {
            return [ 'success' => false, 'message' => $post_id->get_error_message(), 'data' => [] ];
        }

        self::assign_taxonomies( $post_id, $payload );

        return [ 'success' => true, 'data' => [ 'id' => $post_id ] ];
    }

    public static function update_from_payload( int $page_id, array $payload ): array {
        $payload = Functions::sanitize_wiki_payload( $payload );
        $post = get_post( $page_id );

        if ( ! Functions::is_wiki_post( $post ) ) {
            return [
                'success' => false,
                'message' => 'Wiki page not found.',
                'data' => [],
            ];
        }

        $update = [ 'ID' => $page_id ];
        if ( isset( $payload['title'] ) ) {
            $update['post_title'] = $payload['title'];
        }
        if ( isset( $payload['content'] ) ) {
            $update['post_content'] = $payload['content'];
        }
        if ( isset( $payload['excerpt'] ) ) {
            $update['post_excerpt'] = $payload['excerpt'];
        }
        if ( isset( $payload['status'] ) ) {
            $update['post_status'] = $payload['status'];
        }

        $updated = wp_update_post( $update, true );
        if ( is_wp_error( $updated ) ) {
            return [ 'success' => false, 'message' => $updated->get_error_message(), 'data' => [] ];
        }

        self::assign_taxonomies( $page_id, $payload );

        return [ 'success' => true, 'data' => [ 'id' => $page_id ] ];
    }

    public static function delete_by_id( int $page_id ): bool {
        if ( ! Functions::is_wiki_post( get_post( $page_id ) ) ) {
            return false;
        }

        return (bool) wp_delete_post( $page_id, true );
    }

    private static function hydrate_post( \WP_Post $post ): array {
        return [
            'id' => $post->ID,
            'title' => get_the_title( $post ),
            'content' => $post->post_content,
            'excerpt' => get_the_excerpt( $post ),
            'status' => $post->post_status,
            'author' => get_the_author_meta( 'display_name', $post->post_author ),
            'permalink' => get_permalink( $post ),
            'categories' => wp_get_post_terms( $post->ID, 'wiki-categories', [ 'fields' => 'slugs' ] ),
            'tags' => wp_get_post_terms( $post->ID, 'wiki-tags', [ 'fields' => 'slugs' ] ),
            'created_at' => get_the_date( 'c', $post ),
            'updated_at' => get_the_modified_date( 'c', $post ),
        ];
    }

    private static function assign_taxonomies( int $post_id, array $payload ): void {
        $categories = Functions::normalize_terms( $payload['categories'] ?? [] );
        $tags = Functions::normalize_terms( $payload['tags'] ?? [] );

        if ( empty( $categories ) ) {
            $default_category = trim( WikiSettings::get( 'wiki_default_category', '' ) );
            if ( $default_category !== '' ) {
                $categories = [ $default_category ];
            }
        }

        if ( ! empty( $categories ) ) {
            wp_set_post_terms( $post_id, $categories, 'wiki-categories' );
        }

        if ( ! empty( $tags ) ) {
            wp_set_post_terms( $post_id, $tags, 'wiki-tags' );
        }
    }
}
