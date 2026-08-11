<?php
/**
 * This file manages the internationalization functionality of the plugin.
 * 
 * 
 * 
 * @package WikiPress\Includes\Plugins\Elementor\Includes
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Elementor\Includes;

final class I18n {
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wikipress',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/Elementor/Language/'
        );
    }
}