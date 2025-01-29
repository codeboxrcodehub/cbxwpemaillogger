<?php

use Illuminate\Database\Capsule\Manager as Capsule;


if ( ! class_exists( 'CreateComfortMigrationsTable' ) ) {
	/**
	 * Common migration class for migration table and other tables(codeboxr's plugin or 3rd party if anyone use)
	 *
	 * Class ComfortWPMigrationsTable
	 * @since 1.0.0
	 */
	class CreateComfortMigrationsTable {

		/**
		 * Run migrations
		 *
		 * @since 1.0.0
		 */
		public static function up() {
			//migrations table create if not exists
			try {
				if ( ! Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
					Capsule::schema()->create( 'cbxmigrations', function ( $table ) {
						$table->increments( 'id' );
						$table->string( 'migration' );
						$table->integer( 'batch' );
						$table->string( 'plugin' );
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
		 *
		 * @return true|void
		 */
		public static function down() {
			try {
				if ( Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
					return true;
				}
			} catch ( \Exception $e ) {
				if ( function_exists( 'write_log' ) ) {
					write_log( $e->getMessage() );
				}
			}
		}//end method down

	}//end class CreateComfortMigrationsTable
}


if ( isset( $action ) && $action == 'up' ) {
	CreateComfortMigrationsTable::up();
} elseif ( isset( $action ) && $action == 'drop' ) {
	CreateComfortMigrationsTable::down();
}
