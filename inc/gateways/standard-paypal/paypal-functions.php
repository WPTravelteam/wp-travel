<?php

/**
 * Retrieve the correct PayPal Redirect based on http/s
 * and "live" or "test" mode, i.e., sandbox.
 *
 * @return PayPal URI
 */
function wp_travel_get_paypal_redirect_url( $ssl_check = false ) {

	if ( is_ssl() || ! $ssl_check ) {
		$protocol = 'https://';
	} else {
		$protocol = 'http://';
	}

	if ( wp_travel_test_mode() ) {
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
function wp_travel_listen_paypal_ipn() {
	if ( isset( $_GET['wp_travel_listener'] )
		&& $_GET['wp_travel_listener'] == 'IPN'
		|| isset( $_GET['test'] )
		&& $_GET['test'] == true ) {
		do_action( 'wp_travel_verify_paypal_ipn' );
	}
	// echo WP_CONTENT_DIR;die;
}
add_action( 'init', 'wp_travel_listen_paypal_ipn' );


/**
 * When a payment is made PayPal will send us a response and this function is
 * called. From here we will confirm arguments that we sent to PayPal which
 * the ones PayPal is sending back to us.
 * This is the Pink Lilly of the whole operation.
 */
function wp_travel_paypal_ipn_process() {
	/**
	 * Instantiate the IPNListener class
	 */
	include dirname( __FILE__ ) . '/php-paypal-ipn/IPNListener.php';
	$listener = new IPNListener();

	/**
	 * Set to PayPal sandbox or live mode
	 */
	$settings              = wp_travel_get_settings();
	$listener->use_sandbox = ( $settings['wt_test_mode'] ) ? true : false;

	/**
	 * Check if IPN was successfully processed
	 */
	if ( $verified = $listener->processIpn() ) {

		/**
		 * Log successful purchases
		 */
		$transactionData = $listener->getPostData(); // POST data array
		file_put_contents( 'ipn_success.log', print_r( $transactionData, true ) . PHP_EOL, LOCK_EX | FILE_APPEND );

		$message = null;
		/**
		 * Verify seller PayPal email with PayPal email in settings
		 *
		 * Check if the seller email that was processed by the IPN matches what is saved as
		 * the seller email in our DB
		 */
		if ( $_POST['receiver_email'] != $settings['paypal_email'] ) {
			$message .= "\nEmail seller email does not match email in settings\n";
		}

		/**
		 * Verify currency
		 *
		 * Check if the currency that was processed by the IPN matches what is saved as
		 * the currency setting
		 */
		if ( $_POST['mc_currency'] != $settings['currency'] ) {
			$message .= "\nCurrency does not match those assigned in settings\n";
		}

		/**
		 * Check if this payment was already processed
		 *
		 * PayPal transaction id (txn_id) is stored in the database, we check
		 * that against the txn_id returned.
		 */
		$booking_id = isset( $_POST['custom'] ) ? $_POST['custom'] : 0;
		$txn_id     = get_post_meta( $booking_id, 'txn_id', true );
		if ( empty( $txn_id ) ) {
			update_post_meta( $booking_id, 'txn_id', $_POST['txn_id'] );
		} else {
			$message .= "\nThis payment was already processed\n";
		}

		/**
		 * Verify the payment is set to "Completed".
		 *
		 * Create a new payment, send customer an email and empty the cart
		 */

		if ( ! empty( $_POST['payment_status'] ) && $_POST['payment_status'] == 'Completed' ) {
				// Update booking status and Payment args.
				update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
				$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

				$payment_ids = array();
				// get previous payment ids.
				$payment_id     = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
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
				$amount         = $_POST['mc_gross'];
				$detail         = $_POST;

				update_post_meta( $new_payment_id, 'wp_travel_payment_gateway', $payment_method );

				update_post_meta( $new_payment_id, 'wp_travel_payment_amount', $amount );
				update_post_meta( $new_payment_id, 'wp_travel_payment_status', 'paid' );
				update_post_meta( $new_payment_id, 'wp_travel_payment_mode', 'partial' );

				$json = sanitize_text_field( wp_unslash( $_POST['payment_details'] ) );
				wp_travel_update_payment_status( $booking_id, $amount, 'paid', $detail, sprintf( '_%s_args', $payment_method ), $new_payment_id );

			} else { // New Payment.
				update_post_meta( $payment_id, '_paypal_args', $_POST );
				update_post_meta( $payment_id, 'wp_travel_payment_status', 'paid' );
				update_post_meta( $payment_id, 'wp_travel_payment_amount', $_POST['mc_gross'] );

				do_action( 'wp_travel_after_successful_payment', $booking_id );
			}
		} else {

			$message .= "\nPayment status not set to Completed\n";

		}

		/**
		 * Check if this is the test mode
		 *
		 * If this is the test mode we email the IPN text report.
		 * note about and box http://stackoverflow.com/questions/4298117/paypal-ipn-always-return-payment-status-pending-on-sandbox
		 */
		if ( $settings['wt_test_mode'] == true ) {

			$message .= "\nTest Mode\n";
			$email    = array(
				'to'      => $settings['wt_test_email'],
				'subject' => 'Verified IPN',
				'message' => $message . "\n" . $listener->getTextReport(),
			);

			wp_mail( $email['to'], $email['subject'], $email['message'] );

		}
	} else {

		/**
		 * Log errors
		 */
		$errors = $listener->getErrors();
		file_put_contents( 'ipn_errors.log', print_r( $errors, true ) . PHP_EOL, LOCK_EX | FILE_APPEND );

		/**
		 * An Invalid IPN *may* be caused by a fraudulent transaction attempt. It's
		 * a good idea to have a developer or sys admin manually investigate any
		 * invalid IPN.
		 */
		$from_email = isset( $settings['from_email'] ) ? $settings['from_email'] : '';
		if ( ! empty( $from_email ) ) {
			wp_mail( $settings['from_email'], 'Invalid IPN', $listener->getTextReport() );
		}
	}
}
add_action( 'wp_travel_verify_paypal_ipn', 'wp_travel_paypal_ipn_process' );
