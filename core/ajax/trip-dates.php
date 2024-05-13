<?php
class WP_Travel_Ajax_Trip_Dates {
	public static function init() {
		// Get Cart items.

		add_action( 'wp_ajax_wp_travel_remove_trip_date', array( __CLASS__, 'remove_trip_date' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_remove_trip_date', array( __CLASS__, 'remove_trip_date' ) );
	}


	public static function remove_trip_date() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$date_id  = ! empty( $_GET['date_id'] ) ? absint( $_GET['date_id'] ) : 0;
		$response = WP_Travel_Helpers_Trip_Dates::remove_individual_date( $date_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Trip_Dates::init();
