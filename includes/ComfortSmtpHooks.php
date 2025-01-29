<?php

namespace Comfort\Crm\Smtp;

use Comfort\Crm\Smtp\ComfortSmtpPublic;
use Comfort\Crm\Smtp\Api\ComfortRoute;
use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Comfort\Crm\Smtp\Widgets\ComfortDashboardWidget;

class ComfortSmtpHooks {

	private $public;
	private $admin;
	private $api_routes;

	public function __construct() {
		$this->public     = new ComfortSmtpPublic();
		$this->admin      = new ComfortSmtpAdmin();
		$this->api_routes = new ComfortRoute();

		$this->define_common_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_plugin_hooks();//plugin maintenance related hooks
	}//end constructor

	/**
	 * Define common hooks
	 */
	private function define_common_hooks() {
		$route = $this->api_routes;
		$helper = new ComfortSmtpHelpers();

		add_action( 'plugins_loaded', [ $this, 'load_plugin_textdomain' ] );
		add_filter( 'script_loader_tag', [ $this, 'add_module_to_script' ], 10, 3 );

		add_action( 'rest_api_init', [ $route, 'init' ] );
		add_action( 'init', [ $helper, 'load_orm' ] );
		add_filter( 'robots_txt', [ $this, 'custom_robots_txt' ] );
	} //end method define_common_hooks

	/**
	 * Define admin hooks
	 */
	private function define_admin_hooks() {
		$plugin_admin = $this->admin;

		//create admin menu page
		add_action( 'admin_menu', [ $plugin_admin, 'create_menus' ] );
		add_action( 'admin_init', [ $plugin_admin, 'settings_init' ] );

		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_styles' ] );
		add_action( 'admin_enqueue_scripts', [ $plugin_admin, 'enqueue_scripts' ] );

		add_filter( 'wp_mail', [ $plugin_admin, 'insert_log' ] );
		add_action( 'wp_mail_failed', [ $plugin_admin, 'email_sent_failed' ], 10, 2 );
		add_action( 'bp_send_email_failure', [ $plugin_admin, 'email_sent_failed' ], 10, 2 );
		//bp_send_email_failure  for buddypress
		//added from v1.0.3
		add_filter( 'wp_mail_from', [ $plugin_admin, 'wp_mail_from_custom' ], 99999 );
		add_filter( 'wp_mail_from_name', [ $plugin_admin, 'wp_mail_from_name_custom' ], 99999 );
		add_filter( 'phpmailer_init', [ $plugin_admin, 'phpmailer_init_extend' ], 99999 );
		add_filter( 'bp_phpmailer_init', [ $plugin_admin, 'phpmailer_init_extend' ], 99999 );


		add_action( 'comfortsmtp_log_delete_after', [ $plugin_admin, 'delete_attachments_after_log_delete' ] );

		//cron event
		add_action( 'cbxwpemaillogger_daily_event', [
			$plugin_admin,
			'delete_old_log'
		] ); //delete x days old logs every day


		//for upgrade process
		//add_action( 'upgrader_process_complete', [ $plugin_admin, 'plugin_upgrader_process_complete' ], 10, 2 );
		add_action('plugins_loaded', [$plugin_admin, 'plugin_upgrader_process_complete']);
		add_action( 'admin_notices', [ $plugin_admin, 'plugin_activate_upgrade_notices' ] );
		add_filter( 'plugin_action_links_' . COMFORTSMTP_BASE_NAME, [ $plugin_admin, 'plugin_action_links' ] );
		add_filter( 'plugin_row_meta', [ $plugin_admin, 'plugin_row_meta' ], 10, 4 );

		//dashboard widget

		$dashboard_widget = new ComfortDashboardWidget();
		add_action( 'wp_dashboard_setup', [ $dashboard_widget, 'dashboard_widget' ] );

		//add new field in repeat fields
		add_action( 'wp_ajax_comfortsmtp_add_new_field', [
			$plugin_admin,
			'add_new_repeat_field'
		] );      //add new repeat field

		add_action( 'admin_menu', [ $plugin_admin, 'create_menus' ] );
		add_action( 'admin_init', [ $plugin_admin, 'settings_init' ] );

		add_action( 'wp_ajax_comfortsmtp_settings_reset_load', [ $plugin_admin, 'settings_reset_load' ] );
		add_action( 'wp_ajax_comfortsmtp_settings_reset', [ $plugin_admin, 'plugin_options_reset' ] );
		add_action( 'comfortsmtp_before_vuejs_mount_after', [ $plugin_admin, 'dropdown_menu_focusout_js' ] );
	}//end method define_admin_hooks

	/**
	 * Define public hooks
	 */
	private function define_public_hooks() {
		$plugin_public = $this->public;

		add_action( 'template_redirect', [ $plugin_public, 'email_log_body' ] );
		add_action( 'template_redirect', [ $plugin_public, 'download_attachment' ] ); //download attachment
	}//end method define_public_hooks

	/**
	 * Define plugin maintenance and upgrade related hooks
	 *
	 * @return void
	 */
	private function define_plugin_hooks() {
		add_action( 'after_plugin_row_cbxwpemailloggerpro/cbxwpemailloggerpro.php', [
			$this,
			'custom_message_after_plugin_row_proaddon'
		], 10, 2 );
	}//end method define_plugin_hooks


	/**
	 * Plugin textdomain
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'cbxwpemaillogger', false, COMFORTSMTP_ROOT_PATH . 'languages/' );
	} //load_plugin_textdomain

	/**
	 * Add module attribute to script loader
	 *
	 * @param $tag
	 * @param $handle
	 * @param $src
	 *
	 * @return mixed|string
	 */
	public function add_module_to_script( $tag, $handle, $src ) {
		$jsHandles = [
			'comfortsmtp_vue_dev',
			'comfortsmtp_vue_main',
			'comfortsmtp_form_vue_dev',
			'comfortsmtp_form_vue_main',
			'comfortsmtp_dashboard_vue_dev',
			'comfortsmtp_dashboard_vue_main',
			'comfortsmtp_tools_vue_dev',
			'comfortsmtp_tools_vue_main',
		];

		if ( in_array( $handle, $jsHandles ) ) {
			$tag = '<script type="module" id="' . $handle . '" src="' . esc_url( $src ) . '"></script>';
		}

		return $tag;
	}//end method add_module_to_script


	/**
	 * Tell bots not to index some created directories.
	 *
	 * We try to detect the default "User-agent: *" added by WordPress and add our rules to that group, because
	 * it's possible that some bots will only interpret the first group of rules if there are multiple groups with
	 * the same user agent.
	 *
	 * @param string $output The contents that WordPress will output in a robots.txt file.
	 *
	 * @return string
	 */
	private function custom_robots_txt( $output ) {
		$site_url = wp_parse_url( site_url() );
		$path     = ( ! empty( $site_url['path'] ) ) ? $site_url['path'] : '';

		$lines       = preg_split( '/\r\n|\r|\n/', $output );
		$agent_index = array_search( 'User-agent: *', $lines, true );

		if ( false !== $agent_index ) {
			$above = array_slice( $lines, 0, $agent_index + 1 );
			$below = array_slice( $lines, $agent_index + 1 );
		} else {
			$above   = $lines;
			$below   = [];
			$above[] = '';
			$above[] = 'User-agent: *';
		}

		$above[] = "Disallow: $path/wp-content/uploads/cbxwpemaillogger/";

		$lines = array_merge( $above, $below );

		return implode( PHP_EOL, $lines );
	}//end method custom_robots_txt

	/**
	 * Show plugin update
	 *
	 * @param $plugin_file
	 * @param $plugin_data
	 *
	 * @return void
	 */
	public function custom_message_after_plugin_row_proaddon( $plugin_file, $plugin_data ) {
		if ( $plugin_file !== 'cbxwpemailloggerpro/cbxwpemailloggerpro.php' ) {
			return;
		}

		if ( defined( 'COMFORTSMTPPRO_PLUGIN_NAME' ) ) {
			return;
		}

		//$pro_addon_version = ComfortSmtpHelpers::get_any_plugin_version('cbxwpemailloggerpro/cbxwpemailloggerpro.php');
		$pro_addon_version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '';


		if ( $pro_addon_version != '' && version_compare( $pro_addon_version, '1.0.1', '<' ) ) {
			// Custom message to display

			//$plugin_setting_url = admin_url( 'admin.php?page=cbxwpbookmark_settings#cbxwpbookmark_licences' );
			$plugin_manual_update = 'https://codeboxr.com/manual-update-pro-addon/';

			/* translators:translators: %s: plugin setting url for licence */
			$custom_message = wp_kses( sprintf( __( '<strong>Note:</strong> Comfort Email SMTP, Logger & Email Api Pro Addon is custom plugin. This plugin can not be auto update from dashboard/plugin manager. For manual update please check <a target="_blank" href="%1$s">documentation</a>. <strong style="color: red;">It seems this plugin\'s current version is older than 1.0.1. To get the latest pro addon features, this plugin needs to upgrade to 1.0.1 or later.</strong>', 'cbxwpemaillogger' ), esc_url( $plugin_manual_update ) ), [
				'strong' => [ 'style' => [] ],
				'a'      => [
					'href'   => [],
					'target' => []
				]
			] );

			// Output a row with custom content
			echo '<tr class="plugin-update-tr">
            <td colspan="3" class="plugin-update colspanchange">
                <div class="notice notice-warning inline">
                    ' . wp_kses_post( $custom_message ) . '
                </div>
            </td>
          </tr>';
		}
	}//end method custom_message_after_plugin_row_proaddon
}//end class ComfortSmtpAdmin