<?php
/**
 * 
 */
namespace TrilBDev\WikiPress\Admin\Manager\Settings;
use TrilBDev\WikiPress\Includes\Plugins\PluginManager;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;

class SettingsPlugins {
    /**
     * Returns the list of registered settings plugins.
     *
     * @return array The list of registered settings plugins.
     */
    public static function get_registered_plugins(): array {
        $plugins = [];
        foreach ( PluginManager::get_registered_plugins() as $plugin ) {
            if ( $plugin instanceof SettingsProviderInterface ) {
                $plugins[] = $plugin;
            }
        }
        return $plugins;
    }
}