<?php
/**
 * TrilB.Dev Plugin - Wiki API Schema definitions
 *
 * Provides REST API schema and request argument definitions for Wiki endpoints.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Schema {
    public static function get_page_create_args(): array {
        return [
            'title' => [
                'required' => true,
                'type' => 'string',
                'description' => 'The title of the wiki page.',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'content' => [
                'required' => false,
                'type' => 'string',
                'description' => 'The body content of the wiki page.',
                'sanitize_callback' => 'wp_kses_post',
            ],
            'excerpt' => [
                'required' => false,
                'type' => 'string',
                'description' => 'The excerpt for the wiki page.',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'status' => [
                'required' => false,
                'type' => 'string',
                'description' => 'Publication status for the wiki page.',
                'validate_callback' => [ self::class, 'validate_status' ],
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'categories' => [
                'required' => false,
                'type' => 'array',
                'description' => 'List of category slugs to assign.',
                'items' => [ 'type' => 'string' ],
            ],
            'tags' => [
                'required' => false,
                'type' => 'array',
                'description' => 'List of tag slugs to assign.',
                'items' => [ 'type' => 'string' ],
            ],
        ];
    }

    public static function get_page_update_args(): array {
        return [
            'title' => [
                'required' => false,
                'type' => 'string',
                'description' => 'The title of the wiki page.',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'content' => [
                'required' => false,
                'type' => 'string',
                'description' => 'The body content of the wiki page.',
                'sanitize_callback' => 'wp_kses_post',
            ],
            'excerpt' => [
                'required' => false,
                'type' => 'string',
                'description' => 'The excerpt for the wiki page.',
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'status' => [
                'required' => false,
                'type' => 'string',
                'description' => 'Publication status for the wiki page.',
                'validate_callback' => [ self::class, 'validate_status' ],
                'sanitize_callback' => 'sanitize_text_field',
            ],
            'categories' => [
                'required' => false,
                'type' => 'array',
                'description' => 'List of category slugs to assign.',
                'items' => [ 'type' => 'string' ],
            ],
            'tags' => [
                'required' => false,
                'type' => 'array',
                'description' => 'List of tag slugs to assign.',
                'items' => [ 'type' => 'string' ],
            ],
        ];
    }

    public static function get_page_schema(): array {
        return [
            '$schema' => 'http://json-schema.org/draft-04/schema#',
            'title' => 'wiki_page',
            'type' => 'object',
            'required' => [ 'id', 'title' ],
            'properties' => [
                'id' => [ 'type' => 'integer', 'context' => [ 'view' ] ],
                'title' => [ 'type' => 'string', 'context' => [ 'view' ] ],
                'content' => [ 'type' => 'string', 'context' => [ 'view' ] ],
                'excerpt' => [ 'type' => 'string', 'context' => [ 'view' ] ],
                'status' => [ 'type' => 'string', 'enum' => [ 'publish', 'draft', 'private' ], 'context' => [ 'view' ] ],
                'author' => [ 'type' => 'string', 'context' => [ 'view' ] ],
                'permalink' => [ 'type' => 'string', 'format' => 'uri', 'context' => [ 'view' ] ],
                'categories' => [ 'type' => 'array', 'items' => [ 'type' => 'string' ], 'context' => [ 'view' ] ],
                'tags' => [ 'type' => 'array', 'items' => [ 'type' => 'string' ], 'context' => [ 'view' ] ],
                'created_at' => [ 'type' => 'string', 'format' => 'date-time', 'context' => [ 'view' ] ],
                'updated_at' => [ 'type' => 'string', 'format' => 'date-time', 'context' => [ 'view' ] ],
            ],
        ];
    }

    public static function get_list_args(): array {
        return [
            'per_page' => [
                'required' => false,
                'type' => 'integer',
                'default' => 50,
                'sanitize_callback' => 'absint',
                'description' => 'Number of wiki pages to show per page.',
            ],
            'page' => [
                'required' => false,
                'type' => 'integer',
                'default' => 1,
                'sanitize_callback' => 'absint',
                'description' => 'Page of results to return.',
            ],
            'search' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'description' => 'Search term to filter wiki pages.',
            ],
            'category' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'description' => 'Category slug filter.',
            ],
            'tags' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'description' => 'Comma-separated tag slugs filter.',
            ],
        ];
    }

    public static function get_taxonomy_args(): array {
        return [
            'search' => [
                'required' => false,
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'description' => 'Search string for taxonomy terms.',
            ],
            'limit' => [
                'required' => false,
                'type' => 'integer',
                'default' => 50,
                'sanitize_callback' => 'absint',
                'description' => 'Maximum number of terms to return.',
            ],
        ];
    }

    public static function validate_status( $value ): bool {
        return in_array( $value, [ 'publish', 'draft', 'private' ], true );
    }
}
