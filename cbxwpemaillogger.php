<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://codeboxr.com
 * @since             1.0.0
 * @package           ComfortSmtp
 *
 * @wordpress-plugin
 * Plugin Name:       Comfort Email SMTP, Logger & Email Api
 * Plugin URI:        https://codeboxr.com/product/cbx-email-logger-for-wordpress/
 * Description:       Various SMTP protocol, Logs email, tracks sent or failed status and more.
 * Version:           2.0.5
 * Requires at least: 5.3
 * Requires PHP:      8.2
 * Author:            Codeboxr
 * Author URI:        https://codeboxr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cbxwpemaillogger
 * Domain Path:       /languages
 */

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

defined( 'COMFORTSMTP_PLUGIN_NAME' ) or define( 'COMFORTSMTP_PLUGIN_NAME', 'cbxwpemaillogger' );
defined( 'COMFORTSMTP_PLUGIN_VERSION' ) or define( 'COMFORTSMTP_PLUGIN_VERSION', '2.0.5' );
defined( 'COMFORTSMTP_BASE_NAME' ) or define( 'COMFORTSMTP_BASE_NAME', plugin_basename( __FILE__ ) );
defined( 'COMFORTSMTP_ROOT_PATH' ) or define( 'COMFORTSMTP_ROOT_PATH', plugin_dir_path( __FILE__ ) );
defined( 'COMFORTSMTP_ROOT_URL' ) or define( 'COMFORTSMTP_ROOT_URL', plugin_dir_url( __FILE__ ) );


defined( 'CBX_DEBUG' ) or define( 'CBX_DEBUG', false );
defined( 'COMFORTSMTP_DEV_MODE' ) or define( 'COMFORTSMTP_DEV_MODE', CBX_DEBUG );


// Include the main Cbx class.
if ( ! class_exists( 'ComfortSmtp', false ) ) {
	include_once COMFORTSMTP_ROOT_PATH . 'includes/ComfortSmtp.php';
}

/**
 * Checking wp version
 *
 * @return bool
 */
function comfortsmtp_compatible_wp_version( $version = '5.3' ) {
	if ( version_compare( $GLOBALS['wp_version'], $version, '<' ) ) {
		return false;
	}

	// Add sanity checks for other version requirements here

	return true;
}//end function comfortsmtp_compatible_wp_version

/**
 * Checking php version
 *
 * @return bool
 */
function comfortsmtp_compatible_php_version( $version = '8.2' ) {
	if ( version_compare( PHP_VERSION, $version, '<=' ) ) {
		return false;
	}

	return true;
}//end function comfortsmtp_compatible_php_version

/**
 * The code that runs during plugin activation.
 */
function activate_comfortsmtp() {
	$wp_version  = '5.3';
	$php_version = '8.2';

	if ( ! comfortsmtp_compatible_wp_version( $wp_version ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		/* Translators:  WordPress Version */
		wp_die( sprintf( esc_html__( 'Comfort form plugin requires WordPress %s or higher!', 'cbxwpemaillogger' ), esc_html($wp_version) ) );
	}

	if ( ! comfortsmtp_compatible_php_version( $php_version ) ) {
		deactivate_plugins( plugin_basename( __FILE__ ) );
		/* Translators:  PHP Version */
		wp_die( sprintf( esc_html__( 'Comfort form plugin requires PHP %s or higher!', 'cbxwpemaillogger' ), esc_html($php_version) ) );
	}

	ComfortSmtpHelpers::load_orm();

	ComfortSmtp::activate();
}//end function activate_comfortsmtp

register_activation_hook( __FILE__, 'activate_comfortsmtp' );


/**
 * The code that runs during plugin deactivation.
 */
function deactivate_comfortsmtp() {
	ComfortSmtp::deactivate();
}//end function deactivate_comfortsmtp

register_deactivation_hook( __FILE__, 'deactivate_comfortsmtp' );


/**
 * Returns the main instance of WC.
 *
 * @since  1.0
 */
function comfortsmtp() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	global $comfortsmtp_core;
	if ( ! isset( $comfortsmtp_core ) ) {
		$comfortsmtp_core = run_comfortsmtp_core();
	}

	return $comfortsmtp_core;
}//end method comfortsmtp

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_comfortsmtp_core() {
	return ComfortSmtp::instance();
}//end function run_comfortsmtp_core

$GLOBALS['comfortsmtp_core'] = run_comfortsmtp_core();