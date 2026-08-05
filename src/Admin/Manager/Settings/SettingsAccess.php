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
		foreach ( [ 'create_wikis' => 'Who can create wikis?', 'write_pages' => 'Who can write wiki pages?', 'view_analytics' => 'Who can check analytics?', 'manage_plugins' => 'Who can manage plugins?' ] as $key => $label ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-access-' . $key;
			$name = 'wikipress_access[' . $key . ']';
			$options = [ 'manage_options' => 'Administrators', 'edit_posts' => 'Editors', 'publish_posts' => 'Authors' ];
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $label ) . '</th><td>' . FormFieldHelper::select( $name, $options, SanitizationHelper::key( $values[ $key ] ?? 'manage_options', 'manage_options' ), [ 'id' => $id ] ) . '</td></tr>';
		}
	}
}
