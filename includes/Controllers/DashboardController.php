<?php

namespace Comfort\Crm\Smtp\Controllers;

use Exception;
use Comfort\Crm\Smtp\MigrationManage;

class DashboardController {

	/**
	 * Full plugin option reset
	 */
	public function pluginOptionsReset( \WP_REST_Request $request ) {

		$response = new \WP_REST_Response();
		$response->set_status( 200 );

		try {
			if ( ! is_user_logged_in() ) {
				throw new Exception( esc_html__( 'Unauthorized', 'cbxwpemaillogger' ) );
			}

			if ( ! current_user_can( 'comfortsmtp_settings_manage' ) ) {
				throw new Exception( esc_html__( 'Sorry, you don\'t have enough permission!', 'cbxwpemaillogger' ) );
			}


			$data = $request->get_params();

			do_action( 'comfortsmtp_plugin_reset_before' );

			//delete options
			$reset_options = isset( $data['reset_options'] ) ? $data['reset_options'] : [];

			foreach ( $reset_options as $key => $option ) {
				if ( $option ) {
					delete_option( $key );
				}
			}

			do_action( 'comfortsmtp_plugin_option_delete' );
			do_action( 'comfortsmtp_plugin_reset_after' );
			do_action( 'comfortsmtp_plugin_reset' );

			$response->set_data( [
				'success' => true,
				'info'    => esc_html__( 'Comfort form setting options reset successfully', 'cbxwpemaillogger' )
			] );

			return $response;
		} catch ( Exception $e ) {
			$response->set_data( [
				'success' => false,
				'info'    => $e->getMessage(),
			] );

			return $response;
		}
	} //end plugin_reset

	/**
	 * Full plugin option reset
	 */
	public function runMigration( \WP_REST_Request $request ) {
		$response = new \WP_REST_Response();
		$response->set_status( 200 );

		try {
			if ( ! is_user_logged_in() ) {
				throw new Exception( esc_html__( 'Unauthorized', 'cbxwpemaillogger' ) );
			}

			if ( ! current_user_can( 'comfortsmtp_settings_manage' ) ) {
				throw new Exception( esc_html__( 'Sorry, you don\'t have enough permission!', 'cbxwpemaillogger' ) );
			}

			MigrationManage::run();

			$response->set_data( [
				'success' => true,
				'info'    => esc_html__( 'Migrated successfully', 'cbxwpemaillogger' )
			] );

			return $response;
		} catch ( Exception $e ) {
			$response->set_data( [
				'success' => false,
				'info'    => $e->getMessage(),
			] );

			return $response;
		}
	} //end runMigration
}//end class DashboardController