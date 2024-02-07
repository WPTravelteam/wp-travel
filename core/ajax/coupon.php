<?php
class WP_Travel_Ajax_Coupon { // @phpcs:ignore
	/**
	 * Initialize Ajax request for Coupon.
	 *
	 * @since 5.0.0
	 */
	public static function init() {

		// Apply coupons.
		add_action( 'wp_ajax_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );

		// For admin Get coupon.
		add_action( 'wp_ajax_wptravel_get_coupon', array( __CLASS__, 'get_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wptravel_get_coupon', array( __CLASS__, 'get_coupon_code' ) );

		// For admin Update coupon.
		add_action( 'wp_ajax_wptravel_update_coupon', array( __CLASS__, 'update_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wptravel_update_coupon', array( __CLASS__, 'update_coupon_code' ) );
	}

	/**
	 * Apply Coupon. Code
	 *
	 * @since 5.0.0
	 */
	public static function apply_coupon_code() {
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$payload     = json_decode( file_get_contents( 'php://input' ) );
		$payload     = is_object( $payload ) ? (array) $payload : array();
		$payload     = wptravel_sanitize_array( $payload );
		$coupon_code = trim( $payload['couponCode'] );

		$response = WP_Travel_Helpers_Cart::apply_coupon_code( $coupon_code );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Get Coupon details.
	 *
	 * @since 5.0.0
	 */
	public static function get_coupon_code() {

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

		$coupon_id = trim( $payload['coupon_id'] );

		$response = WpTravel_Helpers_Coupon::get_coupon( $coupon_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Update Coupon details.
	 *
	 * @since 5.0.0
	 */
	public static function update_coupon_code() {

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
		$coupon_id = ! empty( $_GET['coupon_id'] ) ? absint( $_GET['coupon_id'] ) : 0; //@phpcs:ignore

		$response = WpTravel_Helpers_Coupon::update_coupon( $coupon_id, $payload );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Coupon::init();