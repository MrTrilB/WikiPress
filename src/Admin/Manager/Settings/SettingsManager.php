<?php

namespace TrilBDev\WikiPress\Admin\Manager\Settings;

use TrilBDev\WikiPress\Admin\Manager\Manager;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager extends Manager {
    private SettingsGeneral $general_page;
    private SettingsLayout $layout_page;
    private SettingsAccess $access_page;
    private SettingsTools $tools_page;

    /**
     * The Page variable.
     *
     * @since 1.0.0
     * @access protected
     * @var string $page The page variable.
     */
    protected $page;
    /**
     * `Constructor` method for the `DashboardManager` class. 
     *
     * @since 1.0.0
     * @return void
     */

    public function __construct() {
        /**
         * Set the page variable to 'dashboard'.
         *
         * @since 1.0.0
         */
        $this->page = 'dashboard';
        $this->general_page = new SettingsGeneral();
        $this->layout_page = new SettingsLayout();
        $this->access_page = new SettingsAccess();
        $this->tools_page = new SettingsTools();
    }
    /**
     * Renders the settings page.
     *
     * @since 1.0.0
     * @return void
     */
    public function render(): void {
        $tab = sanitize_key( $_GET['tab'] ?? 'general' );
        $groups = Settings::get_all();
        $values = $groups[ $tab ] ?? [];
        $this->header( __( 'Settings', 'wikipress' ) );
        ?>
        <?php if ( in_array( $tab, [ 'plugins', 'third-party' ], true ) ) : ?>
            <?php 'plugins' === $tab ? $this->render_wikipress_plugins() : $this->render_third_party_plugins(); ?>
        <?php else : ?>
        <form method="post" action="options.php" class="wikipress-settings-form card shadow-sm">
            <?php settings_fields( 'wikipress_settings' ); ?>
            <div class="card-body"><table class="form-table table align-middle"><tbody>
            <?php if ( 'general' === $tab ) : $this->general_page->render( $values ); ?>
            <?php elseif ( 'layout' === $tab ) : $this->layout_page->render( $values ); ?>
            <?php elseif ( 'access' === $tab ) : $this->access_page->render( $values ); ?>
            <?php elseif ( isset( $this->plugin_settings_pages()[ $tab ] ) ) : $this->render_plugin_page( $this->plugin_settings_pages()[ $tab ], $values ); ?>
            <?php elseif ( 'tools' === $tab ) : $this->tools_page->render( $values ); ?>
            <?php endif; ?>
            </tbody></table>
            <?php submit_button( __( 'Save Changes', 'wikipress' ), 'primary', 'submit', true, [ 'class' => 'btn btn-primary' ] ); ?></div>
        </form>
        <?php if ( 'tools' === $tab ) : $this->tools_page->render_import_form(); endif; ?>
        <?php endif; ?>
        <?php $this->footer();
    }

    public function register_assets( Assets $assets ): void {
        $this->register_page_assets( $assets, [ 'wikipress-settings' ], 'settings' );
    }

    private function plugin_settings_pages(): array {
        $pages = [];
        foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
            if ( ! $plugin instanceof SettingsPageProviderInterface || ! $plugin->is_active() ) {
                continue;
            }

            $page = $plugin->get_settings_page();
            if ( empty( $page['slug'] ) || empty( $page['label'] ) || empty( $page['fields'] ) ) {
                continue;
            }
            $pages[ $page['slug'] ] = $page;
        }
        return $pages;
    }

    private function render_plugin_page( array $page, array $values ): void {
        echo '<tr><th scope="row">' . esc_html( $page['title'] ?? $page['label'] ) . '</th><td>';
        foreach ( $page['fields'] as $field ) {
            $key = sanitize_key( $field['key'] ?? '' );
            if ( '' === $key ) {
                continue;
            }
            $default = array_key_exists( 'default', $field ) ? $field['default'] : false;
            $name = 'wikipress_' . sanitize_key( $page['slug'] ) . '[' . $key . ']';
            $value = $values[ $key ] ?? $default;
            $type = sanitize_key( (string) ( $field['type'] ?? 'checkbox' ) );
            echo '<div class="mb-3">' . FormFieldHelper::label( 'wikipress-' . $key, (string) ( $field['label'] ?? $key ) );
            if ( 'select' === $type ) {
                echo FormFieldHelper::select( $name, (array) ( $field['options'] ?? [] ), $value, [ 'id' => 'wikipress-' . $key ] );
            } elseif ( 'text' === $type ) {
                echo FormFieldHelper::input( $name, is_scalar( $value ) ? (string) $value : '', [ 'id' => 'wikipress-' . $key, 'type' => 'text' ] );
            } else {
                echo FormFieldHelper::checkbox( $name, '1', '', [ 'id' => 'wikipress-' . $key, 'checked' => ! empty( $value ) ] );
            }
            echo '</div>';
        }
        echo '</td></tr>';
    }

    private function render_wikipress_plugins(): void {
        ?>
        <div class="row g-4">
            <?php foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) : ?>
                <?php $this->render_wikipress_plugin_card( $plugin ); ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_third_party_plugins(): void {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        ?>
        <div class="row g-4">
            <?php foreach ( get_plugins() as $file => $plugin ) : ?>
                <?php if ( function_exists( 'plugin_basename' ) && plugin_basename( WIKIPRESS_FILE ) === $file ) { continue; } ?>
                <?php $this->render_third_party_plugin_card( $file, $plugin ); ?>
            <?php endforeach; ?>
        </div>
        <?php
    }

    private function render_wikipress_plugin_card( $plugin ): void {
        $settings_page = $plugin instanceof SettingsPageProviderInterface ? $plugin->get_settings_page() : [];
        $settings_url = ! empty( $settings_page['slug'] ) ? admin_url( 'admin.php?page=wikipress-settings&tab=' . sanitize_key( $settings_page['slug'] ) ) : admin_url( 'admin.php?page=wikipress-settings&tab=plugins' );
        ?>
        <div class="col-12 col-md-6 col-xl-4 d-flex">
            <article class="card wikipress-plugin-card shadow-sm h-100 w-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" <?php checked( $plugin->is_active() ); ?> disabled aria-label="<?php echo esc_attr( sprintf( __( 'Enable %s', 'wikipress' ), $plugin->get_name() ) ); ?>"></div>
                    <span class="fw-semibold"><?php echo esc_html( $plugin->get_name() ); ?></span>
                </div>
                <div class="card-body d-flex flex-column">
                    <span class="wikipress-plugin-icon dashicons dashicons-admin-plugins" aria-hidden="true"></span>
                    <p class="card-text text-secondary mt-3"><?php echo esc_html( $plugin->get_description() ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Author:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin->get_author() ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Version:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin->get_version() ); ?></p>
                    <p class="card-text mb-3"><span class="text-secondary"><?php esc_html_e( 'Docs:', 'wikipress' ); ?></span> <a href="<?php echo esc_url( $plugin->get_uri() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View documentation', 'wikipress' ); ?></a></p>
                    <a href="<?php echo esc_url( $settings_url ); ?>" class="btn btn-primary mt-auto"><?php esc_html_e( 'Settings', 'wikipress' ); ?></a>
                </div>
            </article>
        </div>
        <?php
    }

    private function render_third_party_plugin_card( string $file, array $plugin ): void {
        $active = function_exists( 'is_plugin_active' ) && is_plugin_active( $file );
        ?>
        <div class="col-12 col-md-6 col-xl-4 d-flex">
            <article class="card wikipress-plugin-card shadow-sm h-100 w-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <div class="form-check form-switch mb-0"><input class="form-check-input" type="checkbox" role="switch" <?php checked( $active ); ?> disabled aria-label="<?php echo esc_attr( sprintf( __( 'Enable %s', 'wikipress' ), $plugin['Name'] ?? $file ) ); ?>"></div>
                    <span class="fw-semibold"><?php echo esc_html( $plugin['Name'] ?? $file ); ?></span>
                </div>
                <div class="card-body d-flex flex-column">
                    <span class="wikipress-plugin-icon dashicons dashicons-admin-plugins" aria-hidden="true"></span>
                    <p class="card-text text-secondary mt-3"><?php echo esc_html( $plugin['Description'] ?? __( 'No description provided.', 'wikipress' ) ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Author:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin['AuthorName'] ?? wp_strip_all_tags( $plugin['Author'] ?? __( 'Unknown', 'wikipress' ) ) ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Version:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin['Version'] ?? __( 'Unknown', 'wikipress' ) ); ?></p>
                    <p class="card-text mb-3"><span class="text-secondary"><?php esc_html_e( 'Docs:', 'wikipress' ); ?></span> <?php if ( ! empty( $plugin['PluginURI'] ) ) : ?><a href="<?php echo esc_url( $plugin['PluginURI'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View documentation', 'wikipress' ); ?></a><?php else : ?><?php esc_html_e( 'Not available', 'wikipress' ); ?><?php endif; ?></p>
                    <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="btn btn-primary mt-auto"><?php esc_html_e( 'Settings', 'wikipress' ); ?></a>
                </div>
            </article>
        </div>
        <?php
    }
}
