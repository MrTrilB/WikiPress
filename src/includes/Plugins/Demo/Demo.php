<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\Demo
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Demo;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\ShortcodeProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\I18n;

class Demo implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface, ShortcodeProviderInterface {
    public function get_slug(): string {
        return 'wiki-demo-plugin';
    }

    public function get_name(): string {
        return 'Wiki Demo Plugin';
    }

    public function get_version(): string {
        return '1.0.0';
    }

    public function get_author(): string {
        return 'MrTrilB';
    }

    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }

    public function get_description(): string {
        return 'A demonstration WikiPress extension plugin.';
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

    public function init(): void {
        Includes::get_instance()->init();
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

    public function get_shortcodes(): array {
        return Includes::get_instance()->shortcodes()->definitions();
    }

    public function register_assets(): void {
        ( new Assets() )->register();
    }

    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
