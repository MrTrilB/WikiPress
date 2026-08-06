<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\PermalinkHelper;
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
		$fields = [
			'root_name' => [ 'label' => __( 'WikiPress Root Name', 'wikipress' ), 'description' => __( 'The name used for the main WikiPress area.', 'wikipress' ), 'tooltip' => __( 'This name appears in the admin interface and generated titles.', 'wikipress' ) ],
			'root_slug' => [ 'label' => __( 'WikiPress Root Slug', 'wikipress' ), 'description' => __( 'The URL slug for the WikiPress root.', 'wikipress' ), 'tooltip' => __( 'Use lowercase letters, numbers, and hyphens for the most reliable URLs.', 'wikipress' ) ],
			'category_slug' => [ 'label' => __( 'Custom Category Slug', 'wikipress' ), 'description' => __( 'The URL slug used for WikiPress categories.', 'wikipress' ), 'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ), 'tooltip_type' => 'info' ],
			'tag_slug' => [ 'label' => __( 'Custom Tags Slug', 'wikipress' ), 'description' => __( 'The URL slug used for WikiPress tags.', 'wikipress' ), 'tooltip' => __( 'Changing this value flushes the WordPress rewrite rules.', 'wikipress' ), 'tooltip_type' => 'info' ],
			'permalink' => [ 'label' => __( 'WikiPress Permalink', 'wikipress' ), 'description' => __( 'The permalink structure used by WikiPress content.', 'wikipress' ), 'tooltip' => __( 'Choose a structure that remains readable and stable after publication.', 'wikipress' ) ],
		];
		foreach ( $fields as $key => $field ) {
			$key = SanitizationHelper::key( $key );
			$id = 'wikipress-' . $key;
			$name = 'wikipress_general[' . $key . ']';
			echo '<tr><th scope="row">' . FormFieldHelper::label( $id, $field['label'], $field ) . '</th><td>' . FormFieldHelper::text_input( $name, SanitizationHelper::text( $values[ $key ] ?? '' ), [ 'id' => $id, 'data-permalink-field' => 'permalink' === $key ? 'true' : null ] );
			if ( 'permalink' === $key ) {
				echo '<div class="wikipress-permalink-tokens mt-2" aria-label="' . esc_attr__( 'Available permalink tokens', 'wikipress' ) . '">';
				foreach ( PermalinkHelper::token_definitions() as $token => $description ) {
					echo '<button type="button" class="btn btn-sm btn-outline-secondary me-1 mb-1" data-permalink-token="' . esc_attr( $token ) . '" title="' . esc_attr( $description ) . '">' . esc_html( $token ) . '</button>';
				}
				echo '</div><div class="form-text">' . esc_html__( 'Click a token to add it to the pattern. Tokens are inserted with a trailing slash and reappear when removed.', 'wikipress' ) . '</div>';
			}
			echo '</td></tr>';
		}
	}
}
