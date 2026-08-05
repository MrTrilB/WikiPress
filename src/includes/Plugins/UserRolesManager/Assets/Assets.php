<?php
/**
 * TrilB.Dev Plugin - User Roles Manager Wiki Plugin Assets
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\UserRolesManager\Assets
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Assets;

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
        $this->loader->register_component( $this, [
            [ 'type' => 'filter', 'hook' => 'wikipress_frontend_assets', 'callback' => 'register_frontend_assets' ],
            [ 'type' => 'action', 'hook' => 'admin_enqueue_scripts', 'callback' => 'enqueue_admin_assets' ],
        ] )->run();
    }

    public function register_frontend_assets( array $assets ): array {

        $assets['scripts'][] = [

            'handle' => 'wikipress-user-roles-manager',
            'src' => WIKIPRESS_URL . 'src/includes/Plugins/UserRolesManager/Assets/dist/js/userrolesmanager.js',
            'in_footer' => true,

        ];

        return $assets;
    }

    public function enqueue_admin_assets( string $hook_suffix ): void {
        if ( 'users_page_wikipress-roles-manager' !== $hook_suffix ) {
            return;
        }

        wp_enqueue_style( 'wikipress-bootstrap', WIKIPRESS_URL . 'src/Assets/dist/css/bootstrap.css', [], '5.3.8' );
        wp_enqueue_style( 'wikipress-admin-page', WIKIPRESS_URL . 'src/Assets/dist/css/admin.page.css', [ 'wikipress-bootstrap' ], WIKIPRESS_VERSION );
        wp_enqueue_style( 'wikipress-user-roles-manager', WIKIPRESS_URL . 'src/includes/Plugins/UserRolesManager/Assets/dist/css/user-roles-manager.css', [ 'wikipress-bootstrap' ], WIKIPRESS_VERSION );
        wp_enqueue_script( 'wikipress-bootstrap', WIKIPRESS_URL . 'src/Assets/dist/js/bootstrap.js', [], '5.3.8', true );
        wp_enqueue_script( 'wikipress-user-roles-manager', WIKIPRESS_URL . 'src/includes/Plugins/UserRolesManager/Assets/dist/js/user-roles-manager.js', [ 'wikipress-bootstrap' ], WIKIPRESS_VERSION, true );
    }
}