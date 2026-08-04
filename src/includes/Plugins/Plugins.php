<?php
/**
 * TrilB.Dev Plugin - Wiki Plugins
 *
 * Handles discovery and loading of Wiki plugin modules.
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Plugins
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins;

use TrilBDev\WikiPress\Includes\Functions\Helpers\LoggerHelper;
use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * Class Plugins
 *
 * Manages the discovery, loading, and initialization of Wiki plugin modules.
 */
class Plugins {
    /**
     * Singleton instance of the Plugins class.
     *
     * @var Plugins|null
     */
    private static ?Plugins $instance = null;
    /**
     * Array of loaded plugin class names.
     *
     * @var array
     */
    private array $loaded_plugins = [];
    /**
     * Array of registered plugin instances.
     *
     * @var array
     */
    private array $registered_plugins = [];
    /**
     * Indicates whether plugins should be auto-activated upon registration.
     *
     * @var bool
     */
    private bool $auto_activate = true;
    /**
     * Indicates whether the plugin system has been initialized.
     *
     * @var bool
     */
    private bool $initialized = false;


    public static function get_instance(): Plugins {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }
    /**
     * Initializes the plugin system by discovering and loading plugin files.
     */
    public function init(): void {
        if ( $this->initialized ) {
            return;
        }

        $this->initialized = true;
        $this->auto_activate = $this->should_auto_activate();

        $directory = $this->resolve_plugin_directory();
        $files = $this->discover_plugin_files( $directory );

        foreach ( $files as $file ) {
            $this->load_plugin_file( $file );
        }

        /**
         * Allow WordPress plugins to register Wiki extensions.
         *
         * Plugins installed via the normal WordPress plugin system can hook
         * into this action and call Wiki\Plugins::register_plugin().
         */
        do_action( 'trilbdev_wiki_register_plugin', $this );
    }
    /**
     * Retrieves the list of loaded plugin class names.
     *
     * @return array List of loaded plugin class names.
     */
    public function get_loaded_plugins(): array {
        return $this->loaded_plugins;
    }
    /**
     * Retrieves the list of registered plugin instances.
     *
     * @return array List of registered plugin instances.
     */
    public function get_registered_plugins(): array {
        return $this->registered_plugins;
    }
    /**
     * Registers a plugin instance with the plugin system.
     *
     * @param PluginInterface $plugin The plugin instance to register.
     */
    public static function register_plugin( PluginInterface $plugin ): void {
        self::get_instance()->register_plugin_instance( $plugin );
    }
    /**
     * Registers a plugin instance with the plugin system.
     *
     * @param PluginInterface $plugin The plugin instance to register.
     */
    public function register_plugin_instance( PluginInterface $plugin ): void {
        $slug = trim( $plugin->get_slug() );
        if ( $slug === '' ) {
            return;
        }

        if ( isset( $this->registered_plugins[ $slug ] ) ) {
            return;
        }

        $this->registered_plugins[ $slug ] = $plugin;

        if ( $this->initialized && $this->auto_activate ) {
            $this->initialize_plugin( $plugin );
        }
    }
    /**
     * Resolves the plugin directory path based on settings or defaults.
     *
     * @return string The resolved plugin directory path.
     */
    private function resolve_plugin_directory(): string {
        $path = trim( Settings::get( 'wikipress_plugin_directory', WIKIPRESS_PLUGINS ) );
        $resolved = $this->is_absolute_path( $path )
            ? untrailingslashit( $path )
            : untrailingslashit( WIKIPRESS_ROOT ) . '/' . ltrim( str_replace( '\\', '/', $path ), '/' );

        if ( ! is_dir( $resolved ) ) {
            return WIKIPRESS_PLUGINS;
        }

        return $resolved;
    }
    /**
     * Discovers plugin files in the specified directory and its subdirectories.
     *
     * @param string $directory The directory to search for plugin files.
     * @return array List of discovered plugin file paths.
     */
    private function discover_plugin_files( string $directory ): array {
        if ( ! is_dir( $directory ) ) {
            return [];
        }

        $files = glob( $directory . '/*.php' ) ?: [];
        $subdirs = glob( $directory . '/*', GLOB_ONLYDIR ) ?: [];

        foreach ( $subdirs as $subdir ) {
            if ( ! $this->has_plugin_structure( $subdir ) ) {
                continue;
            }

            $subfiles = glob( $subdir . '/*.php' ) ?: [];
            $files = array_merge( $files, array_filter( $subfiles, function ( string $file ) use ( $subdir ): bool {
                return basename( $file ) === basename( $subdir ) . '.php';
            } ) );
        }

        $files = array_filter( array_unique( $files ), 'is_file' );

        return array_values( array_filter( $files, static function ( string $file ): bool {
            return ! in_array( basename( $file ), [
                'Plugins.php',
                'PluginsInterface.php',
            ], true );
        } ) );
    }
    /**
     * Loads a plugin file, extracts its namespace and class name, and initializes the plugin if applicable.
     *
     * @param string $file The path to the plugin file.
     */
    private function load_plugin_file( string $file ): void {
        $contents = file_get_contents( $file );
        if ( ! is_string( $contents ) ) {
            return;
        }

        $this->load_plugin_includes( dirname( $file ) );

        $namespace = $this->extract_namespace( $contents );
        $class_name = $this->extract_class_name( $contents );

        try {
            require_once $file;
        } catch ( \Throwable $e ) {
            LoggerHelper::write_log( sprintf( 'Wiki plugin loader failed to require file %s: %s', $file, $e->getMessage() ) );
            return;
        }

        if ( $class_name === '' ) {
            return;
        }

        $fqcn = $namespace !== '' ? sprintf( '%s\\%s', trim( $namespace, '\\' ), $class_name ) : $class_name;

        if ( ! class_exists( $fqcn ) ) {
            return;
        }

        $instance = new $fqcn();
        if ( ! $instance instanceof PluginInterface ) {
            LoggerHelper::write_log( sprintf( 'Wiki plugin %s does not implement PluginInterface.', $fqcn ) );
            return;
        }

        $this->register_plugin_instance( $instance );

        $this->loaded_plugins[] = $fqcn;
    }

    private function load_plugin_includes( string $plugin_directory ): void {
        foreach ( [ 'Includes/Includes.php', 'Includes/I18n.php' ] as $includes_file ) {
            $includes_path = trailingslashit( $plugin_directory ) . $includes_file;
            if ( is_readable( $includes_path ) ) {
                require_once $includes_path;
            }
        }
    }

    private function has_plugin_structure( string $plugin_directory ): bool {
        return is_dir( $plugin_directory . '/Assets/dist/css' )
            && is_dir( $plugin_directory . '/Assets/dist/js' )
            && is_readable( $plugin_directory . '/Assets/Assets.php' )
            && is_readable( $plugin_directory . '/Includes/Includes.php' )
            && is_readable( $plugin_directory . '/Includes/I18n.php' )
            && is_readable( $plugin_directory . '/Includes/Settings/Settings.php' )
            && is_dir( $plugin_directory . '/Language' );
    }
    /**
     * Extracts the namespace from the given PHP file content.
     *
     * @param string $content The content of the PHP file.
     * @return string The extracted namespace, or an empty string if not found.
     */
    private function extract_namespace( string $content ): string {
        if ( preg_match( '/namespace\s+([^;]+);/i', $content, $matches ) ) {
            return trim( $matches[1] );
        }

        return '';
    }
    /**
     * Extracts the class name from the given PHP file content.
     *
     * @param string $content The content of the PHP file.
     * @return string The extracted class name, or an empty string if not found.
     */
    private function extract_class_name( string $content ): string {
        if ( preg_match( '/class\s+([A-Za-z0-9_]+)/i', $content, $matches ) ) {
            return trim( $matches[1] );
        }

        return '';
    }
    /**
     * Determines whether plugins should be auto-activated upon registration.
     *
     * @return bool True if plugins should be auto-activated, false otherwise.
     */
    private function should_auto_activate(): bool {
        return Settings::get( 'wiki_plugin_auto_activate', 'on' ) === 'on';
    }
    /**
     * Initializes a registered plugin instance if it is active.
     *
     * @param PluginInterface $plugin The plugin instance to initialize.
     */
    private function initialize_plugin( PluginInterface $plugin ): void {
        if ( ! $plugin->is_active() ) {
            return;
        }

        try {
            if ( $plugin instanceof SettingsProviderInterface ) {
                $plugin->register_settings();
            }

            if ( $plugin instanceof DatabaseProviderInterface ) {
                $plugin->register_tables();
            }

            if ( $plugin instanceof AssetsProviderInterface ) {
                $plugin->register_assets();
            }

            if ( $plugin instanceof AdminPageProviderInterface ) {
                $plugin->register_admin_pages();
            }

            if ( $plugin instanceof RestRouteProviderInterface ) {
                $plugin->register_rest_routes();
            }

            if ( $plugin instanceof FrontendProviderInterface ) {
                $plugin->register_frontend();
            }

            if ( $plugin instanceof I18nProviderInterface ) {
                $plugin->load_textdomain();
            }

            $plugin->init();
        } catch ( \Throwable $e ) {
            LoggerHelper::write_log( sprintf( 'Wiki plugin %s failed to initialize: %s', $plugin->get_slug(), $e->getMessage() ) );
        }
    }
    /**
     * Checks if the given path is an absolute path.
     *
     * @param string $path The path to check.
     * @return bool True if the path is absolute, false otherwise.
     */
    private function is_absolute_path( string $path ): bool {
        return preg_match( '/^(?:[A-Za-z]:[\\\\\/]|[\\\\\\/])/', $path ) === 1;
    }
}