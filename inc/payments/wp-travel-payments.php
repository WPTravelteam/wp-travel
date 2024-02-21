<?php
interface Wp_Travel_Payment_Interface {
	public function process_payment();

	public function render_settings();
}

$GLOBALS['wp_travel_payments'] = array();

if ( ! function_exists( 'wptravel_register_payments' ) ) {

	/**
	 * Register payments here
	 *
	 * @param Object $object Payment Object.
	 */
	function wptravel_register_payments( $object ) {

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
function wptravel_payment_field_list() {
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
function wptravel_payment_gateway_lists() {
	$gateway = array(
		'paypal'       => __( 'Standard Paypal', 'wp-travel' ),
		'bank_deposit' => __( 'Bank Deposit', 'wp-travel' ),
	);
	return apply_filters( 'wp_travel_payment_gateway_lists', $gateway );

}

// Return sorted payment gateway list.
function wptravel_sorted_payment_gateway_lists() {
	$settings = wptravel_get_settings();

	$default_gateways      = wptravel_payment_gateway_lists();
	$default_gateways_keys = array_keys( wptravel_payment_gateway_lists() );

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
		$sorted[ $gateway_key ] = $default_gateways[ $gateway_key ];
	}

	return $sorted;
}

/**
 * Get Minimum payout amount
 *
 * @param Number $trip_id Post ID.
 * @return Number
 */
function wptravel_minimum_partial_payout( $trip_id ) {
	if ( ! $trip_id ) {
		return 0;
	}
	$args        = array( 'trip_id' => $trip_id );
	$trip_price  = WP_Travel_Helpers_Pricings::get_price( $args );
	$tax_details = wptravel_process_trip_price_tax( $trip_id );

	if ( is_array( $tax_details ) && isset( $tax_details['tax_type'] ) ) {

		if ( 'excluxive' === $tax_details['tax_type'] ) {

			$trip_price = $tax_details['actual_trip_price'];

		}
	}
	$payout_percent = wptravel_get_actual_payout_percent( $trip_id );
	$minimum_payout = ( $trip_price * $payout_percent ) / 100;
	return number_format( $minimum_payout, 2, '.', '' );
}


/**
 * Get Minimum payout amount
 *
 * @param Number $post_id Post ID.
 * @return Number
 */
function wptravel_variable_pricing_minimum_partial_payout( $post_id, $price, $tax_details ) {
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
	$payout_percent = wptravel_get_actual_payout_percent( $post_id );
	$minimum_payout = ( $trip_price * $payout_percent ) / 100;
	return number_format( $minimum_payout, 2, '.', '' );

}

/**
 * Get Minimum payout amount
 *
 * @param Number $trip_id Post ID.
 * @return Number
 */
function wptravel_get_payout_percent( $trip_id ) {
	if ( ! $trip_id ) {
		return 0;
	}
	$settings = wptravel_get_settings();
	// Global Payout percent.
	$payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	$use_global     = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_use_global', true );

	$trip_payout_percent = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_percent', true );

	if ( ! $use_global && $trip_payout_percent ) {
		$payout_percent = $trip_payout_percent;
	}

	$payout_percent = apply_filters( 'wp_travel_payout_percent', $payout_percent, $trip_id );
	$payout_percent = wptravel_initial_partial_payout_unformated( $payout_percent );
	return number_format( $payout_percent, 2, '.', '' );
}

function wptravel_get_actual_payout_percent( $post_id ) {
	if ( ! $post_id ) {
		return 0;
	}
	if ( wptravel_use_global_payout_percent( $post_id ) ) {
		$settings               = wptravel_get_settings();
		$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
		$default_payout_percent = wptravel_initial_partial_payout_unformated( $default_payout_percent );
		return $default_payout_percent;
	}

	return wptravel_get_payout_percent( $post_id );
}

/**
 * Returns the minimum or initial partial payout, no matter if payout was saved as string or array.
 *
 * @param mixed   $partial_payout Int|Float|String|Array.
 * @param boolean $force_format   True if you want to force format number.
 * @return float  $partial_payout Minimum partial payout.
 */
function wptravel_initial_partial_payout_unformated( $partial_payout, $force_format = false ) {
	if ( empty( $partial_payout ) ) {
		return $partial_payout;
	}
	if ( is_array( $partial_payout ) && isset( $partial_payout[0] ) ) {
		$partial_payout = $partial_payout[0];
	}
	if ( is_string( $partial_payout ) ) {
		$partial_payout = (float) $partial_payout;
	}
	if ( $force_format ) {
		$partial_payout = number_format( $partial_payout, 2, '.', '' );
	}
	return $partial_payout;
}

function wptravel_use_global_payout_percent( $post_id ) {
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
function wptravel_test_mode() {
	$settings = wptravel_get_settings();
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
function wptravel_enabled_payment_gateways() {
	$gateways            = array();
	$settings            = wptravel_get_settings();
	$payment_gatway_list = wptravel_payment_gateway_lists();
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
function wptravel_is_payment_enabled() {
	$enabled_payment_gateways = wptravel_enabled_payment_gateways();

	$enabled = ! empty( $enabled_payment_gateways ) ? true : false;
	/**
	 * Filter to customize whether payment enabled or not.
	 *
	 * @since 5.3.1
	 */
	$enabled = apply_filters( 'wptravel_is_payment_enabled', $enabled );
	return $enabled;
}

/** Return true if Payment checked */
if ( ! function_exists( 'wptravel_is_partial_payment_enabled' ) ) {
	function wptravel_is_partial_payment_enabled() {
		$settings = wptravel_get_settings();

		return ( isset( $settings['partial_payment'] ) && 'yes' === $settings['partial_payment'] );
	}
}


function wptravel_update_payment_status_admin( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	if ( ! isset( $_POST['wp_travel_security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_travel_security'] ) ), 'wp_travel_security_action' ) ) {
		return;
	}
	$payment_id = wptravel_get_payment_id( $booking_id );

	if ( $payment_id ) {
		$payment_status = isset( $_POST['wp_travel_payment_status'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_payment_status'] ) ) : 'N/A';
		update_post_meta( $payment_id, 'wp_travel_payment_status', $payment_status );

		update_post_meta( $booking_id, 'wp_travel_payment_status', $payment_status ); // Since WP Travel 5.0.2
		update_post_meta( $booking_id, 'wp_travel_payment_mode', 'partial' );  // Since WP Travel 5.0.2
	}
}

function wptravel_update_payment_status_booking_process_frontend( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}

	if ( ! WP_Travel::verify_nonce( true ) ) {
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
	$booking_field_list = wptravel_get_checkout_form_fields();
	$payment_field_list = wptravel_payment_field_list();

	foreach ( $payment_field_list as $field_list ) {
		if ( isset( $booking_field_list['payment_fields'][ $field_list ]['name'] ) ) {
			$meta_field = $booking_field_list['payment_fields'][ $field_list ]['name'];
			if ( isset( $_POST[ $meta_field ] ) ) {
				$meta_value = sanitize_text_field( wp_unslash( $_POST[ $meta_field ] ) );
				if ( 'wp_travel_payment_amount' === $meta_field ) {
					continue;
				}

				if ( 'wp_travel_trip_price' === $meta_field ) {

					$itinery_id     = isset( $_POST['wp_travel_post_id'] ) ? absint( $_POST['wp_travel_post_id'] ) : 0;
					$price_per_text = wptravel_get_price_per_text( $itinery_id );
					if ( isset( $_POST['wp_travel_pax'] ) && 'person' === strtolower( $price_per_text ) ) {
						$meta_value *= absint( $_POST['wp_travel_pax'] );
					}
				}
				update_post_meta( $payment_id, $meta_field, sanitize_text_field( $meta_value ) );
			}
		}
	}
}

/**
 * Send Booking and payment email to admin & customer.
 *
 * @param Number $booking_id Booking ID.
 * @return void
 */
function wptravel_send_email_payment( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	do_action( 'wp_travel_before_payment_email_send', $booking_id );
	$order_items = get_post_meta( $booking_id, 'order_items_data', true );

	$price_keys = array();
	foreach ( $order_items as $key => $item ) {
		$price_keys[] = $item['price_key'];
	}

	$order_items = ( $order_items && is_array( $order_items ) ) ? count( $order_items ) : 1;

	$allow_multiple_items = WP_Travel_Cart::allow_multiple_items();

	$price_key = false;
	if ( ! $allow_multiple_items || ( 1 === $order_items ) ) {
		$price_key = isset( $price_keys[0] ) ? $price_keys[0] : '';
	}

	// Handle Multiple payment Emails.
	// if ( $allow_multiple_items && 1 !== $order_items ) {
	// do_action( 'wp_travel_multiple_payment_emails', $booking_id );
	// exit;
	// }

	// Clearing cart after successfult payment.
	global $wt_cart;
	$wt_cart->clear();

	$settings = wptravel_get_settings();

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

	// $trip_code = wptravel_get_trip_code( $itinerary_id );
	// $title     = 'Booking - ' . $trip_code;

	// $itinerary_title = get_the_title( $itinerary_id );

	$booking_no_of_pax      = get_post_meta( $booking_id, 'wp_travel_pax', true );
	$booking_scheduled_date = 'N/A';
	$date_format            = get_option( 'date_format' );

	$booking_arrival_date   = get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$booking_departure_date = get_post_meta( $booking_id, 'wp_travel_departure_date', true );

	$booking_arrival_date   = ( '' !== $booking_arrival_date ) ? wptravel_format_date( $booking_arrival_date, true, 'Y-m-d' ) : '';
	$booking_departure_date = ( '' !== $booking_departure_date ) ? wptravel_format_date( $booking_departure_date, true, 'Y-m-d' ) : '';

	$arrival_date_email_tag           = wptravel_format_date( $booking_arrival_date, true, 'Y-m-d' );  // email tag date only.
	$wp_travel_arrival_date_email_tag = get_post_meta( $booking_id, 'wp_travel_arrival_date_email_tag', true );
	if ( $wp_travel_arrival_date_email_tag ) {
		$arrival_date_email_tag = $wp_travel_arrival_date_email_tag; // email date tag along with time.
	}

	$customer_gender   = isset( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'] ) ? get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'][array_key_first( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'])][0] : '';

	if( apply_filters( 'wptravel_traveller_salutation', true ) ==  true ){
		if( $customer_gender == 'male' ){
			$salutation = __( 'Mr ', 'wp-travel' );
		}elseif( $customer_gender == 'female' ){
			$salutation = __( 'Miss ', 'wp-travel' );
		}else{
			$salutation = '';
		}
	}else{
		$salutation = '';
	}

	

	$customer_name    = $salutation.$first_name . ' ' . $last_name;

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
		'{itinerary_title}'        => wptravel_get_trip_pricing_name( $itinerary_id, $price_key ),
		'{booking_id}'             => $booking_id,
		'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
		'{booking_no_of_pax}'      => $booking_no_of_pax,
		'{booking_scheduled_date}' => $booking_scheduled_date,
		'{booking_arrival_date}'   => $arrival_date_email_tag,
		'{booking_departure_date}' => $booking_departure_date,

		'{customer_name}'          => $customer_name,
		'{customer_country}'       => $customer_country,
		'{customer_address}'       => $customer_address,
		'{customer_phone}'         => $customer_phone,
		'{customer_email}'         => $customer_email,
		'{customer_note}'          => $customer_note,
		'{payment_status}'         => $wp_travel_payment_status,
		'{payment_mode}'           => $wp_travel_payment_mode,
		'{trip_price}'             => wptravel_get_formated_price_currency( $trip_price ),
		'{payment_amount}'         => wptravel_get_formated_price_currency( $payment_amount ),
		'{currency_symbol}'        => '', // Depricated tag @since 2.0.1.
		'{currency}'               => wptravel_get_currency_symbol(),
		'{booking_info}'           => wptravel_booking_info_table( $booking_id ),
		'{booking_details}'        => WpTravel_Helpers_Booking::render_booking_details( $booking_id ),
		'{traveler_details}'       => WpTravel_Helpers_Booking::render_traveler_details( $booking_id ),
		'{payment_details}'        => WpTravel_Helpers_Payment::render_payment_details( $booking_id ),
	);

	/**
	 * Hook To modify payment email Tag
	 *
	 * @since 2.0.1
	 * @since 5.3.1 Added booking id Param.
	 */
	$email_tags = apply_filters( 'wp_travel_payment_email_tags', $email_tags, $booking_id );

	$email          = new WP_Travel_Emails();
	$reply_to_email = isset( $settings['wp_travel_from_email'] ) ? $settings['wp_travel_from_email'] : $site_admin_email;

	// Send mail to admin if booking email is set to yes.
	if ( 'yes' == $send_booking_email_to_admin ) {
		// Admin Payment Email Vars.
		$admin_payment_template = $email->wptravel_get_email_template( 'payments', 'admin' );

		$admin_message_data  = $admin_payment_template['mail_header'];
		$admin_message_data .= $admin_payment_template['mail_content'];
		$admin_message_data .= $admin_payment_template['mail_footer'];
		$admin_message_data  = apply_filters( 'wp_travel_admin_payment_email', $admin_message_data, $booking_id );

		// Admin message.
		$admin_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $admin_message_data );
		// Admin Subject.
		$admin_payment_subject = str_replace( array_keys( $email_tags ), $email_tags, $admin_payment_template['subject'] );

		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $reply_to_email, $client_email );
		$payment_admin_mail = apply_filters( 'wp_travel_payment_admin_mail', true );
		if ( $payment_admin_mail == true ) {
			if ( ! wp_mail( $admin_email, $admin_payment_subject, $admin_payment_message, $headers ) ) {
				WPTravel()->notices->add( __( 'Your Payment has been received but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
			}
		}
	}

	// Send email to client.
	// Client Payment Email Vars.
	$client_payment_template = $email->wptravel_get_email_template( 'payments', 'client' );

	$client_message_data  = $client_payment_template['mail_header'];
	$client_message_data .= $client_payment_template['mail_content'];
	$client_message_data .= $client_payment_template['mail_footer'];
	$client_message_data  = apply_filters( 'wp_travel_client_payment_email', $client_message_data, $booking_id );

	// Client Payment message.
	$client_payment_message = str_replace( array_keys( $email_tags ), $email_tags, $client_message_data );
	// Client Payment Subject.
	$client_payment_subject = str_replace( array_keys( $email_tags ), $email_tags, $client_payment_template['subject'] );

	// To send HTML mail, the Content-type header must be set.
	$headers = $email->email_headers( $reply_to_email, $reply_to_email );
	$payment_client_mail = apply_filters( 'wp_travel_payment_admin_mail', true );
	if ( $payment_client_mail == true ) {
		if ( ! wp_mail( $client_email, $client_payment_subject, $client_payment_message, $headers ) ) {
			WPTravel()->notices->add( __( 'Your Payment has been received but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
		}
	}

	$email_data = array(
		'from' => $reply_to_email,
		'to'   => $customer_email,
	);
	do_action( 'wp_travel_after_payment_email_sent', $booking_id, $email_data, $email_tags ); // @since 3.0.6 for invoice.
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
function wptravel_update_payment_status( $booking_id, $amount, $status, $args, $key = '_paypal_args', $payment_id = null ) {
	if ( ! $payment_id ) {
		$payment_id = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
		// need to get last payment id here. remaining.
	}
	
	update_post_meta( $booking_id, 'wp_travel_booking_status', 'booked' );
	update_post_meta( $payment_id, 'wp_travel_payment_amount', $amount );
	update_post_meta( $payment_id, 'wp_travel_payment_booking_id', $booking_id );
	update_post_meta( $payment_id, $key, $args );

	$payment_mode = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );
	$details      = wptravel_booking_data( $booking_id );
	$due_amount   = ! empty( $details['due_amount'] ) ? $details['due_amount'] : '';
	if ( 'partial' === $payment_mode ) {
		if ( '0.00' !== $due_amount ) { // if due amount is not 0 and mode is partial. ( In case of partial).
			$status = 'partially_paid';
		} else { // If due amount is 0 and mode is partial. ( This is also for first booking only and then pay later from dashboard as it takes this payment as partial payment mode. And to quick fix, this has been added since 4.3.4 ).
			update_post_meta( $payment_id, 'wp_travel_payment_mode', 'full' ); // @todo remove latter.
			update_post_meta( $booking_id, 'wp_travel_payment_mode', 'full' ); // Since WP Travel 5.0.2 Need to ignore payment meta.
		}
	}

	update_post_meta( $payment_id, 'wp_travel_payment_status', $status ); // @todo remove latter.
	update_post_meta( $booking_id, 'wp_travel_payment_status', $status ); // Since WP Travel 5.0.2 Need to ignore payment meta.
}

/**
 * Return booking message.
 *
 * @param String $message Booking message.
 * @return void
 */
function wptravel_payment_booking_message( $message ) {
	if ( ! WP_Travel::verify_nonce( true ) ) {
		return $message;
	}

	if ( ! isset( $_GET['booking_id'] ) ) {
		return $message;
	}
	$booking_id = absint( $_GET['booking_id'] );
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
function wptravel_get_total_amount() {
	if ( ! WP_Travel::verify_nonce( true ) ) {
		return;
	}

	$response = array(
		'status'  => 'fail',
		'message' => __( 'Invalid', 'wp-travel' ),
	);
	if ( ! isset( $_GET['wt_query_amount'] ) ) {
		return;
	}

	$settings = wptravel_get_settings();
	global $wt_cart;

	$cart_amounts = $wt_cart->get_total();

	$total = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : 0;

	if ( wptravel_is_partial_payment_enabled() && isset( $_REQUEST['partial'] ) && sanitize_text_field( wp_unslash( $_REQUEST['partial'] ) ) ) {
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
function wptravel_get_active_gateways() {
	$payment_gatway_list = wptravel_sorted_payment_gateway_lists();
	$active_gateway_list = array();
	$selected_gateway    = '';
	$settings            = wptravel_get_settings();
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

function wptravel_booking_info_table( $booking_id ) {

	$items            = get_post_meta( $booking_id, 'order_items_data', true );
	$order_items_data = get_post_meta( $booking_id, 'order_data', true ); // includes travelers info.

	$fnames = $order_items_data['wp_travel_fname_traveller'];
	$lnames = $order_items_data['wp_travel_lname_traveller'];

	ob_start();
	if ( is_array( $items ) && count( $items ) > 0 ) { ?>
		<table>
			<?php
			foreach ( $items as $cart_item_id => $item ) {
				$trip_fnames = isset( $fnames[ $cart_item_id ] ) ? $fnames[ $cart_item_id ] : array();
				$trip_lnames = isset( $lnames[ $cart_item_id ] ) ? $lnames[ $cart_item_id ] : array();
				?>
				<tr class="wp-travel-content"> <td><b>Trip: <?php echo esc_html( get_the_title( $item['trip_id'] ) ); ?> on <?php echo esc_html( $item['trip_start_date'] ); ?></b></td> </tr>
				<?php if ( is_array( $trip_fnames ) && count( $trip_fnames ) > 0 ) : ?>
					<tr class="wp-travel-content">
						<td>
							<ol>
								<?php foreach ( $trip_fnames as $k => $fname ) : ?>
									<li> <?php printf( '%s %s', $fname, $trip_lnames[ $k ] ); ?> </li>
								<?php endforeach; ?>
							</ol>

						</td>
					</tr>
					<?php
				endif;
			}
			?>
		</table>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}

add_action( 'wp', 'wptravel_get_total_amount' );
add_action( 'wp_travel_after_booking_data_save', 'wptravel_update_payment_status_admin' );
add_action( 'wt_before_payment_process', 'wptravel_update_payment_status_booking_process_frontend' );
add_action( 'wp_travel_after_successful_payment', 'wptravel_send_email_payment' );
add_filter( 'wp_travel_booked_message', 'wptravel_payment_booking_message' );
