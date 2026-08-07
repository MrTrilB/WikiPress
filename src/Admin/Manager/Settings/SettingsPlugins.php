<?php
namespace TrilBDev\WikiPress\Admin\Manager\Settings;
use TrilBDev\WikiPress\Includes\Functions\Helpers\FormFieldHelper;
use TrilBDev\WikiPress\Includes\Functions\Helpers\SanitizationHelper;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;

final class SettingsPlugins {
    public function has_settings_page( string $slug ): bool {
        return isset( $this->settings_pages()[ $slug ] );
    }

    public function render_settings_page( string $slug, array $values ): void {
        $page = $this->settings_pages()[ $slug ] ?? null;
        if ( ! is_array( $page ) ) {
            return;
        }

        echo '<tr><th scope="row">' . esc_html( $page['title'] ?? $page['label'] ) . '</th><td>';
        foreach ( $page['fields'] as $field ) {
            $key = SanitizationHelper::key( $field['key'] ?? '' );
            if ( '' === $key ) {
                continue;
            }
            $default = array_key_exists( 'default', $field ) ? $field['default'] : false;
            $name = 'wikipress_' . SanitizationHelper::key( $page['slug'] ) . '[' . $key . ']';
            $value = $values[ $key ] ?? $default;
            $type = SanitizationHelper::key( $field['type'] ?? 'checkbox', 'checkbox' );
            echo '<div class="mb-3">' . FormFieldHelper::label(
                'wikipress-' . $key,
                (string) ( $field['label'] ?? $key ),
                [
                    'description' => (string) ( $field['description'] ?? '' ),
                    'tooltip' => (string) ( $field['tooltip'] ?? '' ),
                    'tooltip_type' => SanitizationHelper::key( $field['tooltip_type'] ?? 'question', 'question' ),
                    'tooltip_icon' => (string) ( $field['tooltip_icon'] ?? '' ),
                ]
            );
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

    public function render( string $tab ): void {
        if ( 'third-party' === $tab ) {
            $this->render_third_party_plugins();
            return;
        }

        $this->render_wikipress_plugins();
    }

    private function settings_pages(): array {
        $pages = [];
        foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
            if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface || ! Plugins::get_instance()->is_plugin_enabled( $plugin->get_slug() ) ) {
                continue;
            }

            $page = $plugin->get_settings_page();
            if ( empty( $page['slug'] ) || empty( $page['label'] ) || empty( $page['fields'] ) ) {
                continue;
            }
            $pages[ SanitizationHelper::key( $page['slug'] ) ] = $page;
        }
        return $pages;
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
        $enabled = Plugins::get_instance()->is_plugin_enabled( $plugin->get_slug() );
        $settings_page = $plugin instanceof SettingsPageProviderInterface ? $plugin->get_settings_page() : [];
        $modal_id = 'wikipress-plugin-settings-' . SanitizationHelper::key( $plugin->get_slug() );
        ?>
        <div class="col-12 col-md-6 col-xl-4 d-flex">
            <article class="card wikipress-plugin-card shadow-sm h-100 w-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <?php echo FormFieldHelper::switch( 'wikipress-plugin-status', '1', '', [ 'id' => 'wikipress-plugin-status-' . SanitizationHelper::key( $plugin->get_slug() ), 'checked' => $enabled, 'data-wikipress-plugin-toggle' => 'true', 'data-plugin-slug' => $plugin->get_slug(), 'aria-label' => sprintf( __( 'Enable %s', 'wikipress' ), $plugin->get_name() ) ] ); ?>
                    <span class="fw-semibold"><?php echo esc_html( $plugin->get_name() ); ?></span>
                </div>
                <div class="card-body d-flex flex-column">
                    <span class="wikipress-plugin-icon dashicons dashicons-admin-plugins" aria-hidden="true"></span>
                    <p class="card-text text-secondary mt-3"><?php echo esc_html( $plugin->get_description() ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Author:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin->get_author() ); ?></p>
                    <p class="card-text mb-2"><span class="text-secondary"><?php esc_html_e( 'Version:', 'wikipress' ); ?></span> <?php echo esc_html( $plugin->get_version() ); ?></p>
                    <p class="card-text mb-3"><span class="text-secondary"><?php esc_html_e( 'Docs:', 'wikipress' ); ?></span> <a href="<?php echo esc_url( $plugin->get_uri() ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View documentation', 'wikipress' ); ?></a></p>
                    <?php if ( ! empty( $settings_page['fields'] ) ) : ?>
                        <?php echo FormFieldHelper::button( __( 'Settings', 'wikipress' ), [ 'type' => 'button', 'class' => 'btn-primary mt-auto', 'data-bs-toggle' => 'modal', 'data-bs-target' => '#' . $modal_id ] ); ?>
                    <?php endif; ?>
                </div>
            </article>
        </div>
        <?php

        if ( ! empty( $settings_page['fields'] ) ) {
            $this->render_plugin_settings_modal( $plugin, $settings_page, $modal_id );
        }
    }

    private function render_plugin_settings_modal( PluginInterface $plugin, array $settings_page, string $modal_id ): void {
        $values = Settings::get_group( SanitizationHelper::key( $settings_page['slug'] ), [] ) ?? [];
        ?>
        <div class="modal fade wikipress-plugin-settings-modal" id="<?php echo esc_attr( $modal_id ); ?>" tabindex="-1" aria-labelledby="<?php echo esc_attr( $modal_id . '-label' ); ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-5" id="<?php echo esc_attr( $modal_id . '-label' ); ?>"><?php echo esc_html( $plugin->get_name() ); ?></h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="<?php esc_attr_e( 'Close', 'wikipress' ); ?>"></button>
                    </div>
                    <div class="modal-body">
                        <section class="wikipress-plugin-modal-info mb-4" aria-labelledby="<?php echo esc_attr( $modal_id . '-info' ); ?>">
                            <h3 class="h6" id="<?php echo esc_attr( $modal_id . '-info' ); ?>"><?php esc_html_e( 'Plugin information', 'wikipress' ); ?></h3>
                            <p class="text-secondary mb-3"><?php echo esc_html( $plugin->get_description() ); ?></p>
                            <dl class="row mb-0 small">
                                <dt class="col-sm-3 text-secondary"><?php esc_html_e( 'Author', 'wikipress' ); ?></dt>
                                <dd class="col-sm-9"><?php echo esc_html( $plugin->get_author() ); ?></dd>
                                <dt class="col-sm-3 text-secondary"><?php esc_html_e( 'Version', 'wikipress' ); ?></dt>
                                <dd class="col-sm-9"><?php echo esc_html( $plugin->get_version() ); ?></dd>
                                <dt class="col-sm-3 text-secondary"><?php esc_html_e( 'License', 'wikipress' ); ?></dt>
                                <dd class="col-sm-9 mb-0"><?php echo esc_html( $plugin->get_license() ); ?></dd>
                            </dl>
                        </section>
                        <form class="wikipress-plugin-settings-form" data-plugin-settings-form data-plugin-slug="<?php echo esc_attr( $plugin->get_slug() ); ?>">
                            <h3 class="h6 mb-3"><?php echo esc_html( $settings_page['title'] ?? $settings_page['label'] ); ?></h3>
                            <?php $this->render_plugin_settings_fields( $settings_page, $values, $modal_id ); ?>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php esc_html_e( 'Cancel', 'wikipress' ); ?></button>
                        <button type="button" class="btn btn-primary" data-plugin-settings-save><?php esc_html_e( 'Save', 'wikipress' ); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    private function render_plugin_settings_fields( array $settings_page, array $values, string $prefix ): void {
        foreach ( $settings_page['fields'] as $field ) {
            $key = SanitizationHelper::key( $field['key'] ?? '' );
            if ( '' === $key ) {
                continue;
            }

            $default = array_key_exists( 'default', $field ) ? $field['default'] : false;
            $value = $values[ $key ] ?? $default;
            $type = SanitizationHelper::key( $field['type'] ?? 'checkbox', 'checkbox' );
            $id = SanitizationHelper::key( $prefix . '-' . $key );
            $name = 'settings[' . $key . ']';
            echo '<div class="mb-3">' . FormFieldHelper::label( $id, (string) ( $field['label'] ?? $key ), [
                'description' => (string) ( $field['description'] ?? '' ),
                'tooltip' => (string) ( $field['tooltip'] ?? '' ),
                'tooltip_type' => SanitizationHelper::key( $field['tooltip_type'] ?? 'question', 'question' ),
                'tooltip_icon' => (string) ( $field['tooltip_icon'] ?? '' ),
            ] );
            if ( 'select' === $type ) {
                echo FormFieldHelper::select( $name, (array) ( $field['options'] ?? [] ), $value, [ 'id' => $id ] );
            } elseif ( 'text' === $type ) {
                echo FormFieldHelper::input( $name, is_scalar( $value ) ? (string) $value : '', [ 'id' => $id, 'type' => 'text' ] );
            } else {
                echo FormFieldHelper::checkbox( $name, '1', '', [ 'id' => $id, 'checked' => ! empty( $value ) ] );
            }
            echo '</div>';
        }
    }

    private function render_third_party_plugin_card( string $file, array $plugin ): void {
        $active = function_exists( 'is_plugin_active' ) && is_plugin_active( $file );
        ?>
        <div class="col-12 col-md-6 col-xl-4 d-flex">
            <article class="card wikipress-plugin-card shadow-sm h-100 w-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <?php echo FormFieldHelper::switch( 'wikipress-third-party-status', '1', '', [ 'id' => 'wikipress-third-party-status-' . SanitizationHelper::key( $file ), 'checked' => $active, 'disabled' => true, 'aria-label' => sprintf( __( 'Enable %s', 'wikipress' ), $plugin['Name'] ?? $file ) ] ); ?>
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