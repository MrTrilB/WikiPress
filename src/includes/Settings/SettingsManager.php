<?php

namespace TrilBDev\WikiPress\Includes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class SettingsManager {
    public static function table_name(): string {
        global $wpdb;
        return $wpdb->prefix . 'wikipress_settings';
    }

    public static function install(): void {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table = self::table_name();
        $charset = $wpdb->get_charset_collate();
        dbDelta( "CREATE TABLE {$table} (setting_group varchar(100) NOT NULL, setting_value longtext NOT NULL, autoload varchar(20) NOT NULL DEFAULT 'yes', updated_at datetime NOT NULL, PRIMARY KEY (setting_group)) {$charset};" );
        update_option( 'wikipress_db_version', WIKIPRESS_VERSION );

        foreach ( self::defaults() as $group => $settings ) {
            if ( self::get_group( $group ) === null ) {
                self::set_group( $group, $settings );
            }
        }
    }

    public static function get( string $key, $default = null ) {
        foreach ( self::get_all() as $settings ) {
            if ( is_array( $settings ) && array_key_exists( $key, $settings ) ) {
                return $settings[ $key ];
            }
        }
        return $default;
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

    public static function get_all(): array {
        global $wpdb;
        $rows = $wpdb->get_results( 'SELECT setting_group, setting_value FROM ' . self::table_name(), ARRAY_A );
        $settings = [];
        foreach ( $rows ?: [] as $row ) {
            $settings[ $row['setting_group'] ] = maybe_unserialize( $row['setting_value'] );
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

    private static function get_group( string $group ): ?array {
        global $wpdb;
        $value = $wpdb->get_var( $wpdb->prepare( 'SELECT setting_value FROM ' . self::table_name() . ' WHERE setting_group = %s', $group ) );
        return $value === null ? null : maybe_unserialize( $value );
    }

    private static function set_group( string $group, array $settings ): bool {
        global $wpdb;
        return false !== $wpdb->replace( self::table_name(), [
            'setting_group' => sanitize_key( $group ),
            'setting_value' => maybe_serialize( $settings ),
            'autoload' => 'yes',
            'updated_at' => current_time( 'mysql' ),
        ], [ '%s', '%s', '%s', '%s' ] );
    }

    private static function group_for_key( string $key ): string {
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
}
