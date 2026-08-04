<?php
/**
 * Plugin interface for all plugins.
 *
 * @package TrilBDev
 * @subpackage Includes\Plugins
 */
namespace TrilBDev\WikiPress\Includes\Plugins;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Plugin interface for all plugins.
 */
interface PluginInterface {
    /**
     * Get the plugin slug.
     *
     * @return string The plugin slug.
     */
    public function get_slug(): string;
    /**
     * Get the plugin name.
     *
     * @return string The plugin name.
     */
    public function get_name(): string;
    /**
     * Get the plugin version.
     *
     * @return string The plugin version.
     */
    public function get_version(): string;
    public function get_author(): string;
    public function get_author_uri(): string;
    public function get_description(): string;
    public function get_uri(): string;
    public function get_license(): string;
    /**
     * Check if the plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool;
    /**
     * Initialize the plugin.
     *
     * @return void
     */
    public function init(): void;
}
/**
 * Setting provider interface for plugins.
 */
interface SettingsProviderInterface {
    /**
     * Register settings for the plugin.
     *
     * @return void
     */
    public function register_settings(): void;
}

/**
 * Provides an automatically generated settings tab for a WikiPress plugin.
 */
interface SettingsPageProviderInterface {
    /**
     * Returns the plugin settings tab and field definitions.
     *
     * @return array<string, mixed>
     */
    public function get_settings_page(): array;

    /**
     * Sanitizes and persists the plugin settings.
     *
     * @param mixed $input Submitted settings.
     * @return array<string, mixed>
     */
    public function sanitize_settings( $input ): array;
}
/**
 * Shortcode provider interface for plugins.
 */
interface DatabaseProviderInterface {
    /**
     * Register database tables for the plugin.
     *
     * @return void
     */
    public function register_tables(): void;
}
/**
 * Shortcode provider interface for plugins.
 */
interface AssetsProviderInterface {
    /**
     * Register assets for the plugin.
     *
     * @return void
     */
    public function register_assets(): void;
}
/**
 * Shortcode provider interface for plugins.
 */
interface AdminPageProviderInterface {
    /**
     * Register admin pages for the plugin.
     *
     * @return void
     */
    public function register_admin_pages(): void;
}
/**
 * Shortcode provider interface for plugins.
 */
interface RestRouteProviderInterface {
    /**
     * Register REST API routes for the plugin.
     *
     * @return void
     */
    public function register_rest_routes(): void;
}
/**
 * Shortcode provider interface for plugins.
 */
interface FrontendProviderInterface {
    /**
     * Register frontend functionality for the plugin.
     *
     * @return void
     */
    public function register_frontend(): void;
}

interface I18nProviderInterface {
    public function load_textdomain(): void;
}