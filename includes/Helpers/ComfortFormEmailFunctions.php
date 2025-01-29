<?php
if ( ! function_exists( 'comfortsmtp_rgb_from_hex' ) ) {

	/**
	 * Convert RGB to HEX.
	 *
	 * @param mixed $color Color.
	 *
	 * @return array
	 */
	function comfortsmtp_rgb_from_hex( $color ) {
		$color = str_replace( '#', '', $color ?? '000' );
		// Convert shorthand colors to full format, e.g. "FFF" -> "FFFFFF".
		$color = preg_replace( '~^(.)(.)(.)$~', '$1$1$2$2$3$3', $color );

		$rgb      = [];
		$rgb['R'] = hexdec( $color[0] . $color[1] );
		$rgb['G'] = hexdec( $color[2] . $color[3] );
		$rgb['B'] = hexdec( $color[4] . $color[5] );

		return $rgb;
	}//end function comfortsmtp_rgb_from_hex
}

if ( ! function_exists( 'comfortsmtp_hex_darker' ) ) {

	/**
	 * Make HEX color darker.
	 *
	 * @param mixed $color Color.
	 * @param int $factor Darker factor.
	 *                      Defaults to 30.
	 *
	 * @return string
	 */
	function comfortsmtp_hex_darker( $color, $factor = 30 ) {
		$base  = comfortsmtp_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = $v / 100;
			$amount      = comfortsmtp_round( $amount * $factor );
			$new_decimal = $v - $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}//end function comfortsmtp_hex_darker
}

if ( ! function_exists( 'comfortsmtp_hex_lighter' ) ) {

	/**
	 * Make HEX color lighter.
	 *
	 * @param mixed $color Color.
	 * @param int $factor Lighter factor.
	 *                      Defaults to 30.
	 *
	 * @return string
	 */
	function comfortsmtp_hex_lighter( $color, $factor = 30 ) {
		$base  = comfortsmtp_rgb_from_hex( $color );
		$color = '#';

		foreach ( $base as $k => $v ) {
			$amount      = 255 - $v;
			$amount      = $amount / 100;
			$amount      = comfortsmtp_round( $amount * $factor );
			$new_decimal = $v + $amount;

			$new_hex_component = dechex( $new_decimal );
			if ( strlen( $new_hex_component ) < 2 ) {
				$new_hex_component = '0' . $new_hex_component;
			}
			$color .= $new_hex_component;
		}

		return $color;
	}//end function comfortsmtp_hex_lighter
}

if ( ! function_exists( 'comfortsmtp_hex_is_light' ) ) {

	/**
	 * Determine whether a hex color is light.
	 *
	 * @param mixed $color Color.
	 *
	 * @return bool  True if a light color.
	 */
	function comfortsmtp_hex_is_light( $color ) {
		$hex = str_replace( '#', '', $color ?? '' );

		$c_r = hexdec( substr( $hex, 0, 2 ) );
		$c_g = hexdec( substr( $hex, 2, 2 ) );
		$c_b = hexdec( substr( $hex, 4, 2 ) );

		$brightness = ( ( $c_r * 299 ) + ( $c_g * 587 ) + ( $c_b * 114 ) ) / 1000;

		return $brightness > 155;
	}//end function comfortsmtp_hex_is_light
}

if ( ! function_exists( 'comfortsmtp_light_or_dark' ) ) {

	/**
	 * Detect if we should use a light or dark color on a background color.
	 *
	 * @param mixed $color Color.
	 * @param string $dark Darkest reference.
	 *                      Defaults to '#000000'.
	 * @param string $light Lightest reference.
	 *                      Defaults to '#FFFFFF'.
	 *
	 * @return string
	 */
	function comfortsmtp_light_or_dark( $color, $dark = '#000000', $light = '#FFFFFF' ) {
		return comfortsmtp_hex_is_light( $color ) ? $dark : $light;
	}//end function comfortsmtp_light_or_dark
}

if ( ! function_exists( 'comfortsmtp_format_hex' ) ) {

	/**
	 * Format string as hex.
	 *
	 * @param string $hex HEX color.
	 *
	 * @return string|null
	 */
	function comfortsmtp_format_hex( $hex ) {
		$hex = trim( str_replace( '#', '', $hex ?? '' ) );

		if ( strlen( $hex ) === 3 ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}

		return $hex ? '#' . $hex : null;
	}//end function comfortsmtp_format_hex
}

if(!function_exists('comfortsmtp_round')){
	/**
	 * Round a number using the built-in `round` function, but unless the value to round is numeric
	 * (a number or a string that can be parsed as a number), apply 'floatval' first to it
	 * (so it will convert it to 0 in most cases).
	 *
	 * This is needed because in PHP 7 applying `round` to a non-numeric value returns 0,
	 * but in PHP 8 it throws an error. Specifically, in WooCommerce we have a few places where
	 * round('') is often executed.
	 *
	 * @param mixed $val The value to round.
	 * @param int $precision The optional number of decimal digits to round to.
	 * @param int $mode A constant to specify the mode in which rounding occurs.
	 *
	 * @return float The value rounded to the given precision as a float, or the supplied default value.
	 */
	function comfortsmtp_round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ): float {
		if ( ! is_numeric( $val ) ) {
			$val = floatval( $val );
		}

		return round( $val, $precision, $mode );
	}//end function comfortsmtp_round
}


if(!function_exists('comfortsmtp_array_sum')){
	/**
	 * Get the sum of an array of values using the built-in array_sum function, but sanitize the array values
	 * first to ensure they are all floats.
	 *
	 * This is needed because in PHP 8.3 non-numeric values that cannot be cast as an int or a float will
	 * cause an E_WARNING to be emitted. Prior to PHP 8.3 these values were just ignored.
	 *
	 * Note that, unlike the built-in array_sum, this one will always return a float, never an int.
	 *
	 * @param array $arr The array of values to sum.
	 *
	 * @return float
	 */
	function comfortsmtp_array_sum( array $arr ): float {
		$sanitized_array = array_map( 'floatval', $arr );

		return array_sum( $sanitized_array );
	}//end function comfortsmtp_array_sum
}