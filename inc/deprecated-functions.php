<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/
 */


/**
 * Wrapper for deprecated functions so we can apply some extra logic.
 *
 * @since  1.0.6
 * @param  string $function
 * @param  string $version
 * @param  string $replacement
 */
function wp_travel_deprecated_function( $function, $version, $replacement = null ) {
	if ( defined( 'DOING_AJAX' ) ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version );
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
		error_log( $log_string );
	} else {
		_deprecated_function( $function, $version, $replacement );
	}
}

/** Return All Settings of WP travel and it is depricated since 1.0.5*/
function wp_traval_get_settings() {
	wp_travel_deprecated_function( 'wp_traval_get_settings', '1.0.5', 'wp_travel_get_settings' );
	return wp_travel_get_settings();
}


/**
 * Return Currency symbol by currency code  and it is depricated since 1.0.5
 *
 * @param String $currency_code
 * @return String
 */
function wp_traval_get_currency_symbol( $currency_code = null ) {
	wp_travel_deprecated_function( 'wp_traval_get_currency_symbol', '1.0.5', 'wp_travel_get_currency_symbol' );
	return wp_travel_get_currency_symbol( $currency_code );
}
