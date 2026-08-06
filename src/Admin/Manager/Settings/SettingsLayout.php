<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsLayout {
	/**
	 * Render layout settings fields.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		$fields = [
			'show_search' => [ 'label' => __( 'Show Search', 'wikipress' ), 'description' => __( 'Display search controls in the WikiPress interface.', 'wikipress' ), 'tooltip' => __( 'Disable this if your site provides search through another interface.', 'wikipress' ) ],
			'show_toc' => [ 'label' => __( 'Show Table of Contents', 'wikipress' ), 'description' => __( 'Display a table of contents for WikiPress pages.', 'wikipress' ), 'tooltip' => __( 'The table of contents is generated from page headings.', 'wikipress' ), 'tooltip_type' => 'info' ],
		];
		foreach ( $fields as $key => $field ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-' . $key;
			$name = 'wikipress_layout[' . $key . ']';
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $field['label'], $field ) . '</th><td>' . FormFieldHelper::checkbox( $name, '1', $field['label'], [ 'id' => $id, 'checked' => ! empty( $values[ $key ] ) ] ) . '</td></tr>';
		}
	}
}
