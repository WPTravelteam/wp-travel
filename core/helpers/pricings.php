<?php
/**
 * Helper pricing class that provides all the utility methods for pricings.
 *
 * @category Plugin
 * @package  WP_Travel
 * @author   WEN Solutions
 * @license  GPLv2 or later
 * @link     wp-content/plugins/wp-travel/core/helpers/pricings.php
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper pricing class that provides all the utility methods for pricings.
 */
class WpTravel_Helpers_Pricings {

	/**
	 * WP Travel database table name.
	 *
	 * @var string $table_name.
	 */
	protected static $table_name = 'wt_pricings';

	/**
	 * Returns the pricings data.
	 *
	 * @param int $trip_id    Trip ID.
	 * @param int $pricing_id Pricing Id.
	 */
	public static function get_pricings( $trip_id = false, $pricing_id = null ) {

		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		global $wpdb;
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_pricings WHERE trip_id=%d order by sort_order asc", $trip_id ) );

		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICINGS' );
		}
		$pricings = array();
		$index    = 0;

		$selected_pricing = array();
		foreach ( $results as $price ) {
			$pricings[ $index ]['id']              = absint( $price->id );
			$pricings[ $index ]['title']           = $price->title;
			$pricings[ $index ]['max_pax']         = absint( $price->max_pax );
			$pricings[ $index ]['min_pax']         = absint( $price->min_pax );
			$pricings[ $index ]['has_group_price'] = ! empty( $price->has_group_price );
			$pricings[ $index ]['group_prices']    = ! empty( $price->group_prices ) ? maybe_unserialize( $price->group_prices ) : array();

			if ( ! function_exists( 'wp_travel_group_discount_price' ) ) {
				$pricings[ $index ]['has_group_price'] = false;
				$pricings[ $index ]['group_prices']    = array();
			}

			// Inventory.
			$inventory_data = array(
				'max_pax'        => absint( $price->max_pax ),
				'min_pax'        => absint( $price->min_pax ),
				'available_pax'  => absint( $price->max_pax ),
				'status_message' => '',
				'sold_out'       => false,
				'booked_pax'     => 0,
				'pax_limit'      => absint( $price->max_pax ),
			);
			// End Inventory.

			$pricings[ $index ]['categories'] = array();
			$categories                       = WP_Travel_Helpers_Trip_Pricing_Categories::get_trip_pricing_categories( absint( $price->id ) );
			if ( ! is_wp_error( $categories ) && 'WP_TRAVEL_TRIP_PRICING_CATEGORIES' === $categories['code'] ) {
				$pricings[ $index ]['categories'] = $categories['categories'];
			}
			$pricings[ $index ]['trip_extras'] = array();
			if ( ! empty( $price->trip_extras ) ) {
				// Temp fixes for extras for admin trip edit section.
				if ( is_admin() ) {

					$trip_extras = WP_Travel_Helpers_Trip_Extras::get_trip_extras(
						array(
							'post__in' => explode( ',', trim( $price->trip_extras ) ),
						)
					);
					if ( ! is_wp_error( $trip_extras ) && 'WP_TRAVEL_TRIP_EXTRAS' === $trip_extras['code'] ) {
						$pricings[ $index ]['trip_extras'] = $trip_extras['trip_extras'];
					}
				} else {
					$pricings[ $index ]['trip_extras'] = explode( ',', trim( $price->trip_extras ) );
				}
			}

			if ( absint( $price->id ) === absint( $pricing_id ) ) {
				$selected_pricing = $pricings[ $index ];
			}
			$index++;
		}
		return array(
			'code'     => 'WP_TRAVEL_TRIP_PRICINGS',
			'pricings' => $pricing_id && count( $selected_pricing ) > 0 ? $selected_pricing : $pricings,
		);
	}

	/**
	 * Update trip pricings.
	 *
	 * @param int   $trip_id Trip ID.
	 * @param array $pricings Trip pricing options.
	 */
	public static function update_pricings( $trip_id, $pricings ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $pricings ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICINGS' );
		}

		$responses = array();
		foreach ( $pricings as $index => $pricing ) {
			$pricing_id            = isset( $pricing['id'] ) ? absint( $pricing['id'] ) : 0;
			$pricing['sort_order'] = ( $index + 1 );
			if ( empty( $pricing_id ) ) {
				$result = self::add_individual_pricing( $trip_id, $pricing );
				if ( ! is_wp_error( $result ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $result['code'] && ! empty( $pricing['categories'] ) ) {
					WP_Travel_Helpers_Trip_Pricing_Categories::update_pricing_categories( $result['pricing_id'], $pricing['categories'] );
				}
			} else {
				$result = self::update_individual_pricing( $pricing_id, $pricing );
				if ( ! is_wp_error( $result ) && 'WP_TRAVEL_UPDATED_TRIP_PRICING' === $result['code'] && ! empty( $pricing['categories'] ) ) {
					WP_Travel_Helpers_Trip_Pricing_Categories::update_pricing_categories( $pricing_id, $pricing['categories'] );
				} elseif ( empty( $pricing['categories'] ) ) {
					WP_Travel_Helpers_Trip_Pricing_Categories::remove_trip_pricing_categories( $pricing_id );
				}
			}
		}
		return array(
			'code'      => 'WP_TRAVEL_UPDATE_TRIP_PRICINGS',
			'responses' => $responses,
		);
	}

	/**
	 * Update individual trip pricings.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param array  $pricing_data Pricing Data.
	 */
	public static function update_individual_pricing( $pricing_id, $pricing_data ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $pricing_data['title'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$trip_extras = ! empty( $pricing_data['trip_extras'] ) ? $pricing_data['trip_extras'] : '';
		if ( ! empty( $trip_extras ) && is_array( $trip_extras ) ) {
			$_trip_extras = array();
			foreach ( $trip_extras as $extra ) {
				$_trip_extras[] = $extra['id'];
			}

			$trip_extras = implode( ',', $_trip_extras );
		}
		$response = $wpdb->update(
			"{$wpdb->prefix}wt_pricings",
			array(
				'title'           => esc_attr( $pricing_data['title'] ),
				'max_pax'         => ! empty( $pricing_data['max_pax'] ) ? absint( $pricing_data['max_pax'] ) : 0,
				'min_pax'         => ! empty( $pricing_data['min_pax'] ) ? absint( $pricing_data['min_pax'] ) : 0,
				'has_group_price' => ! empty( $pricing_data['has_group_price'] ) ? absint( $pricing_data['has_group_price'] ) : 0,
				'group_prices'    => ! empty( $pricing_data['has_group_price'] ) && ! empty( $pricing_data['group_prices'] ) ? maybe_serialize( wptravel_sanitize_array( wp_unslash( $pricing_data['group_prices'] ) ) ) : maybe_serialize( array() ),
				'trip_extras'     => esc_attr( $trip_extras ),
				'sort_order'      => ! empty( $pricing_data['sort_order'] ) ? absint( $pricing_data['sort_order'] ) : 1,
			),
			array( 'id' => $pricing_id ),
			array(
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
			),
			array( '%d' )
		);

		if ( false === $response ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
		}

		return array(
			'code'       => 'WP_TRAVEL_UPDATED_TRIP_PRICING',
			'pricing_id' => $pricing_id,
		);
	}

	/**
	 * Get individual trip pricings.
	 *
	 * @param string $pricing_id Pricing ID.
	 */
	public static function get_individual_pricing( $pricing_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}
		global $wpdb;
	}

	/**
	 * Add individual trip pricings.
	 *
	 * @param int   $trip_id Trip ID.
	 * @param array $pricing_data Pricing data.
	 */
	public static function add_individual_pricing( $trip_id, $pricing_data ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}
		if ( empty( $pricing_data['title'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wt_pricings';

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$table} WHERE trip_id=%d AND title=%s", $trip_id, $pricing_data['title'] ) );

		if ( $results ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_DUPLICATE_PRICING' );
		}
		/**
		 * @since 5.3.6
		 */
		$trip_extras = ! empty( $pricing_data['trip_extras'] ) ? $pricing_data['trip_extras'] : '';
		if ( ! empty( $trip_extras ) && is_array( $trip_extras ) ) {
			$_trip_extras = array();
			foreach ( $trip_extras as $extra ) {
				$_trip_extras[] = $extra['id'];
			}

			$trip_extras = implode( ',', $_trip_extras );
		}
		$wpdb->insert(
			$table,
			array(
				'title'           => esc_attr( $pricing_data['title'] ),
				'max_pax'         => ! empty( $pricing_data['max_pax'] ) ? absint( $pricing_data['max_pax'] ) : 0,
				'min_pax'         => ! empty( $pricing_data['min_pax'] ) ? absint( $pricing_data['min_pax'] ) : 0,
				'has_group_price' => ! empty( $pricing_data['has_group_price'] ) ? absint( $pricing_data['has_group_price'] ) : 0,
				'group_prices'    => ! empty( $pricing_data['has_group_price'] ) && ! empty( $pricing_data['group_prices'] ) ? maybe_serialize( $pricing_data['group_prices'] ) : maybe_serialize( array() ),
				'trip_id'         => $trip_id,
				'trip_extras'     => ! empty( $trip_extras ) ? esc_attr( $trip_extras ) : '',
				'sort_order'      => ! empty( $pricing_data['sort_order'] ) ? absint( $pricing_data['sort_order'] ) : 1,
			),
			array(
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
				'%d',
			)
		);
		$inserted_id = $wpdb->insert_id;
		if ( empty( $inserted_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
		}
		return array(
			'code'       => 'WP_TRAVEL_ADDED_TRIP_PRICING',
			'pricing_id' => $inserted_id,
		);
	}

	/**
	 * Remove individual trip pricings.
	 *
	 * @param string $pricing_id Pricing ID.
	 */
	public static function remove_individual_pricing( $pricing_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$result = $wpdb->delete( $table, array( 'id' => $pricing_id ), array( '%d' ) );

		if ( false === $result ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_DELETING_PRICING' );
		}

		WP_Travel_Helpers_Trip_Pricing_Categories::remove_trip_pricing_categories( $pricing_id );

		return WP_Travel_Helpers_Response_Codes::get_success_response( 'WP_TRAVEL_REMOVED_TRIP_PRICING' );
	}

	/**
	 * Get Price of trip. Price key is only for old data less than WP Travel @since 3.0.0
	 *
	 * @param array $args Arguments.
	 *
	 * @since 4.4.0
	 * @return Number
	 */
	public static function get_price( $args = array() ) {

		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();
		if ( ! $trip_id ) {
			return 0;
		}
		if ( 'single-price' === wptravel_get_pricing_option_type( $trip_id ) ) { // For legacy single pricing support @since 3.0.0.
			$price = self::get_price_legacy( $args );
		} else {
			$price        = self::get_price_v4( $args ); // New way to grab price @since 4.0.0.
			$switch_to_v4 = wptravel_is_react_version_enabled();
			if ( ! $switch_to_v4 ) : // Follow the tradtion to get price.
				$price = self::get_price_legacy( $args );
			endif;
		}

		$price = $price ? $price : 0;
		// Add Multiple currency support to get converted price.
		$price = WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $price );
		return apply_filters( 'wptravel_get_price', (float) $price, $args ); // filter wptravel_get_price @since 4.6.4.
	}

	/**
	 * Get Legacy Price. for WP Travel less than v4
	 *
	 * @param array $args Arguments.
	 *
	 * @since 4.4.0
	 * @return Number
	 */
	public static function get_price_legacy( $args = array() ) {

		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();
		if ( ! $trip_id ) {
			return false;
		}
		$is_regular_price = isset( $args['is_regular_price'] ) ? $args['is_regular_price'] : false;
		$pricing_id       = isset( $args['pricing_id'] ) ? $args['pricing_id'] : '';
		$category_id      = isset( $args['category_id'] ) ? $args['category_id'] : '';
		$price_key        = isset( $args['price_key'] ) ? $args['price_key'] : '';

		if ( 'single-price' === wptravel_get_pricing_option_type( $trip_id ) ) { // For legacy single pricing support @since 3.0.0.
			$price       = get_post_meta( $trip_id, 'wp_travel_price', true );
			$enable_sale = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );
			if ( $enable_sale && ! $is_regular_price ) {
				$price = get_post_meta( $trip_id, 'wp_travel_sale_price', true );
			}
		} else {
			$price           = 0;
			$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

			if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
				// Specific listing as per pricing_id to get price.
				if ( ! empty( $pricing_id ) ) {
					$pricing_option = isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();
					if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0.
						if ( ! $price_key && $pricing_id === $category_id ) { // By default we set category_id = pricing_id due to no category_id in listing of legacy version.
							$price_key = isset( $pricing_options[ $pricing_id ]['price_key'] ) ? $pricing_options[ $pricing_id ]['price_key'] : '';
						}

						// [Snippet from wp_travel_get_actual_trip_price function ].
						// Added price calculation for pricing key [multiple pricing] @since 1.9.2.
						$enable_pricing_options = wptravel_is_enable_pricing_options( $trip_id );
						$valid_price_key        = wptravel_is_price_key_valid( $trip_id, $price_key );
						if ( '' !== $price_key && $enable_pricing_options && $valid_price_key ) {
							$pricing_data = wptravel_get_pricing_variation( $trip_id, $price_key );
							if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

								foreach ( $pricing_data as $p_ky => $pricing ) :
									if ( isset( $pricing['price'] ) ) {

										$price       = $pricing['price'];
										$enable_sale = isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'];
										$sale_price  = isset( $pricing['sale_price'] ) && $pricing['sale_price'] > 0 ? $pricing['sale_price'] : 0;

										if ( $enable_sale && $sale_price && ! $only_regular_price ) {
											$price = $pricing['sale_price'];
										}
									}
								endforeach;
							}
						}
					} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
						if ( ! empty( $category_id ) ) {
							$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
							$price           = $category_option['price'];
							$enable_sale     = isset( $category_option['enable_sale'] ) && 'yes' === $category_option['enable_sale'] ? true : false;
							$sale_price      = isset( $category_option['sale_price'] ) && $category_option['sale_price'] > 0 ? $category_option['sale_price'] : 0;

							if ( $enable_sale && $sale_price && ! $is_regular_price ) {
								$price = $category_option['sale_price'];
							}
						}
					}
				} else {
					$min_keys       = wptravel_get_min_pricing_id( $trip_id );
					$pricing_id     = isset( $min_keys['pricing_id'] ) ? $min_keys['pricing_id'] : '';
					$pricing_option = ! empty( $pricing_id ) && isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();

					if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0.

						if ( isset( $pricing_option['price'] ) ) { // Old pricing option.

							$current_price = $pricing_option['price'];
							$enable_sale   = isset( $pricing_option['enable_sale'] ) ? $pricing_option['enable_sale'] : 'no';
							$sale_price    = isset( $pricing_option['sale_price'] ) ? $pricing_option['sale_price'] : 0;

							if ( 'yes' === $enable_sale && $sale_price > 0 && ! $is_regular_price ) {
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

							$min_catetory_id       = '';
							$catetory_id_max_price = '';
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
								}
							}

							// Return regular price.
							if ( $is_regular_price && ! empty( $min_catetory_id ) && isset( $pricing_option['categories'][ $min_catetory_id ]['price'] ) ) {
								$price = $pricing_option['categories'][ $min_catetory_id ]['price'];
							}
						}
					}
				}
			}
		}
		return $price;
	}

	/**
	 * Get V4 or gerater than V4 Price.
	 *
	 * @param array $args Arguments.
	 *
	 * @since 4.4.0
	 * @return Number
	 */
	public static function get_price_v4( $args = array() ) {

		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();
		if ( ! $trip_id ) {
			return false;
		}
		$is_regular_price = isset( $args['is_regular_price'] ) ? $args['is_regular_price'] : false; // This will check sale enable for From Price.
		$pricing_id       = isset( $args['pricing_id'] ) ? $args['pricing_id'] : '';
		$category_id      = isset( $args['category_id'] ) ? $args['category_id'] : '';
		$price_key        = isset( $args['price_key'] ) ? $args['price_key'] : '';

		$price         = 0;
		$pricings_data = self::get_pricings( $trip_id );
		$highest_price = get_post_meta( $trip_id, 'wp_travel_show_highest_price', true );
		if ( ! empty( $pricing_id ) && ! empty( $category_id ) && is_array( $pricings_data ) ) { // Quick Fix here. Pricing data may be WP Error object.

			$pricings = array_filter(
				$pricings_data['pricings'],
				function( $p ) use ( $pricing_id ) {
					return (int) $p['id'] === (int) $pricing_id;
				}
			);
			if ( is_array( $pricings ) && count( $pricings ) > 0 ) {
				$pricing    = array_shift( $pricings );
				$categories = array_filter(
					$pricing['categories'],
					function( $c ) use ( $category_id ) {
						return (int) $c['id'] === (int) $category_id;
					}
				);
				$category   = array_shift( $categories );
				$price      = $category['regular_price'];
				if ( $category['is_sale'] ) {
					$price = $category['sale_price'];
				}
			}
		} else {
			// Min price.
			if ( is_array( $pricings_data ) && isset( $pricings_data['pricings'] ) && count( $pricings_data['pricings'] ) ) {

				$pricings_data = $pricings_data['pricings'];
				$category_data = array_column( $pricings_data, 'categories' );

				$regular = 0; // Init regular price.
				if ( is_array( $category_data ) ) {
					/**
					 * @version 6.0.0
					 */
					if ( 'yes' == $highest_price ) {
						foreach ( $category_data as $pricing_categories ) {
							foreach ( $pricing_categories as $pricing_category ) {
								$current_price = ( $pricing_category['is_sale'] && $pricing_category['sale_price'] > 0 ) ? $pricing_category['sale_price'] : $pricing_category['regular_price'];
								if ( ! (float) $price || (float) $current_price > (float) $price && (float) $current_price > 0 ) { // init / update min price.
									$price   = $current_price;
									$regular = $pricing_category['regular_price'];
								}
							}
						}
					} else {
						foreach ( $category_data as $pricing_categories ) {
							foreach ( $pricing_categories as $pricing_category ) {
								$current_price = ( $pricing_category['is_sale'] && $pricing_category['sale_price'] > 0 ) ? $pricing_category['sale_price'] : $pricing_category['regular_price'];
								if ( ! (float) $price || (float) $current_price < (float) $price && (float) $current_price > 0 ) { // init / update min price.
									$price   = $current_price;
									$regular = $pricing_category['regular_price'];
								}
							}
						}
					}
				}
				// To return regular price. ( for v4 only ).
				if ( $is_regular_price ) {
					$price = $regular;
				}
			}
		}
		return $price;
	}

	// Partial Payments.
	/**
	 * Get Minimum payout amount
	 *
	 * @param Number $trip_id ID of the trip.
	 * @return Number
	 */
	public static function get_payout_percent( $trip_id ) {
		if ( ! $trip_id ) {
			return 0;
		}
		$settings   = wptravel_get_settings();
		$use_global = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_use_global', true );

		// Global Payout percent.
		$payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
		// Trip specific payout percent.
		$trip_payout_percent = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_percent', true );

		global $wt_cart;
		$items = $wt_cart->getItems();
 
		// Trip specific partial payment is only for one cart items. if multiple items then above global payout percent will be used as payout percent.
		if ( $use_global == "" && $trip_payout_percent && count( $items ) < 2 ) {
			$payout_percent = $trip_payout_percent;
		}
		
		$payout_percent = apply_filters( 'wp_travel_payout_percent', $payout_percent, $trip_id ); // @phpcs:ignore
		$payout_percent = apply_filters( 'wptravel_payout_percent', $payout_percent, $trip_id );
		$payout_percent = wptravel_initial_partial_payout_unformated( $payout_percent );
		return number_format( $payout_percent, 2, '.', '' );
	}

}
