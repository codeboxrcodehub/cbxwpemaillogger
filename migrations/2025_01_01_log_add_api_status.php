<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema;

if ( ! class_exists( 'ComfortsmtpAddApiStatusLog' ) ) {
	/**
	 * Class ComfortsmtpAddApiStatusLog
	 * @package Comfort\Form\Migrations
	 * @since 1.0.0
	 */
	class ComfortsmtpAddApiStatusLog {

		/**
		 * Migration run
		 */
		public static function up() {

			$cbxwpemaillogger_log = 'cbxwpemaillogger_log';

			try {
				if ( Capsule::schema()->hasTable( $cbxwpemaillogger_log ) ) {
					Capsule::schema()->table( $cbxwpemaillogger_log, function ( $table ) {
						$table->string( 'api_status' )->nullable();
					} );
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method up

		/**
		 * Drop migrations
		 */
		public static function down() {

			$cbxwpemaillogger_log = 'cbxwpemaillogger_log';

			try {
				if ( Capsule::schema()->hasTable( $cbxwpemaillogger_log ) ) {
					Capsule::schema()->table( $cbxwpemaillogger_log, function ( $table ) {
						$table->dropColumn( 'api_status' );
					} );
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method down

	}//end class ComfortsmtpAddApiStatusLog
}


if ( isset( $action ) && $action == 'up' ) {
	ComfortsmtpAddApiStatusLog::up();
} elseif ( isset( $action ) && $action == 'drop' ) {
	ComfortsmtpAddApiStatusLog::down();
}