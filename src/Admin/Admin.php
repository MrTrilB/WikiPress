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

use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Functions\Admin\FunctionsPlugins;
use TrilBDev\WikiPress\Includes\Functions\Helpers\AjaxHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Admin\Manager\Tools\ToolsManager;
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
    * ToolsManager instance for managing tools-related admin pages.
     *
    * @var ToolsManager
     */
    private ToolsManager $tools_manager;
    /**
     * LoaderHelper instance for managing action and filter hooks.
     *
     * @var LoaderHelper
     */
    private LoaderHelper $loader;
    /**
     * FunctionsPlugins instance for managing plugin-related admin functions.
     *
     * @var FunctionsPlugins
     */
    private FunctionsPlugins $plugin_functions;

    public function __construct( Assets $assets ) {
        $this->dashboard_manager = new DashboardManager();
        $this->content_manager = new ContentManager();
        $this->settings_manager = new SettingsManager();
        $this->tools_manager = new ToolsManager();
        $this->plugin_functions = new FunctionsPlugins();
        $this->loader = new LoaderHelper();
        $this->dashboard_manager->register_assets( $assets );
        $this->content_manager->register_assets( $assets );
        $this->settings_manager->register_assets( $assets );
        $this->tools_manager->register_assets( $assets );
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_load_settings_tab', 'callback' => 'load_settings_tab' ],
        ] );
        $this->loader->register_component( $this->plugin_functions, [
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
        add_submenu_page( 'wikipress', __( 'Tools', 'wikipress' ), __( 'Tools', 'wikipress' ), $this->capability( 'view_tools', 'manage_options' ), 'wikipress-tools&tool=debug', [ $this, 'render_tools' ] );
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
     * Render the tools page.
     *
     * @return void
     */
    public function render_tools(): void {
        $this->tools_manager->render();
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

}
