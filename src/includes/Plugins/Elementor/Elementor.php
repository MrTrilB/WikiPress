<?php


namespace TrilBDev\WikiPress\Includes\Plugins\Elementor;

use TrilBDev\WikiPress\Includes\Plugins\AssetsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\I18nProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\SettingsPageProviderInterface;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Assets\Assets;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\I18n;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Settings\Settings;
use TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes\Widgets\Widgets;
use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;

final class Elementor implements PluginInterface, SettingsProviderInterface, SettingsPageProviderInterface, AssetsProviderInterface, I18nProviderInterface {
    private static ?self $instance = null;
    private LoaderHelper $loader;
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    private function __construct() {
        $this->loader = new LoaderHelper();
    }

    public function init(): void {
        if ( ! $this->is_available() || ! Settings::enabled() ) {
            return;
        }

        $this->loader->register_component( $this, [
            [ 'type' => 'action', 'hook' => 'elementor/elements/categories_registered', 'callback' => 'register_category' ],
        ] );
        $this->loader->add_action( 'elementor/widgets/register', Widgets::class, 'register' )->run();
    }

    public function get_slug(): string {
        return 'wikipress-elementor';
    }

    public function get_name(): string {
        return 'WikiPress Elementor';
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
        return 'Provides Elementor integration for WikiPress.';
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
        return class_exists( '\\Elementor\\Plugin' ) && class_exists( '\\Elementor\\Widget_Base' );
    }

    public function register_category( $elements_manager ): void {
        $elements_manager->add_category(
            'trilbdev-wiki',
            [
                'title' => __( 'WikiPress', 'wikipress-elementor' ),
                'icon'  => 'eicon-book',
            ]
        );
    }
}
