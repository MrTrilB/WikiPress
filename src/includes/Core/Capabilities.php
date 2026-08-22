<?php

namespace WikiPress\Includes\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {
	/**
	 * Capability definitions contributed by WikiPress extensions.
	 *
	 * @var array<string, array{group: string, label: string, description: string}>
	 */
	private static array $extensions = [];

	/**
	 * Return the core and registered extension capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	public static function definitions(): array {
		return array_merge(
			[
				'wikipress_admin_view' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'View Wikis Administration', 'wikipress' ), 'description' => __( 'Allows access to the WikiPress Wikis administration area.', 'wikipress' ) ],
				'wikipress_create' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Create Wikis', 'wikipress' ), 'description' => __( 'Allows creating Wikis.', 'wikipress' ) ],
				'wikipress_edit' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Edit Wikis', 'wikipress' ), 'description' => __( 'Allows editing Wikis.', 'wikipress' ) ],
				'wikipress_delete' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Delete Wikis', 'wikipress' ), 'description' => __( 'Allows deleting Wikis.', 'wikipress' ) ],
				'wikipress_publish' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Publish Wikis', 'wikipress' ), 'description' => __( 'Allows publishing Wikis.', 'wikipress' ) ],
				'wikipress_edit_published' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Edit Published Wikis', 'wikipress' ), 'description' => __( 'Allows editing published Wikis.', 'wikipress' ) ],
				'wikipress_delete_published' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Delete Published Wikis', 'wikipress' ), 'description' => __( 'Allows deleting published Wikis.', 'wikipress' ) ],
				'wikipress_edit_others' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Edit Others Wikis', 'wikipress' ), 'description' => __( 'Allows editing Wikis created by other users.', 'wikipress' ) ],
				'wikipress_delete_others' => [ 'group' => 'WikiPress Wikis', 'label' => __( 'Delete Others Wikis', 'wikipress' ), 'description' => __( 'Allows deleting Wikis created by other users.', 'wikipress' ) ],
				'wikipress_admin_page_view' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'View Wiki Pages Administration', 'wikipress' ), 'description' => __( 'Allows access to the WikiPress Wiki Pages administration area.', 'wikipress' ) ],
				'wikipress_page_create' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Create Wiki Pages', 'wikipress' ), 'description' => __( 'Allows creating Wiki Pages.', 'wikipress' ) ],
				'wikipress_page_edit' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Edit Wiki Pages', 'wikipress' ), 'description' => __( 'Allows editing Wiki Pages.', 'wikipress' ) ],
				'wikipress_page_delete' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Delete Wiki Pages', 'wikipress' ), 'description' => __( 'Allows deleting Wiki Pages.', 'wikipress' ) ],
				'wikipress_page_edit_others' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Edit Others Wiki Pages', 'wikipress' ), 'description' => __( 'Allows editing Wiki Pages created by other users.', 'wikipress' ) ],
				'wikipress_page_delete_others' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Delete Others Wiki Pages', 'wikipress' ), 'description' => __( 'Allows deleting Wiki Pages created by other users.', 'wikipress' ) ],
				'wikipress_page_publish' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Publish Wiki Pages', 'wikipress' ), 'description' => __( 'Allows publishing Wiki Pages.', 'wikipress' ) ],
				'wikipress_page_edit_published' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Edit Published Wiki Pages', 'wikipress' ), 'description' => __( 'Allows editing published Wiki Pages.', 'wikipress' ) ],
				'wikipress_page_delete_published' => [ 'group' => 'WikiPress Wiki Pages', 'label' => __( 'Delete Published Wiki Pages', 'wikipress' ), 'description' => __( 'Allows deleting published Wiki Pages.', 'wikipress' ) ],
				'wikipress_settings_general_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View General Settings', 'wikipress' ), 'description' => __( 'Allows viewing general WikiPress settings.', 'wikipress' ) ],
				'wikipress_settings_general_edit' => [ 'group' => 'WikiPress Settings', 'label' => __( 'Edit General Settings', 'wikipress' ), 'description' => __( 'Allows editing general WikiPress settings.', 'wikipress' ) ],
				'wikipress_settings_layout_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View Layout Settings', 'wikipress' ), 'description' => __( 'Allows viewing WikiPress layout settings.', 'wikipress' ) ],
				'wikipress_settings_layout_edit' => [ 'group' => 'WikiPress Settings', 'label' => __( 'Edit Layout Settings', 'wikipress' ), 'description' => __( 'Allows editing WikiPress layout settings.', 'wikipress' ) ],
				'wikipress_settings_plugins_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View Plugin Settings', 'wikipress' ), 'description' => __( 'Allows viewing WikiPress plugin settings.', 'wikipress' ) ],
				'wikipress_settings_plugins_int_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View Internal Plugin Settings', 'wikipress' ), 'description' => __( 'Allows viewing settings for internal WikiPress plugins.', 'wikipress' ) ],
				'wikipress_settings_plugins_int_edit' => [ 'group' => 'WikiPress Settings', 'label' => __( 'Edit Internal Plugin Settings', 'wikipress' ), 'description' => __( 'Allows editing settings for internal WikiPress plugins.', 'wikipress' ) ],
				'wikipress_settings_plugins_ext_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View External Plugin Settings', 'wikipress' ), 'description' => __( 'Allows viewing settings for external WikiPress plugins.', 'wikipress' ) ],
				'wikipress_settings_plugins_ext_edit' => [ 'group' => 'WikiPress Settings', 'label' => __( 'Edit External Plugin Settings', 'wikipress' ), 'description' => __( 'Allows editing settings for external WikiPress plugins.', 'wikipress' ) ],
				'wikipress_settings_access_view' => [ 'group' => 'WikiPress Settings', 'label' => __( 'View Access Settings', 'wikipress' ), 'description' => __( 'Allows viewing WikiPress access settings.', 'wikipress' ) ],
				'wikipress_settings_access_edit' => [ 'group' => 'WikiPress Settings', 'label' => __( 'Edit Access Settings', 'wikipress' ), 'description' => __( 'Allows editing WikiPress access settings.', 'wikipress' ) ],
				'wikipress_tools_import' => [ 'group' => 'WikiPress Tools', 'label' => __( 'Import WikiPress Data', 'wikipress' ), 'description' => __( 'Allows importing WikiPress data.', 'wikipress' ) ],
				'wikipress_tools_export' => [ 'group' => 'WikiPress Tools', 'label' => __( 'Export WikiPress Data', 'wikipress' ), 'description' => __( 'Allows exporting WikiPress data.', 'wikipress' ) ],
				'wikipress_tools_debug' => [ 'group' => 'WikiPress Tools', 'label' => __( 'View Debug Tools', 'wikipress' ), 'description' => __( 'Allows using WikiPress debug tools.', 'wikipress' ) ],
				'wikipress_tools_analytics' => [ 'group' => 'WikiPress Tools', 'label' => __( 'View Analytics Tools', 'wikipress' ), 'description' => __( 'Allows viewing WikiPress analytics.', 'wikipress' ) ],
			],
			self::$extensions
		);
	}

	/**
	 * Register definitions contributed by a plugin and install any missing caps.
	 *
	 * @param array<string, array{group: string, label: string, description: string}> $definitions Definitions to add.
	 * @return void
	 */
	public static function extend( array $definitions ): void {
		self::$extensions = array_merge( self::$extensions, $definitions );
		self::install();
	}

	/**
	 * Install missing capabilities without removing administrator customizations.
	 *
	 * @return void
	 */
	public static function install(): void {
		$administrator = get_role( 'administrator' );
		if ( ! $administrator ) {
			return;
		}

		foreach ( array_keys( self::definitions() ) as $capability ) {
			if ( ! $administrator->has_cap( $capability ) ) {
				$administrator->add_cap( $capability );
			}
		}
	}
}