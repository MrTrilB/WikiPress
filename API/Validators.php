<?php
/**
 * TrilB.Dev Plugin - Wiki API Validators
 *
 * Validates request payloads for the Wiki REST API.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\API
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\API;

use MrTrilB\TrilBDevPlugin\Includes\Wiki\Functions\Functions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Validators {
    public static function validate_page_payload( array $payload ): array {
        $errors = [];

        if ( empty( trim( $payload['title'] ?? '' ) ) ) {
            $errors[] = 'Title is required.';
        }

        if ( isset( $payload['status'] ) && ! in_array( $payload['status'], [ 'publish', 'draft', 'private' ], true ) ) {
            $errors[] = 'Invalid status value.';
        }

        if ( isset( $payload['categories'] ) && ! is_string( $payload['categories'] ) && ! is_array( $payload['categories'] ) ) {
            $errors[] = 'Categories must be a string or an array.';
        }

        if ( isset( $payload['tags'] ) && ! is_string( $payload['tags'] ) && ! is_array( $payload['tags'] ) ) {
            $errors[] = 'Tags must be a string or an array.';
        }

        $payload['categories'] = Functions::normalize_terms( $payload['categories'] ?? [] );
        $payload['tags'] = Functions::normalize_terms( $payload['tags'] ?? [] );

        return [
            'valid' => empty( $errors ),
            'errors' => $errors,
            'payload' => $payload,
        ];
    }
}
