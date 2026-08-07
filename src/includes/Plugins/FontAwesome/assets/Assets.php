<?php

namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Assets;

use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\Settings\Settings;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    public function register(): void {
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'admin_enqueue_scripts', 'callback' => 'enqueue_icon_picker' ],
        ] )->run();
    }

    public function enqueue_icon_picker(): void {
        if ( ! $this->should_enqueue_icon_picker() ) {
            return;
        }

        $this->enqueue_fontawesome();

        wp_enqueue_style(
            'wikipress-fontawesome-icon-picker',
            WIKIPRESS_URL . 'src/includes/Plugins/FontAwesome/Assets/dist/css/icon-picker.css',
            [],
            WIKIPRESS_VERSION
        );
        wp_enqueue_script(
            'wikipress-fontawesome-icon-picker',
            WIKIPRESS_URL . 'src/includes/Plugins/FontAwesome/Assets/dist/js/icon-picker.js',
            [ 'jquery' ],
            WIKIPRESS_VERSION,
            true
        );

        wp_localize_script( 'wikipress-fontawesome-icon-picker', 'wikipress_fa_picker', [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wikipress_fontawesome_picker' ),
            'strings' => [
                'search_placeholder' => __( 'Search icons...', 'wikipress-fontawesome' ),
                'no_icons_found' => __( 'No icons found', 'wikipress-fontawesome' ),
                'loading' => __( 'Loading...', 'wikipress-fontawesome' ),
                'select_icon' => __( 'Select Icon', 'wikipress-fontawesome' ),
                'close' => __( 'Close', 'wikipress-fontawesome' ),
            ],
        ] );
    }

    /**
     * Enqueue Font Awesome in the document context used by WikiPress.
     *
     * @return void
     */
    private function enqueue_fontawesome(): void {
        if ( 'kit' === Settings::source() && '' !== Settings::kit_id() ) {
            wp_enqueue_script(
                'wikipress-fontawesome',
                'https://kit.fontawesome.com/' . rawurlencode( Settings::kit_id() ) . '.js',
                [],
                null,
                false
            );
            return;
        }

        wp_enqueue_style(
            'wikipress-fontawesome',
            'https://use.fontawesome.com/releases/v' . rawurlencode( Settings::version() ) . '/css/all.css',
            [],
            Settings::version()
        );
    }

    private function should_enqueue_icon_picker(): bool {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return false;
        }

        return strpos( $screen->id, 'wikipress' ) !== false
            || in_array( $screen->id, [ 'post', 'page', 'custom_css', 'customize' ], true );
    }
}