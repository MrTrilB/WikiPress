<?php
/**
 * Language internationalization (i18n) for the Demo plugin.
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\Demo\Includes
 * @since 1.0.0
 * 
 */
namespace WikiPress\Includes\Plugins\Gutenburg\Includes;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wikipress',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/Gutenburg/Language/'
        );
    }
}