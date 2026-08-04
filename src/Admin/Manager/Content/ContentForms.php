<?php

namespace TrilBDev\WikiPress\Admin\Manager\Content;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ContentForms extends Manager {
	/**
	 * Register assets for the content forms.
	 *
	 * @param Assets $assets Asset registry.
	 * @return void
	 */
	public function register_assets( Assets $assets ): void {
		$this->register_page_assets( $assets, [ 'wikipress-add-new', 'wikipress-edit' ], 'page' );
	}

	/**
	 * Render the create wiki page form.
	 *
	 * @return void
	 */
	public function render_add_new(): void {
		$this->header( __( 'Add New Wiki Page', 'wikipress' ) );
		$this->render_form( 'wikipress_create_page', __( 'Create Wiki Page', 'wikipress' ) );
		$this->footer();
	}

	/**
	 * Render the edit form for a wiki or wiki page.
	 *
	 * @return void
	 */
	public function render_edit(): void {
		$post_id = absint( $_GET['post_id'] ?? 0 );
		$post = get_post( $post_id );
		if ( ! $post || ! in_array( $post->post_type, [ PostType::WIKI, PostType::PAGE ], true ) ) {
			wp_die( esc_html__( 'The requested item was not found.', 'wikipress' ), 404 );
		}

		$this->header( $post->post_type === PostType::WIKI ? __( 'Edit Wiki', 'wikipress' ) : __( 'Edit Wiki Page', 'wikipress' ) );
		$this->render_form( 'wikipress_update_post', __( 'Save Changes', 'wikipress' ), $post );
		$this->footer();
	}

	/**
	 * Render the shared create and edit form fields.
	 *
	 * @param string $action Admin-post action.
	 * @param string $submit Submit button label.
	 * @param \WP_Post|null $post Existing post for edit mode.
	 * @return void
	 */
	private function render_form( string $action, string $submit, ?\WP_Post $post = null ): void {
		$is_edit = $post instanceof \WP_Post;
		$is_wiki = $is_edit && $post->post_type === PostType::WIKI;
		$wikis = get_posts( [ 'post_type' => PostType::WIKI, 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
		$categories = get_terms( [ 'taxonomy' => Taxonomy::CATEGORY, 'hide_empty' => false ] );
		$tags = get_terms( [ 'taxonomy' => Taxonomy::TAG, 'hide_empty' => false ] );
		$selected_categories = $is_edit ? wp_get_post_terms( $post->ID, Taxonomy::CATEGORY, [ 'fields' => 'ids' ] ) : [];
		$selected_tags = $is_edit ? wp_get_post_terms( $post->ID, Taxonomy::TAG, [ 'fields' => 'ids' ] ) : [];
		$title = $post->post_title ?? '';
		$content = $post->post_content ?? '';
		$status = $post->post_status ?? 'draft';
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="card wikipress-editor-form shadow-sm">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<?php if ( $is_edit ) : ?><input type="hidden" name="post_id" value="<?php echo absint( $post->ID ); ?>"><?php endif; ?>
			<?php wp_nonce_field( $is_edit ? 'wikipress_update_' . $post->ID : 'wikipress_create_page' ); ?>
			<div class="card-body row g-3">
				<div class="col-12"><label class="form-label" for="wikipress-form-title"><?php echo esc_html( $is_wiki ? __( 'Name', 'wikipress' ) : __( 'Title', 'wikipress' ) ); ?></label><input class="form-control" required id="wikipress-form-title" name="title" value="<?php echo esc_attr( $title ); ?>"></div>
				<div class="col-12"><label class="form-label" for="wikipress-form-content"><?php echo esc_html( $is_wiki ? __( 'Description', 'wikipress' ) : __( 'Content', 'wikipress' ) ); ?></label><textarea class="form-control" rows="10" id="wikipress-form-content" name="<?php echo esc_attr( $is_wiki ? 'description' : 'content' ); ?>"><?php echo esc_textarea( $content ); ?></textarea></div>
				<?php if ( $is_wiki ) : ?>
					<?php echo wp_kses_post( apply_filters( 'wikipress_wiki_form_fields', '', $post ) ); ?>
				<?php endif; ?>
				<?php if ( ! $is_wiki ) : ?>
					<div class="col-md-6"><label class="form-label" for="wikipress-form-wiki"><?php esc_html_e( 'Wiki', 'wikipress' ); ?></label><select class="form-select" id="wikipress-form-wiki" name="wiki_id"><option value="0"><?php esc_html_e( 'Unassigned', 'wikipress' ); ?></option><?php foreach ( $wikis as $wiki ) : ?><option value="<?php echo absint( $wiki->ID ); ?>" <?php selected( get_post_meta( $post->ID ?? 0, '_wikipress_wiki_id', true ), $wiki->ID ); ?>><?php echo esc_html( get_the_title( $wiki ) ); ?></option><?php endforeach; ?></select></div>
					<div class="col-md-6"><label class="form-label" for="wikipress-form-categories"><?php esc_html_e( 'Categories', 'wikipress' ); ?></label><select class="form-select" multiple id="wikipress-form-categories" name="categories[]"><?php foreach ( is_wp_error( $categories ) ? [] : $categories as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>" <?php selected( in_array( $term->term_id, $selected_categories, true ) ); ?>><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></div>
					<div class="col-md-6"><label class="form-label" for="wikipress-form-tags"><?php esc_html_e( 'Tags', 'wikipress' ); ?></label><select class="form-select" multiple id="wikipress-form-tags" name="tags[]"><?php foreach ( is_wp_error( $tags ) ? [] : $tags as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>" <?php selected( in_array( $term->term_id, $selected_tags, true ) ); ?>><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></div>
				<?php endif; ?>
				<div class="col-md-6"><label class="form-label" for="wikipress-form-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label><select class="form-select" id="wikipress-form-status" name="status"><?php foreach ( [ 'draft' => __( 'Draft', 'wikipress' ), 'publish' => __( 'Published', 'wikipress' ), 'private' => __( 'Private', 'wikipress' ) ] as $key => $label ) : ?><option value="<?php echo esc_attr( $key ); ?>" <?php selected( $status, $key ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></div>
				<div class="col-12"><?php submit_button( $submit, 'primary', 'submit', false, [ 'class' => 'btn btn-primary' ] ); ?></div>
			</div>
		</form>
		<?php
	}
}
