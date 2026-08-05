<?php

namespace TrilBDev\WikiPress\Includes\Plugins\FontAwesome;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\IconPicker;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\API\FontAwesomeAPI;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\I18n;
use TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes\Includes;

final class FontAwesome implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    private static ?self $instance = null;
    private ?IconPicker $icon_picker = null;

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {}

    public function init(): void {
        FontAwesomeAPI::configure();
        if ( $this->is_available() ) {
            $this->icon_picker = IconPicker::get_instance();
        }
        Includes::get_instance()->init();
    }

    public function get_slug(): string {
        return 'wikipress-fontawesome';
    }

    public function get_name(): string {
        return 'WikiPress Font Awesome';
    }

    public function get_version(): string {
        return WIKIPRESS_VERSION;
    }

    public function get_author(): string {
        return 'MrTrilB';
    }

    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }

    public function get_description(): string {
        return 'Provides Font Awesome loading, icon picking, and styling APIs for WikiPress.';
    }

    public function get_uri(): string {
        return 'https://trilb.dev/collection/web-extension/wordpress/wikipress';
    }

    public function get_license(): string {
        return 'GPL-2.0-or-later';
    }

    public function is_active(): bool {
        return true;
    }

    public function register_settings(): void {
        Includes::get_instance()->settings()->register();
    }

    public function get_settings_page(): array {
        return Includes::get_instance()->settings()->get_settings_page();
    }

    public function sanitize_settings( $input ): array {
        return Includes::get_instance()->settings()->sanitize( $input );
    }

    public function register_assets(): void {
        ( new Assets() )->register();
    }

    public function load_textdomain(): void {
        I18n::load_textdomain();
    }

    public function is_available(): bool {
        return function_exists( 'FortAwesome\\fa' ) && class_exists( '\\FortAwesome\\FontAwesome' );
    }

    public function get_icon_picker(): ?IconPicker {
        return $this->icon_picker;
    }
}
