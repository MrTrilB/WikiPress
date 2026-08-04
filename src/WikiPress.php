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
use TrilBDev\WikiPress\Includes\Functions\Helpers\LoaderHelper;
use TrilBDev\WikiPress\API\Routes;
use TrilBDev\WikiPress\Includes\Analytics\Analytics;
use TrilBDev\WikiPress\Includes\Plugins\Plugins;
use TrilBDev\WikiPress\PublicArea\Frontend;
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
	protected LoaderHelper $loader;

	/**
	 * The file path to the main plugin file.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_file    The file path to the main plugin file.
	 */
	protected string $plugin_file;
	/**
	 * The instance of the Includes class that handles the plugin's includes.
	 *
	 * @var Includes
	 * @since 1.0.0
	 * @access protected
	 */
	protected Includes $includes;

	/**
	 * The instance of the Assets class that handles the plugin's assets.
	 *
	 * @var Assets
	 * @since 1.0.0
	 * @access protected
	 */
	protected Assets $assets;

	/**
	 * The instance of the Admin class that handles the plugin's admin functionality.
	 *
	 * @var Admin
	 * @since 1.0.0
	 * @access protected
	 */
	protected Admin $admin;

	/**
	 * The instance of the Frontend class that handles the plugin's frontend functionality.
	 *
	 * @var Frontend
	 * @since 1.0.0
	 * @access protected
	 */
	protected Frontend $frontend;

	/**
	 * The WikiPress plugin registry and discovery service.
	 *
	 * @var Plugins
	 * @since 1.0.0
	 * @access protected
	 */
	protected Plugins $plugins;

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
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct( string $plugin_file = WIKIPRESS_FILE, string $plugin_name = WIKIPRESS_NAME, string $version = WIKIPRESS_VERSION ) {
		$this->plugin_file = $plugin_file;
		$this->plugin_name = sanitize_key( $plugin_name );
		$this->version = $version;

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
		$this->loader = new LoaderHelper();

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

		$plugin_i18n = new I18n( $this->plugin_name, null, $this->plugin_file );

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
		$this->includes = Includes::get_instance();
		$this->assets = new Assets();
		$this->assets->register();
		$this->admin = new Admin( $this->assets );
		$this->frontend = new Frontend();
		$this->plugins = Plugins::get_instance();

		$this->loader->add_action( 'init', $this->includes, 'init' );
		$this->loader->add_action( 'init', $this->plugins, 'init', 20 );
		$this->loader->add_action( 'admin_menu', $this->admin, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $this->admin, 'register_settings' );
		$this->loader->add_action( 'admin_post_wikipress_export', $this->admin, 'export_data' );
		$this->loader->add_action( 'admin_post_wikipress_import', $this->admin, 'import_data' );
		$this->loader->add_action( 'admin_enqueue_scripts', $this->assets, 'enqueue_admin' );
		$this->loader->add_action( 'wp_enqueue_scripts', $this->assets, 'enqueue_frontend' );
		$this->loader->add_action( 'wp_head', Analytics::class, 'track_view' );
		$this->loader->add_filter( 'the_content', $this->frontend, 'filter_content' );
		$this->loader->add_filter( 'body_class', $this->frontend, 'body_classes' );
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

	public function get_plugin_file(): string {
		return $this->plugin_file;
	}

	public function get_includes(): Includes {
		return $this->includes;
	}

	public function get_assets(): Assets {
		return $this->assets;
	}

	public function get_admin(): Admin {
		return $this->admin;
	}

	public function get_frontend(): Frontend {
		return $this->frontend;
	}

	public function get_plugins(): Plugins {
		return $this->plugins;
	}

	public function register_extension( callable $extension ): self {
		$this->includes->register_extension( $extension );

		return $this;
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
