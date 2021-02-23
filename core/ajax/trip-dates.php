<?php
class WP_Travel_Ajax_Trip_Dates {
	public static function init() {
		// Get Cart items.
		add_action( 'wp_ajax_wp_travel_get_trip_dates', array( __CLASS__, 'get_trip_dates' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_dates', array( __CLASS__, 'get_trip_dates' ) );

		add_action( 'wp_ajax_wp_travel_update_trip_dates', array( __CLASS__, 'update_trip_dates' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_trip_dates', array( __CLASS__, 'update_trip_dates' ) );

		add_action( 'wp_ajax_wp_travel_remove_trip_date', array( __CLASS__, 'remove_trip_date' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_remove_trip_date', array( __CLASS__, 'remove_trip_date' ) );
	}

	public static function get_trip_dates() {

		WP_Travel::verify_nonce();

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$trip_id  = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
		$response = WP_Travel_Helpers_Trip_Dates::get_dates( $trip_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function update_trip_dates() {

		WP_Travel::verify_nonce();

		if ( ! current_user_can( 'manage_options' ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$trip_id   = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
		$post_data = json_decode( file_get_contents( 'php://input' ) );
		$post_data = wptravel_sanitize_array( $post_data );
		$response  = WP_Travel_Helpers_Trip_Dates::update_dates( $trip_id, $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function remove_trip_date() {

		WP_Travel::verify_nonce();

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$date_id  = ! empty( $_GET['date_id'] ) ? absint( $_GET['date_id'] ) : 0;
		$response = WP_Travel_Helpers_Trip_Dates::remove_individual_date( $date_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Trip_Dates::init();
