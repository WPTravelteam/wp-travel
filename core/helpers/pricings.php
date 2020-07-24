<?php
class WP_Travel_Helpers_Pricings {
	protected static $table_name = 'wt_pricings';
	public static function get_pricings( $trip_id = false, $date = false ) {
		// if ( get_option( 'wp_travel_pricing_table_created', 'no' ) != 'yes' ) {
		// return;
		// }
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		if ( is_multisite() ) {
			/**
			 * @todo Get Table name on Network Activation.
			 */
			$blog_id = get_current_blog_id();
			$table   = $wpdb->base_prefix . $blog_id . '_' . self::$table_name;
		}
		$results = $wpdb->get_results( "SELECT * FROM {$table} WHERE trip_id={$trip_id}" );

		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICINGS' );
		}
		$pricings = array();
		$index    = 0;

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
			// $pricings = apply_filters( 'wp_travel_inventory_data', $inventory_data, $trip_id, '', $start_date );
			// End Inventory.

			$pricings[ $index ]['categories'] = array();
			$categories                       = WP_Travel_Helpers_Trip_Pricing_Categories::get_trip_pricing_categories( absint( $price->id ) );
			if ( ! is_wp_error( $categories ) && 'WP_TRAVEL_TRIP_PRICING_CATEGORIES' === $categories['code'] ) {
				$pricings[ $index ]['categories'] = $categories['categories'];
			}
			$pricings[ $index ]['trip_extras'] = array();
			if ( ! empty( $price->trip_extras ) ) {
				$trip_extras = WP_Travel_Helpers_Trip_Extras::get_trip_extras(
					array(
						'post__in' => explode( ',', trim( $price->trip_extras ) ),
					)
				);

				if ( ! is_wp_error( $trip_extras ) && 'WP_TRAVEL_TRIP_EXTRAS' === $trip_extras['code'] ) {
					$pricings[ $index ]['trip_extras'] = $trip_extras['trip_extras'];
				}
			}
			$index++;
		}
		return array(
			'code'     => 'WP_TRAVEL_TRIP_PRICINGS',
			'pricings' => $pricings,
		);
	}

	public static function update_pricings( $trip_id, $pricings ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $pricings ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICINGS' );
		}

		$responses = array();
		foreach ( $pricings as $pricing ) {
			$pricing_id = isset( $pricing['id'] ) ? absint( $pricing['id'] ) : 0;
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
			$table,
			array(
				'title'           => esc_attr( $pricing_data['title'] ),
				'max_pax'         => ! empty( $pricing_data['max_pax'] ) ? absint( $pricing_data['max_pax'] ) : 0,
				'min_pax'         => ! empty( $pricing_data['min_pax'] ) ? absint( $pricing_data['min_pax'] ) : 0,
				'has_group_price' => ! empty( $pricing_data['has_group_price'] ) ? absint( $pricing_data['has_group_price'] ) : 0,
				'group_prices'    => ! empty( $pricing_data['has_group_price'] ) && ! empty( $pricing_data['group_prices'] ) ? maybe_serialize( $pricing_data['group_prices'] ) : maybe_serialize( array() ),
				'trip_extras'     => esc_attr( $trip_extras ),
			),
			array( 'id' => $pricing_id ),
			array(
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%s',
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

	public static function get_individual_pricing( $pricing_id ) {
		if ( empty( $pricing_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}
		global $wpdb;
		// $myrows = $wpdb->get_results( "SELECT id, name FROM mytable" );
		// return array(
		// 'code' => 'WP_TRAVEL_TRIP_PRICINGS',
		// 'pricings' => $pricings['pricing_data']
		// );
	}

	public static function add_individual_pricing( $trip_id, $pricing_data ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_PRICING_ID' );
		}
		if ( empty( $pricing_data['title'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_SAVING_PRICING' );
		}
		global $wpdb;
		$table = $wpdb->prefix . 'wt_pricings';
		$wpdb->insert(
			$table,
			array(
				'title'           => esc_attr( $pricing_data['title'] ),
				'max_pax'         => ! empty( $pricing_data['max_pax'] ) ? absint( $pricing_data['max_pax'] ) : 0,
				'min_pax'         => ! empty( $pricing_data['min_pax'] ) ? absint( $pricing_data['min_pax'] ) : 0,
				'has_group_price' => ! empty( $pricing_data['has_group_price'] ) ? absint( $pricing_data['has_group_price'] ) : 0,
				'group_prices'    => ! empty( $pricing_data['has_group_price'] ) && ! empty( $pricing_data['group_prices'] ) ? maybe_serialize( $pricing_data['group_prices'] ) : maybe_serialize( array() ),
				'trip_id'         => $trip_id,
				'trip_extras'     => ! empty( $pricing_data['trip_extras'] ) ? esc_attr( $pricing_data['trip_extras'] ) : '',
			),
			array(
				'%s',
				'%d',
				'%d',
				'%d',
				'%s',
				'%d',
				'%s',
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
}
