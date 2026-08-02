<?php

namespace TrilBDev\WikiPress\Includes\Pages;

use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Taxonomies {
    public static function get_terms( string $taxonomy, int $limit = 50, string $search = '' ): array {
        $terms = get_terms( [ 'taxonomy' => sanitize_key( $taxonomy ), 'hide_empty' => false, 'number' => max( 1, $limit ), 'search' => sanitize_text_field( $search ) ] );
        if ( is_wp_error( $terms ) ) {
            return [];
        }
        return array_map( static fn( $term ) => [ 'id' => $term->term_id, 'name' => $term->name, 'slug' => $term->slug, 'count' => $term->count, 'description' => $term->description ], $terms );
    }

    public static function get_categories( int $limit = 50, string $search = '' ): array { return self::get_terms( Taxonomy::CATEGORY, $limit, $search ); }
    public static function get_tags( int $limit = 50, string $search = '' ): array { return self::get_terms( Taxonomy::TAG, $limit, $search ); }
}
