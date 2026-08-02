<?php

namespace TrilBDev\WikiPress\Includes\Core;

use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class PostType {
    public const WIKI = 'wikipress_wiki';
    public const PAGE = 'wikipress_page';

    public function register(): void {
        $this->register_wiki();
        $this->register_page();
    }

    public static function get_post_type_name(): string {
        return self::PAGE;
    }

    public static function page_rewrite_slug(): string {
        return self::setting_slug( 'root_slug', 'wiki' );
    }

    private function register_wiki(): void {
        register_post_type( self::WIKI, [
            'labels' => [
                'name' => __( 'Wikis', 'wikipress' ),
                'singular_name' => __( 'Wiki', 'wikipress' ),
                'add_new_item' => __( 'Add New Wiki', 'wikipress' ),
                'edit_item' => __( 'Edit Wiki', 'wikipress' ),
            ],
            'public' => false,
            'show_ui' => false,
            'show_in_rest' => true,
            'supports' => [ 'title', 'editor', 'author', 'thumbnail', 'revisions' ],
            'capability_type' => [ 'wikipress_wiki', 'wikipress_wikis' ],
            'map_meta_cap' => true,
        ] );
    }

    private function register_page(): void {
        register_post_type( self::PAGE, [
            'labels' => [
                'name' => __( 'Wiki Pages', 'wikipress' ),
                'singular_name' => __( 'Wiki Page', 'wikipress' ),
                'add_new_item' => __( 'Add New Wiki Page', 'wikipress' ),
                'edit_item' => __( 'Edit Wiki Page', 'wikipress' ),
            ],
            'public' => true,
            'show_ui' => false,
            'show_in_rest' => true,
            'has_archive' => false,
            'rewrite' => [ 'slug' => self::page_rewrite_slug() ],
            'supports' => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ],
            'capability_type' => [ 'wikipress_page', 'wikipress_pages' ],
            'map_meta_cap' => true,
        ] );
    }

    private static function setting_slug( string $key, string $fallback ): string {
        $value = sanitize_title( (string) Settings::get( $key, $fallback ) );
        return $value !== '' ? $value : $fallback;
    }
}
