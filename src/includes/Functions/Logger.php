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

use TrilBDev\WikiPress\Includes\Settings\Settings;

class Logger {
    public static function write_log( $log ): void {
        $debug_enabled = defined( 'WP_DEBUG' ) && WP_DEBUG;
        $plugin_logging = (bool) Settings::get( 'debug_logging', false );
        if ( ! $debug_enabled && ! $plugin_logging ) {
            return;
        }
        error_log( is_array( $log ) || is_object( $log ) ? print_r( $log, true ) : (string) $log );
    }
}