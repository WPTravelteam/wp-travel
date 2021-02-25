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
 * @param  string $function Name of function.
 * @param  string $version Deprecate since version.
 * @param  string $replacement Function alternative / replacement.
 */
function wptravel_deprecated_function( $function, $version, $replacement = null ) {
	if ( defined( 'DOING_AJAX' ) ) {
		do_action( 'deprecated_function_run', $function, $replacement, $version ); // @phpcs:ignore
		$log_string  = "The {$function} function is deprecated since version {$version}.";
		$log_string .= $replacement ? " Replace with {$replacement}." : '';
		error_log( $log_string ); // @phpcs:ignore
	} else {
		_deprecated_function( $function, $version, $replacement ); // @phpcs:ignore
	}
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
function wptravel_do_deprecated_action( $tag, $args, $version, $replacement = null, $message = null ) {
	if ( ! has_action( $tag ) ) {
		return;
	}

	wptravel_deprecated_hook( $tag, $version, $replacement, $message );
	do_action_ref_array( $tag, $args ); // @phpcs:ignore
}

/**
 * Runs a deprecated filter with notice only if used.
 *
 * @since 4.4.7
 * @param string $tag         The name of the action hook.
 * @param array  $args        Array of additional function arguments to be passed to do_action().
 * @param string $version     The version of WooCommerce that deprecated the hook.
 * @param string $replacement The hook that should have been used.
 * @param string $message     A message regarding the change.
 */
function wptravel_do_deprecated_filter( $tag, $args, $version, $replacement = null, $message = null ) {
	if ( has_filter( $tag ) ) {
		error_log( 'has old filter' );
		// return apply_filters_deprecated( $tag, $args, $version, $replacement );
		return apply_filters_ref_array( $tag, $args ); //@phpcs:ignore
	}
	return $args[0];
	// wptravel_deprecated_hook( $tag, $version, $replacement, $message );
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
function wptravel_deprecated_hook( $hook, $version, $replacement = null, $message = null ) {
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

// Deprecated Functions.
require sprintf( '%s/inc/deprecated/105.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/193.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/307.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/440.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/442.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/447.php', WP_TRAVEL_ABSPATH );
require sprintf( '%s/inc/deprecated/449.php', WP_TRAVEL_ABSPATH );
