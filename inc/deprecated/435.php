<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/deprecated
 */

/**
 * Check sale price enable or not.
 *
 * @param Number $post_id Current post id.
 * @param String $price_key Price Key for multiple pricing.
 * @since WP Travel 1.0.5 Modified in 2.0.1, 2.0.5, 2.0.7 and Deprecated in WP Travel 4.3.5
 */
function wp_travel_is_enable_sale( $trip_id, $price_key = null ) {
	wp_travel_deprecated_function( 'wp_travel_is_enable_sale', '4.3.5', 'WP_Travel_Helpers_Trips::is_sale_enabled()' );
	$args = array(
		'trip_id' => $trip_id
	);
	return WP_Travel_Helpers_Trips::is_sale_enabled( $args );
}

/**
 * Check sale price enable or not.
 *
 * @param Number $trip_id Trip Id.
 * @param String $from_price_sale_enable Check sale price enable in from price.
 * @param String $pricing_id Pricing Id of trip.
 * @param String $category_id Category Id of trip.
 * @param String $price_key Price Key of trip.
 * @since WP Travel 3.0.0 and Deprecated in WP Travel 4.3.5
 */
function wp_travel_is_enable_sale_price( $trip_id, $from_price_sale_enable = false, $pricing_id = '', $category_id = '', $price_key = '' ) {
	wp_travel_deprecated_function( 'wp_travel_is_enable_sale_price', '4.3.5', 'WP_Travel_Helpers_Trips::is_sale_enabled()' );
	$args = array(
		'trip_id' => $trip_id,
		'from_price_sale_enable' => $from_price_sale_enable,
		'pricing_id' => $pricing_id,
		'category_id' => $category_id,
		'price_key' => $price_key,
	);
	return WP_Travel_Helpers_Trips::is_sale_enabled( $args );
}

/**
 * Return True if Tax is enabled in settings.
 *
 * @since Deprecated in WP Travel 4.3.5
 */
function wp_travel_is_trip_price_tax_enabled( $trip_id, $from_price_sale_enable = false, $pricing_id = '', $category_id = '', $price_key = '' ) {
	wp_travel_deprecated_function( 'wp_travel_is_trip_price_tax_enabled', '4.3.5', 'WP_Travel_Helpers_Trips::is_tax_enabled()' );
	
	return WP_Travel_Helpers_Trips::is_tax_enabled();
}

/**
 * Return True Percent if tax is applicable otherwise return false.
 *
 * @since WP Travel 1.9.1 and Deprecated in WP Travel 4.3.5
 * @return Mixed
 */
function wp_travel_is_taxable() {
	wp_travel_deprecated_function( 'wp_travel_is_taxable', '4.3.5', 'WP_Travel_Helpers_Trips::get_tax_rate()' );
	return WP_Travel_Helpers_Trips::get_tax_rate();
}

/**
 * Return HTML Booking Form
 *
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.3.5
 * @return HTML [description]
 */
function wp_travel_get_booking_form() {
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_get_booking_form', '4.3.5' );
	global $post;
	$trip_id = 0;
	$settings = wp_travel_get_settings();
	if ( isset( $_REQUEST['trip_id'] ) ) {
		$trip_id = $_REQUEST['trip_id'];
	} elseif ( isset( $_POST['wp_travel_post_id'] ) ) {
		$trip_id = $_POST['wp_travel_post_id'];
	} elseif ( isset( $post->ID ) ) {
		$trip_id = $post->ID;
	}
	include_once WP_TRAVEL_ABSPATH . 'inc/framework/form/class.form.php';
	$form_options = array(
		'id'            => 'wp-travel-booking',
		'wrapper_class' => 'wp-travel-booking-form-wrapper',
		'submit_button' => array(
			'name'  => 'wp_travel_book_now',
			'id'    => 'wp-travel-book-now',
			'value' => __( 'Book Now', 'wp-travel' ),
		),
		'nonce'         => array(
			'action' => 'wp_travel_security_action',
			'field'  => 'wp_travel_security',
		),
	);
	$fields = wp_travel_booking_form_fields();
	// GDPR Support

	$gdpr_msg = isset( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );
	$policy_link = wp_travel_privacy_link();
	if ( ! empty( $gdpr_msg ) && $policy_link ) {
		// GDPR Compatibility for enquiry.
		$fields['wp_travel_booking_gdpr'] = array(
			'type'              => 'checkbox',
			'label'             => __( 'Privacy Policy', 'wp-travel' ),
			'options'           => array( 'gdpr_agree' => sprintf( '%1s %2s', $gdpr_msg, $policy_link ) ),
			'name'              => 'wp_travel_booking_gdpr_msg',
			'id'                => 'wp-travel-enquiry-gdpr-msg',
			'validations'       => array(
				'required' => true,
			),
			'option_attributes' => array(
				'required' => true,
			),
			'priority'          => 100,
			'wrapper_class'     => 'full-width',
		);
	}

	$form              = new WP_Travel_FW_Form();
	$fields['post_id'] = array(
		'type'    => 'hidden',
		'name'    => 'wp_travel_post_id',
		'id'      => 'wp-travel-post-id',
		'default' => $trip_id,
	);
	$fixed_departure   = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
	$fixed_departure   = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure   = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
	$trip_start_date   = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date     = get_post_meta( $trip_id, 'wp_travel_end_date', true );

	if ( 'yes' === $fixed_departure ) {
		$fields['arrival_date']['class']     = '';
		$fields['arrival_date']['default']   = date( 'Y-m-d', strtotime( $trip_start_date ) );
		$fields['arrival_date']['type']      = 'hidden';
		$fields['departure_date']['class']   = '';
		$fields['departure_date']['default'] = date( 'Y-m-d', strtotime( $trip_end_date ) );
		$fields['departure_date']['type']    = 'hidden';
		unset( $fields['trip_duration'] );
	}

	$trip_duration = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );

	$fields['trip_duration']['default'] = $trip_duration;
	$fields['trip_duration']['type']    = 'hidden';

	$group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );

	if ( isset( $group_size ) && '' != $group_size ) {
		$fields['pax']['validations']['max'] = $group_size;
	}
	$trip_price = wp_travel_get_actual_trip_price( $trip_id );
	if ( '' == $trip_price || '0' == $trip_price ) {
		unset( $fields['is_partial_payment'], $fields['payment_gateway'], $fields['booking_option'], $fields['trip_price'], $fields['payment_mode'], $fields['payment_amount'], $fields['trip_price_info'], $fields['payment_amount_info'] );
	}
	return $form->init( $form_options )->fields( $fields )->template();
}

/**
 * Array List of form field to generate booking fields.
 *
 * @since WP Travel 1.0.0 and Deprecated in WP Travel 4.3.5
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_booking_form_fields', '4.3.5' );
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
