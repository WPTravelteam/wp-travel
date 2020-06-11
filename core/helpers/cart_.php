<?php
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
		return array(
			'code' => 'WP_TRAVEL_CART',
			'cart' => array(
				'cart_items'         => $cart_items['cart_items'],
				'cart_total'         => (float) number_format( $cart_total, 2, '.', '' ),
				'cart_total_regular' => (float) number_format( $cart_total_regular, 2, '.', '' ),
				'version'            => '1',
				// 'currency' =>
			),
		);
	}

	public static function get_cart_items() {
		global $wt_cart;
		$cart_items = $wt_cart->getItems();
		if ( empty( $cart_items ) ) {
			return new WP_Error( 'WP_TRAVEL_EMPTY_CART', __( 'Cart is empty.', 'wp-travel' ) );
		}
		$date_format = get_option( 'date_format' );
		// Section to apply category group disocunt.
		$category_discount_data = array();  // To get Discount as per trip id.
		$cart_trip_count        = array(); // To calculate Total no of trips as per trip id.

		// Start Discount Implementation
		$terms = array();
		if ( count( $cart_items ) > 1 ) :
			$common_terms = array();
			foreach ( $cart_items as $cart_id => $item ) {
				$cart_trip_count[ $item['trip_id'] ] = ! isset( $cart_trip_count[ $item['trip_id'] ] ) ? 1 : ( $cart_trip_count[ $item['trip_id'] ] + 1 );
				$taxonomy                            = 'itinerary_types';
				// $primary_term = get_post_meta( $item['trip_id'], "_primary_term_{$taxonomy" );
				$trip_terms = get_the_terms( $item['trip_id'], 'itinerary_types' );
				if ( is_array( $trip_terms ) ) :
					// $terms[ $item['trip_id'] ] = array_map(
					// function( $sterm ) {
					// return $sterm->term_id;
					// },
					// $trip_terms
					// );
					$terms[] = array_map(
						function( $sterm ) {
							return $sterm->term_id;
						},
						$trip_terms
					);
					// if ( empty( $common_terms ) ) {
					// 	$common_terms = $terms[ $item['trip_id'] ];
					// } else {
					// 	$common_terms = array_intersect( $common_terms, $terms[ $item['trip_id'] ] );
					// }
				endif;

				// $discount = [];
				// if ( ! empty( $common_terms ) ) {
				// $discount_info = [];
				// foreach ( $common_terms as $common_term_id ) {
				// $discount_info[ $common_term_id ]['discount_figure']     = (float) get_term_meta( $common_term_id, 'trip_types_discount', true );
				// $discount_info[ $common_term_id ]['is_percent_discount'] = 'yes' === get_term_meta( $common_term_id, 'trip_type_percent_discount', true ) ? 'yes' : 'no';
				// $discount_info[ $common_term_id ]['no_of_trips']         = (int) get_term_meta( $common_term_id, 'trip_types_discount_trip_count', true );
				// }
				// $discount = array_filter(
				// $discount_info,
				// function( $di ) use ( $cart_items ) {
				// return $di['no_of_trips'] <= count( $cart_items );
				// }
				// );

				// usort(
				// $discount,
				// function( $a, $b ) {
				// if ( $a['discount_figure'] == $b['discount_figure'] ) {
				// return 0;
				// }
				// return ( $a['discount_figure'] < $b['discount_figure'] ) ? 1 : -1;
				// }
				// );
				// }
				// if ( is_array( $terms ) && count( $terms ) > 0 ) {
				// foreach ( $terms as $term ) {
				// $term_id          = $term->term_id;
				// $discount_figure = get_term_meta( $term_id, 'trip_types_discount', true );
				// $is_percent_discount = get_term_meta( $term_id, 'trip_type_percent_discount', true );
				// $is_percent_discount = ( 'yes' === $is_percent_discount  )  ? 'yes' : 'no';
				// if ( $discount_figure > 0 ) {
				// $category_discount_data[ $item['trip_id'] ] = array( 'discount_figure' => $discount_figure, 'is_percent_discount' => $is_percent_discount );
				// }
				// }
				// }
			}
			$common_terms = array_unique( array_merge( $terms ) );
			$discount     = array();
			if ( ! empty( $common_terms ) ) {
				$discount_info = array();
				foreach ( $common_terms as $common_term_id ) {
					$discount_info[ $common_term_id ]['discount_figure']     = (float) get_term_meta( $common_term_id, 'trip_types_discount', true );
					// $discount_info[ $common_term_id ]['is_percent_discount'] = 'yes' === get_term_meta( $common_term_id, 'trip_type_percent_discount', true ) ? 'yes' : 'no';
					$discount_info[ $common_term_id ]['no_of_trips']         = (int) get_term_meta( $common_term_id, 'trip_types_discount_trip_count', true );
				}

				// $type_with_higher_discount = 0;
				$discount = array_filter(
					$discount_info,
					function( $di ) use ( $cart_items ) {
						return count( $cart_items ) > 1;
					}
				);

				usort(
					$discount,
					function( $a, $b ) {
						if ( $a['discount_figure'] == $b['discount_figure'] ) {
							return 0;
						}
						return ( $a['discount_figure'] < $b['discount_figure'] ) ? 1 : -1;
					}
				);
			}

			// return array(
			// 'code'       => 'WP_TRAVEL_CART_ITEMS',
			// 'cart_items' => array($category_discount_data, $cart_trip_count, $terms),
			// );
		endif;

		// End Discount Implementation
		$cart = array();
		foreach ( $cart_items as $cart_id => $item ) {
			$trip_data     = WP_Travel_Helpers_Trips::get_trip( absint( $item['trip_id'] ) );
			$is_item_valid = true;
			if ( ! is_wp_error( $trip_data ) && 'WP_TRAVEL_TRIP_INFO' === $trip_data['code'] ) {
				$trip_items = ! empty( $item['trip'] ) ? $item['trip'] : array();
				if ( is_array( $trip_items ) && count( $trip_items ) > 0 ) {
					foreach ( $trip_items as $cat_id => $cat_value ) {
						unset( $item['trip'][ $cat_id ]['price'], $item['trip'][ $cat_id ]['price_partial'], $item['trip'][ $cat_id ]['custom_label'] );
					}
				}
				$cart[ $cart_id ]['pricing_id'] = $item['pricing_id'];
				$cart[ $cart_id ]['price_key']  = $item['price_key'];
				$cart[ $cart_id ]['trip_price'] = (float) number_format( $item['trip_price'], 2, '.', '' );

				// Start Apply Discounts if applicable
				// if ( ! empty( $discount ) ) {
				// 	$trip_price = (float) $item['trip_price'] * ( 100 - (float) $discount[0]['discount_figure'] ) / 100;
				// 	// if ( ! empty( $discount[0]['is_percent_discount'] ) && 'yes' === $discount[0]['is_percent_discount'] ) {
				// 	// } else {
				// 	// 	$trip_price = (float) $item['trip_price'] - (float) $discount[0]['discount_figure'];
				// 	// }
				// 	// $cart[ $cart_id ]['trip_price']         = $item['trip_price'];
				// 	$cart[ $cart_id ]['trip_price'] = number_format( $trip_price, 2, '.', '' );
				// }

				$cart[ $cart_id ]['trip_price_regular'] = (float) number_format( $item['trip_price'], 2, '.', '' );
				$cart[ $cart_id ]['extras']             = $item['trip_extras'];
				$cart[ $cart_id ]['trip']               = $item['trip'];
				$cart[ $cart_id ]['trip_data']          = $trip_data['trip'];
				$cart[ $cart_id ]['arrival_date']       = wp_travel_format_date( $item['arrival_date'] );
				$cart[ $cart_id ]['trip_time']          = $item['trip_time'];

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
		);
	}

	public static function add_to_cart( $postData = array() ) {
		if ( empty( $postData['trip_id'] ) ) {
			return new WP_Error( 'WP_TRAVEL_NO_TRIP_ID', __( 'Invalid trip id.', 'wp-travel' ) );
		}

		// START Temporary solution
		ob_start();
		$WP_Travel_Ajax = new WP_Travel_Ajax();
		$WP_Travel_Ajax->wp_travel_add_to_cart();
		$res = ob_get_contents();
		ob_end_clean();
		// END Temporary solution
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

	public static function add_to_cart1( $postData = array() ) {
		if ( empty( $postData['trip_id'] ) ) {
			return new WP_Error( 'WP_TRAVEL_NO_TRIP_ID', __( 'Invalid trip id.', 'wp-travel' ) );
		}

		global $wp_travel_cart;
		$allow_multiple_cart_items = apply_filters( 'wp_travel_allow_multiple_cart_items', false );

		if ( ! $allow_multiple_cart_items ) {
			$wp_travel_cart->clear();
		}

		$trip_id        = $postData['trip_id'];
		$price_key      = ! empty( $postData['price_key'] ) ? $postData['price_key'] : '';
		$pricing_id     = ! empty( $postData['pricing_id'] ) ? $postData['pricing_id'] : false;
		$arrival_date   = ! empty( $postData['arrival_date'] ) ? $postData['arrival_date'] : false;
		$departure_date = ! empty( $postData['departure_date'] ) ? $postData['departure_date'] : false;
		$pax            = ! empty( $postData['pax'] ) ? (array) $postData['pax'] : 0;
		$trip_extras    = ! empty( $postData['wp_travel_trip_extras'] ) ? $postData['wp_travel_trip_extras'] : array();
		$trip_extras    = ! empty( $postData['trip_extras'] ) ? $postData['trip_extras'] : $trip_extras;
		$trip_price     = 0;

		$attrs               = wp_travel_get_cart_attrs( $trip_id, $pax, '' );
		$pricing_option_type = wp_travel_get_pricing_option_type( $trip_id );

		if ( is_array( $pax ) && 'multiple-price' === $pricing_option_type ) { // @since 3.0.0
			$total_pax          = array_sum( $pax );
			$pricings           = wp_travel_get_trip_pricing_option( $trip_id ); // Get Pricing Options for the trip.
			$pricing_data       = isset( $pricings['pricing_data'] ) ? $pricings['pricing_data'] : array();
			$trip               = array();
			$trip_price_partial = 0;
			foreach ( $pax as $category_id => $pax_value ) {
				$category_price = wp_travel_get_price( $trip_id, false, $pricing_id, $category_id, $price_key ); // price key for legacy pricing structure @since 3.0.0.

				if ( function_exists( 'wp_travel_group_discount_price' ) ) { // From Group Discount addons.
					$group_trip_price = wp_travel_group_discount_price( $trip_id, $pax_value, $pricing_id, $category_id );

					if ( $group_trip_price ) {
						$category_price = $group_trip_price;
					}
				}
				$category_price_partial = $category_price;

				if ( wp_travel_is_partial_payment_enabled() ) {
					$percent                = wp_travel_get_actual_payout_percent( $trip_id );
					$category_price_partial = ( $category_price * $percent ) / 100;
				}

				$pricing_index = null;
				foreach ( $pricing_data as $index => $pricing ) {
					if ( isset( $pricing['categories'] ) && is_array( $pricing['categories'] ) ) {
						if ( array_key_exists( $category_id, $pricing['categories'] ) ) {
							$pricing_index = $index;
							break;
						};
					}
				}

				$category             = isset( $pricing_data[ $pricing_index ]['categories'][ $category_id ] ) ? $pricing_data[ $pricing_index ]['categories'][ $category_id ] : array();
				$trip[ $category_id ] = array(
					'pax'           => $pax_value,
					'price'         => wp_travel_get_formated_price( $category_price ),
					'price_partial' => wp_travel_get_formated_price( $category_price_partial ),
					'type'          => isset( $category['type'] ) ? $category['type'] : '', // Not set yet.
					'custom_label'  => isset( $category['custom_label'] ) ? $category['custom_label'] : __( 'Custom', 'wp-travel' ),
					'price_per'     => isset( $category['price_per'] ) ? $category['price_per'] : 'person',
				);

				// multiply category_price by pax to add in trip price if price per is person.
				if ( 'person' == $trip[ $category_id ]['price_per'] ) {
					$category_price         *= $pax_value;
					$category_price_partial *= $pax_value;
				}
				// add price.
				$trip_price         += $category_price;
				$trip_price_partial += $category_price_partial;
			}
			$attrs['trip'] = $trip;
			$pax           = $total_pax;

			$attrs['enable_partial'] = wp_travel_is_partial_payment_enabled();
			if ( $attrs['enable_partial'] ) {
				$trip_price_partial             = $trip_price;
				$payout_percent                 = wp_travel_get_payout_percent( $trip_id );
				$attrs['partial_payout_figure'] = $payout_percent; // added in 1.8.4

				if ( $payout_percent > 0 ) {
					$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
					$trip_price_partial = wp_travel_get_formated_price( $trip_price_partial );
				}
				$attrs['trip_price_partial'] = $trip_price_partial;
			}

			$attrs['pricing_id']     = $pricing_id;
			$attrs['arrival_date']   = $arrival_date;
			$attrs['departure_date'] = $departure_date;
			$attrs['trip_extras']    = $trip_extras;

			$attrs = apply_filters( 'wp_travel_cart_attributes', $attrs );

			$cart_item_id = $wt_cart->wp_travel_get_cart_item_id( $trip_id, $price_key, $arrival_date );

			$update_cart_on_add = apply_filters( 'wp_travel_filter_update_cart_on_add', true );

			if ( true === $update_cart_on_add ) {
				$items = $wt_cart->getItems();

				if ( isset( $items[ $cart_item_id ] ) ) {
					$pax     += $items[ $cart_item_id ]['pax'];
					$response = $wt_cart->update( $cart_item_id, $pax );
				} else {
					$response = $wt_cart->add( $trip_id, $trip_price, $trip_price_partial, $pax, $price_key, $attrs );
				}
			} else {
				$response = $wt_cart->add( $trip_id, $trip_price, $pax, $price_key, $attrs );
			}
			if ( true !== $response ) {
				return new WP_Error( 'WP_TRAVEL_CART_ITEM_NOT_ADDED', __( 'Trip not added to cart.', 'wp-travel' ) );
			}
			return array(
				'code'    => 'WP_TRAVEL_CART_ITEM_ADDED',
				'message' => __( 'Trip added to cart.', 'wp-travel' ),
			);
		}
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
		$response = $wt_cart->update( $cart_id, 1, false, $itemData );
		if ( ! $response ) {
			return new WP_Error( 'WP_TRAVEL_CART_ITEM_NOT_UPDATED', __( 'Cart item not updated.', 'wp-travel' ) );
		}
		return array(
			'code'    => 'WP_TRAVEL_CART_ITEM_UPDATED',
			'message' => __( 'Cart item updated.', 'wp-travel' ),
		);
	}
}
