<?php

namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Schema {
    public static function page(): array {
        return [
            'type' => 'object',
            'required' => [ 'title' ],
            'properties' => [
                'title' => [ 'type' => 'string', 'required' => true ],
                'content' => [ 'type' => 'string' ],
                'status' => [ 'type' => 'string', 'enum' => [ 'draft', 'publish', 'private' ] ],
            ],
        ];
    }
}
