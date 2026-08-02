<?php
/**
 * TrilB.Dev Plugin - Demo Wiki Plugin
 *
 * @package TrilBDev
 * @subpackage Admin\Wiki\Plugins\Demo
 * @since 1.0.0
 */

namespace TrilBDev\WikiPress\Includes\Wiki\Plugins\Demo;

use TrilBDev\WikiPress\Includes\Wiki\Plugins\PluginInterface;
use TrilBDev\WikiPress\Includes\Wiki\Plugins\Demo\Assets\Assets;
use TrilBDev\WikiPress\Includes\Wiki\Plugins\Demo\Includes\Includes;
use TrilBDev\WikiPress\Includes\Plugins\PluginInterface as BasePluginInterface;

class Demo implements BasePluginInterface {
    public function get_slug(): string {
        return 'wiki-demo-plugin';
    }

    public function get_name(): string {
        return 'Wiki Demo Plugin';
    }

    public function is_active(): bool {
        return true;
    }

    public function init(): void {
        if ( class_exists( Assets::class ) ) {
            new Assets();
        }

        if ( class_exists( Includes::class ) ) {
            Includes::get_instance()->init();
        }
    }
}
