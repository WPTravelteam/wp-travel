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

	$trip_id = 0;
	if ( isset( $_REQUEST['trip_id'] ) ) {
		$trip_id = $_REQUEST['trip_id'];
	} elseif ( isset( $_POST['wp_travel_post_id'] ) ) {
		$trip_id = $_POST['wp_travel_post_id'];
	} elseif ( isset( $post->ID ) ) {
		$trip_id = $post->ID;
	} 

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
	);
	if ( isset( $max_pax ) && '' != $max_pax ) {
		$booking_fileds['pax']['validations']['max'] = $max_pax;
		$booking_fileds['pax']['attributes']['max'] = $max_pax;
	}
	if ( wp_travel_is_checkout_page() ) {		
		$booking_arrival_date 	= get_post_meta( $trip_id, 'wp_travel_start_date', true );
		$booking_departure_date = get_post_meta( $trip_id, 'wp_travel_end_date', true );

		$booking_fileds['pax']['type'] = 'hidden';

		if ( isset( $_GET['trip_date'] ) && '' !== $_GET['trip_date'] ) {

			$booking_arrival_date = urldecode( $_GET['trip_date'] );

			$booking_fileds['arrival_date']['default'] = date('m/d/Y', strtotime( $booking_arrival_date ) );
			$booking_fileds['arrival_date']['type'] = 'hidden';

			unset ( $booking_fileds['departure_date'] );
		}
		else {

			$booking_fileds['arrival_date']['default'] = date('m/d/Y', strtotime( $booking_arrival_date ) );
			$booking_fileds['arrival_date']['type'] = 'hidden';
			
			$booking_fileds['departure_date']['default'] = date('m/d/Y', strtotime( $booking_departure_date ) );
			$booking_fileds['departure_date']['type'] = 'hidden';
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
    
    $traveller_fields['gender'] = array(
        'type' => 'select',
        'label' => __( 'Gender', 'wp-travel' ),
        'name' => 'wp_travel_gender',
        'id' => 'wp-travel-gender',
        'options' => array( 'male' => __( 'Male', 'wp-travel' ), 'female' => __( 'Female', 'wp-travel' ), 'other' => __( 'Other', 'wp-travel' ) ),
        'validations' => array(
            'required' => true,
        ),
        'priority' => 25,
    );
    $traveller_fields['date_of_birth'] = array(
        'type' => 'date',
        'label' => __( 'Date of Birth', 'wp-travel' ),
        'name' => 'wp_travel_dob',
        'id' => 'wp-travel-dob',
        'options' => array( 'male' => __( 'Male', 'wp-travel' ), 'female' => __( 'Female', 'wp-travel' ), 'other' => __( 'Other', 'wp-travel' ) ),
        'validations' => array(
            'required' => true,
        ),
        'attributes' => array( 'readonly' => 'readonly' ),
        'date_options' => array(),
        'priority' => 26,
    );

	// Payment Info Fields
    
    // Standard paypal Merge.
    $payment_fields = array();
	if ( wp_travel_is_payment_enabled() ) {        
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
				'type' => 'select',
				'label' => __( 'Payment Gateway', 'wp-travel' ),
				'name' => 'wp_travel_payment_gateway',
				'id' => 'wp-travel-payment-gateway',
				'wrapper_class'=>'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',				
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
			// 'default' => number_format( $actual_trip_price, 2, '.', '' ),
			'priority' => 110,
		);
		$payment_fields['payment_amount_info'] = array(
			'type' => 'text_info',
			'label' => __( 'Payment Amount', 'wp-travel' ).' ( '.wp_travel_get_actual_payout_percent($trip_id). ' %) ',
			'name' => 'wp_travel_payment_amount_info',
			'id' => 'wp-travel-payment-amount-info',
			'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment',
			'validations' => array(
				'required' => true,
			),
			'attributes' => array(
				// 'min' => $minimum_partial_payout,
				// 'max' => $actual_trip_price,
			),
			'before_field' => wp_travel_get_currency_symbol(),
			// 'default' => number_format( $payment_amount, 2, '.', '' ),
			'priority' => 115,
		);

		if ( $tax_rate = wp_travel_is_taxable() ) {

			$payment_fields['payment_tax_percentage_info'] = array(
				'type' => 'text_info',
				'label' => __( 'Tax', 'wp-travel' ).$inclusive_text,
				'name' => 'wp_travel_payment_tax_percentage',
				'id' => 'wp-travel-payment-tax-percentage-info',
				'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment',
				'validations' => array(
					'required' => true,
				),
				'before_field' => '',
				'default' => $tax_rate .' %',
				'priority' => 109,
			);
		}
	}
    
    // unset other uncecessary fields form $fields. For Billing info
    unset(
        $fields['pax'],
        $fields['wp_travel_arrival_date'],
        $fields['departure_date'],
        $fields['trip_price_key'],
        $fields['arrival_date'],
        $fields['trip_duration']        
    );
    $fields['address']['priority'] = 10;
    $fields['billing_city'] = array(
        'type' => 'text',
        'label' => __( 'City', 'wp-travel' ),
        'name' => 'billing_city',
        'id' => 'wp-travel-billing-city',
        'validations' => array(
            'required' => true,
        ),
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
        'priority' => 30,
    );
    $fields['billing_province'] = array(
        'type' => 'text',
        'label' => __( 'Province', 'wp-travel' ),
        'name' => 'billing_province',
        'id' => 'wp-travel-billing-province',
        'validations' => array(
            'required' => true,
        ),
        'priority' => 40,
    );
    $fields['country']['priority'] = 50;

	$new_fields = array(
		'traveller_fields' 	=> wp_travel_sort_checkout_fields( $traveller_fields ),
		'billing_fields' 	=> wp_travel_sort_checkout_fields( $fields ),
		'payment_fields'	=> wp_travel_sort_checkout_fields( $payment_fields ),
	);
	return apply_filters( 'wp_travel_checkout_fields', $new_fields );	
}

function wp_travel_sort_checkout_fields( $fields ) {
    $priority = array();
    foreach ( $fields as $key => $row ) {
        $priority[ $key ] = isset( $row['priority'] ) ? $row['priority'] : 1;
    }
    array_multisort( $priority, SORT_ASC, $fields );
    return $fields;
}
