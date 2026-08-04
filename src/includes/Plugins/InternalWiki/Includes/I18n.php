<?php
/**
 * Language internationalization (i18n) for the Internal Wiki plugin.
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 * 
 */
namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes;

class I18n {
    /**
     * Loads the plugin's text domain for translation.
     */
    public static function load_textdomain(): void {
        load_plugin_textdomain(
            'internal-wiki-plugin',
            false,
            dirname( plugin_basename( WIKIPRESS_FILE ) ) . '/src/includes/Plugins/InternalWiki/Language/'
        );
    }
}