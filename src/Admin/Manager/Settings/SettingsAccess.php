<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

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
			echo '<tr><th scope="row"><label for="wikipress-access-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label></th><td><select class="form-select" id="wikipress-access-' . esc_attr( $key ) . '" name="wikipress_access[' . esc_attr( $key ) . ']">';
			foreach ( [ 'manage_options' => 'Administrators', 'edit_posts' => 'Editors', 'publish_posts' => 'Authors' ] as $capability => $capability_label ) {
				echo '<option value="' . esc_attr( $capability ) . '" ' . selected( $values[ $key ] ?? 'manage_options', $capability, false ) . '>' . esc_html( $capability_label ) . '</option>';
			}
			echo '</select></td></tr>';
		}
	}
}
