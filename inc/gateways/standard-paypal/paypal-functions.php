<?php

/**
 * Retrieve the correct PayPal Redirect based on http/s
 * and "live" or "test" mode, i.e., sandbox.
 *
 * @return PayPal URI
 */
function wptravel_get_paypal_redirect_url( $ssl_check = false ) {

	if ( is_ssl() || ! $ssl_check ) {
		$protocol = 'https://';
	} else {
		$protocol = 'http://';
	}

	if ( wptravel_test_mode() ) {
		$paypal_uri = $protocol . 'www.sandbox.paypal.com/cgi-bin/webscr';
	} else {
		$paypal_uri = $protocol . 'www.paypal.com/cgi-bin/webscr';
	}

	return $paypal_uri;
}


/**
 * Listen for a $_GET request from our PayPal IPN.
 * This would also do the "set-up" for an "alternate purchase verification"
 */
function wptravel_listen_paypal_ipn() {

	if ( isset( $_POST['payer_id'] ) && isset( $_POST['txn_id'] ) ) {
		do_action( 'wp_travel_verify_paypal_ipn' );
    }

}
add_action( 'init', 'wptravel_listen_paypal_ipn' );


/**
 * When a payment is made PayPal will send us a response and this function is
 * called. From here we will confirm arguments that we sent to PayPal which
 * the ones PayPal is sending back to us.
 * This is the Pink Lilly of the whole operation.
 */
function wptravel_paypal_ipn_process() {


		$settings              = wptravel_get_settings();

		$message = null;
	
		if ( $_POST['mc_currency'] != $settings['currency'] ) { // @phpcs:ignore
			$message .= "\nCurrency does not match those assigned in settings\n";
		}

		/**
		 * Check if this payment was already processed
		 *
		 * PayPal transaction id (txn_id) is stored in the database, we check
		 * that against the txn_id returned.
		 */
		$booking_id = isset( $_POST['custom'] ) ? absint( $_POST['custom'] ) : 0;
		$txn_id     = get_post_meta( $booking_id, 'txn_id', true );
		if ( empty( $txn_id ) ) {
			update_post_meta( $booking_id, 'txn_id', sanitize_text_field( $_POST['txn_id'] ) );
		} else {
			$message .= "\nThis payment was already processed\n";
		}

		/**
		 * Verify the payment is set to "Completed".
		 *
		 * Create a new payment, send customer an email and empty the cart
		 */

		if ( ! empty( $_POST['payer_status'] ) && $_POST['payer_status'] == 'VERIFIED' && ! isset( $_GET['partial'] ) ) { // @phpcs:ignore
				
			
			// Update booking status and Payment args.
				update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
				$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

				$payment_ids = array();
				// get previous payment ids.
				$payment_id  = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
				$paypal_args = get_post_meta( $booking_id, '_paypal_args', true );

			if ( '' !== $paypal_args ) { // Partial Payment.
				if ( is_string( $payment_id ) && '' !== $payment_id ) {
					$payment_ids[] = $payment_id;
				} else {
					$payment_ids = $payment_id;
				}

				// insert new payment id and update meta.
				$title          = 'Payment - #' . $booking_id;
				$post_array     = array(
					'post_title'   => $title,
					'post_content' => '',
					'post_status'  => 'publish',
					'post_slug'    => uniqid(),
					'post_type'    => 'wp-travel-payment',
				);
				$new_payment_id = wp_insert_post( $post_array );
				$payment_ids[]  = $new_payment_id;
				update_post_meta( $booking_id, 'wp_travel_payment_id', $payment_ids );

				$payment_method = 'paypal';
				$amount         = sanitize_text_field( wp_unslash( $_POST['mc_gross'] ) );
				$detail         = wptravel_sanitize_array( $_POST );

				update_post_meta( $new_payment_id, 'wp_travel_payment_gateway', $payment_method );

				update_post_meta( $new_payment_id, 'wp_travel_payment_amount', $amount );
				
		
				if( $_POST['payment_status'] == 'Completed' ){
		
					update_post_meta( $new_payment_id, 'wp_travel_payment_status', 'paid' );
				}else{
	
					update_post_meta( $new_payment_id, 'wp_travel_payment_status', 'pending' );
				}
				
				update_post_meta( $new_payment_id, 'wp_travel_payment_mode', 'partial' );

				$json = sanitize_text_field( wp_unslash( $_POST['payment_details'] ) );
				wptravel_update_payment_status( $booking_id, $amount, 'paid', $detail, sprintf( '_%s_args', $payment_method ), $new_payment_id );

			} else { 
                
				
				update_post_meta( $payment_id, '_paypal_args', wptravel_sanitize_array( $_POST ) );
				if( $_GET['payment'] == 'partial' ){
					if( $_POST['payment_status'] == 'Completed' ){
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'partially_paid' );
					}else{
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'pending' );
					}
				}elseif( $_GET['payment'] == 'full' ){
				    
					if( $_POST['payment_status'] == 'Completed' ){
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'paid' );
					}else{
					   
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'pending' );
					}
				}else{
					if( $_POST['payment_status'] == 'Completed' ){
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'paid' );
					}else{
						update_post_meta( $payment_id, 'wp_travel_payment_status', 'pending' );
					}
				}

				update_post_meta( $payment_id, 'wp_travel_payment_mode', 'full' );

				if( $_GET['payment'] == 'partial' ){ 
					update_post_meta( $payment_id, 'wp_travel_payment_mode', 'partial' );
				}
				
				update_post_meta( $payment_id, 'wp_travel_payment_amount', sanitize_text_field( $_POST['mc_gross'] ) );

				do_action( 'wp_travel_after_successful_payment', $booking_id );
			}
		} elseif( ! empty( $_POST['payer_status'] ) && $_POST['payer_status'] == 'VERIFIED' && isset( $_GET['partial'] ) ) {
				$payment_gateway = 'paypal';
				$booking_id      = (int)$_GET['booking_id'];
				
				$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

				
				$new_payment_id = apply_filters( 'wptravel_before_insert_partial_payment', $payment_id, $booking_id, $payment_gateway );

				$detail = wptravel_sanitize_array( $_POST );

				$amount = sanitize_text_field( $_POST['mc_gross'] ); // @since 1.0.7

				wptravel_update_payment_status( $booking_id, $amount, 'paid', $detail, sprintf( '_%s_args', $payment_gateway ), $new_payment_id );
			
		}else {

			$message .= "\nPayment status not set to Completed\n";

		}    

}
add_action( 'wp_travel_verify_paypal_ipn', 'wptravel_paypal_ipn_process' );
