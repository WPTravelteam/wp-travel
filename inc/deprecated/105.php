<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/deprecated
 */

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
