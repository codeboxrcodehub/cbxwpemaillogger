<?php

namespace Comfort\Crm\Smtp\Controllers;

use Comfort\Crm\Smtp\ComfortSmtpSettings;
use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use Comfort\Crm\Smtp\Models\SmtpLog;
use Exception;
use WP_REST_Request;
use WP_REST_Response;
use Illuminate\Database\QueryException;
use Rakit\Validation\Validator;

/**
 * Class LogController for email log listing manage
 */
class LogController {

	/**
	 * Get Email log List
	 *
	 * @return WP_REST_Response
	 */
	public function get_log_list( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		try {
			//01. Check if current user is logged in
			if ( ! is_user_logged_in() ) {
				throw new Exception( esc_html__( 'Unauthorized request', 'cbxwpemaillogger' ) );
			}

			//02. Check for user core capability
			if ( ! current_user_can( 'comfortsmtp_log_manage' ) ) {
				throw new \Exception( esc_html__( 'Sorry, you don\'t have enough permission to do this action', 'cbxwpemaillogger' ) );
			}

			global $wpdb;

			$data = $request->get_params();

			$filter             = [];
			$filter['limit']    = isset( $data['limit'] ) ? absint( $data['limit'] ) : 10;
			$filter['page']     = isset( $data['page'] ) ? absint( $data['page'] ) : 1;
			$filter['order_by'] = isset( $data['order_by'] ) ? sanitize_text_field( wp_unslash( $data['order_by'] ) ) : 'id';
			$filter['sort']     = $sort = isset( $data['sort'] ) ? sanitize_text_field( wp_unslash( $data['sort'] ) ) : 'desc';
			$filter['search']   = isset( $data['search'] ) ? sanitize_text_field( wp_unslash( $data['search'] ) ) : null;
			$filter['status']   = isset( $data['status'] ) ? sanitize_text_field( wp_unslash( $data['status'] ) ) : null;
			$filter['source']   = isset( $data['source'] ) ? sanitize_text_field( wp_unslash( $data['source'] ) ) : null;


			$order_by = $filter['order_by']; //todo: check if $order_by is within any allowed list
			$sort     = strtolower($filter['sort']);

			if(!in_array($sort, ['asc', 'desc']))

			if ( isset( $data['date'] ) && $data['date'] !== '' ) {
				if ( str_contains( $data['date'], ' to ' ) ) {
					$dates = explode( ' to ', $data['date'] );
				} else {
					$dates = $data['date'];
				}
				$filter['date'] = $dates;
			}


			$logs = SmtpLog::query();

			if ( $filter['search'] ) {
				$filter['search'] = $wpdb->esc_like( $filter['search'] );
				$logs             = $logs->where( 'subject', 'LIKE', '%' . $filter['search'] . '%' );
			}

			if ( isset( $filter['date'] ) && $filter['date'] && is_array( $filter['date'] ) ) {
				$logs = $logs->whereBetween( 'date_created', $filter['date'] );
			}

			if ( $filter['source'] ) {
				$logs = $logs->where( 'src_tracked', $filter['source'] );
			}

			if ( isset( $filter['status'] ) && $filter['status'] != null ) {
				$logs = $logs->where( 'status', absint( $filter['status'] ) );
			}

			$logs = $logs->orderBy( $filter['order_by'], $filter['sort'] )->paginate( $filter['limit'], '*', 'page',
				$filter['page'] )->toArray();

			// $response->set_data($logs);

			$response->set_data( [
				'success' => true,
				'data'    => $logs,
				'info'    => esc_html__( 'List of logs', 'cbxwpemaillogger' )
			] );

		} catch ( QueryException $e ) {
			// Check if the error is due to a missing table
			if ( str_contains( $e->getMessage(), 'Base table or view not found' ) ) {
				$response->set_data( [
					'info'    => esc_html__( 'Log table does not exist. Please check the database structure.', 'cbxwpemaillogger' ),
					'success' => false
				] );
			} else {
				$response->set_data( [
					'info'    => esc_html__( 'Something Went Wrong. Please try again later.', 'cbxwpemaillogger' ),
					'success' => false
				] );
			}
		} catch ( Exception $e ) {
			$response->set_data( [
				'info'    => esc_html__( 'Something Went Wrong. Please try again later.', 'cbxwpemaillogger' ),
				'success' => false
			] );
		}

		return $response;
	} //end method get_log_list


	/**
	 * Get log fields data
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 * @throws Exception
	 */
	public function get_log_data( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		//01. Check if current user is logged in
		if ( ! is_user_logged_in() ) {
			throw new \Exception( esc_html__( 'Unauthorized request', 'cbxwpemaillogger' ) );
		}

		//02. Check for user core capability
		if ( ! current_user_can( 'comfortsmtp_log_view' ) ) {
			throw new \Exception( esc_html__( 'Sorry, you don\'t have enough permission to do this action', 'cbxwpemaillogger' ) );
		}

		if ( isset( $request['id'] ) ) {
			$log = SmtpLog::where( 'id', absint($request['id']) )->first();

			if ( $log ) {
				$response->set_data( $log );
			} else {
				$response->set_data( [ 'error' => esc_html__( 'Log Not Found', 'cbxwpemaillogger' ) ] );
			}
		}

		return $response;
	}//end method get_log_data

	/**
	 * Log delete
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function destroy( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		$success_count = $fail_count = 0;

		try {
			if ( ! is_user_logged_in() ) {
				throw new Exception( esc_html__( 'Unauthorized', 'cbxwpemaillogger' ) );
			}

			//02. Check for user core capability
			if ( ! current_user_can( 'comfortsmtp_log_delete' ) ) {
				throw new \Exception( esc_html__( 'Sorry, you don\'t have enough permission to do this action', 'cbxwpemaillogger' ) );
			}

			$data = $request->get_params();
			if ( empty( $data['id'] ) ) {
				throw new Exception( esc_html__( 'Log id is required.', 'cbxwpemaillogger' ) );
			}


			if ( is_array( $data['id'] ) && count( $data['id'] ) ) {
				foreach ( $data['id'] as $id ) {
					$log = SmtpLog::query()->find( absint( $id ) );
					if ( $log ) {
						if ( $log->delete() ) {
							$success_count ++;
						} else {
							$fail_count ++;
						}
					}
				}
			} else {
				$log = SmtpLog::query()->find( intval( $data['id'] ) );
				if ( $log ) {
					if ( $log->delete() ) {
						$success_count ++;
					} else {
						$fail_count ++;
					}

				}
			}

			$success_msg = $fail_msg = '';
			if ( $success_count > 0 ) {
				/* translators: %d: log successfully deleted count */
				$success_msg = sprintf( esc_html__( '%d log(s) deleted successfully. ', 'cbxwpemaillogger' ), $success_count );

			}

			if ( $fail_count > 0 ) {
				/* translators: %d: log delete fail count */
				$fail_msg = sprintf( esc_html__( '%d log(s) can`t be deleted as they may have dependency.', 'cbxwpemaillogger' ), $fail_count );

			}

			$response->set_data( [
				'success' => true,
				'info'    => $success_msg . $fail_msg
			] );

			return $response;

		} catch ( Exception $e ) {
			$response->set_data( [
				'success' => false,
				'err'     => $e->getMessage(),
				/* translators: 1: Success count 2. Fail count  */
				'info'    => sprintf( esc_html__( 'Incomplete deletion. %1$d successfully and %2$d failed', 'cbxwpemaillogger' ), $success_count, $fail_count ),
			] );

			return $response;
		}
	} //end method destroy

	/**
	 * @param $id
	 *
	 * @return bool|mixed|null
	 * @throws Exception
	 */
	/*private function singleDelete( $id ) {
		$log = SmtpLog::find( $id );
		if ( ! is_array( $log ) && ! count( $log ) ) {
			throw new Exception( esc_html__( 'Log not found', 'cbxwpemaillogger' ) );
		}

		do_action( 'comfortsmtp_before_delete_log', $log );

		$is_delete = SmtpLog::query()->find( $id )->delete();

		do_action( 'comfortsmtp_after_delete_log', true, intval( $id ) );

		return $is_delete;
	} //end method singleDelete*/


	/**
	 * Delete old logs
	 *
	 * @return WP_REST_Response
	 * @throws Exception
	 */
	public function deleteOldLog( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		try {

			//01. Check if current user is logged in
			if ( ! is_user_logged_in() ) {
				throw new Exception( esc_html__( 'Unauthorized request', 'cbxwpemaillogger' ) );
			}

			//02. Check for user core capability
			if ( ! current_user_can( 'comfortsmtp_settings_manage' ) ) {
				throw new \Exception( esc_html__( 'Sorry, you don\'t have enough permission to do this action', 'cbxwpemaillogger' ) );
			}

			$settings = new ComfortSmtpSettings();
			$old_days = absint($settings->get_field('log_old_days', 'comfortsmtp_log', 30));


			ComfortSmtpHelpers::delete_old_log( $old_days );

			$response->set_data( [
				'success' => true,
				'info'    => esc_html__( 'Deleted Successfully', 'cbxwpemaillogger' ),
			] );
		} catch ( Exception ) {

		}

		return $response;
	} //end method deleteOldLog

	/**
	 * send test email
	 *
	 * @param WP_REST_Request $request
	 *
	 * @return WP_REST_Response
	 */
	public function testEmail( WP_REST_Request $request ) {

		$response = new WP_REST_Response();
		$response->set_status( 200 );

		$success_count = $fail_count = 0;

		try {
			if ( ! is_user_logged_in() || ! current_user_can( 'comfortsmtp_settings_manage' ) ) {
				throw new Exception( esc_html__( 'Unauthorized', 'cbxwpemaillogger' ) );
			}

			$post_data = $request->get_params();

			$to      = isset( $post_data['to'] ) ? sanitize_email( $post_data['to'] ) : '';
			$subject = isset( $post_data['subject'] ) ? sanitize_text_field( wp_unslash($post_data['subject']) ) : '';
			$message = isset( $post_data['message'] ) ? sanitize_textarea_field( wp_unslash($post_data['message']) ) : '';

			$cc  = isset( $post_data['cc'] ) ? sanitize_text_field( wp_unslash($post_data['cc']) ) : '';
			$bcc = isset( $post_data['bcc'] ) ? sanitize_text_field( wp_unslash($post_data['bcc']) ) : '';

			// Split the CC and BCC into arrays by comma
			$cc  = array_filter( array_map( 'sanitize_email', explode( ',', $cc ) ) );
			$bcc = array_filter( array_map( 'sanitize_email', explode( ',', $bcc ) ) );

			$headers = [];

			// Add CC headers if any valid CC emails are present
			if ( ! empty( $cc ) ) {
				foreach ( $cc as $email ) {
					if ( is_email( $email ) ) {
						$headers[] = 'cc: ' . $email;
					}
				}
			}

			// Add BCC headers if any valid BCC emails are present
			if ( ! empty( $bcc ) ) {
				foreach ( $bcc as $email ) {
					if ( is_email( $email ) ) {
						$headers[] = 'bcc: ' . $email;
					}
				}
			}

			$file = $request->get_file_params();

			$validator = new Validator;

			$validation = $validator->validate( $post_data, [
				'to'      => 'required',
				'subject' => 'required',
				'message' => 'required'
			] );

			if ( $validation->fails() ) {
				$errors = $validation->errors();
				$response->set_data( [
					'success' => false,
					'errors'  => $errors->firstOfAll(),
					'info'    => esc_html__( 'Server error', 'cbxwpemaillogger' ),
				] );

				return $response;
			}

			$email_success = 0;

			try {
				$attachments = [];

				if ( isset( $file['file'] ) ) {
					if ( ! function_exists( 'wp_handle_upload' ) ) {
						require_once( ABSPATH . 'wp-admin/includes/file.php' );
					}

					$uploaded_file = $file['file'];

					$upload_overrides = [
						'test_form' => false
					];

					$move_file = wp_handle_upload( $uploaded_file, $upload_overrides );

					if ( $move_file && ! isset( $move_file['error'] ) ) {
						$attachments[] = $move_file['file'];
						$headers[]     = 'Content-Type: text/html; charset=UTF-8';

					} else {
						//
						/*
							* Error generated by _wp_handle_upload()
							* @see _wp_handle_upload() in wp-admin/includes/file.php
							*/
						//echo $movefile['error'];
					}
				}

				// \Comfort\Crm\SmtpPro\Helpers\ComfortSmtpProHelpers::send_email_via_sendgrid($to, $subject, $message, $headers);

				//send email
				$status = wp_mail( $to, $subject, $message, $headers, $attachments );

				if ( $status ) {
					$response->set_data( [
						'success' => true,
						'info'    => esc_html__( 'Test Email sent.', 'cbxwpemaillogger' )
					] );

					$email_success = 1;

					//let's delete the uploaded file
					if ( sizeof( $attachments ) > 0 ) {
						foreach ( $attachments as $attachment ) {
							wp_delete_file( $attachment );
						}
					}
				} else {
					$response->set_data( [
						'success' => false,
						'info'    => esc_html__( 'Email sent failed.', 'cbxwpemaillogger' )
					] );
				}
			} catch ( Exception $e ) {
				$response->set_data( [
					'success' => false,
					'info'    => esc_html__( 'Email sent failed. Error:', 'cbxwpemaillogger' ).esc_html($e->getMessage())
				] );
			}

			update_option( 'comfortsmtp_testmsg', [ 'message' => $response, 'type' => intval( $email_success ) ] );

			return $response;

		} catch ( Exception $e ) {
			$response->set_data( [
				'success' => false,
				'err'     => $e->getMessage(),
				'info'    => esc_html__( 'Email sent failed', 'cbxwpemaillogger' ),
			] );

			return $response;
		}
	} //end method testEmail

	/**
	 * Email log resend ajax handle
	 */
	public function emailResend( WP_REST_Request $request ) {
		$response = new WP_REST_Response();
		$response->set_status( 200 );

		//only logged in user and user who has option change capability can change this.
		if ( is_user_logged_in() && current_user_can( 'comfortsmtp_settings_manage' ) ) {

			$data = $request->get_params();

			$id = isset( $data['id'] ) ? absint( $data['id'] ) : 0;

			if ( $id > 0 ) {

				$item = SmtpLog::find( $id );

				if ( $item ) {
					$item = $item->toArray();

					$email_data = maybe_unserialize( $item['email_data'] );

					$atts = isset( $email_data['atts'] ) ? $email_data['atts'] : [];


					if ( is_array( $atts ) && sizeof( $atts ) > 0 ) {

						[ $to, $subject, $message, $headers, $attachments ] = array_values( $atts );

						$attachments_t = [];
						if ( is_array( $attachments ) && sizeof( $attachments ) > 0 ) {
							$dir_info = ComfortSmtpHelpers::uploadDirInfo( $id );

							global $wp_filesystem;
							require_once( ABSPATH . '/wp-admin/includes/file.php' );
							WP_Filesystem();

							$log_folder_dir = $dir_info['comfortsmtp_base_dir'] . $id;

							foreach ( $attachments as $attachment ) {
								$file_name = basename( $attachment );

								if ( $wp_filesystem->exists( $log_folder_dir . '/' . $file_name ) ) {
									$attachments_t[] = $log_folder_dir . '/' . $file_name;
								}
							}

							$attachments = $attachments_t;
						}

						$email_type = esc_attr( $item['email_type'] );
						set_transient( 'comfortsmtp_resend_filter_mail_content_type', $email_type );

						add_filter( 'wp_mail_content_type', [ $this, 'resend_filter_mail_content_type' ] );
						$report = wp_mail( $to, $subject, $message, $headers, $attachments );
						remove_filter( 'wp_mail_content_type', [ $this, 'resend_filter_mail_content_type' ] );

						if ( $report ) {
							$response->set_data( [
								'info'    => esc_html__( 'Email sent successfully.', 'cbxwpemaillogger' ),
								'success' => true
							] );

						} else {
							$response->set_data( [
								'info'    => esc_html__( 'Email Resend failed.', 'cbxwpemaillogger' ),
								'success' => false
							] );
						}

						return $response;
					}
				}
			}
		}//if user allowed

		$response->set_data( [
			'info'    => esc_html__( 'Failed to send or not enough access to send', 'cbxwpemaillogger' ),
			'success' => false
		] );

		return $response;
	}//end emailResend

	/**
	 * Send email same origin content type format while resending
	 *
	 * @param string $content_type
	 *
	 * @return mixed|string
	 */
	public function resend_filter_mail_content_type( $content_type = 'text/plain' ) {
		$email_type = get_transient( 'comfortsmtp_resend_filter_mail_content_type' );
		if ( $email_type !== false ) {
			delete_transient( 'comfortsmtp_resend_filter_mail_content_type' );

			return $email_type;
		}

		return $content_type;
	}//end resend_filter_mail_content_type
}//end class LogController