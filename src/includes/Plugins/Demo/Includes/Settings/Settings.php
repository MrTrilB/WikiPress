<?php
/**
 * Settings for the Demo plugin.
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\Settings;
use TrilBDev\WikiPress\Includes\Settings\Settings as BaseSettings;

final class Settings {
    /**
     * Returns the settings for the Demo plugin.
     *
     * @return array The settings array.
     */
    public function register(): void {
        BaseSettings::register_group( 'demo', [
            'demo_setting_1' => '',
            'demo_setting_2' => false,
        ] );
    }
}