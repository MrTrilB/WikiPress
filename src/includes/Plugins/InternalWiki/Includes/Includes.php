<?php
/**
 * TrilB.Dev Plugin - Internal Wiki Wiki Plugin Includes
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\InternalWiki\Includes
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes;
use TrilBDev\WikiPress\Includes\Plugins\InternalWiki\Includes\Settings\Settings;

final class Includes {
    private static ?self $instance = null;
    private Settings $settings;

    private function __construct() {
        $this->settings = new Settings();
    }

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    public function init(): void {
        $this->settings->register();
    }

    public function settings(): Settings {
        return $this->settings;
    }
}