<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://www.divvit.com
 * @since             1.0.1
 * @package           Divvit_Tracking
 *
 * @wordpress-plugin
 * Plugin Name:       Divvit Tracking Plugin
 * Plugin URI:        https://www.divvit.com
 * Description:       Integrate the Divvit tracking pixel into Woocommerce
 * Version:           1.0.3
 * Author:            Divvit AB
 * Author URI:        https://www.divvit.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       divvit-tracking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

function activate_Divvit_Tracking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-divvit-tracking-activator.php';
	Divvit_Tracking_Activator::activate();
}

function deactivate_Divvit_Tracking() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-divvit-tracking-deactivator.php';
	Divvit_Tracking_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Divvit_Tracking' );
register_deactivation_hook( __FILE__, 'deactivate_Divvit_Tracking' );

require plugin_dir_path( __FILE__ ) . 'includes/class-divvit-tracking.php';

/**
 * @since    1.0.0
 */
function run_Divvit_Tracking() {
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$plugin = new Divvit_Tracking();
		$plugin->run();
	}
}
run_Divvit_Tracking();
