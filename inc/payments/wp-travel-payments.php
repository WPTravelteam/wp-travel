<?php
interface Wp_Travel_Payment_Interface {
	public function process_payment();

	public function render_settings();
}

$GLOBALS['wp_travel_payments'] = [];

if ( ! function_exists( 'wp_travel_register_payments' ) ) {

	/**
	 * Register payments here
	 *
	 * @param Object $object Payment Object.
	 */
	function wp_travel_register_payments( $object ) {

		if ( ! is_object( $object ) ) {
			throw new \Exception( 'Payment gateway must be an instance of class. ' . gettype( $object ) . ' given.' );
		}

		if ( ! ( $object instanceof Wp_Travel_Payment_Interface ) ) {
			throw new \Exception( 'Payment gateway must be an instance of Wp_Travel_Payment_Interface. Instance of ' . get_class( $object ) . ' given.' );
		}

		array_push( $GLOBALS['wp_travel_payments'], $object );
	}
}


// Other Payment Functions.
/**
 * List of payment fields
 *
 * @return array
 */
function wp_travel_payment_field_list() {
	return array(
		'is_partial_payment',
		'payment_gateway',
		'booking_option',
		'trip_price',

		'payment_mode',
		'payment_amount',
		'trip_price_info',
		'payment_amount_info',
	);
}

/**
 * Return all Payment Methods.
 *
 * @since 1.1.0
 * @return Array
 */
function wp_travel_payment_gateway_lists() {
	$gateway = array(
		'paypal' => __( 'Standard Paypal', 'wp-travel' ),
	);
	return apply_filters( 'wp_travel_payment_gateway_lists', $gateway );
}

/**
 * Get Minimum payout amount
 *
 * @param Number $post_id Post ID.
 * @return Number
 */
function wp_travel_minimum_partial_payout( $post_id ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_price = wp_travel_get_actual_trip_price( $post_id );
	$tax_details = wp_travel_process_trip_price_tax( $post_id );
	
	if ( is_array( $tax_details ) && isset( $tax_details['tax_type'] ) ) {

		if ( 'excluxive' === $tax_details['tax_type'] ) {

			$trip_price = $tax_details['actual_trip_price'];

		}
	}
	$payout_percent = wp_travel_get_actual_payout_percent( $post_id );
	$minimum_payout = ( $trip_price * $payout_percent ) / 100;	
	return number_format( $minimum_payout, 2, '.', '' );
	// $minimum_payout = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout', true );

	// if ( ! $minimum_payout ) {

	// 	$settings = wp_travel_get_settings();
	// 	$payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

	// 	$trip_price = wp_travel_get_actual_trip_price( $post_id );
	// 	$minimum_payout = ( $trip_price * $payout_percent ) / 100;
	// }
}


/**
 * Get Minimum payout amount
 *
 * @param Number $post_id Post ID.
 * @return Number
 */
function wp_travel_get_payout_percent( $post_id ) {
	if ( ! $post_id ) {
		return 0;
	}
	$settings = wp_travel_get_settings();
	$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	$payout_percent = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_percent', true );
	if ( ! $payout_percent  ) {
		$payout_percent = $default_payout_percent;
	}	
	return number_format( $payout_percent, 2, '.', ''  );
}

function wp_travel_get_actual_payout_percent( $post_id ) {
	if ( ! $post_id ) {
		return 0;
	}
	if ( wp_travel_use_global_payout_percent( $post_id ) ) {
		$settings = wp_travel_get_settings();
		return $default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	}

	return wp_travel_get_payout_percent( $post_id );
}

function wp_travel_use_global_payout_percent( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$use_global = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_use_global', true );
	if ( $use_global ) {
		return true;
	}
	return false;
}

/** Return true if test mode checked */
function wp_travel_test_mode() {
	$settings = wp_travel_get_settings();
	// Default true.
	if ( ! isset( $settings['wt_test_mode'] ) ) {
		return true;
	}
	if ( isset( $settings['wt_test_mode'] ) && 'yes' === $settings['wt_test_mode'] ) {
		return true;
	}
	return false;
}

/** Return true if Payment checked */
function wp_travel_is_payment_enabled() {
	$settings = wp_travel_get_settings();

	$payment_gatway_list = wp_travel_payment_gateway_lists();
	
	if ( is_array( $payment_gatway_list ) && count( $payment_gatway_list ) > 0 ) {
		foreach ( $payment_gatway_list as $gateway => $label ) {
			if ( isset( $settings["payment_option_{$gateway}"] ) && 'yes' === $settings["payment_option_{$gateway}"] ) {
				return true;
			}
		}
	}
	return false;
}

/** Return true if Payment checked */
if ( ! function_exists( 'wp_travel_is_partial_payment_enabled' ) ) {
	function wp_travel_is_partial_payment_enabled() {
		$settings = wp_travel_get_settings();

		if ( isset( $settings['partial_payment'] ) && '' !== $settings['partial_payment'] ) {
			return true;
		}
		return false;
	}
}


function wp_travel_update_payment_status_admin( $post_id ) {
	if ( ! $post_id ) {
		return;
	}
	$payment_id = get_post_meta( $post_id, 'wp_travel_payment_id', true );

	if ( $payment_id ) {
		$payment_status = isset( $_POST['wp_travel_payment_status'] ) ? $_POST['wp_travel_payment_status'] : 'N/A';
		update_post_meta( $payment_id, 'wp_travel_payment_status', $payment_status );
	}
}

function wp_travel_update_payment_status_booking_process_frontend( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
	if ( ! $payment_id ) {
		$title = 'Payment - #' . $booking_id;
		$post_array = array(
			'post_title' => $title,
			'post_content' => '',
			'post_status' => 'publish',
			'post_slug' => uniqid(),
			'post_type' => 'wp-travel-payment',
			);
		$payment_id = wp_insert_post( $post_array );
		update_post_meta( $booking_id, 'wp_travel_payment_id', $payment_id );
	}
	$booking_field_list = wp_travel_booking_form_fields();
	$payment_field_list = wp_travel_payment_field_list();

	foreach ( $payment_field_list as $field_list ) {
		if ( isset( $booking_field_list[ $field_list ]['name'] ) ) {
			$meta_field = $booking_field_list[ $field_list ]['name'];
			if ( isset( $_POST[ $meta_field ] ) ) {
				$meta_value = $_POST[ $meta_field ];
				if ( 'wp_travel_payment_amount' === $meta_field ) {
					continue;
				}

				if ( 'wp_travel_trip_price' === $meta_field ) {

					$itinery_id = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
					$price_per_text = wp_travel_get_price_per_text( $itinery_id );
					if ( isset( $_POST['wp_travel_pax'] ) && 'person' === strtolower( $price_per_text ) ) {
						$meta_value *= $_POST['wp_travel_pax'];						
					}
				}
				update_post_meta( $payment_id, $meta_field, $meta_value );
			}
		}
	}
	update_post_meta( $payment_id, 'wp_travel_payment_status', 'N/A' );
}

/**
 * Send Booking and payment email to admin & customer.
 *
 * @param Number $booking_id Booking ID.
 * @return void
 */
function wp_travel_send_email_payment( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	$settings = wp_travel_get_settings();

	$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';

	// Prepare variables to assign in email.
	$client_email = get_post_meta( $booking_id, 'wp_travel_email', true );

	$admin_email = get_option( 'admin_email' );

	// Email Variables.
	if ( is_multisite() ) {
		$sitename = get_network()->site_name;
	} else {
		/*
			* The blogname option is escaped with esc_html on the way into the database
			* in sanitize_option we want to reverse this for the plain text arena of emails.
			*/
		$sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	$itinerary_id 			= get_post_meta( $booking_id, 'wp_travel_post_id', true );
	$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

	$trip_code = wp_travel_get_trip_code( $itinerary_id );
	$title = 'Booking - ' . $trip_code;

	$itinerary_title 		= get_the_title( $itinerary_id );

	$booking_no_of_pax 		= get_post_meta( $booking_id, 'wp_travel_pax', true );
	$booking_scheduled_date = 'N/A';
	$booking_arrival_date 	= get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$booking_departure_date = get_post_meta( $booking_id, 'wp_travel_departure_date', true );

	$customer_name 		  	= get_post_meta( $booking_id, 'wp_travel_fname', true ) . ' ' . get_post_meta( $booking_id, 'wp_travel_lname', true );
	$customer_country 		= get_post_meta( $booking_id, 'wp_travel_country', true );
	$customer_address 		= get_post_meta( $booking_id, 'wp_travel_address', true );
	$customer_phone 		= get_post_meta( $booking_id, 'wp_travel_phone', true );
	$customer_email 		= get_post_meta( $booking_id, 'wp_travel_email', true );
	$customer_note 			= get_post_meta( $booking_id, 'wp_travel_note', true );

	$wp_travel_payment_status = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
	$wp_travel_payment_mode   = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );
	$trip_price = get_post_meta( $payment_id, 'wp_travel_trip_price', true );
	$payment_amount    = get_post_meta( $payment_id, 'wp_travel_payment_amount', true );

	$email_tags = array(
		'{sitename}'				=> $sitename,
		'{itinerary_link}'			=> get_permalink( $itinerary_id ),
		'{itinerary_title}'			=> $itinerary_title,
		'{booking_id}'				=> $booking_id,
		'{booking_edit_link}'		=> get_edit_post_link( $booking_id ),
		'{booking_no_of_pax}'		=> $booking_no_of_pax,
		'{booking_scheduled_date}'	=> $booking_scheduled_date,
		'{booking_arrival_date}'	=> $booking_arrival_date,
		'{booking_departure_date}'	=> $booking_departure_date,

		'{customer_name}'			=> $customer_name,
		'{customer_country}'		=> $customer_country,
		'{customer_address}'		=> $customer_address,
		'{customer_phone}'			=> $customer_phone,
		'{customer_email}'			=> $customer_email,
		'{customer_note}'			=> $customer_note,
		'{payment_status}'			=> $wp_travel_payment_status,
		'{payment_mode}'			=> $wp_travel_payment_mode,
		'{trip_price}'				=> $trip_price,
		'{payment_amount}'			=> $payment_amount,
		'{currency_symbol}'			=> wp_travel_get_currency_symbol(),
	);

	$email = new WP_Travel_Emails();
	
	// Send mail to admin if booking email is set to yes.
	if ( 'yes' == $send_booking_email_to_admin ) {
		// Admin Payment Email Vars.
		$admin_payment_template = $email->wp_travel_get_email_template( 'payments', 'admin' );
		//Admin message.
		$admin_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_payment_template['mail_content'] );
		//Admin Subject.
		$admin_payment_subject = $admin_payment_template['subject'];

		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $client_email, $client_email );

		if ( ! wp_mail( $admin_email, $admin_payment_subject, $admin_payment_message, $headers ) ) {
			$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $_SERVER['REDIRECT_URL'] );
			$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
			header( 'Location: ' . $thankyou_page_url );
			exit;
		}
	}

	// Send email to client.
	// Client Payment Email Vars.
	$client_payment_template = $email->wp_travel_get_email_template( 'payments', 'client' );
	// Client Payment message.
	$client_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $client_payment_template['mail_content'] );
	// Client Payment Subject.
	$client_payment_subject = $client_payment_template['subject'];

	// To send HTML mail, the Content-type header must be set.
	$headers = $email->email_headers( $admin_email, $admin_email );

	if ( ! wp_mail( $client_email, $client_payment_subject, $client_payment_message, $headers ) ) {
		$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $_SERVER['REDIRECT_URL'] );
			$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
			header( 'Location: ' . $thankyou_page_url );
			exit;
	}
}

/**
 * Update Payment After payment Success.
 *
 * @param Number $booking_id Booking ID.
 * @param Number $amount Payment Amount.
 * @param String $status Payment Status.
 * @param Arrays $args Payment Args.
 * @param string $key Payment args Key.
 * @return void
 */
function wp_travel_update_payment_status( $booking_id, $amount, $status, $args, $key = '_paypal_args' ) {
		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
		update_post_meta( $payment_id, 'wp_travel_payment_amount', $amount );
		update_post_meta( $payment_id, $key, $args );
		update_post_meta( $payment_id, 'wp_travel_payment_status', $status );
}

/**
 * Return booking message.
 *
 * @param String $message Booking message
 * @return void
 */
function wp_travel_payment_booking_message( $message ) {
	if ( ! isset( $_GET['booking_id'] ) ) {
		return $message;
	}
	$booking_id = $_GET['booking_id'];
	if ( isset( $_GET['status'] ) && 'cancel' === $_GET['status'] ) {
		update_post_meta( $booking_id, 'wp_travel_payment_status', 'canceled' );
		$message = esc_html__( 'Your booking has been canceled', 'wp-travel' );
	}
	if ( isset( $_GET['status'] ) && 'success' === $_GET['status'] ) {
		// already upadted status.
		$message = esc_html__( "We've received your booking and payment details. We'll contact you soon.", 'wp-travel' );
	}
	return $message;
}

// Calculate Total Cart amount.
function wp_travel_get_total_amount() {
	$response = array( 'status' => 'fail', 'message' => __( 'Invalid' ),  'total_amount' => 0, 'payment_amount' => 0 );
	if ( ! isset( $_GET['wt_query_amount'] ) ) {
		return;
	}
	// if ( ! wp_travel_is_checkout_page() ) {
	// 	$response['message'] = __( 'Invalid Page' );
	// 	// return;
	// }
	$settings = wp_travel_get_settings();

	$pax 		= isset( $_GET['pax'] ) && $_GET['pax'] > 0 ? $_GET['pax'] : 1;
	$trip_id	= isset( $_GET['trip_id'] ) ? $_GET['trip_id'] : 0;
	if ( is_singular( WP_TRAVEL_POST_TYPE ) ) {
		$trip_id = get_the_ID();
	}

	$price_per 	= wp_travel_get_price_per_text( $trip_id );

	if ( $trip_id < 1 ) {
		$response['message'] = __( 'Trip not selected' );
	}

	if ( ! wp_travel_is_itinerary( $trip_id ) ) {
		$response['message'] = __( 'Invalid post type' );
	}

	$trip_price_tax = wp_travel_process_trip_price_tax( $trip_id );
	if ( isset( $trip_price_tax['actual_trip_price'] ) ) {
		$response['total_amount'] = $payment_amount = $trip_price_tax['actual_trip_price'];
	}
	else {
		$response['total_amount'] = $payment_amount = $trip_price_tax['trip_price'];
	}

	$response['payment_amount'] = $response['total_amount'];

	if ( isset( $settings['partial_payment'] ) && 'yes' === $settings['partial_payment'] ) {
		$response['payment_amount'] = wp_travel_minimum_partial_payout( $trip_id );
	}

	// Success.
	if ( $response['payment_amount'] > 0 && $response['total_amount'] > 0 ) {
		$response['status'] = 'success';
		$response['message'] = __( 'Success' );

		if ( strtolower( $price_per ) === 'person' ) {
			$response['total_amount'] *= $pax;
			$response['payment_amount'] *= $pax;
		}
	}

	echo wp_json_encode( $response );
	die;
}
add_action( 'wp', 'wp_travel_get_total_amount' );
add_action( 'wp_travel_after_booking_data_save', 'wp_travel_update_payment_status_admin' );
add_action( 'wt_before_payment_process', 'wp_travel_update_payment_status_booking_process_frontend' );
add_action( 'wp_travel_after_successful_payment', 'wp_travel_send_email_payment' );
add_filter( 'wp_travel_booked_message', 'wp_travel_payment_booking_message' );


