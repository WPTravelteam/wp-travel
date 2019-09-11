<?php
interface Wp_Travel_Payment_Interface {
	public function process_payment();

	public function render_settings();
}

$GLOBALS['wp_travel_payments'] = array();

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
		'bank_deposit' => __( 'Bank Deposit', 'wp-travel' ),
	);
	return apply_filters( 'wp_travel_payment_gateway_lists', $gateway );

}

// Return sorted payment gateway list.
function wp_travel_sorted_payment_gateway_lists() {
	$settings = wp_travel_get_settings();

	$default_gateways      = wp_travel_payment_gateway_lists();
	$default_gateways_keys = array_keys( wp_travel_payment_gateway_lists() );

	$sorted_gateways = isset( $settings['sorted_gateways'] ) ? $settings['sorted_gateways'] : array();

	// remove if gateway not listed in default [ due to deactivated plugin ].
	if ( is_array( $sorted_gateways ) && count( $sorted_gateways ) > 0 && count( $default_gateways_keys ) > 0 ) {
		foreach ( $sorted_gateways as $key => $gateway ) {
			if ( ! in_array( $gateway, $default_gateways_keys ) ) {
				unset( $sorted_gateways[ $key ] );
			}
		}
	}

	// List newly added payment gateway into sorting list.
	foreach ( $default_gateways_keys as $gateway ) {
		if ( ! in_array( $gateway, $sorted_gateways ) ) {
			$sorted_gateways[] = $gateway;
		}
	}

	if ( empty( $sorted_gateways ) ) {
		$sorted_gateways = $default_gateways_keys;
	}
	// assign label into gateway.

	$sorted = array();
	foreach ( $sorted_gateways as $gateway_key ) {
		$sorted[ $gateway_key ] = $default_gateways[ $gateway_key  ];
	}


	return $sorted;
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
	$trip_price  = wp_travel_get_actual_trip_price( $post_id );
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
	// $settings = wp_travel_get_settings();
	// $payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	// $trip_price = wp_travel_get_actual_trip_price( $post_id );
	// $minimum_payout = ( $trip_price * $payout_percent ) / 100;
	// }
}


/**
 * Get Minimum payout amount
 *
 * @param Number $post_id Post ID.
 * @return Number
 */
function wp_travel_variable_pricing_minimum_partial_payout( $post_id, $price, $tax_details ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_price  = $price;
	$tax_details = $tax_details;

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
	// $settings = wp_travel_get_settings();
	// $payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	// $trip_price = wp_travel_get_actual_trip_price( $post_id );
	// $minimum_payout = ( $trip_price * $payout_percent ) / 100;
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
	$settings               = wp_travel_get_settings();
	$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

	$payout_percent = $default_payout_percent;
	$use_global     = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_use_global', true );

	$trip_payout_percent = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_percent', true );

	if ( ! $use_global && $trip_payout_percent ) {
		$payout_percent = $trip_payout_percent;
	}

	$payout_percent = apply_filters( 'wp_travel_payout_percent', $payout_percent, $post_id );
	return number_format( $payout_percent, 2, '.', '' );
}

function wp_travel_get_actual_payout_percent( $post_id ) {
	if ( ! $post_id ) {
		return 0;
	}
	if ( wp_travel_use_global_payout_percent( $post_id ) ) {
		$settings                      = wp_travel_get_settings();
		return $default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
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

/**
 * List of enabled payment gateways.
 *
 * @return array
 */
function wp_travel_enabled_payment_gateways() {
	$gateways            = array();
	$settings            = wp_travel_get_settings();
	$payment_gatway_list = wp_travel_payment_gateway_lists();
	if ( is_array( $payment_gatway_list ) && count( $payment_gatway_list ) > 0 ) {
		foreach ( $payment_gatway_list as $gateway => $label ) {
			if ( isset( $settings[ "payment_option_{$gateway}" ] ) && 'yes' === $settings[ "payment_option_{$gateway}" ] ) {
				$gateways[] = $gateway;
			}
		}
	}
	return $gateways;
}

/** Return true if Payment checked */
function wp_travel_is_payment_enabled() {
	$enabled_payment_gateways = wp_travel_enabled_payment_gateways();
	return ! empty( $enabled_payment_gateways ) ? true : false;
}

/** Return true if Payment checked */
if ( ! function_exists( 'wp_travel_is_partial_payment_enabled' ) ) {
	function wp_travel_is_partial_payment_enabled() {
		$settings = wp_travel_get_settings();

		return ( isset( $settings['partial_payment'] ) && 'yes' === $settings['partial_payment'] );
	}
}


function wp_travel_update_payment_status_admin( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	$payment_id = wp_travel_get_payment_id( $booking_id );

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
		$title      = 'Payment - #' . $booking_id;
		$post_array = array(
			'post_title'   => $title,
			'post_content' => '',
			'post_status'  => 'publish',
			'post_slug'    => uniqid(),
			'post_type'    => 'wp-travel-payment',
		);
		$payment_id = wp_insert_post( $post_array );
		update_post_meta( $booking_id, 'wp_travel_payment_id', $payment_id );
	}
	$booking_field_list = wp_travel_get_checkout_form_fields();
	$payment_field_list = wp_travel_payment_field_list();

	foreach ( $payment_field_list as $field_list ) {
		if ( isset( $booking_field_list['payment_fields'][ $field_list ]['name'] ) ) {
			$meta_field = $booking_field_list['payment_fields'][ $field_list ]['name'];
			if ( isset( $_POST[ $meta_field ] ) ) {
				$meta_value = $_POST[ $meta_field ];
				if ( 'wp_travel_payment_amount' === $meta_field ) {
					continue;
				}

				if ( 'wp_travel_trip_price' === $meta_field ) {

					$itinery_id     = isset( $_POST['wp_travel_post_id'] ) ? $_POST['wp_travel_post_id'] : 0;
					$price_per_text = wp_travel_get_price_per_text( $itinery_id );
					if ( isset( $_POST['wp_travel_pax'] ) && 'person' === strtolower( $price_per_text ) ) {
						$meta_value *= $_POST['wp_travel_pax'];
					}
				}
				update_post_meta( $payment_id, $meta_field, $meta_value );
			}
		}
	}
	// update_post_meta( $payment_id, 'wp_travel_payment_status', 'N/A' );
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
	$order_items = get_post_meta( $booking_id, 'order_items_data', true );

	$price_keys = array();
	foreach ( $order_items as $key => $item ) {
		$price_keys[] = $item['price_key'];
	}

	$order_items = ( $order_items && is_array( $order_items ) ) ? count( $order_items ) : 1;

	$allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );

	$price_key = false;
	if ( ! $allow_multiple_cart_items || ( 1 === $order_items ) ) {
		$price_key = isset( $price_keys[0] ) ? $price_keys[0] : '';
	}

	// Handle Multiple payment Emails.
	if ( $allow_multiple_cart_items && 1 !== $order_items ) {
		do_action( 'wp_travel_multiple_payment_emails', $booking_id );
		exit;
	}

	// Clearing cart after successfult payment.
	global $wt_cart;
	$wt_cart->clear();

	$settings = wp_travel_get_settings();

	$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';

	$first_name = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
	$last_name  = get_post_meta( $booking_id, 'wp_travel_lname_traveller', true );
	$country    = get_post_meta( $booking_id, 'wp_travel_country_traveller', true );
	$phone      = get_post_meta( $booking_id, 'wp_travel_phone_traveller', true );
	$email      = get_post_meta( $booking_id, 'wp_travel_email_traveller', true );

	reset( $first_name );
	$first_key = key( $first_name );

	$first_name = isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ? $first_name[ $first_key ][0] : '';
	$last_name  = isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ? $last_name[ $first_key ][0] : '';
	$country    = isset( $country[ $first_key ] ) && isset( $country[ $first_key ][0] ) ? $country[ $first_key ][0] : '';
	$phone      = isset( $phone[ $first_key ] ) && isset( $phone[ $first_key ][0] ) ? $phone[ $first_key ][0] : '';
	$email      = isset( $email[ $first_key ] ) && isset( $email[ $first_key ][0] ) ? $email[ $first_key ][0] : '';

	// Prepare variables to assign in email.
	$client_email = $email;

	$site_admin_email = get_option( 'admin_email' );

	$admin_email = apply_filters( 'wp_travel_payments_admin_emails', $site_admin_email );

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

	$itinerary_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	$payment_id   = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

	$trip_code = wp_travel_get_trip_code( $itinerary_id );
	$title     = 'Booking - ' . $trip_code;

	$itinerary_title = get_the_title( $itinerary_id );

	$booking_no_of_pax      = get_post_meta( $booking_id, 'wp_travel_pax', true );
	$booking_scheduled_date = 'N/A';
	$date_format            = get_option( 'date_format' );

	$booking_arrival_date   = get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$booking_departure_date = get_post_meta( $booking_id, 'wp_travel_departure_date', true );

	$booking_arrival_date   = ( '' !== $booking_arrival_date ) ? wp_travel_format_date( $booking_arrival_date, true, 'Y-m-d' ) : '';
	$booking_departure_date = ( '' !== $booking_departure_date ) ? wp_travel_format_date( $booking_departure_date, true, 'Y-m-d' ) : '';

	$customer_name    = $first_name . ' ' . $last_name;
	$customer_country = $country;
	$customer_address = get_post_meta( $booking_id, 'wp_travel_address', true );
	$customer_phone   = $phone;
	$customer_email   = $client_email;
	$customer_note    = get_post_meta( $booking_id, 'wp_travel_note', true );

	$wp_travel_payment_status = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
	$wp_travel_payment_mode   = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );
	$trip_price               = get_post_meta( $payment_id, 'wp_travel_trip_price', true );
	$payment_amount           = get_post_meta( $payment_id, 'wp_travel_payment_amount', true );

	$email_tags = array(
		'{sitename}'               => $sitename,
		'{itinerary_link}'         => get_permalink( $itinerary_id ),
		'{itinerary_title}'        => wp_travel_get_trip_pricing_name( $itinerary_id, $price_key ),
		'{booking_id}'             => $booking_id,
		'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
		'{booking_no_of_pax}'      => $booking_no_of_pax,
		'{booking_scheduled_date}' => $booking_scheduled_date,
		'{booking_arrival_date}'   => $booking_arrival_date,
		'{booking_departure_date}' => $booking_departure_date,

		'{customer_name}'          => $customer_name,
		'{customer_country}'       => $customer_country,
		'{customer_address}'       => $customer_address,
		'{customer_phone}'         => $customer_phone,
		'{customer_email}'         => $customer_email,
		'{customer_note}'          => $customer_note,
		'{payment_status}'         => $wp_travel_payment_status,
		'{payment_mode}'           => $wp_travel_payment_mode,
		'{trip_price}'             => wp_travel_get_formated_price_currency( $trip_price ),
		'{payment_amount}'         => wp_travel_get_formated_price_currency( $payment_amount ),
		'{currency_symbol}'        => '', // Depricated tag @since 2.0.1.
		'{currency}'               => wp_travel_get_currency_symbol(),
	);

	$email_tags = apply_filters( 'wp_travel_payment_email_tags', $email_tags );

	$email = new WP_Travel_Emails();
	$reply_to_email = isset( $settings['wp_travel_from_email'] ) ? $settings['wp_travel_from_email'] : $site_admin_email;

	// Send mail to admin if booking email is set to yes.
	if ( 'yes' == $send_booking_email_to_admin ) {
		// Admin Payment Email Vars.
		$admin_payment_template = $email->wp_travel_get_email_template( 'payments', 'admin' );

		$admin_message_data  = $admin_payment_template['mail_header'];
		$admin_message_data .= $admin_payment_template['mail_content'];
		$admin_message_data .= $admin_payment_template['mail_footer'];

		// Admin message.
		$admin_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message_data );
		// Admin Subject.
		$admin_payment_subject = $admin_payment_template['subject'];

		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $reply_to_email, $client_email );

		if ( ! wp_mail( $admin_email, $admin_payment_subject, $admin_payment_message, $headers ) ) {
			$thankyou_page_url = $_SERVER['REDIRECT_URL'];
			$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
			$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url, $booking_id );
			header( 'Location: ' . $thankyou_page_url );
			exit;
		}
	}

	// Send email to client.
	// Client Payment Email Vars.
	$client_payment_template = $email->wp_travel_get_email_template( 'payments', 'client' );

	$client_message_data  = $client_payment_template['mail_header'];
	$client_message_data .= $client_payment_template['mail_content'];
	$client_message_data .= $client_payment_template['mail_footer'];

	// Client Payment message.
	$client_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $client_message_data );
	// Client Payment Subject.
	$client_payment_subject = $client_payment_template['subject'];

	// To send HTML mail, the Content-type header must be set.
	$headers = $email->email_headers( $reply_to_email, $reply_to_email );

	if ( ! wp_mail( $client_email, $client_payment_subject, $client_payment_message, $headers ) ) {
		$thankyou_page_url = $_SERVER['REDIRECT_URL'];
		$thankyou_page_url = add_query_arg( 'booked', 'false', $thankyou_page_url );
		$thankyou_page_url = apply_filters( 'wp_travel_thankyou_page_url', $thankyou_page_url, $booking_id );
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
function wp_travel_update_payment_status( $booking_id, $amount, $status, $args, $key = '_paypal_args', $payment_id = null ) {
	if ( ! $payment_id ) {
		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		// need to get last payment id here. remaining.
	}

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
	$response = array(
		'status'  => 'fail',
		'message' => __( 'Invalid', 'wp-travel' ),
	);
	if ( ! isset( $_GET['wt_query_amount'] ) ) {
		return;
	}

	$settings = wp_travel_get_settings();
	global $wt_cart;

	$cart_amounts = $wt_cart->get_total();

	$total = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : 0;

	if ( wp_travel_is_partial_payment_enabled() && isset( $_REQUEST['partial'] ) && $_REQUEST['partial'] ) {
		$total = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : 0;
	}

	if ( $total > 0 ) {
		$response['status']  = 'success';
		$response['message'] = __( 'Success', 'wp-travel' );
		$response['total']   = $total;
	}
	wp_send_json( $response );
}

/**
 * Return Active Payment gateway list.
 */
function wp_travel_get_active_gateways() {
	$payment_gatway_list = wp_travel_sorted_payment_gateway_lists();
	$active_gateway_list = array();
	$selected_gateway    = '';
	$settings            = wp_travel_get_settings();
	$gateway_list        = array();
	if ( is_array( $payment_gatway_list ) && count( $payment_gatway_list ) > 0 ) {
		foreach ( $payment_gatway_list as $gateway => $label ) {
			if ( isset( $settings[ "payment_option_{$gateway}" ] ) && 'yes' === $settings[ "payment_option_{$gateway}" ] ) {
				if ( '' === $selected_gateway ) {
					$gateway_list['selected'] = $gateway;
				}
				$active_gateway_list[ $gateway ] = $label;
			}
		}
		$gateway_list['active'] = $active_gateway_list;
	}
	if ( isset( $gateway_list['selected'] ) ) {
		$gateway_list['selected'] = apply_filters( 'wp_travel_selected_payment_gateway', $gateway_list['selected'] );
	}
	return $gateway_list;
}

add_action( 'wp', 'wp_travel_get_total_amount' );
add_action( 'wp_travel_after_booking_data_save', 'wp_travel_update_payment_status_admin' );
add_action( 'wt_before_payment_process', 'wp_travel_update_payment_status_booking_process_frontend' );
add_action( 'wp_travel_after_successful_payment', 'wp_travel_send_email_payment' );
add_filter( 'wp_travel_booked_message', 'wp_travel_payment_booking_message' );
