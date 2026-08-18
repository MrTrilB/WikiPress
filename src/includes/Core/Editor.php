<?php

namespace WikiPress\Includes\Core;

use WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Editor {
    public static function save_wiki_page( int $wiki_id, int $page_id = 0 ): bool {
        if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ?? '' ) || 'save_wiki_page' !== ( $_POST['wikipress_action'] ?? '' ) || ! check_admin_referer( 'wikipress_save_wiki_page', 'wikipress_save_wiki_page_nonce' ) ) {
            return false;
        }

        $input = wp_unslash( $_POST['wikipress_page'] ?? [] );
        $input = is_array( $input ) ? $input : [];
        $title = SanitizationHelper::text( $input['title'] ?? '' );
        if ( '' === $title ) {
            return false;
        }

        $post_id = wp_insert_post( [
            'ID' => $page_id,
            'post_type' => PostType::PAGE,
            'post_title' => $title,
            'post_content' => wp_kses_post( (string) ( $input['content'] ?? '' ) ),
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ], true );
        if ( is_wp_error( $post_id ) ) {
            return false;
        }

        update_post_meta( $post_id, '_wikipress_wiki_id', $wiki_id );
        return true;
    }

    public static function render_wiki_page_form( ?\WP_Post $page = null ): void {
        ?>
        <form method="post" class="card shadow-sm">
            <?php wp_nonce_field( 'wikipress_save_wiki_page', 'wikipress_save_wiki_page_nonce' ); ?>
            <input type="hidden" name="wikipress_action" value="save_wiki_page">
            <div class="card-body"><div class="mb-3"><label class="form-label" for="wikipress-page-title"><?php esc_html_e( 'Page Title', 'wikipress' ); ?></label><input class="form-control" id="wikipress-page-title" name="wikipress_page[title]" value="<?php echo esc_attr( $page ? $page->post_title : '' ); ?>" required></div><?php FormFieldHelper::tinymce( 'wikipress-page-content', 'wikipress_page[content]', __( 'Page Content', 'wikipress' ), $page ? $page->post_content : '', 14, true ); ?></div>
            <div class="card-footer d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage' ) ); ?>"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></a><button class="btn btn-primary" type="submit"><?php echo esc_html( $page ? __( 'Save Page', 'wikipress' ) : __( 'Create Page', 'wikipress' ) ); ?></button></div>
        </form>
        <?php
    }
}