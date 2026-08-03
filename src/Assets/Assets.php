<?php

namespace TrilBDev\WikiPress\Assets;

use BootstrapPHP\Bootstrap;

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

        $jscfg = Bootstrap::config([
            // package: 'css' or 'js'
            'package' => 'js',
            // type: grid | reboot | utilities | bundle | esm | None
            'type' => 'bundle',
            // build: min | None
            'build' => 'min',
            // rtl: true | false (CSS only)
            'rtl' => false
        ]);
        $csscfg = Bootstrap::config([
            // package: 'css' or 'js'
            'package' => 'css',
            // type: grid | reboot | utilities | bundle | esm | None
            'type' => 'none',
            // build: min | None
            'build' => 'min',
            // rtl: true | false (CSS only)
            'rtl' => false
        ]);

        wp_enqueue_style( 'wikipress-bootstrap-css', Bootstrap::assets($csscfg), [], '5.3.8' );
        wp_enqueue_style( 'wikipress-admin', WIKIPRESS_URL . 'src/Assets/css/admin.css', [], WIKIPRESS_VERSION );
        wp_enqueue_script( 'wikipress-bootstrap-js', Bootstrap::assets($jscfg), [], '5.3.8', true );
        wp_enqueue_script( 'wikipress-admin', WIKIPRESS_URL . 'src/Assets/js/admin.js', [ 'wikipress-bootstrap' ], WIKIPRESS_VERSION, true );
    }
}
