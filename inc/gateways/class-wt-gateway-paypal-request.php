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
		$itinery_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
		$price_per_text = wp_travel_get_price_per_text( $itinery_id );
		$item_qty       = ( isset( $_POST['wp_travel_pax'] ) && 'person' === $price_per_text ) ? $_POST['wp_travel_pax'] : 1;
		$payment_mode 	= isset( $_POST['wp_travel_payment_mode'] ) ? $_POST['wp_travel_payment_mode'] : 'partial';
		$payable_amount = wp_travel_get_actual_trip_price( $itinery_id );

		$item_amount = $payable_amount;
		$trip_price = $item_amount * $item_qty;

		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

		// Updating Status for booking.
		update_post_meta( $payment_id, 'wp_travel_payment_status', esc_html( 'pending' ) );

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
		if ( ! $booking_id ) {
			return;
		}
		// // Get settings.
		$settings = wp_travel_get_settings();

		// Check if paypal email is set.
		if ( ! isset( $settings['paypal_email'] ) || '' === $settings['paypal_email'] ) {
		    return false;
		}
		$itinery_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
		$paypal_email = sanitize_email( $settings['paypal_email'] );

		$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] :'';
		$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

		$current_url = get_permalink( $itinery_id );
		$current_url = apply_filters( 'wp_travel_thankyou_page_url', $current_url );	

		$price_per_text = wp_travel_get_price_per_text( $itinery_id );

		$item_name      = html_entity_decode( get_the_title( $itinery_id ) );
		$item_qty       = 1;

		$trip_code 		= wp_travel_get_trip_code( $itinery_id );
		$payment_mode 	= isset( $_POST['wp_travel_payment_mode'] ) ? $_POST['wp_travel_payment_mode'] : 'full';

		if ( isset( $_REQUEST['price_key'] ) && ! empty( $_REQUEST['price_key'] ) ) {

			$pricing_key = $_GET['price_key'];

			$pricing_data = wp_travel_get_pricing_variation( $itinery_id, $pricing_key );

			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

				foreach ( $pricing_data as $p_ky => $pricing ) :

					$trip_price  = $pricing['price'];
					$enable_sale = isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'] ? true : false;

					$taxable_price = $pricing['price'];

						if ( $enable_sale && isset( $pricing['sale_price'] ) && '' !== $pricing['sale_price'] ) {
							$sale_price    = $pricing['sale_price'];
							$taxable_price = $sale_price;
						}

						$trip_tax_details = wp_travel_process_trip_price_tax_by_price( $itinery_id, $taxable_price );

						if ( isset( $trip_tax_details['tax_type'] ) ) {
								$actual_trip_price = $trip_tax_details['actual_trip_price'];

							}
							else {
								$actual_trip_price = $trip_tax_details['trip_price'];
							}
				endforeach;
			}
			$item_amount = $actual_trip_price;
		}
		else {

			$item_amount = wp_travel_get_actual_trip_price( $itinery_id );
		}

		if ( 'partial' === $payment_mode ) {
			$item_amount = isset( $_POST['wp_travel_payment_amount'] ) ? $_POST['wp_travel_payment_amount'] : $item_amount;
		}
		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		$trip_price = get_post_meta( $payment_id, 'wp_travel_trip_price' );

		if ( isset( $_POST['wp_travel_pax'] ) && 'person' === strtolower( $price_per_text ) ) {
			$item_amount *= $_POST['wp_travel_pax'];
		}

		$tax = 0;
		if ( 'partial' !== $payment_mode && wp_travel_is_trip_price_tax_enabled() && isset( $settings['trip_tax_price_inclusive'] ) && 'no' === $settings['trip_tax_price_inclusive'] ) {
			$taxable_price = $item_qty * $item_amount;
			$tax_rate = isset( $settings['trip_tax_percentage'] ) ? $settings['trip_tax_percentage'] : 0;
			$tax = ( $taxable_price * $tax_rate ) / 100;
			$tax = number_format( $tax, 2, '.', '' );			
		}

		$args['cmd']			= '_cart';
		$args['upload']			= '1';
		$args['currency_code']	= sanitize_text_field( $settings['currency'] );
		$args['business']		= sanitize_email( $paypal_email );
		$args['bn']				= '';
		$args['rm']				= '2';
		$args['tax_cart']		= $tax;
		$args['charset']		= get_bloginfo( 'charset' );
		$args['cbt']  			= get_bloginfo( 'name' );
		$args['return'] 		= add_query_arg( array( 'booking_id' => $booking_id, 'booked' => true, 'status' => 'success' ), $current_url );
		$args['cancel'] 		= add_query_arg( array( 'booking_id' => $booking_id, 'booked' => true, 'status' => 'cancel' ), $current_url );
		$args['custom'] 		= $booking_id;
		$args['handling']		= 0;
		$args['handling_cart']	= 0;
		$args['no_shipping']	= 0;
		$args['notify_url']		= esc_url( add_query_arg( 'wp_travel_listener', 'IPN', home_url( 'index.php' ) ) );
		
		// Cart Item.
		$args['item_name_1']   = $item_name;
		$args['quantity_1']   = $item_qty;
		$args['amount_1']   = $item_amount;
		$args['item_number_1']   = $itinery_id;
		$args['on0_1'] = __( 'Trip Code', 'wp-travel' );
		$args['on1_1'] = __( 'Payment Mode', 'wp-travel' );
		$args['on2_1'] = __( 'Trip Price', 'wp-travel' );

		$agrs_index = 3;

		$args['option_index_0'] = $agrs_index;

		$args['os0_1'] = $trip_code;
		$args['os1_1'] = $payment_mode;
		$args['os2_1'] = $trip_price;

		return apply_filters( 'wp_travel_paypal_args', $args );
	}
}

new WP_Travel_Gateway_Paypal_Request();
