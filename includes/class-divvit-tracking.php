<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://divvit.com
 * @since      1.0.0
 *
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/includes
 */

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
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/includes
 * @author     Your Name <email@example.com>
 */
class Divvit_Tracking {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Divvit_Tracking_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $Divvit_Tracking    The string used to uniquely identify this plugin.
	 */
	protected $Divvit_Tracking;

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
	public function __construct() {

		$this->Divvit_Tracking = 'divvit-tracking';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Divvit_Tracking_Loader. Orchestrates the hooks of the plugin.
	 * - Divvit_Tracking_i18n. Defines internationalization functionality.
	 * - Divvit_Tracking_Admin. Defines all hooks for the admin area.
	 * - `. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-divvit-tracking-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-divvit-tracking-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-divvit-tracking-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-divvit-tracking-public.php';

		$this->loader = new Divvit_Tracking_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Divvit_Tracking_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new Divvit_Tracking_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Divvit_Tracking_Admin( $this->get_Divvit_Tracking(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_filter( 'woocommerce_get_settings_general',$plugin_admin, 'add_divvit_tracking_settings', 10, 2 );
		$this->loader->add_action( 'woocommerce_settings_save_general', $plugin_admin, 'divvit_id_updated' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Divvit_Tracking_Public( $this->get_Divvit_Tracking(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'insert_divvit_tracking_script' );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'insert_divvit_order_tracking_script' );
		$this->loader->add_action( 'template_redirect', $plugin_public, 'exposed_divvit_order' );
		// $this->loader->add_action( 'woocommerce_ajax_added_to_cart', $plugin_public, 'divvit_add_cart_item' );
		// $this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'divvit_add_cart_item' );
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
	public function get_Divvit_Tracking() {
		return $this->Divvit_Tracking;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Divvit_Tracking_Loader    Orchestrates the hooks of the plugin.
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

	/**
	 * Get corresponding divvit tag/tracker url
	 *
	 * @since    1.0.4
	 * @access   public
	 */
	public function get_divvit_url($type = '')
	{
	    if ($type == 'tag') {
	        if (getenv('DIVVIT_TAG_URL') != '') {
	            return getenv('DIVVIT_TAG_URL');
	        } else {
	            return 'https://tag.divvit.com';
	        }
	    } else {
	        if (getenv('DIVVIT_TRACKING_URL') != '') {
	            return getenv('DIVVIT_TRACKING_URL');
	        } else {
	            return 'https://tracker.divvit.com';
	        }
	    }
	}

}
