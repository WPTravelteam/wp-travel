<?php
class WP_Travel_Ajax_Pricings {
	public static function init() {
		// Get Pricings.
		add_action( 'wp_ajax_wp_travel_get_pricings', array( __CLASS__, 'get_pricings' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_pricings', array( __CLASS__, 'get_pricings' ) );

		add_action( 'wp_ajax_wp_travel_remove_trip_pricing', array( __CLASS__, 'remove_trip_pricing' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_remove_trip_pricing', array( __CLASS__, 'remove_trip_pricing' ) );
	}

	public static function get_pricings() {

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
		$trip_id  = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
		$response = WP_Travel_Helpers_Pricings::get_pricings( $trip_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function remove_trip_pricing() {

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
		$pricing_id = ! empty( $_GET['pricing_id'] ) ? absint( $_GET['pricing_id'] ) : 0;
		$response   = WP_Travel_Helpers_Pricings::remove_individual_pricing( $pricing_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

}

WP_Travel_Ajax_Pricings::init();
