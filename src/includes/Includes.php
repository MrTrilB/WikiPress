<?php

namespace TrilBDev\WikiPress\Includes;

use TrilBDev\WikiPress\Includes\Core\PostType;
use TrilBDev\WikiPress\Includes\Core\Taxonomy;
use TrilBDev\WikiPress\Includes\Functions\Logger;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Includes {
    private static ?self $instance = null;

    private function __construct() {
        Logger::write_log( 'WikiPress core includes initialized.' );
    }

    public static function get_instance(): self {
        return self::$instance ??= new self();
    }

    public function init(): void {
        ( new PostType() )->register();
        ( new Taxonomy() )->register();
    }
}
