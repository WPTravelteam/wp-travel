<?php
/**
 * Helpers class for trip pricing categories.
 *
 * @package WP_Travel
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helpers class for trip pricing categories.
 */
class WpTravel_Helpers_Trip_Pricing_Categories {

	/**
	 * WP Travel table name.
	 *
	 * @var string $table_name WP Travel table name.
	 */
	private static $table_name = 'wt_price_category_relation';

	/**
	 * Returns the trip pricing categories data accoring to the provided pricing id.
	 *
	 * @param string $pricing_id Pricing ID.
	 */
	public static function get_trip_pricing_categories( $pricing_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		if ( is_multisite() ) {
			/**
			 * If it is multisite.
			 */
			$blog_id = get_current_blog_id();
			$table   = $wpdb->base_prefix . $blog_id . '_' . self::$table_name;
		}

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_price_category_relation WHERE `pricing_id` = %d", $pricing_id ) );

		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_PRICING_CATEGORIES' );
		}

		$categories = array();
		$index      = 0;
		foreach ( $results as $result ) {
			
			$pricing_category_id = absint( $result->pricing_category_id );
			# pricing categories may be got deleted
			if( ! term_exists( $pricing_category_id, 'itinerary_pricing_category' ) ){
				continue;
			}

			$regular_price = $result->regular_price;
			$sale_price    = $result->sale_price;

			$group_prices     = ! empty( $result->group_prices ) ? maybe_unserialize( $result->group_prices ) : array();
			$new_group_prices = $group_prices;

			if ( is_array( $group_prices ) && count( $group_prices ) > 0 ) {
				foreach ( $group_prices as $i => $group_price ) {
					$new_group_price                 = $group_price['price'];
					// $new_group_prices[ $i ]['price'] = self::get_converted_price( $new_group_price );
					$new_group_prices[ $i ]['price'] = $new_group_price;
				}
			}

			$categories[ $index ]['id']              = $pricing_category_id;
			$categories[ $index ]['price_per']       = $result->price_per;
			// $categories[ $index ]['regular_price']   = self::get_converted_price( $regular_price );
			$categories[ $index ]['regular_price']   = $regular_price;
			$categories[ $index ]['is_sale']         = ! empty( $result->is_sale ) ? true : false;
			// $categories[ $index ]['sale_price']      = self::get_converted_price( $sale_price );
			$categories[ $index ]['sale_price']      = $sale_price;
			$categories[ $index ]['has_group_price'] = ! empty( $result->has_group_price );
			$categories[ $index ]['group_prices']    = $new_group_prices;

			if ( ! function_exists( 'wp_travel_group_discount_price' ) ) {
				$categories[ $index ]['has_group_price'] = false;
				$categories[ $index ]['group_prices']    = array();
			}

			$categories[ $index ]['default_pax'] = ! empty( $result->default_pax ) ? absint( $result->default_pax ) : '0';
			$term_info                           = WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy::get_trip_pricing_categories_term( $pricing_category_id );
			if ( ! is_wp_error( $term_info ) && 'WP_TRAVEL_TRIP_PRICING_CATEGORIES_TAXONOMY_TERM' === $term_info['code'] ) {
				$categories[ $index ]['term_info'] = $term_info['pricing_category_term_info'];
			}
			$index++;
		}

		return array(
			'code'       => 'WP_TRAVEL_TRIP_PRICING_CATEGORIES',
			'categories' => $categories,
		);
	}

	/**
	 * Remove trip pricing categories.
	 *
	 * @param string $pricing_id Pricing ID.
	 */
	public static function remove_trip_pricing_categories( $pricing_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$result = $wpdb->delete( $table, array( 'pricing_id' => $pricing_id ), array( '%d' ) );

		if ( false === $result ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_DELETING_PRICING_CATEGORIES' );
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response( 'WP_TRAVEL_REMOVED_TRIP_PRICING_CATEGORIES' );
	}

	/**
	 * Update trip pricing categories.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param string $categories Categories.
	 */
	public static function update_pricing_categories( $pricing_id, $categories ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $categories ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_PRICING_CATEGORIES' );
		}

		$result           = self::remove_trip_pricing_categories( $pricing_id );
		$saved_categories = array();
		foreach ( $categories as $category ) {
			$category_id = absint( $category['id'] );
			if ( ! empty( $category_id ) ) {
				$result = self::update_individual_pricing_category( $pricing_id, $category );
				if ( ! is_wp_error( $result ) && 'WP_TRAVEL_UPDATED_TRIP_PRICING_CATEGORY' === $result['code'] ) {
					$saved_categories[] = $category;
				}
			}
		}
		return array(
			'code'       => 'WP_TRAVEL_UPDATE_TRIP_PRICINGS',
			'categories' => $saved_categories,
		);
	}

	/**
	 * Select individual pricing category.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param string $category_id Category ID.
	 */
	public static function select_individual_pricing_category( $pricing_id, $category_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $category_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_CATEGORY_ID' );
		}

		global $wpdb;

		$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_price_category_relation WHERE `pricing_id` = %d AND `pricing_category_id` = %d", $pricing_id, $category_id ) );
		if ( empty( $result ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_CATEGORY' );
		}

		return array(
			'code'     => 'WP_TRAVEL_TRIP_PRICING_CATEGORY',
			'category' => $result,
		);
	}

	/**
	 * Update individual pricing category.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param array  $category Category data.
	 */
	public static function update_individual_pricing_category( $pricing_id, $category ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $category['id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_CATEGORY_ID' );
		}
		$get_category = self::select_individual_pricing_category( $pricing_id, $category['id'] );
		if ( ! is_wp_error( $get_category ) && 'WP_TRAVEL_TRIP_PRICING_CATEGORY' === $get_category['code'] ) {
			global $wpdb;
			$table    = $wpdb->prefix . self::$table_name;
			$response = $wpdb->update(
				$table,
				array(
					'price_per'       => ! empty( $category['price_per'] ) ? $category['price_per'] : 'person',
					'regular_price'   => ! empty( $category['regular_price'] ) ? $category['regular_price'] : 0,
					'is_sale'         => ! empty( $category['is_sale'] ) ? absint( $category['is_sale'] ) : 0,
					'sale_price'      => ! empty( $category['sale_price'] ) ? $category['sale_price'] : 0,
					'has_group_price' => ! empty( $category['has_group_price'] ) ? absint( $category['has_group_price'] ) : 0,
					'group_prices'    => ! empty( $category['has_group_price'] ) && ! empty( $category['group_prices'] ) ? maybe_serialize( $category['group_prices'] ) : maybe_serialize( array() ),
					'default_pax'     => ! empty( $category['default_pax'] ) ? absint( $category['default_pax'] ) : 0,
				),
				array(
					'pricing_id'          => $pricing_id,
					'pricing_category_id' => $category['id'],
				),
				array(
					'%s',
					'%s',
					'%d',
					'%s',
					'%d',
					'%s',
					'%d',
				),
				array( '%d', '%d' )
			);

			if ( false === $response ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_UPDATING_PRICING_CATEGORY' );
			}
		} else {
			$result = self::add_individual_pricing_category( $pricing_id, $category );
			if ( is_wp_error( $result ) ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_ADDING_PRICING_CATEGORY' );
			}
		}
		return array(
			'code'     => 'WP_TRAVEL_UPDATED_TRIP_PRICING_CATEGORY',
			'category' => $category,
		);
	}

	/**
	 * Add individual pricing category.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param array  $category Category data.
	 */
	public static function add_individual_pricing_category( $pricing_id, $category ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $category['id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_CATEGORY_ID' );
		}
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		$wpdb->insert(
			$table,
			array(
				'pricing_id'          => $pricing_id,
				'pricing_category_id' => $category['id'],
				'price_per'           => ! empty( $category['price_per'] ) ? $category['price_per'] : 'person',
				'regular_price'       => ! empty( $category['regular_price'] ) ? $category['regular_price'] : 0,
				'is_sale'             => ! empty( $category['is_sale'] ) ? $category['is_sale'] : 0,
				'sale_price'          => ! empty( $category['sale_price'] ) ? $category['sale_price'] : 0,
				'has_group_price'     => ! empty( $category['has_group_price'] ) ? absint( $category['has_group_price'] ) : 0,
				'group_prices'        => ! empty( $category['has_group_price'] ) && ! empty( $category['group_prices'] ) ? maybe_serialize( $category['group_prices'] ) : maybe_serialize( array() ),
				'default_pax'         => ! empty( $category['default_pax'] ) ? absint( $category['default_pax'] ) : 0,
			),
			array(
				'%d',
				'%d',
				'%s',
				'%s',
				'%d',
				'%s',
				'%d',
				'%s',
				'%d',
			)
		);
		$inserted_id = $wpdb->insert_id;
		if ( empty( $inserted_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_ADDING_PRICING_CATEGORY' );
		}

		return array(
			'code'     => 'WP_TRAVEL_ADDED_TRIP_PRICING_CATEGORY',
			'category' => $category,
		);
	}

	/**
	 * Remove individual pricing category.
	 *
	 * @param string $pricing_id Pricing ID.
	 * @param string $category_id Category ID.
	 */
	public static function remove_individual_pricing_category( $pricing_id, $category_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}

		if ( empty( $category_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_CATEGORY_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;

		$result = $wpdb->delete(
			$table,
			array(
				'pricing_id'          => $pricing_id,
				'pricing_category_id' => $category_id,
			),
			array( '%d', '%d' )
		);

		if ( false === $result ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_DELETING_PRICING_CATEGORY' );
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response( 'WP_TRAVEL_REMOVED_TRIP_PRICING_CATEGORY' );
	}

	/**
	 * Returns multiple currency converted price.
	 *
	 * @param string $price Trip price which needs to be converted.
	 */
	public static function get_converted_price( $price ) {
		if ( empty( $price ) ) {
			return $price;
		}
		// This will return if not ajax request.
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return $price;
		}
		$price = apply_filters( 'wp_travel_multiple_currency', $price ); // @phpcs:ignore
		$price = apply_filters( 'wptravel_multiple_currency', $price );

		return self::get_formatted_price( $price );
	}

	/**
	 * Formats the provided price string to readable format.
	 *
	 * @param float $amount Trip unformatted amount.
	 * @param int   $number_of_decimals Number of decimals number after dot.
	 */
	private static function get_formatted_price( $amount, $number_of_decimals = 2 ) {
		if ( ! is_float( $amount ) ) {
			$amount = (float) $amount;
		}
		return number_format( $amount, $number_of_decimals, '.', '' );
	}
}
