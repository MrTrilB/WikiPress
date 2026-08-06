<?php
/**
 * REST API schema definitions for WikiPress.
 *
 * @package TrilBDev\WikiPress
 * @subpackage API
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Schema {
    /**
     * Returns the schema for a wiki object.
     *
     * @return array The schema definition for a wiki object.
     */
    public static function wiki(): array {
        return [
            'type' => 'object',
            'required' => [ 'title' ],
            'properties' => [
                'title' => [ 'type' => 'string', 'required' => true ],
                'description' => [ 'type' => 'string' ],
                'status' => [ 'type' => 'string', 'enum' => [ 'draft', 'publish', 'private' ] ],
				'permalink' => [ 'type' => 'string', 'description' => 'Optional tokenized permalink override for this Wiki.' ],
            ],
        ];
    }
    /**
     * Returns the schema for a page object.
     *
     * @return array The schema definition for a page object.
     */
    public static function page(): array {
        return [
            'type' => 'object',
            'required' => [ 'title' ],
            'properties' => [
                'title' => [ 'type' => 'string', 'required' => true ],
                'content' => [ 'type' => 'string' ],
                'excerpt' => [ 'type' => 'string' ],
                'status' => [ 'type' => 'string', 'enum' => [ 'draft', 'publish', 'private' ] ],
                'wiki_id' => [ 'type' => 'integer', 'minimum' => 0 ],
                'categories' => [ 'type' => 'array', 'items' => [ 'type' => 'string' ] ],
                'tags' => [ 'type' => 'array', 'items' => [ 'type' => 'string' ] ],
            ],
        ];
    }
    /**
     * Returns the parameters for collection endpoints (list of wikis or pages).
     *
     * @return array The parameters for collection endpoints.
     */
    public static function collection_parameters(): array {
        return [
            'per_page' => [ 'type' => 'integer', 'minimum' => 1, 'maximum' => 100, 'default' => 20 ],
            'page' => [ 'type' => 'integer', 'minimum' => 1, 'default' => 1 ],
            'search' => [ 'type' => 'string', 'default' => '' ],
        ];
    }
}
