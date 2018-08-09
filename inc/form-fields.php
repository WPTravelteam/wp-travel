<?php
/**
 * Booking Functions.
 *
 * @package wp-travel/inc/
 */

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	global $post;

	global $wt_cart;

	if ( isset( $post->ID ) ) {

		$trip_id = $post->ID;

	}

	$cart_items = $wt_cart->getItems();

	$cart_trip = '';

	if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

		$cart_trip = array_slice( $cart_items, 0, 1 );
		$cart_trip = array_shift( $cart_trip );

	}
		
	$trip_id = isset( $cart_trip['trip_id'] ) ? $cart_trip['trip_id'] : $trip_id;
	$trip_price = isset( $cart_trip['trip_price'] ) ? $cart_trip['trip_price'] : '';
	$trip_start_date = isset( $cart_trip['trip_start_date'] ) ? $cart_trip['trip_start_date'] : '';
	$price_key = isset( $cart_trip['price_key'] ) ? $cart_trip['price_key'] : '';

	if ( $trip_id > 0 ) {
		$max_pax = get_post_meta( $trip_id, 'wp_travel_group_size', true );
	}

	$pax_size = 1;
	if ( isset( $_REQUEST['pax'] ) && ( ! $max_pax || ( $max_pax && $_REQUEST['pax'] <= $max_pax ) ) ){
		$pax_size = $_REQUEST['pax'];
	}
	$trip_duration = 1;
	if ( isset( $_REQUEST['trip_duration'] ) ) {
		$trip_duration = $_REQUEST['trip_duration'];
	}

	$price_key = isset( $_GET['price_key'] ) && '' != $_GET['price_key']  ? $_GET['price_key'] : '';

	// Set Defaults for booking form.
	$user_fname      = '';
	$user_lname      = '';
	$user_email      = '';
	// Billings.
	$billing_address = '';
	$billing_city    = '';
	$billing_company = '';
	$billing_zip     = '';
	$billing_country = '';
	$billing_phone   = '';

	// User Details Merged.
	if ( is_user_logged_in() ) {

		$user = wp_get_current_user();

		if ( in_array( 'wp-travel-customer', (array) $user->roles ) ) {

			$user_fname = isset( $user->first_name ) ? $user->first_name : '' ;
			$user_lname = isset( $user->last_name ) ? $user->last_name : '' ;
			$user_email = isset( $user->user_email ) ? $user->user_email : '' ;

			$biling_data = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );

			$billing_address = isset( $biling_data['billing_address'] ) ? $biling_data['billing_address'] : '';
			$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
			$billing_company = isset( $biling_data['billing_company'] ) ? $biling_data['billing_company'] : '';
			$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
			$billing_country = isset( $biling_data['billing_country'] ) ? $biling_data['billing_country'] : '';
			$billing_phone   = isset( $biling_data['billing_phone'] ) ? $biling_data['billing_phone'] : '';
		}
	}

	$booking_fileds = array(
		'first_name'	=> array(
			'type' => 'text',
			'label' => __( 'First Name', 'wp-travel' ),
			'name' => 'wp_travel_fname',
			'id' => 'wp-travel-fname',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'default' => $user_fname,
			'priority' => 10,
		),

		'last_name'		=> array(
			'type' => 'text',
			'label' => __( 'Last Name', 'wp-travel' ),
			'name' => 'wp_travel_lname',
			'id' => 'wp-travel-lname',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'default' => $user_lname,
			'priority' => 20,
		),
		'country'		=> array(
			'type' => 'select',
			'label' => __( 'Country', 'wp-travel' ),
			'name' => 'wp_travel_country',
			'id' => 'wp-travel-country',
			'options' => wp_travel_get_countries(),
			'validations' => array(
				'required' => true,
			),
			'default' => $billing_country,
			'priority' => 30,
		),
		'address'		=> array(
			'type' => 'text',
			'label' => __( 'Address', 'wp-travel' ),
			'name' => 'wp_travel_address',
			'id' => 'wp-travel-address',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
			),
			'default' => $billing_address,
			'priority' => 40,
		),
		'phone_number'	=> array(
			'type' => 'text',
			'label' => __( 'Phone Number', 'wp-travel' ),
			'name' => 'wp_travel_phone',
			'id' => 'wp-travel-phone',
			'validations' => array(
				'required' => true,
				'maxlength' => '50',
				'pattern' => '^[\d\+\-\.\(\)\/\s]*$',
			),
			'default' => $billing_phone,
			'priority' => 50,
		),
		'email' => array(
			'type' => 'email',
			'label' => __( 'Email', 'wp-travel' ),
			'name' => 'wp_travel_email',
			'id' => 'wp-travel-email',
			'validations' => array(
				'required' => true,
				'maxlength' => '60',
			),
			'default' => $user_email,
			'priority' => 60,
		),
		'arrival_date' => array(
			'type' => 'date',
			'label' => __( 'Arrival Date', 'wp-travel' ),
			'name' => 'wp_travel_arrival_date',
			'id' => 'wp-travel-arrival-date',
			'class' => 'wp-travel-datepicker',
			'validations' => array(
				'required' => true,
			),
			'attributes' => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority' => 70,
		),
		'departure_date' => array(
			'type' => 'date',
			'label' => __( 'Departure Date', 'wp-travel' ),
			'name' => 'wp_travel_departure_date',
			'id' => 'wp-travel-departure-date',
			'class' => 'wp-travel-datepicker',
			'validations' => array(
				'required' => true,
			),
			'attributes' => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority' => 80,
		),
		'trip_duration' => array(
			'type' => 'number',
			'label' => __( 'Trip Duration', 'wp-travel' ),
			'name' => 'wp_travel_trip_duration',
			'id' => 'wp-travel-trip-duration',
			'class' => 'wp-travel-trip-duration',
			'validations' => array(
				'required' => true,
				'min' => 1,
			),
			'default' => $trip_duration,
			'attributes' => array( 'min' => 1 ),
			'priority' => 70,
		),
		'pax' => array(
			'type' => 'number',
			'label' => __( 'Pax', 'wp-travel' ),
			'name' => 'wp_travel_pax',
			'id' => 'wp-travel-pax',
			'default' => $pax_size,
			'validations' => array(
				'required' => '',
				'min' => 1,
			),
			'attributes' => array( 'min' => 1 ),
			'priority' => 81,
		),
		'note' => array(
			'type' => 'textarea',
			'label' => __( 'Note', 'wp-travel' ),
			'name' => 'wp_travel_note',
			'id' => 'wp-travel-note',
			'placeholder' => __( 'Enter some notes...', 'wp-travel' ),
			'rows' => 6,
			'cols' => 150,
			'priority' => 90,
			'wrapper_class' => 'full-width textarea-field',
		),
		'trip_price_key' => array(
			'type' => 'hidden',
			'name' => 'price_key',
			'id' => 'wp-travel-price-key',
			'default' => $price_key,
			'priority' => 98,
		),
		'post_id' => array(
			'type' => 'hidden',
			'name' => 'wp_travel_post_id',
			'id' => 'wp-travel-post-id',
			'default' => $trip_id,
		),
	);
	if ( isset( $max_pax ) && '' != $max_pax ) {
		$booking_fileds['pax']['validations']['max'] = $max_pax;
		$booking_fileds['pax']['attributes']['max'] = $max_pax;
	}
	if ( wp_travel_is_checkout_page() ) {

		$booking_fileds['pax']['type'] = 'hidden';

		$booking_fileds['arrival_date']['default'] = date( 'm/d/Y', strtotime( $trip_start_date ) );

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

		if ( 'yes' === $fixed_departure ) {

			$booking_fileds['arrival_date']['type'] = 'hidden';
			unset( $booking_fileds['departure_date'] );

		}
	}
	return apply_filters( 'wp_travel_booking_form_fields', $booking_fileds );
}

/**
 * Return HTML of Checkout Form Fields
 *
 * @return [type] [description]
 */
function wp_travel_get_checkout_form_fields() {

	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';

	// All Fields
	$fields = wp_travel_booking_form_fields();
	$country_fields = $fields['country'];

	// Traveller traveller_fields fields. only array keys
	$traveller_fields_key = array( 'first_name', 'last_name', 'gender', 'dob', 'email', 'phone_number', 'country' );

	$traveller_fields = array();
	foreach ( $traveller_fields_key as $key ) {
		if ( isset( $fields[ $key ] ) ) {
			$traveller_fields[ $key ] = $fields[ $key ];
			if ( 'country' == $key ) {
				continue;
			}			
			unset( $fields[ $key ] );
		}
	}

	// Payment Info Fields
	
	// Standard paypal Merge.
	$payment_fields = array();
	if ( wp_travel_is_payment_enabled() ) {
		
		global $wt_cart;
		$cart_amounts = $wt_cart->get_total();

		$cart_items = $wt_cart->getItems();

		$cart_trip = '';

		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

			$cart_trip = array_slice( $cart_items, 0, 1 );
			$cart_trip = array_shift( $cart_trip );

		}

		$trip_id = isset( $cart_trip['trip_id'] ) ? $cart_trip['trip_id'] : '';
		$trip_price = isset( $cart_trip['trip_price'] ) ? $cart_trip['trip_price'] : '';
		$trip_start_date = isset( $cart_trip['trip_start_date'] ) ? $cart_trip['trip_start_date'] : '';
		$price_key = isset( $cart_trip['price_key'] ) ? $cart_trip['price_key'] : '';

		$total_amount = $cart_amounts['total'];
		$total_partial_amount = $cart_amounts['total_partial'];

		$settings = wp_travel_get_settings();
		$partial_payment = isset( $settings['partial_payment'] ) ? $settings['partial_payment'] : '';
		$payment_fields['is_partial_payment'] = array(
			'type' => 'hidden',
			'name' => 'wp_travel_is_partial_payment',
			'id' => 'wp-travel-partial-payment',
			'default' => $partial_payment,
		);
		
		$payment_fields['booking_option'] = array(
			'type' => 'select',
			'label' => __( 'Booking Options', 'wp-travel' ),
			'name' => 'wp_travel_booking_option',
			'id' => 'wp-travel-option',
			'validations' => array(
				'required' => true,
			),
			'options' => array( 'booking_with_payment' => esc_html__( 'Booking with payment', 'wp-travel' ), 'booking_only' => esc_html__( 'Booking only', 'wp-travel' ) ),
			'default' => 'booking_with_payment',
			'priority' => 100,
		);

		$gateway_list = wp_travel_get_active_gateways();
		$active_gateway_list = isset( $gateway_list['active'] ) ? $gateway_list['active'] : array();
		$selected_gateway = isset( $gateway_list['selected'] ) ? $gateway_list['selected'] : '';

		if ( is_array( $active_gateway_list ) && count( $active_gateway_list ) > 0 ) {
			$selected_gateway = apply_filters( 'wp_travel_checkout_default_gateway', $selected_gateway );

			$payment_fields['payment_gateway'] = array(
				'type' => 'radio',
				'label' => __( 'Payment Gateway', 'wp-travel' ),
				'name' => 'wp_travel_payment_gateway',
				'id' => 'wp-travel-payment-gateway',
				'wrapper_class'=>'wp-travel-radio-group wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',				
				'validations' => array(
					'required' => true,
				),
				'options' => $active_gateway_list,
				'default' => $selected_gateway,
				'priority' => 101,
			);
		}

		// $payment_amount = $actual_trip_price;
		if ( wp_travel_is_partial_payment_enabled() ) {
			// $payment_amount = $minimum_partial_payout;
			$payment_fields['payment_mode'] = array(
				'type' => 'select',
				'label' => __( 'Payment Mode', 'wp-travel' ),
				'name' => 'wp_travel_payment_mode',
				'id' => 'wp-travel-payment-mode',
				'wrapper_class'=>'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
				'validations' => array(
					'required' => true,
				),
				'options' => array( 'partial' => esc_html__( 'Partial Payment', 'wp-travel' ), 'full' => esc_html__( 'Full Payment', 'wp-travel' ) ),
				'default' => 'full',
				'priority' => 102,
			);
		}

		$payment_fields['trip_price_info'] = array(
			'type' => 'text_info',
			'label' => __( 'Total Trip Price', 'wp-travel' ),
			'name' => 'wp_travel_trip_price_info',
			'id' => 'wp-travel-trip-price_info',
			'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment',
			'before_field' => wp_travel_get_currency_symbol(),
			'default' => wp_travel_get_formated_price( $total_amount ),
			'priority' => 110,
		);
		$payment_fields['payment_amount_info'] = array(
			'type' => 'text_info',
			'label' => __( 'Payment Amount', 'wp-travel' ),
			'name' => 'wp_travel_payment_amount_info',
			'id' => 'wp-travel-payment-amount-info',
			'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment',					
			'before_field' => wp_travel_get_currency_symbol(),
			'default' => wp_travel_get_formated_price( $total_partial_amount ),
			'priority' => 115,
		);
		$payment_fields['trip_price'] = array(
			'type' => 'hidden',
			'label' => __( 'Trip Price', 'wp-travel' ),
			'name' => 'wp_travel_trip_price',
			'id' => 'wp-travel-trip-price',
			'default' => wp_travel_get_formated_price( $trip_price ),
			'priority' => 102,
		);

		// if ( $tax_rate = wp_travel_is_taxable() ) {

		// 	$payment_fields['payment_tax_percentage_info'] = array(
		// 		'type' => 'text_info',
		// 		'label' => __( 'Tax', 'wp-travel' ).$inclusive_text,
		// 		'name' => 'wp_travel_payment_tax_percentage',
		// 		'id' => 'wp-travel-payment-tax-percentage-info',
		// 		'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment',
		// 		'validations' => array(
		// 			'required' => true,
		// 		),
		// 		'before_field' => '',
		// 		'default' => $tax_rate .' %',
		// 		'priority' => 109,
		// 	);
		// }
	}

	// unset other uncecessary fields form $fields. For Billing info
	unset(
		// $fields['pax'],
		// $fields['trip_price_key'],
		$fields['wp_travel_arrival_date'],
		$fields['departure_date'],
		$fields['arrival_date'],
		$fields['trip_duration']
	);

	// Set Arrival and departure date.
	$fields['address']['priority'] = 10;

	$billing_city = '';
	$billing_zip = '';

	// User Details Merged.
	if ( is_user_logged_in() ) {

		$user = wp_get_current_user();

		if ( in_array( 'wp-travel-customer', (array) $user->roles ) ) {

			$biling_data     = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );
			$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
			$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
		}
	}


	$fields['billing_city'] = array(
		'type' => 'text',
		'label' => __( 'City', 'wp-travel' ),
		'name' => 'billing_city',
		'id' => 'wp-travel-billing-city',
		'validations' => array(
			'required' => true,
		),
		'default' => $billing_city,
		'priority' => 20,
	);

	$fields['billing_postal'] = array(
		'type' => 'text',
		'label' => __( 'Postal', 'wp-travel' ),
		'name' => 'billing_postal',
		'id' => 'wp-travel-billing-postal',
		'validations' => array(
			'required' => true,
		),
		'default' => $billing_zip,
		'priority' => 30,
	);
	$fields['country']['priority'] = 50;

	$new_fields = array(
		'traveller_fields' => wp_travel_sort_checkout_fields( $traveller_fields ),
		'billing_fields'   => wp_travel_sort_checkout_fields( $fields ),
		'payment_fields'   => wp_travel_sort_checkout_fields( $payment_fields ),
	);
	return apply_filters( 'wp_travel_checkout_fields', $new_fields );	
}
/**
 * Sort Checkout form fields.
 *
 * @return array $fields
 */
function wp_travel_sort_checkout_fields( $fields ) {
	$priority = array();
	foreach ( $fields as $key => $row ) {
		$priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
	}
	array_multisort( $priority, SORT_ASC, $fields );
	return $fields;
}
