<?php
/**
 * Language internationalization (i18n) for the Internal Wiki plugin.
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 * 
 */
namespace WikiPress\Includes\Plugins\InternalWiki\Includes;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'wikipress',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/InternalWiki/Language/'
        );
    }
}