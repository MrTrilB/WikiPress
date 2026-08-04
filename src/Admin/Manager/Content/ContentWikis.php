<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\PostType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentWikis extends Manager {
	/**
	 * Register assets for the wiki page.
	 *
	 * @param Assets $assets Asset registry.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-wikis' ], 'content' );
	}

	/**
	 * Render the wiki listing and creation form.
	 *
	 * @return void
	 */
	public function render(): void {
		$this->header( __( 'All Wikis', 'wikipress' ) );
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wikipress-inline-form row g-3 align-items-end mb-4">
			<input type="hidden" name="action" value="wikipress_create_wiki">
			<?php wp_nonce_field( 'wikipress_create_wiki' ); ?>
			<div class="col-md-4"><label class="form-label" for="wikipress-wiki-title"><?php esc_html_e( 'Wiki name', 'wikipress' ); ?></label><input required class="form-control" id="wikipress-wiki-title" name="title" type="text"></div>
			<div class="col-md-4"><label class="form-label" for="wikipress-wiki-description"><?php esc_html_e( 'Short description', 'wikipress' ); ?></label><input class="form-control" id="wikipress-wiki-description" name="description" type="text"></div>
			<div class="col-md-2"><label class="form-label" for="wikipress-wiki-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label><select class="form-select" id="wikipress-wiki-status" name="status"><option value="publish"><?php esc_html_e( 'Published', 'wikipress' ); ?></option><option value="draft"><?php esc_html_e( 'Draft', 'wikipress' ); ?></option></select></div>
			<div class="col-12"><?php echo wp_kses_post( apply_filters( 'wikipress_wiki_form_fields', '', null ) ); ?></div>
			<div class="col-md-2"><?php submit_button( __( 'Create Wiki', 'wikipress' ), 'primary', 'submit', false, [ 'class' => 'btn btn-primary w-100' ] ); ?></div>
		</form>
		<?php
		$this->render_post_table_body( PostType::WIKI );
		$this->footer();
	}
}?>
