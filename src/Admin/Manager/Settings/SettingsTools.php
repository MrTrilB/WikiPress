<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

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
		echo '<tr><th scope="row">' . esc_html__( 'Debug logging', 'wikipress' ) . '</th><td><label class="form-check"><input class="form-check-input" type="checkbox" name="wikipress_tools[debug_logging]" value="1" ' . checked( ! empty( $values['debug_logging'] ), true, false ) . '> ' . esc_html__( 'Enable WikiPress debug logging', 'wikipress' ) . '</label></td></tr>';
		echo '<tr><th scope="row">' . esc_html__( 'Import and export', 'wikipress' ) . '</th><td><a class="btn btn-outline-primary" href="' . esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wikipress_export' ), 'wikipress_export' ) ) . '">' . esc_html__( 'Export WikiPress JSON', 'wikipress' ) . '</a></td></tr>';
		echo '<tr><th scope="row">' . esc_html__( 'Database manager', 'wikipress' ) . '</th><td>' . esc_html__( 'The settings table is managed automatically during plugin activation.', 'wikipress' ) . '</td></tr>';
	}

	/**
	 * Render the JSON import form.
	 *
	 * @return void
	 */
	public function render_import_form(): void {
		?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" class="card wikipress-import-form shadow-sm mt-4">
			<input type="hidden" name="action" value="wikipress_import">
			<?php wp_nonce_field( 'wikipress_import' ); ?>
			<div class="card-body"><label class="form-label" for="wikipress-import-file"><?php esc_html_e( 'Import WikiPress JSON', 'wikipress' ); ?></label><input class="form-control mb-3" id="wikipress-import-file" type="file" name="wikipress_import_file" accept="application/json,.json" required><button class="btn btn-primary" type="submit"><?php esc_html_e( 'Import JSON', 'wikipress' ); ?></button></div>
		</form>
		<?php
	}
}
