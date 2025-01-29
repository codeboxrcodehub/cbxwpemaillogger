<?php

use Comfort\Crm\Smtp\Helpers\ComfortSmtpHelpers;
use enshrined\svgSanitize\Sanitizer;

if ( ! defined( 'WPINC' ) ) {
	die;
}

if(!function_exists('comfortsmtp_is_rest_api_request')){
	/**
	 * Check if doing rest request
	 *
	 * @return bool
	 */
	function comfortsmtp_is_rest_api_request() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		return ( false !== strpos( sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])), $rest_prefix ) );
	}//end function comfortsmtp_is_rest_api_request
}

if(!function_exists('comfortsmtp_doing_it_wrong')){
	/**
	 * Wrapper for _doing_it_wrong().
	 *
	 * @since  1.0.0
	 * @param string $function Function used.
	 * @param string $message Message to log.
	 * @param string $version Version the message was added in.
	 */
	function comfortsmtp_doing_it_wrong( $function, $message, $version ) {
		// @codingStandardsIgnoreStart
		$message .= ' Backtrace: ' . wp_debug_backtrace_summary();

		if ( wp_doing_ajax() || comfortsmtp_is_rest_api_request() ) {
			do_action( 'doing_it_wrong_run', $function, $message, $version );
			error_log( "{$function} was called incorrectly. {$message}. This message was added in version {$version}." );
		} else {
			_doing_it_wrong( $function, $message, $version );
		}
		// @codingStandardsIgnoreEnd
	}//end function comfortsmtp_doing_it_wrong
}

if ( ! function_exists( 'comfortsmtp_terms_page_id' ) ) {
	/**
	 * Returns terms id
	 *
	 * @return int
	 * @since 1.0.0
	 */
	function comfortsmtp_terms_page_id() {
		return ComfortSmtpHelpers::terms_page_id();
	}//end function comfortsmtp_terms_page_id
}


if ( ! function_exists( 'comfortsmtp_terms_page_url' ) ) {
	/**
	 * return terms url
	 *
	 * @return string
	 * @since 1.0.0
	 */
	function comfortsmtp_terms_page_url() {
		$page_id = absint( comfortsmtp_terms_page_id() );

		return esc_url( get_the_permalink( $page_id ) );
	}//end function comfortsmtp_terms_page_url
}


if ( ! function_exists( 'comfortsmtp_icon_path' ) ) {
	/**
	 * Form icon path
	 *
	 * @return mixed|null
	 * @since 1.0.0
	 */
	function comfortsmtp_icon_path() {
		$directory = trailingslashit( COMFORTSMTP_ROOT_PATH ) . 'assets/icons/';

		return apply_filters( 'comfortsmtp_icon_path', $directory );
	}//end method comfortsmtp_icon_path
}

if ( ! function_exists( 'comfortsmtp_load_svg' ) ) {
	/**
	 * Load an SVG file from a directory.
	 *
	 * @param string $svg_name The name of the SVG file (without the .svg extension).
	 * @param string $directory The directory where the SVG files are stored.
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function comfortsmtp_load_svg( $svg_name = '', $folder = '' ) {
		//note: code partially generated using chatgpt
		if ( $svg_name == '' ) {
			return '';
		}

		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		$credentials = request_filesystem_credentials( site_url() . '/wp-admin/', '', false, false, null );
		if ( ! WP_Filesystem( $credentials ) ) {
			return; // Error handling here
		}

		global $wp_filesystem;

		$directory = comfortsmtp_icon_path();

		// Sanitize the file name to prevent directory traversal attacks.
		$svg_name = sanitize_file_name( $svg_name );
		if ( $folder != '' ) {
			$folder = trailingslashit( $folder );
		}

		// Construct the full file path.
		$file_path = $directory . $folder . $svg_name . '.svg';

		$file_path = apply_filters( 'comfortsmtp_svg_file_path', $file_path, $svg_name );

		// Check if the file exists.
		if ( $wp_filesystem->exists( $file_path ) && is_readable( $file_path ) ) {
			// Get the SVG file content.
			return $wp_filesystem->get_contents( $file_path );
		} else {
			// Return false if the file does not exist or is not readable.
			return '';
		}
	}//end method comfortsmtp_load_svg
}


if ( ! function_exists( 'comfortsmtp_all_caps' ) ) {
	/**
	 * All form caps
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function comfortsmtp_all_caps() {
		//$all_caps = array_merge( comfortsmtp_log_caps() , comfortsmtp_submission_caps() );
		$all_caps = comfortsmtp_log_caps();

		return apply_filters( 'comfortsmtp_all_caps', $all_caps );
	}//end function comfortsmtp_all_caps
}

if ( ! function_exists( 'comfortsmtp_log_caps' ) ) {
	/**
	 * comfortsmtp component capabilities for dashboard
	 *
	 * @return array
	 * @since 1.0.0
	 */
	function comfortsmtp_log_caps() {
		//format: plugin_component_verb
		$caps = [
			'comfortsmtp_dashboard_manage',
			'comfortsmtp_settings_manage',
			'comfortsmtp_log_manage',
			'comfortsmtp_log_view',
			//'comfortsmtp_log_edit',
			'comfortsmtp_log_delete',
		];

		return apply_filters( 'comfortsmtp_log_caps', $caps );
	}//end function comfortsmtp_category_caps
}


if ( ! function_exists( 'comfortsmtp_esc_svg' ) ) {
	/**
	 * SVG sanitizer
	 *
	 * @param string $svg_content The content of the SVG file
	 *
	 * @return string|false The SVG content if found, or false on failure.
	 * @since 1.0.0
	 */
	function comfortsmtp_esc_svg( $svg_content = '' ) {
		// Create a new sanitizer instance
		$sanitizer = new Sanitizer();

		return $sanitizer->sanitize( $svg_content );
	}// end method comfortsmtp_esc_svg
}

if ( ! function_exists( 'comfortsmtp_get_user_capabilities' ) ) {
	/**
	 * Get user capabilities
	 *
	 * @return array
	 */
	function comfortsmtp_get_user_capabilities() {
		$wp_user = new \WP_User( get_current_user_id() );

		return $wp_user->allcaps;
	} //end function comfortsmtp_get_user_capabilities
}


if ( ! function_exists( 'comfortsmtp_dashboard_menus' ) ) {
	function comfortsmtp_dashboard_menus() {
		return ComfortSmtpHelpers::dashboard_menus();
	}//end method comfortsmtp_dashboard_menus
}

if(!function_exists('comfortsmtp_deprecated_function')){
	/**
	 * Wrapper for deprecated functions so we can apply some extra logic.
	 *
	 * @param  string  $function
	 * @param  string  $version
	 * @param  string  $replacement
	 *
	 * @since  2.0.5
	 *
	 */
	function comfortsmtp_deprecated_function( $function, $version, $replacement = null ) {
		if ( defined( 'DOING_AJAX' ) ) {
			do_action( 'deprecated_function_run', $function, $replacement, $version );
			$log_string = "The {$function} function is deprecated since version {$version}."; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$log_string .= $replacement ? " Replace with {$replacement}." : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

			if(defined('WP_DEBUG') && WP_DEBUG && defined('WP_DEBUG_LOG') && WP_DEBUG_LOG){
				error_log( $log_string );//phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		} else {
			_deprecated_function( $function, $version, $replacement ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}//end function comfortsmtp_deprecated_function
}