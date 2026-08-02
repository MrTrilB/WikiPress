<?php

namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Validators {
    public static function page_payload( array $payload ): array {
        $errors = [];
        $title = sanitize_text_field( $payload['title'] ?? '' );
        if ( $title === '' ) {
            $errors[] = __( 'A page title is required.', 'wikipress' );
        }
        $status = sanitize_key( $payload['status'] ?? 'draft' );
        if ( ! in_array( $status, [ 'draft', 'publish', 'private' ], true ) ) {
            $errors[] = __( 'The page status is invalid.', 'wikipress' );
        }
        return [ 'valid' => empty( $errors ), 'errors' => $errors, 'payload' => [ 'title' => $title, 'content' => wp_kses_post( $payload['content'] ?? '' ), 'status' => $status ] ];
    }
}
