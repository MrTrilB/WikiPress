<?php
/**
 * TrilB.Dev Plugin - Wiki Taxonomies
 *
 * Provides shared logic for listing Wiki taxonomy terms.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Pages
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Pages;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Taxonomies {
    public static function get_terms( string $taxonomy, int $limit = 50, string $search = '' ): array {
        $args = [
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'number'     => $limit,
        ];

        if ( $search !== '' ) {
            $args['search'] = sanitize_text_field( $search );
        }

        $terms = get_terms( $args );
        if ( is_wp_error( $terms ) ) {
            return [];
        }

        return array_map( static function ( $term ) {
            return [
                'id' => $term->term_id,
                'name' => $term->name,
                'slug' => $term->slug,
                'count' => $term->count,
                'description' => $term->description,
            ];
        }, $terms );
    }

    public static function get_categories( int $limit = 50, string $search = '' ): array {
        return self::get_terms( 'wiki-categories', $limit, $search );
    }

    public static function get_tags( int $limit = 50, string $search = '' ): array {
        return self::get_terms( 'wiki-tags', $limit, $search );
    }
}
