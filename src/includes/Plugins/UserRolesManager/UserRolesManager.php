<?php
/**
 * WikiPress - User Roles Manager
 *
 * @package WikiPress
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\UserRolesManager;

use WikiPress\Includes\Plugins\AssetsProviderInterface;
use WikiPress\Includes\Plugins\AdminPageProviderInterface;
use WikiPress\Includes\Plugins\I18nProviderInterface;
use WikiPress\Includes\Plugins\PluginInterface;
use WikiPress\Includes\Plugins\SettingsProviderInterface;
use WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use WikiPress\Includes\Plugins\UserRolesManager\Assets\Assets;
use WikiPress\Includes\Plugins\UserRolesManager\Includes\Includes;
use WikiPress\Includes\Plugins\UserRolesManager\Includes\I18n;
use WikiPress\Includes\Plugins\UserRolesManager\Includes\Admin\RoleManager;

class UserRolesManager implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, AdminPageProviderInterface, I18nProviderInterface {
    /**
     * Returns the slug of the plugin.
     * @since 1.0.0
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'wikipress-user-roles-manager-plugin';
    }
    /**
     * Returns the name of the plugin.
     * @since 1.0.0
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'User Roles Manager';
    }
    /**
     * Returns the version of the plugin.
     * @since 1.0.0
     * @return string The plugin version.
     */
    public function get_version(): string {
        return '1.0.0';
    }
    /**
     * Returns the author of the plugin.
     * @since 1.0.0
     * @return string The plugin author.
     */
    public function get_author(): string {
        return 'WikiPress Team';
    }
    /**
     * Returns the author URI of the plugin.
     * @since 1.0.0
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }
    /**
     * Returns the description of the plugin.
     * @since 1.0.0
     * @return string The plugin description.
     */
    public function get_description(): string {
        return __( 'Allows you to manage user roles and capabilities in your WordPress site.', 'wikipress' );
    }
    /**
     * Returns the URI of the plugin.
     * @since 1.0.0
     * @return string The plugin URI.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/wikipress';
    }
    /**
     * Returns the license of the plugin.
     * @since 1.0.0
     * @return string The plugin license.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Checks if the plugin is active.
     * @since 1.0.0
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Initializes the plugin.
     * @since 1.0.0
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
     * @return array<string, mixed> The settings page configuration.
     */
    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }
    /**
     * Sanitize and persist plugin settings.
     *
     * @param mixed $input Submitted settings.
     * @return array<string, mixed> Sanitized settings.
     */
    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }
    /**
     * Registers the assets for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Registers the admin pages for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function register_admin_pages(): void {
        ( new RoleManager() )->register();
    }
    /**
     * Loads the text domain for the plugin.
     * @since 1.0.0
     * @return void
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
