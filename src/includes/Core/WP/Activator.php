<?php

namespace TrilBDev\WikiPress\Includes\Core\WP;

use TrilBDev\WikiPress\Includes\Settings\SettingsManager;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Activator {
    public static function activate(): void {
        SettingsManager::install();
        ( new \TrilBDev\WikiPress\Includes\Core\PostType() )->register();
        ( new \TrilBDev\WikiPress\Includes\Core\Taxonomy() )->register();
        flush_rewrite_rules();
    }
}
