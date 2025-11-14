<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema;

if ( ! class_exists( 'ComfortsmtpAddFieldsLog' ) ) {
	/**
	 * Class ComfortsmtpAddFieldsLog
	 * @package Comfort\Form\Migrations
	 * @since 1.0.0
	 */
	class ComfortsmtpAddFieldsLog {

		/**
		 * Migration run
		 */
		public static function up() {

			$cbxwpemaillogger_log = 'cbxwpemaillogger_log';

			try {
				if ( Capsule::schema()->hasTable( $cbxwpemaillogger_log ) ) {
					Capsule::schema()->table( $cbxwpemaillogger_log, function ( $table ) {
						$table->string( 'mailer' )->nullable();
						$table->string( 'mailer_api' )->nullable();
						$table->string( 'mailer_id' )->nullable();
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
						$table->dropColumn( 'mailer' );
						$table->dropColumn( 'mailer_id' );
						$table->dropColumn( 'mailer_api' );
					} );
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method down

	}//end class ComfortsmtpAddFieldsLog
}


if ( isset( $action ) && $action == 'up' ) {
	ComfortsmtpAddFieldsLog::up();
} elseif ( isset( $action ) && $action == 'drop' ) {
	ComfortsmtpAddFieldsLog::down();
}