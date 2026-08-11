<?php
/**
 * Settings for the User Roles Manager plugin.
 * @package WikiPress
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
        BaseSettings::register_group( 'user_roles_manager', [ 'role_manager_capability' => 'manage_options' ] );
    }

    public function get_settings_page(): array {
        return [
            'slug' => 'user_roles_manager',
            'label' => __( 'User Roles Manager', 'wikipress' ),
            'title' => __( 'User Roles Manager settings', 'wikipress' ),
            'layout' => 'table',
            'fields' => [
                [
                    'key' => 'role_manager_capability',
                    'label' => __( 'Required capability', 'wikipress' ),
                    'description' => __( 'Choose the capability required to manage roles.', 'wikipress' ),
                    'type' => 'select',
                    'options' => [
                        'manage_options' => __( 'Manage options', 'wikipress' ),
                        'edit_users' => __( 'Edit users', 'wikipress' ),
                    ],
                    'default' => 'manage_options',
                ],
            ],
        ];
    }

    public function sanitize( $input ): array {
        $input = is_array( $input ) ? $input : [];
        $capability = sanitize_key( $input['role_manager_capability'] ?? 'manage_options' );
        $settings = [
            'role_manager_capability' => in_array( $capability, [ 'manage_options', 'edit_users' ], true ) ? $capability : 'manage_options',
        ];
        BaseSettings::set_group( 'user_roles_manager', $settings );
        return $settings;
    }
}