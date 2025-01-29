<?php
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

use Comfort\Crm\Smtp\Api\ComfortRoute;
use Comfort\Crm\Smtp\Controllers\LogController;
use Comfort\Crm\Smtp\Controllers\DashboardController;

ComfortRoute::get( 'v1/log-list', [ LogController::class, 'get_log_list' ] );
ComfortRoute::get( 'v1/log-data', [ LogController::class, 'get_log_data' ] );
ComfortRoute::post( 'v1/delete-log', [ LogController::class, 'destroy' ] );
ComfortRoute::post( 'v1/test-email-submit', [ LogController::class, 'testEmail' ] );
ComfortRoute::post( 'v1/email-resend', [ LogController::class, 'emailResend' ] );
ComfortRoute::post( 'v1/delete-old-log', [ LogController::class, 'deleteOldLog' ] );

ComfortRoute::middleware( 'manage_options' )->post( 'v1/reset-option', [
	DashboardController::class,
	'pluginOptionsReset'
] );
ComfortRoute::middleware( 'manage_options' )->post( 'v1/migrate-table', [
	DashboardController::class,
	'runMigration'
] );




