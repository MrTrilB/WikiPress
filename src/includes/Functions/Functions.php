<?php
/**
 * TrilB.Dev Plugin - Wiki functions helper
 *
 * Shared utilities used by Wiki services, pages, and REST routes.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Functions
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Functions;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Functions {
    public static function sanitize_wiki_payload( array $payload ): array {
        $sanitized = [];

        $sanitized['title'] = sanitize_text_field( $payload['title'] ?? '' );
        $sanitized['content'] = wp_kses_post( $payload['content'] ?? '' );
        $sanitized['excerpt'] = sanitize_text_field( $payload['excerpt'] ?? '' );
        $sanitized['status'] = in_array( $payload['status'] ?? 'publish', [ 'publish', 'draft', 'private' ], true ) ? $payload['status'] : 'publish';
        $sanitized['wiki_id'] = absint( $payload['wiki_id'] ?? 0 );
        $sanitized['categories'] = self::normalize_terms( $payload['categories'] ?? [] );
        $sanitized['tags'] = self::normalize_terms( $payload['tags'] ?? [] );

        return $sanitized;
    }

    public static function normalize_terms( $terms ): array {
        if ( is_string( $terms ) ) {
            $terms = explode( ',', $terms );
        }

        if ( ! is_array( $terms ) ) {
            return [];
        }

        return array_values( array_filter( array_map( 'sanitize_text_field', $terms ), 'strlen' ) );
    }

    public static function is_wiki_post( $post ): bool {
        return $post instanceof \WP_Post && $post->post_type === \TrilBDev\WikiPress\Includes\Core\PostType::PAGE;
    }

    public static function rest_response( bool $success, string $message = '', array $data = [] ): array {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }
}
