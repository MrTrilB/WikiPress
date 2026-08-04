<?php
/**
 * REST API response helper for WikiPress.
 *
 * @package TrilBDev\WikiPress
 * @subpackage API
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Response {
    /**
     * Creates a successful REST API response.
     *
     * @param mixed $data The data to include in the response.
     * @param int $status The HTTP status code (default is 200).
     * @return \WP_REST_Response The REST API response object.
     */
    public static function success( $data, int $status = 200 ): \WP_REST_Response {

        return new \WP_REST_Response( [ 'success' => true, 'data' => $data ], $status );

    }
    /**
     * Creates an error REST API response.
     *
     * @param string $code The error code.
     * @param string $message The error message.
     * @param int $status The HTTP status code (default is 400).
     * @return \WP_Error The WP_Error object representing the error.
     */
    public static function error( string $code, string $message, int $status = 400 ): \WP_Error {

        return new \WP_Error( $code, $message, [ 'status' => $status ] );

    }
}
