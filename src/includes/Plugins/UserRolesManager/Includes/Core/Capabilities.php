<?php

namespace WikiPress\Includes\Plugins\UserRolesManager\Includes\Core;

use WikiPress\Includes\Core\Capabilities as CoreCapabilities;

final class Capabilities extends CoreCapabilities {
	/**
	 * Register User Roles Manager capabilities with WikiPress core.
	 *
	 * @return void
	 */
	public static function register(): void {
		parent::extend( self::plugin_definitions() );
	}

	/**
	 * Return User Roles Manager capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	private static function plugin_definitions(): array {
		return [
			'wikipress_roles_view' => [ 'group' => 'WikiPress User Roles', 'label' => __( 'View User Roles Manager', 'wikipress' ), 'description' => __( 'Allows viewing the WikiPress User Roles Manager.', 'wikipress' ) ],
			'wikipress_roles_create' => [ 'group' => 'WikiPress User Roles', 'label' => __( 'Create User Roles', 'wikipress' ), 'description' => __( 'Allows creating user roles.', 'wikipress' ) ],
			'wikipress_roles_edit' => [ 'group' => 'WikiPress User Roles', 'label' => __( 'Edit User Roles', 'wikipress' ), 'description' => __( 'Allows editing user roles.', 'wikipress' ) ],
			'wikipress_roles_delete' => [ 'group' => 'WikiPress User Roles', 'label' => __( 'Delete User Roles', 'wikipress' ), 'description' => __( 'Allows deleting user roles.', 'wikipress' ) ],
		];
	}
}