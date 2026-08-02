<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://https://trilb.dev/MrTrilB
 * @since      1.0.0
 *
 * @package    Wikipress
 * @subpackage Wikipress/includes
 */
namespace TrilBDev\WikiPress;
use TrilBDev\WikiPress\Admin\Admin;
use TrilBDev\WikiPress\Assets\Assets;
use TrilBDev\WikiPress\Includes\Includes;
use TrilBDev\WikiPress\Includes\Core\WP\I18n;
use TrilBDev\WikiPress\Includes\Core\WP\WPLoader;
use TrilBDev\WikiPress\API\Routes;
/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Wikipress
 * @subpackage Wikipress/src
 * @author     MrTrilB <mrtrilb@trilb.dev>
 */
class WikiPress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Constants to be used throughout the plugin.
	 * 
	 * @since 1.0.0
	 */
	/**
	 * The root directory of the plugin.
	 *
	 * @since 1.0.0
	 */
	const WIKIPRESS_ROOT = __DIR__;
	/**
	 * The root URL of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_ROOT_URL = WIKIPRESS_URL;
	/**
	 * 
	 */
	const WIKIPRESS_API = self::WIKIPRESS_ROOT . '/API';
	/**
	 * The Assets directory of the plugin
	 * 
	 * @since 1.0.0
	 */
    const WIKIPRESS_ASSETS = self::WIKIPRESS_ROOT . '/Assets';
	/**
	 * The Assets URL of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_ASSETS_URL = self::WIKIPRESS_ROOT_URL . 'src/Assets';
	/**
	 * The Admin directory of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_ADMIN = self::WIKIPRESS_ROOT . '/Admin';
	/**
	 * 
	 */
	const WIKIPRESS_ADMIN_URL = self::WIKIPRESS_ROOT_URL . 'src/Admin';
	/**
	 * The Languages directory of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_LANGUAGES = self::WIKIPRESS_ROOT . '/languages';
	/**
	 * The Includes directory of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_INCLUDES = self::WIKIPRESS_ROOT . '/includes';
	/**
	 * The Includes directory of the plugin.
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_CORE = self::WIKIPRESS_INCLUDES . '/Core';
	/**
	 * The Elementor directory of the plugin
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_ELEMENTOR = self::WIKIPRESS_INCLUDES . '/Elementor';
	/**
	 * The Elementor directory url of the plugin
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_ELEMENTOR_URL = self::WIKIPRESS_ROOT_URL . '/Includes/Elementor';
	/**
	 * The Settings directory of the plugin
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_SETTINGS = self::WIKIPRESS_INCLUDES . '/Settings';
	/**
	 * The Plugins directory of the plugin
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_PLUGINS = self::WIKIPRESS_INCLUDES . '/Plugins';
	/**
	 * The Plugins directory url of the plugin
	 * 
	 * @since 1.0.0
	 */
	const WIKIPRESS_PLUGINS_URL = self::WIKIPRESS_ROOT_URL . '/Includes/Plugins';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( string $plugin_file = WIKIPRESS_FILE ) {
		if ( defined( 'WIKIPRESS_VERSION' ) ) {
			$this->version = WIKIPRESS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wikipress';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_core_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Wikipress_Loader. Orchestrates the hooks of the plugin.
	 * - Wikipress_i18n. Defines internationalization functionality.
	 * - Wikipress_Admin. Defines all hooks for the admin area.
	 * - Wikipress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		$this->loader = new WPLoader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Wikipress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new I18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_core_hooks() {
		$includes = Includes::get_instance();
		$assets = new Assets();
		$admin = new Admin();

		$this->loader->add_action( 'init', $includes, 'init' );
		$this->loader->add_action( 'admin_menu', $admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $admin, 'register_settings' );
		$this->loader->add_action( 'admin_enqueue_scripts', $assets, 'enqueue_admin' );
		$this->loader->add_action( 'wp_enqueue_scripts', $assets, 'enqueue_frontend' );
		$this->loader->add_action( 'rest_api_init', Routes::class, 'register_routes' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
