<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/** Return All Settings of WP travel and it is depricated since 1.0.5*/
function wp_traval_get_settings() {
	wp_travel_deprecated_function( 'wp_traval_get_settings', '1.0.5', 'wptravel_get_settings' );
	return wptravel_get_settings();
}

/**
 * Return Currency symbol by currency code  and it is depricated since 1.0.5
 *
 * @param String $currency_code
 * @return String
 */
function wp_traval_get_currency_symbol( $currency_code = null ) {
	wp_travel_deprecated_function( 'wp_traval_get_currency_symbol', '1.0.5', 'wptravel_get_currency_symbol' );
	return wptravel_get_currency_symbol( $currency_code );
}
