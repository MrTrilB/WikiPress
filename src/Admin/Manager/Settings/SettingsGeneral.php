<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsGeneral {
	/**
	 * Render general WikiPress settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		foreach ( [ 'root_name' => 'WikiPress Root Name', 'root_slug' => 'WikiPress Root Slug', 'category_slug' => 'Custom Category Slug', 'tag_slug' => 'Custom Tags Slug', 'permalink' => 'WikiPress Permalink' ] as $key => $label ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-' . $key;
			$name = 'wikipress_general[' . $key . ']';
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $label ) . '</th><td>' . FormFieldHelper::text_input( $name, SanitizationHelper::text( $values[ $key ] ?? '' ), [ 'id' => $id ] ) . '</td></tr>';
		}
	}
}
