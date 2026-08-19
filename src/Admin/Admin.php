<?php
/**
 * Admin class for WikiPress plugin.
 *
 * @package WikiPress
 * @subpackage Admin
 * @since 1.0.0
 * 
 */
namespace WikiPress\Admin;

use WikiPress\Includes\Settings\Settings;
use WikiPress\Includes\Functions\Admin\FunctionsPlugins;
use WikiPress\Includes\Functions\Admin\FunctionsWiki;
use WikiPress\Includes\Functions\Helpers\AjaxHelper;
use WikiPress\Includes\Functions\Helpers\LoaderHelper;
use WikiPress\Includes\Functions\Admin\FunctionsSidebar;
use WikiPress\Assets\Assets;
use WikiPress\Admin\Manager\Tools\ToolsManager;
use WikiPress\Admin\Manager\Dashboard\DashboardManager;
use WikiPress\Admin\Manager\Settings\SettingsManager;
use WikiPress\Admin\Manager\Wiki\WikiManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Admin {
    /**
     * The DashboardManager instance for managing the dashboard page. 
     * 
     * @var DashboardManager
     * */
    private DashboardManager $dashboard_manager;
    /**
     * WikiManager instance for managing content-related admin pages.
     *
     * @var WikiManager
     */
    private WikiManager $wiki_manager;
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
    /** 
     * Wiki functions instance for managing wiki-related admin functions.
     * 
     * @var FunctionsWiki
     *  */
    private FunctionsWiki $wiki_functions;

    public function __construct( Assets $assets ) {
        $this->dashboard_manager = new DashboardManager();
        $this->wiki_functions = new FunctionsWiki();
        $this->wiki_manager = new WikiManager( $this->wiki_functions );
        $this->settings_manager = new SettingsManager();
        $this->tools_manager = new ToolsManager();
        $this->plugin_functions = new FunctionsPlugins();
        $this->loader = new LoaderHelper();
        $this->dashboard_manager->register_assets( $assets );
        $this->wiki_manager->register_assets( $assets );
        $this->settings_manager->register_assets( $assets );
        $this->tools_manager->register_assets( $assets );
        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_load_settings_tab', 'callback' => 'load_settings_tab' ],
        ] );
        $this->loader->register_component( $this->wiki_functions, [
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_save_wiki_settings', 'callback' => 'save_wiki_settings' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_delete_wiki', 'callback' => 'delete_wiki' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_delete_wiki_page', 'callback' => 'delete_wiki_page' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_save_wiki_term', 'callback' => 'save_wiki_term' ],
            [ 'type' => 'action', 'hook' => 'wp_ajax_wikipress_delete_wiki_term', 'callback' => 'delete_wiki_term' ],
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
        FunctionsSidebar::register_admin_menu( $this );
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
     * It delegates the rendering to the WikiManager instance.
     */
    public function render_wikis(): void {
        $this->wiki_manager->render();
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
    public function capability( string $key, string $fallback ): string {
        $value = Settings::get( $key, $fallback );
        $values = is_array( $value ) ? $value : [ $value ];
        $allowed = [ 'manage_options', 'edit_posts', 'publish_posts', 'manage_categories', 'delete_posts' ];
        foreach ( $values as $value ) {
            $capability = sanitize_key( (string) $value );
            if ( in_array( $capability, $allowed, true ) ) {
                return $capability;
            }
        }
        return $fallback;
    }

}
