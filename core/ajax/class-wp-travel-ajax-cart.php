<?php
/**
 * Cart ajax file.
 *
 * @package WP Travel.
 */

/**
 * WP_Travel_Ajax_Cart class.
 */
class WP_Travel_Ajax_Cart {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		// Get Cart items.
		add_action( 'wp_ajax_wp_travel_get_cart', array( __CLASS__, 'get_cart' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_cart', array( __CLASS__, 'get_cart' ) );

		// Add to cart.
		add_action( 'wp_ajax_wp_travel_add_to_cart', array( __CLASS__, 'add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_add_to_cart', array( __CLASS__, 'add_to_cart' ) );

		// Remove item from cart.
		add_action( 'wp_ajax_wp_travel_remove_cart_item', array( __CLASS__, 'remove_cart_item' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_remove_cart_item', array( __CLASS__, 'remove_cart_item' ) );

		// Remove item from cart.
		add_action( 'wp_ajax_wp_travel_update_cart_item', array( __CLASS__, 'update_cart_item' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_cart_item', array( __CLASS__, 'update_cart_item' ) );

	}

	/**
	 * Get cart function.
	 *
	 * @return void
	 */
	public static function get_cart() {
		WP_Travel::verify_nonce();
		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$response = WP_Travel_Helpers_Cart::get_cart();
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Add to cart function.
	 *
	 * @return void
	 */
	public static function add_to_cart() {
		WP_Travel::verify_nonce();
		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */

		$post_data = json_decode( file_get_contents( 'php://input' ) );
		$post_data = is_object( $post_data ) ? (array) $post_data : array();
		$post_data = wptravel_sanitize_array( $post_data );
		$response  = WP_Travel_Helpers_Cart::add_to_cart( $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Remove cart item.
	 *
	 * @return void
	 */
	public static function remove_cart_item() {
		WP_Travel::verify_nonce();
		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */

		$cart_id  = ! empty( $_GET['cart_id'] ) ? absint( $_GET['cart_id'] ) : 0;
		$response = WP_Travel_Helpers_Cart::remove_cart_item( $cart_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Update cart item function.
	 *
	 * @return void
	 */
	public static function update_cart_item() {
		WP_Travel::verify_nonce();
		/**
		 * We are checking nonce using WP_Travel::verify_nonce(); method.
		 */
		$cart_id   = ! empty( $_GET['cart_id'] ) ? sanitize_text_field( $_GET['cart_id'] ) : 0; //phpcs:ignore 
		$post_data = json_decode( file_get_contents( 'php://input' ) );
		$post_data = is_object( $post_data ) ? (array) $post_data : array();
		$post_data = wptravel_sanitize_array( $post_data );

		$response = WP_Travel_Helpers_Cart::update_cart_item( $cart_id, $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Cart::init();
