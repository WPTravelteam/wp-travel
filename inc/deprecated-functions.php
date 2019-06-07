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

function wp_travel_get_default_frontend_tabs( $is_show_in_menu_query = false ) {
	wp_travel_deprecated_function( 'wp_travel_get_default_frontend_tabs', '1.9.3', 'wp_travel_get_default_trip_tabs' );
	return wp_travel_get_default_trip_tabs( $is_show_in_menu_query );
}


/**
 * Runs a deprecated action with notice only if used.
 *
 * @since 2.0.4
 * @param string $tag         The name of the action hook.
 * @param array  $args        Array of additional function arguments to be passed to do_action().
 * @param string $version     The version of WooCommerce that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function wp_travel_do_deprecated_action( $tag, $args, $version, $replacement = null, $message = null ) {
	if ( ! has_action( $tag ) ) {
		return;
	}

	wp_travel_deprecated_hook( $tag, $version, $replacement, $message );
	do_action_ref_array( $tag, $args );
}

/**
 * Wrapper for deprecated hook so we can apply some extra logic.
 *
 * @since 2.0.4
 * @param string $hook        The hook that was used.
 * @param string $version     The version of WordPress that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function wp_travel_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
	// @codingStandardsIgnoreStart
	if ( defined( 'DOING_AJAX' ) ) {
		do_action( 'deprecated_hook_run', $hook, $replacement, $version, $message );

		$message    = empty( $message ) ? '' : ' ' . $message;
		$log_string = "{$hook} is deprecated since version {$version}";
		$log_string .= $replacement ? "! Use {$replacement} instead." : ' with no alternative available.';

		error_log( $log_string . $message );
	} else {
		_deprecated_hook( $hook, $version, $replacement, $message );
	}
	// @codingStandardsIgnoreEnd
}

