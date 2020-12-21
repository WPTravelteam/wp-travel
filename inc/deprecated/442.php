<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/deprecated
 */

 /**
 * Check if the current page is WP Travel page or not.
 *
 * @since WP Travel 1.0.4 and Deprecated in WP Travel 4.4.2
 * @return Boolean
 */
function is_wp_travel_archive_page() {
	wp_travel_deprecated_function( 'is_wp_travel_archive_page', '4.4.2', "WP_Travel::is_page( 'archive' )" );
	return WP_Travel::is_page( 'archive' );
}

/**
 * Check whether page is checkout page or not.
 *
 * @since WP Travel 1.8.5* and Deprecated in WP Travel 4.4.2
 * @return Boolean
 */
function wp_travel_is_checkout_page() {
	wp_travel_deprecated_function( 'wp_travel_is_checkout_page', '4.4.2', "WP_Travel::is_page( 'checkout' )" );
	return WP_Travel::is_page( 'checkout' );
}

/**
 * Check whether page is cart page or not.
 *
 * @since WP Travel 1.8.5* and Deprecated in WP Travel 4.4.2
 * @return Boolean
 */
function wp_travel_is_cart_page() {
	wp_travel_deprecated_function( 'wp_travel_is_cart_page', '4.4.2', "WP_Travel::is_page( 'cart' )" );
	return WP_Travel::is_page( 'cart' );
}

/**
 * Check whether page is dashboard page or not.
 *
 * @since WP Travel 1.8.5* and Deprecated in WP Travel 4.4.2
 * @return Boolean
 */
function wp_travel_is_dashboard_page() {
	wp_travel_deprecated_function( 'wp_travel_is_dashboard_page', '4.4.2', "WP_Travel::is_page( 'dashboard' )" );
	return WP_Travel::is_page( 'dashboard' );
}

/**
 * wp_travel_Is_account_page - Returns true when viewing an account page.
 *
 * @since WP Travel 1.8.5* and Deprecated in WP Travel 4.4.2
 * @return bool
 */
function wp_travel_is_account_page() {
	wp_travel_deprecated_function( 'wp_travel_is_account_page', '4.4.2', "WP_Travel::is_page( 'dashboard' )" );
	return WP_Travel::is_page( 'dashboard' );
}
