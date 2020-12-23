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

/**
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 */
function wp_travel_register_booking_metaboxes() {
	// register_metaboxes
	wp_travel_deprecated_function( 'wp_travel_register_booking_metaboxes', '4.4.2' );
}
/**
 * Hide publish and visibility.
 *
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 */
function wp_travel_admin_head_meta() {
	// internal_style
	wp_travel_deprecated_function( 'wp_travel_admin_head_meta', '4.4.2' );
	return WP_Travel_Admin_Booking()->internal_style();
}

/**
 * Call back for booking metabox.
 *
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 * @param Object $post Post object.
 */
function wp_travel_booking_info( $post ) {
	// booking_info
	wp_travel_deprecated_function( 'wp_travel_booking_info', '4.4.2' );
	return WP_Travel_Admin_Booking()->booking_info();
}

/**
 * Save Post meta data from admin.
 *
 * @param  int $booking_id Booking ID.
 * 
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 * @return Mixed
 */
function wp_travel_save_booking_data( $booking_id ) {
	// save
	wp_travel_deprecated_function( 'wp_travel_save_booking_data', '4.4.2' );
	return WP_Travel_Admin_Booking()->save( $booking_id );
}

/**
 * Customize Admin column.
 *
 * @param  Array $booking_columns List of columns.
 *
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 * @return Array                  [description]
 */
function wp_travel_booking_columns( $booking_columns ) {
	// booking_columns
	wp_travel_deprecated_function( 'wp_travel_booking_columns', '4.4.2' );
	return WP_Travel_Admin_Booking()->booking_columns( $booking_columns );

}

/**
 * Add data to custom column.
 *
 * @param  String $column_name Custom column name.
 * @param  int    $id          Post ID.
 * 
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 */
function wp_travel_booking_manage_columns( $column_name, $id ) {
	// booking_columns_content
	wp_travel_deprecated_function( 'wp_travel_booking_manage_columns', '4.4.2' );
	return WP_Travel_Admin_Booking()->booking_columns_content( $column_name, $id );

}

/**
 * ADMIN COLUMN - SORTING - MAKE HEADERS SORTABLE
 * https://gist.github.com/906872
 * 
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 */
function wp_travel_booking_sort( $columns ) {
	// booking_columns_sort
	wp_travel_deprecated_function( 'wp_travel_booking_sort', '4.4.2' );
	return WP_Travel_Admin_Booking()->booking_columns_sort( $columns );

}

/**
 * Manage Order By custom column.
 *
 * @param  Array $vars Order By array.
 * 
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.4.2
 * @return Array       Order By array.
 */
function wp_travel_booking_column_orderby( $vars ) {
	//booking_columns_content_sort
	wp_travel_deprecated_function( 'wp_travel_booking_column_orderby', '4.4.2' );
	return WP_Travel_Admin_Booking()->booking_columns_content_sort( $vars );

}

