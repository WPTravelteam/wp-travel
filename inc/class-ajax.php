<?php
class WP_Travel_Ajax {

	public function __construct() {
		add_action( 'wp_ajax_envira_gallery_load_image', array( $this, 'post_gallery_ajax_load_image' ) );
		
		// Ajax for cart
		// Add
		add_action( 'wp_ajax_wt_add_to_cart', array( $this, 'wp_travel_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_add_to_cart', array( $this, 'wp_travel_add_to_cart' ) );
		
		// Update
		add_action( 'wp_ajax_wt_update_cart', array( $this, 'wp_travel_update_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_update_cart', array( $this, 'wp_travel_update_cart' ) );

		//Apply Coupon
		add_action( 'wp_ajax_wt_cart_apply_coupon', array( $this, 'wt_cart_apply_coupon' ) );
		add_action( 'wp_ajax_nopriv_wt_cart_apply_coupon', array( $this, 'wt_cart_apply_coupon' ) );

		// Delete cart item
		add_action( 'wp_ajax_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );

		//Check Coupon Code
		add_action( 'wp_ajax_wp_travel_check_coupon_code', array( $this, 'wp_travel_check_coupon_code' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_check_coupon_code', array( $this, 'wp_travel_check_coupon_code' ) );

		
	}

	function wp_travel_check_coupon_code() {

		if ( ! isset( $_POST['coupon_code'] ) ) {
			return;
		}

		$post_id = $_POST['coupon_id'];

		$coupon = WP_Travel()->coupon->get_coupon_id_by_code( $_POST['coupon_code'] );

		if ( ! $coupon || $post_id === $coupon ) {

			wp_send_json_success( $_POST['coupon_code'] );
		}

		wp_send_json_error( $_POST['coupon_code'] );

	}

	function post_gallery_ajax_load_image() {
		// Run a security check first.
		check_ajax_referer( 'wp-travel-drag-drop-nonce', 'nonce' );
		// Prepare variables.
		$id  = absint( $_POST['id'] );
		echo wp_json_encode( array(
			'id' => $id,
			'url' => wp_get_attachment_thumb_url( $id ),
		) );
		exit;
	}

	function wp_travel_add_to_cart() {
		if ( ! isset( $_POST['trip_id'] ) ) {
			return;
		}
		global $wt_cart;

		$allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );
		
		if ( ! $allow_multiple_cart_items ) {

			$wt_cart->clear();
		}

		$trip_id 	    = $_POST['trip_id'];
		$pax 		    = isset( $_POST['pax'] ) ? $_POST['pax'] : 0;
		$price_key 	    = isset( $_POST['price_key'] ) ? $_POST['price_key'] : '';
		$arrival_date   = isset( $_POST['trip_date'] ) ? $_POST['trip_date'] : '';
		$departure_date = isset( $_POST['trip_departure_date'] ) ? $_POST['trip_departure_date'] : '';

		$trip_price = wp_travel_get_cart_attrs( $trip_id, $pax, $price_key, true );
		
		$attrs = wp_travel_get_cart_attrs( $trip_id, $pax, $price_key );

		$attrs['arrival_date']   = $arrival_date;
		$attrs['departure_date'] = $departure_date;

		$wt_cart->add( $trip_id, $trip_price, $pax, $price_key, $attrs );
		return true;
	}

	function wp_travel_update_cart() {		
		if ( ! isset( $_POST['update_cart_fields'] ) ) {
			return;
		}

		if ( count( $_POST['update_cart_fields'] ) == 0 ) {
			return;
		}

		global $wt_cart;

		foreach( $_POST['update_cart_fields'] as $cart_field ) {
			$wt_cart->update( $cart_field['cart_id'], $cart_field['pax'] );
		}

		WP_Travel()->notices->add( apply_filters( 'wp_travel_cart_success', __( '<strong> </strong>Cart updated succesfully.Please Proceed to Checkout', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	function wt_cart_apply_coupon() {		
		if ( ! isset( $_POST['CouponCode'] ) ) {
			return;
		}

		if ( ! isset( $_POST['trip_ids'] ) ) {
			return;
		}

		if ( empty( $_POST['CouponCode'] ) ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Coupon Code cannot be empty', 'wp-travel' ) ), 'error' );

			return;
		}

		$coupon_id = WP_Travel()->coupon->get_coupon_id_by_code( $_POST['CouponCode'] );

		if ( ! $coupon_id ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Invalid Coupon Code', 'wp-travel' ) ), 'error' );

			return;

		}

		$date_validity = WP_Travel()->coupon->is_coupon_valid( $coupon_id );

		if ( ! $date_validity ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>The coupoun is either inactive or has expired. Coupon Code could not be applied.', 'wp-travel' ) ), 'error' );

			return;

		}

		$trip_ids = $_POST['trip_ids'];

		$trips_validity = WP_Travel()->coupon->trip_ids_allowed( $coupon_id, $trip_ids );

		if ( ! $trips_validity ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>This coupon cannot be applied to the selected trip', 'wp-travel' ) ), 'error' );

			return;

		}

		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab  = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$coupon_limit_number  = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';

		if ( ! empty( $coupon_limit_number ) ) {

			$usage_count = WP_Travel()->coupon->get_usage_count( $coupon_id );

			if ( absint( $usage_count ) >= absint( $coupon_limit_number ) ) {

				WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong>Error : </strong>Coupon Expired. Maximum no. of coupon usage exceeded.', 'wp-travel' ) ), 'error' );

				return;

			}

		}

		// Prepare Coupon Application.
		global $wt_cart;

		$discount_type   = WP_Travel()->coupon->get_discount_type( $coupon_id );
		$discount_amount = WP_Travel()->coupon->get_discount_amount( $coupon_id );

		$wt_cart->add_discount_values( $coupon_id, $discount_type, $discount_amount );

			WP_Travel()->notices->add( apply_filters( 'wp_travel_apply_coupon_errors', __( '<strong> </strong>Coupon applied succesfully.', 'wp-travel' ) ), 'success' );

		echo true;
		die;
	}

	function wp_travel_remove_from_cart() {
		if ( ! isset( $_POST['cart_id'] ) ) {
			return;
		}
		global $wt_cart;
		
		$wt_cart->remove( $_POST['cart_id'] );
		return true;
	}


}
new WP_Travel_Ajax();
