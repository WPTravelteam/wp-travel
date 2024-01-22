<?php
/**
 * Helpers cart.
 *
 * @package WP_Travel
 */

/**
 * WP_Travel_Helpers_Cart class.
 */
class WP_Travel_Helpers_Cart {
	public static function get_cart() {
		$cart_items = self::get_cart_items();
		if ( is_wp_error( $cart_items ) || 'WP_TRAVEL_CART_ITEMS' !== $cart_items['code'] ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_EMPTY_CART' );
		}

		$cart_total         = 0;
		$cart_total_regular = 0;
		foreach ( $cart_items['cart_items'] as $cart_id => $item ) {
			$cart_total         = $item['trip_price'] + $cart_total;
			$cart_total_regular = $item['trip_price_regular'] + $cart_total_regular;
		}
		$is_coupon_applied = false;

		if ( isset( $cart_items['discount']['type'] ) ) {
			if ( 'percentage' === $cart_items['discount']['type'] ) {
				$cart_total = isset( $cart_items['discount']['value'] ) ? $cart_total * ( 100 - (float) $cart_items['discount']['value'] ) / 100 : $cart_total;
			} elseif ( isset( $cart_items['discount']['value'] ) ) {
				$discount_amount = WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $cart_items['discount']['value'] );
				$cart_total      = $cart_total - (float) $discount_amount;
			}
			$is_coupon_applied = true;
		}
		$tax_amount = 0;
		$tax_rate   = WP_Travel_Helpers_Trips::get_tax_rate();
		if ( $tax_rate ) {
			$tax_amount = ( $cart_total * (float) $tax_rate ) / 100;
			$cart_total = $cart_total - $tax_amount;
			// $tax_amount_partial = ( $total_trip_price_partial_after_dis * $tax_rate ) / 100;
		}

		return array(
			'code' => 'WP_TRAVEL_CART',
			'cart' => array(
				'cart_items'         => $cart_items['cart_items'],
				'total'              => $cart_items['total'],
				'cart_total'         => (float) number_format( $cart_total, 2, '.', '' ), // Total cart price after discount and tax.
				'cart_total_regular' => (float) number_format( $cart_total_regular, 2, '.', '' ),
				'coupon_applied'     => $is_coupon_applied, // Coupon Implementation.
				'coupon'             => count( $cart_items['discount'] ) > 0 ? $cart_items['discount'] : array(),
				'tax'                => $tax_rate,
				'version'            => '1',
				// 'currency' =>
			),
		);
	}

	public static function get_cart_items() {
		global $wt_cart;
		$cart_items = $wt_cart->getItems();
		if ( empty( $cart_items ) ) {
			$wt_cart->clear(); // Added to remove coupon discount when no items.
			return new WP_Error( 'WP_TRAVEL_EMPTY_CART', __( 'Cart is empty.', 'wp-travel' ) );
		}
		$date_format = get_option( 'date_format' );
		// Section to apply category group disocunt.
		$category_discount_data = array();  // To get Discount as per trip id.
		$cart_trip_count        = array(); // To calculate Total no of trips as per trip id.

		// Start Discount Implementation
		$terms    = array();
		$discount = apply_filters( 'wp_travel_trip_discounts', array(), $cart_items );

		// End Discount Implementation
		$cart = array();
		foreach ( $cart_items as $cart_id => $item ) {
			$trip_data     = WP_Travel_Helpers_Trips::get_trip( absint( $item['trip_id'] ) );
			$is_item_valid = true;
			if ( ! is_wp_error( $trip_data ) && 'WP_TRAVEL_TRIP_INFO' === $trip_data['code'] ) {

				// Temporary fixes for multiple currency.
				$temp_pricings = $trip_data['trip']['pricings'];
				foreach ( $temp_pricings as $temp_pricing_index => $temp_pricing ) {
					// Group Price.
					if ( $temp_pricing['has_group_price'] && count( $temp_pricing['group_prices'] ) > 0 ) {
						foreach ( $temp_pricing['group_prices'] as $gpi => $gp ) {
							$group_price = $gp['price'];
							// $trip_data['trip']['pricings']['group_prices'][ $gpi ]['price'] = $group_price;
						}
					}
					foreach ( $temp_pricing['categories'] as $temp_pricing_cat_index => $temp_pricing_cat ) {
						$temp_regular_price = $temp_pricing_cat['regular_price'];
						$temp_sale_price    = $temp_pricing_cat['sale_price'];

						$trip_data['trip']['pricings'][ $temp_pricing_index ]['categories'][ $temp_pricing_cat_index ]['regular_price'] = $temp_regular_price;
						$trip_data['trip']['pricings'][ $temp_pricing_index ]['categories'][ $temp_pricing_cat_index ]['sale_price']    = $temp_sale_price;

						// Group Price.
						if ( $temp_pricing_cat['has_group_price'] && count( $temp_pricing_cat['group_prices'] ) > 0 ) {
							foreach ( $temp_pricing_cat['group_prices'] as $gpi => $gp ) {
								$group_price2 = $gp['price'];
								$trip_data['trip']['pricings'][ $temp_pricing_index ]['categories'][ $temp_pricing_cat_index ]['group_prices'][ $gpi ]['price'] = $group_price2;
							}
						}
					}
				}
				// End of Temporary fixes for multiple currency.

				$trip_items = ! empty( $item['trip'] ) ? $item['trip'] : array();
				if ( is_array( $trip_items ) && count( $trip_items ) > 0 ) {
					foreach ( $trip_items as $cat_id => $cat_value ) {
						unset( $item['trip'][ $cat_id ]['price'], $item['trip'][ $cat_id ]['price_partial'], $item['trip'][ $cat_id ]['custom_label'] );
					}
				}
				$cart[ $cart_id ]['trip_id']    = $item['trip_id']; // To loop cart items with trip id. like in discount.
				$cart[ $cart_id ]['pricing_id'] = $item['pricing_id'];
				$cart[ $cart_id ]['price_key']  = $item['price_key'];
				$cart[ $cart_id ]['trip_price'] = (float) number_format( $item['trip_price'], 2, '.', '' );

				// Calculation of individual trip total along with extras.
				$cart[ $cart_id ]['trip_total']         = $wt_cart->get_item_total( $cart_id ); // Gross individual trip total including extras. It helps to apply discount.
				$cart[ $cart_id ]['trip_total_partial'] = $wt_cart->get_item_total( $cart_id, true ); // Gross individual trip total including extras. It helps to apply discount.
				$cart[ $cart_id ]['payout_percent']     = WP_Travel_Helpers_Pricings::get_payout_percent( $item['trip_id'] ); // Gross individual trip total including extras. It helps to apply discount.

				if ( isset( $item['discount'] ) ) {
					$cart[ $cart_id ]['discount'] = $item['discount']; // Discount amount applied to individual trip total.
				}

				if ( ! empty( $discount ) && isset( $discount['coupon'] ) && ! $discount['coupon'] ) {
					$trip_price = (float) $item['trip_price'] * ( 100 - (float) $discount['value'] ) / 100;
					// if ( ! empty( $discount[0]['is_percent_discount'] ) && 'yes' === $discount[0]['is_percent_discount'] ) {
					// } else {
					// $trip_price = (float) $item['trip_price'] - (float) $discount[0]['discount_figure'];
					// }
					// $cart[ $cart_id ]['trip_price']         = $item['trip_price'];
					$cart[ $cart_id ]['trip_price'] = number_format( $trip_price, 2, '.', '' );
				}

				$cart[ $cart_id ]['trip_price_regular'] = (float) number_format( $item['trip_price'], 2, '.', '' );
				$cart[ $cart_id ]['extras']             = $item['trip_extras'];
				$cart[ $cart_id ]['trip']               = $item['trip'];
				$cart[ $cart_id ]['trip_data']          = $trip_data['trip'];
				$cart[ $cart_id ]['arrival_date']       = wptravel_format_date( $item['arrival_date'] );
				$cart[ $cart_id ]['date_id']       		= $item['date_id'];
				if ( isset( $item['trip_time'] ) ) {
					$cart[ $cart_id ]['trip_time'] = $item['trip_time'];
				}
			} else {
				self::remove_cart_item( $cart_id );
			}
		}

		// TODO:REMOve Later
		// $cart['terms']         = $terms;
		// $cart['common']        = $common_terms;
		// $cart['discount']      = $discount;
		// $cart['discount_info'] = $discount_info;
		return array(
			'code'       => 'WP_TRAVEL_CART_ITEMS',
			'cart_items' => $cart,
			'total'      => $wt_cart->get_total(),
			'discount'   => $wt_cart->get_discounts(), // Coupon Implementation.
		);
	}

	public static function add_to_cart( $postData = array() ) {
		if ( empty( $postData['trip_id'] ) ) {
			return new WP_Error( 'WP_TRAVEL_NO_TRIP_ID', __( 'Invalid trip id.', 'wp-travel' ) );
		}

		// START Temporary solution
		ob_start();
		$WP_Travel_Ajax = new WP_Travel_Ajax();
		$WP_Travel_Ajax->add_to_cart();
		$res = ob_get_contents();
		ob_end_clean();
		// END Temporary solution
		if ( $res ) { // add to cart success.
			$cart      = self::get_cart();
			$cart_data = array();
			if ( ! is_wp_error( $cart ) && 'WP_TRAVEL_CART' === $cart['code'] ) {
				$cart_data = $cart['cart'];
			}
			return WP_Travel_Helpers_Response_Codes::get_success_response(
				'WP_TRAVEL_ADDED_TO_CART',
				array(
					'cart' => $cart_data,
				)
			);
		}

		return new WP_Error( 'WP_TRAVEL_CART_ITEM_NOT_ADDED', __( 'Cart item not Added.', 'wp-travel' ) );
	}

	public static function remove_cart_item( $cart_id = false ) {
		if ( empty( $cart_id ) ) {
			return new WP_Error( 'WP_TRAVEL_NO_CART_ID', __( 'Invalid cart id.', 'wp-travel' ) );
		}
		global $wt_cart;
		$wt_cart->remove( $cart_id );
		$cart = self::get_cart();
		return array(
			'code'    => 'WP_TRAVEL_REMOVED_CART_ITEM',
			'message' => __( 'Trip removed from cart.', 'wp-travel' ),
			'cart'    => ( ! is_wp_error( $cart ) && 'WP_TRAVEL_CART' === $cart['code'] ) ? $cart['cart'] : array(),
		);
	}

	public static function update_cart_item( $cart_id = false, $itemData = array() ) {
		if ( empty( $cart_id ) ) {
			return new WP_Error( 'WP_TRAVEL_NO_CART_ID', __( 'Invalid cart id.', 'wp-travel' ) );
		}
		global $wt_cart;
		$pax         = isset( $itemData['pax'] ) ? $itemData['pax'] : array();
		$trip_extras = isset( $itemData['wp_travel_trip_extras'] ) ? (array) $itemData['wp_travel_trip_extras'] : array();
		$response    = $wt_cart->update( $cart_id, $pax, $trip_extras, $itemData );
		if ( ! $response ) {
			return new WP_Error( 'WP_TRAVEL_CART_ITEM_NOT_UPDATED', __( 'Cart item not updated.', 'wp-travel' ) );
		}
		$cart_data = self::get_cart();
		$cart      = is_array( $cart_data ) && 'WP_TRAVEL_CART' === $cart_data['code'] ? $cart_data['cart'] : array();
		return array(
			'code'    => 'WP_TRAVEL_CART_ITEM_UPDATED',
			'message' => __( 'Cart item updated.', 'wp-travel' ),
			'cart'    => $cart,
		);
	}

	public static function apply_coupon_code( $coupon_code ) {

		$payload = json_decode( file_get_contents( 'php://input' ) );
		$payload = is_object( $payload ) ? (array) $payload : array();
		if ( empty( $coupon_code ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_EMPTY_COUPON' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		if ( ! is_string( $coupon_code ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_COUPON' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$coupon_id = WPTravel()->coupon->get_coupon_id_by_code( $coupon_code ); // Gets Coupon Code if Exists.
		if ( $coupon_id ) {
			if ( ! WPTravel()->coupon->is_coupon_valid( $coupon_id ) ) {
				$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_COUPON_DATE' );
				WP_Travel_Helpers_REST_API::response( $error );
			}

			if ( WPTravel()->coupon->is_limit_exceed( $coupon_id ) ) {
				$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_COUPON_LIMIT_EXCEED' );
				WP_Travel_Helpers_REST_API::response( $error );
			}

			if ( ! WPTravel()->coupon->valid_for_user( $coupon_id ) ) {
				$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_COUPON_NOT_ALLOWED_FOR_USER' );
				WP_Travel_Helpers_REST_API::response( $error );
			}

			$cart = self::get_cart();
			// Prepare Coupon Application.
			if ( is_wp_error( $cart ) ) {
				return $cart;
			}
			$items = $cart['cart']['cart_items'];

			$discount_applicable_total = WPTravel()->coupon->get_discount_applicable_total( $coupon_id );
			if ( ! $discount_applicable_total ) {
				$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_COUPON_NOT_ALLOWED_FOR_TRIP' );
				WP_Travel_Helpers_REST_API::response( $error );
			}

			$discount_type  = WPTravel()->coupon->get_discount_type( $coupon_id );
			$discount_value = WPTravel()->coupon->get_discount_value( $coupon_id );
			if ( 'fixed' === $discount_type ) {
				if ( $discount_value > $discount_applicable_total ) {
					// Error related to fixed discount amount is higher than trip amount.
					$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_COUPON_DISCOUNT_AMOUNT_HIGH' );
					WP_Travel_Helpers_REST_API::response( $error );

				} elseif ( $discount_value === $discount_applicable_total ) {
					$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_COUPON_DISCOUNT_AMOUNT_EQUAL_TO_TRIP_AMOUNT' );
					WP_Travel_Helpers_REST_API::response( $error );
				}
			}
			
			global $wt_cart;
			$wt_cart->add_discount_values( $coupon_id, $discount_type, $discount_value, $coupon_code );
			$cart = self::get_cart(); // 2nd assignment after deducting discount.

			return array(
				'code'    => 'WP_TRAVEL_COUPON_APPLIED',
				'message' => __( 'Discount coupon code applied successfully.', 'wp-travel' ),
				'cart'    => $cart['cart'],
			);
		} else {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_COUPON' );
		}
	}

	/**
	 * Return true if cart page is enabled. Fucntion is used to bypass cart page if disabled while doing add to cart.
	 *
	 * @since 4.3.2
	 */
	public static function is_enabled_cart_page() {
		$enabled  = false;
		$settings = wptravel_get_settings();

		$skip_cart_page_booking = isset( $settings['skip_cart_page_booking'] ) && ! empty( $settings['skip_cart_page_booking'] ) ? $settings['skip_cart_page_booking'] : 'no';
		if ( 'yes' !== $skip_cart_page_booking ) {
			$enabled = true;
		}
		return apply_filters( 'wp_travel_filter_is_enabled_cart_page', $enabled, $settings );
	}
}
