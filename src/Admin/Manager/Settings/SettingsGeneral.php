<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

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
			echo '<tr><th scope="row"><label for="wikipress-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><input class="form-control" id="wikipress-' . esc_attr( $key ) . '" name="wikipress_general[' . esc_attr( $key ) . ']" value="' . esc_attr( $values[ $key ] ?? '' ) . '"></td></tr>';
		}
	}
}
