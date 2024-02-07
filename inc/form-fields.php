<?php
/**
 * Booking Functions.
 * // TODO: Remove This function.
 *
 * @package WP_Travel
 */

/**
 * Return HTML of Checkout Form Fields
 *
 * @return [type] [description]
 */
function wptravel_get_checkout_form_fields() {

	// @todo need to add this default value from array field itself.
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
		// if ( in_array( 'wp-travel-customer', (array) $user->roles, true ) ) {
			$user_fname = isset( $user->first_name ) ? $user->first_name : '';
			$user_lname = isset( $user->last_name ) ? $user->last_name : '';
			$user_email = isset( $user->user_email ) ? $user->user_email : '';

			$biling_data     = get_user_meta( $user->ID, 'wp_travel_customer_billing_details', true );
			$billing_city    = isset( $biling_data['billing_city'] ) ? $biling_data['billing_city'] : '';
			$billing_zip     = isset( $biling_data['billing_zip_code'] ) ? $biling_data['billing_zip_code'] : '';
			$billing_address = isset( $biling_data['billing_address'] ) ? $biling_data['billing_address'] : '';
			$billing_country = isset( $biling_data['billing_country'] ) ? $biling_data['billing_country'] : '';
			$billing_phone   = isset( $biling_data['billing_phone'] ) ? $biling_data['billing_phone'] : '';
		// }
	}

	$traveller_fields = WP_Travel_Default_Form_Fields::traveller();
	$traveller_fields = apply_filters( 'wp_travel_checkout_traveller_fields', $traveller_fields );

	$traveller_fields['first_name']['default']   = $user_fname;
	$traveller_fields['last_name']['default']    = $user_lname;
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
	$settings       = wptravel_get_settings();

	// GDPR Support.
	$gdpr_msg = ! empty( $settings['wp_travel_gdpr_message'] ) ? esc_html( $settings['wp_travel_gdpr_message'] ) : __( 'By contacting us, you agree to our ', 'wp-travel' );

	$policy_link = wptravel_privacy_link();
	

	$strings                    = WpTravel_Helpers_Strings::get();
	$label_booking_options      = $strings['bookings']['booking_option'];
	$label_booking_with_payment = $strings['bookings']['booking_with_payment'];
	$label_booking_only         = $strings['bookings']['booking_only'];

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

	// Default Booking option.
	$payment_fields['booking_option'] = array(
		'type'        => 'hidden',
		'label'       => '',
		'name'        => 'wp_travel_booking_option',
		'id'          => 'wp-travel-option',
		'validations' => array(
			'required' => true,
		),
		'default'     => 'booking_only',
		'priority'    => 140,
	);

	global $wt_cart;

	$cart_amounts = $wt_cart->get_total();

	$cart_total   = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : 0;
	if ( wptravel_is_payment_enabled() && $cart_total > 0 ) {
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
			'label'       => $label_booking_options,
			'name'        => 'wp_travel_booking_option',
			'id'          => 'wp-travel-option',
			'validations' => array(
				'required' => true,
			),
			'options'     => array(
				'booking_with_payment' => esc_html( $label_booking_with_payment ),
				'booking_only'         => esc_html( $label_booking_only ),
			),
			'default'     => 'booking_with_payment',
			'priority'    => 100,
		);

		$gateway_list        = wptravel_get_active_gateways();
		$active_gateway_list = isset( $gateway_list['active'] ) ? $gateway_list['active'] : array();
		$selected_gateway    = isset( $gateway_list['selected'] ) ? $gateway_list['selected'] : '';

		$trip_ids = $cart_items[ array_key_first( $cart_items ) ]['trip_id'];

		$trip_location = '';
		$payment       = '';


		if ( count( wp_get_post_terms( $trip_ids, 'travel_locations', array( 'fields' => 'all' ) ) ) > 0 ) {
			$trip_location = wp_get_post_terms( $trip_ids, 'travel_locations', array( 'fields' => 'all' ) )[0]->slug;

		}

		if ( ( $trip_location !== '' ) && class_exists( 'WP_Travel_Pro' ) && class_exists( 'WP_Travel_Conditional_Payment_Core' ) && wptravel_get_settings()['enable_conditional_payment'] == 'yes' ) {
			add_action(
				'wp_enqueue_scripts',
				function() {
					wp_localize_script( 'wp-travel-script', '_wp_travel_conditional_payment_list', wptravel_get_settings()['conditional_payment_list'] );
				}
			);

			$conditional_payment = array();
			foreach ( wptravel_get_settings()['conditional_payment_list'] as $value ) {

				if ( array_key_exists( $trip_location, $conditional_payment ) ) {
					array_push( $conditional_payment[ $trip_location ], $value['payment_gateway'] );
				} else {
					$conditional_payment[ $value['trip_location'] ] = array( $value['payment_gateway'] );
				}
			}

			if ( array_key_exists( $trip_location, $conditional_payment ) ) {
				$payment_list = array();

				$conditional_payment = $conditional_payment[ $trip_location ];

				foreach ( $conditional_payment as $value ) {
					if ( array_key_exists( $value, $gateway_list['active'] ) ) {
						if ( $value == 'authorizenet' ) {
							$payment_list[ $value ] = 'Authorize.Net';
						}
						if ( $value == 'paypal' ) {
							$payment_list[ $value ] = 'Standard Paypal';
						}
						if ( $value == 'bank_deposit' ) {
							$payment_list[ $value ] = 'Bank Deposit';
						}
						if ( $value == 'instamojo_checkout' ) {
							$payment_list[ $value ] = 'Instamojo checkout';
						}
						if ( $value == 'khalti' ) {
							$payment_list[ $value ] = 'Khalti';
						}
						if ( $value == 'payu' ) {
							$payment_list[ $value ] = 'PayU Checkout';
						}
						if ( $value == 'payu_latam' ) {
							$payment_list[ $value ] = 'PayU Latam Checkout';
						}
						if ( $value == 'payfast' ) {
							$payment_list[ $value ] = 'PayFast Checkout';
						}
						if ( $value == 'payhere' ) {
							$payment_list[ $value ] = 'PayHere Checkout';
						}
						if ( $value == 'express_checkout' ) {
							$payment_list[ $value ] = 'Paypal Express Checkout';
						}
						if ( $value == 'paystack' ) {
							$payment_list[ $value ] = 'Paystack Checkout';
						}
						if ( $value == 'razorpay_checkout' ) {
							$payment_list[ $value ] = 'Razorpay checkout';
						}
						if ( $value == 'squareup_checkout' ) {
							$payment_list[ $value ] = 'Squareup Checkout';
						}
						if ( $value == 'stripe' ) {
							$payment_list[ $value ] = 'Stripe Checkout';
						}
						if ( $value == 'stripe_ideal' ) {
							$payment_list[ $value ] = 'Stripe iDEAL Checkout';
						}
						
						$selected_gateway = $value;
					}
				}
				$payment = $payment_list;
				if ( $payment_list == null ) {
					$active_gateway_list = $active_gateway_list;
				} else {
					$active_gateway_list = $payment_list;
				}
			}
		} else {
			$active_gateway_list = $active_gateway_list;
		}

		if ( is_array( $active_gateway_list ) && count( $active_gateway_list ) > 0 ) {
			$selected_gateway = apply_filters( 'wp_travel_checkout_default_gateway', $selected_gateway );

			if ( class_exists( 'WP_Travel_Pro' ) && class_exists( 'WP_Travel_Conditional_Payment' ) && isset( wptravel_get_settings()['enable_CP_by_billing_address'] ) && wptravel_get_settings()['enable_CP_by_billing_address'] == 'yes' ) {
				$active_gateway_list = array();
			}

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

		if ( wptravel_is_partial_payment_enabled() ) {
			$payment_fields['payment_mode']        = array(
				'type'          => 'select',
				'label'         => __( 'Payment Mode', 'wp-travel' ),
				'name'          => 'wp_travel_payment_mode',
				'id'            => 'wp-travel-payment-mode',
				'wrapper_class' => 'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
				'validations'   => array(
					'required' => true,
				),
				'options'       => wptravel_get_payment_modes(),
				'default'       => 'full',
				'priority'      => 102,
			);
			$payment_fields['payment_amount_info'] = array(
				'type'          => 'text_info',
				'label'         => __( 'Payment Amount', 'wp-travel' ),
				'name'          => 'wp_travel_payment_amount_info',
				'id'            => 'wp-travel-payment-amount-info',
				'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment',
				'attributes'    => array(
					'data-wpt-cart-partial-total' => '',
				),
				'default'       => wptravel_get_formated_price_currency( $total_partial_amount ),
				'priority'      => 115,
			);
		}

		$method = array_keys( $active_gateway_list );
		if ( in_array( 'bank_deposit', $method, true ) ) {
			$payment_fields['bank_deposite_info'] = array(
				'type'          => 'text_info',
				'label'         => __( 'Bank Detail', 'wp-travel' ),
				'name'          => 'wp_travel_payment_bank_detail',
				'id'            => 'wp-travel-payment-bank-detail',
				'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment f-bank-deposit',
				'default'       => wptravel_get_bank_deposit_account_table(),
				'priority'      => 117,
			);
		}

		$payment_fields['trip_price_info'] = array(
			'type'          => 'text_info',
			'label'         => __( 'Total Trip Price', 'wp-travel' ),
			'name'          => 'wp_travel_trip_price_info',
			'id'            => 'wp-travel-trip-price_info',
			'attributes'    => array(
				'data-wpt-cart-total' => '',
			),
			'wrapper_class' => 'wp-travel-payment-field  f-booking-with-payment f-partial-payment f-full-payment',
			'default'       => wptravel_get_formated_price_currency( $total_amount ),
			'priority'      => 110,
		);

		$payment_fields['trip_price'] = array(
			'type'       => 'hidden',
			'label'      => __( 'Trip Price', 'wp-travel' ),
			'name'       => 'wp_travel_trip_price',
			'id'         => 'wp-travel-trip-price',
			'default'    => wptravel_get_formated_price( $trip_price ),
			'priority'   => 102,
			'attributes' => array(
				'data-wpt-cart-total' => '',
			),
		);
	}

	$checkout_fields = array(
		'traveller_fields' => $traveller_fields,
		'billing_fields'   => $fields,
		'payment_fields'   => $payment_fields,
	);
	$checkout_fields = apply_filters( 'wp_travel_checkout_fields', $checkout_fields ); // sort field after this filter.


	if ( isset( $checkout_fields['traveller_fields'] ) ) {
		$checkout_fields['traveller_fields'] = wptravel_sort_form_fields( $checkout_fields['traveller_fields'] );
	}
	if ( isset( $checkout_fields['billing_fields'] ) ) {
		$checkout_fields['billing_fields'] = wptravel_sort_form_fields( $checkout_fields['billing_fields'] );
	}
	if ( isset( $checkout_fields['payment_fields'] ) ) {
		$checkout_fields['payment_fields'] = wptravel_sort_form_fields( $checkout_fields['payment_fields'] );
	}
	return $checkout_fields;
}

/**
 * Array List of form field to generate booking fields.
 *
 * @return array Returns form fields.
 */
function wptravel_search_filter_widget_form_fields( $sanitize_get = array() ) {

	/**
	 * Already checking nonce above using WP_Travel::verify_nonce( true ).
	 */
	$keyword  = ( isset( $sanitize_get['keyword'] ) && '' !== $sanitize_get['keyword'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['keyword'] ) ) : '';
	$fact     = ( isset( $sanitize_get['fact'] ) && '' !== $sanitize_get['fact'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['fact'] ) ) : '';
	$type     = ( isset( $sanitize_get['itinerary_types'] ) && '' !== $sanitize_get['itinerary_types'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['itinerary_types'] ) ) : '';
	$location = ( isset( $sanitize_get['travel_locations'] ) && '' !== $sanitize_get['travel_locations'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['travel_locations'] ) ) : '';
	$price    = ( isset( $sanitize_get['price'] ) ) ? sanitize_text_field( $sanitize_get['price'] ) : '';

	$min_price   = ( isset( $sanitize_get['min_price'] ) && '' !== $sanitize_get['min_price'] ) ? (float) $sanitize_get['min_price'] : 0;
	$max_price   = ( isset( $sanitize_get['max_price'] ) && '' !== $sanitize_get['max_price'] ) ? (float) $sanitize_get['max_price'] : 0;
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

	$trip_start = (int) ( isset( $sanitize_get['trip_start'] ) && '' !== $sanitize_get['trip_start'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['trip_start'] ) ) : '';
	$trip_end   = (int) ( isset( $sanitize_get['trip_end'] ) && '' !== $sanitize_get['trip_end'] ) ? sanitize_text_field( wp_unslash( $sanitize_get['trip_end'] ) ) : '';

	$show_end_date = wptravel_booking_show_end_date();

	$strings       = WpTravel_Helpers_Strings::get();
	$trip_duration = array(
		array(
			'name'  => 'trip_start',
			'label' => $strings['from'],
			'value' => $trip_start,
			'id'    => 'datepicker1', // Extra id.

		),
	);
	if ( $show_end_date ) {
		$trip_duration[] = array(
			'name'  => 'trip_end',
			'label' => $strings['to'],
			'value' => $trip_end,
			'id'    => 'datepicker2', // Extra id.
		);
	}

	// Note. Main key of $fields array is used as customizer to show field.
	$fields = array(
		'keyword_search'       => array(
			'type'        => 'text',
			'label'       => $strings['keyword'],
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
			'label'       => $strings['fact'],
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
			'label'           => $strings['trip_type'],
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
			'label'           => $strings['location'],
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
			'label'       => $strings['price'],
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
			'label'         => $strings['price_range'],
			'id'            => 'amount',
			'class'         => 'wp_travel_search_widget_filters_input',
			'default'       => $price_range,
			'priority'      => 60,
			'wrapper_class' => 'wp-trave-price-range',
		),
		'trip_dates'           => array(
			'type'          => 'date_range',
			'label'         => $strings['trip_duration'],
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

	return wptravel_sort_form_fields( $fields );
}

/**
 * Bank Deposit form fields.
 *
 * @since 2.0.0
 */
function wptravel_get_bank_deposit_form_fields() {
	$fields = array();
	if ( wptravel_is_partial_payment_enabled() ) {

		$fields['payment_mode'] = array(
			'type'          => 'select',
			'label'         => __( 'Payment Mode', 'wp-travel' ),
			'name'          => 'wp_travel_payment_mode',
			'id'            => 'wp-travel-payment-mode',
			'wrapper_class' => 'wp-travel-payment-field f-booking-with-payment f-partial-payment f-full-payment',
			'validations'   => array(
				'required' => true,
			),
			'options'       => wptravel_get_payment_modes(),
			'default'       => 'full',
			'priority'      => 10,
		);

	}
	$fields['wp_travel_bank_deposit_slip']           = array(
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
	return wptravel_sort_form_fields( $fields );
}
