<?php

namespace TrilBDev\WikiPress\Admin\Manager;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class ContentManager extends PageManager {
    public function render_wikis(): void {
        $this->header( __( 'All Wikis', 'wikipress' ) );
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wikipress-inline-form row g-3 align-items-end mb-4">
            <input type="hidden" name="action" value="wikipress_create_wiki">
            <?php wp_nonce_field( 'wikipress_create_wiki' ); ?>
            <div class="col-md-4"><label class="form-label" for="wikipress-wiki-title"><?php esc_html_e( 'Wiki name', 'wikipress' ); ?></label><input required class="form-control" id="wikipress-wiki-title" name="title" type="text"></div>
            <div class="col-md-4"><label class="form-label" for="wikipress-wiki-description"><?php esc_html_e( 'Short description', 'wikipress' ); ?></label><input class="form-control" id="wikipress-wiki-description" name="description" type="text"></div>
            <div class="col-md-2"><label class="form-label" for="wikipress-wiki-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label><select class="form-select" id="wikipress-wiki-status" name="status"><option value="publish"><?php esc_html_e( 'Published', 'wikipress' ); ?></option><option value="draft"><?php esc_html_e( 'Draft', 'wikipress' ); ?></option></select></div>
            <div class="col-md-2"><?php submit_button( __( 'Create Wiki', 'wikipress' ), 'primary', 'submit', false, [ 'class' => 'btn btn-primary w-100' ] ); ?></div>
        </form>
        <?php
        $this->render_post_table_body( PostType::WIKI );
        $this->footer();
    }

    public function render_pages(): void {
        $this->render_post_table( PostType::PAGE, __( 'All Wiki Pages', 'wikipress' ) );
    }

    public function render_add_new(): void {
        $wikis = get_posts( [ 'post_type' => PostType::WIKI, 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
        $categories = get_terms( [ 'taxonomy' => Taxonomy::CATEGORY, 'hide_empty' => false ] );
        $tags = get_terms( [ 'taxonomy' => Taxonomy::TAG, 'hide_empty' => false ] );
        $this->header( __( 'Add New Wiki Page', 'wikipress' ) );
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="card wikipress-editor-form shadow-sm">
            <input type="hidden" name="action" value="wikipress_create_page">
            <?php wp_nonce_field( 'wikipress_create_page' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
                <tr><th scope="row"><label for="wikipress-page-title"><?php esc_html_e( 'Title', 'wikipress' ); ?></label></th><td><input class="regular-text" required id="wikipress-page-title" name="title" type="text"></td></tr>
                <tr><th scope="row"><label for="wikipress-page-content"><?php esc_html_e( 'Content', 'wikipress' ); ?></label></th><td><textarea class="large-text" rows="12" id="wikipress-page-content" name="content"></textarea></td></tr>
                <tr><th scope="row"><label for="wikipress-page-wiki"><?php esc_html_e( 'Wiki', 'wikipress' ); ?></label></th><td><select id="wikipress-page-wiki" name="wiki_id"><option value="0"><?php esc_html_e( 'Unassigned', 'wikipress' ); ?></option><?php foreach ( $wikis as $wiki ) : ?><option value="<?php echo absint( $wiki->ID ); ?>"><?php echo esc_html( get_the_title( $wiki ) ); ?></option><?php endforeach; ?></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label></th><td><select id="wikipress-page-status" name="status"><option value="draft"><?php esc_html_e( 'Draft', 'wikipress' ); ?></option><option value="publish"><?php esc_html_e( 'Published', 'wikipress' ); ?></option><option value="private"><?php esc_html_e( 'Private', 'wikipress' ); ?></option></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-categories"><?php esc_html_e( 'Categories', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-page-categories" name="categories[]"><?php foreach ( is_wp_error( $categories ) ? [] : $categories as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-tags"><?php esc_html_e( 'Tags', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-page-tags" name="tags[]"><?php foreach ( is_wp_error( $tags ) ? [] : $tags as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr>
            </tbody></table>
            <?php submit_button( __( 'Create Wiki Page', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php $this->footer();
    }

    public function render_edit(): void {
        $post_id = absint( $_GET['post_id'] ?? 0 );
        $post = get_post( $post_id );
        if ( ! $post || ! in_array( $post->post_type, [ PostType::WIKI, PostType::PAGE ], true ) ) {
            wp_die( esc_html__( 'The requested item was not found.', 'wikipress' ), 404 );
        }
        $is_wiki = $post->post_type === PostType::WIKI;
        $wikis = get_posts( [ 'post_type' => PostType::WIKI, 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
        $categories = get_terms( [ 'taxonomy' => Taxonomy::CATEGORY, 'hide_empty' => false ] );
        $tags = get_terms( [ 'taxonomy' => Taxonomy::TAG, 'hide_empty' => false ] );
        $selected_categories = wp_get_post_terms( $post_id, Taxonomy::CATEGORY, [ 'fields' => 'ids' ] );
        $selected_tags = wp_get_post_terms( $post_id, Taxonomy::TAG, [ 'fields' => 'ids' ] );
        $this->header( $is_wiki ? __( 'Edit Wiki', 'wikipress' ) : __( 'Edit Wiki Page', 'wikipress' ) );
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="card shadow-sm">
            <input type="hidden" name="action" value="wikipress_update_post"><input type="hidden" name="post_id" value="<?php echo absint( $post_id ); ?>">
            <?php wp_nonce_field( 'wikipress_update_' . $post_id ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
                <tr><th scope="row"><label for="wikipress-edit-title"><?php echo esc_html( $is_wiki ? __( 'Name', 'wikipress' ) : __( 'Title', 'wikipress' ) ); ?></label></th><td><input class="regular-text" required id="wikipress-edit-title" name="title" value="<?php echo esc_attr( $post->post_title ); ?>"></td></tr>
                <tr><th scope="row"><label for="wikipress-edit-content"><?php echo esc_html( $is_wiki ? __( 'Description', 'wikipress' ) : __( 'Content', 'wikipress' ) ); ?></label></th><td><textarea class="large-text" rows="10" id="wikipress-edit-content" name="<?php echo $is_wiki ? 'description' : 'content'; ?>"><?php echo esc_textarea( $post->post_content ); ?></textarea></td></tr>
                <?php if ( ! $is_wiki ) : ?><tr><th scope="row"><label for="wikipress-edit-excerpt"><?php esc_html_e( 'Excerpt', 'wikipress' ); ?></label></th><td><textarea class="large-text" rows="3" id="wikipress-edit-excerpt" name="excerpt"><?php echo esc_textarea( $post->post_excerpt ); ?></textarea></td></tr><tr><th scope="row"><label for="wikipress-edit-wiki"><?php esc_html_e( 'Wiki', 'wikipress' ); ?></label></th><td><select id="wikipress-edit-wiki" name="wiki_id"><option value="0"><?php esc_html_e( 'Unassigned', 'wikipress' ); ?></option><?php foreach ( $wikis as $wiki ) : ?><option value="<?php echo absint( $wiki->ID ); ?>" <?php selected( get_post_meta( $post_id, '_wikipress_wiki_id', true ), $wiki->ID ); ?>><?php echo esc_html( get_the_title( $wiki ) ); ?></option><?php endforeach; ?></select></td></tr><tr><th scope="row"><label for="wikipress-edit-categories"><?php esc_html_e( 'Categories', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-edit-categories" name="categories[]"><?php foreach ( is_wp_error( $categories ) ? [] : $categories as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>" <?php selected( in_array( $term->term_id, $selected_categories, true ) ); ?>><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr><tr><th scope="row"><label for="wikipress-edit-tags"><?php esc_html_e( 'Tags', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-edit-tags" name="tags[]"><?php foreach ( is_wp_error( $tags ) ? [] : $tags as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>" <?php selected( in_array( $term->term_id, $selected_tags, true ) ); ?>><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr><?php endif; ?>
                <tr><th scope="row"><label for="wikipress-edit-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label></th><td><select id="wikipress-edit-status" name="status"><?php foreach ( [ 'draft' => __( 'Draft', 'wikipress' ), 'publish' => __( 'Published', 'wikipress' ), 'private' => __( 'Private', 'wikipress' ) ] as $status => $label ) : ?><option value="<?php echo esc_attr( $status ); ?>" <?php selected( $post->post_status, $status ); ?>><?php echo esc_html( $label ); ?></option><?php endforeach; ?></select></td></tr>
            </tbody></table>
            <?php submit_button( __( 'Save Changes', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php $this->footer();
    }

    public function render_terms( string $taxonomy, string $title ): void {
        $this->header( $title );
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="wikipress-inline-form row g-3 align-items-end mb-4">
            <input type="hidden" name="action" value="wikipress_create_term">
            <input type="hidden" name="taxonomy" value="<?php echo esc_attr( $taxonomy ); ?>">
            <?php wp_nonce_field( 'wikipress_create_term' ); ?>
            <div class="col-md-5"><label class="form-label" for="wikipress-term-name"><?php esc_html_e( 'Name', 'wikipress' ); ?></label><input required class="form-control" id="wikipress-term-name" name="name" type="text"></div>
            <div class="col-md-5"><label class="form-label" for="wikipress-term-description"><?php esc_html_e( 'Description', 'wikipress' ); ?></label><input class="form-control" id="wikipress-term-description" name="description" type="text"></div>
            <div class="col-md-2"><?php submit_button( __( 'Create', 'wikipress' ), 'primary', 'submit', false, [ 'class' => 'btn btn-primary w-100' ] ); ?></div>
        </form>
        <?php
        $terms = get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] );
        echo '<div class="table-responsive"><table class="table table-striped table-hover align-middle"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Slug', 'wikipress' ) . '</th><th>' . esc_html__( 'Post Count', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( is_wp_error( $terms ) ? [] : $terms as $term ) {
            printf( '<tr><td>%s</td><td>%s</td><td>%d</td></tr>', esc_html( $term->name ), esc_html( $term->slug ), absint( $term->count ) );
        }
        if ( is_wp_error( $terms ) || ! $terms ) {
            echo '<tr><td colspan="3">' . esc_html__( 'No terms found.', 'wikipress' ) . '</td></tr>';
        }
        echo '</tbody></table></div>';
        $this->footer();
    }
}
