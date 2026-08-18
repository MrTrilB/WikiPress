<?php
/**
 * REST API schema definitions for WikiPress.
 *
 * @package WikiPress
 * @subpackage API
 * @since 1.0.0
 */
namespace WikiPress\API;

use WikiPress\Includes\Functions\Functions;
use WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Validators {
    /**
     * Validates a wiki payload.
     *
     * @param array $payload The payload to validate.
     * @param string $default_status The default status to use if not provided in the payload.
     * @return array An array containing validation results and sanitized payload.
     */
    public static function wiki_payload( array $payload, string $default_status = 'publish' ): array {
        $title = SanitizationHelper::text( $payload['title'] ?? '' );
        $status = SanitizationHelper::key( $payload['status'] ?? $default_status );
        $errors = [];
        if ( '' === $title ) {
            $errors[] = __( 'A Wiki name is required.', 'wikipress' );
        }
        if ( ! in_array( $status, [ 'draft', 'publish', 'private' ], true ) ) {
            $errors[] = __( 'The Wiki status is invalid.', 'wikipress' );
        }

        return [
            'valid' => empty( $errors ),
            'errors' => $errors,
            'payload' => [
                'title' => $title,
                'description' => is_scalar( $payload['description'] ?? null ) ? wp_kses_post( (string) $payload['description'] ) : '',
                'status' => $status,
            ],
        ];
    }
    /**
     * Validates a page payload.
     *
     * @param array $payload The payload to validate.
     * @return array An array containing validation results and sanitized payload.
     */
    public static function page_payload( array $payload ): array {
        $payload = Functions::sanitize_wiki_payload( $payload );
        $errors = [];
        $title = $payload['title'];
        if ( $title === '' ) {
            $errors[] = __( 'A page title is required.', 'wikipress' );
        }
        $status = $payload['status'];
        if ( ! in_array( $status, [ 'draft', 'publish', 'private' ], true ) ) {
            $errors[] = __( 'The page status is invalid.', 'wikipress' );
        }
        return [ 'valid' => empty( $errors ), 'errors' => $errors, 'payload' => $payload ];
    }
}
