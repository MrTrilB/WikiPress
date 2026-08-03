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

        $page = sanitize_key( $_GET['page'] ?? 'wikipress' );
        $bundle = match ( $page ) {
            'wikipress-wikis', 'wikipress-pages', 'wikipress-categories', 'wikipress-tags' => 'content',
            'wikipress-add-new', 'wikipress-edit' => 'page',
            'wikipress-settings' => 'settings',
            'wikipress-analytics' => 'analytics',
            default => 'dashboard',
        };

        wp_enqueue_style( 'wikipress-admin', WIKIPRESS_URL . 'src/Assets/css/admin.css', [], WIKIPRESS_VERSION );
        wp_enqueue_style( 'wikipress-bootstrap', WIKIPRESS_URL . 'src/Assets/dist/css/bootstrap.css', [], '5.3.8' );
        wp_enqueue_style( 'wikipress-admin-' . $bundle, WIKIPRESS_URL . 'src/Assets/dist/css/admin.' . $bundle . '.css', [], WIKIPRESS_VERSION );
        wp_enqueue_script( 'wikipress-bootstrap', WIKIPRESS_URL . 'src/Assets/dist/js/bootstrap.js', [], '5.3.8', true );
        wp_enqueue_script( 'wikipress-admin-' . $bundle, WIKIPRESS_URL . 'src/Assets/dist/js/admin.' . $bundle . '.js', [ 'wikipress-bootstrap' ], WIKIPRESS_VERSION, true );
    }
}
