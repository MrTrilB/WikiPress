<?php

namespace WikiPress\Admin\Manager\Wiki;

use WikiPress\Includes\Core\PostType;
use WikiPress\Includes\Core\Taxonomy;
use WikiPress\Includes\Functions\Helpers\QueryHelper;
use WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use WikiPress\Includes\Functions\Helpers\TaxonomyHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiForms {
    public static function render_new_wiki_form( array $categories, array $tags, string $fields = '' ): void {
        ?>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage&wiki=new' ) ); ?>" class="wikipress-wiki-form card shadow-sm">
            <?php wp_nonce_field( 'wikipress_create_wiki', 'wikipress_create_wiki_nonce' ); ?>
            <input type="hidden" name="wikipress_action" value="create_wiki">
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12">
                        <h2 class="h5 mb-1">
                            <?php esc_html_e( 'Wiki Details', 'wikipress' ); ?>
                        </h2>
                        <p class="text-secondary mb-0">
                            <?php esc_html_e( 'Set the identity, content, and navigation style for this Wiki.', 'wikipress' ); ?>
                        </p>
                    </div>
                    <?php self::floating_text_field( 'name', __( 'Wiki Name', 'wikipress' ), '', true ); ?>
                    <?php self::floating_text_field( 'slug', __( 'Wiki Slug', 'wikipress' ) ); ?>
                    <div class="col-md-6">
                        <fieldset class="border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6 mb-0">
                                <?php esc_html_e( 'Wiki Categories', 'wikipress' ); ?>
                            </legend>
                            <?php echo FormFieldHelper::bootstrap_multiselect( 'wikipress_wiki[categories][]', [ 'data' => self::category_options( $categories ), 'class' => 'show-tick', 'icons_base' => 'fa-solid', 'tick_icon' => 'fa-check', 'live_search' => true, 'open_options' => true, 'placeholder' => __( 'Select or create categories', 'wikipress' ), 'live_search_placeholder' => __( 'Search or create categories', 'wikipress' ), 'attributes' => [ 'id' => 'wikipress-wiki-categories', 'data-wikipress-category-select' => 'true', 'data-wikipress-taxonomy-endpoint' => rest_url( 'wp/v2/' . Taxonomy::CATEGORY ), 'data-wikipress-taxonomy-create' => 'true', 'data-wikipress-rest-nonce' => wp_create_nonce( 'wp_rest' ) ] ] ); ?>
                            <div class="form-text">
                                <?php esc_html_e( 'Search existing categories or create a new category from the picker.', 'wikipress' ); ?>
                            </div>
                        </fieldset>
                    </div>
                    <div class="col-md-6">
                        <fieldset class="border rounded p-3">
                            <legend class="float-none w-auto px-2 fs-6 mb-0">
                                <?php esc_html_e( 'Wiki Tags', 'wikipress' ); ?>
                            </legend>
                            <?php echo FormFieldHelper::bootstrap_multiselect( 'wikipress_wiki[tags][]', [ 'data' => array_map( static fn( $tag ) => [ 'value' => (string) $tag->term_id, 'label' => $tag->name ], $tags ), 'class' => 'show-tick', 'icons_base' => 'fa-solid', 'tick_icon' => 'fa-check', 'open_options' => true, 'placeholder' => __( 'Search or select tags', 'wikipress' ), 'live_search_placeholder' => __( 'Search or create tags', 'wikipress' ), 'attributes' => [ 'id' => 'wikipress-wiki-tags', 'data-wikipress-taxonomy-endpoint' => rest_url( 'wp/v2/' . Taxonomy::TAG ), 'data-wikipress-taxonomy-create' => 'true', 'data-wikipress-rest-nonce' => wp_create_nonce( 'wp_rest' ) ] ] ); ?>
                            <div class="form-text">
                                <?php esc_html_e( 'Search existing tags or create a new tag from the picker.', 'wikipress' ); ?>
                            </div>
                        </fieldset>
                    </div>
                <div class="col-12">
                    <?php FormFieldHelper::tinymce( 'wikipress-wiki-excerpt', 'wikipress_wiki[excerpt]', __( 'Wiki Excerpt', 'wikipress' ), '', 6 ); ?>
                </div>
                <div class="col-12">
                    <?php //FormFieldHelper::tinymce( 'wikipress-wiki-description', 'wikipress_wiki[description]', __( 'Wiki Description', 'wikipress' ), '', 10 ); ?>
                </div>
                <div class="col-md-6">
                    <?php self::render_media_field( 'thumbnail_id', __( 'Wiki Header Image', 'wikipress' ), __( 'Select the featured image displayed for this Wiki.', 'wikipress' ) ); ?>
                </div>
                <div class="col-md-6">
                    <?php self::render_media_field( 'logo_id', __( 'Wiki Logo', 'wikipress' ), __( 'Select an optional logo for this Wiki.', 'wikipress' ) ); ?>
                </div>
                <div class="col-12">
                    <fieldset>
                        <legend class="form-label">
                            <?php esc_html_e( 'Wiki Navigation Style', 'wikipress' ); ?>
                        </legend>
                        <div class="d-flex flex-wrap gap-3">
                            <label class="form-check">
                                <input class="form-check-input" type="radio" name="wikipress_wiki[navigation]" value="horizontal" checked> 
                                <?php esc_html_e( 'Horizontal - Along the top', 'wikipress' ); ?>
                            </label>
                            <label class="form-check">
                                <input class="form-check-input" type="radio" name="wikipress_wiki[navigation]" value="vertical"> 
                                <?php esc_html_e( 'Vertical - Sidebar', 'wikipress' ); ?>
                            </label>
                        </div>
                    </fieldset>
                </div>
                <?php if ( $fields ) : ?><div class="col-12"><h2 class="h5"><?php esc_html_e( 'Wiki Plugin Fields', 'wikipress' ); ?></h2><?php echo $fields; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php endif; ?>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-end gap-2"><a class="btn btn-outline-secondary" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-manage' ) ); ?>"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></a><button class="btn btn-primary" type="submit"><?php esc_html_e( 'Create Wiki', 'wikipress' ); ?></button></div>
        </form>
        <?php
    }

    private static function floating_text_field( string $key, string $label, string $value = '', bool $required = false ): void {
        $id = 'wikipress-wiki-' . $key;
        printf( '<div class="col-md-6"><div class="form-floating"><input class="form-control" id="%1$s" name="wikipress_wiki[%2$s]" type="text" value="%3$s" placeholder="%4$s"%5$s><label for="%1$s">%4$s</label></div></div>', esc_attr( $id ), esc_attr( $key ), esc_attr( $value ), esc_attr( $label ), $required ? ' required' : '' );
    }

    private static function category_options( array $categories, int $parent = 0, int $level = 0 ): array {
        $options = [];

        foreach ( $categories as $category ) {
            if ( (int) $category->parent !== $parent ) {
                continue;
            }

            $options[] = [
                'value' => (string) $category->term_id,
                'label' => ( $level > 0 ? str_repeat( '-- ', $level ) : '' ) . $category->name,
            ];
            $options = array_merge( $options, self::category_options( $categories, (int) $category->term_id, $level + 1 ) );
        }

        return $options;
    }

    private static function render_media_field( string $key, string $label, string $description ): void {
        $id = 'wikipress-wiki-' . $key;
        ?>
        <div class="wikipress-media-field"><label class="form-label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label><input class="form-control" id="<?php echo esc_attr( $id ); ?>" name="wikipress_wiki[<?php echo esc_attr( $key ); ?>]" type="hidden" value=""><div class="d-flex align-items-center gap-2"><button class="btn btn-outline-secondary" type="button" data-wikipress-media-picker data-media-target="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Choose Image', 'wikipress' ); ?></button><button class="btn btn-link text-danger d-none" type="button" data-wikipress-media-clear data-media-target="<?php echo esc_attr( $id ); ?>"><?php esc_html_e( 'Clear', 'wikipress' ); ?></button></div><div class="form-text"><?php echo esc_html( $description ); ?></div><div class="mt-2" data-media-preview="<?php echo esc_attr( $id ); ?>"></div></div>
        <?php
    }

    public static function getWikiForm( $wikiId = null ): array {
        $form = [
            'title' => __( 'Wiki Form', 'wikipress' ),
            'fields' => [
                'title' => [
                    'label' => __( 'Title', 'wikipress' ),
                    'type' => 'text',
                    'required' => true,
                ],
                'content' => [
                    'label' => __( 'Content', 'wikipress' ),
                    'type' => 'textarea',
                    'required' => true,
                ],
                // Add more fields as needed
            ],
        ];

        $form['submit_label'] = $wikiId ? __( 'Update Wiki', 'wikipress' ) : __( 'Create Wiki', 'wikipress' );

        return $form;
    }

    public static function render_modals( \WP_Post $wiki ): void {
        $settings_id = 'wikipress-wiki-settings-' . $wiki->ID;
        $manage_id = 'wikipress-wiki-manage-' . $wiki->ID;
        ?>
        <div class="modal fade" id="<?php echo esc_attr( $settings_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $settings_id ); ?>-title" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $settings_id ); ?>-title"><?php /* translators: %s is the Wiki title. */ printf( esc_html__( '%s Settings', 'wikipress' ), esc_html( get_the_title( $wiki ) ) ); ?></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'wikipress' ); ?>"></button></div>
                    <div class="modal-body">
                        <div class="accordion" id="<?php echo esc_attr( $settings_id ); ?>-accordion">
                            <div class="accordion-item">
                                <h3 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $settings_id ); ?>-details" aria-expanded="true"><?php esc_html_e( 'Wiki Details', 'wikipress' ); ?></button></h3>
                                <div id="<?php echo esc_attr( $settings_id ); ?>-details" class="accordion-collapse collapse show"><div class="accordion-body"><div class="row g-3">
                                    <?php self::text_field( $wiki->ID, 'name', __( 'Wiki Name', 'wikipress' ), $wiki->post_title ); ?>
                                    <?php self::text_field( $wiki->ID, 'slug', __( 'Wiki Slug', 'wikipress' ), $wiki->post_name ); ?>
                                    <?php self::text_field( $wiki->ID, 'navigation', __( 'Navigation Style', 'wikipress' ), get_post_meta( $wiki->ID, '_wikipress_navigation_style', true ) ?: 'horizontal' ); ?>
                                    <div class="col-12"><p class="text-secondary mb-0"><?php esc_html_e( 'The full Wiki editor is available from the Manage Wiki tabs. Save this panel to persist Wiki-level settings.', 'wikipress' ); ?></p></div>
                                </div></div></div>
                            </div>
                            <div class="accordion-item">
                                <h3 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr( $settings_id ); ?>-global"><?php esc_html_e( 'Global WikiPress Settings', 'wikipress' ); ?></button></h3>
                                <div id="<?php echo esc_attr( $settings_id ); ?>-global" class="accordion-collapse collapse"><div class="accordion-body">
                                    <?php foreach ( [ 'permalink' => __( 'Permalink', 'wikipress' ), 'search' => __( 'Wiki Search', 'wikipress' ), 'page' => __( 'Wiki Page', 'wikipress' ) ] as $key => $label ) : ?>
                                        <div class="row align-items-center g-2 border-bottom py-3"><div class="col"><strong><?php echo esc_html( $label ); ?></strong><div class="form-text"><?php esc_html_e( 'Use the global WikiPress value unless this Wiki needs an override.', 'wikipress' ); ?></div></div><div class="col-auto"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="<?php echo esc_attr( $settings_id . '-' . $key . '-global' ); ?>" name="wikipress_wiki_settings[<?php echo esc_attr( $key ); ?>][use_global]" value="1" checked data-wikipress-use-global><label class="form-check-label" for="<?php echo esc_attr( $settings_id . '-' . $key . '-global' ); ?>"><?php esc_html_e( 'Use Global', 'wikipress' ); ?></label></div></div><div class="col-12"><input class="form-control" name="wikipress_wiki_settings[<?php echo esc_attr( $key ); ?>][value]" type="text" disabled data-wikipress-global-value></div></div>
                                    <?php endforeach; ?>
                                </div></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></button><button type="button" class="btn btn-primary" data-wikipress-save-wiki-settings="<?php echo esc_attr( $wiki->ID ); ?>"><?php esc_html_e( 'Save', 'wikipress' ); ?></button></div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="<?php echo esc_attr( $manage_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $manage_id ); ?>-title" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
                <div class="modal-header"><h2 class="modal-title h5" id="<?php echo esc_attr( $manage_id ); ?>-title"><?php /* translators: %s is the wiki title. */ printf( esc_html__( 'Manage %s', 'wikipress' ), esc_html( get_the_title( $wiki ) ) ); ?></h2><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'wikipress' ); ?>"></button></div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" role="tablist"><?php foreach ( [ 'pages' => __( 'Pages', 'wikipress' ), 'categories' => __( 'Categories', 'wikipress' ), 'tags' => __( 'Tags', 'wikipress' ), 'navigation' => __( 'Navigation', 'wikipress' ) ] as $tab => $label ) : ?><li class="nav-item" role="presentation"><button class="nav-link <?php echo 'pages' === $tab ? 'active' : ''; ?>" type="button" data-bs-toggle="tab" data-bs-target="#<?php echo esc_attr( $manage_id . '-' . $tab ); ?>" role="tab"><?php echo esc_html( $label ); ?></button></li><?php endforeach; ?></ul>
                    <div class="tab-content pt-3">
                        <div class="tab-pane fade show active" id="<?php echo esc_attr( $manage_id ); ?>-pages" role="tabpanel"><?php self::render_pages( $wiki ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-categories" role="tabpanel"><?php self::render_terms( $wiki, Taxonomy::CATEGORY, __( 'Category', 'wikipress' ) ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-tags" role="tabpanel"><?php self::render_terms( $wiki, Taxonomy::TAG, __( 'Tag', 'wikipress' ) ); ?></div>
                        <div class="tab-pane fade" id="<?php echo esc_attr( $manage_id ); ?>-navigation" role="tabpanel"><div class="alert alert-secondary"><?php esc_html_e( 'Add Wiki Pages, Categories, Tags, and custom links to build the navigation menu.', 'wikipress' ); ?></div><div class="border rounded p-3" data-wikipress-menu-builder><div class="fw-semibold mb-2"><?php esc_html_e( 'Wiki Navigation', 'wikipress' ); ?></div><ol class="list-group list-group-numbered mb-3" data-wikipress-nav-items></ol><div class="d-flex flex-wrap gap-2"><button type="button" class="btn btn-sm btn-outline-secondary" data-wikipress-nav-add="pages" data-wikipress-nav-add-label="<?php esc_attr_e( 'Wiki Pages', 'wikipress' ); ?>"><?php esc_html_e( 'Add Wiki Pages', 'wikipress' ); ?></button><button type="button" class="btn btn-sm btn-outline-secondary" data-wikipress-nav-add="categories" data-wikipress-nav-add-label="<?php esc_attr_e( 'Wiki Categories', 'wikipress' ); ?>"><?php esc_html_e( 'Add Wiki Categories', 'wikipress' ); ?></button><button type="button" class="btn btn-sm btn-outline-secondary" data-wikipress-nav-add="tags" data-wikipress-nav-add-label="<?php esc_attr_e( 'Wiki Tags', 'wikipress' ); ?>"><?php esc_html_e( 'Add Wiki Tags', 'wikipress' ); ?></button><button type="button" class="btn btn-sm btn-outline-secondary" data-wikipress-nav-add="custom" data-wikipress-nav-add-label="<?php esc_attr_e( 'Wiki Custom', 'wikipress' ); ?>"><?php esc_html_e( 'Add Custom Link', 'wikipress' ); ?></button></div></div></div>
                    </div>
                </div>
            </div></div>
        </div>
        <?php
    }

    private static function text_field( int $wiki_id, string $key, string $label, string $value ): void {
        $id = 'wikipress-wiki-' . $wiki_id . '-' . $key;
        echo '<div class="col-md-6"><label class="form-label" for="' . esc_attr( $id ) . '">' . esc_html( $label ) . '</label><input class="form-control" id="' . esc_attr( $id ) . '" name="wikipress_wiki_settings[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '"></div>';
    }

    private static function render_pages( \WP_Post $wiki ): void {
        $pages = QueryHelper::posts( [ 'post_type' => PostType::PAGE, 'post_status' => 'any', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'meta_key' => '_wikipress_wiki_id', 'meta_value' => $wiki->ID ] )->posts;
        echo '<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h6 mb-0">' . esc_html__( 'Wiki Pages', 'wikipress' ) . '</h3><a class="btn btn-primary btn-sm" href="' . esc_url( admin_url( 'admin.php?page=wikipress-manage&wiki=page-new&wiki_id=' . $wiki->ID ) ) . '">' . esc_html__( 'Add New Page', 'wikipress' ) . '</a></div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>' . esc_html__( 'Title', 'wikipress' ) . '</th><th>' . esc_html__( 'Status', 'wikipress' ) . '</th><th class="text-end">' . esc_html__( 'Actions', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $pages as $page ) {
            echo '<tr><td>' . esc_html( get_the_title( $page ) ) . '</td><td>' . esc_html( ucfirst( $page->post_status ) ) . '</td><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="' . esc_url( admin_url( 'admin.php?page=wikipress-manage&wiki=page-edit&wiki_id=' . $wiki->ID . '&page_id=' . $page->ID ) ) . '">' . esc_html__( 'Edit', 'wikipress' ) . '</a> <button type="button" class="btn btn-sm btn-outline-danger" data-wikipress-delete-page="' . esc_attr( $page->ID ) . '">' . esc_html__( 'Delete', 'wikipress' ) . '</button></td></tr>';
        }
        if ( ! $pages ) echo '<tr><td colspan="3" class="text-secondary">' . esc_html__( 'No Wiki Pages have been created yet.', 'wikipress' ) . '</td></tr>';
        echo '</tbody></table></div>';
    }

    private static function render_terms( \WP_Post $wiki, string $taxonomy, string $label ): void {
        $terms = TaxonomyHelper::terms( $taxonomy, $wiki->ID );
        echo '<div class="d-flex justify-content-between align-items-center mb-3"><h3 class="h6 mb-0">' . esc_html( $label . 's' ) . '</h3><button type="button" class="btn btn-primary btn-sm" data-wikipress-add-term="' . esc_attr( $taxonomy ) . '">' . esc_html__( 'Add New', 'wikipress' ) . '</button></div><div class="table-responsive"><table class="table align-middle"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Slug', 'wikipress' ) . '</th><th class="text-end">' . esc_html__( 'Actions', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $terms as $term ) echo '<tr><td>' . esc_html( $term->name ) . '</td><td>' . esc_html( $term->slug ) . '</td><td class="text-end"><button type="button" class="btn btn-sm btn-outline-secondary" data-wikipress-edit-term="' . esc_attr( $term->term_id ) . '" data-wikipress-term-name="' . esc_attr( $term->name ) . '" data-wikipress-term-slug="' . esc_attr( $term->slug ) . '" data-wikipress-taxonomy="' . esc_attr( $taxonomy ) . '">' . esc_html__( 'Edit', 'wikipress' ) . '</button> <button type="button" class="btn btn-sm btn-outline-danger" data-wikipress-delete-term="' . esc_attr( $term->term_id ) . '" data-wikipress-taxonomy="' . esc_attr( $taxonomy ) . '">' . esc_html__( 'Delete', 'wikipress' ) . '</button></td></tr>';
        if ( ! $terms ) echo '<tr><td colspan="3" class="text-secondary">' . esc_html__( 'No terms are assigned to this Wiki yet.', 'wikipress' ) . '</td></tr>';
        echo '</tbody></table></div><div class="collapse mt-3" data-wikipress-term-form data-wikipress-taxonomy="' . esc_attr( $taxonomy ) . '"><div class="border rounded p-3"><div class="row g-3"><div class="col-md-6"><label class="form-label">' . esc_html( $label . ' ' . __( 'Name', 'wikipress' ) ) . '</label><input class="form-control" type="text" data-wikipress-term-name></div><div class="col-md-6"><label class="form-label">' . esc_html__( 'Slug', 'wikipress' ) . '</label><input class="form-control" type="text" data-wikipress-term-slug></div><div class="col-12"><label class="form-label">' . esc_html__( 'Description', 'wikipress' ) . '</label><textarea class="form-control" rows="3" data-wikipress-term-description></textarea></div></div><div class="d-flex justify-content-end gap-2 mt-3"><button type="button" class="btn btn-outline-secondary" data-wikipress-cancel-term>' . esc_html__( 'Cancel', 'wikipress' ) . '</button><button type="button" class="btn btn-primary" data-wikipress-save-term>' . esc_html__( 'Save', 'wikipress' ) . '</button></div></div></div>';
    }
}