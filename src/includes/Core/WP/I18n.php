<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://https://trilb.dev/MrTrilB
 * @since      1.0.0
 *
 * @package    Wikipress
 * @subpackage Wikipress/includes
 */
namespace TrilBDev\WikiPress\Includes\Core\WP;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wikipress
 * @subpackage Wikipress/includes
 * @author     MrTrilB <mrtrilb@trilb.dev>
 */
class I18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( 'wikipress', false, dirname( WIKIPRESS_BASENAME ) . '/src/languages/' );

	}



}
