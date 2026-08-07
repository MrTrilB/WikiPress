<?php
/**
 * Admin class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin
 * @since 1.0.0
 * 
 */
namespace TrilBDev\WikiPress\Admin;

use TrilBDev\WikiPress\Includes\Tools\DataTransfer;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Functions\Helpers\PermalinkHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\AjaxHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Admin\Manager\Analytics\AnalyticsManager;
use TrilBDev\WikiPress\Admin\Manager\Dashboard\DashboardManager;
use TrilBDev\WikiPress\Admin\Manager\Settings\SettingsManager;
use TrilBDev\WikiPress\Admin\Manager\Content\ContentManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Admin {
    /**
     * Singleton instance of the Admin class.
     *
     * @var self|null
     */
    private DashboardManager $dashboard_manager;
    /**
     * ContentManager instance for managing content-related admin pages.
     *
     * @var ContentManager
     */
    private ContentManager $content_manager;
    /**
     * SettingsManager instance for managing settings-related admin pages.
     *
     * @var SettingsManager
     */
    private SettingsManager $settings_manager;
    /**
     * AnalyticsManager instance for managing analytics-related admin pages.
     *
     * @var AnalyticsManager
     */
    private AnalyticsManager $analytics_manager;
    /**
     * LoaderHelper instance for managing action and filter hooks.
     *
     * @var LoaderHelper
     */
    private LoaderHelper $loader;

    public function __construct( Assets $assets ) {
        $this->dashboard_manager = new DashboardManager();
        $this->content_manager = new ContentManager();
        $this->settings_manager = new SettingsManager();
        $this->analytics_manager = new AnalyticsManager();
        $this->loader = new LoaderHelper();
        $this->dashboard_manager->register_assets( $assets );
        $this->content_manager->register_assets( $assets );
        $this->settings_manager->register_assets( $assets );
        $this->analytics_manager->register_assets( $assets );
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_load_settings_tab', 'callback' => 'load_settings_tab' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_toggle_plugin', 'callback' => 'toggle_plugin' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_save_plugin_settings', 'callback' => 'save_plugin_settings' ],
        ] )->run();
    }
    /**
     * Register admin menu pages and subpages.
     * @since 1.0.0
     */
    public function register_admin_menu(): void {
        $manager_capability = $this->capability( 'manager_wiki', 'manage_options' );
        add_menu_page( __( 'WikiPress', 'wikipress' ), __( 'WikiPress', 'wikipress' ), $manager_capability, 'wikipress', [ $this, 'render_dashboard' ], 'dashicons-book-alt', 30 );
        add_submenu_page( 'wikipress', __( 'Dashboard', 'wikipress' ), __( 'Dashboard', 'wikipress' ), 'manage_options', 'wikipress', [ $this, 'render_dashboard' ] );
        add_submenu_page( 'wikipress', __( 'Manage Wiki', 'wikipress' ), __( 'Manage Wiki', 'wikipress' ), $this->capability( 'manager_wiki', 'manage_options' ), 'wikipress-manage', [ $this, 'render_wikis' ] );
        add_submenu_page( 'wikipress', __( 'Settings', 'wikipress' ), __( 'Settings', 'wikipress' ), 'manage_options', 'wikipress-settings', [ $this, 'render_settings' ] );
        add_submenu_page( 'wikipress', __( 'Analytics', 'wikipress' ), __( 'Analytics', 'wikipress' ), $this->capability( 'view_analytics', 'manage_options' ), 'wikipress-analytics', [ $this, 'render_analytics' ] );
    }
    /**
     * Register settings for the WikiPress plugin.
     *
     * This method registers the settings for the WikiPress plugin, including general, layout, access, and tools settings.
     * It also registers settings for active WikiPress plugins that provide their own settings pages.
     */
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
    /**
     * Sanitize general settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_general( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $rewrite_changed = false;
        foreach ( [ 'root_name', 'root_description', 'archive_title', 'archive_description', 'root_slug', 'category_slug', 'tag_slug', 'permalink', 'enable_schema' ] as $key ) {
            $value = in_array( $key, [ 'root_slug', 'category_slug', 'tag_slug' ], true ) ? sanitize_title( $input[ $key ] ?? '' ) : ( 'permalink' === $key ? PermalinkHelper::sanitize_pattern( $input[ $key ] ?? '' ) : ( 'enable_schema' === $key ? ! empty( $input[ $key ] ) : sanitize_textarea_field( $input[ $key ] ?? '' ) ) );
            $rewrite_changed = $rewrite_changed || $value !== (string) Settings::get( $key, '' );
            $input[ $key ] = $value;
            Settings::set( $key, $input[ $key ] );
        }
        if ( $rewrite_changed ) {
            flush_rewrite_rules();
        }
        return $input;
    }
    /**
     * Sanitize layout settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_layout( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $section = sanitize_key( $input['layout_section'] ?? 'general' );
        unset( $input['layout_section'] );
        $section_keys = [
            'general' => [ 'show_search', 'show_breadcrumbs', 'show_sidebar' ],
            'search' => [ 'show_search', 'search_placeholder', 'search_button_text', 'search_scope', 'search_no_results_message', 'search_results_count', 'search_min_chars', 'search_live_results' ],
            'sidebar' => [ 'show_sidebar', 'sidebar_position', 'sidebar_width', 'sidebar_sticky', 'sidebar_show_categories', 'sidebar_show_category_count', 'sidebar_expand_categories', 'sidebar_show_page_count' ],
            'page' => [ 'page_show_title', 'show_breadcrumbs', 'page_show_toc', 'page_toc_position', 'toc_min_level', 'toc_max_level', 'show_last_updated', 'show_author', 'show_reading_time', 'reading_time_wpm', 'show_feedback', 'page_show_navigation', 'show_related_pages', 'related_pages_count' ],
        ];
        $active_keys = $section_keys[ $section ] ?? array_merge( ...array_values( $section_keys ) );
        foreach ( [
            'show_search', 'show_toc', 'show_breadcrumbs', 'show_last_updated', 'show_author', 'show_reading_time',
            'show_feedback', 'show_related_pages', 'search_live_results', 'show_sidebar', 'sidebar_sticky',
            'sidebar_show_categories', 'sidebar_show_category_count', 'sidebar_expand_categories', 'sidebar_show_page_count',
            'page_show_title', 'page_show_toc', 'page_show_navigation',
        ] as $key ) {
            if ( ! in_array( $key, $active_keys, true ) ) {
                continue;
            }
            $value = ! empty( $input[ $key ] );
            $input[ $key ] = $value;
            Settings::set( $key, $value );
        }
        foreach ( [ 'search_placeholder', 'search_button_text', 'search_no_results_message' ] as $key ) {
            if ( ! in_array( $key, $active_keys, true ) ) {
                continue;
            }
            $input[ $key ] = sanitize_text_field( $input[ $key ] ?? '' );
            Settings::set( $key, $input[ $key ] );
        }
        if ( in_array( 'search_scope', $active_keys, true ) ) {
            $input['search_scope'] = in_array( $input['search_scope'] ?? '', [ 'all', 'title', 'content' ], true ) ? $input['search_scope'] : 'all';
            Settings::set( 'search_scope', $input['search_scope'] );
        }
        if ( in_array( 'sidebar_position', $active_keys, true ) ) {
            $input['sidebar_position'] = in_array( $input['sidebar_position'] ?? '', [ 'left', 'right' ], true ) ? $input['sidebar_position'] : 'left';
            Settings::set( 'sidebar_position', $input['sidebar_position'] );
        }
        if ( in_array( 'page_toc_position', $active_keys, true ) ) {
            $input['page_toc_position'] = in_array( $input['page_toc_position'] ?? '', [ 'sidebar', 'content' ], true ) ? $input['page_toc_position'] : 'sidebar';
            Settings::set( 'page_toc_position', $input['page_toc_position'] );
        }
        foreach ( [ 'related_pages_count' => [ 1, 12 ], 'search_results_count' => [ 1, 50 ], 'search_min_chars' => [ 1, 5 ], 'sidebar_width' => [ 180, 480 ], 'toc_min_level' => [ 1, 5 ], 'toc_max_level' => [ 2, 6 ], 'reading_time_wpm' => [ 100, 400 ] ] as $key => [ $minimum, $maximum ] ) {
            if ( ! in_array( $key, $active_keys, true ) ) {
                continue;
            }
            $input[ $key ] = max( $minimum, min( $maximum, absint( $input[ $key ] ?? $minimum ) ) );
            Settings::set( $key, $input[ $key ] );
        }
        return $input;
    }
    /**
     * Sanitize access settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_access( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( [ 'create_wikis', 'write_pages', 'view_analytics', 'manage_plugins' ] as $key ) {
            $input[ $key ] = sanitize_key( $input[ $key ] ?? 'manage_options' );
            Settings::set( $key, $input[ $key ] );
        }
        return $input;
    }
    /**
     * Sanitize tools settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_tools( $input ): array {
        $input = is_array( $input ) ? $input : [];
        foreach ( [ 'debug_logging', 'console_logging' ] as $key ) {
            $input[ $key ] = ! empty( $input[ $key ] );
            Settings::set( $key, $input[ $key ] );
        }
        return $input;
    }
    /**
     * Render the dashboard page.
     *
     * This method is responsible for rendering the dashboard page of the WikiPress plugin.
     * It delegates the rendering to the DashboardManager instance.
     */
    public function render_dashboard(): void {
        $this->dashboard_manager->render();
    }
    /**
     * Render the manage wikis page.
     *
     * This method is responsible for rendering the manage wikis page of the WikiPress plugin.
     * It delegates the rendering to the ContentManager instance.
     */
    public function render_wikis(): void {
        $this->content_manager->render();
    }
    /**
     * Render the settings page.
     *
     * This method is responsible for rendering the settings page of the WikiPress plugin.
     * It delegates the rendering to the SettingsManager instance.
     */
    public function render_settings(): void {
        $this->settings_manager->render();
    }
    /**
     * Render the analytics page.
     *
     * This method is responsible for rendering the analytics page of the WikiPress plugin.
     * It delegates the rendering to the AnalyticsManager instance.
     */
    public function load_settings_tab(): void {
        if ( ! AjaxHelper::authorized( 'wikipress_settings_tabs', 'manage_options' ) ) {
            AjaxHelper::unauthorized( __( 'You are not authorized to load WikiPress settings.', 'wikipress' ) );
        }

        $tab = sanitize_key( $_POST['tab'] ?? 'general' );
        $layout_section = sanitize_key( $_POST['layout_section'] ?? 'general' );
        ob_start();
        $this->settings_manager->render_tab_content( $tab, $layout_section );
        $html = (string) ob_get_clean();
        AjaxHelper::success( [ 'html' => $html, 'tab' => $tab, 'layout_section' => $layout_section ] );
    }
    /**
     * Toggle the enabled state of a WikiPress plugin.
     *
     * This method handles the AJAX request to enable or disable a WikiPress plugin.
     * It checks for authorization, validates the plugin slug, and updates the plugin's enabled state.
     */
    public function toggle_plugin(): void {
        if ( ! AjaxHelper::authorized( 'wikipress_plugin_toggle', 'manage_options' ) ) {
            AjaxHelper::unauthorized( __( 'You are not authorized to manage WikiPress plugins.', 'wikipress' ) );
        }

        $slug = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
        $enabled = ! empty( $_POST['enabled'] );
        $plugin = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;

        if ( ! $plugin instanceof \TrilBDev\WikiPress\Includes\Plugins\PluginInterface ) {
            AjaxHelper::error( [ 'message' => __( 'The requested WikiPress plugin was not found.', 'wikipress' ) ], 404 );
        }

        if ( ! Plugins::get_instance()->set_plugin_enabled( $slug, $enabled ) ) {
            AjaxHelper::error( [ 'message' => __( 'The WikiPress plugin state could not be saved.', 'wikipress' ) ], 500 );
        }

        AjaxHelper::success( [ 'slug' => $slug, 'enabled' => $enabled ] );
    }

    /**
     * Saves settings submitted from a WikiPress plugin modal.
     */
    public function save_plugin_settings(): void {
        if ( ! AjaxHelper::authorized( 'wikipress_plugin_settings', 'manage_options' ) ) {
            AjaxHelper::unauthorized( __( 'You are not authorized to save WikiPress plugin settings.', 'wikipress' ) );
        }

        $slug = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
        $plugin = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;
        if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface ) {
            AjaxHelper::error( [ 'message' => __( 'The requested WikiPress plugin settings were not found.', 'wikipress' ) ], 404 );
        }

        $input = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : [];
        $settings = $plugin->sanitize_settings( $input );

        AjaxHelper::success( [ 'slug' => $slug, 'settings' => $settings ] );
    }
    /**
     * Render the analytics page.
     *
     * This method is responsible for rendering the analytics page of the WikiPress plugin.
     * It delegates the rendering to the AnalyticsManager instance.
     */
    public function render_analytics(): void {
        $this->analytics_manager->render();
    }
    /**
     * Sanitize general settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
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
    /**
     * Sanitize general settings input.
     *
     * @param mixed $input The input settings to sanitize.
     * @return array The sanitized settings.
     */
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
    /**
     * Get the capability for a given key, with a fallback.
     *
     * @param string $key The settings key to retrieve the capability for.
     * @param string $fallback The fallback capability if the key is not set or invalid.
     * @return string The capability associated with the key, or the fallback if not valid.
     */
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
            if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface || ! Plugins::get_instance()->is_plugin_enabled( $plugin->get_slug() ) ) {
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
