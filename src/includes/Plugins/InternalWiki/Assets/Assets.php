<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin Assets
 *
 * @package TrilBDev
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
            [ 'type' => 'filter', 'hook' => 'wikipress_frontend_assets', 'callback' => 'register_frontend_assets' ],
            [ 'type' => 'filter', 'hook' => 'wikipress_admin_assets', 'callback' => 'register_admin_assets', 'accepted_args' => 2 ],
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

    public function register_admin_assets( array $assets, string $context = '' ): array {
        if ( 'wikipress-manage' !== RequestHelper::get_key( 'page' ) ) {
            return $assets;
        }

        $assets['scripts'][] = [
            'handle' => 'wikipress-internal-wiki',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/InternalWiki/Assets/dist/js/internalwiki.js',
            'deps' => [ 'wikipress-bootstrap-select' ],
            'in_footer' => true,
        ];

        return $assets;
    }
}