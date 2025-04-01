<?php
/**
 * Paypal payment request
 *
 * @package WP_Travel
 * @author WEN Solutions.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function(){
	if( isset( $_GET['PayerID'] ) && isset( $_GET['partial'] ) && $_GET['partial'] =='1' ){
		header( 'Location: ' . get_the_permalink( wptravel_get_settings()['dashboard_page_id'] ) );
	}	
} );


/**
 * Paypal payment request.
 */
class WP_Travel_Gateway_Paypal_Request {
	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'wp_travel_standard_paypal_payment_process', array( $this, 'process' ) );
		// For partial payment.
		add_action( 'wp_travel_before_partial_payment_complete', array( $this, 'process' ), 10, 2 );
	}

	/**
	 * Paypal Process.
	 *
	 * @param int $booking_id Booking ID.
	 * @return void
	 */
	public function process( $booking_id, $complete_partial_payment = false ) {
		
		
		if ( ! $booking_id ) {
			return;
		}
		/**
		 * Before payment process action [ not needed in partial payment ].
		 * wptravel_update_payment_status_booking_payment() // add/update payment id.
		 */
		if ( ! $complete_partial_payment ) { // updated in 1.8.5 for partial payment.
			do_action( 'wt_before_payment_process', $booking_id );
		}
		// Check if paypal is selected.
		if ( ! isset( $_POST['wp_travel_payment_gateway'] ) || 'paypal' !== $_POST['wp_travel_payment_gateway'] ) { //@phpcs:ignore
			return;
		}
		// $complete_partial_payment = false;
		$args = $this->get_args( $booking_id, $complete_partial_payment );
		
		
		$redirect_uri = esc_url( home_url( '/' ) );
		
		if ( $args ) {

			$paypal_args = http_build_query($args, '', '&');
			$redirect_uri = esc_url( wptravel_get_paypal_redirect_url() ) . '?' . $paypal_args;
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
	private function get_args( $booking_id, $complete_partial_payment = false ) {

		// Get settings.
		$settings = wptravel_get_settings();

		// Check if paypal email is set.
		if ( ! isset( $settings['paypal_email'] ) || '' === $settings['paypal_email'] ) {
			return false;
		}

		$itinerary_id  = isset( $_POST['wp_travel_post_id'] ) ? absint( $_POST['wp_travel_post_id'] ) : 0;
		$paypal_email  = sanitize_email( $settings['paypal_email'] );
		$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
		$payment_mode  = isset( $_POST['wp_travel_payment_mode'] ) ? sanitize_text_field( $_POST['wp_travel_payment_mode'] ) : '';

		global $wt_cart;
		$items       = $wt_cart->getItems();
		$current_url = wptravel_thankyou_page_url( $itinerary_id );
		if ( $complete_partial_payment ) { // For partial payment addons.
			
			$args['cmd']                  = '_cart';
			$args['upload']               = '1';
			$args['currency_code']        = sanitize_text_field( $currency_code );
			$args['business']             = sanitize_email( $paypal_email );
			$args['bn']                   = '';
			$args['rm']                   = '2';
			$args['discount_amount_cart'] = 0;
			$args['tax_cart']             = 0;
			$args['charset']              = get_bloginfo( 'charset' );
			$args['cbt']                  = get_bloginfo( 'name' );
			$args['return']               = add_query_arg(
				array(
					'booking_id' => $booking_id,
					'booked'     => true,
					'status'     => 'success',
					'partial'    => true,
				),
				$current_url
			);
			$args['cancel']               = add_query_arg(
				array(
					'booking_id' => $booking_id,
					'booked'     => true,
					'status'     => 'cancel',
					'partial'    => true,
				),
				$current_url
			);
			$args['handling']             = 0;
			$args['handling_cart']        = 0;
			$args['no_shipping']          = 0;
			// $args['notify_url']           = esc_url(
			// 	add_query_arg(
			// 		array(
			// 			'wp_travel_listener' => 'IPN',
			// 			'partial'            => true,
			// 		),
			// 		home_url( 'index.php' )
			// 	)
			// );

			// Cart Item.
			$agrs_index = 1;

			$args[ 'item_name_' . $agrs_index ] = 'Partial Payment for Booking ' . $booking_id;

			$args[ 'quantity_' . $agrs_index ] = 1;

			$args[ 'amount_' . $agrs_index ]      = sanitize_text_field( wp_unslash( $_POST['amount'] ) );
			$args[ 'item_number_' . $agrs_index ] = $booking_id;

		} elseif ( $items ) {  // Normal Payment.
			
			$cart_amounts = $wt_cart->get_total();

			$tax = 0;
			if ( $tax_rate = WP_Travel_Helpers_Trips::get_tax_rate() ) {
				$tax = $cart_amounts['tax'];
				if ( 'partial' === $payment_mode ) {
					$tax = $cart_amounts['tax_partial'];
				}
			}
			

			// if ( 'partial' === $payment_mode ) {
			// 	$discount = isset( $cart_amounts['discount_partial'] ) ? wptravel_get_formated_price( $cart_amounts['discount_partial'] ) : 0;
			// }
			

			$args['cmd']                  = '_cart';
			$args['upload']               = '1';
			$args['currency_code']        = sanitize_text_field( $currency_code );
			$args['business']             = sanitize_email( $paypal_email );
			$args['bn']                   = '';
			$args['rm']                   = '2';
			$args['discount_amount_cart'] = 0;
			$args['tax_cart']             = $tax;
			$args['charset']              = get_bloginfo( 'charset' );
			$args['cbt']                  = get_bloginfo( 'name' );
			$args['return']               = add_query_arg(
				array(
					'booking_id' => $booking_id,
					'booked'     => true,
					'status'     => 'success',
					'order_id'   => $booking_id,
					'payment' => $payment_mode,
				),
				$current_url
			);
			$args['cancel']               = add_query_arg(
				array(
					'booking_id' => $booking_id,
					'booked'     => true,
					'status'     => 'cancel',
				),
				$current_url
			);
			$args['handling']             = 0;
			$args['handling_cart']        = 0;
			$args['no_shipping']          = 0;
			// $args['notify_url']           = esc_url( add_query_arg( 'wp_travel_listener', 'IPN', home_url( 'index.php' ) ) );

			// Cart Item.
			$agrs_index = 1; // Initialize only once

			$payment_amount = 0;

			foreach ( $items as $cart_id => $item ) { 
				$payment_amount += (float) $item['trip_price'];
				
				$trip_extras = isset( $item['trip_extras'] ) ? $item['trip_extras'] : array();

				if ( is_array( $trip_extras ) && count( $trip_extras ) > 0 ) {

					if ( ! isset( $trip_extras['id'] ) ) {
						return;
					}

					if ( ! is_array( $trip_extras['id'] ) || empty( $trip_extras['id'] ) ) {
						return;
					}

					foreach ( $trip_extras['id'] as $key => $id ) :
						$trip_extras_data = get_post_meta( $id, 'wp_travel_tour_extras_metas', true );

						$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
						$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;
						$unit       = isset( $trip_extras_data['extras_item_unit'] ) && ! empty( $trip_extras_data['extras_item_unit'] ) ? $trip_extras_data['extras_item_unit'] : false;

						if ( $sale_price ) {
							$price = $sale_price;
						}
						
						$qty = isset( $trip_extras['qty'][ $key ] ) && ! empty( $trip_extras['qty'][ $key ] ) ? $trip_extras['qty'][ $key ] : 1;
						
			
						
				
						$payment_amount += ( (float)$qty * (float)$price );
		
					endforeach;

				}
				$partial_payout_figure = $item['partial_payout_figure'];
			}

			if( isset( $cart_amounts['discount'] ) ){
				$discount = isset( $cart_amounts['discount'] ) ? wptravel_get_formated_price( $cart_amounts['discount'] ) : 0;
				$payment_amount = $payment_amount - $discount;
			}

			if( 'partial' === $payment_mode ){
				$payment_amount = ($partial_payout_figure / 100) * $payment_amount;
			}

			$item_name = 'Booking ID '.$booking_id;

			$args[ 'item_name_1'] = $item_name;
			$args[ 'quantity_1']  = 1;
			$args[ 'amount_1' ]    = $payment_amount;
			$args[ 'item_number_1' ] = $booking_id;

			// foreach ( $items as $cart_id => $item ) {

			// 	$trip_id = $item['trip_id'];

			// 	$pax        = 1;
			// 	$trip_price = $item['trip_price'];

			// 	$item_name  = get_the_title( $trip_id );
			// 	$trip_code  = wptravel_get_trip_code( $trip_id );

			// 	$price_per = isset( $item['price_key'] ) && ! empty( $item['price_key'] ) 
			// 		? wptravel_get_pricing_variation_price_per( $trip_id, $item['price_key'] ) 
			// 		: get_post_meta( $trip_id, 'wp_travel_price_per', true );

			// 	$payment_amount = ( 'partial' === $payment_mode ) 
			// 		? wptravel_get_formated_price( $item['trip_price_partial'] ) 
			// 		: wptravel_get_formated_price( $trip_price );

			// 	if ( wptravel_is_react_version_enabled() ) {
			// 		$partial        = 'partial' === $payment_mode;
			// 		$trip_price     = wptravel_get_cart_item_price_with_extras( $cart_id, $trip_id, $partial );
			// 		$payment_amount = wptravel_get_formated_price( $trip_price );
			// 	}

			// 	if ( 'group' === $price_per ) {
			// 		$pax = 1;
			// 	}

			// 	// Build args array
			// 	$args[ 'item_name_' . $agrs_index ] = $item_name;
			// 	$args[ 'quantity_' . $agrs_index ]  = $pax;
			// 	$args[ 'amount_' . $agrs_index ]    = $payment_amount;
			// 	$args[ 'item_number_' . $agrs_index ] = $trip_id;
			// 	$args[ 'on0_' . $agrs_index ] = __( 'Trip Code', 'wp-travel' );
			// 	$args[ 'os0_' . $agrs_index ] = $trip_code;
			// 	$args[ 'on2_' . $agrs_index ] = __( 'Trip Price', 'wp-travel' );
			// 	$args[ 'os2_' . $agrs_index ] = $item['trip_price'];


			// 	if( class_exists( 'wp_travel_pro' ) ){
	
			// 		$args = apply_filters( 'wp_travel_tour_extra_paypal_args', $args, $item, $cart_id, $agrs_index )['args'];
				
			// 		$agrs_index = apply_filters( 'wp_travel_tour_extra_paypal_args', $args, $item, $cart_id, $agrs_index )['count'];				
			// 	}
				
			// 	$agrs_index++; // Increment after processing
			// }


		} else {
			return;
		}

		$args['option_index_0'] = $agrs_index;
		$args['custom']         = $booking_id;

	

		return apply_filters( 'wp_travel_paypal_args', $args );
	}
}

new WP_Travel_Gateway_Paypal_Request();

