<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\UrlHelper;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsTools {
	/**
	 * Render tools settings fields and import controls.
	 *
	 * @param array<string, mixed> $values Current settings.
	 * @return void
	 */
	public function render( array $values ): void {
		$debug_id = 'wikipress-debug-logging';
		echo '<tr><th scope="row">' . FormFieldHelper::label( $debug_id, __( 'Debug logging', 'wikipress' ) ) . '</th><td>' . FormFieldHelper::checkbox( 'wikipress_tools[debug_logging]', '1', __( 'Enable WikiPress debug logging', 'wikipress' ), [ 'id' => $debug_id, 'checked' => ! empty( $values['debug_logging'] ) ] ) . '</td></tr>';
		echo '<tr><th scope="row">' . esc_html__( 'Import and export', 'wikipress' ) . '</th><td><a class="btn btn-outline-primary" href="' . esc_url( UrlHelper::admin_action_nonce( 'wikipress_export', 'wikipress_export' ) ) . '">' . esc_html__( 'Export WikiPress JSON', 'wikipress' ) . '</a></td></tr>';
		echo '<tr><th scope="row">' . esc_html__( 'Database manager', 'wikipress' ) . '</th><td>' . esc_html__( 'The settings table is managed automatically during plugin activation.', 'wikipress' ) . '</td></tr>';
	}

	/**
	 * Render the JSON import form.
	 *
	 * @return void
	 */
	public function render_import_form(): void {
		?>
		<form method="post" action="<?php echo esc_url( UrlHelper::admin_action( 'wikipress_import' ) ); ?>" enctype="multipart/form-data" class="card wikipress-import-form shadow-sm mt-4">
			<?php echo FormFieldHelper::input( 'action', 'wikipress_import', [ 'type' => 'hidden' ] ); ?>
			<?php wp_nonce_field( 'wikipress_import' ); ?>
			<div class="card-body"><?php echo FormFieldHelper::label( 'wikipress-import-file', __( 'Import WikiPress JSON', 'wikipress' ) ); ?><?php echo FormFieldHelper::input( 'wikipress_import_file', '', [ 'id' => 'wikipress-import-file', 'type' => 'file', 'class' => 'mb-3', 'accept' => 'application/json,.json', 'required' => true ] ); ?><button class="btn btn-primary" type="submit"><?php esc_html_e( 'Import JSON', 'wikipress' ); ?></button></div>
		</form>
		<?php
	}
}
