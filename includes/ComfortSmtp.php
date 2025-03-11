<?php

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Comfort\Crm\Smtp\ComfortSmtpHooks;
use Comfort\Crm\Smtp\MigrationManage;
use Comfort\Crm\Smtp\ComfortSmtpSettings;

/**
 * Class Comfort form core
 */
final class ComfortSmtp {
	private static $instance = null;
	protected $hooks;

	/**
	 * The ID of this plugin.
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	public function __construct() {
		$this->plugin_name = COMFORTSMTP_PLUGIN_NAME;
		$this->version     = COMFORTSMTP_PLUGIN_VERSION;

		$this->include_files();

		$this->hooks = new ComfortSmtpHooks();
	}//end constructor

	/**
	 * Create instance
	 *
	 * @return ComfortSmtp|null
	 * @since 1.0.0
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}//end method instance

	/**
	 * Autoload inaccessible or non-existing properties on demand.
	 *
	 * @param $key
	 *
	 * @return void
	 */
	public function __get( $key ) {
		if ( in_array( $key, [ 'mailer' ], true ) ) {
			return $this->$key();
		}
	}//end magic method get

	/**
	 * Set the value of an inaccessible or non-existing property.
	 *
	 * @param string $key Property name.
	 * @param mixed $value Property value.
	 */
	public function __set( string $key, $value ) {
		if ( property_exists( $this, $key ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'Cannot access private property ComfortSmtp::$' . esc_html( $key ), E_USER_ERROR );
		} else {
			$this->$key = $value;
		}
	}//end magic mathod set

	/**
	 * Cloning is forbidden.
	 *
	 * @since 2.0.5
	 */
	public function __clone() {
		cbxmcratingreview_doing_it_wrong( __FUNCTION__, esc_html__( 'Cloning is forbidden.', 'cbxwpemaillogger' ), '2.0.5' );
	}//end method clone

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 2.0.5
	 */
	public function __wakeup() {
		cbxmcratingreview_doing_it_wrong( __FUNCTION__, esc_html__( 'Unserializing instances of this class is forbidden.', 'cbxwpemaillogger' ), '2.0.5' );
	}//end method wakeup

	/**
	 * Include necessary files
	 *
	 * @return void
	 */
	private function include_files() {
		require_once __DIR__ . '/../lib/autoload.php';
		// include_once __DIR__ . '/ComfortSmtpEmails.php';
	}//end method include_files










}//end class ComfortSmtp