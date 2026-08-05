<?php
/**
 * Settings for the User Roles Manager plugin.
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\UserRolesManager\Includes
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Plugins\UserRolesManager\Includes\Settings;
use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;

final class Settings {
    /**
     * Returns the settings for the User Roles Manager plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 'user_roles_manager' );
    }
}