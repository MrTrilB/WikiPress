<?php

namespace TrilBDev\WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Schemas {
    public static function page(): array {
        return Schema::page();
    }
}
