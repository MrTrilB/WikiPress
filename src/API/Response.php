<?php

namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Response {
    public static function success( $data, int $status = 200 ): \WP_REST_Response {
        return new \WP_REST_Response( [ 'success' => true, 'data' => $data ], $status );
    }

    public static function error( string $code, string $message, int $status = 400 ): \WP_Error {
        return new \WP_Error( $code, $message, [ 'status' => $status ] );
    }
}
