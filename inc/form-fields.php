<?php
/**
 * Booking Functions.
 * // TODO: Remove This function.
 *
 * @package wp-travel/inc/
 */

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	$trip_id = 0;
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

	$trip_id         = isset( $cart_trip['trip_id'] ) ? $cart_trip['trip_id'] : $trip_id;
	$trip_price      = isset( $cart_trip['trip_price'] ) ? $cart_trip['trip_price'] : '';
	$trip_start_date = isset( $cart_trip['trip_start_date'] ) ? $cart_trip['trip_start_date'] : '';
	$price_key       = isset( $cart_trip['price_key'] ) ? $cart_trip['price_key'] : '';

	if ( $trip_id > 0 ) {
		$max_pax = get_post_meta( $trip_id, 'wp_travel_group_size', true );
	}

	$pax_size = 1;
	if ( isset( $_REQUEST['pax'] ) && ( ! $max_pax || ( $max_pax && $_REQUEST['pax'] <= $max_pax ) ) ) {
		if( is_array( $_REQUEST['pax'] ) ) {
			$pax_size = array_sum( $_REQUEST['pax'] );
		}
	}
	$trip_duration = 1;
	if ( isset( $_REQUEST['trip_duration'] ) ) {
		$trip_duration = $_REQUEST['trip_duration'];
	}

	$price_key = isset( $_GET['price_key'] ) && '' != $_GET['price_key'] ? $_GET['price_key'] : '';

	// Set Defaults for booking form.
	$user_fname = '';
	$user_lname = '';
	$user_email = '';
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

			$user_fname = isset( $user->first_name ) ? $user->first_name : '';
			$user_lname = isset( $user->last_name ) ? $user->last_name : '';
			$user_email = isset( $user->user_email ) ? $user->user_email : '';

			$biling_data = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );

			$billing_address = isset( $biling_data['billing_address'] ) ? $biling_data['billing_address'] : '';
			$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
			$billing_company = isset( $biling_data['billing_company'] ) ? $biling_data['billing_company'] : '';
			$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
			$billing_country = isset( $biling_data['billing_country'] ) ? $biling_data['billing_country'] : '';
			$billing_phone   = isset( $biling_data['billing_phone'] ) ? $biling_data['billing_phone'] : '';
		}
	}

	$booking_fields = array(
		'first_name'     => array(
			'type'        => 'text',
			'label'       => __( 'First Name', 'wp-travel' ),
			'name'        => 'wp_travel_fname',
			'id'          => 'wp-travel-fname',
			'validations' => array(
				'required'  => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'default'     => $user_fname,
			'priority'    => 10,
		),

		'last_name'      => array(
			'type'        => 'text',
			'label'       => __( 'Last Name', 'wp-travel' ),
			'name'        => 'wp_travel_lname',
			'id'          => 'wp-travel-lname',
			'validations' => array(
				'required'  => true,
				'maxlength' => '50',
				// 'type' => 'alphanum',
			),
			'default'     => $user_lname,
			'priority'    => 20,
		),
		'country'        => array(
			'type'        => 'country_dropdown',
			'label'       => __( 'Country', 'wp-travel' ),
			'name'        => 'wp_travel_country',
			'id'          => 'wp-travel-country',
			// 'options' => wp_travel_get_countries(),
			'validations' => array(
				'required' => true,
			),
			'default'     => $billing_country,
			'priority'    => 30,
		),
		'address'        => array(
			'type'        => 'text',
			'label'       => __( 'Address', 'wp-travel' ),
			'name'        => 'wp_travel_address',
			'id'          => 'wp-travel-address',
			'validations' => array(
				'required'  => true,
				'maxlength' => '50',
			),
			'default'     => $billing_address,
			'priority'    => 40,
		),
		'phone_number'   => array(
			'type'        => 'text',
			'label'       => __( 'Phone Number', 'wp-travel' ),
			'name'        => 'wp_travel_phone',
			'id'          => 'wp-travel-phone',
			'validations' => array(
				'required'  => true,
				'maxlength' => '50',
				'pattern'   => '^[\d\+\-\.\(\)\/\s]*$',
			),
			'default'     => $billing_phone,
			'priority'    => 50,
		),
		'email'          => array(
			'type'        => 'email',
			'label'       => __( 'Email', 'wp-travel' ),
			'name'        => 'wp_travel_email',
			'id'          => 'wp-travel-email',
			'validations' => array(
				'required'  => true,
				'maxlength' => '60',
			),
			'default'     => $user_email,
			'priority'    => 60,
		),
		'arrival_date'   => array(
			'type'         => 'date',
			'label'        => __( 'Arrival Date', 'wp-travel' ),
			'name'         => 'wp_travel_arrival_date',
			'id'           => 'wp-travel-arrival-date',
			'class'        => 'wp-travel-datepicker',
			'validations'  => array(
				'required' => true,
			),
			'attributes'   => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority'     => 70,
		),
		'departure_date' => array(
			'type'         => 'date',
			'label'        => __( 'Departure Date', 'wp-travel' ),
			'name'         => 'wp_travel_departure_date',
			'id'           => 'wp-travel-departure-date',
			'class'        => 'wp-travel-datepicker',
			'validations'  => array(
				'required' => true,
			),
			'attributes'   => array( 'readonly' => 'readonly' ),
			'date_options' => array(),
			'priority'     => 80,
		),
		'trip_duration'  => array(
			'type'        => 'number',
			'label'       => __( 'Trip Duration', 'wp-travel' ),
			'name'        => 'wp_travel_trip_duration',
			'id'          => 'wp-travel-trip-duration',
			'class'       => 'wp-travel-trip-duration',
			'validations' => array(
				'required' => true,
				'min'      => 1,
			),
			'default'     => $trip_duration,
			'attributes'  => array( 'min' => 1 ),
			'priority'    => 70,
		),
		'pax'            => array(
			'type'        => 'number',
			'label'       => __( 'Pax', 'wp-travel' ),
			'name'        => 'wp_travel_pax',
			'id'          => 'wp-travel-pax',
			'default'     => $pax_size,
			'validations' => array(
				'required' => '',
				'min'      => 1,
			),
			'attributes'  => array( 'min' => 1 ),
			'priority'    => 81,
		),
		'note'           => array(
			'type'          => 'textarea',
			'label'         => __( 'Note', 'wp-travel' ),
			'name'          => 'wp_travel_note',
			'id'            => 'wp-travel-note',
			'placeholder'   => __( 'Enter some notes...', 'wp-travel' ),
			'rows'          => 6,
			'cols'          => 150,
			'priority'      => 90,
			'wrapper_class' => 'full-width textarea-field',
		),
		'trip_price_key' => array(
			'type'     => 'hidden',
			'name'     => 'price_key',
			'id'       => 'wp-travel-price-key',
			'default'  => $price_key,
			'priority' => 98,
		),
		'post_id'        => array(
			'type'    => 'hidden',
			'name'    => 'wp_travel_post_id',
			'id'      => 'wp-travel-post-id',
			'default' => $trip_id,
		),
	);
	if ( isset( $max_pax ) && '' != $max_pax ) {
		$booking_fields['pax']['validations']['max'] = $max_pax;
		$booking_fields['pax']['attributes']['max']  = $max_pax;
	}
	if ( wp_travel_is_checkout_page() ) {

		$booking_fields['pax']['type'] = 'hidden';

		$booking_fields['arrival_date']['default'] = date( 'm/d/Y', strtotime( $trip_start_date ) );

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

		if ( 'yes' === $fixed_departure ) {

			$booking_fields['arrival_date']['type'] = 'hidden';
			unset( $booking_fields['departure_date'] );

		}
	}
	return apply_filters( 'wp_travel_booking_form_fields', $booking_fields );
}

/**
 * Return HTML of Checkout Form Fields
 *
 * @return [type] [description]
 */
function wp_travel_get_checkout_form_fields() {

	$user_fname      = '';
	$user_lname      = '';
	$user_email      = '';
	$billing_city    = '';
	$billing_zip     = '';
	$billing_address = '';
	$billing_country = '';
	$billing_phone   = '';

	// User Details Merged.
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( in_array( 'wp-travel-customer', (array) $user->roles ) ) {
			$user_fname = isset( $user->first_name ) ? $user->first_name : '';
			$user_lname = isset( $user->last_name ) ? $user->last_name : '';
			$user_email = isset( $user->user_email ) ? $user->user_email : '';

			$biling_data     = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );
			$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
			$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
			$billing_address = isset( $biling_data['billing_address'] ) ? $biling_data['billing_address'] : '';
			$billing_country = isset( $biling_data['billing_country'] ) ? $biling_data['billing_country'] : '';
			$billing_phone   = isset( $biling_data['billing_phone'] ) ? $biling_data['billing_phone'] : '';
		}
	}

	$traveller_fields = WP_Travel_Default_Form_Fields::traveller();
	$traveller_fields = apply_filters( 'wp_travel_checkout_traveller_fields', $traveller_fields );
	// Set default values.
	$traveller_fields['first_name']['default']   = $user_fname;
	$traveller_fields['last_name']['default']    = $user_fname;
	$traveller_fields['country']['default']      = $billing_country;
	$traveller_fields['phone_number']['default'] = $billing_phone;
	$traveller_fields['email']['default']        = $user_email;

	// Billing fields.
	$billing_fields = WP_Travel_Default_Form_Fields::billing();
	$billing_fields = apply_filters( 'wp_travel_checkout_billing_fields', $billing_fields );
	// Get billing hidden fields.
	$billing_hidden_fields = WP_Travel_Default_Form_Fields::_billing();
	$fields                = wp_parse_args( $billing_fields, $billing_hidden_fields );

	// Set defaults.
	$fields['billing_city']['default']   = $billing_city;
	$fields['country']['default']        = $billing_country;
	$fields['billing_postal']['default'] = $billing_zip;
	$fields['address']['default']        = $billing_address;

	// Payment Info Fields
	// Standard paypal Merge.
	$payment_fields = array();
	$settings       = wp_travel_get_settings();

	// GDPR Support
	$gdpr_msg = ! empty( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );

	$policy_link = wp_travel_privacy_link();
	if ( ! empty( $gdpr_msg ) && $policy_link ) {

		// GDPR Compatibility for enquiry.
		$payment_fields['wp_travel_checkout_gdpr'] = array(
			'type'              => 'checkbox',
			'label'             => __( 'Privacy Policy', 'wp-travel' ),
			'options'           => array( 'gdpr_agree' => sprintf( '%1s %2s', $gdpr_msg, $policy_link ) ),
			'name'              => 'wp_travel_checkout_gdpr_msg',
			'id'                => 'wp-travel-enquiry-gdpr-msg',
			'validations'       => array(
				'required' => true,
			),
			'option_attributes' => array(
				'required' => true,
			),
			'priority'          => 120,
		);
	}

	if ( wp_travel_is_payment_enabled() ) {
		$payment_fields['wp_travel_billing_address_heading'] = array(
			'type'        => 'heading',
			'label'       => __( 'Booking / Payments', 'wp-travel' ),
			'name'        => 'wp_travel_payment_heading',
			'id'          => 'wp-travel-payment-heading',
			'class'       => 'panel-title',
			'heading_tag' => 'h4',
			'priority'    => 1,
		);
		global $wt_cart;
		$cart_amounts = $wt_cart->get_total();

		$cart_items = $wt_cart->getItems();

		$cart_trip = '';

		if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {

			$cart_trip = array_slice( $cart_items, 0, 1 );
			$cart_trip = array_shift( $cart_trip );

		}

		$trip_id         = isset( $cart_trip['trip_id'] ) ? $cart_trip['trip_id'] : '';
		$trip_price      = isset( $cart_trip['trip_price'] ) ? $cart_trip['trip_price'] : '';
		$trip_start_date = isset( $cart_trip['trip_start_date'] ) ? $cart_trip['trip_start_date'] : '';
		$price_key       = isset( $cart_trip['price_key'] ) ? $cart_trip['price_key'] : '';

		$total_amount         = $cart_amounts['total'];
		$total_partial_amount = $cart_amounts['total_partial'];
		$partial_payment      = isset( $settings['partial_payment'] ) ? $settings['partial_payment'] : '';

		$payment_fields['is_partial_payment'] = array(
			'type'    => 'hidden',
			'name'    => 'wp_travel_is_partial_payment',
			'id'      => 'wp-travel-partial-payment',
			'default' => $partial_payment,
		);

		$payment_fields['booking_option'] = array(
			'type'        => 'select',
			'label'       => __( 'Booking Options', 'wp-travel' ),
			'name'        => 'wp_travel_booking_option',
			'id'          => 'wp-travel-option',
			'validations' => array(
				'required' => true,
			),
			'options'     => array(
				'booking_with_payment' => esc_html__( 'Booking with payment', 'wp-travel' ),
				'booking_only'         => esc_html__( 'Booking only', 'wp-travel' ),
			),
			'default'     => 'booking_with_payment',
			'priority'    => 100,
		);

		$gateway_list        = wp_travel_get_active_gateways();
		$active_gateway_list = isset( $gateway_list['active'] ) ? $gateway_list['active'] : array();
		$selected_gateway    = isset( $gateway_list['selected'] ) ? $gateway_list['selected'] : '';

		if ( is_array( $active_gateway_list ) && count( $active_gateway_list ) > 0 ) {
			$selected_gateway = apply_filters( 'wp_travel_checkout_default_gateway', $selected_gateway );

			$payment_fields['payment_gateway'] = array(
				'type'          => 'radio',
				'label'         => __( 'Payment Gateway', 'wp-travel' ),
				'name'          => 'wp_travel_payment_gateway',
				'id'            => 'wp-travel-payment-gateway',
				'wrapper_class' => 'wp-travel-radio-group wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
				'validations'   => array(
					'required' => true,
				),
				'options'       => $active_gateway_list,
				'default'       => $selected_gateway,
				'priority'      => 101,
			);
		}

		if ( wp_travel_is_partial_payment_enabled() ) {
			$payment_fields['payment_mode']        = array(
				'type'          => 'select',
				'label'         => __( 'Payment Mode', 'wp-travel' ),
				'name'          => 'wp_travel_payment_mode',
				'id'            => 'wp-travel-payment-mode',
				'wrapper_class' => 'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
				'validations'   => array(
					'required' => true,
				),
				'options'       => wp_travel_get_payment_modes(),
				'default'       => 'full',
				'priority'      => 102,
			);
			$payment_fields['payment_amount_info'] = array(
				'type'          => 'text_info',
				'label'         => __( 'Payment Amount', 'wp-travel' ),
				'name'          => 'wp_travel_payment_amount_info',
				'id'            => 'wp-travel-payment-amount-info',
				'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment',
				// 'before_field'  => wp_travel_get_currency_symbol(),
				'default'       => wp_travel_get_formated_price_currency( $total_partial_amount ),
				'priority'      => 115,
			);
		}

		$method = array_keys( $active_gateway_list );
		if ( in_array( 'bank_deposit', $method ) ) {
			$payment_fields['bank_deposite_info'] = array(
				'type'          => 'text_info',
				'label'         => __( 'Bank Detail', 'wp-travel' ),
				'name'          => 'wp_travel_payment_bank_detail',
				'id'            => 'wp-travel-payment-bank-detail',
				'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment f-bank-deposit',
				'default'       => wp_travel_get_bank_deposit_account_table(),
				'priority'      => 117,
			);
		}

		$payment_fields['trip_price_info'] = array(
			'type'          => 'text_info',
			'label'         => __( 'Total Trip Price', 'wp-travel' ),
			'name'          => 'wp_travel_trip_price_info',
			'id'            => 'wp-travel-trip-price_info',
			'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment',
			// 'before_field'  => wp_travel_get_currency_symbol(),
			'default'       => wp_travel_get_formated_price_currency( $total_amount ),
			'priority'      => 110,
		);

		$payment_fields['trip_price'] = array(
			'type'     => 'hidden',
			'label'    => __( 'Trip Price', 'wp-travel' ),
			'name'     => 'wp_travel_trip_price',
			'id'       => 'wp-travel-trip-price',
			'default'  => wp_travel_get_formated_price( $trip_price ),
			'priority' => 102,
		);
	}

	$checkout_fields = array(
		'traveller_fields' => $traveller_fields,
		'billing_fields'   => $fields,
		'payment_fields'   => $payment_fields,
	);
	$checkout_fields = apply_filters( 'wp_travel_checkout_fields', $checkout_fields ); /// sort field after this filter.

	$checkout_fields = array(
		'traveller_fields' => wp_travel_sort_form_fields( $checkout_fields['traveller_fields'] ),
		'billing_fields'   => wp_travel_sort_form_fields( $checkout_fields['billing_fields'] ),
		'payment_fields'   => wp_travel_sort_form_fields( $checkout_fields['payment_fields'] ),
	);

	if ( isset( $checkout_fields['traveller_fields'] ) ) {
		$checkout_fields['traveller_fields'] = wp_travel_sort_form_fields( $checkout_fields['traveller_fields'] );
	}
	if ( isset( $checkout_fields['billing_fields'] ) ) {
		$checkout_fields['billing_fields'] = wp_travel_sort_form_fields( $checkout_fields['billing_fields'] );
	}
	if ( isset( $checkout_fields['payment_fields'] ) ) {
		$checkout_fields['payment_fields'] = wp_travel_sort_form_fields( $checkout_fields['payment_fields'] );
	}
	return $checkout_fields;
}

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wp_travel_search_filter_widget_form_fields() {

	$keyword  = ( isset( $_GET['keyword'] ) && '' !== $_GET['keyword'] ) ? $_GET['keyword'] : '';
	$fact     = ( isset( $_GET['fact'] ) && '' !== $_GET['fact'] ) ? $_GET['fact'] : '';
	$type     = ( isset( $_GET['itinerary_types'] ) && '' !== $_GET['itinerary_types'] ) ? $_GET['itinerary_types'] : '';
	$location = ( isset( $_GET['travel_locations'] ) && '' !== $_GET['travel_locations'] ) ? $_GET['travel_locations'] : '';
	$price    = ( isset( $_GET['price'] ) ) ? $_GET['price'] : '';

	$min_price   = ( isset( $_GET['min_price'] ) && '' !== $_GET['min_price'] ) ? (int) $_GET['min_price'] : 0;
	$max_price   = ( isset( $_GET['max_price'] ) && '' !== $_GET['max_price'] ) ? (int) $_GET['max_price'] : 0;
	$price_range = array(
		array(
			'name'  => 'min_price',
			'value' => $min_price,
			'class' => 'wp-travel-filter-price-min', // Extra class.
		),
		array(
			'name'  => 'max_price',
			'value' => $max_price,
			'class' => 'wp-travel-filter-price-max', // Extra class.
		),
	);

	$trip_start = (int) ( isset( $_GET['trip_start'] ) && '' !== $_GET['trip_start'] ) ? $_GET['trip_start'] : '';
	$trip_end   = (int) ( isset( $_GET['trip_end'] ) && '' !== $_GET['trip_end'] ) ? $_GET['trip_end'] : '';

	$show_end_date = wp_travel_booking_show_end_date();
	$trip_duration = array(
		array(
			'name'  => 'trip_start',
			'label' => __( 'From', 'wp-travel' ),
			'value' => $trip_start,
			'id'    => 'datepicker1', // Extra id.

		),
	);
	if ( $show_end_date ) {
		$trip_duration[] = array(
			'name'  => 'trip_end',
			'label' => __( 'To', 'wp-travel' ),
			'value' => $trip_end,
			'id'    => 'datepicker2', // Extra id.
		);
	}

	// Note. Main key of $fields array is used as customizer to show field.
	$fields = array(
		'keyword_search'       => array(
			'type'        => 'text',
			'label'       => __( 'Keyword:', 'wp-travel' ),
			'name'        => 'keyword',
			'id'          => 'wp-travel-filter-keyword',
			'class'       => 'wp_travel_search_widget_filters_input',
			'validations' => array(
				'required'  => false,
				'maxlength' => '100',
			),
			'default'     => $keyword,
			'priority'    => 10,
		),
		'fact'                 => array(
			'type'        => 'text',
			'label'       => __( 'Fact:', 'wp-travel' ),
			'name'        => 'fact',
			'id'          => 'wp-travel-filter-fact',
			'class'       => 'wp_travel_search_widget_filters_input',
			'validations' => array(
				'required'  => false,
				'maxlength' => '100',
			),
			'default'     => $fact,
			'priority'    => 20,
		),
		// Key 'trip_type_filter' is used in customizer to show /hide fields.
		'trip_type_filter'     => array(
			'type'            => 'category_dropdown',
			'taxonomy'        => 'itinerary_types', // only for category_dropdown
			'show_option_all' => __( 'All', 'wp-travel' ),  // only for category_dropdown
			'label'           => __( 'Trip Type:', 'wp-travel' ),
			'name'            => 'itinerary_types',
			'id'              => 'itinerary_types',
			'class'           => 'wp_travel_search_widget_filters_input',

			'validations'     => array(
				'required' => false,
			),
			'default'         => $type,
			'priority'        => 30,
		),
		// Key 'trip_location_filter' is used in customizer to show /hide fields.
		'trip_location_filter' => array(
			'type'            => 'category_dropdown',
			'taxonomy'        => 'travel_locations', // only for category_dropdown.
			'show_option_all' => __( 'All', 'wp-travel' ),  // only for category_dropdown.
			'label'           => __( 'Location:', 'wp-travel' ),
			'name'            => 'travel_locations',
			'id'              => 'travel_locations',
			'class'           => 'wp_travel_search_widget_filters_input',

			'validations'     => array(
				'required' => false,
			),
			'default'         => $location,
			'priority'        => 40,
		),
		'price_orderby'        => array(
			'type'        => 'select',
			'label'       => __( 'Price:', 'wp-travel' ),
			'name'        => 'price',
			'id'          => 'wp-travel-price',
			'class'       => 'wp_travel_search_widget_filters_input',
			'validations' => array(
				'required' => false,
			),
			'options'     => array(
				'low_high' => esc_html__( 'Price low to high', 'wp-travel' ),
				'high_low' => esc_html__( 'Price high to low', 'wp-travel' ),
			),
			'attributes'  => array( 'placeholder' => __( '--', 'wp-travel' ) ),
			'default'     => $price,
			'priority'    => 50,
		),
		'price_range'          => array(
			'type'          => 'range',
			'label'         => __( 'Price Range:', 'wp-travel' ),
			// 'name'        => 'price',
			'id'            => 'amount',
			'class'         => 'wp_travel_search_widget_filters_input',
			'default'       => $price_range,
			'priority'      => 60,
			'wrapper_class' => 'wp-trave-price-range',
		),
		'trip_dates'           => array(
			'type'          => 'date_range',
			'label'         => __( 'Trip Duration:', 'wp-travel' ),
			'class'         => 'wp_travel_search_widget_filters_input',
			'validations'   => array(
				'required' => false,
			),
			'default'       => $trip_duration,
			'priority'      => 70,
			'wrapper_class' => 'wp-travel-trip-duration',
		),
	);
	$fields = apply_filters( 'wp_travel_search_filter_widget_form_fields', $fields );

	return wp_travel_sort_form_fields( $fields );
}

/**
 * Bank Deposit form fields.
 *
 * @since 2.0.0
 */
function wp_travel_get_bank_deposit_form_fields() {
	$fields = array();
	if ( wp_travel_is_partial_payment_enabled() ) {

		$fields['payment_mode'] = array(
			'type'          => 'select',
			'label'         => __( 'Payment Mode', 'wp-travel' ),
			'name'          => 'wp_travel_payment_mode',
			'id'            => 'wp-travel-payment-mode',
			'wrapper_class' => 'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
			'validations'   => array(
				'required' => true,
			),
			'options'       => wp_travel_get_payment_modes(),
			'default'       => 'full',
			'priority'      => 10,
		);

	}
	$fields['wp_travel_bank_deposit_slip'] = array(
		'type'        => 'file',
		'label'       => __( 'Bank Deposit Slip', 'wp-travel' ),
		'name'        => 'wp_travel_bank_deposit_slip',
		'id'          => 'wp-travel-deposit-slip',
		'class'       => 'wp-travel-deposit-slip',
		'validations' => array(
			'required' => true,
		),
		'default'     => '',
		'priority'    => 20,
	);
	$fields['wp_travel_bank_deposit_transaction_id'] = array(
		'type'        => 'text',
		'label'       => __( 'Transaction ID (from receipt)	', 'wp-travel' ),
		'name'        => 'wp_travel_bank_deposit_transaction_id',
		'id'          => 'wp-travel-deposit-transaction-id',
		'class'       => 'wp-travel-deposit-transaction-id',
		'validations' => array(
			'required' => true,
		),
		'default'     => '',
		'priority'    => 30,
	);

	$fields = apply_filters( 'wp_travel_bank_deposit_fields', $fields );
	return wp_travel_sort_form_fields( $fields );
}
