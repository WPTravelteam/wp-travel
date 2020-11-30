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
 * Return HTM of Booking Form
 *
 * @return [type] [description]
 */
function wp_travel_get_booking_form() {
	// No suggested alternative function.
	wp_travel_deprecated_function( 'wp_travel_get_booking_form', '4.3.5' );
	global $post;

	$trip_id = 0;
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
	$settings = wp_travel_get_settings();

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


