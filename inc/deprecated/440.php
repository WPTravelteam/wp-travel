<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/**
 * Check sale price enable or not. Modified in 2.0.1, 2.0.5, 2.0.7.
 *
 * @param Number $trip_id Current post id.
 * @param String $price_key Price Key for multiple pricing.
 * @since 1.0.5
 * @deprecated 4.4.0
 */
function wp_travel_is_enable_sale( $trip_id, $price_key = null ) {
	wp_travel_deprecated_function( 'wp_travel_is_enable_sale', '4.4.0', 'WP_Travel_Helpers_Trips::is_sale_enabled()' );
	$args = array(
		'trip_id' => $trip_id,
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
 * @since 3.0.0
 * @deprecated 4.4.0
 */
function wp_travel_is_enable_sale_price( $trip_id, $from_price_sale_enable = false, $pricing_id = '', $category_id = '', $price_key = '' ) {
	wp_travel_deprecated_function( 'wp_travel_is_enable_sale_price', '4.4.0', 'WP_Travel_Helpers_Trips::is_sale_enabled()' );
	$args = array(
		'trip_id'                => $trip_id,
		'from_price_sale_enable' => $from_price_sale_enable,
		'pricing_id'             => $pricing_id,
		'category_id'            => $category_id,
		'price_key'              => $price_key,
	);
	return WP_Travel_Helpers_Trips::is_sale_enabled( $args );
}

/**
 * Return True if Tax is enabled in settings.
 *
 * @deprecated 4.4.0
 */
function wp_travel_is_trip_price_tax_enabled( $trip_id, $from_price_sale_enable = false, $pricing_id = '', $category_id = '', $price_key = '' ) {
	wp_travel_deprecated_function( 'wp_travel_is_trip_price_tax_enabled', '4.4.0', 'WP_Travel_Helpers_Trips::is_tax_enabled()' );

	return WP_Travel_Helpers_Trips::is_tax_enabled();
}

/**
 * Return True Percent if tax is applicable otherwise return false.
 *
 * @since 1.9.1
 * @deprecated 4.4.0
 * @return Mixed
 */
function wp_travel_is_taxable() {
	wp_travel_deprecated_function( 'wp_travel_is_taxable', '4.4.0', 'WP_Travel_Helpers_Trips::get_tax_rate()' );
	return WP_Travel_Helpers_Trips::get_tax_rate();
}

/**
 * Return HTML Booking Form
 *
 * @since 1.0.0
 * @deprecated 4.4.0
 * @return HTML [description]
 */
function wp_travel_get_booking_form() {
	if ( ! isset( $_POST['wp_travel_security'] ) ) {
		return;
	}
	if ( ! isset( $_POST['wp_travel_security'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wp_travel_security'] ) ), 'wp_travel_security_action' ) ) {
		return;
	}
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_get_booking_form', '4.4.0' );
	global $post;
	$trip_id  = 0;
	$settings = wptravel_get_settings();

	$trip_id = $post->ID;
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
	$fields       = wp_travel_booking_form_fields();
	// GDPR Support.

	$gdpr_msg    = isset( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );
	$policy_link = wptravel_enquiries_form_fields();
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
	$fixed_departure   = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );
	$trip_start_date   = get_post_meta( $trip_id, 'wp_travel_start_date', true );
	$trip_end_date     = get_post_meta( $trip_id, 'wp_travel_end_date', true );

	if ( $fixed_departure ) {
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
	$args       = array( 'trip_id' => $trip_id );
	$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
	if ( '' == $trip_price || '0' == $trip_price ) {
		unset( $fields['is_partial_payment'], $fields['payment_gateway'], $fields['booking_option'], $fields['trip_price'], $fields['payment_mode'], $fields['payment_amount'], $fields['trip_price_info'], $fields['payment_amount_info'] );
	}
	return $form->init( $form_options )->fields( $fields )->template();
}

/**
 * Array List of form field to generate booking fields.
 *
 * @since 1.0.0
 * @deprecated 4.4.0
 * @return array Returns form fields.
 */
function wp_travel_booking_form_fields() {
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_booking_form_fields', '4.4.0' );

	if ( WP_Travel::verify_nonce( true ) ) {
		return apply_filters( 'wp_travel_booking_form_fields', array() );
	}
	$trip_id = 0;
	global $post;
	global $wt_cart;
	if ( isset( $post->ID ) ) {
		$trip_id = $post->ID;
	}
	$cart_items = $wt_cart->getItems();
	$cart_trip  = '';

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
		if ( is_array( $_REQUEST['pax'] ) ) {
			$pax_size = array_sum( wptravel_sanitize_array( $_REQUEST['pax'] ) );
		}
	}
	$trip_duration = 1;
	if ( isset( $_REQUEST['trip_duration'] ) ) {
		$trip_duration = esc_attr( $_REQUEST['trip_duration'] );
	}

	$price_key = isset( $_GET['price_key'] ) && '' != $_GET['price_key'] ? sanitize_text_field( wp_unslash( $_GET['price_key'] ) ) : '';

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
			// 'options' => wptravel_get_countries(),
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
	if ( WP_Travel::is_page( 'checkout' ) ) {
		$booking_fields['pax']['type']             = 'hidden';
		$booking_fields['arrival_date']['default'] = date( 'm/d/Y', strtotime( $trip_start_date ) );
		$fixed_departure                           = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
		if ( 'yes' === $fixed_departure ) {
			$booking_fields['arrival_date']['type'] = 'hidden';
			unset( $booking_fields['departure_date'] );
		}
	}
	return apply_filters( 'wp_travel_booking_form_fields', $booking_fields );
}

/**
 * Return Tree Form of post Object.
 *
 * @param Object $elements Post Object.
 * @param Int    $parent_id Parent ID of post.
 *
 * @since 1.9.1
 * @deprecated 4.4.0
 * @return Object Return Tree Form of post Object.
 */
function wp_travel_build_post_tree( array &$elements, $parent_id = 0 ) {
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_build_post_tree', '4.4.0' );
	$branch = array();
	foreach ( $elements as $element ) {
		if ( $element->post_parent == $parent_id ) {
			$children = wp_travel_build_post_tree( $elements, $element->ID );
			if ( $children ) {
				$element->children = $children;
			}
			$branch[ $element->ID ] = $element;
			unset( $elements[ $element->ID ] );
		}
	}
	return $branch;
}

/**
 * [wp_travel_get_post_hierarchy_dropdown description]
 *
 * @param  [type]  $list_serialized [description].
 * @param  [type]  $selected        [description].
 * @param  integer $nesting_level   [description].
 * @param  boolean $echo            [description].
 * @since  1.9.1
 * @deprecated 4.4.0
 * @return [type]                   [description]
 */
function wp_travel_get_post_hierarchy_dropdown( $list_serialized, $selected, $nesting_level = 0, $echo = true ) {
	wp_travel_get_post_hierarchy_dropdown( 'wp_travel_build_post_tree', '4.4.0' );
	$contents = '';
	if ( $list_serialized ) :

		$space = '';
		for ( $i = 1; $i <= $nesting_level; $i ++ ) {
			$space .= '&nbsp;&nbsp;&nbsp;';
		}

		foreach ( $list_serialized as $content ) {

			$contents .= '<option value="' . $content->ID . '" ' . selected( $selected, $content->ID, false ) . ' >' . $space . $content->post_title . '</option>';
			if ( isset( $content->children ) ) {
				$contents .= wp_travel_get_post_hierarchy_dropdown( $content->children, $selected, ( $nesting_level + 1 ), false );
			}
		}
	endif;
	if ( ! $echo ) {
		return $contents;
	}
	echo $contents;
	return false;
}

/**
 * Get Price of trip. Price key is only for old data less than WP Travel @since 3.0.0-below legacy version.
 *
 * @since 3.0.0
 * @deprecated 4.4.0
 * @return Number
 */
function wp_travel_get_price( $trip_id, $is_regular_price = false, $pricing_id = '', $category_id = '', $price_key = '' ) {

	$args = array(
		'trip_id'          => $trip_id,
		'is_regular_price' => $is_regular_price,
		'pricing_id'       => $pricing_id,
		'category_id'      => $category_id,
		'price_key'        => $price_key,
	);

	wp_travel_deprecated_function( 'wp_travel_get_price', '4.4.0', 'WP_Travel_Helpers_Trips::get_price()' );
	return WP_Travel_Helpers_Pricings::get_price( $args );
}

/**
 * Return Trip Price. Modified 1.9.2, 2.0.7.
 *
 * @param int    $trip_id Post id of the post.
 * @param String $price_key Price key for multiple pricing.
 * @param Bool   $only_regular_price Return only trip price rather than sale price as trip price if this is set to true.
 *
 * @since 1.0.5
 * @deprecated 4.4.0
 * @return int Trip Price.
 */
function wp_travel_get_actual_trip_price( $trip_id = 0, $price_key = '', $is_regular_price = false ) {
	if ( ! $trip_id ) {
		return 0;
	}

	$args = array(
		'trip_id'          => $trip_id,
		'is_regular_price' => $is_regular_price,
		'price_key'        => $price_key,
	);

	wp_travel_deprecated_function( 'wp_travel_get_actual_trip_price', '4.4.0', 'WP_Travel_Helpers_Trips::get_price()' );
	return WP_Travel_Helpers_Pricings::get_price( $args );
}

/**
 * Return Trip Sale Price.
 *
 * @param  int $trip_id Post id of the post.
 *
 * @since 1.9.1
 * @deprecated 4.4.0
 * @return int Trip Price.
 */
function wp_travel_get_trip_sale_price( $trip_id = 0 ) {
	if ( ! $trip_id ) {
		return 0;
	}
	wp_travel_deprecated_function( 'wp_travel_get_trip_sale_price', '4.4.0', 'WP_Travel_Helpers_Trips::get_price()' );

	$args = array(
		'trip_id'          => $trip_id,
		'is_regular_price' => true,
	);
	return WP_Travel_Helpers_Pricings::get_price( $args );
}

/**
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
 *
 * @since 1.9.1
 * @deprecated 4.4.0
 * @return int Trip Price.
 */
function wp_travel_get_trip_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	wp_travel_deprecated_function( 'wp_travel_get_trip_price', '4.4.0', 'WP_Travel_Helpers_Trips::get_price()' );

	$args = array(
		'trip_id' => $trip_id,
	);
	return WP_Travel_Helpers_Pricings::get_price( $args );
}

/**
 * Return Min price key for the trip. modified in 2.0,5 not required from 3.0.0.
 *
 * @param Mixed $options pricing_option | trip id.
 *
 * @since 2.0.0
 * @deprecated 4.4.0
 * @return Mixed.
 */
function wp_travel_get_min_price_key( $options ) {
	if ( ! $options ) {
		return;
	}
	wp_travel_deprecated_function( 'wp_travel_get_min_price_key', '4.4.0' );

	$pricing_options = false;
	if ( is_array( $options ) ) {
		$pricing_options = $options;
	} elseif ( is_numeric( $options ) ) {
		$trip_id         = $options;
		$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );
	}

	if ( ! $pricing_options ) {
		return;
	}
	$min_price = 0;
	$price_key = '';
	foreach ( $pricing_options as $pricing_option ) {

		if ( isset( $pricing_option['categories'] ) ) {
			if ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
				$min_price = 0;
				foreach ( $pricing_option['categories'] as $category_id => $category_option ) {

					$price       = $category_option['price'];
					$enable_sale = isset( $category_option['enable_sale'] ) && 'yes' === $category_option['enable_sale'] ? true : false;
					$sale_price  = isset( $category_option['sale_price'] ) && $category_option['sale_price'] > 0 ? $category_option['sale_price'] : 0;

					if ( $enable_sale && $sale_price ) {
						$price = $category_option['sale_price'];
					}

					if ( ! $min_price || $price < $min_price ) {
						$min_price = $price;
						$price_key = $pricing_option['price_key'];
					}
				}
			}
		} else {

			if ( isset( $pricing_option['price'] ) ) { // old pricing option.
				$current_price = $pricing_option['price'];
				$enable_sale   = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
				$sale_price    = isset( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : 0;

				if ( 'yes' === $enable_sale && $sale_price > 0 ) {
					$current_price = $sale_price;

				}

				if ( ( 0 === $min_price && $current_price > 0 ) || $min_price > $current_price ) { // Initialize min price if 0.
					$min_price = $current_price;
					$price_key = $pricing_option['price_key'];
				}
			}
		}
	}
	return apply_filters( 'wp_travel_min_price_key', $price_key, $pricing_options ); // Filter @since 2.0.3.
}
