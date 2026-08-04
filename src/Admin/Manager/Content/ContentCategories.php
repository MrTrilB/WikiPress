<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentCategories extends Manager {
	/**
	 * Register assets for the categories screen.
	 *
	 * @param Assets $assets Asset registry.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-categories' ], 'content' );
	}

	/**
	 * Render the wiki category management page.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_term_table( Taxonomy::CATEGORY, __( 'Categories', 'wikipress' ) );
	}
}
