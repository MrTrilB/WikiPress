<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsAccess {
	/**
	 * Render access restriction fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		$fields = [
			'create_wikis' => [ 'label' => __( 'Who can create wikis?', 'wikipress' ), 'description' => __( 'Choose the minimum capability required to create WikiPress wikis.', 'wikipress' ), 'tooltip' => __( 'Users without this capability cannot create new wikis.', 'wikipress' ) ],
			'write_pages' => [ 'label' => __( 'Who can write wiki pages?', 'wikipress' ), 'description' => __( 'Choose the minimum capability required to create or edit wiki pages.', 'wikipress' ), 'tooltip' => __( 'This controls editing access to wiki page content.', 'wikipress' ) ],
			'view_analytics' => [ 'label' => __( 'Who can check analytics?', 'wikipress' ), 'description' => __( 'Choose the minimum capability required to view WikiPress analytics.', 'wikipress' ), 'tooltip' => __( 'Analytics data is shown only to users who meet this capability.', 'wikipress' ), 'tooltip_type' => 'info' ],
			'manage_plugins' => [ 'label' => __( 'Who can manage plugins?', 'wikipress' ), 'description' => __( 'Choose the minimum capability required to manage WikiPress plugins.', 'wikipress' ), 'tooltip' => __( 'Use a trusted administrator-level capability for plugin management.', 'wikipress' ), 'tooltip_icon' => 'fa-shield-halved' ],
		];
		foreach ( $fields as $key => $field ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-access-' . $key;
			$name = 'wikipress_access[' . $key . ']';
			$options = [ 'manage_options' => 'Administrators', 'edit_posts' => 'Editors', 'publish_posts' => 'Authors' ];
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $field['label'], $field ) . '</th><td>' . FormFieldHelper::select( $name, $options, SanitizationHelper::key( $values[ $key ] ?? 'manage_options', 'manage_options' ), [ 'id' => $id ] ) . '</td></tr>';
		}
	}
}
