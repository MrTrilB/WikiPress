<?php
/**
 * REST API schema definitions for WikiPress.
 *
 * @package WikiPress
 * @subpackage API
 * @since 1.0.0
 */
namespace WikiPress\API;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

final class Schemas {
    /**
     * Returns the schema for a wiki object.
     *
     * @return array The schema definition for a wiki object.
     */
    public static function wiki(): array {
        return Schema::wiki();
    }
    /**
     * Returns the schema for a page object.
     *
     * @return array The schema definition for a page object.
     */
    public static function page(): array {
        return Schema::page();
    }
    /**
     * Returns the parameters for collection endpoints (list of wikis or pages).
     *
     * @return array The parameters for collection endpoints.
     */
    public static function collection_parameters(): array {
        return Schema::collection_parameters();
    }
}
