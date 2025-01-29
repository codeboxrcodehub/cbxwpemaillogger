<?php

namespace Comfort\Crm\Smtp\Api;

use WP_Error;

/**
 * Class ComfortRoute
 * @package Comfort\Form
 * @since 1.0.0
 */
class ComfortRoute {

	/**
	 * Routes uri here
	 * @var array
	 * @since 1.0.0
	 */
	private static array $routes = [];

	/**
	 * Route prefix
	 * @var string
	 * @since 1.0.0
	 */
	private static string $prefix = 'comfortsmtp';

	private static string $capability = '';

	public function __construct() {
		require_once COMFORTSMTP_ROOT_PATH . 'includes/Api/routes.php';
	}//end of constructor

	/**
	 * Initialize WP rest hooks for rest api
	 *
	 * @since 1.0.0
	 */
	public function init() {
		foreach ( self::$routes as $route ) {
			register_rest_route( $route['namespace'],
				$route['uri'], [
					[
						'methods'             => $route['method'],
						'callback'            => $route['action'],
						'permission_callback' => function () use ( $route ) {
							return $this->check_permission( $route['capability'] );
						}
					]
				]
			);
		}
	}//end method init


	/**
	 * Check user permission current route
	 *
	 * @param $capability
	 *
	 * @return bool
	 */
	private function check_permission( $capability ) {
		if ( empty( $capability ) ) {
			return true;
		}

		if ( is_string( $capability ) ) {
			return current_user_can( $capability );
		} else {
			return false;
		}
	}//end method check_permission

	/**
	 * Rest API prefix Set
	 *
	 * @param $prefix
	 *
	 * @return static
	 * @since 1.0.0
	 */
	public static function prefix( $prefix ) {
		self::$prefix = $prefix;

		return new static();
	}//end method prefix

	/**
	 * @param string $capabilities
	 *
	 * @return static
	 * @since 1.0.0
	 */
	public static function middleware( $capabilities ) {
		self::$capability = $capabilities;

		return new static();
	}//end method middleware

	/**
	 * Rest API get route
	 *
	 * @param $uri
	 * @param array $action
	 *
	 * @return ComfortRoute
	 * @since 1.0.0
	 */
	public static function get( $uri, $action = [] ) {
		// set routes
		self::setRoutes( 'GET', $uri, $action );

		return new static();

	}//end method get

	/**
	 * Rest API post route
	 *
	 * @param $uri
	 * @param array $action
	 *
	 * @return ComfortRoute
	 * @since 1.0.0
	 */
	public static function post( $uri, $action = [] ) {
		// set routes
		self::setRoutes( 'POST', $uri, $action );

		return new static();
	}//end method post

	/**
	 * Set Routes
	 *
	 * @param string $method
	 * @param string $uri
	 * @param array $action
	 *
	 * @return WP_Error|void
	 * @since 1.0.0
	 */
	private static function setRoutes( $method, $uri, $action = [] ) {
		$className  = $action[0];
		$methodName = $action[1];

		// create class instance
		$classInstance = new $className();

		static::$routes[] = [
			'namespace'  => self::$prefix,
			'method'     => $method,
			'uri'        => $uri,
			'action'     => [ $classInstance, $methodName ],
			'capability' => self::$capability
		];

		// reset capability
		self::$capability = '';
	}//end method setRoutes

}//end method ComfortRoute