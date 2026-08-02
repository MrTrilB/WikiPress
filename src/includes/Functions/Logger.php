<?php
/**
 * This file contains the core logging & debugging functions for the plugin.
 * 
 * 
 * 
 * @package    Wikipress
 * @subpackage Wikipress/includes
 * @since      1.0.0
 * @author     MrTrilB <
 */
namespace TrilBDev\WikiPress\Includes\Functions;

class Logger {
    public static function write_log( $log ) {
        if ( true === WP_DEBUG ) {
            if ( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
}