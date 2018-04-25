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

		// Delete cart item
		add_action( 'wp_ajax_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );
		add_action( 'wp_ajax_nopriv_wt_remove_from_cart', array( $this, 'wp_travel_remove_from_cart' ) );

		
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

		// $wt_cart->clear();
		$trip_id 	= $_POST['trip_id'];
		$pax 		= isset( $_POST['pax'] ) ? $_POST['pax'] : 0;
		$price_key 	= isset( $_POST['price_key'] ) ? $_POST['price_key'] : '';

		$trip_price = wp_travel_get_cart_attrs( $trip_id, $pax, $price_key, true );
		
		$attrs = wp_travel_get_cart_attrs( $trip_id, $pax, $price_key );

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
