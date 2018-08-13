<?php
/**
 * Paypal payment request
 *
 * @package WP-Travel-Paypal
 * @author WEN Solutions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Paypal payment request.
 */
class WP_Travel_Gateway_Paypal_Request {
	/**
	 * Constructor.
	 */
	function __construct() {
		// add_action( 'init', array( $this, 'process' ) );
		add_action( 'wp_travel_after_frontend_booking_save', array( $this, 'process' ) );
	}


	/**
	 * Paypal Process.
	 *
	 * @param int $booking_id Booking ID.
	 * @return void
	 */
	function process( $booking_id ) {
		if ( ! $booking_id ) {
			return;
		}
		/**
		 * Before payment process action.
		 * wp_travel_update_payment_status_booking_payment() // add/update payment id.
		 */
		do_action( 'wt_before_payment_process', $booking_id );
		// Check if paypal is selected.
		if ( ! isset( $_POST['wp_travel_payment_gateway'] ) || 'paypal' !== $_POST['wp_travel_payment_gateway'] ) {
			return;
		}
		// Check if Booking with payment is selected.
		if ( ! isset( $_POST['wp_travel_booking_option'] ) || 'booking_with_payment' !== $_POST['wp_travel_booking_option'] ) {
			return;
		}
		// $itinery_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
		// $price_per_text = wp_travel_get_price_per_text( $itinery_id );
		// $item_qty       = ( isset( $_POST['wp_travel_pax'] ) && 'person' === $price_per_text ) ? $_POST['wp_travel_pax'] : 1;
		// $payment_mode 	= isset( $_POST['wp_travel_payment_mode'] ) ? $_POST['wp_travel_payment_mode'] : 'partial';
		// $payable_amount = wp_travel_get_actual_trip_price( $itinery_id );

		// $item_amount = $payable_amount;
		// $trip_price = $item_amount * $item_qty;

		// $payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

		// // Updating Status for booking.
		// update_post_meta( $payment_id, 'wp_travel_payment_status', esc_html( 'pending' ) );

		$args = $this->get_args( $booking_id );

		$redirect_uri = esc_url( home_url( '/' ) );

		if ( $args ) {
			$paypal_args = http_build_query( $args, '', '&' );
			$redirect_uri = esc_url( wp_travel_get_paypal_redirect_url() ) . '?' . $paypal_args;
		}
		wp_redirect( $redirect_uri );

		exit;
	}
	/**
	 * Get Paypal Arguments.
	 *
	 * @param number $booking_id Booking ID.
	 * @return Array
	 */
	private function get_args( $booking_id ) {
		
		global $wt_cart;
		$items = $wt_cart->getItems();
		
		if ( ! $items ) {
			return false;
		}
		
		// Get settings.
		$settings = wp_travel_get_settings();

		// Check if paypal email is set.
		if ( ! isset( $settings['paypal_email'] ) || '' === $settings['paypal_email'] ) {
		    return false;
		}
		$itinery_id    = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id']          : 0;
		$paypal_email  = sanitize_email( $settings['paypal_email'] );
		$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency']                  : '';
		$payment_mode  = isset( $_POST['wp_travel_payment_mode'] ) ? $_POST['wp_travel_payment_mode']: 'partial';
		$current_url   = get_permalink( $itinery_id );
		$current_url   = apply_filters( 'wp_travel_thankyou_page_url', $current_url );
		$cart_amounts  = $wt_cart->get_total();
		
		$tax = 0;
		if ( $tax_rate =  wp_travel_is_taxable() ) {
			$tax = $cart_amounts['tax'];
			if ( 'partial' === $payment_mode ) {
				$tax = $cart_amounts['tax_partial'];
			}
		}
		$discount = isset( $cart_amounts['discount'] ) ? wp_travel_get_formated_price( $cart_amounts['discount'] ) : 0;

		if ( 'partial' === $payment_mode ) {
			$discount = isset( $cart_amounts['discount_partial'] ) ? wp_travel_get_formated_price( $cart_amounts['discount_partial'] ) : 0;
		}



		$args['cmd']			      = '_cart';
		$args['upload']			      = '1';
		$args['currency_code']	      = sanitize_text_field( $currency_code );
		$args['business']		      = sanitize_email( $paypal_email );
		$args['bn']				      = '';
		$args['rm']				      = '2';
		$args['discount_amount_cart'] = $discount;
		$args['tax_cart']		      = $tax;
		$args['charset']		      = get_bloginfo( 'charset' );
		$args['cbt']  			      = get_bloginfo( 'name' );
		$args['return'] 		      = add_query_arg( array( 'booking_id' => $booking_id, 'booked' => true, 'status' => 'success' ), $current_url );
		$args['cancel'] 		      = add_query_arg( array( 'booking_id' => $booking_id, 'booked' => true, 'status' => 'cancel' ), $current_url );
		$args['handling']		      = 0;
		$args['handling_cart']	      = 0;
		$args['no_shipping']	      = 0;
		$args['notify_url']		      = esc_url( add_query_arg( 'wp_travel_listener', 'IPN', home_url( 'index.php' ) ) );
		
		// Cart Item.
		$agrs_index = 1;
		foreach ( $items as $cart_id => $item ) {
			$trip_id 			= $item['trip_id'];
			$pax 				= $item['pax'];
			$trip_price 		= $item['trip_price'];
			$trip_price_partial = $item['trip_price_partial'];

			$item_name      = html_entity_decode( get_the_title( $trip_id ) );
			$trip_code 		= wp_travel_get_trip_code( $trip_id );

			$price_per = get_post_meta( $trip_id, 'wp_travel_price_per', true );

			$payment_amount =  wp_travel_get_formated_price( $trip_price );
			if ( 'partial' === $payment_mode ) {
				$payment_amount =  wp_travel_get_formated_price( $trip_price_partial );
			}
			// Group Multiply disable.
			if ( 'group' === $price_per ) {
				$pax = 1;
			}

			$args['item_name_' . $agrs_index ]   = $item_name;

			$args['quantity_' . $agrs_index ]   = $pax;
			
			$args['amount_' . $agrs_index ]   = $payment_amount;
			$args['item_number_' . $agrs_index ]   = $trip_id;

			$args['on0_' . $agrs_index ] = __( 'Trip Code', 'wp-travel' );
			// $args['on1_' . $agrs_index ] = __( 'Payment Mode', 'wp-travel' );
			$args['on2_' . $agrs_index ] = __( 'Trip Price', 'wp-travel' );
			
			$args['os0_' . $agrs_index ] = $trip_code;
			// $args['os1_' . $agrs_index ] = $payment_mode;
			$args['os2_' . $agrs_index ] = $item['trip_price'];

			$agrs_index++;
		}
		
		$args['option_index_0'] = $agrs_index;
		return apply_filters( 'wp_travel_paypal_args', $args );
	}
}

new WP_Travel_Gateway_Paypal_Request();
