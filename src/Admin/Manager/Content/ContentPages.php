<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentPages extends Manager {
	/**
	 * Register assets for the wiki pages screen.
	 *
	 * @param Assets $assets Asset registry.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-pages' ], 'content' );
	}

	/**
	 * Render the wiki page listing.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_post_table( PostType::PAGE, __( 'All Wiki Pages', 'wikipress' ) );
	}
}
