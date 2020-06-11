<?php

add_action( 'wp_ajax_wp_travel_apply_coupon', 'wp_travel_apply_coupon' );
add_action( 'wp_ajax_nopriv_wp_travel_apply_coupon', 'wp_travel_apply_coupon' );
function wp_travel_apply_coupon() {

	$payload = json_decode( file_get_contents( 'php://input' ) );
	$payload = is_object( $payload ) ? (array) $payload : array();

	if ( empty( $payload['couponCode'] ) || ! is_string( $payload['couponCode'] ) ) {
		return;
	}

	$coupon_code = $payload['couponCode'];

	$coupon_id = WP_Travel()->coupon->get_coupon_id_by_code( $coupon_code ); // Gets Coupon Code if Exists.

	if ( $coupon_id ) {
		// Prepare Coupon Application.
		global $wt_cart;

		$discount_type   = WP_Travel()->coupon->get_discount_type( $coupon_id );
		$discount_amount = WP_Travel()->coupon->get_discount_amount( $coupon_id );

		if ( 'fixed' === $discount_type ) {
			$cart_amounts = $wt_cart->get_total( false );
			$total        = $cart_amounts['total'];
			if ( $discount_amount >= $total ) {
				WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Cannot apply coupon for this trip.', 'wp-travel' ) ), 'error' );
				return;
			}
		}

		$wt_cart->add_discount_values( $coupon_id, $discount_type, $discount_amount, $coupon_code );

		$cart = WP_Travel_Helpers_Cart::get_cart();
		wp_send_json_success(
			array(
				'code'    => 'WP_TRAVEL_COUPON_APPLIED',
				'message' => sprintf( __( 'Discount Coupon Code Applied : %s', 'wp-travel' ), $coupon_code ),
				'cart'    => $cart['cart'],
			)
		);

		// WP_Travel_Ajax_Cart::get_cart();
	} else {
		wp_send_json_error(
			array(
				'code'    => 'WP_TRAVEL_INVALID_COUPON',
				'message' => __( 'The Coupon Code is Invalid.', 'wp-travel' ),
			)
		);
	}
}
