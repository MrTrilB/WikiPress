<?php
/**
 * This file manages the internationalization functionality of the plugin.
 * 
 * 
 * 
 * @package TrilBDev\WikiPress\Includes\Plugins\FontAwesome\Includes
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes;

final class I18n {
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wikipress-elementor',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/Elementor/Language/'
        );
    }
}