<?php
/**
 * TrilB.Dev Plugin - Wiki API schema registration
 *
 * Additional REST schema support for the Wiki API endpoints.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Schemas {
    public static function register_schemas(): void {
        register_rest_field(
            'wiki',
            'wiki_schema',
            [
                'get_callback' => [ self::class, 'schema_callback' ],
                'schema' => [
                    '$schema' => 'http://json-schema.org/draft-04/schema#',
                    'title' => 'wiki_page',
                    'type' => 'object',
                    'properties' => [
                        'id' => [ 'type' => 'integer' ],
                        'title' => [ 'type' => 'string' ],
                        'content' => [ 'type' => 'string' ],
                        'excerpt' => [ 'type' => 'string' ],
                        'status' => [ 'type' => 'string' ],
                    ],
                ],
            ]
        );
    }

    public static function schema_callback( $object ): array {
        return [
            'id' => $object['id'] ?? 0,
            'title' => $object['title'] ?? '',
            'content' => $object['content'] ?? '',
            'excerpt' => $object['excerpt'] ?? '',
            'status' => $object['status'] ?? '',
        ];
    }
}
