<?php
/**
 * WikiPress - Demo Wiki Plugin
 *
 * @package WikiPress
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Demo;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\ShortcodeProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\I18n;

class Demo implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface, ShortcodeProviderInterface {
    /**
     * Get the plugin slug.
     *
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'wiki-demo-plugin';
    }
    /**
     * Get the plugin name.
     *
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'Demo';
    }
    /**
     * Get the plugin version.
     *
     * @return string The plugin version.
     */
    public function get_version(): string {
        return '1.0.0';
    }
    /**
     * Get the plugin author.
     *
     * @return string The plugin author.
     */
    public function get_author(): string {
        return 'MrTrilB';
    }
    /**
     * Get the plugin author URI.
     *
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }
    /**
     * Get the plugin description.
     *
     * @return string The plugin description.
     */
    public function get_description(): string {
        return 'A demonstration WikiPress extension plugin.';
    }
    /**
     * Get the plugin URI.
     *
     * @return string The plugin URI.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/wikipress';
    }
    /**
     * Get the plugin license.
     *
     * @return string The plugin license.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Check if the plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Initializes the plugin.
     * @since 1.0.0
     * @return void
     */
    public function init(): void {
        Includes::get_instance()->init();
    }
    /**
     * Registers the settings for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }
    /**
     * Get the settings page for the plugin.
     *
     * @return array The settings page configuration.
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }
    /**
     * Sanitize the settings input for the plugin.
     *
     * @param mixed $input The input to sanitize.
     * @return array The sanitized settings.
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }
    /**
     * Get the shortcodes for the plugin.
     *
     * @return array The shortcodes for the plugin.
     */
    public function get_shortcodes(): array {
        return Includes::get_instance()->shortcodes()->definitions();
    }
    /**
     * Register the assets for the plugin.
     *
     * @return void
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Load the text domain for the plugin.
     *
     * @return void
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
