<?php
/**
 * TrilB.Dev Plugin - Wiki Assets
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Assets
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Assets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Assets {
    public function __construct() {
        // Initialization code for the wiki assets
    }

    public function enqueue_frontend(): void {
        if ( ! function_exists( 'wp_enqueue_script' ) ) {
            return;
        }

        wp_enqueue_style( 'trilbdev-wiki-frontend', TRILBDEV_INCLUDES_URL . '/Wiki/Assets/css/wiki-frontend.css', [], '1.0.0' );
        wp_enqueue_script( 'trilbdev-wiki-frontend', TRILBDEV_INCLUDES_URL . '/Wiki/Assets/js/wiki-frontend.js', [ 'jquery' ], '1.0.0', true );
    }

    public function enqueue_admin(): void {
        if ( ! function_exists( 'wp_enqueue_script' ) ) {
            return;
        }

        wp_enqueue_style( 'trilbdev-wiki-admin', TRILBDEV_INCLUDES_URL . '/Wiki/Assets/css/wiki-admin.css', [], '1.0.0' );
        wp_enqueue_script( 'trilbdev-wiki-admin', TRILBDEV_INCLUDES_URL . '/Wiki/Assets/js/wiki-admin.js', [ 'jquery' ], '1.0.0', true );
    }
}