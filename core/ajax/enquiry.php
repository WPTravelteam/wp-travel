<?php
class WP_Travel_Ajax_Enquiry { // @phpcs:ignore
	/**
	 * Initialize Ajax request for Enquiry.
	 *
	 * @since 5.0.0
	 */
	public static function init() {

	    // For admin Get  enquiry details
		add_action( 'wp_ajax_wptravel_get_enquiry', array( __CLASS__, 'get_enquiry_details' ) );
		add_action( 'wp_ajax_nopriv_wptravel_get_enquiry', array( __CLASS__, 'get_enquiry_details' ) );

		// For admin Update enquiry details.
		add_action( 'wp_ajax_wptravel_update_enquiry', array( __CLASS__, 'update_enquiry_details' ) );
		add_action( 'wp_ajax_nopriv_wptravel_update_enquiry', array( __CLASS__, 'update_enquiry_details' ) );
	}

	/**
	 * Get Enquiry details.
	 *
	 * @since 5.0.0
	 */
	public static function get_enquiry_details() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$payload = WP_Travel::get_sanitize_request();

		$enquiry_id = trim( $payload['enquiry_id'] );

		$response = WpTravel_Helpers_Enquiry::get_enquiry( $enquiry_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Update enquiry details.
	 *
	 * @since 5.0.0
	 */
	public static function update_enquiry_details() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$payload = json_decode( file_get_contents( 'php://input' ) );
		$payload = is_object( $payload ) ? (array) $payload : array();
		$payload = wptravel_sanitize_array( $payload );

		// Nonce already verified.
		$enquiry_id = ! empty( $_GET['enquiry_id'] ) ? absint( $_GET['enquiry_id'] ) : 0; //@phpcs:ignore

		$response = WpTravel_Helpers_Enquiry::update_enquiry( $enquiry_id, $payload );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Enquiry::init();
