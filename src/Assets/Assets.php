<?php

namespace TrilBDev\WikiPress\Assets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Assets {
    public function enqueue_frontend(): void {
        if ( ! is_singular( 'wikipress_page' ) ) {
            return;
        }

        wp_enqueue_style( 'wikipress-public', WIKIPRESS_URL . 'src/Assets/css/public.css', [], WIKIPRESS_VERSION );
        wp_enqueue_script( 'wikipress-public', WIKIPRESS_URL . 'src/Assets/js/public.js', [], WIKIPRESS_VERSION, true );
    }

    public function enqueue_admin( string $hook_suffix ): void {
        if ( false === strpos( $hook_suffix, 'wikipress' ) ) {
            return;
        }

        wp_enqueue_style( 'wikipress-admin', WIKIPRESS_URL . 'src/Assets/css/admin.css', [], WIKIPRESS_VERSION );
        wp_enqueue_script( 'wikipress-admin', WIKIPRESS_URL . 'src/Assets/js/admin.js', [ 'jquery' ], WIKIPRESS_VERSION, true );
    }
}
