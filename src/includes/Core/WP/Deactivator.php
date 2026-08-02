<?php

namespace TrilBDev\WikiPress\Includes\Core\WP;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Deactivator {
    public static function deactivate(): void {
        flush_rewrite_rules();
    }
}
