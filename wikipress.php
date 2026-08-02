<?php

/**
 * WikiPress - A WordPress Plugin
 *
 * This is the main plugin file for the WikiPress WordPress plugin. It contains the plugin metadata and initializes the plugin by including necessary files and setting up activation and deactivation hooks.
 *
 * @link              https://trilb.dev
 * @since             1.0.0
 * @package           Wikipress
 *
 * @wordpress-plugin
 * Plugin Name:       WikiPress
 * Plugin URI:        https://trilb.dev/collection/web-extension/wordpress/wikipress
 * Description:       WikiPress is a WordPress plugin that provides a comprehensive wiki management system, allowing users to create, manage, and display wiki content within their WordPress site.
 * Version:           1.0.0
 * Author:            MrTrilB
 * Author URI:        https://trilb.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wikipress
 * Domain Path:       src/languages
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
define( 'WIKIPRESS_FILE', __FILE__ );
define( 'WIKIPRESS_DIR', plugin_dir_path( __FILE__ ) );
define( 'WIKIPRESS_URL', plugin_dir_url( __FILE__ ) );
define( 'WIKIPRESS_BASENAME', plugin_basename( __FILE__ ) );

$wikipress_autoloader = WIKIPRESS_DIR . 'vendor/autoload.php';
if ( is_readable( $wikipress_autoloader ) ) {
	require_once $wikipress_autoloader;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wikipress-activator.php
 */
function activate_wikipress() {
	\TrilBDev\WikiPress\Includes\Core\WP\Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wikipress-deactivator.php
 */
function deactivate_wikipress() {
	\TrilBDev\WikiPress\Includes\Core\WP\Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wikipress' );
register_deactivation_hook( __FILE__, 'deactivate_wikipress' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once WIKIPRESS_DIR . 'src/WikiPress.php';

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

	$plugin = new \TrilBDev\WikiPress\WikiPress( WIKIPRESS_FILE );
	$plugin->run();

}
run_wikipress();
