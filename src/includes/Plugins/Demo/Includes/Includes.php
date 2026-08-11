<?php
/**
 * Demo Wiki Plugin Includes
 *
 * @package WikiPress
 * @subpackage Plugins\Demo\Includes
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Plugins\Demo\Includes;
use TrilBDev\WikiPress\Includes\Plugins\Demo\Includes\Settings\Settings;

final class Includes {
    private static ?self $instance = null;
    private Settings $settings;
    private Shortcodes $shortcodes;

    private function __construct() {
        $this->settings = new Settings();
        $this->shortcodes = new Shortcodes();
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

    public function shortcodes(): Shortcodes {
        return $this->shortcodes;
    }
}