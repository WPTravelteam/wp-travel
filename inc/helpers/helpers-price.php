<?php
/**
 * Price Functions.
 *
 * @package wp-travel/inc
 */

// Migrated functions from inc/helpers.php.

/**
 * Return price per fields.
 *
 * @since 1.0.5
 * @return array
 */
function wp_travel_get_price_per_fields() {
	$price_per = array(
		'person' => __( 'Person', 'wp-travel' ),
		'group'  => __( 'Group', 'wp-travel' ),
	);

	return apply_filters( 'wp_travel_price_per_fields', $price_per );
}

/**
 * Get Price Per text.
 *
 * @param Number $post_id Current post id.
 * @since 1.0.5
 */
function wp_travel_get_price_per_text( $trip_id, $price_key = '', $return_key = false ) {
	if ( ! $trip_id ) {
		return;
	}
	$pricing_option_type = get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true );

	if ( 'single-price' === $pricing_option_type ) {
		// Single pricing option.
		$price_per_fields = wp_travel_get_price_per_fields();

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
		$pricing_data = wp_travel_get_pricing_variation( $trip_id, $price_key );
		if ( is_array( $pricing_data ) && '' !== $pricing_data ) {
			$price_per_fields = wp_travel_get_pricing_variation_options();

			foreach ( $pricing_data as $p_ky => $pricing ) :
				$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
				$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';

				$per_person_key = $pricing_type;
				if ( 'custom' === $pricing_type ) {
					$per_person_key = $pricing_custom_label;
					// also append this key value in $price_per_fields.
					$price_per_fields[ $per_person_key ] = $per_person_key;
				}
			endforeach;

			if ( true === $return_key ) {
				return $per_person_key;
			}

			return $price_per_fields[ $per_person_key ];

		}
	}

}

/**
 * Check sale price enable or not.
 *
 * @param Number $post_id Current post id.
 * @param String $price_key Price Key for multiple pricing.
 * @since 1.0.5 Modified in 2.0.1, 2.0.5, 2.0.7 // Need to depricate, use wp_travel_is_enable_sale_price @since 3.0.0
 */
function wp_travel_is_enable_sale( $post_id, $price_key = null ) {
	if ( ! $post_id ) {
		return false;
	}
	$pricing_option_type = get_post_meta( $post_id, 'wp_travel_pricing_option_type', true );
	$enable_sale         = false;

	if ( 'single-price' === $pricing_option_type ) {
		$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	} elseif ( 'multiple-price' === $pricing_option_type ) {
		$pricing_options = get_post_meta( $post_id, 'wp_travel_pricing_options', true );

		if ( $price_key & ! empty( $price_key ) ) { // checks in indivicual pricing key [specific pricing is enabled in trip].
			if ( isset( $pricing_options[ $price_key ]['enable_sale'] ) && 'yes' === $pricing_options[ $price_key ]['enable_sale'] ) {
				$enable_sale = true;
			}
		} else { // Checks as a whole. if any pricing is enabled then return true.
			if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
				foreach ( $pricing_options as $pricing_key => $option ) {
					if ( isset( $option['enable_sale'] ) && 'yes' === $option['enable_sale'] ) {
						$enable_sale = true;
						break;
					}
				}
			}
		}
	}

	$enable_sale = apply_filters( 'wp_travel_enable_sale', $enable_sale, $post_id, $pricing_option, $price_key ); // Filter since 2.0.5.

	if ( $enable_sale ) {
		return true;
	}
	return false;
}
// wp_travel_get_pricing_min_id- pricing_id, category_id
function wp_travel_is_enable_sale_price( $trip_id, $enable_sale_for_min_price = false, $pricing_id = '', $category_id = '', $price_key = '' ) {
	if ( ! $trip_id ) {
		return false;
	}
	$pricing_option_type = get_post_meta( $trip_id, 'wp_travel_pricing_option_type', true );
	$enable_sale         = false;

	if ( $enable_sale_for_min_price ) {
		// get min price to check whether min price has sale enabled of not.
		$trip_price = wp_travel_get_price( $trip_id );
	}
	$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );
	if ( 'single-price' === $pricing_option_type ) {
		$enable_sale = get_post_meta( $trip_id, 'wp_travel_enable_sale', true ); // only for old user.
	} elseif ( 'multiple-price' === $pricing_option_type ) {

		if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {

			if ( ! empty( $pricing_id ) ) {
				$pricing_option = isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();

				if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0-below legacy version
					if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
						foreach ( $pricing_options as $pricing_key => $option ) {
							if ( isset( $option['enable_sale'] ) && 'yes' === $option['enable_sale'] ) {
								$enable_sale = true;
								break;
							}
						}
					}
				} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
					if ( ! empty( $category_id ) ) {
						$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
					} else {

						foreach ( $pricing_option['categories'] as $category_id => $category_option ) {

						}
					}
				}
			} else {

				foreach ( $pricing_options as $pricing_id => $pricing_option ) {

					if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0-below legacy version.
						if ( $price_key && ! empty( $price_key ) ) { // checks in indivicual pricing key [specific pricing is enabled in trip].
							if ( isset( $pricing_options[ $price_key ]['enable_sale'] ) && 'yes' === $pricing_options[ $price_key ]['enable_sale'] ) {
								$enable_sale = true;
							}
						} else { // Checks as a whole. if any pricing is enabled then return true.
							if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
								foreach ( $pricing_options as $pricing_key => $option ) {
									if ( isset( $option['enable_sale'] ) && 'yes' === $option['enable_sale'] ) {
										if ( $enable_sale_for_min_price ) {
											$sale_price = apply_filters( 'wp_travel_price', $option['sale_price'] );
											if ( $sale_price === $trip_price ) {
												$enable_sale = true;
												break;
											}
										} else {
											$enable_sale = true;
											break;
										}
									}
								}
							}
						}
					} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
						if ( ! empty( $category_id ) ) {
							$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
						} else {
							$min_catetory_id = '';
							$catetory_id_max_price = '';
							foreach ( $pricing_option['categories'] as $category_option ) {
								$pricing_enable_sale = isset( $category_option['enable_sale'] ) ? $category_option['enable_sale'] : 'no';
								if ( 'yes' === $pricing_enable_sale ) {
									if ( $enable_sale_for_min_price ) {
										$sale_price = apply_filters( 'wp_travel_price', $category_option['sale_price'] );
										if ( $sale_price === $trip_price ) {
											$enable_sale = true;
											break;
										}
									} else {
										$enable_sale = true;
										break;
									}
								}
							}
						}
					}
				}
			}
		}
	}

	$enable_sale = apply_filters( 'wp_travel_enable_sale', $enable_sale, $trip_id, $pricing_options, $price_key ); // Filter since 2.0.5.

	if ( $enable_sale ) {
		return true;
	}
	return false;
}


/**
 * WP Travel Trip is trip type enable.
 *
 * @return bool
 */
function wp_travel_is_trip_price_tax_enabled() {

	$settings = wp_travel_get_settings();

	if ( isset( $settings['trip_tax_enable'] ) && 'yes' == $settings['trip_tax_enable'] ) {

		return true;
	}

	return false;

}

/**
 * Wp Travel Process Trip Price Tax.
 *
 * @param int $post_id post id.
 * @return mixed $trip_price | $tax_details.
 */
function wp_travel_process_trip_price_tax( $post_id ) {

	if ( ! $post_id ) {
		return 0;
	}
	$settings = wp_travel_get_settings();

	$trip_price = wp_travel_get_actual_trip_price( $post_id );

	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {

		$tax_details         = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$trip_price          = wp_travel_get_actual_trip_price( $post_id );
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
			$tax_details['trip_price']        = $trip_price;
			$tax_details['tax_percentage']    = $tax_percentage;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2, '.', '' );

			return $tax_details;

		}
	}

	return array( 'trip_price' => $trip_price );

}

/**
 * Wp Travel Process Trip Price Tax.
 *
 * @param int $post_id post id.
 * @return mixed $trip_price | $tax_details.
 */
function wp_travel_process_trip_price_tax_by_price( $post_id, $price ) {

	if ( ! $post_id || ! $price ) {
		return 0;
	}
	$settings = wp_travel_get_settings();

	$trip_price = $price;

	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {

		$tax_details         = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$trip_price          = $price;
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
			$tax_details['trip_price']        = $trip_price;
			$tax_details['tax_percentage']    = $tax_percentage;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2, '.', '' );

			return $tax_details;

		}
	}

	return array( 'trip_price' => $trip_price );

}

function wp_travel_taxed_amount( $amount ) {

	$settings = wp_travel_get_settings();

	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {
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
function wp_travel_get_pricing_variation_dates( $post_id, $pricing_key ) {

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
function wp_travel_get_pricing_variation_price_per( $post_id, $pricing_key ) {

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
function wp_travel_booking_data( $booking_id ) {

	if ( ! $booking_id ) {
		return;
	}
	$trip_id = get_post_meta( $booking_id, 'wp_travel_post_id', true );

	$booking_status = get_post_meta( $booking_id, 'wp_travel_booking_status', true );
	$booking_status = ! empty( $booking_status ) ? $booking_status : 'N/A';

	$payment_ids       = get_post_meta( $booking_id, 'wp_travel_payment_id', true );
	$total_paid_amount = 0;
	$mode              = wp_travel_get_payment_mode();

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
	$taxed_trip_price = wp_travel_taxed_amount( $trip_price ); // Trip price including tax.
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

	$amounts = array(
		'booking_status' => $booking_status,
		'mode'           => $mode,
		'payment_mode'   => $payment_mode,
		'payment_status' => $payment_status,
		'sub_total'      => sprintf( '%0.2f', $sub_total ),
		'discount'       => sprintf( '%0.2f', $discount ),
		'tax'            => sprintf( '%0.2f', $tax ),
		'total'          => sprintf( '%0.2f', $total ),
		'paid_amount'    => sprintf( '%0.2f', $total_paid_amount ),
		'due_amount'     => sprintf( '%0.2f', $due_amount ),
	);

	// Partical calculation.
	if ( wp_travel_is_partial_payment_enabled() ) {
		$payout_percent = wp_travel_get_payout_percent( $trip_id );

		if ( $payout_percent > 0 ) {
			$trip_price_partial       = ( $trip_price * $payout_percent ) / 100;
			$trip_price_partial       = wp_travel_get_formated_price( $trip_price_partial );
			$taxed_trip_price_partial = wp_travel_taxed_amount( $trip_price_partial ); // Trip price including tax.
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
function wp_travel_get_payment_id( $booking_id ) {
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
 * Return Trip Price.
 *
 * @param  int $post_id Post id of the post.
 * @return int Trip Price.
 */
function wp_travel_get_trip_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_price = get_post_meta( $post_id, 'wp_travel_price', true );
	if ( $trip_price ) {
		return $trip_price;
	}
	return 0;
}

/**
 * Return Trip Sale Price.
 *
 * @param  int $post_id Post id of the post.
 * @return int Trip Price.
 */
function wp_travel_get_trip_sale_price( $post_id = 0 ) {
	if ( ! $post_id ) {
		return 0;
	}
	$trip_sale_price = get_post_meta( $post_id, 'wp_travel_sale_price', true );
	if ( $trip_sale_price ) {
		return $trip_sale_price;
	}
	return 0;
}

/**
 * Return Trip Price.
 *
 * @param int    $trip_id Post id of the post.
 * @param String $price_key Price key for multiple pricing.
 * @param Bool   $only_regular_price Return only trip price rather than sale price as trip price if this is set to true.
 *
 * @since 1.0.5 // Modified 1.9.2, 2.0.7
 * @return int Trip Price.
 */
function wp_travel_get_actual_trip_price( $trip_id = 0, $price_key = '', $only_regular_price = false ) {
	if ( ! $trip_id ) {
		return 0;
	}

	$trip_price  = wp_travel_get_trip_price( $trip_id );
	$enable_sale = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );
	if ( $enable_sale && ! $only_regular_price ) {
		$trip_price = wp_travel_get_trip_sale_price( $trip_id );
	}

	// @since 1.9.2 // Added price calculation for pricing key [multiple pricing].
	$enable_pricing_options = wp_travel_is_enable_pricing_options( $trip_id );
	$valid_price_key        = wp_travel_is_price_key_valid( $trip_id, $price_key );

	if ( '' !== $price_key && $enable_pricing_options && $valid_price_key ) {
		$pricing_data = wp_travel_get_pricing_variation( $trip_id, $price_key );
		if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

			foreach ( $pricing_data as $p_ky => $pricing ) :
				if ( isset( $pricing['price'] ) ) {

					$trip_price  = $pricing['price'];
					$enable_sale = isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'];
					$sale_price  = isset( $pricing['sale_price'] ) && $pricing['sale_price'] > 0 ? $pricing['sale_price'] : 0;

					if ( $enable_sale && $sale_price && ! $only_regular_price ) {
						$trip_price = $pricing['sale_price'];
					}
				}
			endforeach;
		}
	}
	return apply_filters( 'wp_travel_trip_price', $trip_price, $trip_id, $price_key, $only_regular_price );
}
// End of Migrated functions from inc/helpers.php / These prices are only for display.

/**
 * Get Cart Attrs [  Need Enhancement ]
 */
// function wp_travel_get_cart_attrs( $trip_id, $pax = 0, $price_key = '', $return_price = false, $pricing_id = null ) {
// 	if ( ! $trip_id ) {
// 		return 0;
// 	}

// 	$attrs = [
// 		'pax_label'       => '', // what is pax_label?
// 		'max_available'   => '',
// 		'min_available'   => '',
// 		'trip_start_date' => '',
// 		'arrival_date'    => '',
// 		'departure_date'  => '',
// 		'trip_extras'     => '',
// 		'currency'        => '',
// 		'enable_partial'  => '',
// 		'trip'            => [],
// 	];

// 	// Default Pricings.
// 	$trip_price = wp_travel_get_actual_trip_price( $trip_id, $price_key ); // Default Trip Price.

// 	if ( function_exists( 'wp_travel_group_discount_price' ) ) { // From Group Discount addons.
// 		$group_trip_price = wp_travel_group_discount_price( $trip_id, $price_key, $pax );
// 		if ( $group_trip_price ) {
// 			$trip_price = $group_trip_price;
// 		}
// 	}

// 	$per_person_text        = wp_travel_get_price_per_text( $trip_id );
// 	$enable_pricing_options = wp_travel_is_enable_pricing_options( $trip_id );

// 	$pax_label = ! empty( $per_person_text ) ? $per_person_text : __( 'Person', 'wp-travel' );

// 	if ( '' != $price_key && $enable_pricing_options ) {
// 		$valid_price_key = wp_travel_is_price_key_valid( $trip_id, $price_key );

// 		if ( $valid_price_key && $enable_pricing_options ) {

// 			$pricing_data = wp_travel_get_pricing_variation( $trip_id, $price_key );

// 			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

// 				foreach ( $pricing_data as $p_ky => $pricing ) :
// 					// Product Metas.
// 					$trip_start_date       = isset( $_REQUEST['arrival_date'] ) && '' !== $_REQUEST['arrival_date'] ? $_REQUEST['arrival_date'] : '';
// 					$pricing_default_types = wp_travel_get_pricing_variation_options();
// 					$pax_label             = isset( $pricing['type'] ) && 'custom' === $pricing['type'] && '' !== $pricing['custom_label'] ? $pricing['custom_label'] : $pricing_default_types[ $pricing['type'] ];
// 					$max_available         = isset( $pricing['max_pax'] ) && '' !== $pricing['max_pax'] ? true : false;
// 					$min_available         = isset( $pricing['min_pax'] ) && '' !== $pricing['min_pax'] ? true : false;

// 					if ( $max_available ) {
// 						$max_available = $pricing['max_pax'];
// 						// $max_attr = 'max=' . $pricing['max_pax'];
// 					}
// 					if ( $min_available ) {
// 						$min_available = $pricing['min_pax'];
// 					}
// 				endforeach;
// 			}
// 		}
// 	} else {
// 		// Product Metas.
// 		$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
// 		$max_available   = get_post_meta( $trip_id, 'wp_travel_group_size', true );
// 		$min_available   = 1;
// 	}

// 	if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {

// 		$inventory = new WP_Travel_Util_Inventory();

// 		$inventory_enabled = $inventory->is_inventory_enabled( $trip_id );
// 		$available_pax     = $inventory->get_available_pax( $trip_id, $price_key, $trip_start_date );

// 		if ( $inventory_enabled && $available_pax ) {
// 			$max_available = $available_pax;
// 		}
// 	}

// 	$trip_price = wp_travel_get_formated_price( $trip_price );

// 	if ( $return_price ) {
// 		return $trip_price;
// 	}

// 	$attrs = array(
// 		'pax_label'       => $pax_label,
// 		'max_available'   => $max_available,
// 		'min_available'   => $min_available,
// 		'trip_start_date' => $trip_start_date,
// 		'arrival_date'    => '',
// 		'departure_date'  => '',
// 		'trip_extras'     => '',
// 		'currency'        => wp_travel_get_currency_symbol(), // added in 1.8.4
// 	);

// 	$attrs['enable_partial'] = wp_travel_is_partial_payment_enabled();

// 	// $trip_price_partial = $trip_price;
// 	// if ( $attrs['enable_partial'] ) {
// 	// 	$payout_percent                 = wp_travel_get_payout_percent( $trip_id );
// 	// 	$attrs['partial_payout_figure'] = $payout_percent; // added in 1.8.4

// 	// 	if ( $payout_percent > 0 ) {
// 	// 		$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
// 	// 		$trip_price_partial = wp_travel_get_formated_price( $trip_price_partial );
// 	// 	}
// 	// 	$attrs['trip_price_partial'] = $trip_price_partial;
// 	// }

// 	return $attrs;

// }

/**
 * Modified version of previous `wp_travel_get_cart_attrs` function.
 *
 * @param [type] $trip_id
 * @param integer $pax
 * @param string $price_key
 * @param boolean $return_price
 * @return void
 */
function wp_travel_get_cart_attrs( $trip_id, $pax = 0, $price_key = '', $return_price = false ) {
	if ( ! $trip_id ) {
		return 0;
	}

	// $attrs = array(
	// 	'pax_label'       => '', // what is pax_label?
	// 	'max_available'   => '',
	// 	'min_available'   => '',
	// 	'trip_start_date' => '',
	// 	'currency'        => '',
	// 	'enable_partial'  => '',
	// );

	// Default Pricings.
	// $trip_price = wp_travel_get_actual_trip_price( $trip_id, $price_key ); // Default Trip Price.

	// if ( function_exists( 'wp_travel_group_discount_price' ) ) { // From Group Discount addons.
	// 	$group_trip_price = wp_travel_group_discount_price( $trip_id, $price_key, $pax );
	// 	if ( $group_trip_price ) {
	// 		$trip_price = $group_trip_price;
	// 	}
	// }

	// $per_person_text        = wp_travel_get_price_per_text( $trip_id );
	$enable_pricing_options = wp_travel_is_enable_pricing_options( $trip_id );

	// $pax_label = ! empty( $per_person_text ) ? $per_person_text : __( 'Person', 'wp-travel' );

	if ( ! empty( $price_key ) && $enable_pricing_options ) {
		$valid_price_key = wp_travel_is_price_key_valid( $trip_id, $price_key );

		if ( $valid_price_key ) {

			$pricing_data = wp_travel_get_pricing_variation( $trip_id, $price_key );

			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

				foreach ( $pricing_data as $p_ky => $pricing ) :
					// Product Metas.
					$trip_start_date       = isset( $_REQUEST['arrival_date'] ) && '' !== $_REQUEST['arrival_date'] ? $_REQUEST['arrival_date'] : '';
					$pricing_default_types = wp_travel_get_pricing_variation_options();
					// $pax_label             = isset( $pricing['type'] ) && 'custom' === $pricing['type'] && '' !== $pricing['custom_label'] ? $pricing['custom_label'] : $pricing_default_types[ $pricing['type'] ];
					$max_available         = isset( $pricing['max_pax'] ) && '' !== $pricing['max_pax'] ? $pricing['max_pax'] : '';
					$min_available         = isset( $pricing['min_pax'] ) && '' !== $pricing['min_pax'] ? $pricing['min_pax'] : '';

					// if ( $max_available ) {
					// 	$max_available = $pricing['max_pax'];
					// 	// $max_attr = 'max=' . $pricing['max_pax'];
					// }
					// if ( $min_available ) {
					// 	$min_available = $pricing['min_pax'];
					// }
				endforeach;
			}
		}
	} else {
		// Product Metas.
		$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
		$max_available   = get_post_meta( $trip_id, 'wp_travel_group_size', true );
		$min_available   = 1;
	}

	if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {

		$inventory = new WP_Travel_Util_Inventory();

		$inventory_enabled = $inventory->is_inventory_enabled( $trip_id );
		$available_pax     = $inventory->get_available_pax( $trip_id, $price_key, $trip_start_date );

		if ( $inventory_enabled && $available_pax ) {
			$max_available = $available_pax;
		}
	}

	// $trip_price = wp_travel_get_formated_price( $trip_price );

	// if ( $return_price ) {
	// 	return $trip_price;
	// }

	$attrs = array(
		// 'pax_label'       => $pax_label,
		'max_available'   => $max_available,
		'min_available'   => $min_available,
		'trip_start_date' => $trip_start_date,
		// 'arrival_date'    => '',
		// 'departure_date'  => '',
		// 'trip_extras'     => '',
		'currency'        => wp_travel_get_currency_symbol(), // added in 1.8.4
	);
	// $attrs = array(
	// 	// 'pax_label'       => '', // what is pax_label?
	// 	'max_available'   => $max_available,
	// 	'min_available'   => $min_available,
	// 	'trip_start_date' => '',
	// 	'currency'        => '',
	// 	'enable_partial'  => '',
	// );

	// $attrs['enable_partial'] = wp_travel_is_partial_payment_enabled();

	// $trip_price_partial = $trip_price;
	// if ( $attrs['enable_partial'] ) {
	// 	$payout_percent                 = wp_travel_get_payout_percent( $trip_id );
	// 	$attrs['partial_payout_figure'] = $payout_percent; // added in 1.8.4

	// 	if ( $payout_percent > 0 ) {
	// 		$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
	// 		$trip_price_partial = wp_travel_get_formated_price( $trip_price_partial );
	// 	}
	// 	$attrs['trip_price_partial'] = $trip_price_partial;
	// }

	return $attrs;

}

function wp_travel_get_partial_trip_price( $trip_id, $price_key = null ) {

	$trip_price = wp_travel_get_actual_trip_price( $trip_id, $price_key );

	if ( wp_travel_is_partial_payment_enabled() ) {
		$payout_percent = wp_travel_get_payout_percent( $trip_id );
		if ( $payout_percent > 0 ) {
			$trip_price = ( $trip_price * $payout_percent ) / 100;
			$trip_price = wp_travel_get_formated_price( $trip_price );
		}
	}

	return $trip_price;
}

/**
 * Validate pricing Key
 *
 * @return bool true | false.
 */
function wp_travel_is_price_key_valid( $trip_id, $price_key ) {

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

function wp_travel_is_enable_pricing_options( $trip_id ) {
	return isset( $trip_id ) && 'multiple-price' === wp_travel_get_pricing_option_type( $trip_id );
}

/**
 * Return Min price key for the trip
 *
 * @param Mixed $options pricing_option | trip id.
 *
 * @since 2.0.0, 2.0.5 [Introduced in 2.0.0 or earlier, modified in 2.0,5 ] not required @since 3.0.0.
 * @return Mixed.
 */

function wp_travel_get_min_price_key( $options ) {
	if ( ! $options ) {
		return;
	}

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
	return apply_filters( 'wp_travel_min_price_key', $price_key, $pricing_options ); // Filter @since 2.0.3.
}

/**
 * Used For Calculation purpose. for display purpose use wp_travel_get_formated_price_currency.
 */
function wp_travel_get_formated_price( $price, $number_of_decimals = 2 ) {
	if ( ! $price ) {
		return 0;
	}
	$settings           = wp_travel_get_settings();
	$thousand_separator = '';
	$decimal_separator  = $settings['decimal_separator'];
	$number_of_decimals = isset( $settings['number_of_decimals'] ) && ! empty( $settings['number_of_decimals'] ) ? $settings['number_of_decimals'] : 0;
	return number_format( $price, $number_of_decimals, $decimal_separator, $thousand_separator );
}

/**
 * Currency position with price
 *
 * @param Number  $price         Price.
 * @param Boolean $regular_price Is price regular or sale price.
 * @param String  $price_key     Price key of the price. It is only required for some customization.
 *
 * @since 2.0.1 / 2.0.3
 *
 * @return Mixed
 */
function wp_travel_get_formated_price_currency( $price, $regular_price = false, $price_key = '' ) {
	$settings          = wp_travel_get_settings();
	$currency_position = isset( $settings['currency_position'] ) ? $settings['currency_position'] : 'left';

	$filter_name     = 'wp_travel_itinerary_sale_price'; // Filter for customization work support.
	$price_class     = 'wp-travel-trip-price-figure';
	$currency_symbol = apply_filters( 'wp_travel_display_currency_symbol', wp_travel_get_currency_symbol() );
	if ( $regular_price ) {
		$filter_name = 'wp_travel_itinerary_price';
		$price_class = 'wp-travel-regular-price-figure';
	}

	// Price Format Start.
	$thousand_separator = $settings['thousand_separator'];
	$decimal_separator  = $settings['decimal_separator'];
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

function wp_travel_is_taxable() {

	$settings        = wp_travel_get_settings();
	$trip_tax_enable = isset( $settings['trip_tax_enable'] ) ? $settings['trip_tax_enable'] : 'no';

	if ( 'yes' == $trip_tax_enable ) {
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$tax_percentage      = isset( $settings['trip_tax_percentage'] ) ? $settings['trip_tax_percentage'] : '';

		if ( '' == $tax_percentage ) {
			return false;
		}
		if ( 'yes' == $tax_inclusive_price ) {
			return false;
		}
		return $tax_percentage;
	}
	return false;
}

/**
 * Get Pricing option type[single-pricing || multiple-pricing].
 *
 * @param   int $post_id Post ID.
 *
 * @since   1.7.6
 * @return String Pricing option type.
 */
function wp_travel_get_pricing_option_type( $post_id = null ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	// need to remove in future. [replaced this with 'wp_travel_pricing_option_type' meta]. @since 1.7.6
	$enable_pricing_options = get_post_meta( $post_id, 'wp_travel_enable_pricing_options', true );

	$pricing_option_type = get_post_meta( $post_id, 'wp_travel_pricing_option_type', true );
	if ( ! $pricing_option_type ) {
		$pricing_option_type = isset( $enable_pricing_options ) && 'yes' === $enable_pricing_options ? 'multiple-price' : 'single-price';
	}
	return $pricing_option_type;
}

function wp_travel_get_payment_modes() {
	$modes = array(
		'partial' => esc_html__( 'Partial Payment', 'wp-travel' ),
		'full'    => esc_html__( 'Full Payment', 'wp-travel' ),
	);
	return apply_filters( 'wp_travel_payment_modes', $modes );
}

/**
 * Get Price of trip. Price key is only for old data less than WP Travel @since 3.0.0-below legacy version
 */
function wp_travel_get_price( $trip_id, $return_regular_price = false, $pricing_id = '', $category_id = '', $price_key = '' ) {

	if ( ! $trip_id ) {
		return;
	}

	$price = 0;

	$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

	if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
		// Specific listing as per pricing_id to get price.
		if ( ! empty( $pricing_id ) ) {
			$pricing_option = isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();
			if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0-below legacy version
				if ( ! $price_key && $pricing_id === $category_id ) { // By default we set category_id = pricing_id due to no category_id in listing of legacy version.
					$price_key = isset( $pricing_options[ $pricing_id ]['price_key'] ) ? $pricing_options[ $pricing_id ]['price_key'] : '';
				}

				$price = wp_travel_get_actual_trip_price( $trip_id, $price_key, $return_regular_price );
			} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
				if ( ! empty( $category_id ) ) {

					$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
					$price  = $category_option['price'];
					$enable_sale = isset( $category_option['enable_sale'] ) && 'yes' === $category_option['enable_sale'] ? true : false;
					$sale_price  = isset( $category_option['sale_price'] ) && $category_option['sale_price'] > 0 ? $category_option['sale_price'] : 0;

					if ( $enable_sale && $sale_price && ! $return_regular_price ) {
						$price = $category_option['sale_price'];
					}
				} else {

					foreach ( $pricing_option['categories'] as $category_id => $category_option ) {

					}
				}
			}
		} else {
			$min_keys = wp_travel_get_min_pricing_id( $trip_id );
			$pricing_id = isset( $min_keys[ 'pricing_id' ] ) ?  $min_keys[ 'pricing_id' ] : '';
			$pricing_option = ! empty( $pricing_id ) && isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();


			if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0-below legacy version

				if ( isset( $pricing_option['price'] ) ) { // old pricing option.
					$current_price = $pricing_option['price'];
					$enable_sale   = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
					$sale_price    = isset( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : 0;

					if ( 'yes' === $enable_sale && $sale_price > 0 ) {
						$current_price = $sale_price;
					}

					if ( ( 0 === $price && $current_price > 0 ) || $price > $current_price ) { // Initialize min price if 0.
						$price = $current_price;
					}
				}
			} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
				if ( ! empty( $category_id ) ) {
					$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
				} else {

					$min_catetory_id = '';
					$catetory_id_max_price = '';
					foreach ( $pricing_option['categories'] as $category_id => $category_option ) {

						$current_price = $category_option['price'];
						$enable_sale   = isset( $category_option['enable_sale'] ) ? $category_option['enable_sale'] : 'no';
						$sale_price    = isset( $category_option['sale_price'] ) ? $category_option['sale_price'] : 0;

						if ( 'yes' === $enable_sale && $sale_price > 0 ) {
							$current_price = $sale_price;
						}

						if ( ( 0 === $price && $current_price > 0 ) || $price > $current_price ) { // Initialize min price if 0.
							$price                 = $current_price;
							$min_catetory_id = $category_id; // Add min price category id.
						}
					}

					// Return regular price.
					if ( $return_regular_price && ! empty( $min_catetory_id ) && isset( $pricing_option['categories'][ $min_catetory_id ]['price'] ) ) {
						$price = $pricing_option['categories'][ $min_catetory_id ]['price'];
					}
				}
			}
		}
	} else { // Default single pricing price
		$price = wp_travel_get_actual_trip_price( $trip_id, '', $return_regular_price );
	}

	$price = apply_filters( 'wp_travel_price', $price );

	return $price;
}

function wp_travel_get_min_pricing_id( $trip_id ) {
	if ( ! $trip_id ) {
		return;
	}

	$price = 0;
	$min_pricing_id = '';
	$min_category_id = '';

	$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

	// All listings to get min price.
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
						$price = $current_price;
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
					$price                 = $current_price;
					$min_catetory_id = $category_id; // Add min price category id.
					$min_pricing_id = $pricing_id;
				}
			}

			// Return regular price.
			// if ( $return_regular_price && ! empty( $min_catetory_id ) && isset( $pricing_option['categories'][ $min_catetory_id ]['price'] ) ) {
			// 	$price = $pricing_option['categories'][ $min_catetory_id ]['price'];
			// }
		}
	}
	return array( 'pricing_id' => $min_pricing_id, 'category_id' => $min_category_id  );
}
