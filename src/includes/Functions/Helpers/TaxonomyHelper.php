<?php
/**
 * Taxonomy query and term normalization helpers for WikiPress.
 *
 * @package TrilBDev\WikiPress
 * @subpackage Includes\Functions\Helpers
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Centralize safe taxonomy lookups used by admin and frontend code.
 */
final class TaxonomyHelper {
    public static function terms( string $taxonomy, int $post_id = 0, int $limit = 0, string $search = '' ): array {
        $args = [
            'taxonomy' => SanitizationHelper::key( $taxonomy ),
            'hide_empty' => false,
        ];

        if ( $post_id > 0 ) {
            $args['object_ids'] = [ $post_id ];
        }
        if ( $limit > 0 ) {
            $args['number'] = $limit;
        }
        if ( '' !== $search ) {
            $args['search'] = SanitizationHelper::text( $search );
        }

        $terms = get_terms( $args );
        return is_wp_error( $terms ) || ! is_array( $terms ) ? [] : $terms;
    }

    public static function ids( $terms ): array {
        if ( ! is_array( $terms ) ) {
            $terms = SanitizationHelper::terms( $terms );
        }

        $ids = [];
        foreach ( $terms as $term ) {
            $id = is_object( $term ) && isset( $term->term_id ) ? $term->term_id : $term;
            if ( is_numeric( $id ) && absint( $id ) > 0 ) {
                $ids[] = absint( $id );
            }
        }

        return array_values( array_unique( $ids ) );
    }

    public static function names( $terms ): array {
        if ( ! is_array( $terms ) ) {
            $terms = SanitizationHelper::terms( $terms );
        }

        $names = [];
        foreach ( $terms as $term ) {
            $name = is_object( $term ) && isset( $term->name ) ? $term->name : $term;
            $name = SanitizationHelper::text( $name );
            if ( '' !== $name ) {
                $names[] = $name;
            }
        }

        return array_values( array_unique( $names ) );
    }
}
