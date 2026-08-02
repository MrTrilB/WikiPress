<?php

/**
 * WikiPress - A WordPress Plugin
 *
 * This is the main plugin file for the WikiPress WordPress plugin. It contains the plugin metadata and initializes the plugin by including necessary files and setting up activation and deactivation hooks.
 *
 * @link              https://https://trilb.dev/MrTrilB
 * @since             1.0.0
 * @package           Wikipress
 *
 * @wordpress-plugin
 * Plugin Name:       WikiPress
 * Plugin URI:        https://https://trilb.dev/collection/web-extension/wordpress/wikipress
 * Description:       This is a description of the plugin.
 * Version:           1.0.0
 * Author:            MrTrilB
 * Author URI:        https://https://trilb.dev/MrTrilB/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wikipress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WIKIPRESS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wikipress-activator.php
 */
function activate_wikipress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wikipress-activator.php';
	Wikipress_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wikipress-deactivator.php
 */
function deactivate_wikipress() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wikipress-deactivator.php';
	Wikipress_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wikipress' );
register_deactivation_hook( __FILE__, 'deactivate_wikipress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wikipress.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wikipress() {

	$plugin = new Wikipress();
	$plugin->run();

}
run_wikipress();
