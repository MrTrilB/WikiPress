<?php

namespace TrilBDev\WikiPress\Admin\Manager;

use TrilBDev\WikiPress\Includes\Settings\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager extends PageManager {
    public function render(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'general' );
        $groups = Settings::get_all();
        $values = $groups[ $tab ] ?? [];
        $tabs = [ 'general' => __( 'General', 'wikipress' ), 'layout' => __( 'Layout', 'wikipress' ), 'access' => __( 'Access Restrictions', 'wikipress' ), 'tools' => __( 'Tools', 'wikipress' ) ];
        $this->header( __( 'Settings', 'wikipress' ) );
        ?>
        <nav class="nav nav-tabs mb-4">
            <?php foreach ( $tabs as $key => $label ) : ?><a class="nav-link <?php echo $tab === $key ? 'active' : ''; ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=wikipress-settings&tab=' . $key ) ); ?>"><?php echo esc_html( $label ); ?></a><?php endforeach; ?>
        </nav>
        <form method="post" action="options.php" class="wikipress-settings-form card shadow-sm">
            <?php settings_fields( 'wikipress_settings' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
            <?php if ( 'general' === $tab ) : ?>
                <?php foreach ( [ 'root_name' => 'WikiPress Root Name', 'root_slug' => 'WikiPress Root Slug', 'category_slug' => 'Custom Category Slug', 'tag_slug' => 'Custom Tags Slug', 'permalink' => 'WikiPress Permalink' ] as $key => $label ) : ?>
                    <tr><th scope="row"><label for="wikipress-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><input class="regular-text" id="wikipress-<?php echo esc_attr( $key ); ?>" name="wikipress_general[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $values[ $key ] ?? '' ); ?>"></td></tr>
                <?php endforeach; ?>
            <?php elseif ( 'layout' === $tab ) : ?>
                <?php foreach ( [ 'show_search' => 'Show Search', 'show_toc' => 'Show Table of Contents' ] as $key => $label ) : ?><tr><th scope="row"><?php echo esc_html( $label ); ?></th><td><label><input type="checkbox" name="wikipress_layout[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $values[ $key ] ) ); ?>> <?php echo esc_html( $label ); ?></label></td></tr><?php endforeach; ?>
            <?php elseif ( 'access' === $tab ) : ?>
                <?php foreach ( [ 'create_wikis' => 'Who can create wikis?', 'write_pages' => 'Who can write wiki pages?', 'view_analytics' => 'Who can check analytics?', 'manage_plugins' => 'Who can manage plugins?' ] as $key => $label ) : ?><tr><th scope="row"><label for="wikipress-access-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label></th><td><select id="wikipress-access-<?php echo esc_attr( $key ); ?>" name="wikipress_access[<?php echo esc_attr( $key ); ?>]"><?php foreach ( [ 'manage_options' => 'Administrators', 'edit_posts' => 'Editors', 'publish_posts' => 'Authors' ] as $capability => $capability_label ) : ?><option value="<?php echo esc_attr( $capability ); ?>" <?php selected( $values[ $key ] ?? 'manage_options', $capability ); ?>><?php echo esc_html( $capability_label ); ?></option><?php endforeach; ?></select></td></tr><?php endforeach; ?>
            <?php elseif ( 'tools' === $tab ) : ?>
                <tr><th scope="row"><?php esc_html_e( 'Debug logging', 'wikipress' ); ?></th><td><label><input type="checkbox" name="wikipress_tools[debug_logging]" value="1" <?php checked( ! empty( $values['debug_logging'] ) ); ?>> <?php esc_html_e( 'Enable WikiPress debug logging', 'wikipress' ); ?></label></td></tr>
                <tr><th scope="row"><?php esc_html_e( 'Import and export', 'wikipress' ); ?></th><td><a class="button btn btn-outline-primary" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=wikipress_export' ), 'wikipress_export' ) ); ?>"><?php esc_html_e( 'Export WikiPress JSON', 'wikipress' ); ?></a></td></tr>
                <tr><th scope="row"><?php esc_html_e( 'Database manager', 'wikipress' ); ?></th><td><?php esc_html_e( 'The settings table is managed automatically during plugin activation.', 'wikipress' ); ?></td></tr>
            <?php endif; ?>
            </tbody></table>
            <?php submit_button( __( 'Save Changes', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php if ( 'tools' === $tab ) : ?>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data" class="card wikipress-import-form shadow-sm mt-4">
                <input type="hidden" name="action" value="wikipress_import">
                <?php wp_nonce_field( 'wikipress_import' ); ?>
                <div class="card-body"><label class="form-label" for="wikipress-import-file"><?php esc_html_e( 'Import WikiPress JSON', 'wikipress' ); ?></label><input class="form-control mb-3" id="wikipress-import-file" type="file" name="wikipress_import_file" accept="application/json,.json" required><button class="button btn btn-primary" type="submit"><?php esc_html_e( 'Import JSON', 'wikipress' ); ?></button></div>
            </form>
        <?php endif; ?>
        <?php $this->footer();
    }
}
