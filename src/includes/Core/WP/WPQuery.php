<?php
/**
 * 
 * WPQuery class for the WikiPress Plugin.
 * 
 * @package WikiPress
 * 
 * @since 1.0.0
 * 
 */
namespace TrilBDev\WikiPress\Includes\Core\WP;

class WPQuery {
    /**
     * Get the current query object.
     *
     * @return \WP_Query
     */
    public static function get_current_query() {
        global $wp_query;
        return $wp_query;
    }

    /**
     * Get the current post object.
     *
     * @return \WP_Post|null
     */
    public static function get_current_post() {
        $query = self::get_current_query();
        return $query->post ?? null;
    }
}