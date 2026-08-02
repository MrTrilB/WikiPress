<?php

namespace TrilBDev\WikiPress\Includes\Pages;

use TrilBDev\WikiPress\API\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Pages {
    public static function list_pages( array $query_args = [] ): array {
        return API::list_pages( $query_args );
    }

    public static function get_by_id( int $page_id ): array {
        $page = API::get_page( $page_id );
        return $page ? [ 'success' => true, 'data' => $page ] : [ 'success' => false, 'message' => __( 'Wiki page not found.', 'wikipress' ), 'data' => [] ];
    }

    public static function create_from_payload( array $payload ): array {
        $page = API::create_page( $payload );
        return is_wp_error( $page ) ? [ 'success' => false, 'message' => $page->get_error_message(), 'data' => [] ] : [ 'success' => true, 'data' => $page ];
    }
}
