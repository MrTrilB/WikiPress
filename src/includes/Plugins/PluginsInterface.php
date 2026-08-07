<?php
/**
 * WikiPress Plugin interface for all plugins.
 *
 * @package WikiPress
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
    /**
     * Get the plugin author.
     *
     * @return string The plugin author.
     */
    public function get_author(): string;
    /**
     * Get the plugin author URI.
     *
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string;
    /**
     * Get the plugin description.
     *
     * @return string The plugin description.
     */
    public function get_description(): string;
    /**
     * Get the plugin URI.
     *
     * @return string The plugin URI.
     */
    public function get_uri(): string;
    /**
     * Get the plugin license.
     *
     * @return string The plugin license.
     */
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
 * Provides shortcode definitions for a WikiPress extension.
 */
interface ShortcodeProviderInterface {
    /**
     * Return definitions created with ShortcodeHelper::define().
     *
     * @return array<int, array<string, mixed>>
     */
    public function get_shortcodes(): array;
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
/**
 * Shortcode provider interface for plugins.
 */
interface I18nProviderInterface {
    public function load_textdomain(): void;
}