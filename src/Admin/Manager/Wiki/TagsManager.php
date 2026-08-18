<?php
/**
 * TagsManager class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin\Manager\Wiki
 * @since 1.0.0
 */
namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Admin\Manager\Wiki\WikiManager;
use WikiPress\Assets\Assets;

class TagsManager extends WikiManager {
    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-manage' ], 'wiki' );
    }
}