<?php

namespace TrilBDev\WikiPress\Includes\Core;

use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Taxonomy {
    public const CATEGORY = 'wikipress_category';
    public const TAG = 'wikipress_tag';

    public function register(): void {
        register_taxonomy( self::CATEGORY, [ PostType::PAGE ], self::category_args() );
        register_taxonomy( self::TAG, [ PostType::PAGE ], self::tag_args() );
    }

    /**
     * Build the hierarchical Wiki category taxonomy definition.
     *
     * @return array<string, mixed> Registration arguments.
     */
    public static function category_args(): array {
        return apply_filters( 'wikipress_category_taxonomy_args', [
            'labels' => [ 'name' => __( 'Wiki Categories', 'wikipress' ), 'singular_name' => __( 'Wiki Category', 'wikipress' ) ],
            'hierarchical' => true,
            'public' => true,
            'show_ui' => false,
            'show_in_rest' => true,
            'rewrite' => [ 'slug' => self::setting_slug( 'category_slug', 'wiki-category' ) ],
        ], self::CATEGORY );
    }

    /**
     * Build the non-hierarchical Wiki tag taxonomy definition.
     *
     * @return array<string, mixed> Registration arguments.
     */
    public static function tag_args(): array {
        return apply_filters( 'wikipress_tag_taxonomy_args', [
            'labels' => [ 'name' => __( 'Wiki Tags', 'wikipress' ), 'singular_name' => __( 'Wiki Tag', 'wikipress' ) ],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => false,
            'show_in_rest' => true,
            'rewrite' => [ 'slug' => self::setting_slug( 'tag_slug', 'wiki-tag' ) ],
        ], self::TAG );
    }

    public static function get_taxonomy_names(): array {
        return [ self::CATEGORY, self::TAG ];
    }

    private static function setting_slug( string $key, string $fallback ): string {
        $value = sanitize_title( (string) Settings::get( $key, $fallback ) );
        return $value !== '' ? $value : $fallback;
    }
}
