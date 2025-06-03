<?php

use Comfort\Crm\Smtp\ComfortSmtpUninstall;

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       https://codeboxr.com
 * @since      1.0.0
 *
 * @package    ComfortResume
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}


/**
 * The code that runs during plugin uninstall.
 */
function uninstall_cbxwpemaillogger() {
	require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
	ComfortSmtpUninstall::uninstall();
}//end function uninstall_cbxwpemaillogger

if ( ! defined( 'COMFORTSMTP_PLUGIN_NAME' ) ) {
	uninstall_cbxwpemaillogger();
}
