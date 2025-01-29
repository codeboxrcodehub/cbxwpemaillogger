<?php

namespace Comfort\Crm\Smtp;

use Comfort\Crm\Smtp\Models\Migrations;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Manage migration and database table
 * Class MigrationManage
 * @package Comfort\Smtp
 * @since 1.0.0
 */
class MigrationManage {

	/**
	 * Migration run and database table manage
	 *
	 * @since 1.0.0
	 */
	public static function run() {
		do_action( 'comfortsmtp_migration_run_start' );

		$migrations = self::migration_files();

		$form_migrate_files = [];
		if ( ! Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
			$form_migrate_files = $migrations;
		} else {
			$migrations_table_files = Migrations::query()->where( 'plugin',
				COMFORTSMTP_PLUGIN_NAME )->get()->toArray();
			$migrated_files         = array_column( $migrations_table_files, 'migration' );
			$form_migrate_files     = array_values( array_diff( $migrations, $migrated_files ) );
		}

		// migration running
		foreach ( $form_migrate_files as $migration ) {

			$is_run_migration = self::load_migration( $migration, 'up' );

			if ( $is_run_migration ) {
				if ( Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
					Migrations::query()->create( [
						'migration' => $migration,
						'batch'     => 0,
						'plugin'    => COMFORTSMTP_PLUGIN_NAME
					] );
				}
			}
		}

		do_action( 'comfortsmtp_migration_run_end' );
	} //end method run

	/**
	 * Drop migrations
	 *
	 * @return false
	 * @since 1.0.0
	 */
	public static function drop() {

		if ( ! Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
			return false;
		}

		$migrations = Migrations::query()->where( 'plugin',
			COMFORTSMTP_PLUGIN_NAME )->orderByDesc( 'id' )->get();

		foreach ( $migrations as $migration ) {
			$is_drop_migration = self::load_migration( $migration->migration, 'drop' );

			if ( $is_drop_migration ) {
				$migration->delete();
			}
		}

	} //end method drop

	/**
	 * Load Migration files
	 *
	 * @param string $filePath
	 * @param string $action {up,drop}
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	public static function load_migration( $filePath, $action ) {

		$fileFullPath = plugin_dir_path( __FILE__ ) . '../migrations/' . $filePath . '.php';

		try {
			if ( file_exists( $fileFullPath ) ) {

				include $fileFullPath;

				return true;
			} else {
				return false;
			}

		} catch ( Exception $e ) {

			return false;
		}

	} //end method load_migration

	/**
	 * get all Migration files
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function migration_files() {

		//dev migration files
		$migrations_dev = [
			'2023_05_07_01_create_migrations_table',
			'2024_12_17_01_log_create',
			'2024_12_31_01_log_add_fields',
			'2025_01_01_log_add_api_status'
		];

		return $migrations_dev;
	} //end method migration_files

	/**
	 * get Migration files left
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public static function migration_files_left() {
		$migrations = self::migration_files();

		if ( ! Capsule::schema()->hasTable( 'cbxmigrations' ) ) {
			$form_migrate_files = $migrations;
		} else {
			$migrations_table_files = Migrations::query()->where( 'plugin', COMFORTSMTP_PLUGIN_NAME )->get()->toArray();
			$migrated_files         = array_column( $migrations_table_files, 'migration' );
			$form_migrate_files     = array_values( array_diff( $migrations, $migrated_files ) );
		}

		return $form_migrate_files;
	} //end method migration_files_left

	/**
	 * Migrate old option names to new option names
	 *
	 * @return void
	 */
	public static function migrate_old_options() {
		$old_options = [
			'cbxwpemaillogger_log',
			'cbxwpemaillogger_email',
			'cbxwpemaillogger_smtps',
			'cbxwpemaillogger_tools'
		];

		$new_options = [
			'comfortsmtp_log',
			'comfortsmtp_email',
			'comfortsmtp_smtps',
			'comfortsmtp_tools'
		];

		foreach ( $old_options as $old_option_key => $old_option_name ) {
			$old_option = get_option( $old_option_name );

			//if( !$old_option ) continue;
			if($old_option === false) continue;

			if ( isset( $new_options[ $old_option_key ] ) ) {
				$new_option_name = $new_options[ $old_option_key ];
				update_option( $new_option_name, $old_option );
			}

			delete_option( $old_option_name );
		}
	}//end method migrate_old_options
}//end class MigrationManage