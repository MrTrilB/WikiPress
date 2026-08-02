<?php

namespace TrilBDev\WikiPress\Includes\Tools;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class DataTransfer {
    public static function export(): array {
        $data = [ 'version' => 1, 'wikis' => [], 'pages' => [], 'categories' => [], 'tags' => [] ];
        foreach ( get_posts( [ 'post_type' => [ PostType::WIKI, PostType::PAGE ], 'post_status' => 'any', 'posts_per_page' => -1 ] ) as $post ) {
            $item = [ 'id' => $post->ID, 'title' => $post->post_title, 'content' => $post->post_content, 'excerpt' => $post->post_excerpt, 'status' => $post->post_status, 'wiki_id' => absint( get_post_meta( $post->ID, '_wikipress_wiki_id', true ) ) ];
            $data[ $post->post_type === PostType::WIKI ? 'wikis' : 'pages' ][] = $item;
        }
        foreach ( [ 'categories' => Taxonomy::CATEGORY, 'tags' => Taxonomy::TAG ] as $key => $taxonomy ) {
            $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
            if ( is_wp_error( $terms ) ) {
                continue;
            }
            foreach ( $terms as $term ) {
                $data[ $key ][] = [ 'name' => $term->name, 'slug' => $term->slug, 'description' => $term->description ];
            }
        }
        return $data;
    }

    public static function import( array $data ): array|\WP_Error {
        if ( absint( $data['version'] ?? 0 ) !== 1 ) {
            return new \WP_Error( 'unsupported_version', __( 'This WikiPress export version is not supported.', 'wikipress' ) );
        }
        $wiki_map = [];
        foreach ( (array) ( $data['wikis'] ?? [] ) as $wiki ) {
            $id = wp_insert_post( [ 'post_type' => PostType::WIKI, 'post_title' => sanitize_text_field( $wiki['title'] ?? '' ), 'post_content' => wp_kses_post( $wiki['content'] ?? '' ), 'post_status' => self::status( $wiki['status'] ?? 'draft' ) ], true );
            if ( ! is_wp_error( $id ) ) {
                $wiki_map[ absint( $wiki['id'] ?? 0 ) ] = (int) $id;
            }
        }
        foreach ( (array) ( $data['pages'] ?? [] ) as $page ) {
            $id = wp_insert_post( [ 'post_type' => PostType::PAGE, 'post_title' => sanitize_text_field( $page['title'] ?? '' ), 'post_content' => wp_kses_post( $page['content'] ?? '' ), 'post_excerpt' => sanitize_text_field( $page['excerpt'] ?? '' ), 'post_status' => self::status( $page['status'] ?? 'draft' ) ], true );
            if ( ! is_wp_error( $id ) && ! empty( $wiki_map[ absint( $page['wiki_id'] ?? 0 ) ] ) ) {
                update_post_meta( (int) $id, '_wikipress_wiki_id', $wiki_map[ absint( $page['wiki_id'] ) ] );
            }
        }
        self::import_terms( (array) ( $data['categories'] ?? [] ), Taxonomy::CATEGORY );
        self::import_terms( (array) ( $data['tags'] ?? [] ), Taxonomy::TAG );
        return [ 'wikis' => count( (array) ( $data['wikis'] ?? [] ) ), 'pages' => count( (array) ( $data['pages'] ?? [] ) ) ];
    }

    private static function import_terms( array $terms, string $taxonomy ): void {
        foreach ( $terms as $term ) {
            if ( ! empty( $term['name'] ) ) {
                wp_insert_term( sanitize_text_field( $term['name'] ), $taxonomy, [ 'slug' => sanitize_title( $term['slug'] ?? '' ), 'description' => sanitize_textarea_field( $term['description'] ?? '' ) ] );
            }
        }
    }

    private static function status( string $status ): string {
        return in_array( $status, [ 'publish', 'draft', 'private' ], true ) ? $status : 'draft';
    }
}
