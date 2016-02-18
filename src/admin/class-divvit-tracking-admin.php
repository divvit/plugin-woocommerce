<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://divvit.com
 * @since      1.0.0
 *
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Divvit_Tracking
 * @subpackage Divvit_Tracking/admin
 * @author     Johannes Bugiel <johannes@outofscope.io>
 */
class Divvit_Tracking_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $Divvit_Tracking    The ID of this plugin.
	 */
	private $Divvit_Tracking;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $Divvit_Tracking       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $Divvit_Tracking, $version ) {

		$this->Divvit_Tracking = $Divvit_Tracking;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->Divvit_Tracking, plugin_dir_url( __FILE__ ) . 'css/divvit-tracking-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->Divvit_Tracking, plugin_dir_url( __FILE__ ) . 'js/divvit-tracking-admin.js', array( 'jquery' ), $this->version, false );
	}

	public function add_divvit_tracking_settings( $settings ) {
		$settings_divvit = array();
		$settings_divvit[] = array( 'name' => __( 'Divvit Tracking Settings', 'text-domain' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure Divvit Tracking', 'divvit-tracking' ), 'id' => 'divvittracking' );
		$settings_divvit[] = array(
			'name'     => __( 'Divvit Frontend ID', 'divvit-tracking' ),
			'desc_tip' => __( 'This will add the frontend ID to your tracking pixel', 'divvit-tracking' ),
			'id'       => 'divvit_tracking_id',
			'type'     => 'text',
			'desc'     => __( 'Insert Frontend ID here', 'divvit-tracking' ),
		);
		$settings_divvit[] = array( 'type' => 'sectionend', 'id' => 'divvittracking' );
		return array_merge($settings,$settings_divvit);
	}
}
