<?php

namespace WikiPress\Includes\Plugins\InternalWiki\Includes\Core;

use WikiPress\Includes\Core\Capabilities as CoreCapabilities;

final class Capabilities extends CoreCapabilities {
	/**
	 * Register Internal Wiki capabilities with WikiPress core.
	 *
	 * @return void
	 */
	public static function register(): void {
		parent::extend( self::plugin_definitions() );
	}

	/**
	 * Return Internal Wiki capability definitions.
	 *
	 * @return array<string, array{group: string, label: string, description: string}>
	 */
	private static function plugin_definitions(): array {
		return [
			'wikipress_int_read' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Read Internal Wiki', 'wikipress' ), 'description' => __( 'Allows reading internal WikiPress content.', 'wikipress' ) ],
			'wikipress_admin_int_view' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'View Internal Wiki Administration', 'wikipress' ), 'description' => __( 'Allows access to Internal Wiki administration.', 'wikipress' ) ],
			'wikipress_int_create' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Create Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows creating internal WikiPress content.', 'wikipress' ) ],
			'wikipress_int_edit' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Edit Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows editing internal WikiPress content.', 'wikipress' ) ],
			'wikipress_int_delete' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Delete Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows deleting internal WikiPress content.', 'wikipress' ) ],
			'wikipress_int_publish' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Publish Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows publishing internal WikiPress content.', 'wikipress' ) ],
			'wikipress_int_edit_others' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Edit Others Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows editing internal WikiPress content created by other users.', 'wikipress' ) ],
			'wikipress_int_delete_others' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Delete Others Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows deleting internal WikiPress content created by other users.', 'wikipress' ) ],
			'wikipress_int_edit_published' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Edit Published Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows editing published internal WikiPress content.', 'wikipress' ) ],
			'wikipress_int_delete_published' => [ 'group' => 'WikiPress Internal Wiki', 'label' => __( 'Delete Published Internal Wiki Content', 'wikipress' ), 'description' => __( 'Allows deleting published internal WikiPress content.', 'wikipress' ) ],
		];
	}
}