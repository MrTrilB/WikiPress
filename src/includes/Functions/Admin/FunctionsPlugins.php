<?php
/**
 * Plugin-related admin functions for WikiPress.
 *
 * @package WikiPress
 * @subpackage Includes\Functions\Admin
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Functions\Admin;

use TrilBDev\WikiPress\Includes\Functions\Helpers\AjaxHelper;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class FunctionsPlugins {
    /**
     * Toggle the enabled state of a WikiPress plugin.
     *
     * @return void
     */
    public function toggle_plugin(): void {
        if ( ! AjaxHelper::authorized( 'wikipress_plugin_toggle', 'manage_options' ) ) {
            AjaxHelper::unauthorized( __( 'You are not authorized to manage WikiPress plugins.', 'wikipress' ) );
        }

        $slug = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
        $enabled = ! empty( $_POST['enabled'] );
        $plugin = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;

        if ( ! $plugin instanceof PluginInterface ) {
            AjaxHelper::error( [ 'message' => __( 'The requested WikiPress plugin was not found.', 'wikipress' ) ], 404 );
        }

        if ( ! Plugins::get_instance()->set_plugin_enabled( $slug, $enabled ) ) {
            AjaxHelper::error( [ 'message' => __( 'The WikiPress plugin state could not be saved.', 'wikipress' ) ], 500 );
        }

        AjaxHelper::success( [ 'slug' => $slug, 'enabled' => $enabled ] );
    }

    /**
     * Save settings submitted from a WikiPress plugin modal.
     *
     * @return void
     */
    public function save_plugin_settings(): void {
        if ( ! AjaxHelper::authorized( 'wikipress_plugin_settings', 'manage_options' ) ) {
            AjaxHelper::unauthorized( __( 'You are not authorized to save WikiPress plugin settings.', 'wikipress' ) );
        }

        $slug = sanitize_key( wp_unslash( $_POST['slug'] ?? '' ) );
        $plugin = Plugins::get_instance()->get_registered_plugins()[ $slug ] ?? null;
        if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface ) {
            AjaxHelper::error( [ 'message' => __( 'The requested WikiPress plugin settings were not found.', 'wikipress' ) ], 404 );
        }

        $input = isset( $_POST['settings'] ) && is_array( $_POST['settings'] ) ? wp_unslash( $_POST['settings'] ) : [];
        $settings = $plugin->sanitize_settings( $input );

        AjaxHelper::success(
            [
                'slug' => $slug,
                'settings' => $settings,
                'message' => __( 'Plugin settings saved successfully.', 'wikipress' ),
            ]
        );
    }

    /**
     * Collect settings pages from enabled WikiPress plugins.
     *
     * @return array<int, array{provider: SettingsPageProviderInterface, slug: string, label: string, title: string, fields: array}>
     */
    public function plugin_settings_pages(): array {
        $pages = [];
        foreach ( Plugins::get_instance()->get_registered_plugins() as $plugin ) {
            if ( ! $plugin instanceof PluginInterface || ! $plugin instanceof SettingsPageProviderInterface || ! Plugins::get_instance()->is_plugin_enabled( $plugin->get_slug() ) ) {
                continue;
            }

            $page = $plugin->get_settings_page();
            if ( empty( $page['slug'] ) || empty( $page['label'] ) || empty( $page['fields'] ) ) {
                continue;
            }

            $page['provider'] = $plugin;
            $pages[] = $page;
        }
        return $pages;
    }
}
