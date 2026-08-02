<?php
/**
 * TrilB.Dev Plugin - Wiki Settings
 *
 * @package TrilBDev
 * @subpackage Includes\Wiki\Settings
 * @since 1.0.0
 */

namespace MrTrilB\TrilBDevPlugin\Includes\Wiki\Settings;

use MrTrilB\TrilBDevPlugin\Includes\Settings\SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Settings {
    private const TABLE_NAME = 'trilbdev_wiki';

    public function __construct() {
        // No-op.
    }

    public static function get( string $key, $default = null ) {
        return SettingsManager::get( $key, $default, self::TABLE_NAME );
    }

    public static function set( string $key, $value ): bool {
        return SettingsManager::set( $key, $value, self::TABLE_NAME );
    }

    public static function delete( string $key ): bool {
        return SettingsManager::delete( $key, self::TABLE_NAME );
    }

    public static function get_all(): array {
        return SettingsManager::get_all( self::TABLE_NAME );
    }
}