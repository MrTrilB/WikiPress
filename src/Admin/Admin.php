<?php

namespace TrilBDev\WikiPress\Admin;

use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Admin {
    public function register_admin_menu(): void {
        add_menu_page( __( 'WikiPress', 'wikipress' ), __( 'WikiPress', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ], 'dashicons-book-alt', 30 );
        add_submenu_page( 'wikipress', __( 'Dashboard', 'wikipress' ), __( 'Dashboard', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ] );
        add_submenu_page( 'wikipress', __( 'All Wikis', 'wikipress' ), __( 'All Wikis', 'wikipress' ), 'manage_options', 'wikipress-wikis', [ $this, 'render_wikis' ] );
        add_submenu_page( 'wikipress', __( 'All Wiki Pages', 'wikipress' ), __( 'All Wiki Pages', 'wikipress' ), 'edit_posts', 'wikipress-pages', [ $this, 'render_pages' ] );
        add_submenu_page( 'wikipress', __( 'Categories', 'wikipress' ), __( 'Categories', 'wikipress' ), 'manage_categories', 'wikipress-categories', [ $this, 'render_categories' ] );
        add_submenu_page( 'wikipress', __( 'Tags', 'wikipress' ), __( 'Tags', 'wikipress' ), 'manage_categories', 'wikipress-tags', [ $this, 'render_tags' ] );
        add_submenu_page( 'wikipress', __( 'Settings', 'wikipress' ), __( 'Settings', 'wikipress' ), 'manage_options', 'wikipress-settings', [ $this, 'render_settings' ] );
        add_submenu_page( 'wikipress', __( 'Analytics', 'wikipress' ), __( 'Analytics', 'wikipress' ), 'manage_options', 'wikipress-analytics', [ $this, 'render_analytics' ] );
    }

    public function register_settings(): void {
        register_setting( 'wikipress_settings', 'wikipress_general', [ 'sanitize_callback' => [ $this, 'sanitize_general' ] ] );
    }

    public function sanitize_general( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( [ 'root_name', 'root_slug', 'category_slug', 'tag_slug', 'permalink' ] as $key ) {
            $input[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
            Settings::set( $key, $input[ $key ] );
        }
        return $input;
    }

    public function render_dashboard(): void {
        $this->header( __( 'Dashboard', 'wikipress' ) );
        $this->card( __( 'Wikis', 'wikipress' ), wp_count_posts( 'wikipress_wiki' )->publish ?? 0, 'wikipress-wikis' );
        $this->card( __( 'Wiki Pages', 'wikipress' ), wp_count_posts( 'wikipress_page' )->publish ?? 0, 'wikipress-pages' );
        $this->card( __( 'Categories', 'wikipress' ), wp_count_terms( 'wikipress_category' ), 'wikipress-categories' );
        $this->card( __( 'Tags', 'wikipress' ), wp_count_terms( 'wikipress_tag' ), 'wikipress-tags' );
        $this->footer();
    }

    public function render_wikis(): void { $this->render_post_table( 'wikipress_wiki', __( 'All Wikis', 'wikipress' ) ); }
    public function render_pages(): void { $this->render_post_table( 'wikipress_page', __( 'All Wiki Pages', 'wikipress' ) ); }
    public function render_categories(): void { $this->render_term_table( 'wikipress_category', __( 'Categories', 'wikipress' ) ); }
    public function render_tags(): void { $this->render_term_table( 'wikipress_tag', __( 'Tags', 'wikipress' ) ); }

    public function render_settings(): void {
        $values = Settings::get_all()['general'] ?? [];
        $this->header( __( 'Settings', 'wikipress' ) );
        ?>
        <form method="post" action="options.php" class="wikipress-settings-form">
            <?php settings_fields( 'wikipress_settings' ); ?>
            <table class="form-table"><tbody>
            <?php foreach ( [ 'root_name' => 'WikiPress Root Name', 'root_slug' => 'WikiPress Root Slug', 'category_slug' => 'Custom Category Slug', 'tag_slug' => 'Custom Tags Slug', 'permalink' => 'WikiPress Permalink' ] as $key => $label ) : ?>
                <tr><th scope="row"><label for="wikipress-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input class="regular-text" id="wikipress-<?php echo esc_attr( $key ); ?>" name="wikipress_general[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"></td></tr>
            <?php endforeach; ?>
            </tbody></table>
            <?php submit_button(); ?>
        </form>
        <?php $this->footer();
    }

    public function render_analytics(): void {
        $this->header( __( 'Analytics', 'wikipress' ) );
        echo '<p>' . esc_html__( 'Analytics will be expanded as view tracking is introduced.', 'wikipress' ) . '</p>';
        $this->footer();
    }

    private function render_post_table( string $post_type, string $title ): void {
        $query = new \WP_Query( [ 'post_type' => $post_type, 'posts_per_page' => 20, 'post_status' => 'any' ] );
        $this->header( $title );
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Author', 'wikipress' ) . '</th><th>' . esc_html__( 'Created', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( $query->posts as $post ) {
            printf( '<tr><td>%s</td><td>%s</td><td>%s</td></tr>', esc_html( get_the_title( $post ) ), esc_html( get_the_author_meta( 'display_name', $post->post_author ) ), esc_html( get_the_date( '', $post ) ) );
        }
        echo '</tbody></table>';
        $this->footer();
    }

    private function render_term_table( string $taxonomy, string $title ): void {
        $this->header( $title );
        echo '<table class="widefat striped"><thead><tr><th>' . esc_html__( 'Name', 'wikipress' ) . '</th><th>' . esc_html__( 'Slug', 'wikipress' ) . '</th><th>' . esc_html__( 'Post Count', 'wikipress' ) . '</th></tr></thead><tbody>';
        foreach ( get_terms( [ 'taxonomy' => $taxonomy, 'hide_empty' => false ] ) as $term ) {
            printf( '<tr><td>%s</td><td>%s</td><td>%d</td></tr>', esc_html( $term->name ), esc_html( $term->slug ), absint( $term->count ) );
        }
        echo '</tbody></table>';
        $this->footer();
    }

    private function header( string $title ): void { echo '<div class="wrap"><h1>' . esc_html( $title ) . '</h1>'; }
    private function footer(): void { echo '</div>'; }
    private function card( string $label, $value, string $slug ): void { printf( '<div class="wikipress-card"><h2>%s</h2><p><a href="%s">%s</a></p></div>', esc_html( $label ), esc_url( admin_url( 'admin.php?page=' . $slug ) ), esc_html( (string) $value ) ); }
}
