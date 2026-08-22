<?php
/**
 * TrilB.Dev Plugin - Internal Wiki Wiki Plugin Includes
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\InternalWiki\Includes;
use WikiPress\Includes\Plugins\InternalWiki\Includes\Core\Capabilities;
use WikiPress\Includes\Plugins\InternalWiki\Includes\Settings\Settings;

final class Includes {
    /**
     * Singleton instance of the Includes class.
     * @var self|null
     */
    private static ?self $instance = null;
    /**
     * Settings instance for managing plugin settings.
     * @var Settings
     */
    private Settings $settings;
    /**
     * Private constructor to prevent direct instantiation.
     */
    private function __construct() {
        $this->settings = new Settings();
    }
    /**
     * Retrieves the singleton instance of the Includes class.
     *
     * @return self The singleton instance.
     */
    public static function get_instance(): self {
        return self::$instance ??= new self();
    }
    /**
     * Initializes the plugin's settings.
     */
    public function init(): void {
        Capabilities::register();
        $this->settings->register();
    }
    /**
     * Retrieves the Settings instance for managing plugin settings.
     *
     * @return Settings The Settings instance.
     */
    public function settings(): Settings {
        return $this->settings;
    }
}