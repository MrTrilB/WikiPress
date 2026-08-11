<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Assets
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Demo\Assets
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Assets;

use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\RequestHelper;

final class Assets {
    private LoaderHelper $loader;

    public function __construct( ?LoaderHelper $loader = null ) {
        $this->loader = $loader ?? new LoaderHelper();
    }

    /**
     * Constructor for the Demo plugin assets.
     */
    public function register(): void {
        $this->loader->register_component( $this, [
            [ 
                'type' => 'filter',
                'hook' => 'wikipress_frontend_assets',
                'callback' => 'register_frontend_assets'
            ],
            [ 
                'type' => 'action',
                'hook' => 'admin_enqueue_scripts',
                'callback' => 'enqueue_admin_assets',
                'priority' => 20
            ],
        ] )->run();
    }

    public function register_frontend_assets( array $assets ): array {
        $assets['scripts'][] = [
            'handle' => 'wikipress-internal-wiki-plugin',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/InternalWiki/Assets/dist/js/internalwiki.js',
            'in_footer' => true,
        ];

        return $assets;
    }

    public function enqueue_admin_assets(): void {
        if ( 'wikipress-manage' !== RequestHelper::get_key( 'page' ) ) {
            return;
        }

        wp_enqueue_script(
            'wikipress-internal-wiki-admin',
            WIKIPRESS_URL . 'src/Includes/Plugins/InternalWiki/Assets/dist/js/admin.internal-wiki.js',
            [ 'wikipress-bootstrap-select' ],
            WIKIPRESS_VERSION,
            true
        );
    }
}