<?php
/**
 * TrilB.Dev Plugin - Wiki API Response helper
 *
 * Provides a centralized response format for Wiki REST endpoints.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Response {
    public static function success( $data = [], string $message = '' ): array {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    public static function error( string $message, array $data = [] ): array {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
