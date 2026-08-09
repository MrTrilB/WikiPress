<?php
/**
 * TagsManager class for WikiPress plugin.
 *
 * @package TrilBDev\WikiPress
 * @subpackage Admin\Manager\Wiki
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Admin\Manager\Wiki;

use TrilBDev\WikiPress\Admin\Manager\Wiki\WikiManager;
use TrilBDev\WikiPress\Assets\Assets;

class TagsManager extends WikiManager {
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-manage' ], 'wiki' );
    }
}