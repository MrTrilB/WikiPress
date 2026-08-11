<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Assets
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Demo\Assets
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Gutenburg\Assets;

use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the Demo plugin assets.
     */
    public function register(): void {
        $this->register_editor_script();
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'wikipress_frontend_assets', 'callback' => 'register_frontend_assets' ],
        ] )->run();
    }

    public function register_frontend_assets( array $assets ): array {
        $assets['scripts'][] = [
            'handle' => 'wikipress-gutenburg-blocks',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/Gutenburg/Assets/dist/js/blocks.js',
            'in_footer' => true,
        ];

        return $assets;
    }

    public function register_editor_script(): void {
        wp_register_script(
            'wikipress-gutenburg-blocks',
            WIKIPRESS_URL . 'src/includes/Plugins/Gutenburg/Assets/dist/js/blocks.js',
            [ 'wp-blocks', 'wp-block-editor', 'wp-components', 'wp-element', 'wp-i18n' ],
            WIKIPRESS_VERSION,
            true
        );
    }
}