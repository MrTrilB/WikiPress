<?php

namespace TrilBDev\WikiPress\Includes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Settings {
    public static function get( string $key, $default = null ) {
        return SettingsManager::get( $key, $default );
    }

    public static function set( string $key, $value ): bool {
        return SettingsManager::set( $key, $value );
    }

    public static function delete( string $key ): bool {
        return SettingsManager::delete( $key );
    }

    public static function get_all(): array {
        return SettingsManager::get_all();
    }
}
