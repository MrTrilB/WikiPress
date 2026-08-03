<?php

namespace TrilBDev\WikiPress\Admin\Manager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

abstract class PageManager {
    protected function header( string $title ): void {
        echo '<div class="wrap wikipress-admin"><div class="container-fluid px-0 py-3"><h1 class="display-6 mb-4">' . esc_html( $title ) . '</h1>';
    }

    protected function footer(): void {
        echo '</div></div>';
    }

    protected function card( string $label, $value, string $slug ): void {
        printf(
            '<div class="col-md-6 col-xl-3 mb-4"><div class="card h-100 shadow-sm"><div class="card-body"><h2 class="h6 text-muted">%s</h2><p class="display-6 mb-0"><a class="text-decoration-none" href="%s">%s</a></p></div></div></div>',
            esc_html( $label ),
            esc_url( admin_url( 'admin.php?page=' . $slug ) ),
            esc_html( (string) $value )
        );
    }

    protected function render_post_table( string $post_type, string $title ): void {
        $this->header( $title );
        $this->render_post_table_body( $post_type );
        $this->footer();
    }

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
}
