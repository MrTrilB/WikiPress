<?php
/**
 * TrilB.Dev Plugin - User Roles Manager Wiki Plugin Includes
 *
 * @package WikiPress
 * @subpackage Admin\Wiki\Plugins\UserRolesManager\Includes
 * @since 1.0.0
 */

namespace WikiPress\Includes\Plugins\UserRolesManager\Includes;
use WikiPress\Includes\Plugins\UserRolesManager\Includes\Settings\Settings;

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