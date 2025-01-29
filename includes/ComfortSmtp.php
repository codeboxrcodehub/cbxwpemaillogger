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
	 * Include necessary files
	 *
	 * @return void
	 */
	private function include_files() {
		require_once __DIR__ . '/../lib/autoload.php';
		// include_once __DIR__ . '/ComfortSmtpEmails.php';
	}//end method include_files

	/**
	 * On plugin activate
	 */
	public static function activate() {
		//set the current version
		update_option('comfortsmtp_version', COMFORTSMTP_PLUGIN_VERSION);


		( new self() )->migration_and_defaults();

		( new self() )->create_cron_job();
		//clear old plugin's event hook
		//wp_clear_scheduled_hook( 'cbxwpemaillogger_daily_event' );
		set_transient('comfortsmtp_upgraded_notice', 1);
	}//end method activate

	/**
	 * On plugin deactivate
	 */
	public static function deactivate() {
		wp_clear_scheduled_hook( 'cbxwpemaillogger_daily_event' );
	}//end method activate

	/**
	 * On plugin active or reset data set default data for this plugin
	 *
	 * @since 1.0.0
	 */
	public function default_data_set() {
		// create base/main upload directories
		$this->create_base_upload_directories();

		// add role and custom capability
		$this->defaultRoleCapability();
	}//end method default_data_set

	/**
	 * create base/main upload directories
	 */
	public function create_base_upload_directories() {
		ComfortSmtpHelpers::checkUploadDir();
	}//end method create_base_upload_directories

	/**
	 * create cron job
	 */
	public function create_cron_job() {
		$settings = new ComfortSmtpSettings();

		$delete_old_log = $settings->get_option( 'delete_old_log', 'comfortsmtp_log', 'no' );

		if ( $delete_old_log == 'yes' ) {
			if ( ! wp_next_scheduled( 'cbxwpemaillogger_daily_event' ) ) {
				wp_schedule_event( time(), 'daily', 'cbxwpemaillogger_daily_event' );
			}
		}
	}//end method create_cron_job

	/**
	 * On plugin activate
	 */
	public static function migration_and_defaults() {

		MigrationManage::run();

		//set default data
		( new self() )->default_data_set();

		//ComfortSmtpHelpers::upload_folder();

		MigrationManage::migrate_old_options();
	}//end method activate

	/**
	 * Create default role and capability on plugin activation and rest
	 *
	 * @since 1.0.0
	 */
	private function defaultRoleCapability() {
		//smtp capabilities list
		$caps = comfortsmtp_all_caps();

		//add the caps to the administrator role
		$role = get_role( 'administrator' );

		foreach ( $caps as $cap ) {
			//add cap to the role
			if ( ! $role->has_cap( $cap ) ) {
				// add a custom capability
				$role->add_cap( $cap, true );

			}

			//update the same cap for the current user who is installing or updating if logged in
			$this->update_user_capability($cap);
		}
	}//end method defaultRoleCapability

	/**
	 * Add any capability to the current user
	 *
	 * @param $capability_to_add
	 *
	 * @return void
	 */
	private function update_user_capability( $capability_to_add ) {
		// Check if a user is logged in.
		if ( is_user_logged_in() ) {
			// Get the current user object.
			$user = wp_get_current_user();

			// Check if the user already has the capability.
			if ( ! $user->has_cap( $capability_to_add ) ) {
				// Add the capability.
				$user->add_cap( $capability_to_add );

				// Optional: Log the capability addition (for debugging or auditing).
				//error_log( 'Added capability "' . $capability_to_add . '" to user: ' . $user->user_login );

				// Optional: Force a refresh of the user's capabilities (sometimes needed).
				wp_cache_delete( $user->ID, 'users' );
				wp_cache_delete( 'user_meta', $user->ID );

			} else {
				// Optional: Log that the user already has the capability.
				//error_log( 'User: ' . $user->user_login . ' already has capability: ' . $capability_to_add );
			}
		} else {
			// Optional: Handle the case where no user is logged in.
			//error_log( 'No user is logged in.' );
		}
	}//end method update_user_capability
}//end class ComfortSmtp