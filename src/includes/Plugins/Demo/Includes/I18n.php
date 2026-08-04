<?php
/**
 * Language internationalization (i18n) for the Demo plugin.
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 * 
 */
namespace TrilBDev\WikiPress\Includes\Plugins\Demo\Includes;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wiki-demo-plugin',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/Demo/Language/'
        );
    }
}