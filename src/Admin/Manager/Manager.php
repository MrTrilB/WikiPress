<?php

namespace TrilBDev\WikiPress\Admin\Manager;

use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Admin\Manager\UI\Footer;
use TrilBDev\WikiPress\Admin\Manager\UI\Header;
use TrilBDev\WikiPress\Admin\Manager\UI\Sidebar;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class Manager {
    /**
     * Register one asset bundle for a group of admin pages.
     *
     * @param Assets $assets Asset registry.
     * @param array<int, string> $pages Admin page slugs.
     * @param string $bundle Compiled bundle name.
     * @return void
     */
    protected function register_page_assets( Assets $assets, array $pages, string $bundle ): void {
        foreach ( $pages as $page ) {
            $assets->register_page( $page, $this->assets( $bundle ) );
        }
    }

    /**
     * Build the asset definition for an admin bundle.
     *
     * @param string $bundle Compiled bundle name.
     * @return array<string, array<int, array<string, mixed>>> Asset definition.
     */
    protected function assets( string $bundle ): array {
        return [
            'styles'  => [ [ 'handle' => 'wikipress-admin-' . $bundle, 'src' => WIKIPRESS_URL . 'src/Assets/dist/css/admin.' . $bundle . '.css' ] ],
            'scripts' => [ [ 'handle' => 'wikipress-admin-' . $bundle, 'src' => WIKIPRESS_URL . 'src/Assets/dist/js/admin.' . $bundle . '.js', 'deps' => [ 'wikipress-bootstrap' ], 'in_footer' => true ] ],
        ];
    }

    /**
     * Render the shared admin page header.
     *
     * @param string $title Page title.
     * @return void
     */
    protected function header( string $title ): void {
        echo '<div class="wrap wikipress-admin">';
        Header::render();
        ?>
        <main class="wikipress-admin-main">
            <div class="container-fluid px-3 px-lg-4 py-4">
                <div class="row g-4">
                    <?php Sidebar::render(); ?>
                    <section class="col-12 col-lg-9 col-xl-10" aria-labelledby="wikipress-page-title">
                        <div class="wikipress-page-heading d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                            <div>
                                <p class="text-uppercase small fw-semibold text-primary mb-2"><?php esc_html_e( 'WikiPress workspace', 'wikipress' ); ?></p>
                                <h1 class="h2 mb-0" id="wikipress-page-title"><?php echo esc_html( $title ); ?></h1>
                            </div>
                        </div>
        <?php
    }

    /**
     * Render the shared admin page footer.
     *
     * @return void
     */
    protected function footer(): void {
        Footer::render();
        echo '</div>';
    }

    /**
     * Render a dashboard statistic card.
     *
     * @param string $label Card label.
     * @param mixed $value Card value.
     * @param string $slug Destination admin page slug.
     * @return void
     */
    protected function card( string $label, $value, string $slug ): void {
        printf(
            '<div class="col-md-6 col-xl-3 mb-4"><div class="card wikipress-dashboard-card h-100 shadow-sm"><div class="card-body"><h2 class="h6 text-muted">%s</h2><p class="display-6 mb-0"><a class="text-decoration-none" href="%s">%s</a></p></div></div></div>',
            esc_html( $label ),
            esc_url( admin_url( 'admin.php?page=' . $slug ) ),
            esc_html( (string) $value )
        );
    }

    /**
     * Render a post table page.
     *
     * @param string $post_type Post type to query.
     * @param string $title Page title.
     * @return void
     */
    protected function render_post_table( string $post_type, string $title ): void {
        $this->header( $title );
        $this->render_post_table_body( $post_type );
        $this->footer();
    }

    /**
     * Render the post table body and pagination controls.
     *
     * @param string $post_type Post type to query.
     * @return void
     */
    protected function render_post_table_body( string $post_type ): void {
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

    /**
     * Render a taxonomy term table with its creation form.
     *
     * @param string $taxonomy Taxonomy name.
     * @param string $title Page title.
     * @return void
     */
    public function render_term_table( string $taxonomy, string $title ): void {
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
