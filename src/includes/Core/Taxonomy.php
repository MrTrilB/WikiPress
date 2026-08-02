<?php

namespace TrilBDev\WikiPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Taxonomy {
    public const CATEGORY = 'wikipress_category';
    public const TAG = 'wikipress_tag';

    public function register(): void {
        register_taxonomy( self::CATEGORY, [ PostType::PAGE ], [
            'labels' => [ 'name' => __( 'Wiki Categories', 'wikipress' ), 'singular_name' => __( 'Wiki Category', 'wikipress' ) ],
            'hierarchical' => true,
            'public' => true,
            'show_ui' => false,
            'show_in_rest' => true,
            'rewrite' => [ 'slug' => 'wiki-category' ],
        ] );

        register_taxonomy( self::TAG, [ PostType::PAGE ], [
            'labels' => [ 'name' => __( 'Wiki Tags', 'wikipress' ), 'singular_name' => __( 'Wiki Tag', 'wikipress' ) ],
            'hierarchical' => false,
            'public' => true,
            'show_ui' => false,
            'show_in_rest' => true,
            'rewrite' => [ 'slug' => 'wiki-tag' ],
        ] );
    }

    public static function get_taxonomy_names(): array {
        return [ self::CATEGORY, self::TAG ];
    }
}
