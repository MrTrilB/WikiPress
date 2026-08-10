<?php
/**
 * TrilB.Dev Plugin - WikiPress Assets
 *
 * @package TrilBDev
 * @subpackage Assets
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Assets;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Class Assets
 *
 * Manages the registration and enqueueing of assets for the WikiPress plugin.
 */
final class Assets {
    /**
     * Array to hold registered assets for different pages.
     *
     * @var array
     */
    private array $pages = [];
    /**
     * Registers the default assets for the plugin.
     *
     * @return void
     */
    public function register(): void {
        add_filter( 'wikipress_base_assets', [ $this, 'default_assets' ], 10, 2 );
        add_action( 'admin_enqueue_scripts', [ $this, 'dequeue_wp_forms' ], PHP_INT_MAX );
        add_action( 'admin_print_styles', [ $this, 'dequeue_wp_forms' ], 0 );
    }

    /**
     * Remove the WordPress admin forms stylesheet from WikiPress screens.
     *
     * @param string $hook_suffix The current admin page hook suffix.
     * @return void
     */
    public function dequeue_wp_forms( string $hook_suffix = '' ): void {
        $page = sanitize_key( $_GET['page'] ?? '' );

        if ( false === strpos( $hook_suffix, 'wikipress' ) && 0 !== strpos( $page, 'wikipress' ) ) {
            return;
        }

        wp_dequeue_style( 'forms' );
    }
    /**
     * Registers assets for a specific page.
     *
     * @param string $page The page identifier.
     * @param array  $assets The assets to register for the page.
     * @return void
     */
    public function register_page( string $page, array $assets ): void {
        $page = sanitize_key( $page );
        $this->pages[ $page ] = [
            'styles' => array_merge( $this->pages[ $page ]['styles'] ?? [], $assets['styles'] ?? [] ),
            'scripts' => array_merge( $this->pages[ $page ]['scripts'] ?? [], $assets['scripts'] ?? [] ),
        ];
    }
    /**
     * Returns the default assets for the plugin.
     *
     * @param array  $assets The current assets.
     * @param string $context The context (e.g., 'frontend', 'admin').
     * @return array The default assets.
     */
    public function default_assets( array $assets, string $context ): array {
        $defaults = [
            'styles'  => [
                [
                    'handle' => 'wikipress-bootstrap',
                    'src' => WIKIPRESS_URL . 'src/Assets/dist/css/bootstrap.css',
                    'version' => '5.3.8'
                ],
                [
                    'handle' => 'wikipress-bootstrap-select',
                    'src' => WIKIPRESS_URL . 'src/Assets/dist/css/bootstrap-select.css',
                    'version' => '1.2.2',
                    'deps' => [
                        'wikipress-bootstrap'
                        ]
                ],
            ],
            'scripts' => [
                [
                    'handle' => 'wikipress-bootstrap',
                    'src' => WIKIPRESS_URL . 'src/Assets/dist/js/bootstrap.js',
                    'version' => '5.3.8',
                    'in_footer' => true
                ],
                [
                    'handle' => 'wikipress-bootstrap-select',
                    'src' => WIKIPRESS_URL . 'src/Assets/dist/js/bootstrap-select.js',
                    'version' => '1.2.2',
                    'deps' => [ 'wikipress-bootstrap' ],
                    'in_footer' => true
                ],
            ],
        ];

        if ( 'admin' === $context ) {
            $defaults['styles'][] = [
                'handle' => 'wikipress-admin-ui',
                'src' => WIKIPRESS_URL . 'src/Assets/dist/css/admin.ui.css',
            ];
            $defaults['scripts'][] = [
                'handle' => 'wikipress-shadow',
                'src' => WIKIPRESS_URL . 'src/Assets/dist/js/shadow.js',
                'in_footer' => true,
            ];
            $defaults['scripts'][] = [
                'handle' => 'wikipress-admin-ui',
                'src' => WIKIPRESS_URL . 'src/Assets/dist/js/admin.ui.js',
                'deps' => [ 'wikipress-bootstrap', 'wikipress-shadow' ],
                'in_footer' => true,
            ];
        }

        return [ 'base' => $defaults ] + $defaults;
    }

    /**
     * Enqueues the frontend assets for the plugin.
     *
     * @return void
     */
    public function enqueue_frontend(): void {
        if ( ! is_singular( 'wikipress_page' ) ) {
            return;
        }

        $assets = apply_filters( 'wikipress_base_assets', [], 'frontend' );
        $this->enqueue_registered( 'frontend', [
            'styles'  => array_merge( $assets['base']['styles'] ?? [], [ [ 'handle' => 'wikipress-public', 'src' => WIKIPRESS_URL . 'src/Assets/css/public.css' ] ] ),
            'scripts' => array_merge( $assets['base']['scripts'] ?? [], [ [ 'handle' => 'wikipress-public', 'src' => WIKIPRESS_URL . 'src/Assets/js/public.js', 'in_footer' => true ] ] ),
        ] );
    }
    /**
     * Enqueues the admin assets for the plugin.
     *
     * @param string $hook_suffix The current admin page hook suffix.
     * @return void
     */
    public function enqueue_admin( string $hook_suffix ): void {
        if ( false === strpos( $hook_suffix, 'wikipress' ) ) {
            return;
        }

        $page = sanitize_key( $_GET['page'] ?? 'wikipress' );
        $registered = $this->pages[ $page ] ?? [];
        $base = apply_filters( 'wikipress_base_assets', [], 'admin' );
        $this->enqueue_registered( 'admin', [
            'styles'  => array_merge( $base['styles'] ?? [], $registered['styles'] ?? [] ),
            'scripts' => array_merge( $base['scripts'] ?? [], $registered['scripts'] ?? [] ),
        ] );

        $this->enqueue_fontawesome_vendor_assets();
    }

    /**
     * Request Font Awesome through the official vendor handle.
     *
     * The vendor remains responsible for registering the resource URL and
     * selecting CDN, Kit, Web Font, or SVG technology.
     *
     * @return void
     */
    private function enqueue_fontawesome_vendor_assets(): void {
        foreach ( [ 'font-awesome-official', 'font-awesome-official-v4shim' ] as $handle ) {
            if ( wp_style_is( $handle, 'registered' ) || wp_style_is( $handle, 'enqueued' ) ) {
                wp_enqueue_style( $handle );
            }

            if ( wp_script_is( $handle, 'registered' ) || wp_script_is( $handle, 'enqueued' ) ) {
                wp_enqueue_script( $handle );
            }
        }
    }
    /**
     * Enqueues the registered assets for a given context.
     *
     * @param string $context The context (e.g., 'frontend', 'admin').
     * @param array  $assets The assets to enqueue.
     * @return void
     */
    private function enqueue_registered( string $context, array $assets ): void {
        $assets = apply_filters( 'wikipress_' . $context . '_assets', $assets, $context );
        $this->enqueue_bundle( $assets );
    }
    /**
     * Enqueues a bundle of assets (styles and scripts).
     *
     * @param array $assets The assets to enqueue.
     * @return void
     */
    private function enqueue_bundle( array $assets ): void {
        if ( isset( $assets['styles'] ) && is_string( $assets['styles'] ) ) {
            $assets['styles'] = [ [ 'handle' => 'wikipress-admin-' . $assets['styles'], 'src' => WIKIPRESS_URL . 'src/Assets/dist/css/admin.' . $assets['styles'] . '.css' ] ];
        }
        if ( isset( $assets['scripts'] ) && is_string( $assets['scripts'] ) ) {
            $assets['scripts'] = [ [ 'handle' => 'wikipress-admin-' . $assets['scripts'], 'src' => WIKIPRESS_URL . 'src/Assets/dist/js/admin.' . $assets['scripts'] . '.js', 'deps' => [ 'wikipress-bootstrap' ] ] ];
        }
        foreach ( $assets['styles'] ?? [] as $style ) {
            wp_enqueue_style( $style['handle'], $style['src'], $style['deps'] ?? [], $style['version'] ?? WIKIPRESS_VERSION, $style['media'] ?? 'all' );
        }
        foreach ( $assets['scripts'] ?? [] as $script ) {
            wp_enqueue_script( $script['handle'], $script['src'], $script['deps'] ?? [], $script['version'] ?? WIKIPRESS_VERSION, $script['in_footer'] ?? true );
        }
        if ( 'wikipress-settings' === sanitize_key( $_GET['page'] ?? '' ) ) {
            $settings_config = [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wikipress_settings_tabs' ),
                'pluginNonce' => wp_create_nonce( 'wikipress_plugin_toggle' ),
                'pluginSettingsNonce' => wp_create_nonce( 'wikipress_plugin_settings' ),
            ];
            foreach ( [ 'wikipress-admin-settings', 'wikipress-admin-plugins' ] as $handle ) {
                if ( wp_script_is( $handle, 'enqueued' ) ) {
                    wp_localize_script( $handle, 'wikipressSettingsTabs', $settings_config );
                }
            }
        }
        if ( 'wikipress-manage' === sanitize_key( $_GET['page'] ?? '' ) && wp_script_is( 'wikipress-admin-wiki', 'enqueued' ) ) {
            wp_localize_script( 'wikipress-admin-wiki', 'wikipressWikiManager', [
                'ajaxUrl' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'wikipress_manage_wiki' ),
            ] );
        }
    }
}
