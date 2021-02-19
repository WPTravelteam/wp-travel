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
		WP_Travel::verify_nonce();

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$trip_id  = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
		$response = WP_Travel_Helpers_Pricings::get_pricings( $trip_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function remove_trip_pricing() {

		WP_Travel::verify_nonce();

		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$pricing_id = ! empty( $_GET['pricing_id'] ) ? absint( $_GET['pricing_id'] ) : 0;
		$response   = WP_Travel_Helpers_Pricings::remove_individual_pricing( $pricing_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function get_pricings_permissions_check() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( sanititze_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'wp_travel_nonce' ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}

		return true;
	}

	public static function delete_pricings_permissions_check() {

		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'wp_travel_nonce' ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
		}

		return true;
	}
}

WP_Travel_Ajax_Pricings::init();
