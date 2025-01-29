<?php

namespace Comfort\Crm\Smtp;

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Comfort\Crm\Smtp\Models\SmtpLog;

/**
 *
 */
class ComfortSmtpPublic {
	public string $version = '';
	public $settings = null;

	public function __construct() {
		$this->version  = COMFORTSMTP_PLUGIN_VERSION;
		$this->settings = new ComfortSmtpSettings();
	}//end of constructor

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function public_styles() {


	}//end method public_styles

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function public_scripts() {

	} //end method public_scripts

	/**
	 * Ajax email template viewer
	 */
	public function email_log_body() {
		//phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST['action'] ) && esc_attr( sanitize_text_field( wp_unslash($_REQUEST['action']) ) ) == 'comfortsmtp_log_body' && is_user_logged_in() && current_user_can( 'comfortsmtp_log_manage' ) ) {
			if ( ! wp_verify_nonce( sanitize_text_field(wp_unslash( $_REQUEST['_wpnonce'] )), 'comfortsmtp' ) ) { //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated
				// This nonce is not valid.
				die( 'Security check' );
			} else {
				$id = isset( $_REQUEST['id'] ) ? absint( $_REQUEST['id'] ) : 0;


				$item = SmtpLog::find( $id );

				if ( $item ) {
					$email_data = maybe_unserialize( $item['email_data'] );
					$body       = isset( $email_data['body'] ) ? $email_data['body'] : '';
					echo $body; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					exit;
				}
			}

			// die();
		}
	}//end email_log_body

	/**
	 * Download attachments
	 */
	public function download_attachment() {
		if ( isset( $_REQUEST['action'] ) && sanitize_text_field( wp_unslash($_REQUEST['action']) )  == 'comfortsmtp_download_attachment') {

			check_ajax_referer( 'comfortsmtp', '_wpnonce' );

			//only logged in user and user who has option change capability can change this.
			if ( is_user_logged_in() && current_user_can( 'comfortsmtp_log_manage' ) ) {
				$log_id = isset( $_REQUEST['log_id'] ) ? absint( $_REQUEST['log_id'] ) : 0;
				$file   = isset( $_REQUEST['file'] ) ? sanitize_text_field( wp_unslash($_REQUEST['file']))  : '';

				if ( $log_id > 0 && $file != '' ) {

					$dir_info = ComfortSmtpHelpers::checkUploadDir();
					global $wp_filesystem;
					require_once( ABSPATH . '/wp-admin/includes/file.php' );
					WP_Filesystem();

					$file_path = $dir_info['comfortsmtp_base_dir'] . $log_id . '/' . $file;

					if ( $wp_filesystem->exists( $file_path ) ) {
						// Prevent browsers from MIME-sniffing the content-type:
						header( 'X-Content-Type-Options: nosniff' );

						header( 'Content-Type: application/octet-stream' );
						header( 'Content-Disposition: attachment; filename="' . $file . '"' );
						//header( 'Content-Length: ' . ComfortSmtpHelpers::fix_integer_overflow( filesize( $file_path ) ) );
						header( 'Content-Length: ' . ComfortSmtpHelpers::fix_integer_overflow( $wp_filesystem->size( $file_path ) ) );
						//header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s T', filemtime( $file_path ) ) );
						header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s T', $wp_filesystem->mtime( $file_path ) ) );
						//readfile( $file_path );
						echo $wp_filesystem->get_contents($file_path); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
				}
			}//if user loggedin and has permission to manage options

			die();
		}
	}//end download_attachment
}//end class ComfortSmtpPublic