<?php

namespace Comfort\Crm\Smtp\Helpers;

use Comfort\Crm\Smtp\ComfortSmtpSettings;

use Comfort\Crm\Smtp\Models\SmtpLog;
//use Exception;
use Illuminate\Database\Capsule\Manager;
use Comfort\Crm\Smtp\MigrationManage;
use DateTime;

/**
 * Plugin helper
 */
class ComfortSmtpHelpers {
	/**
	 * Load ORM
	 *
	 * @since  1.0.0
	 */
	public static function load_orm() {
		global $wpdb;
		/**
		 * Init DB in ORM
		 */
		$capsule = new Manager();

		$connection_params = [
			'driver'   => 'mysql',
			'database' => DB_NAME,
			'username' => DB_USER,
			'password' => DB_PASSWORD,
			'prefix'   => $wpdb->prefix,
		];

		// Parse host and port
		$host = DB_HOST;
		$port = null;

		// Handle host like "localhost:3307"
		if ( strpos( $host, ':' ) !== false ) {
			[ $host, $port ] = explode( ':', $host, 2 );
		}

		$connection_params['host'] = $host;

		if ( ! empty( $port ) ) {
			$connection_params['port'] = (int) $port;
		}

		// Handle charset and collation
		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( DB_CHARSET ) ) {
				$connection_params['charset'] = DB_CHARSET;
			}
			if ( ! empty( DB_COLLATE ) ) {
				$connection_params['collation'] = DB_COLLATE;
			}
		}

		$capsule->addConnection( apply_filters( 'comfortsmtp_database_connection_params', $connection_params ) );

		$capsule->setAsGlobal();
		$capsule->bootEloquent();
	} //end method load_orm

	/**
	 * List all global option name with prefix comfortsmtp_
	 *
	 * @since 1.0.0
	 */
	public static function getAllOptionNames() {
		global $wpdb;

		$prefix = 'comfortsmtp_';
		//$option_names = $wpdb->get_results("SELECT * FROM {$wpdb->options} WHERE option_name LIKE '{$prefix}%'", ARRAY_A);

		$wild = '%';
		$like = $wpdb->esc_like( $prefix ) . $wild;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$option_names = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->options} WHERE option_name LIKE %s", $like ), ARRAY_A );

		return apply_filters( 'comfortsmtp_option_names', $option_names );
	} //end method getAllOptionNames

	/**
	 * Get all  core tables list
	 *
	 * @since 1.0.0
	 */
	public static function getAllDBTablesList() {
		global $wpdb;
		$table_names = [ $wpdb->prefix . 'comfortsmtp_log' ];

		return apply_filters( 'comfortsmtp_table_list', $table_names );
	} //end method getAllDBTablesList

	/**
	 * Get the user roles for voting purpose
	 *
	 * @param bool $plain
	 * @param bool $include_guest
	 * @param array $ignore
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function user_roles( $plain = true, $include_guest = false, $ignore = [] ) {
		global $wp_roles;

		if ( ! function_exists( 'get_editable_roles' ) ) {
			require_once( ABSPATH . '/wp-admin/includes/user.php' );
		}

		$userRoles = [];
		if ( $plain ) {
			foreach ( get_editable_roles() as $role => $roleInfo ) {
				if ( in_array( $role, $ignore ) ) {
					continue;
				}
				$userRoles[ $role ] = $roleInfo['name'];
			}
			if ( $include_guest ) {
				$userRoles['guest'] = esc_html__( 'Guest', 'cbxwpemaillogger' );
			}
		} else {
			//optgroup
			$userRoles_r = [];
			foreach ( get_editable_roles() as $role => $roleInfo ) {
				if ( in_array( $role, $ignore ) ) {
					continue;
				}
				$userRoles_r[ $role ] = $roleInfo['name'];
			}

			$userRoles = [
				'Registered' => $userRoles_r,
			];

			if ( $include_guest ) {
				$userRoles['Anonymous'] = [
					'guest' => esc_html__( 'Guest', 'cbxwpemaillogger' )
				];
			}
		}

		return apply_filters( 'comfortsmtp_user_roles', $userRoles, $plain, $include_guest );
	}

	/**
	 * common js translation and variable used resume plugin
	 *
	 * @param string $current_user
	 * @param string $blog_id
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public static function common_js_translation( $current_user = '', $blog_id = '' ) {
		$current_user = ( $current_user == '' ) ? wp_get_current_user() : $current_user;

		if ( $blog_id == '' ) {
			$blog_id = is_multisite() ? get_current_blog_id() : null;
		}

		$js_translations = [
			'icons_url'       => COMFORTSMTP_ROOT_URL . '/assets/icons/',
			'nonce'           => wp_create_nonce( 'comfortsmtp' ),
			'rest_nonce'      => wp_create_nonce( 'wp_rest' ),
			'dashboard_menus' => self::dashboard_menus(),
			'site_url'        => site_url(),
			'site_email'        => get_option('admin_email'),
			'translations'    => [
				'buttons'                   => [
					'close'  => [
						'title'    => esc_attr__( 'Click to close', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Close', 'cbxwpemaillogger' )
					],
					'search' => [
						'title'    => esc_attr__( 'Click to search', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Search', 'cbxwpemaillogger' )
					],
					'reset'  => [
						'title'    => esc_attr__( 'Click to reset', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Reset', 'cbxwpemaillogger' )
					],
					'filter' => [
						'title'    => esc_attr__( 'Column Filter', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Filter', 'cbxwpemaillogger' )
					],
					'view'   => [
						'title'    => esc_attr__( 'Click to view', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'View', 'cbxwpemaillogger' )
					],
					'clone'  => [
						'title'    => esc_attr__( 'Click to clone', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Clone', 'cbxwpemaillogger' )
					],
					'edit'   => [
						'title'    => esc_attr__( 'Click to edit', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Edit', 'cbxwpemaillogger' )
					],
					'delete' => [
						'title'    => esc_attr__( 'Click to delete', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Delete', 'cbxwpemaillogger' )
					],
					'gear'   => [
						'title'    => esc_attr__( 'Click to Configure', 'cbxwpemaillogger' ),
						'sr_label' => esc_html__( 'Configure', 'cbxwpemaillogger' )
					],
				],
				'cc'                        => esc_html__( 'CC', 'cbxwpemaillogger' ),
				'bcc'                       => esc_html__( 'BCC', 'cbxwpemaillogger' ),
				'export'                    => esc_html__( 'Export', 'cbxwpemaillogger' ),
				'total'                     => esc_html__( 'Total', 'cbxwpemaillogger' ),
				'id'                        => esc_html__( 'ID', 'cbxwpemaillogger' ),
				'action'                    => esc_html__( 'Action', 'cbxwpemaillogger' ),
				'delete_confirmation_title' => esc_html__( 'Are you sure?', 'cbxwpemaillogger' ),
				'delete_confirmation_txt'   => esc_html__( 'You want to delete this.', 'cbxwpemaillogger' ),
				'delete_btn_txt'            => esc_html__( 'Delete', 'cbxwpemaillogger' ),
				'showing'                   => esc_html__( 'Showing ', 'cbxwpemaillogger' ),
				'of'                        => esc_html__( 'of', 'cbxwpemaillogger' ),
				'rowCount'                  => esc_html__( 'Row count ', 'cbxwpemaillogger' ),
				'goTo'                      => esc_html__( 'Go to page ', 'cbxwpemaillogger' ),
				'delete'                    => esc_html__( 'Delete', 'cbxwpemaillogger' ),
				'status'                    => esc_html__( 'Status', 'cbxwpemaillogger' ),
				'attachment'                => esc_html__( 'Attachment', 'cbxwpemaillogger' ),
				'upload'                    => esc_html__( 'Upload', 'cbxwpemaillogger' ),
				'posted'                    => esc_html__( 'Posted', 'cbxwpemaillogger' ),
				'updated_date'              => esc_html__( 'Updated', 'cbxwpemaillogger' ),
				'updated_by'                => esc_html__( 'Updated By', 'cbxwpemaillogger' ),
				'created_by'                => esc_html__( 'Created By', 'cbxwpemaillogger' ),
				'updated_at'                => esc_html__( 'Updated At', 'cbxwpemaillogger' ),
				'created_at'                => esc_html__( 'Created At', 'cbxwpemaillogger' ),
				'back'                      => esc_html__( 'Back', 'cbxwpemaillogger' ),
				'user_list'                 => esc_html__( 'User List', 'cbxwpemaillogger' ),
				'listing'                   => [
					'select_role' => esc_html__( 'Select Role', 'cbxwpemaillogger' ),
					'user_list'   => [
						'id'     => esc_html__( 'ID', 'cbxwpemaillogger' ),
						'name'   => esc_html__( 'Name', 'cbxwpemaillogger' ),
						'email'  => esc_html__( 'Email', 'cbxwpemaillogger' ),
						'action' => esc_html__( 'Actions', 'cbxwpemaillogger' ),
					],
				],
				'copy_labels'               => [
					'copy_before' => esc_html__( 'Copy', 'cbxwpemaillogger' ),
					'copy_after'  => esc_html__( 'Copied', 'cbxwpemaillogger' )
				],
			],
			'user_roles'      => ComfortSmtpHelpers::user_roles( true, true ),
			'rest_end_points' => [
				'get_log'      => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/log-data' ) ),
				'get_log_list' => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/log-list' ) ),

				'delete_log'         => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/delete-log' ) ),
				'testing_submit_url' => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/test-email-submit' ) ),
				'email_resend'       => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/email-resend' ) ),
				'delete_old_log'     => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/delete-old-log' ) ),

				'reset_option'  => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/reset-option' ) ),
				'migrate_table' => esc_url_raw( get_rest_url( $blog_id, 'comfortsmtp/v1/migrate-table' ) ),
			],
			'cbx_table_lite'  => self::table_light_translation(),
			'mail_statuses'     => self::mailStatuses(),
			'is_comfortsmtppro_active' => self::is_comfortsmtppro_active(),
		];

		return $js_translations;
	} //end method common_js_translation

	/**
	 * form builder js translation list
	 *
	 * @param $current_user
	 * @param $blog_id
	 *
	 * @return mixed|void
	 */
	public static function comfortsmtp_log_builder_js_translation( $current_user, $blog_id ) {
		$common_js_translations = self::common_js_translation( $current_user, $blog_id );

		$form_js_translations = [
			'email_sources' => self::email_known_src(),
			'translations'  => [
				'filter_email_source' => esc_html__( 'Filter By Email Source', 'cbxwpemaillogger' ),
				'sent'                => esc_html__( 'Sent', 'cbxwpemaillogger' ),
				'log_id'              => esc_html__( 'Log ID', 'cbxwpemaillogger' ),
				'resend'              => esc_html__( 'Resend', 'cbxwpemaillogger' ),
				'failed'              => esc_html__( 'Failed', 'cbxwpemaillogger' ),
				'owner'               => esc_html__( 'Owner', 'cbxwpemaillogger' ),
				'to'                  => esc_html__( 'To', 'cbxwpemaillogger' ),
				'from'                => esc_html__( 'From', 'cbxwpemaillogger' ),
				'reply_to'            => esc_html__( 'Reply To', 'cbxwpemaillogger' ),
				'ip_address'          => esc_html__( 'IP address', 'cbxwpemaillogger' ),
				'error_message'       => esc_html__( 'Error message', 'cbxwpemaillogger' ),
				'mailer_api'          => esc_html__( 'Mailer API', 'cbxwpemaillogger' ),
				'api_status'          => esc_html__( 'Api Status', 'cbxwpemaillogger' ),
				'click_to_copy'       => esc_html__( 'Click to Copy', 'cbxwpemaillogger' ),
				'copy'                => esc_html__( 'Copy', 'cbxwpemaillogger' ),
				'add_new'             => esc_html__( 'Add New', 'cbxwpemaillogger' ),
				'name'                => esc_html__( 'Name', 'cbxwpemaillogger' ),
				'label'               => esc_html__( 'Label', 'cbxwpemaillogger' ),
				'value'               => esc_html__( 'Value', 'cbxwpemaillogger' ),
				'edit'                => esc_html__( 'Edit', 'cbxwpemaillogger' ),
				'options'             => esc_html__( 'Options', 'cbxwpemaillogger' ),
				'logs'                => esc_html__( 'Logs', 'cbxwpemaillogger' ),
				'title'               => esc_html__( 'Title', 'cbxwpemaillogger' ),
				'subject'             => esc_html__( 'Subject', 'cbxwpemaillogger' ),
				'search_text'         => esc_html__( 'Search', 'cbxwpemaillogger' ),
				'delete_all'          => esc_html__( 'Delete All', 'cbxwpemaillogger' ),
				'update'              => esc_html__( 'Update', 'cbxwpemaillogger' ),
				'close'               => esc_html__( 'Close', 'cbxwpemaillogger' ),
				'type'                => esc_html__( 'Type', 'cbxwpemaillogger' ),
				'datetime'            => esc_html__( 'DateTime', 'cbxwpemaillogger' ),
				'time'                => esc_html__( 'Time', 'cbxwpemaillogger' ),
				'date'                => esc_html__( 'Date', 'cbxwpemaillogger' ),
				'description'         => esc_html__( 'Description', 'cbxwpemaillogger' ),
				'placeholder'         => esc_html__( 'Placeholder', 'cbxwpemaillogger' ),
				'log_manager'         => esc_html__( 'Log Manager', 'cbxwpemaillogger' ),
				'no_log_found'        => esc_html__( 'No log found', 'cbxwpemaillogger' ),
				'select_status'       => esc_html__( 'Select status', 'cbxwpemaillogger' ),
				'email_body'          => esc_html__( 'Email Body', 'cbxwpemaillogger' ),
				'attachments'         => esc_html__( 'Attachments', 'cbxwpemaillogger' ),
				'n_a'                 => esc_html__( 'N\A', 'cbxwpemaillogger' ),
				'delete_old_log'      => esc_html__( 'Delete Old Log', 'cbxwpemaillogger' ),
				'preview'             => esc_html__( 'Preview', 'cbxwpemaillogger' ),
				'email_body_preview'  => esc_html__( 'Email Template/Body Preview', 'cbxwpemaillogger' ),
				'date_range'          => esc_html__( 'Date Range', 'cbxwpemaillogger' ),
			],
		];

		$js_translations = array_merge_recursive( $common_js_translations, $form_js_translations );

		return apply_filters( 'comfortsmtp_log_js_translation', $js_translations );
	} //end method comfortsmtp_log_builder_js_translation

	/**
	 * test email builder js translation list
	 *
	 * @param $current_user
	 * @param $blog_id
	 *
	 * @return mixed|void
	 */
	public static function comfortsmtp_test_email_js_translation( $current_user, $blog_id ) {
		$common_js_translations = self::common_js_translation( $current_user, $blog_id );

		$form_js_translations = [
			'translations' => [
				'email_testing' => esc_html__( 'Email Testing', 'cbxwpemaillogger' ),
				'send'          => esc_html__( 'Send', 'cbxwpemaillogger' ),
				'to'            => esc_html__( 'To', 'cbxwpemaillogger' ),
				'subject'       => esc_html__( 'Subject', 'cbxwpemaillogger' ),
				'body'          => esc_html__( 'Body', 'cbxwpemaillogger' ),
				'file'          => esc_html__( 'File', 'cbxwpemaillogger' ),
			],
		];

		$js_translations = array_merge_recursive( $common_js_translations, $form_js_translations );

		return apply_filters( 'comfortsmtp_log_js_translation', $js_translations );
	} //end method comfortsmtp_test_email_js_translation

	/**
	 * Tools js translation list
	 *
	 * @param $current_user
	 * @param $blog_id
	 *
	 * @return mixed|void
	 */
	public static function comfortsmtp_tools_js_translation( $current_user, $blog_id ) {

		$common_js_translations = self::common_js_translation( $current_user, $blog_id );

		$tools_js_translations = [
			'translations' => [
				'tools' => [
					'following_option_values' => esc_html__( 'Following option values created by this plugin(including addon) from WordPress core option table', 'cbxwpemaillogger' ),
					'check_all'               => esc_html__( 'Check All', 'cbxwpemaillogger' ),
					'uncheck_all'             => esc_html__( 'Uncheck All', 'cbxwpemaillogger' ),
					'option_name'             => esc_html__( 'Option Name', 'cbxwpemaillogger' ),
					'option_id'               => esc_html__( 'Option ID', 'cbxwpemaillogger' ),
					'reset_data'              => esc_html__( 'Reset Data', 'cbxwpemaillogger' ),
					'please_select_one'       => esc_html__( 'Please select at least one option', 'cbxwpemaillogger' ),
					'reset_option_data'       => esc_html__( 'Reset option data', 'cbxwpemaillogger' ),
					'show_hide'               => esc_html__( 'Show/Hide', 'cbxwpemaillogger' ),
					'done'                    => esc_html__( 'Done', 'cbxwpemaillogger' ),
					'need_migrate'            => esc_html__( 'Need to migrate', 'cbxwpemaillogger' ),
					'migration_files'         => esc_html__( 'Migration Files', 'cbxwpemaillogger' ),
					'run_migration'           => esc_html__( 'Run Migration', 'cbxwpemaillogger' ),
					'migration_file_name'     => esc_html__( 'Migration File Name', 'cbxwpemaillogger' ),
					'status'                  => esc_html__( 'Status', 'cbxwpemaillogger' ),
					'heading'                 => esc_html__( 'Smtp Manager: Tools', 'cbxwpemaillogger' ),
				],
			],
			'option_array' => self::getAllOptionNames(),
		];

		$js_translations = array_merge_recursive( $common_js_translations, $tools_js_translations );

		return apply_filters( 'comfortsmtp_tools_js_translation', $js_translations );
	} //end method comfortsmtp_tools_js_translation

	/**
	 * get date filter types
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public static function date_filter_type() {
		$date_filter = [
			'created_date' => esc_html__( 'Created Date', 'cbxwpemaillogger' ),
			'updated_date' => esc_html__( 'Updated Date', 'cbxwpemaillogger' ),
		];

		return apply_filters( 'comfortsmtp_date_filter_type', $date_filter );
	} //end of method date_filter_type

	/**
	 * Translation for table translation
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function table_light_translation() {
		return [
			'loading' => esc_html__( 'Loading...', 'cbxwpemaillogger' ),
			'first'   => esc_html__( 'First', 'cbxwpemaillogger' ),
			'prev'    => esc_html__( 'Prev', 'cbxwpemaillogger' ),
			'next'    => esc_html__( 'Next', 'cbxwpemaillogger' ),
			'last'    => esc_html__( 'Last', 'cbxwpemaillogger' ),
		];
	} //end of method table_light_translation


	/**
	 * return path info
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public static function uploadDirInfo( $id = 0 ) {
		$upload_dir = wp_upload_dir();

		//wordpress core base dir and url
		$upload_dir_basedir = $upload_dir['basedir'];
		$upload_dir_baseurl = $upload_dir['baseurl'];

		//comfortsmtp base dir and base url
		$comfortsmtp_base_dir = $upload_dir_basedir . '/cbxwpemaillogger/';
		$comfortsmtp_base_url = $upload_dir_baseurl . '/cbxwpemaillogger/';

		//comfortsmtp temp dir and temp url
		$comfortsmtp_temp_dir = $upload_dir_basedir . '/cbxwpemaillogger/temp/';
		$comfortsmtp_temp_url = $upload_dir_baseurl . '/cbxwpemaillogger/temp/';

		$comfortsmtp_folder_exists = 1;
		$tmp_folder_exists         = 1;

		$dir_info = [
			'comfortsmtp_folder_exists' => $comfortsmtp_folder_exists,
			'tmp_folder_exists'         => $tmp_folder_exists,
			'upload_dir_basedir'        => $upload_dir_basedir,
			'upload_dir_baseurl'        => $upload_dir_baseurl,
			'comfortsmtp_base_dir'      => $comfortsmtp_base_dir,
			'comfortsmtp_base_url'      => $comfortsmtp_base_url,
			'comfortsmtp_temp_dir'      => $comfortsmtp_temp_dir,
			'comfortsmtp_temp_url'      => $comfortsmtp_temp_url
		];

		$id = absint( $id );
		if ( $id == 0 ) {
			return $dir_info;
		}

		$comfortsmtp_files_dir = $comfortsmtp_base_dir . $id . '/';
		$comfortsmtp_files_url = $comfortsmtp_base_url . $id . '/';

		$id_dir_info = [
			'comfortsmtp_files_dir' => $comfortsmtp_files_dir,
			'comfortsmtp_files_url' => $comfortsmtp_files_url,
		];

		return $dir_info + $id_dir_info;
	} //end method uploadDirInfo

	/**
	 * make cbxpetition folder in uploads directory if not exist, return path info
	 *
	 * @return mixed|void
	 * @since 1.0.0
	 */
	public static function checkUploadDir( $id = 0 ) {
		$upload_dir = wp_upload_dir();

		//wordpress core base dir and url
		$upload_dir_basedir = $upload_dir['basedir'];
		$upload_dir_baseurl = $upload_dir['baseurl'];

		//comfortsmtp base dir and base url
		$form_base_dir = $upload_dir_basedir . '/cbxwpemaillogger/';
		$form_base_url = $upload_dir_baseurl . '/cbxwpemaillogger/';

		//comfortsmtp temp dir and temp url
		$form_temp_dir = $upload_dir_basedir . '/cbxwpemaillogger/temp/';
		$form_temp_url = $upload_dir_baseurl . '/cbxwpemaillogger/temp/';


		global $wp_filesystem;
		require_once( ABSPATH . '/wp-admin/includes/file.php' );
		WP_Filesystem();

		$form_folder_exists = 1;
		$tmp_folder_exists  = 1;

		if ( ! $wp_filesystem->exists( $form_base_dir ) ) {

			$created = $wp_filesystem->mkdir( $form_base_dir );
			if ( $created ) {
				$form_folder_exists = 1;
			} else {
				$form_folder_exists = 0;
			}
		}
		if ( ! $wp_filesystem->exists( $form_temp_dir ) ) {

			$created = $wp_filesystem->mkdir( $form_temp_dir );
			if ( $created ) {
				$tmp_folder_exists = 1;
			} else {
				$tmp_folder_exists = 0;
			}
		}

		$dir_info = [
			'comfortsmtp_folder_exists' => $form_folder_exists,
			'tmp_folder_exists'         => $tmp_folder_exists,
			'upload_dir_basedir'        => $upload_dir_basedir,
			'upload_dir_baseurl'        => $upload_dir_baseurl,
			'comfortsmtp_base_dir'      => $form_base_dir,
			'comfortsmtp_base_url'      => $form_base_url,
			'comfortsmtp_temp_dir'      => $form_temp_dir,
			'comfortsmtp_temp_url'      => $form_temp_url
		];

		$id = absint( $id );
		if ( $id == 0 ) {
			return $dir_info;
		}

		$form_files_dir = $form_base_dir . $id . '/';
		$form_files_url = $form_base_url . $id . '/';

		if ( ! $wp_filesystem->exists( $form_files_dir ) ) {

			$created = $wp_filesystem->mkdir( $form_files_dir );
			if ( $created ) {
				$form_folder_exists = 1;
			} else {
				$form_folder_exists = 0;
			}
		}


		$id_dir_info = [
			'comfortsmtp_files_dir' => $form_files_dir,
			'comfortsmtp_files_url' => $form_files_url,
		];

		return $dir_info + $id_dir_info;
	} //end method checkUploadDir

	/**
	 * Email type formats
	 *
	 * @return mixed|void
	 */
	public static function email_type_formats() {
		return apply_filters(
			'comfortsmtp_email_type_formats',
			[
				'html'  => esc_html__( 'Rich Html Email', 'cbxwpemaillogger' ),
				'plain' => esc_html__( 'Plain Text', 'cbxwpemaillogger' ),
			]
		);
	} //end method email_type_formats

	/**
	 * Get all the pages
	 *
	 * @return array page names with key value pairs
	 */
	public static function get_pages() {
		$pages         = get_pages();
		$pages_options = [];
		if ( $pages ) {
			foreach ( $pages as $page ) {
				$pages_options[ $page->ID ] = $page->post_title;
			}
		}

		return $pages_options;
	} //end method get_pages

	/**
	 * Set global setting sections
	 *
	 * @return mixed|null
	 */
	public static function get_settings_sections() {
		return apply_filters( 'comfortsmtp_setting_sections',
			[
				[
					'id'    => 'comfortsmtp_log',
					'title' => esc_html__( 'Email Log', 'cbxwpemaillogger' ),
				],
				[
					'id'    => 'comfortsmtp_email',
					'title' => esc_html__( 'Email Control', 'cbxwpemaillogger' ),
				],
				[
					'id'    => 'comfortsmtp_smtps',
					'title' => esc_html__( 'Email Sending', 'cbxwpemaillogger' ),
				],
			] );
	} //end method get_settings_sections

	/**
	 * Returns list of custom mailers
	 *
	 * @return mixed|void
	 */
	public static function getCustomMailer() {
		$custom_mailer = [];

		$custom_mailer['custom_smtp'] = esc_html__( 'Custom SMTP Host Server', 'cbxwpemaillogger' );

		return apply_filters( 'comfortsmtp_custom_mailer', $custom_mailer );
	}//end getCustomMailer

	/**
	 * Returns list of custom smtp host servers
	 *
	 * @param bool $enabled_only
	 *
	 * @return array
	 */
	public static function getSMTPHostServers( $enabled_only = false ) {
		$settings           = new ComfortSmtpSettings();
		$smtp_email_servers = $settings->get_option( 'smtp_email_servers', 'comfortsmtp_smtps', [] );
		if ( ! is_array( $smtp_email_servers ) ) {
			$smtp_email_servers = [];
		}

		$smtp_email_servers_list = [];

		if ( is_array( $smtp_email_servers ) && sizeof( $smtp_email_servers ) > 0 ) {
			$index = 0;

			foreach ( $smtp_email_servers as $smtp_email_server ) {
				$smtp_email_enable = isset( $smtp_email_server['smtp_email_enable'] ) ? intval( $smtp_email_server['smtp_email_enable'] ) : 0;
				$smtp_email_host   = isset( $smtp_email_server['smtp_email_host'] ) ? esc_attr( $smtp_email_server['smtp_email_host'] ) : '';
				$smtp_email_port   = isset( $smtp_email_server['smtp_email_port'] ) ? intval( $smtp_email_server['smtp_email_port'] ) : 0;

				if ( $enabled_only && $smtp_email_enable ) {
					/* translators: 1: index no 2. host name 3. port no  */
					$smtp_email_servers_list[ $index ] = sprintf( esc_html__( 'SMTP Host Servers #%1$d(%2$s, %3$s)', 'cbxwpemaillogger' ), ( $index + 1 ), $smtp_email_host, $smtp_email_port );
				}

				$index ++;
			}
		}

		return $smtp_email_servers_list;
	}//end getSMTPHostServers

	/**
	 * Returns smtp server config
	 *
	 * @return array
	 */
	public static function getSMTPHostServer( $index ) {
		$settings           = new ComfortSmtpSettings();
		$smtp_email_servers = $settings->get_option( 'smtp_email_servers', 'comfortsmtp_smtps', [] );
		if ( ! is_array( $smtp_email_servers ) ) {
			$smtp_email_servers = [];
		}

		return isset( $smtp_email_servers[ $index ] ) ? $smtp_email_servers[ $index ] : [];
	}//end getSMTPHostServer

	/**
	 * Returns all the settings fields
	 *
	 * @return mixed|null
	 */
	public static function get_settings_fields() {
		$custom_mailer = self::getCustomMailer();


		//$smtp_email_servers_default = CBXWPEmailLoggerHelper::smtp_email_servers_default(  );
		$smtp_email_servers_list = self::getSMTPHostServers( true );

		$settings_builtin_fields = [
			'comfortsmtp_log'   => [
				'log_heading' => [
					'name'    => 'log_heading',
					'label'   => esc_html__( 'Email Log Control', 'cbxwpemaillogger' ),
					'type'    => 'heading',
					'default' => '',
				],
				'email_log_enable'        => [
					'name'    => 'email_log_enable',
					'label'   => esc_html__( 'Email Log Control', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'Control Email logging, default is enabled on after plugin activated.', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 1,
				],
				'delete_old_log'          => [
					'name'              => 'delete_old_log',
					'label'             => esc_html__( 'Delete Old email logs', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'If enabled it will check everyday if there is any x days old emails. Number of days(x) is configured in next field. This plugin needs to deactivate and activate again to make this feature work.', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'radio',
					'options'           => [
						'yes' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
						'no'  => esc_html__( 'No', 'cbxwpemaillogger' ),
					],
					'default'           => 'no',
					'sanitize_callback' => 'esc_html',
				],
				'log_old_days'            => [
					'name'              => 'log_old_days',
					'label'             => esc_html__( 'Number of days', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Number of days email will be deleted as old based on email send date', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => '30',
					'sanitize_callback' => 'absint',
				],
				'enable_store_attachment' => [
					'name'    => 'enable_store_attachment',
					'label'   => esc_html__( 'Save Attachment Files', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'If enabled attachments will be stored. If log deleted attachments will be deleted from the stored location. Sometimes attachment are sent from dynamically generated contents which is deleted from memory after email is sent, if not stored separately then email resend feature will not be able to attach email. This feature is default disabled.', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 0,
				],
			],
			'comfortsmtp_email' => [
				'email_control_settings_heading' => [
					'name'    => 'email_control_settings_heading',
					'label'   => esc_html__( 'Email Control Settings', 'cbxwpemaillogger' ),
					'type'    => 'heading',
					'default' => '',
				],
				'email_smtp_enable'     => [
					'name'    => 'email_smtp_enable',
					'label'   => esc_html__( 'Control Email Sending', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . __( 'Control email sending, default is disabled on after plugin activated. <strong>If disabled, this plugin will not touch any email sending feature.</strong>', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'radio',
					'options' => [
						1 => esc_html__( 'Enable', 'cbxwpemaillogger' ),
						0 => esc_html__( 'Disable', 'cbxwpemaillogger' ),
					],
					'default' => 0,
				],
				'smtp_from_email'       => [
					'name'              => 'smtp_from_email',
					'label'             => esc_html__( 'Override From Email', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => sanitize_email( get_option( 'admin_email' ) ),
					'sanitize_callback' => 'sanitize_email',
				],
				'smtp_from_name'        => [
					'name'              => 'smtp_from_name',
					'label'             => esc_html__( 'Override From Name', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'Leave blank/empty to use default', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => esc_html( get_option( 'blogname' ) ),
					'sanitize_callback' => 'sanitize_text_field',
				],
				'smtp_email_returnpath' => [
					'name'              => 'smtp_email_returnpath',
					'label'             => esc_html__( 'Email Return path', 'cbxwpemaillogger' ),
					'desc'              => '<p>' . esc_html__( 'If blank will ignore', 'cbxwpemaillogger' ) . '</p>',
					'type'              => 'text',
					'default'           => '',
					'sanitize_callback' => 'sanitize_email',
				],
				'mailer'                => [
					'name'    => 'mailer',
					'label'   => esc_html__( 'Emailer', 'cbxwpemaillogger' ),
					'desc'    => '<p>' . esc_html__( 'Default is wordpress default', 'cbxwpemaillogger' ) . '</p>',
					'type'    => 'select',
					'default' => 'default',
					'options' => [
						'default' => esc_html__( 'WordPress Default', 'cbxwpemaillogger' ),
						'custom'  => esc_html__( 'Custom Mailer(Choose from Email Sending Tab)', 'cbxwpemaillogger' ),
					],
				],

			],
			'comfortsmtp_smtps' => [
				'mailer_settings_heading' => [
					'name'    => 'mailer_settings_heading',
					'label'   => esc_html__( 'Mailer Settings', 'cbxwpemaillogger' ),
					'type'    => 'heading',
					'default' => '',
				],
				'custom_mailer'        => [
					'name'              => 'custom_mailer',
					'label'             => esc_html__( 'Choose Custom Mailer', 'cbxwpemaillogger' ),
					'type'              => 'radio',
					'default'           => 'custom_smtp',
					'options'           => $custom_mailer,
					'sanitize_callback' => 'sanitize_text_field'
				],
				'mailer_server_settings_heading' => [
					'name'    => 'mailer_server_settings_heading',
					'label'   => esc_html__( 'Mail Server Settings', 'cbxwpemaillogger' ),
					'type'    => 'heading',
					'default' => '',
				],
				'smtp_email_servers' => [
					'name'      => 'smtp_email_servers',
					'label'     => esc_html__( 'SMTP Host Servers', 'cbxwpemaillogger' ),
					'type'      => 'repeat',
					'allow_new' => apply_filters( 'comfortsmtp_smtp_email_servers_allow_new', 0 ),
					'default'   => [
						'0' => [
							'smtp_email_enable'   => 1,
							'smtp_email_host'     => 'localhost',
							'smtp_email_port'     => '25',
							'smtp_email_secure'   => 'none',
							'smtp_email_auth'     => 0,
							'smtp_email_username' => '',
							'smtp_email_password' => '',
						]
					],
					'fields'    => [
						'smtp_email_enable'   => [
							'name'    => 'smtp_email_enable',
							'label'   => esc_html__( 'Enable Service', 'cbxwpemaillogger' ),
							'type'    => 'radio',
							'default' => 0,
							'options' => [
								'1' => esc_html__( 'Yes', 'cbxwpemaillogger' ),
								'0' => esc_html__( 'No', 'cbxwpemaillogger' )
							],
						],
						'smtp_email_host'     => [
							'name'    => 'smtp_email_host',
							'label'   => esc_html__( 'SMTP Host', 'cbxwpemaillogger' ),
							'type'    => 'text',
							'default' => 'localhost',
						],
						'smtp_email_port'     => [
							'name'              => 'smtp_email_port',
							'label'             => esc_html__( 'SMTP Port', 'cbxwpemaillogger' ),
							'type'              => 'text',
							'default'           => '25',
							'sanitize_callback' => 'absint',
						],
						'smtp_email_secure'   => [
							'name'    => 'smtp_email_secure',
							'label'   => esc_html__( 'SMTP Secure', 'cbxwpemaillogger' ),
							'type'    => 'select',
							'default' => 'none',
							'options' => [
								'none' => esc_html__( 'None(Port: 25)', 'cbxwpemaillogger' ),
								'ssl'  => esc_html__( 'SSL(Port: 465)', 'cbxwpemaillogger' ),
								'tls'  => esc_html__( 'TLS(Port: 465)', 'cbxwpemaillogger' ),
							],
						],
						'smtp_email_auth'     => [
							'name'    => 'smtp_email_auth',
							'label'   => esc_html__( 'SMTP Authentication', 'cbxwpemaillogger' ),
							'type'    => 'radio',
							'default' => 0,
							'options' => [
								0 => esc_html__( 'No', 'cbxwpemaillogger' ),
								1 => esc_html__( 'Yes', 'cbxwpemaillogger' ),
							],
						],
						'smtp_email_username' => [
							'name'              => 'smtp_email_username',
							'label'             => esc_html__( 'SMTP Username', 'cbxwpemaillogger' ),
							'type'              => 'text',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],
						'smtp_email_password' => [
							'name'              => 'smtp_email_password',
							'label'             => esc_html__( 'SMTP Password', 'cbxwpemaillogger' ),
							'type'              => 'password',
							'default'           => '',
							'sanitize_callback' => 'sanitize_text_field',
						],

					]
				],
				'smtp_email_server'  => [
					'name'    => 'smtp_email_server',
					'label'   => esc_html__( 'Choose SMTP Server', 'cbxwpemaillogger' ),
					'desc'    => esc_html__( 'List is showing only enabled servers', 'cbxwpemaillogger' ),
					'type'    => 'select',
					'default' => 0,
					'options' => $smtp_email_servers_list
				],
			],
		];

		$settings_builtin_fields = apply_filters( 'comfortsmtp_settings_fields', $settings_builtin_fields );

		$settings_fields = []; //final setting array that will be passed to different filters
		$sections        = ComfortSmtpHelpers::get_settings_sections();

		//check and make confirm every section has fields, if not then set blank array as fields
		foreach ( $sections as $section ) {
			if ( ! isset( $settings_builtin_fields[ $section['id'] ] ) ) {
				$settings_builtin_fields[ $section['id'] ] = [];
			}
		}

		foreach ( $sections as $section ) {
			$settings_fields[ $section['id'] ] = apply_filters(
				'comfortsmtp_global_' . $section['id'] . '_fields',
				$settings_builtin_fields[ $section['id'] ]
			);
		}

		$settings_fields = apply_filters( 'comfortsmtp_global_fields', $settings_fields ); //final filter if need

		return $settings_fields;

	} //end method get_settings_fields

	/**
	 * Load reset option table html
	 *
	 * @return string
	 */
	public static function setting_reset_html_table() {
		$option_values = ComfortSmtpHelpers::getAllOptionNames();

		$table_html = '<p style="margin-bottom: 15px;" id="comfortsmtp_plg_gfig_info"><strong>' . esc_html__( 'Following option values created by this plugin(including addon) from WordPress core option table',
				'cbxwpemaillogger' ) . '</strong></p>';

		$table_html .= '<p style="margin-bottom: 10px;" class="grouped gapless grouped_buttons" id="comfortsmtp_setting_options_check_actions"><a href="#" class="button primary comfortsmtp_setting_options_check_action_call">' . esc_html__( 'Check All',
				'cbxwpemaillogger' ) . '</a><a href="#" class="button outline comfortsmtp_setting_options_check_action_ucall">' . esc_html__( 'Uncheck All',
				'cbxwpemaillogger' ) . '</a></p>';
		$table_html .= '<table class="widefat widethin comfortsmtp_table_data" id="comfortsmtp_setting_options_table">
                        <thead>
                        <tr>
                            <th class="row-title">' . esc_attr__( 'Option Name', 'cbxwpemaillogger' ) . '</th>
                            <th>' . esc_attr__( 'Option ID', 'cbxwpemaillogger' ) . '</th>		
                        </tr>
                    </thead>';

		$table_html .= '<tbody>';

		$i = 0;
		foreach ( $option_values as $key => $value ) {
			$alternate_class = ( $i % 2 == 0 ) ? 'alternate' : '';
			$i ++;
			$table_html .= '<tr class="' . esc_attr( $alternate_class ) . '">
                                <td class="row-title"><input checked class="magic-checkbox reset_options" type="checkbox" name="reset_options[' . $value['option_name'] . ']" id="reset_options_' . esc_attr( $value['option_name'] ) . '" value="' . $value['option_name'] . '" />
                                    <label for="reset_options_' . esc_attr( $value['option_name'] ) . '">' . esc_attr( $value['option_name'] ) . '</td>
                                <td>' . esc_attr( $value['option_id'] ) . '</td>									
                            </tr>';
		}

		$table_html .= '</tbody>';
		$table_html .= '<tfoot>
                <tr>
                    <th class="row-title">' . esc_attr__( 'Option Name', 'cbxwpemaillogger' ) . '</th>
                    <th>' . esc_attr__( 'Option ID', 'cbxwpemaillogger' ) . '</th>				
                </tr>
                </tfoot>
            </table>';

		return $table_html;
	} //end method setting_reset_html_table

	/**
	 * Option names only
	 *
	 * @return array
	 */
	public static function getAllOptionNamesValues() {
		$option_values = self::getAllOptionNames();
		$names_only    = [];

		foreach ( $option_values as $key => $value ) {
			$names_only[] = $value['option_name'];
		}

		return $names_only;
	} //end method getAllOptionNamesValues

	/**
	 * Return terms page id
	 *
	 * @return int
	 */
	public static function terms_page_id() {
		return absint( ( new ComfortSmtpSettings() )->get_field( 'terms_page', 'comfortsmtp_page', 0 ) );
	}//end method terms_page_id


	/**
	 * Create files/directories.
	 *
	 * This function copied from woocommerce and modified
	 */
	public static function upload_folder() {
		// Bypass if filesystem is read-only and/or non-standard upload system is used.
		if ( apply_filters( 'comfortsmtp_install_skip_create_files', false ) ) {
			return;
		}

		// Install files and folders for uploading files and prevent hotlinking.
		$upload_dir = wp_upload_dir();
		//$download_method = get_option( 'woocommerce_file_download_method', 'force' );

		$files = [
			[
				'base'    => $upload_dir['basedir'] . '/cbxwpemaillogger',
				'file'    => 'index.html',
				'content' => '',
			],
			/* [
			 	'base'    => $upload_dir['basedir'] . '/comfortsmtp',
			 	'file'    => '.htaccess',
			 	'content' => 'deny from all',
			 ]*/
		];

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
		if ( ! WP_Filesystem( $creds ) ) {
			return; // Handle error
		}

		global $wp_filesystem;

		foreach ( $files as $file ) {
			// Ensure the directory exists
			if ( $wp_filesystem->mkdir( $file['base'] ) ) {
				$file_path = trailingslashit( $file['base'] ) . $file['file'];

				// Check if the file already exists to avoid overwriting
				if ( ! $wp_filesystem->exists( $file_path ) ) {
					// Write the content to the file
					$wp_filesystem->put_contents( $file_path, $file['content'], FS_CHMOD_FILE );
				}
			}
		}
	}//end method upload_folder


	/**
	 * Add utm params to any url
	 *
	 * @param string $url
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function url_utmy( $url = '' ) {
		if ( $url == '' ) {
			return $url;
		}

		return add_query_arg( [
			'utm_source'   => 'plgsidebarinfo',
			'utm_medium'   => 'plgsidebar',
			'utm_campaign' => 'wpfreemium',
		], $url );
	}//end method url_utmy

	/**
	 * dashboard menu list
	 *
	 * @since 1.0.0
	 */
	public static function dashboard_menus() {
		$menus = [];

		if ( current_user_can( 'comfortsmtp_log_manage' ) ) {
			$menus['comfortsmtps'] = [
				'url'        => admin_url( 'admin.php?page=comfortsmtp_log' ),
				'title-attr' => esc_html__( 'Manage Log', 'cbxwpemaillogger' ),
				'title'      => esc_html__( 'Log Manager', 'cbxwpemaillogger' ),
			];
		}

		if ( current_user_can( 'comfortsmtp_settings_manage' ) ) {
			$menus['comfortsmtp_settings'] = [
				'url'        => admin_url( 'admin.php?page=comfortsmtp_settings' ),
				'title-attr' => esc_html__( 'Manage Settings', 'cbxwpemaillogger' ),
				'title'      => esc_html__( 'Settings', 'cbxwpemaillogger' ),
			];
		}

		if ( current_user_can( 'comfortsmtp_settings_manage' ) ) {
			$menus['comfortsmtp_support'] = [
				'url'        => admin_url( 'admin.php?page=comfortsmtp_support' ),
				'title-attr' => esc_html__( 'Helps And Updates', 'cbxwpemaillogger' ),
				'title'      => esc_html__( 'Helps And Updates', 'cbxwpemaillogger' ),
			];
		}

		return $menus;
	}// end function dashboard_menus


	/**
	 * All migration files(may include file names from other addon or 3rd party addons))
	 *
	 * @return mixed
	 */
	public static function migration_files() {
		$migration_files = MigrationManage::migration_files();//migrations from core files

		return apply_filters( 'comfortsmtp_migration_files', $migration_files );
	}//end method migration_files

	/**
	 * Migration files left
	 *
	 * @return mixed
	 */
	public static function migration_files_left() {
		$migration_files_left = MigrationManage::migration_files_left();

		return apply_filters( 'comfortsmtp_migration_files_left', $migration_files_left );
	}//end method migration_files_left

	/**
	 * Get IP address
	 *
	 * @return string|void
	 */
	public static function get_ipaddress() {

		//phpcs:disabled
		if ( empty( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
			$ip_address = $_SERVER["REMOTE_ADDR"];
		} else {

			$ip_address = $_SERVER["HTTP_X_FORWARDED_FOR"];
		}
		//phpcs:enabled

		if ( strpos( $ip_address, ',' ) !== false ) {

			$ip_address = explode( ',', $ip_address );
			$ip_address = $ip_address[0];
		}

		return esc_attr( $ip_address );
	}//end get_ipaddress

	/**
	 * delete uploaded photos of the petition
	 *
	 * @param int $log_id
	 *
	 */
	public static function deleteLogFolder( $log_id = 0 ) {
		try {
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}

			$creds = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
			if ( ! WP_Filesystem( $creds ) ) {
				return false;
			}

			global $wp_filesystem;

			if ( $log_id ) {
				$folderPath = self::uploadDirInfo( $log_id )['comfortsmtp_files_dir'];
				$wp_filesystem->delete( $folderPath, true );

				return true;
			}
		} catch ( \Exception $e ) {
			if(function_exists('write_log')){
				write_log( $e->getMessage());
			}

			return false;
		}

		return false;
	}//end method deleteLogFolder

	/**
	 * Delete old logs based on the age in days
	 *
	 * @param int $log_old_days
	 */
	public static function delete_old_log( $log_old_days = 30 ) {
		// Get the date threshold
		//$date_threshold = now()->subDays($log_old_days);  // Subtract $log_old_days from the current date
		$date_threshold = ( new DateTime() )->modify( "-$log_old_days days" );

		// Fetch logs older than the threshold
		SmtpLog::where( 'date_created', '<=', $date_threshold )->delete();
	}//end method delete_old_log

	// Fix for overflowing signed 32 bit integers,
	// works for sizes up to 2^32-1 bytes (4 GiB - 1):
	public static function fix_integer_overflow( $size ) {
		if ( $size < 0 ) {
			$size += 2.0 * ( PHP_INT_MAX + 1 );
		}

		return $size;
	}//end fix_integer_overflow

	/**
	 * Clean label_for or id tad
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	public static function settings_clean_label_for( $str ) {
		$str = str_replace( '][', '_', $str );
		$str = str_replace( ']', '_', $str );
		$str = str_replace( '[', '_', $str );

		return $str;
	}//end settings_clean_label_for

	/**
	 * Known src, from which plugin email is sent
	 *
	 * @return mixed|void
	 */
	public static function email_known_src() {
		$src = [
			'contact-form-7' => esc_html__( 'Contact Form 7', 'cbxwpemaillogger' )
		];

		return apply_filters( 'comfortsmtp_known_src', $src );
	}//end email_known_src

	public static function getLogData( $search = '', $logdate = '', $emailsource = '', $status = - 1, $orderby = 'id', $order = 'DESC', $perpage = 20, $page = 1 ) {

		$filter             = [];
		$filter['limit']    = $perpage ? absint( $perpage ) : 20;
		$filter['page']     = $page ? absint( $page ) : 1;
		$filter['order_by'] = $orderby ?? 'id';
		$filter['sort']     = $order ?? 'desc';
		$filter['search']   = $search ?? null;
		$filter['status']   = $status ?? null;
		$filter['source']   = $emailsource ?? null;

		$logs = SmtpLog::query();

		if ( $filter['search'] ) {
			$logs = $logs->where( 'subject', 'LIKE', '%' . sanitize_text_field( $filter['search'] ) . '%' );
		}

		if ( isset( $filter['date'] ) && $filter['date'] && is_array( $filter['date'] ) ) {
			$logs = $logs->whereBetween( 'date_created', $filter['date'] );
		}

		if ( $filter['source'] ) {
			$logs = $logs->where( 'src_tracked', sanitize_text_field( $filter['source'] ) );
		}

		if ( isset( $filter['status'] ) && $filter['status'] != null ) {
			$logs = $logs->where( 'status', absint( $filter['status'] ) );
		}

		$logs = $logs->orderBy( $filter['order_by'], $filter['sort'] )->paginate( $filter['limit'], '*', 'page',
			$filter['page'] )->toArray();

		return isset( $logs['data'] ) ? $logs['data'] : [];
	}//end getLogData

	/**
	 * Get any plugin information
	 *
	 * @param $plugin_slug
	 *
	 * @return mixed|string
	 */
	public static function get_any_plugin_version( $plugin_slug = '' ) {
		if ( $plugin_slug == '' ) {
			return '';
		}

		// Ensure the required file is loaded
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Get all installed plugins
		$all_plugins = get_plugins();

		// Check if the plugin exists
		if ( isset( $all_plugins[ $plugin_slug ] ) ) {
			return $all_plugins[ $plugin_slug ]['Version'];
		}

		// Return false if the plugin is not found
		return '';
	}//end method get_pro_addon_version

	/**
	 * Returns list of mail status
	 *
	 * @return mixed|void
	 */
	public static function mailStatuses() {

		$statuses = [ 
			0 => esc_html__( 'Failed', 'cbxwpemaillogger' ),
			1 => esc_html__( 'Sent', 'cbxwpemaillogger' ),
			2 => esc_html__( 'Pending', 'cbxwpemaillogger' )
		];

		return apply_filters( 'comfortsmtp_mail_status', $statuses );
	}//end mailStatuses

	/**
	 * Check if the Comfort SmtpPro is active or not
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function is_comfortsmtppro_active() {
		if ( defined( 'COMFORTSMTPPRO_PLUGIN_NAME' ) && COMFORTSMTPPRO_PLUGIN_NAME ) {
			return true;
		} else {
			return false;
		}
	} //end of method is_comfortsmtppro_active

	/**
	 * Returns codeboxr news feeds using transient cache
	 *
	 * @return false|mixed|\SimplePie\Item[]|null
	 */
	public static function codeboxr_news_feed() {
		$cache_key   = 'codeboxr_news_feed_cache';
		$cached_feed = get_transient( $cache_key );

		$news = false;

		if ( false === $cached_feed ) {
			include_once ABSPATH . WPINC . '/feed.php'; // Ensure feed functions are available
			$feed = fetch_feed( 'https://codeboxr.com/feed?post_type=post' );

			if ( is_wp_error( $feed ) ) {
				return false; // Return false if there's an error
			}

			$feed->init();

			$feed->set_output_encoding( 'UTF-8' );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        // this is the encoding parameter, and can be left unchanged in almost every case
			$feed->handle_content_type();                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                // this double-checks the encoding type
			$feed->set_cache_duration( 21600 );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                          // 21,600 seconds is six hours
			$limit  = $feed->get_item_quantity( 10 );                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                     // fetches the 18 most recent RSS feed stories
			$items  = $feed->get_items( 0, $limit );
			$blocks = array_slice( $items, 0, 10 );

			$news = [];
			foreach ( $blocks as $block ) {
				$url   = $block->get_permalink();
				$url   = ComfortSmtpHelpers::url_utmy( esc_url( $url ) );
				$title = $block->get_title();

				$news[] = ['url' => $url, 'title' => $title];
			}

			set_transient( $cache_key, $news, HOUR_IN_SECONDS * 6 ); // Cache for 6 hours
		} else {
			$news = $cached_feed;
		}

		return $news;
	}//end method codeboxr_news_feed

	/**
	 * On plugin activate
	 */
	public static function activate() {
		//set the current version
		update_option('comfortsmtp_version', COMFORTSMTP_PLUGIN_VERSION);

		ComfortSmtpHelpers::migration_and_defaults();
		ComfortSmtpHelpers::create_cron_job();

		set_transient('comfortsmtp_activated_notice', 1);
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
	public static function default_data_set() {
		// create base/main upload directories
		ComfortSmtpHelpers::checkUploadDir();

		// add role and custom capability
		ComfortSmtpHelpers::defaultRoleCapability();
	}//end method default_data_set

	/**
	 * Create default role and capability on plugin activation and rest
	 *
	 * @since 1.0.0
	 */
	public static function defaultRoleCapability() {
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
			ComfortSmtpHelpers::update_user_capability($cap);
		}
	}//end method defaultRoleCapability

	/**
	 * Add any capability to the current user
	 *
	 * @param $capability_to_add
	 *
	 * @return void
	 */
	public static function update_user_capability( $capability_to_add ) {
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

	/**
	 * On plugin activate
	 */
	public static function migration_and_defaults() {
		MigrationManage::run();

		//set default data
		ComfortSmtpHelpers::default_data_set();

		//ComfortSmtpHelpers::upload_folder();

		MigrationManage::migrate_old_options();
	}//end method activate

	/**
	 * create cron job
	 */
	public static function create_cron_job() {
		$settings = new ComfortSmtpSettings();

		$delete_old_log = $settings->get_option( 'delete_old_log', 'comfortsmtp_log', 'no' );

		if ( $delete_old_log == 'yes' ) {
			if ( ! wp_next_scheduled( 'cbxwpemaillogger_daily_event' ) ) {
				wp_schedule_event( time(), 'daily', 'cbxwpemaillogger_daily_event' );
			}
		}
	}//end method create_cron_job
}//end class ComfortSmtpHelpers