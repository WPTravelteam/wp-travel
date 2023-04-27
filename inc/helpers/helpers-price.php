<?php
/**
 * Price Functions.
 *
 * @package WP_Travel
 */

/**
 * Return price per fields.
 *
 * @since 1.0.5
 * @return array
 */
function wptravel_get_price_per_fields() {
	$price_per = array(
		'person' => __( 'Person', 'wp-travel' ),
		'group'  => __( 'Group', 'wp-travel' ),
	);

	return apply_filters( 'wp_travel_price_per_fields', $price_per );
}

/**
 * @since 3.0.6
 */
function wptravel_get_price_per_by_key( $key = null ) {
	if ( ! $key ) {
		return;
	}

	$variation = wptravel_get_price_per_fields();

	if ( array_key_exists( $key, $variation ) ) {
		return $variation[ $key ];
	}
	return;
}

/**
 * Get Price Per text.
 *
 * @param Number $post_id Current post id.
 * @since 1.0.5
 */
function wptravel_get_price_per_text( $trip_id, $price_key = '', $return_key = false, $category_id = null ) {
	if ( ! $trip_id ) {
		return;
	}
	$pricing_option_type = get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true );

	if ( 'single-price' === $pricing_option_type ) {
		// Single pricing option.
		$price_per_fields = wptravel_get_price_per_fields();

		$per_person_key = get_post_meta( $trip_id, 'wp_travel_price_per', true );
		if ( ! $per_person_key ) {
			$per_person_key = 'person';
		}

		if ( true === $return_key ) {
			return $per_person_key;
		}

		return $price_per_fields[ $per_person_key ];

	} elseif ( 'multiple-price' === $pricing_option_type ) {
		// multiple pricing option.
		$pricing_data = wptravel_get_pricing_variation( $trip_id, $price_key );
		if ( is_array( $pricing_data ) && '' !== $pricing_data ) {
			$price_per_fields = wptravel_get_pricing_variation_options();

			foreach ( $pricing_data as $p_ky => $pricing ) :

				if ( isset( $pricing['categories'] ) ) {
					if ( is_array( $pricing['categories'] ) && count( $pricing['categories'] ) > 0 ) {

						if ( $category_id ) {
							$category_option = isset( $pricing_data['categories'][ $category_id ] ) ? $pricing_data['categories'][ $category_id ] : array();
							$per_person_key  = isset( $category_option['type'] ) ? $category_option['type'] : '';
						} else {
							$min_price = 0;
							foreach ( $pricing['categories'] as $category_id => $category_option ) {

								$price       = $category_option['price'];
								$enable_sale = isset( $category_option['enable_sale'] ) && 'yes' === $category_option['enable_sale'] ? true : false;
								$sale_price  = isset( $category_option['sale_price'] ) && $category_option['sale_price'] > 0 ? $category_option['sale_price'] : 0;

								if ( $enable_sale && $sale_price ) {
									$price = $category_option['sale_price'];
								}

								if ( ! $min_price || $price < $min_price ) {
									$min_price      = $price;
									$per_person_key = $category_option['type'];
								}
							}
						}
					}
				} else {

					$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
					$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';

					$per_person_key = $pricing_type;
					if ( 'custom' === $pricing_type ) {
						$per_person_key = $pricing_custom_label;
						// also append this key value in $price_per_fields.
						$price_per_fields[ $per_person_key ] = $per_person_key;
					}
				}
			endforeach;

			if ( $per_person_key ) {

				if ( true === $return_key ) {
					return $per_person_key;
				}

				return $price_per_fields[ $per_person_key ];
			}
		}
	}

}

/**
 * Wp Travel Process Trip Price Tax.
 *
 * @param int $trip_id post id.
 * @return mixed $trip_price | $tax_details.
 */
function wptravel_process_trip_price_tax( $trip_id ) {

	if ( ! $trip_id ) {
		return 0;
	}
	$settings = wptravel_get_settings();

	$args       = $args_regular = array( 'trip_id' => $trip_id );
	$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );

	if ( WP_Travel_Helpers_Trips::is_tax_enabled() ) {

		$tax_details         = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$tax_percentage      = @$settings['trip_tax_percentage'];

		if ( 0 == $trip_price || '' == $tax_percentage ) {
			return array( 'trip_price' => $trip_price );
		}

		if ( 'yes' == $tax_inclusive_price ) {
			$tax_details['tax_type']          = 'inclusive';
			$tax_details['tax_percentage']    = $tax_percentage;
			$actual_trip_price                = ( 100 * $trip_price ) / ( 100 + $tax_percentage );
			$tax_details['trip_price']        = $actual_trip_price;
			$tax_details['actual_trip_price'] = $trip_price;
			return $tax_details;
		} else {
			$tax_details['tax_type']          = 'excluxive';
			$tax_details['tax_percentage']    = $tax_percentage;
			$tax_details['trip_price']        = $trip_price;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2, '.', '' );
			return $tax_details;
		}
	}

	return array( 'trip_price' => $trip_price );

}

function wptravel_taxed_amount( $amount ) {

	$settings = wptravel_get_settings();

	if ( WP_Travel_Helpers_Trips::is_tax_enabled() ) {
		$tax_details         = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$tax_percentage      = @$settings['trip_tax_percentage'];

		if ( 0 == $amount || '' == $tax_percentage ) {
			return $amount;
		}
		if ( 'no' == $tax_inclusive_price ) {
			return number_format( ( $amount + ( ( $amount * $tax_percentage ) / 100 ) ), 2, '.', '' );
		}
	}
	return $amount;
}

/**
 * Get pricing variation dates.
 *
 * @return array $available_dates Variation Options.
 */
function wptravel_get_pricing_variation_dates( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {

		return false;

	}

	// Get Dates.
	$available_trip_dates = get_post_meta( $post_id, 'wp_travel_multiple_trip_dates', true );

	if ( is_array( $available_trip_dates ) && '' !== $available_trip_dates ) {

		$result = array_filter(
			$available_trip_dates,
			function( $single ) use ( $pricing_key ) {
				$pricing_options = isset( $single['pricing_options'] ) ? $single['pricing_options'] : array();
				return in_array( $pricing_key, $pricing_options );
			}
		);

		return $result;

	}

	return false;

}

/**
 * Get pricing variation price_per_value
 *
 * @return string pricing variation price_per value.
 */
function wptravel_get_pricing_variation_price_per( $post_id, $pricing_key ) {

	if ( '' === $post_id || '' === $pricing_key ) {

		return false;

	}

	// Get Pricing variations.
	$pricing_variations = get_post_meta( $post_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

		foreach ( $pricing_variations as $ky => $variation ) {

			if ( $pricing_variations[ $ky ]['price_key'] === $pricing_key ) {

				return isset( $pricing_variations[ $ky ]['price_per'] ) ? $pricing_variations[ $ky ]['price_per'] : 'trip-default';

			}
		}
	}

	return 'trip-default';

}

/**
 * Calculate Due amount.
 *
 * @since 1.8.0
 * @return array
 */
function wptravel_booking_data( $booking_id ) {

	if ( ! $booking_id ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );

	$booking_status = get_post_meta( $booking_id, 'wp_travel_booking_status', true );
	$booking_status = ! empty( $booking_status ) ? $booking_status : 'N/A';

	$payment_ids       = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
	$total_paid_amount = 0;
	$mode              = wptravel_get_payment_mode();

	// Total trip price only in first payment id so we need to get total trip price from first payment id.
	if ( is_array( $payment_ids ) && count( $payment_ids ) > 0 ) {
		if ( isset( $payment_ids[0] ) ) {
			$trip_price = ( get_post_meta( $payment_ids[0], 'wp_travel_trip_price', true ) ) ? get_post_meta( $payment_ids[0], 'wp_travel_trip_price', true ) : 0;
			$trip_price = number_format( $trip_price, 2, '.', '' );
		}

		foreach ( $payment_ids as $payment_id ) {

			$paid_amount = ( get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) ) ? get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) : 0;
			$paid_amount = number_format( $paid_amount, 2, '.', '' );

			$total_paid_amount += $paid_amount;
			// $last_payment_id = $payment_id;
		}
	} else {
		$payment_id = $payment_ids;

		$trip_price = ( get_post_meta( $payment_id, 'wp_travel_trip_price', true ) ) ? get_post_meta( $payment_id, 'wp_travel_trip_price', true ) : 0;
		$trip_price = number_format( $trip_price, 2, '.', '' );

		$paid_amount = ( get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) ) ? get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) : 0;
		$paid_amount = number_format( $paid_amount, 2, '.', '' );

		$total_paid_amount += $paid_amount;
	}

	$sub_total        = $trip_price; // init sub total.
	$discount         = 0;
	$taxed_trip_price = wptravel_taxed_amount( $trip_price ); // Trip price including tax.
	$tax              = $taxed_trip_price - $sub_total;

	$total = $sub_total - $discount + $tax;

	// Price Calculation for multiple trips. after 1.8.0 it also included in single trip.
	$order_totals = get_post_meta( $booking_id, 'order_totals', true );
	if ( isset( $order_totals['sub_total'] ) && $order_totals['sub_total'] > 0 ) {
		$sub_total = isset( $order_totals['sub_total'] ) ? $order_totals['sub_total'] : $sub_total;
		$discount  = isset( $order_totals['discount'] ) ? $order_totals['discount'] : $discount;
		// Above sub total excludes discount so we need to add it here.
		$sub_total += $discount;
		$tax        = isset( $order_totals['tax'] ) ? $order_totals['tax'] : $tax;
		$total      = isset( $order_totals['total'] ) ? $order_totals['total'] : $total;
	}

	$due_amount = $total - $total_paid_amount;
	if ( $due_amount < 0 ) {
		$due_amount = 0;
	}

	

	$payment_status = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
	$payment_status = ( ! empty( $payment_status ) ) ? $payment_status : 'N/A';

	$label_key    = get_post_meta( $payment_id, 'wp_travel_payment_mode', true );
	$payment_mode = isset( $mode[ $label_key ]['text'] ) ? $mode[ $label_key ]['text'] : 'N/A';

	$booking_option = get_post_meta( $payment_id, 'wp_travel_booking_option', true );
	$booking_option = ( ! empty( $booking_option ) ) ? $booking_option : 'booking_with_payment'; // if booking option empty while paying from dashboard then default assign to booking_with_payment.
	

	$partial_mode = get_post_meta( $booking_id, 'wp_travel_is_partial_payment', true );

	/**
	* Change payment mode N/A to full while payment full.
	* @since 6.6.0
	*/
	$booking_paid = get_post_meta( $booking_id, 'wp_travel_payment_status', true );
	if ( $booking_paid == 'paid' && $payment_status == 'paid' && $partial_mode == 'no' &&  $total > 0 ) {

		if ( $label_key == 'full' && $due_amount > 0 ) {
			update_post_meta( $payment_id, 'wp_travel_payment_amount', $total );
		}
		$pay_amount = get_post_meta( $payment_id, 'wp_travel_payment_amount', true );
		if ( $label_key == '' && $pay_amount == 0 ) {
			update_post_meta( $payment_id, 'wp_travel_payment_amount', $total );
			update_post_meta( $payment_id, 'wp_travel_payment_mode', 'full' );
		}
	}

	if ( $payment_status == 'paid' ) {
		if ( $partial_mode == 'yes' ) {
			if ( $due_amount != 0.00 ) {
				update_post_meta( $payment_id, 'wp_travel_partial_payment_amount_change_status', $total_paid_amount );
				update_post_meta( $payment_id, 'wp_travel_payment_amount', $total );
				update_post_meta( $payment_id, 'wp_travel_payment_mode', 'full' );
				$due_amount = 0.00;
				$total_paid_amount = $total;
				$payment_mode = 'full';
			}
		}
	} else {  
		if ( $payment_status == 'partially_paid' ) {
			if ( $partial_mode == 'yes' ) {
				$change_status = get_post_meta( $payment_id, 'wp_travel_partial_payment_amount_change_status', true );
				if ( isset( $change_status ) && ! empty( $change_status ) && $change_status > 0 && $change_status != $total ) {
					$due_amount_after_change_status = $total - $change_status;
					if ( $due_amount_after_change_status < 0 ) {
						$due_amount_after_change_status = 0;
					}
					update_post_meta( $payment_id, 'wp_travel_payment_mode', 'partial' );
					update_post_meta( $payment_id, 'wp_travel_payment_amount', $change_status );
					$due_amount = $due_amount_after_change_status;
					$payment_mode = 'partial';
					$total_paid_amount = $change_status;
				}
			}
		}
	}
	$amounts = array(
		'booking_status' => $booking_status,
		'booking_option' => $booking_option,
		'mode'           => $mode,
		'payment_mode'   => $payment_mode,
		'payment_status' => $payment_status,
		'payment_id'     => $payment_id,
		'sub_total'      => sprintf( '%0.2f', $sub_total ),
		'discount'       => sprintf( '%0.2f', $discount ),
		'tax'            => sprintf( '%0.2f', $tax ),
		'total'          => sprintf( '%0.2f', $total ),
		'paid_amount'    => sprintf( '%0.2f', $total_paid_amount ),
		'due_amount'     => sprintf( '%0.2f', $due_amount ),
	);
	// Partical calculation.
	if ( wptravel_is_partial_payment_enabled() ) {
		$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );

		if ( $payout_percent > 0 ) {
			$trip_price_partial       = ( $trip_price * $payout_percent ) / 100;
			$trip_price_partial       = wptravel_get_formated_price( $trip_price_partial );
			$taxed_trip_price_partial = wptravel_taxed_amount( $trip_price_partial ); // Trip price including tax.
			$tax_partial              = $taxed_trip_price_partial - $trip_price_partial;

			$total_partial = $trip_price_partial + $tax_partial;

			$amounts['sub_total_partial'] = $trip_price_partial;
			$amounts['tax_partial']       = $tax_partial;
			$amounts['total_partial']     = $total_partial;
		}
	}
	return $amounts;
}

/**
 * Return last payment ID.
 *
 * @since 2.0.0
 */
function wptravel_get_payment_id( $booking_id ) {
	if ( ! $booking_id ) {
		return;
	}
	$payment_ids = get_post_meta( $booking_id, 'wp_travel_payment_id', true );

	if ( is_array( $payment_ids ) && count( $payment_ids ) > 0 ) {
		$payment_id = end( $payment_ids );
	} else {
		$payment_id = $payment_ids;
	}
	return $payment_id;
}

/**
 * Modified version of previous `wptravel_get_cart_attrs` function.
 *
 * @param [type]  $trip_id
 * @param integer $pax
 * @param string  $price_key
 * @param boolean $return_price
 * @return void
 */
function wptravel_get_cart_attrs( $args, $pax = 0, $price_key = '', $pricing_id = null, $trip_start_date = null, $return_price = false ) {
	if ( ! $args ) {
		return 0;
	}

	if ( is_array( $args ) ) {
		$trip_id         = isset( $args['trip_id'] ) ? $args['trip_id'] : 0;
		$pax             = isset( $args['pax'] ) ? $args['pax'] : 0; // May be not required. @todo need to verify this field..
		$price_key       = isset( $args['price_key'] ) ? $args['price_key'] : '';
		$pricing_id      = isset( $args['pricing_id'] ) ? $args['pricing_id'] : null;
		$trip_start_date = isset( $args['trip_start_date'] ) ? $args['trip_start_date'] : null;
		$return_price    = isset( $args['return_price'] ) ? $args['return_price'] : false;
		$request_data    = isset( $args['request_data'] ) ? $args['request_data'] : array();
	} else {
		$trip_id = $args; // Legacy.
	}

	$enable_pricing_options = wptravel_is_enable_pricing_options( $trip_id );
	$group_size             = get_post_meta( $trip_id, 'wp_travel_group_size', true );
	$group_size             = ! empty( $group_size ) ? $group_size : 999;

	if ( ! empty( $price_key ) && $enable_pricing_options ) {
		$valid_price_key = wptravel_is_price_key_valid( $trip_id, $price_key );

		if ( $valid_price_key ) {
			$pricing_data = wptravel_get_pricing_variation( $trip_id, $price_key );

			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

				foreach ( $pricing_data as $p_ky => $pricing ) :
					// Product Metas.
					$trip_start_date       = isset( $request_data['arrival_date'] ) && '' !== $request_data['arrival_date'] ? $request_data['arrival_date'] : '';
					$pricing_default_types = wptravel_get_pricing_variation_options();
					$max_available         = ! empty( $pricing['max_pax'] ) ? $pricing['max_pax'] : $group_size;
					$min_available         = ! empty( $pricing['min_pax'] ) ? $pricing['min_pax'] : 1;

				endforeach;
			}
		}
	} else {
		$switch_to_react = wptravel_is_react_version_enabled();
		if ( $switch_to_react && $pricing_id ) {
			$pricings_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id );
			if ( ! is_wp_error( $pricings_data ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricings_data['code'] ) {
				$pricings_data = $pricings_data['pricings'];
				foreach ( $pricings_data as $pricing_data ) {
					if ( $pricing_data['id'] === (int) $pricing_id ) {
						$max_available = $pricing_data['max_pax'];
						$min_available = 1;
						break;
					}
				}
			}
		} else {
			// Product Metas.
			$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
			$max_available   = $group_size;
			$min_available   = 1;
		}
	}

	if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {

		$inventory = new WP_Travel_Util_Inventory();

		$inventory_enabled = $inventory->is_inventory_enabled( $trip_id );
		$available_pax     = $inventory->get_available_pax( $trip_id, $price_key, $trip_start_date );

		$args = array(
			'trip_id'       => $trip_id,
			'pricing_id'    => $pricing_id,
			'selected_date' => $trip_start_date,
			// 'times'         => $times,
			'times'         => null,
		);

		$response = WP_Travel_Helpers_Inventory::get_inventory( $args );
		if ( is_array( $response ) && isset( $response['code'] ) && 'WP_TRAVEL_INVENTORY_INFO' === $response['code'] ) {
			$available_pax = $response['inventory'][0]['pax_available'];
		}

		if ( $inventory_enabled && $available_pax ) {
			$max_available = $available_pax;
		}
	}

	$attrs = array(
		'max_available'   => $max_available,
		'min_available'   => $min_available,
		'trip_start_date' => $trip_start_date,
		'currency'        => wptravel_get_currency_symbol(), // added in 1.8.4
	);

	return $attrs;

}

function wptravel_get_partial_trip_price( $trip_id, $price_key = null ) {

	$args       = array(
		'trip_id'   => $trip_id,
		'price_key' => $price_key,
	);
	$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );

	if ( wptravel_is_partial_payment_enabled() ) {
		$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
		if ( $payout_percent > 0 ) {
			$trip_price = ( $trip_price * $payout_percent ) / 100;
			$trip_price = wptravel_get_formated_price( $trip_price );
		}
	}

	return $trip_price;
}

/**
 * Validate pricing Key
 *
 * @return bool true | false.
 */
function wptravel_is_price_key_valid( $trip_id, $price_key ) {

	if ( '' === $trip_id || '' === $price_key ) {
		return false;
	}
	// Get Pricing variations.
	$pricing_variations = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

		$result = array_filter(
			$pricing_variations,
			function( $single ) use ( $price_key ) {
				return in_array( $price_key, $single, true );
			}
		);
		return ( '' !== $result && count( $result ) > 0 ) ? true : false;
	}
	return false;
}

function wptravel_is_enable_pricing_options( $trip_id ) {
	return isset( $trip_id ) && 'multiple-price' === wptravel_get_pricing_option_type( $trip_id );
}

/**
 * Used For Calculation purpose. for display purpose use wptravel_get_formated_price_currency.
 *
 * @param int  $price Amount to be formatted.
 * @param bool $format If true should be formatted according to the WP Travel Number fomatting Setting @since v3.0.4
 * @param int  $number_of_decimals Number after decimal .00.
 */
function wptravel_get_formated_price( $price, $format = true, $number_of_decimals = 2 ) {
	if ( ! $price ) {
		return 0;
	}

	if ( ! $format ) {
		return $price;
	}

	// $settings           = wptravel_get_settings();
	// $thousand_separator = '';
	// $decimal_separator  = $settings['decimal_separator'];
	// $number_of_decimals = isset( $settings['number_of_decimals'] ) && ! empty( $settings['number_of_decimals'] ) ? $settings['number_of_decimals'] : 0;
	/**
	 * Defaults to all the currency to fix the issue caused by formatting.
	 *
	 * @since 3.0.4
	 */
	return number_format( $price, $number_of_decimals, '.', '' );
}

/**
 * Supports the multiple currency plugin for converting price according to the selected currency.
 * This function must be called just before the displaying the price.
 * The main purpose of this function is to reduce the number of hooks used by the multiple currency plugin
 * to retain the consistency in code and better enhanced debugging process that can occured due to using multiple hooks.
 *
 * @param int|float $price Unformatted Price that needs to be converted.
 * @param bool      $convert [Optional] Default is true, pass false if the price has been already been converted,
 *                                      so it won't reconvert it the converted price.
 *
 * @since 4.0.7
 * @author Garvit Shrestha
 */
function wptravel_convert_price( $price, $convert = true ) {

	if ( ! $convert ) {
		return $price;
	}

	return apply_filters( 'wp_travel_convert_price', $price );
}

/**
 * Currency position with price
 *
 * @param Number  $price         Price.
 * @param Boolean $regular_price Is price regular or sale price.
 * @param String  $price_key     Price key of the price. It is only required for some customization.
 * @param int     $post_id       Can be booking id or trip id @since 3.0.2
 *
 * @since 2.0.1 / 2.0.3
 *
 * @return Mixed
 */
function wptravel_get_formated_price_currency( $price = 0, $regular_price = false, $price_key = '', $post_id = null ) {
	$price             = (float) $price;
	$settings          = wptravel_get_settings();
	$currency_position = isset( $settings['currency_position'] ) ? $settings['currency_position'] : 'left';

	$filter_name     = 'wp_travel_itinerary_sale_price'; // Filter for customization work support.
	$price_class     = 'wp-travel-trip-price-figure';
	$currency_symbol = apply_filters( 'wp_travel_display_currency_symbol', wptravel_get_currency_symbol(), $post_id );
	if ( $regular_price ) {
		$filter_name = 'wp_travel_itinerary_price';
		$price_class = 'wp-travel-regular-price-figure';
	}

	// Price Format Start.
	$thousand_separator = $settings['thousand_separator'] ? $settings['thousand_separator'] : ',';
	$decimal_separator  = $settings['decimal_separator'] ? $settings['decimal_separator'] : '.';
	$number_of_decimals = isset( $settings['number_of_decimals'] ) && ! empty( $settings['number_of_decimals'] ) ? $settings['number_of_decimals'] : 0;
	$price              = number_format( $price, $number_of_decimals, $decimal_separator, $thousand_separator );
	// End of Price Format.
	ob_start();
	switch ( $currency_position ) {
		case 'left':
			?>
			<span class="wp-travel-trip-currency"><?php echo esc_html( $currency_symbol ); ?></span><span class="<?php echo esc_attr( $price_class ); ?>"><?php echo esc_html( $price ); ?></span>
			<?php
			break;
		case 'left_with_space':
			?>
			<span class="wp-travel-trip-currency"><?php echo esc_html( $currency_symbol ); ?></span> <span class="<?php echo esc_attr( $price_class ); ?>"><?php echo esc_html( $price ); ?></span>
			<?php
			break;
		case 'right':
			?>
			<span class="<?php echo esc_attr( $price_class ); ?>"><?php echo esc_html( $price ); ?></span><span class="wp-travel-trip-currency"><?php echo esc_html( $currency_symbol ); ?></span>
			<?php
			break;
		case 'right_with_space':
			?>
			<span class="<?php echo esc_attr( $price_class ); ?>"><?php echo esc_html( $price ); ?></span> <span class="wp-travel-trip-currency"><?php echo esc_html( $currency_symbol ); ?></span>
			<?php
			break;
	}
	$content = ob_get_contents();
	ob_end_clean();

	return apply_filters( $filter_name, $content, $currency_symbol, $price );
}

/**
 * Get Pricing option type[single-pricing || multiple-pricing].
 *
 * @param   int $post_id Post ID.
 *
 * @since   1.7.6
 * @return String Pricing option type.
 */
function wptravel_get_pricing_option_type( $post_id = null ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
	}
	$switch_to_react = wptravel_is_react_version_enabled();

	// need to remove in future. [replaced this with 'wp_travel_pricing_option_type' meta]. @since 1.7.6
	$enable_pricing_options = get_post_meta( $post_id, 'wp_travel_enable_pricing_options', true );

	$pricing_option_type = get_post_meta( $post_id, 'wp_travel_pricing_option_type', true );
	if ( ! $pricing_option_type ) {
		$pricing_option_type = isset( $enable_pricing_options ) && 'yes' === $enable_pricing_options ? 'multiple-price' : 'single-price';
	}

	if ( $switch_to_react && 'single-price' === $pricing_option_type ) {
		$pricing_option_type = 'multiple-price';
	}
	return $pricing_option_type;
}

function wptravel_get_payment_modes() {
	$modes = array(
		'partial' => esc_html__( 'Partial Payment', 'wp-travel' ),
		'full'    => esc_html__( 'Full Payment', 'wp-travel' ),
	);
	return apply_filters( 'wp_travel_payment_modes', $modes );
}

function wptravel_get_min_pricing_id( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}

	$price           = 0;
	$min_pricing_id  = '';
	$min_category_id = '';

	$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

	// All listings to get min price.
	if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
		foreach ( $pricing_options as $pricing_id => $pricing_option ) {

			if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0-below legacy version
				// foreach ( $pricing_options as $pricing_option ) {
				if ( isset( $pricing_option['price'] ) ) { // old pricing option.
					$current_price = $pricing_option['price'];
					$enable_sale   = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
					$sale_price    = isset( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : 0;

					if ( 'yes' === $enable_sale && $sale_price > 0 ) {
						$current_price = $sale_price;
					}

					if ( ( 0 === $price && $current_price > 0 ) || $price > $current_price ) { // Initialize min price if 0.
						$price          = $current_price;
						$min_pricing_id = $pricing_id;
					}
				}
				// }
			} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
				foreach ( $pricing_option['categories'] as $category_id => $category_option ) {

					$current_price = $category_option['price'];
					$enable_sale   = isset( $category_option['enable_sale'] ) ? $category_option['enable_sale'] : 'no';
					$sale_price    = isset( $category_option['sale_price'] ) ? $category_option['sale_price'] : 0;

					if ( 'yes' === $enable_sale && $sale_price > 0 ) {
						$current_price = $sale_price;
					}

					if ( ( 0 === $price && $current_price > 0 ) || $price > $current_price ) { // Initialize min price if 0.
						$price           = $current_price;
						$min_catetory_id = $category_id; // Add min price category id.
						$min_pricing_id  = $pricing_id;
					}
				}

				// Return regular price.
				// if ( $return_regular_price && ! empty( $min_catetory_id ) && isset( $pricing_option['categories'][ $min_catetory_id ]['price'] ) ) {
				// $price = $pricing_option['categories'][ $min_catetory_id ]['price'];
				// }
			}
		}
	}
	return array(
		'pricing_id'  => $min_pricing_id,
		'category_id' => $min_category_id,
	);
}
