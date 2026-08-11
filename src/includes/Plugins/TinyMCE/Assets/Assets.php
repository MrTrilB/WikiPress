<?php
/**
 * TinyMCE Editor Plugin Assets
 *
 * @package WikiPress
 * @subpackage Plugins\TinyMCE\Assets
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\TinyMCE\Assets;

use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\Includes\Plugins\TinyMCE\Includes\Settings\Settings;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the TinyMCE plugin assets.
     */
    public function register(): void {
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'wikipress_admin_assets', 'callback' => 'register_admin_assets', 'accepted_args' => 2 ],
        ] )->run();
    }

    public function register_admin_assets( array $assets, string $context = '' ): array {
        $base_url = WIKIPRESS_URL . 'src/includes/Plugins/TinyMCE/Assets/tinymce/';

        $assets['styles'][] = [
            'handle' => 'wikipress-tinymce-skin',
            'src' => $base_url . 'skins/ui/' . Settings::ui_skin() . '/skin.min.css',
        ];
        $assets['scripts'][] = [
            'handle' => 'wikipress-tinymce',
            'src' => $base_url . 'tinymce.min.js',
            'in_footer' => true,
        ];
        $assets['scripts'][] = [
            'handle' => 'wikipress-tinymce-boot',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/TinyMCE/Assets/js/tinymce.js',
            'deps' => [ 'wikipress-tinymce' ],
            'in_footer' => true,
            'localize' => [
                'object_name' => 'wikipressTinyMCE',
                'data' => [
                    'mediaTitle' => __( 'Insert media', 'wikipress' ),
                    'mediaButton' => __( 'Insert into editor', 'wikipress' ),
                    'mediaTooltip' => __( 'Insert media', 'wikipress' ),
                ],
            ],
        ];

        if ( function_exists( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }

        return $assets;
    }
}