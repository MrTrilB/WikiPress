<?php
/**
 * Tokenized WikiPress permalink support.
 *
 * @package WikiPress
 */

namespace TrilBDev\WikiPress\Includes\Functions\Helpers;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class PermalinkHelper {
    public const OVERRIDE_META = '_wikipress_permalink';

    public static function token_definitions(): array {
        return [
            '%root%' => __( 'The root WikiPress slug.', 'wikipress' ),
            '%root_category%' => __( 'The Wiki categories, from parent to child.', 'wikipress' ),
            '%root_tags%' => __( 'The tags assigned to the Wiki container.', 'wikipress' ),
            '%wiki%' => __( 'The Wiki slug.', 'wikipress' ),
            '%category%' => __( 'The page categories, from parent to child.', 'wikipress' ),
            '%tags%' => __( 'The tags assigned to the Wiki page.', 'wikipress' ),
        ];
    }

    public static function default_pattern(): string {
        return '%root%/%root_category%/%wiki%/%category%/%tags%';
    }

    public static function sanitize_pattern( $pattern ): string {
        $pattern = trim( (string) $pattern );
        $pattern = str_replace( [ '%wiki_root%', '%postname%' ], [ '%root%', '%page%' ], $pattern );
        $allowed = array_keys( self::token_definitions() );
        $segments = [];

        foreach ( preg_split( '#/+#', trim( $pattern, '/' ) ) ?: [] as $segment ) {
            $segment = trim( $segment );
            if ( '' === $segment ) {
                continue;
            }
            if ( in_array( $segment, $allowed, true ) ) {
                $segments[] = $segment;
                continue;
            }
            $slug = sanitize_title( $segment );
            if ( '' !== $slug ) {
                $segments[] = $slug;
            }
        }

        return implode( '/', $segments );
    }

    public static function pattern_for_wiki( int $wiki_id = 0 ): string {
        $pattern = $wiki_id > 0 ? get_post_meta( $wiki_id, self::OVERRIDE_META, true ) : '';
        return self::sanitize_pattern( $pattern ?: Settings::get( 'permalink', self::default_pattern() ) ) ?: self::default_pattern();
    }

    public static function page_url( \WP_Post $page ): string {
        $wiki_id = absint( get_post_meta( $page->ID, '_wikipress_wiki_id', true ) );
        $wiki = $wiki_id > 0 ? get_post( $wiki_id ) : null;
        $pattern = self::pattern_for_wiki( $wiki_id );
        $path = self::expand( $pattern, $page, $wiki instanceof \WP_Post ? $wiki : null );
        return home_url( user_trailingslashit( trim( $path, '/' ) ) );
    }

    public static function expand( string $pattern, \WP_Post $page, ?\WP_Post $wiki = null ): string {
        $values = [
            '%root%' => sanitize_title( (string) Settings::get( 'root_slug', 'wiki' ) ),
            '%root_category%' => $wiki ? self::term_path( Taxonomy::CATEGORY, $wiki->ID ) : '',
            '%root_tags%' => $wiki ? self::term_path( Taxonomy::TAG, $wiki->ID ) : '',
            '%wiki%' => $wiki ? sanitize_title( $wiki->post_name ?: $wiki->post_title ) : '',
            '%category%' => self::term_path( Taxonomy::CATEGORY, $page->ID ),
            '%tags%' => self::term_path( Taxonomy::TAG, $page->ID ),
            '%page%' => sanitize_title( $page->post_name ?: $page->post_title ),
        ];

        $normalized = self::sanitize_pattern( $pattern );
        $path = strtr( $normalized, $values );
        if ( ! str_contains( $normalized, '%page%' ) ) {
            $path .= '/' . $values['%page%'];
        }
        return trim( preg_replace( '#/+#', '/', trim( $path, '/' ) ), '/' );
    }

    public static function rewrite_rule(): void {
        add_rewrite_rule( '^(.+?)/?$', 'index.php?wikipress_path=$matches[1]', 'top' );
        add_filter( 'query_vars', static function ( array $vars ): array {
            $vars[] = 'wikipress_path';
            return $vars;
        } );
        add_filter( 'request', [ self::class, 'resolve_request' ] );
    }

    public static function resolve_request( array $vars ): array {
        $requested_path = isset( $vars['wikipress_path'] ) ? trim( urldecode( (string) $vars['wikipress_path'] ), '/' ) : '';
        if ( '' === $requested_path ) {
            return $vars;
        }

        $pages = get_posts( [
            'post_type' => PostType::PAGE,
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'suppress_filters' => false,
        ] );
        foreach ( $pages as $page ) {
            if ( self::page_url_path( $page ) === $requested_path ) {
                return [ 'p' => $page->ID ];
            }
        }

        return $vars;
    }

    public static function filter_page_permalink( string $link, \WP_Post $post ): string {
        return $post->post_type === PostType::PAGE ? self::page_url( $post ) : $link;
    }

    private static function page_url_path( \WP_Post $page ): string {
        $wiki_id = absint( get_post_meta( $page->ID, '_wikipress_wiki_id', true ) );
        $wiki = $wiki_id > 0 ? get_post( $wiki_id ) : null;
        return self::expand( self::pattern_for_wiki( $wiki_id ), $page, $wiki instanceof \WP_Post ? $wiki : null );
    }

    private static function term_path( string $taxonomy, int $post_id ): string {
        $terms = get_the_terms( $post_id, $taxonomy );
        if ( ! is_array( $terms ) || empty( $terms ) ) {
            return '';
        }

        $ordered = [];
        foreach ( $terms as $term ) {
            $ancestors = is_taxonomy_hierarchical( $taxonomy ) ? array_reverse( get_ancestors( $term->term_id, $taxonomy, 'taxonomy' ) ) : [];
            foreach ( array_merge( $ancestors, [ $term->term_id ] ) as $term_id ) {
                $ancestor = get_term( $term_id, $taxonomy );
                if ( $ancestor && ! is_wp_error( $ancestor ) ) {
                    $ordered[ $ancestor->term_id ] = sanitize_title( $ancestor->slug ?: $ancestor->name );
                }
            }
        }

        return implode( '/', array_filter( $ordered ) );
    }
}