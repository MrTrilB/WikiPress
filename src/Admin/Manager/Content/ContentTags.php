<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentTags extends Manager {
	/**
	 * Register assets for the tags screen.
	 *
	 * @param Assets $assets Asset registry.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-tags' ], 'content' );
	}

	/**
	 * Render the wiki tag management page.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->render_term_table( Taxonomy::TAG, __( 'Tags', 'wikipress' ) );
	}
}
