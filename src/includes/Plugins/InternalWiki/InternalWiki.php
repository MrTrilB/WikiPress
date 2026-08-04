<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\Demo
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes\I18n;

class InternalWiki implements PluginInterface, SettingsProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    public function get_slug(): string {
        return 'wikipress-internal-wiki';
    }

    public function get_name(): string {
        return 'Internal Wiki';
    }

    public function get_version(): string {
        return '1.0.0';
    }

    public function get_author(): string {
        return 'WikiPress Team';
    }

    public function get_author_uri(): string {
        return 'https://trilb.dev';
    }

    public function get_description(): string {
        return 'A plugin that provides an internal wiki system for your WordPress site.';
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

    public function register_assets(): void {
        ( new Assets() )->register();
    }

    public function load_textdomain(): void {
        I18n::load_textdomain();
    }
}
