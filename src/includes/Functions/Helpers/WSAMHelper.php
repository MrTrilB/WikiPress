<?php
/**
 * WikiPress Sidebar Admin Menu Helper class for WikiPress plugin.
 * 
 * @package WikiPress
 * @subpackage Includes\Functions\Helpers
 * @since 1.0.0
 */
namespace WikiPress\Includes\Functions\Helpers;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WSAMHelper {
    /**
     * Get the admin sidebar menu page URL for a given slug.
     *
     * @param string $slug The slug of the admin sidebar menu page.
     * @return string The URL of the admin sidebar menu page.
     */
    public static function get_admin_sidebar_menu_page_url( string $slug ): string {
        return admin_url( 'admin.php?page=' . $slug );
    }
}