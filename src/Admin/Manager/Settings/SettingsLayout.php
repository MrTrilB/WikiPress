<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

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
			echo '<tr><th scope="row">' . esc_html( $label ) . '</th><td><label class="form-check"><input class="form-check-input" type="checkbox" name="wikipress_layout[' . esc_attr( $key ) . ']" value="1" ' . checked( ! empty( $values[ $key ] ), true, false ) . '> ' . esc_html( $label ) . '</label></td></tr>';
		}
	}
}
