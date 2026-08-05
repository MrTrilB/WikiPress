<?php
/**
 * TrilB.Dev Plugin - User Roles Manager Wiki Plugin
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\UserRolesManager
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\UserRolesManager;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\AdminPageProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Includes\I18n;
use TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Includes\Admin\RoleManager;

class UserRolesManager implements PluginInterface, SettingsProviderInterface, AssetsProviderInterface, AdminPageProviderInterface, I18nProviderInterface {
    /**
     * Returns the slug of the plugin.
     *
     * @return string The plugin slug.
     */
    public function get_slug(): string {
        return 'wikipress-user-roles-manager-plugin';
    }
    /**
     * Returns the name of the plugin.
     *
     * @return string The plugin name.
     */
    public function get_name(): string {
        return 'WikiPress User Roles Manager Plugin';
    }
    /**
     * Returns the version of the plugin.
     *
     * @return string The plugin version.
     */
    public function get_version(): string {
        return '1.0.0';
    }
    /**
     * Returns the author of the plugin.
     *
     * @return string The plugin author.
     */
    public function get_author(): string {
        return 'WikiPress Team';
    }
    /**
     * Returns the author URI of the plugin.
     *
     * @return string The plugin author URI.
     */
    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }
    /**
     * Returns the description of the plugin.
     *
     * @return string The plugin description.
     */
    public function get_description(): string {
        return 'Allows you to manage user roles and capabilities in your WordPress site.';
    }
    /**
     * Returns the URI of the plugin.
     *
     * @return string The plugin URI.
     */
    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/wikipress';
    }
    /**
     * Returns the license of the plugin.
     *
     * @return string The plugin license.
     */
    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }
    /**
     * Checks if the plugin is active.
     *
     * @return bool True if the plugin is active, false otherwise.
     */
    public function is_active(): bool {
        return true;
    }
    /**
     * Initializes the plugin.
     */
    public function init(): void {
        Includes::get_instance()->init();
    }
    /**
     * Registers the settings for the plugin.
     */
    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }
    /**
     * Registers the assets for the plugin.
     */
    public function register_assets(): void {
        ( new Assets() )->register();
    }
    /**
     * Registers the admin pages for the plugin.
     */
    public function register_admin_pages(): void {
        ( new RoleManager() )->register();
    }
    /**
     * Loads the text domain for the plugin.
     */
    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
