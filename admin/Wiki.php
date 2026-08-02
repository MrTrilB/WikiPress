<?php
/**
 * TrilB.Dev Plugin - Wiki Manager Module
 *
 * Main coordinator for wiki management interface
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki
 * @since 1.0.0
 */
namespace MrTrilB\TrilBDevPlugin\Admin\Wiki;

use MrTrilB\TrilBDevPlugin\Admin\Wiki\Manager\WikiPluginManager;
use MrTrilB\TrilBDevPlugin\Admin\Wiki\Manager\WikiSettings;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Wiki {
    private static ?Wiki $instance = null;

    public static function get_instance(): Wiki {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct() {
        add_action( 'admin_init', [ $this, 'register_wiki_settings' ] );
    }

    public function register_wiki_settings(): void {
        register_setting( 'trilbdev_settings_group', 'trilbdev_wiki_settings', [ self::class, 'sanitize_wiki_settings' ] );
    }

    public static function sanitize_wiki_settings( $input ): array {
        return WikiSettings::sanitize_settings( $input );
    }

    public static function render_wiki_tab_content(): void {
        WikiSettings::render_wiki_tab_content();
    }
}
