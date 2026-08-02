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

use TrilBDev\WikiPress\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Interface\PluginInterface;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Plugins {
    private static ?Plugins $instance = null;
    private array $loaded_plugins = [];
    private array $registered_plugins = [];
    private bool $auto_activate = true;
    private bool $initialized = false;

    public static function get_instance(): Plugins {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

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

    public function get_loaded_plugins(): array {
        return $this->loaded_plugins;
    }

    public function get_registered_plugins(): array {
        return $this->registered_plugins;
    }

    public static function register_plugin( PluginInterface $plugin ): void {
        self::get_instance()->register_plugin_instance( $plugin );
    }

    public function register_plugin_instance( PluginInterface $plugin ): void {
        $slug = trim( $plugin->get_slug() );
        if ( $slug === '' ) {
            return;
        }

        if ( isset( $this->registered_plugins[ $slug ] ) ) {
            return;
        }

        $this->registered_plugins[ $slug ] = $plugin;

        if ( $this->auto_activate ) {
            try {
                $plugin->init();
            } catch ( \Throwable $e ) {
                error_log( sprintf( 'Wiki plugin %s failed to initialize: %s', $slug, $e->getMessage() ) );
            }
        }
    }

    private function resolve_plugin_directory(): string {
        $path = trim( Settings::get( 'wikipress_plugin_directory', 'src/Includes/Plugins' ) );
        $resolved = untrailingslashit( WIKIPRESS_PLUGINS_URL ) . '/' . ltrim( $path, '/\\' );

        if ( ! is_dir( $resolved ) ) {
            return WIKIPRESS_PLUGINS;
        }

        return $resolved;
    }

    private function discover_plugin_files( string $directory ): array {
        if ( ! is_dir( $directory ) ) {
            return [];
        }

        $files = glob( $directory . '/*.php' ) ?: [];
        $subdirs = glob( $directory . '/*', GLOB_ONLYDIR ) ?: [];

        foreach ( $subdirs as $subdir ) {
            $subfiles = glob( $subdir . '/*.php' ) ?: [];
            $files = array_merge( $files, $subfiles );
        }

        return array_filter( array_unique( $files ), 'is_file' );
    }

    private function load_plugin_file( string $file ): void {
        $contents = file_get_contents( $file );
        if ( ! is_string( $contents ) ) {
            return;
        }

        $namespace = $this->extract_namespace( $contents );
        $class_name = $this->extract_class_name( $contents );

        try {
            require_once $file;
        } catch ( \Throwable $e ) {
            error_log( sprintf( 'Wiki plugin loader failed to require file %s: %s', $file, $e->getMessage() ) );
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
        if ( $instance instanceof PluginInterface ) {
            $this->register_plugin_instance( $instance );
        } elseif ( method_exists( $instance, 'init' ) ) {
            try {
                $instance->init();
            } catch ( \Throwable $e ) {
                error_log( sprintf( 'Wiki plugin %s init failed: %s', $fqcn, $e->getMessage() ) );
            }
        }

        $this->loaded_plugins[] = $fqcn;
    }

    private function extract_namespace( string $content ): string {
        if ( preg_match( '/namespace\s+([^;]+);/i', $content, $matches ) ) {
            return trim( $matches[1] );
        }

        return '';
    }

    private function extract_class_name( string $content ): string {
        if ( preg_match( '/class\s+([A-Za-z0-9_]+)/i', $content, $matches ) ) {
            return trim( $matches[1] );
        }

        return '';
    }

    private function should_auto_activate(): bool {
        return Settings::get( 'wiki_plugin_auto_activate', 'on' ) === 'on';
    }
}