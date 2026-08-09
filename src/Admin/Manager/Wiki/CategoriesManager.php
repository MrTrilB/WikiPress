<?php
/**
 * CategoriesManager class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Wiki
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Wiki;
use TrilBDev\WikiPress\Admin\Manager\Wiki\WikiManager;

class CategoriesManager extends WikiManager {
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-manage' ], 'wiki' );
    }
}