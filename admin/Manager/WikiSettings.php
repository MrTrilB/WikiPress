<?php
/**
 * TrilB.Dev Plugin - Wiki Settings Manager
 *
 * Manages the settings for the Wiki admin page and settings persistence.
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Manager
 * @since 1.0.0
 */
namespace MrTrilB\TrilBDevPlugin\Admin\Wiki\Manager;

use MrTrilB\TrilBDevPlugin\Includes\Settings\SettingsManager;
use MrTrilB\TrilBDevPlugin\Includes\Wiki\Plugins\Plugins as WikiPlugins;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WikiSettings {
    public static function sanitize_settings( $input ): array {
        $settings = is_array( $input ) ? $input : [];

        $defaults = [
            'wiki_name'                 => '',
            'wiki_description'          => '',
            'wiki_home_page'            => '',
            'wiki_enable_search'        => 'on',
            'wiki_show_toc'             => 'on',
            'wiki_default_category'     => '',
            'wiki_default_tags'         => '',
            'wiki_plugin_directory'     => 'src/Includes/Wiki/Plugins',
            'wiki_plugin_auto_activate' => 'on',
        ];

        $settings = array_merge( $defaults, $settings );

        $settings['wiki_name'] = sanitize_text_field( $settings['wiki_name'] );
        $settings['wiki_description'] = sanitize_textarea_field( $settings['wiki_description'] );
        $settings['wiki_home_page'] = sanitize_text_field( $settings['wiki_home_page'] );
        $settings['wiki_enable_search'] = isset( $settings['wiki_enable_search'] ) && $settings['wiki_enable_search'] === 'on' ? 'on' : 'off';
        $settings['wiki_show_toc'] = isset( $settings['wiki_show_toc'] ) && $settings['wiki_show_toc'] === 'on' ? 'on' : 'off';
        $settings['wiki_default_category'] = sanitize_text_field( $settings['wiki_default_category'] );
        $settings['wiki_default_tags'] = sanitize_text_field( $settings['wiki_default_tags'] );
        $settings['wiki_plugin_directory'] = sanitize_text_field( $settings['wiki_plugin_directory'] );
        $settings['wiki_plugin_auto_activate'] = isset( $settings['wiki_plugin_auto_activate'] ) && $settings['wiki_plugin_auto_activate'] === 'on' ? 'on' : 'off';

        return $settings;
    }

    public static function render_wiki_tab_content(): void {
        $options = SettingsManager::get( 'trilbdev_wiki_settings', [], 'trilbdev_wiki' );
        $options = is_array( $options ) ? $options : [];
        ?>
        <div class="trilbdev-addmod-container">
            <h2 class="trilbdev-addmod-title">
                <i class="fas fa-book"></i> <?php esc_html_e( 'Wiki Settings', 'trilbdev' ); ?>
            </h2>
            <small class="trilbdev-addmod-desc"><?php esc_html_e( 'Configure the base Wiki settings and plugin discovery options.', 'trilbdev' ); ?></small>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="trilbdev-settings-form">
                <input type="hidden" name="action" value="trilbdev_save_settings">
                <input type="hidden" name="trilbdev_current_tab" value="wiki">
                <?php wp_nonce_field( 'trilbdev_save_settings' ); ?>

                <div class="trilbdev-addmod-grid">
                    <div class="trilbdev-addmod-card">
                        <label for="wiki_name"><?php esc_html_e( 'Wiki Name', 'trilbdev' ); ?></label>
                        <input type="text" id="wiki_name" name="trilbdev_wiki_settings[wiki_name]" value="<?php echo esc_attr( $options['wiki_name'] ?? '' ); ?>" />
                        <p class="form-helper"><?php esc_html_e( 'The name shown for your Wiki instance.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label for="wiki_description"><?php esc_html_e( 'Wiki Description', 'trilbdev' ); ?></label>
                        <textarea id="wiki_description" name="trilbdev_wiki_settings[wiki_description]" rows="4"><?php echo esc_textarea( $options['wiki_description'] ?? '' ); ?></textarea>
                        <p class="form-helper"><?php esc_html_e( 'A short description of the Wiki.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label for="wiki_home_page"><?php esc_html_e( 'Home Page Slug', 'trilbdev' ); ?></label>
                        <input type="text" id="wiki_home_page" name="trilbdev_wiki_settings[wiki_home_page]" value="<?php echo esc_attr( $options['wiki_home_page'] ?? '' ); ?>" />
                        <p class="form-helper"><?php esc_html_e( 'Set the slug of the default Wiki home page.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label for="wiki_default_category"><?php esc_html_e( 'Default Category', 'trilbdev' ); ?></label>
                        <input type="text" id="wiki_default_category" name="trilbdev_wiki_settings[wiki_default_category]" value="<?php echo esc_attr( $options['wiki_default_category'] ?? '' ); ?>" />
                        <p class="form-helper"><?php esc_html_e( 'Slug of the default wiki category to apply to new pages.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label for="wiki_default_tags"><?php esc_html_e( 'Default Tags', 'trilbdev' ); ?></label>
                        <input type="text" id="wiki_default_tags" name="trilbdev_wiki_settings[wiki_default_tags]" value="<?php echo esc_attr( $options['wiki_default_tags'] ?? '' ); ?>" />
                        <p class="form-helper"><?php esc_html_e( 'Comma-separated tags added to new Wiki pages by default.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label><?php esc_html_e( 'Search & TOC', 'trilbdev' ); ?></label>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="trilbdev_wiki_settings[wiki_enable_search]" value="on" <?php checked( $options['wiki_enable_search'] ?? 'on', 'on' ); ?> />
                                <?php esc_html_e( 'Enable wiki search', 'trilbdev' ); ?>
                            </label>
                        </div>
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="trilbdev_wiki_settings[wiki_show_toc]" value="on" <?php checked( $options['wiki_show_toc'] ?? 'on', 'on' ); ?> />
                                <?php esc_html_e( 'Show table of contents by default', 'trilbdev' ); ?>
                            </label>
                        </div>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label for="wiki_plugin_directory"><?php esc_html_e( 'Wiki Plugin Directory', 'trilbdev' ); ?></label>
                        <input type="text" id="wiki_plugin_directory" name="trilbdev_wiki_settings[wiki_plugin_directory]" value="<?php echo esc_attr( $options['wiki_plugin_directory'] ?? 'src/Includes/Wiki/Plugins' ); ?>" />
                        <p class="form-helper"><?php esc_html_e( 'Relative path under the plugin root where Wiki plugins are discovered.', 'trilbdev' ); ?></p>
                    </div>

                    <div class="trilbdev-addmod-card">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="trilbdev_wiki_settings[wiki_plugin_auto_activate]" value="on" <?php checked( $options['wiki_plugin_auto_activate'] ?? 'on', 'on' ); ?> />
                            <?php esc_html_e( 'Automatically activate discovered Wiki plugins', 'trilbdev' ); ?>
                        </label>
                    </div>
                </div>

                <?php self::render_wiki_plugin_discovery_panel( $options ); ?>

                <div class="trilbdev-addmod-actions">
                    <button type="submit" class="create_mod_btn">
                        <i class="fas fa-save"></i> <?php esc_html_e( 'Save Wiki Settings', 'trilbdev' ); ?>
                    </button>
                </div>
            </form>
        </div>
        <?php
    }

    private static function render_wiki_plugin_discovery_panel( array $options ): void {
        $plugin_directory = esc_html( $options['wiki_plugin_directory'] ?? 'src/Includes/Wiki/Plugins' );
        $plugin_manager = WikiPlugins::get_instance();
        $plugin_manager->init();

        $registered = $plugin_manager->get_registered_plugins();
        $loaded = $plugin_manager->get_loaded_plugins();
        $registered_classes = array_map( 'get_class', $registered );
        ?>
        <div class="trilbdev-addmod-card">
            <h3><?php esc_html_e( 'Wiki Plugin Discovery', 'trilbdev' ); ?></h3>
            <p class="form-helper"><?php esc_html_e( 'Wiki plugins can be dropped into the configured directory or registered by normal WordPress plugins via the "trilbdev_wiki_register_plugin" action.', 'trilbdev' ); ?></p>
            <p><strong><?php esc_html_e( 'Configured plugin directory:', 'trilbdev' ); ?></strong> <?php echo $plugin_directory; ?></p>

            <?php if ( empty( $registered ) && empty( $loaded ) ) : ?>
                <p><?php esc_html_e( 'No Wiki plugins were discovered. Please add plugin files to the configured directory or register a plugin using the action hook.', 'trilbdev' ); ?></p>
            <?php else : ?>
                <ul class="list-group">
                    <?php foreach ( $registered as $slug => $plugin ) : ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div>
                                <strong><?php echo esc_html( $plugin->get_name() ); ?></strong><br />
                                <small><?php echo esc_html( $slug ); ?></small>
                            </div>
                            <span class="badge bg-success rounded-pill"><?php echo $plugin->is_active() ? esc_html__( 'Active', 'trilbdev' ) : esc_html__( 'Inactive', 'trilbdev' ); ?></span>
                        </li>
                    <?php endforeach; ?>

                    <?php foreach ( $loaded as $class ) : ?>
                        <?php if ( ! in_array( $class, $registered_classes, true ) ) : ?>
                            <li class="list-group-item">
                                <em><?php echo esc_html( $class ); ?></em>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php
    }
}