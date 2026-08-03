<?php

namespace TrilBDev\WikiPress\Admin;

use TrilBDev\WikiPress\API\API;
use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Analytics\Analytics;
use TrilBDev\WikiPress\Includes\Tools\DataTransfer;
use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Admin {
    public function register_admin_menu(): void {
        add_menu_page( __( 'WikiPress', 'wikipress' ), __( 'WikiPress', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ], 'dashicons-book-alt', 30 );
        add_submenu_page( 'wikipress', __( 'Dashboard', 'wikipress' ), __( 'Dashboard', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ] );
        add_submenu_page( 'wikipress', __( 'All Wikis', 'wikipress' ), __( 'All Wikis', 'wikipress' ), $this->capability( 'create_wikis', 'manage_options' ), 'wikipress-wikis', [ $this, 'render_wikis' ] );
        add_submenu_page( 'wikipress', __( 'All Wiki Pages', 'wikipress' ), __( 'All Wiki Pages', 'wikipress' ), $this->capability( 'write_pages', 'edit_posts' ), 'wikipress-pages', [ $this, 'render_pages' ] );
        add_submenu_page( 'wikipress', __( 'Add New', 'wikipress' ), __( 'Add New', 'wikipress' ), $this->capability( 'write_pages', 'edit_posts' ), 'wikipress-add-new', [ $this, 'render_add_new' ] );
        add_submenu_page( 'wikipress', __( 'Categories', 'wikipress' ), __( 'Categories', 'wikipress' ), 'manage_categories', 'wikipress-categories', [ $this, 'render_categories' ] );
        add_submenu_page( 'wikipress', __( 'Tags', 'wikipress' ), __( 'Tags', 'wikipress' ), 'manage_categories', 'wikipress-tags', [ $this, 'render_tags' ] );
        add_submenu_page( 'wikipress', __( 'Settings', 'wikipress' ), __( 'Settings', 'wikipress' ), 'manage_options', 'wikipress-settings', [ $this, 'render_settings' ] );
        add_submenu_page( 'wikipress', __( 'Analytics', 'wikipress' ), __( 'Analytics', 'wikipress' ), $this->capability( 'view_analytics', 'manage_options' ), 'wikipress-analytics', [ $this, 'render_analytics' ] );
        add_submenu_page( null, __( 'Edit WikiPress Item', 'wikipress' ), __( 'Edit WikiPress Item', 'wikipress' ), 'edit_posts', 'wikipress-edit', [ $this, 'render_edit' ] );
    }

    public function register_settings(): void {
        register_setting( 'wikipress_settings', 'wikipress_general', [ 'sanitize_callback' => [ $this, 'sanitize_general' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_layout', [ 'sanitize_callback' => [ $this, 'sanitize_layout' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_access', [ 'sanitize_callback' => [ $this, 'sanitize_access' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_tools', [ 'sanitize_callback' => [ $this, 'sanitize_tools' ] ] );
    }

    public function sanitize_general( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $rewrite_changed = false;
        foreach ( [ 'root_name', 'root_slug', 'category_slug', 'tag_slug', 'permalink' ] as $key ) {
            $value = in_array( $key, [ 'root_slug', 'category_slug', 'tag_slug' ], true ) ? sanitize_title( $input[ $key ] ?? '' ) : sanitize_text_field( $input[ $key ] ?? '' );
            $rewrite_changed = $rewrite_changed || $value !== (string) Settings::get( $key, '' );
            $input[ $key ] = $value;
            Settings::set( $key, $input[ $key ] );
        }
        if ( $rewrite_changed ) {
            flush_rewrite_rules();
        }
        return $input;
    }

    public function sanitize_layout( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( [ 'show_search', 'show_toc' ] as $key ) {
            $value = ! empty( $input[ $key ] );
            $input[ $key ] = $value;
            Settings::set( $key, $value );
        }
        return $input;
    }

    public function sanitize_access( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( [ 'create_wikis', 'write_pages', 'view_analytics', 'manage_plugins' ] as $key ) {
            $input[ $key ] = sanitize_key( $input[ $key ] ?? 'manage_options' );
            Settings::set( $key, $input[ $key ] );
        }
        return $input;
    }

    public function sanitize_tools( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $input['debug_logging'] = ! empty( $input['debug_logging'] );
        Settings::set( 'debug_logging', $input['debug_logging'] );
        return $input;
    }

    public function render_dashboard(): void {
        $this->header( __( 'Dashboard', 'wikipress' ) );
        echo '<div class="row g-4">';
        $this->card( __( 'Wikis', 'wikipress' ), wp_count_posts( 'wikipress_wiki' )->publish ?? 0, 'wikipress-wikis' );
        $this->card( __( 'Wiki Pages', 'wikipress' ), wp_count_posts( 'wikipress_page' )->publish ?? 0, 'wikipress-pages' );
        $this->card( __( 'Categories', 'wikipress' ), wp_count_terms( 'wikipress_category' ), 'wikipress-categories' );
        $this->card( __( 'Tags', 'wikipress' ), wp_count_terms( 'wikipress_tag' ), 'wikipress-tags' );
        echo '</div>';
        $this->footer();
    }

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
    public function render_pages(): void { $this->render_post_table( 'wikipress_page', __( 'All Wiki Pages', 'wikipress' ) ); }
    public function render_categories(): void { $this->render_term_table( 'wikipress_category', __( 'Categories', 'wikipress' ) ); }
    public function render_tags(): void { $this->render_term_table( 'wikipress_tag', __( 'Tags', 'wikipress' ) ); }

    public function render_settings(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'general' );
        $groups = Settings::get_all();
        $values = $groups[ $tab ] ?? [];
        $tabs = [ 'general' => __( 'General', 'wikipress' ), 'layout' => __( 'Layout', 'wikipress' ), 'access' => __( 'Access Restrictions', 'wikipress' ), 'tools' => __( 'Tools', 'wikipress' ) ];
        $this->header( __( 'Settings', 'wikipress' ) );
        ?>
        <nav class="nav nav-tabs mb-4">
            <?php foreach ( $tabs as $key => $label ) : ?><a class="nav-link <?php echo $tab === $key ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-settings&tab=' . $key ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?>
        </nav>
        <form method="post" action="options.php" class="wikipress-settings-form card shadow-sm">
            <?php settings_fields( 'wikipress_settings' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
            <?php if ( 'general' === $tab ) : ?>
                <?php foreach ( [ 'root_name' => 'WikiPress Root Name', 'root_slug' => 'WikiPress Root Slug', 'category_slug' => 'Custom Category Slug', 'tag_slug' => 'Custom Tags Slug', 'permalink' => 'WikiPress Permalink' ] as $key => $label ) : ?>
                    <tr><th scope="row"><label for="wikipress-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input class="regular-text" id="wikipress-<?php echo esc_attr( $key ); ?>" name="wikipress_general[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"></td></tr>
                <?php endforeach; ?>
            <?php elseif ( 'layout' === $tab ) : ?>
                <?php foreach ( [ 'show_search' => 'Show Search', 'show_toc' => 'Show Table of Contents' ] as $key => $label ) : ?><tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><label><input type="checkbox" name="wikipress_layout[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $values[ $key ] ) ); ?>> <?php echo esc_html( $label ); ?></label></td></tr><?php endforeach; ?>
            <?php elseif ( 'access' === $tab ) : ?>
                <?php foreach ( [ 'create_wikis' => 'Who can create wikis?', 'write_pages' => 'Who can write wiki pages?', 'view_analytics' => 'Who can check analytics?', 'manage_plugins' => 'Who can manage plugins?' ] as $key => $label ) : ?><tr><th scope="row"><label for="wikipress-access-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><select id="wikipress-access-<?php echo esc_attr( $key ); ?>" name="wikipress_access[<?php echo esc_attr( $key ); ?>]"><?php foreach ( [ 'manage_options' => 'Administrators', 'edit_posts' => 'Editors', 'publish_posts' => 'Authors' ] as $capability => $capability_label ) : ?><option value="<?php echo esc_attr( $capability ); ?>" <?php selected( $values[ $key ] ?? 'manage_options', $capability ); ?>><?php echo esc_html( $capability_label ); ?></option><?php endforeach; ?></select></td></tr><?php endforeach; ?>
            <?php elseif ( 'tools' === $tab ) : ?>
                <tr><th scope="row"><?php esc_html_e( 'Debug logging', 'wikipress' ); ?></th><td><label><input type="checkbox" name="wikipress_tools[debug_logging]" value="1" <?php checked( ! empty( $values['debug_logging'] ) ); ?>> <?php esc_html_e( 'Enable WikiPress debug logging', 'wikipress' ); ?></label></td></tr>
                <tr><th scope="row"><?php esc_html_e( 'Import and export', 'wikipress' ); ?></th><td><a class="button" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wikipress_export' ), 'wikipress_export' ) ); ?>"><?php esc_html_e( 'Export WikiPress JSON', 'wikipress' ); ?></a><hr><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data"><input type="hidden" name="action" value="wikipress_import"><?php wp_nonce_field( 'wikipress_import' ); ?><input type="file" name="wikipress_import_file" accept="application/json,.json" required><button class="button" type="submit"><?php esc_html_e( 'Import JSON', 'wikipress' ); ?></button></form></td></tr>
                <tr><th scope="row"><?php esc_html_e( 'Database manager', 'wikipress' ); ?></th><td><?php esc_html_e( 'The settings table is managed automatically during plugin activation.', 'wikipress' ); ?></td></tr>
            <?php endif; ?>
            </tbody></table>
            <?php submit_button( __( 'Save Changes', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php $this->footer();
    }

    public function render_analytics(): void {
        $this->header( __( 'Analytics', 'wikipress' ) );
        $this->card( __( 'Total Wiki Page Views', 'wikipress' ), Analytics::total_views(), 'wikipress-pages' );
        echo '<h2 class="h4 mt-4">' . esc_html__( 'Most Viewed Wiki Pages', 'wikipress' ) . '</h2><div class="table-responsive"><table class="table table-striped table-hover align-middle"><thead><tr><th>' . esc_html__( 'Page', 'wikipress' ) . '</th><th>' . esc_html__( 'Views', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( Analytics::top_pages() as $page ) {
            printf( '<tr><td><a href="%s">%s</a></td><td>%d</td></tr>', esc_url( $page['link'] ), esc_html( $page['title'] ), absint( $page['views'] ) );
        }
        echo '</tbody></table></div>';
        $this->footer();
    }

    public function render_add_new(): void {
        $wikis = get_posts( [ 'post_type' => PostType::WIKI, 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
        $categories = get_terms( [ 'taxonomy' => Taxonomy::CATEGORY, 'hide_empty' => false ] );
        $tags = get_terms( [ 'taxonomy' => Taxonomy::TAG, 'hide_empty' => false ] );
        $selected_categories = wp_get_post_terms( $post_id, Taxonomy::CATEGORY, [ 'fields' => 'ids' ] );
        $selected_tags = wp_get_post_terms( $post_id, Taxonomy::TAG, [ 'fields' => 'ids' ] );
        $this->header( __( 'Add New Wiki Page', 'wikipress' ) );
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="card shadow-sm">
            <input type="hidden" name="action" value="wikipress_create_page">
            <?php wp_nonce_field( 'wikipress_create_page' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
                <tr><th scope="row"><label for="wikipress-page-title"><?php esc_html_e( 'Title', 'wikipress' ); ?></label></th><td><input class="regular-text" required id="wikipress-page-title" name="title" type="text"></td></tr>
                <tr><th scope="row"><label for="wikipress-page-content"><?php esc_html_e( 'Content', 'wikipress' ); ?></label></th><td><textarea class="large-text" rows="12" id="wikipress-page-content" name="content"></textarea></td></tr>
                <tr><th scope="row"><label for="wikipress-page-wiki"><?php esc_html_e( 'Wiki', 'wikipress' ); ?></label></th><td><select id="wikipress-page-wiki" name="wiki_id"><option value="0"><?php esc_html_e( 'Unassigned', 'wikipress' ); ?></option><?php foreach ( $wikis as $wiki ) : ?><option value="<?php echo absint( $wiki->ID ); ?>"><?php echo esc_html( get_the_title( $wiki ) ); ?></option><?php endforeach; ?></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-status"><?php esc_html_e( 'Status', 'wikipress' ); ?></label></th><td><select id="wikipress-page-status" name="status"><option value="draft"><?php esc_html_e( 'Draft', 'wikipress' ); ?></option><option value="publish"><?php esc_html_e( 'Published', 'wikipress' ); ?></option><option value="private"><?php esc_html_e( 'Private', 'wikipress' ); ?></option></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-categories"><?php esc_html_e( 'Categories', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-page-categories" name="categories[]"><?php foreach ( $categories as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr>
                <tr><th scope="row"><label for="wikipress-page-tags"><?php esc_html_e( 'Tags', 'wikipress' ); ?></label></th><td><select multiple id="wikipress-page-tags" name="tags[]"><?php foreach ( $tags as $term ) : ?><option value="<?php echo absint( $term->term_id ); ?>"><?php echo esc_html( $term->name ); ?></option><?php endforeach; ?></select></td></tr>
            </tbody></table>
            <?php submit_button( __( 'Create Wiki Page', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php $this->footer();
    }

    public function create_page(): void {
        if ( ! current_user_can( $this->capability( 'write_pages', 'edit_posts' ) ) ) {
            wp_die( esc_html__( 'You are not allowed to create Wiki Pages.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_create_page' );
        $result = API::create_page( wp_unslash( $_POST ) );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ), 400 );
        }
        wp_safe_redirect( admin_url( 'admin.php?page=wikipress-pages&created=1' ) );
        exit;
    }

    public function create_wiki(): void {
        if ( ! current_user_can( $this->capability( 'create_wikis', 'manage_options' ) ) ) {
            wp_die( esc_html__( 'You are not allowed to create Wikis.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_create_wiki' );
        $result = API::create_wiki( wp_unslash( $_POST ) );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ), 400 );
        }
        wp_safe_redirect( admin_url( 'admin.php?page=wikipress-wikis&created=1' ) );
        exit;
    }

    public function delete_post(): void {
        $post_id = absint( $_GET['post_id'] ?? 0 );
        $post_type = sanitize_key( $_GET['post_type'] ?? '' );
        if ( ! current_user_can( 'delete_posts' ) || ! in_array( $post_type, [ PostType::WIKI, PostType::PAGE ], true ) ) {
            wp_die( esc_html__( 'You are not allowed to delete this item.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_delete_' . $post_id );
        $post = get_post( $post_id );
        if ( ! $post || $post->post_type !== $post_type ) {
            wp_die( esc_html__( 'The requested item was not found.', 'wikipress' ), 404 );
        }
        $deleted = $post_type === PostType::WIKI ? API::delete_wiki( $post_id ) : API::delete_page( $post_id );
        if ( is_wp_error( $deleted ) ) {
            wp_die( esc_html( $deleted->get_error_message() ), 400 );
        }
        $target = $post_type === PostType::WIKI ? 'wikipress-wikis' : 'wikipress-pages';
        wp_safe_redirect( admin_url( 'admin.php?page=' . $target . '&deleted=1' ) );
        exit;
    }

    public function create_term(): void {
        if ( ! current_user_can( 'manage_categories' ) ) {
            wp_die( esc_html__( 'You are not allowed to manage Wiki taxonomies.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_create_term' );
        $taxonomy = sanitize_key( $_POST['taxonomy'] ?? '' );
        if ( ! in_array( $taxonomy, [ Taxonomy::CATEGORY, Taxonomy::TAG ], true ) ) {
            wp_die( esc_html__( 'Invalid Wiki taxonomy.', 'wikipress' ), 400 );
        }
        $result = wp_insert_term( sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) ), $taxonomy, [ 'description' => sanitize_textarea_field( wp_unslash( $_POST['description'] ?? '' ) ) ] );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ), 400 );
        }
        wp_safe_redirect( admin_url( 'admin.php?page=' . ( $taxonomy === Taxonomy::CATEGORY ? 'wikipress-categories' : 'wikipress-tags' ) . '&created=1' ) );
        exit;
    }

    public function render_edit(): void {
        $post_id = absint( $_GET['post_id'] ?? 0 );
        $post = get_post( $post_id );
        if ( ! $post || ! in_array( $post->post_type, [ PostType::WIKI, PostType::PAGE ], true ) ) {
            wp_die( esc_html__( 'The requested item was not found.', 'wikipress' ), 404 );
        }
        $is_wiki = $post->post_type === PostType::WIKI;
        $this->header( $is_wiki ? __( 'Edit Wiki', 'wikipress' ) : __( 'Edit Wiki Page', 'wikipress' ) );
        $wikis = get_posts( [ 'post_type' => PostType::WIKI, 'post_status' => [ 'publish', 'draft' ], 'posts_per_page' => 100, 'orderby' => 'title', 'order' => 'ASC' ] );
        $categories = get_terms( [ 'taxonomy' => Taxonomy::CATEGORY, 'hide_empty' => false ] );
        $tags = get_terms( [ 'taxonomy' => Taxonomy::TAG, 'hide_empty' => false ] );
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

    public function update_post(): void {
        $post_id = absint( $_POST['post_id'] ?? 0 );
        $post = get_post( $post_id );
        if ( ! $post || ! in_array( $post->post_type, [ PostType::WIKI, PostType::PAGE ], true ) || ! current_user_can( 'edit_posts' ) ) {
            wp_die( esc_html__( 'You are not allowed to edit this item.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_update_' . $post_id );
        $payload = wp_unslash( $_POST );
        $result = $post->post_type === PostType::WIKI ? API::update_wiki( $post_id, $payload ) : API::update_page( $post_id, $payload );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ), 400 );
        }
        wp_safe_redirect( admin_url( 'admin.php?page=' . ( $post->post_type === PostType::WIKI ? 'wikipress-wikis' : 'wikipress-pages' ) . '&updated=1' ) );
        exit;
    }

    public function export_data(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to export WikiPress data.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_export' );
        nocache_headers();
        header( 'Content-Type: application/json; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=wikipress-export-' . gmdate( 'Y-m-d' ) . '.json' );
        echo wp_json_encode( DataTransfer::export(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        exit;
    }

    public function import_data(): void {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You are not allowed to import WikiPress data.', 'wikipress' ), 403 );
        }
        check_admin_referer( 'wikipress_import' );
        $file = $_FILES['wikipress_import_file'] ?? [];
        if ( empty( $file['tmp_name'] ) || ( $file['error'] ?? UPLOAD_ERR_NO_FILE ) !== UPLOAD_ERR_OK ) {
            wp_die( esc_html__( 'Please upload a valid WikiPress JSON export.', 'wikipress' ), 400 );
        }
        $data = json_decode( file_get_contents( $file['tmp_name'] ), true );
        if ( ! is_array( $data ) ) {
            wp_die( esc_html__( 'The uploaded file is not valid JSON.', 'wikipress' ), 400 );
        }
        $result = DataTransfer::import( $data );
        if ( is_wp_error( $result ) ) {
            wp_die( esc_html( $result->get_error_message() ), 400 );
        }
        wp_safe_redirect( admin_url( 'admin.php?page=wikipress-settings&tab=tools&imported=1' ) );
        exit;
    }

    private function render_post_table( string $post_type, string $title ): void {
        $this->header( $title );
        $this->render_post_table_body( $post_type );
        $this->footer();
    }

    private function render_post_table_body( string $post_type ): void {
        $page = max( 1, absint( $_GET['paged'] ?? 1 ) );
        $search = sanitize_text_field( wp_unslash( $_GET['s'] ?? '' ) );
        $query = new \WP_Query( [ 'post_type' => $post_type, 'posts_per_page' => 20, 'paged' => $page, 's' => $search, 'post_status' => 'any' ] );
        echo '<form method="get" class="row g-2 align-items-end mb-4"><input type="hidden" name="page" value="' . esc_attr( sanitize_key( $_GET['page'] ?? '' ) ) . '"><div class="col-sm-6 col-md-4"><label class="form-label" for="wikipress-search">' . esc_html__( 'Search', 'wikipress' ) . '</label><input class="form-control" id="wikipress-search" name="s" value="' . esc_attr( $search ) . '"></div><div class="col-auto"><button class="btn btn-outline-primary" type="submit">' . esc_html__( 'Search', 'wikipress' ) . '</button></div></form>';
        echo '<div class="table-responsive"><table class="table table-striped table-hover align-middle"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Author', 'wikipress' ) . '</th><th>' . esc_html__( 'Created', 'wikipress' ) . '</th><th>' . esc_html__( 'Actions', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $query->posts as $post ) {
            $delete_url = wp_nonce_url( admin_url( 'admin-post.php?action=wikipress_delete_post&post_id=' . absint( $post->ID ) . '&post_type=' . rawurlencode( $post_type ) ), 'wikipress_delete_' . absint( $post->ID ) );
            $edit_url = admin_url( 'admin.php?page=wikipress-edit&post_id=' . absint( $post->ID ) );
            printf( '<tr><td>%s</td><td>%s</td><td>%s</td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary me-2" href="%s">%s</a><a class="btn btn-sm btn-outline-danger" href="%s">%s</a></td></tr>', esc_html( get_the_title( $post ) ), esc_html( get_the_author_meta( 'display_name', $post->post_author ) ), esc_html( get_the_date( '', $post ) ), esc_url( $edit_url ), esc_html__( 'Edit', 'wikipress' ), esc_url( $delete_url ), esc_html__( 'Delete', 'wikipress' ) );
        }
        if ( ! $query->posts ) {
            echo '<tr><td colspan="4">' . esc_html__( 'No items found.', 'wikipress' ) . '</td></tr>';
        }
        echo '</tbody></table></div>';
        echo '<div class="tablenav bottom"><div class="tablenav-pages">' . wp_kses_post( paginate_links( [ 'base' => add_query_arg( [ 'page' => sanitize_key( $_GET['page'] ?? '' ), 's' => rawurlencode( $search ), 'paged' => '%#%' ], admin_url( 'admin.php' ) ), 'format' => '', 'current' => $page, 'total' => max( 1, (int) $query->max_num_pages ), 'type' => 'plain' ] ) ) . '</div></div>';
    }

    private function render_term_table( string $taxonomy, string $title ): void {
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

    private function header( string $title ): void { echo '<div class="wrap wikipress-admin"><div class="container-fluid px-0 py-3"><h1 class="display-6 mb-4">' . esc_html( $title ) . '</h1>'; }
    private function footer(): void { echo '</div></div>'; }
    private function card( string $label, $value, string $slug ): void { printf( '<div class="col-md-6 col-xl-3 mb-4"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h6 text-muted">%s</h2><p class="display-6 mb-0"><a class="text-decoration-none" href="%s">%s</a></p></div></div></div>', esc_html( $label ), esc_url( admin_url( 'admin.php?page=' . $slug ) ), esc_html( (string) $value ) ); }

    private function capability( string $key, string $fallback ): string {
        $capability = sanitize_key( (string) Settings::get( $key, $fallback ) );
        return in_array( $capability, [ 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ], true ) ? $capability : $fallback;
    }
}
