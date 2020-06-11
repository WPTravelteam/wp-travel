<?php
class WP_Travel_Ajax_Cart {
	public static function init() {
		// Get Cart items
		add_action( 'wp_ajax_wp_travel_get_cart', array( __CLASS__, 'get_cart' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_cart', array( __CLASS__, 'get_cart' ) );

		// Add to cart
		add_action( 'wp_ajax_wp_travel_add_to_cart', array( __CLASS__, 'add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_add_to_cart', array( __CLASS__, 'add_to_cart' ) );

		// Remove item from cart
		add_action( 'wp_ajax_wp_travel_remove_cart_item', array( __CLASS__, 'remove_cart_item' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_remove_cart_item', array( __CLASS__, 'remove_cart_item' ) );

		// Remove item from cart
		add_action( 'wp_ajax_wp_travel_update_cart_item', array( __CLASS__, 'update_cart_item' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_cart_item', array( __CLASS__, 'update_cart_item' ) );

		// Apply coupons.
		add_action( 'wp_ajax_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_apply_coupon', array( __CLASS__, 'apply_coupon_code' ) );
	}

	public static function get_cart() {
		$permission = self::get_cart_permissions();
		if ( is_wp_error( self::get_cart_permissions() || ! $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$response = WP_Travel_Helpers_Cart::get_cart();
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function add_to_cart() {
		$permission = self::get_cart_permissions();
		if ( is_wp_error( self::get_cart_permissions() || ! $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$postData = json_decode( file_get_contents( 'php://input' ) );
		$postData = is_object( $postData ) ? (array) $postData : array();
		$response = WP_Travel_Helpers_Cart::add_to_cart( $postData );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function remove_cart_item() {
		$permission = self::get_cart_permissions();
		if ( is_wp_error( self::get_cart_permissions() || ! $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$cart_id  = ! empty( $_GET['cart_id'] ) ? $_GET['cart_id'] : 0;
		$response = WP_Travel_Helpers_Cart::remove_cart_item( $cart_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function update_cart_item() {
		$permission = self::get_cart_permissions();
		if ( is_wp_error( self::get_cart_permissions() || ! $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$cart_id  = ! empty( $_GET['cart_id'] ) ? $_GET['cart_id'] : 0;
		$postData = json_decode( file_get_contents( 'php://input' ) );
		$postData = is_object( $postData ) ? (array) $postData : array();
		$response = WP_Travel_Helpers_Cart::update_cart_item( $cart_id, $postData );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function apply_coupon_code() {
		$permission = self::get_cart_permissions();
		if ( is_wp_error( self::get_cart_permissions() || ! $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$payload = json_decode( file_get_contents( 'php://input' ) );
		$payload = is_object( $payload ) ? (array) $payload : array();

		if ( empty( $payload['couponCode'] ) || ! is_string( $payload['couponCode'] ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_COUPON' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$coupon_code = $payload['couponCode'];
		
		$response = WP_Travel_Helpers_Cart::apply_coupon_code( $coupon_code );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function get_cart_permissions() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'wp_travel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
			return WP_Travel_Helpers_REST_API::response( $error );
		}
		return true;
	}
}

WP_Travel_Ajax_Cart::init();
