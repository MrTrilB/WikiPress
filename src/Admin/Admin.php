<?php

namespace TrilBDev\WikiPress\Admin;

use TrilBDev\WikiPress\API\API;
use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Tools\DataTransfer;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Admin\Manager\Analytics\AnalyticsManager;
use TrilBDev\WikiPress\Admin\Manager\Dashboard\DashboardManager;
use TrilBDev\WikiPress\Admin\Manager\Settings\SettingsManager;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentManager;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentCategories;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentPages;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentTags;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentWikis;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentForms;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Admin {
    private DashboardManager $dashboard_manager;
    private ContentManager $content_manager;
    private SettingsManager $settings_manager;
    private AnalyticsManager $analytics_manager;
    private ContentWikis $wikis_page;
    private ContentPages $pages_page;
    private ContentCategories $categories_page;
    private ContentTags $tags_page;
    private ContentForms $forms_page;

    public function __construct( Assets $assets ) {
        $this->dashboard_manager = new DashboardManager();
        $this->content_manager = new ContentManager();
        $this->settings_manager = new SettingsManager();
        $this->analytics_manager = new AnalyticsManager();
        $this->wikis_page = new ContentWikis();
        $this->pages_page = new ContentPages();
        $this->categories_page = new ContentCategories();
        $this->tags_page = new ContentTags();
        $this->forms_page = new ContentForms();
        $this->dashboard_manager->register_assets( $assets );
        $this->content_manager->register_assets( $assets );
        $this->wikis_page->register_assets( $assets );
        $this->pages_page->register_assets( $assets );
        $this->categories_page->register_assets( $assets );
        $this->tags_page->register_assets( $assets );
        $this->forms_page->register_assets( $assets );
        $this->settings_manager->register_assets( $assets );
        $this->analytics_manager->register_assets( $assets );
    }

    public function register_admin_menu(): void {
        add_menu_page( __( 'WikiPress', 'wikipress' ), __( 'WikiPress', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ], 'dashicons-book-alt', 30 );
        add_submenu_page( 'wikipress', __( 'Dashboard', 'wikipress' ), __( 'Dashboard', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ] );
        add_submenu_page( 'wikipress', __( 'Manage Wiki', 'wikipress' ), __( 'Manage Wiki', 'wikipress' ), $this->capability( 'manager_wiki', 'manage_options' ), 'wikipress-manage', [ $this, 'render_wikis' ] );
        add_submenu_page( 'wikipress', __( 'Settings', 'wikipress' ), __( 'Settings', 'wikipress' ), 'manage_options', 'wikipress-settings', [ $this, 'render_settings' ] );
        add_submenu_page( 'wikipress', __( 'Analytics', 'wikipress' ), __( 'Analytics', 'wikipress' ), $this->capability( 'view_analytics', 'manage_options' ), 'wikipress-analytics', [ $this, 'render_analytics' ] );
    }

    public function register_settings(): void {
        register_setting( 'wikipress_settings', 'wikipress_general', [ 'sanitize_callback' => [ $this, 'sanitize_general' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_layout', [ 'sanitize_callback' => [ $this, 'sanitize_layout' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_access', [ 'sanitize_callback' => [ $this, 'sanitize_access' ] ] );
        register_setting( 'wikipress_settings', 'wikipress_tools', [ 'sanitize_callback' => [ $this, 'sanitize_tools' ] ] );

        foreach ( $this->plugin_settings_pages() as $page ) {
            register_setting(
                'wikipress_settings',
                'wikipress_' . $page['slug'],
                [ 'sanitize_callback' => $page['provider']->sanitize_settings( ... ) ]
            );
        }
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
        $this->dashboard_manager->render();
    }

    public function render_wikis(): void {
        $this->content_manager->render();
    }

    public function render_wiki_listing(): void {
        $this->wikis_page->render();
    }

    /**
     * Render the wiki page listing.
     *
     * @return void
     */
    public function render_pages(): void { $this->pages_page->render(); }

    /**
     * Render the category management page.
     *
     * @return void
     */
    public function render_categories(): void { $this->categories_page->render(); }

    /**
     * Render the tag management page.
     *
     * @return void
     */
    public function render_tags(): void { $this->tags_page->render(); }

    public function render_settings(): void {
        $this->settings_manager->render();
    }

    public function render_analytics(): void {
        $this->analytics_manager->render();
    }

    public function render_add_new(): void {
        $this->forms_page->render_add_new();
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
        $this->forms_page->render_edit();
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

    private function capability( string $key, string $fallback ): string {
        $capability = sanitize_key( (string) Settings::get( $key, $fallback ) );
        return in_array( $capability, [ 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ], true ) ? $capability : $fallback;
    }

    /**
     * Collect settings pages from active WikiPress plugins.
     *
     * @return array<int, array{provider: SettingsPageProviderInterface, slug: string, label: string, title: string, fields: array}>
     */
    private function plugin_settings_pages(): array {
        $pages = [];
        foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
            if ( ! $plugin instanceof SettingsPageProviderInterface || ! $plugin->is_active() ) {
                continue;
            }

            $page = $plugin->get_settings_page();
            if ( empty( $page['slug'] ) || empty( $page['label'] ) || empty( $page['fields'] ) ) {
                continue;
            }

            $page['provider'] = $plugin;
            $pages[] = $page;
        }
        return $pages;
    }
}
