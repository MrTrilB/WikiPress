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
		foreach ( [ 'show_search' => 'Show Search', 'show_toc' => 'Show Table of Contents' ] as $key => $label ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-' . $key;
			$name = 'wikipress_layout[' . $key . ']';
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $label ) . '</th><td>' . FormFieldHelper::checkbox( $name, '1', $label, [ 'id' => $id, 'checked' => ! empty( $values[ $key ] ) ] ) . '</td></tr>';
		}
	}
}
