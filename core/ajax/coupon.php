<?php
class WP_Travel_Ajax_Coupon {
	public static function init() {

		// Apply coupons.
		add_action( 'wp_ajax_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );
	}

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
}

WP_Travel_Ajax_Coupon::init();
