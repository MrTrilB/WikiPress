<?php
/**
 * Settings for the Font Awesome WikiPress plugin.
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 */
namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\Settings;
use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;

final class Settings {
    public function register(): void {
        BaseSettings::register_group( 'fontawesome', [
            'fontawesome_source' => 'base',
            'fontawesome_kit_id' => '',
            'fontawesome_version' => '7.0.0',
        ] );
    }

    public static function source(): string {
        return BaseSettings::get_key( 'fontawesome_source', 'base' );
    }

    public static function kit_id(): string {
        return BaseSettings::get_string( 'fontawesome_kit_id' );
    }
}