<?php

use Illuminate\Database\Capsule\Manager as Capsule;

if ( ! class_exists( 'ComfortsmtpCreateLogTable' ) ) {
	/**
	 * Common migration class for migration table and other tables(codeboxr's plugin or 3rd party if anyone use)
	 *
	 * Class CBXWPMigrationsTable
	 * @since 1.0.0
	 */
	class ComfortsmtpCreateLogTable {
		/**
		 * Run migrations
		 *
		 * @since 1.0.0
		 */
		public static function up() {
			//migrations table create if not exists
			try {
				if ( ! Capsule::schema()->hasTable( 'cbxwpemaillogger_log' ) ) {
					Capsule::schema()->create( 'cbxwpemaillogger_log', function ( $table ) {
						$table->bigIncrements( 'id' );
						$table->string( 'subject' )->nullable();
						$table->string( 'email_type' )->nullable();
						$table->longText( 'email_data' )->nullable();
						$table->string( 'ip_address' )->nullable();
						$table->tinyInteger( 'status' )->default( 1 );
						$table->text( 'error_message' )->nullable();
						$table->string( 'src_tracked' )->nullable();
						$table->dateTime( 'date_created' )->nullable();
					} );
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method up

		/**
		 * Migration drop
		 */
		public static function down() {
			try {
				if ( Capsule::schema()->hasTable( 'cbxwpemaillogger_log' ) ) {
					Capsule::schema()->dropIfExists( 'cbxwpemaillogger_log' );
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method down

	}//end class CreateCBXMigrationsTable
}


if ( isset( $action ) && $action == 'up' ) {
	ComfortsmtpCreateLogTable::up();
} elseif ( isset( $action ) && $action == 'drop' ) {
	ComfortsmtpCreateLogTable::down();
}
