<?php

namespace TrilBDev\WikiPress\Includes\Settings;

use TrilBDev\WikiPress\Includes\Core\WP\Database;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager {
    /** @var array<string, array<string, mixed>> */
    private static array $registered_groups = [];

    /** @var array<string, string> */
    private static array $registered_keys = [];

    public static function table_name(): string {
        return Database::table_name( 'settings' );
    }

    public static function install(): void {
        Database::install();

        foreach ( self::registered_defaults() as $group => $settings ) {
            $stored_settings = self::get_group( $group );
            if ( null === $stored_settings ) {
                $legacy_settings = self::get_legacy_group( $group );
                $stored_settings = is_array( $legacy_settings ) ? $legacy_settings : [];
            }

            self::set_group( $group, array_merge( $settings, $stored_settings ) );
            self::delete_legacy_group( $group );
        }
    }

    public static function get( string $key, $default = null ) {
        foreach ( self::get_all() as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }
        return self::registered_default( $key, $default );
    }

    public static function set( string $key, $value ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group ) ?? [];
        $settings[ $key ] = $value;
        return self::set_group( $group, $settings );
    }

    public static function delete( string $key ): bool {
        $group = self::group_for_key( $key );
        $settings = self::get_group( $group );
        if ( ! is_array( $settings ) || ! array_key_exists( $key, $settings ) ) {
            return false;
        }
        unset( $settings[ $key ] );
        return self::set_group( $group, $settings );
    }

    public static function has( string $key ): bool {
        foreach ( self::get_all() as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return true;
            }
        }

        return false;
    }

    public static function get_all(): array {
        global $wpdb;
        $rows = $wpdb->get_results( 'SELECT setting_group, setting_value FROM ' . self::table_name(), ARRAY_A );
        $settings = [];
        foreach ( $rows ?: [] as $row ) {
            $group = self::logical_group( $row['setting_group'] );
            $settings[ $group ] = maybe_unserialize( $row['setting_value'] );
        }
        return $settings;
    }

    public static function defaults(): array {
        return [
            'general' => [
                'root_name' => 'WikiPress',
                'root_slug' => 'wiki',
                'category_slug' => 'wiki-category',
                'tag_slug' => 'wiki-tag',
                'permalink' => '%wiki_root%/%wiki%/%postname%',
            ],
            'layout' => [ 'show_search' => true, 'show_toc' => true ],
            'access' => [],
            'tools' => [ 'debug_logging' => false ],
        ];
    }

    public static function get_group( string $group ): ?array {
        global $wpdb;
        $value = $wpdb->get_var( $wpdb->prepare( 'SELECT setting_value FROM ' . self::table_name() . ' WHERE setting_group = %s', self::storage_group( $group ) ) );
        $settings = $value === null ? null : maybe_unserialize( $value );
        return is_array( $settings ) ? $settings : null;
    }

    public static function set_group( string $group, array $settings ): bool {
        global $wpdb;
        return false !== $wpdb->replace( self::table_name(), [
            'setting_group' => self::storage_group( $group ),
            'setting_value' => maybe_serialize( $settings ),
            'autoload' => 'yes',
            'updated_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
    }

    public static function register_group( string $group, array $defaults = [] ): bool {
        $group = self::normalize_group( $group );
        if ( '' === $group ) {
            return false;
        }

        self::$registered_groups[ $group ] = array_merge( self::$registered_groups[ $group ] ?? [], $defaults );
        foreach ( $defaults as $key => $default ) {
            $key = sanitize_key( (string) $key );
            if ( '' !== $key ) {
                self::$registered_keys[ $key ] = $group;
            }
        }
        return true;
    }

    public static function register_key( string $key, string $group, $default = null ): bool {
        $key = sanitize_key( $key );
        if ( '' === $key || ! self::register_group( $group ) ) {
            return false;
        }

        $group = self::normalize_group( $group );
        self::$registered_keys[ $key ] = $group;
        self::$registered_groups[ $group ][ $key ] = $default;
        return true;
    }

    private static function storage_group( string $group ): string {
        $group = self::normalize_group( $group );
        return str_starts_with( $group, 'wikipress_' ) ? $group : 'wikipress_' . $group;
    }

    private static function logical_group( string $group ): string {
        return str_starts_with( $group, 'wikipress_' ) ? substr( $group, 10 ) : $group;
    }

    private static function get_legacy_group( string $group ): ?array {
        global $wpdb;
        $value = $wpdb->get_var( $wpdb->prepare( 'SELECT setting_value FROM ' . self::table_name() . ' WHERE setting_group = %s', sanitize_key( $group ) ) );
        return $value === null ? null : maybe_unserialize( $value );
    }

    private static function delete_legacy_group( string $group ): void {
        global $wpdb;
        $wpdb->delete( self::table_name(), [ 'setting_group' => sanitize_key( $group ) ], [ '%s' ] );
    }

    private static function group_for_key( string $key ): string {
        $key = sanitize_key( $key );
        if ( isset( self::$registered_keys[ $key ] ) ) {
            return self::$registered_keys[ $key ];
        }
        if ( in_array( $key, [ 'create_wikis', 'write_pages', 'view_analytics', 'manage_plugins' ], true ) ) {
            return 'access';
        }
        if ( str_contains( $key, 'layout' ) ) {
            return 'layout';
        }
        if ( str_contains( $key, 'access' ) ) {
            return 'access';
        }
        if ( str_contains( $key, 'tool' ) ) {
            return 'tools';
        }
        return 'general';
    }

    /**
     * Return core and extension defaults for activation and fallback reads.
     *
     * @return array<string, array<string, mixed>>
     */
    private static function registered_defaults(): array {
        $defaults = self::defaults();
        foreach ( self::$registered_groups as $group => $settings ) {
            $defaults[ $group ] = array_merge( $defaults[ $group ] ?? [], $settings );
        }

        return $defaults;
    }

    private static function registered_default( string $key, $fallback ) {
        $key = sanitize_key( $key );
        foreach ( self::registered_defaults() as $settings ) {
            if ( array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }

        return $fallback;
    }

    private static function normalize_group( string $group ): string {
        $group = sanitize_key( $group );
        return str_starts_with( $group, 'wikipress_' ) ? substr( $group, 10 ) : $group;
    }
}
