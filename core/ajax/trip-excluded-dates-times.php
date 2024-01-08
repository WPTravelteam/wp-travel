<?php
// class WP_Travel_Ajax_Trip_Excluded_Dates_Times {
// 	public static function init() {
		// Get Cart items
		// add_action( 'wp_ajax_wp_travel_get_trip_excluded_dates_times', array( __CLASS__, 'get_trip_dates_times' ) );
		// add_action( 'wp_ajax_nopriv_wp_travel_get_trip_excluded_dates_times', array( __CLASS__, 'get_trip_dates_times' ) );

		// add_action( 'wp_ajax_wp_travel_update_trip_excluded_dates_times', array( __CLASS__, 'update_trip_dates_times' ) );
		// add_action( 'wp_ajax_nopriv_wp_travel_update_trip_excluded_dates_times', array( __CLASS__, 'update_trip_dates_times' ) );
	// }

	// public static function get_trip_dates_times() {

	// 	if ( ! current_user_can( 'manage_options' ) ) {
	// 		return wp_send_json( array( 'result' => 'Authentication error' ) );
	// 	}
		
	// 	$permission = WP_Travel::verify_nonce();

	// 	if ( ! $permission || is_wp_error( $permission ) ) {
	// 		WP_Travel_Helpers_REST_API::response( $permission );
	// 	}

	// 	/**
	// 	 * We are checking nonce using WP_Travel::verify_nonce(); method.
	// 	 */
	// 	$trip_id  = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
	// 	$response = WP_Travel_Helpers_Trip_Excluded_Dates_Times::get_dates_times( $trip_id );
	// 	WP_Travel_Helpers_REST_API::response( $response );
	// }

	// public static function update_trip_dates_times() {

	// 	if ( ! current_user_can( 'manage_options' ) ) {
	// 		return wp_send_json( array( 'result' => 'Authentication error' ) );
	// 	}
		
	// 	$permission = WP_Travel::verify_nonce();

	// 	if ( ! $permission || is_wp_error( $permission ) ) {
	// 		WP_Travel_Helpers_REST_API::response( $permission );
	// 	}

	// 	/**
	// 	 * We are checking nonce using WP_Travel::verify_nonce(); method.
	// 	 */
	// 	$trip_id   = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
	// 	$post_data = json_decode( file_get_contents( 'php://input' ) );
	// 	$post_data = wptravel_sanitize_array( $post_data );
	// 	$response  = WP_Travel_Helpers_Trip_Excluded_Dates_Times::update_dates_times( $trip_id, $post_data );
	// 	WP_Travel_Helpers_REST_API::response( $response );
	// }
// }

// WP_Travel_Ajax_Trip_Excluded_Dates_Times::init();