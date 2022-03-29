<?php
class WP_Travel_Helpers_Trip_Extras {
	public static function get_trip_extras( $args = array() ) {
		$default = array(
			'post_type'      => 'tour-extras',
			'post_status'    => array( 'publish' ),
			'posts_per_page' => '-1',
		);

		$args = wp_parse_args( $args, $default );

		$trip_extras = get_posts( $args );

		if ( empty( $trip_extras ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_EXTRAS' );
		}

		$_trip_extras = array();
		$index        = 0;
		foreach ( $trip_extras as $key => $trip_extra ) {
			$tour_extras_metas                 = get_post_meta( $trip_extra->ID, 'wp_travel_tour_extras_metas', true );
			$tour_extras_metas                 = maybe_unserialize( $tour_extras_metas );
			$_trip_extras[ $index ]['id']      = $trip_extra->ID;
			$_trip_extras[ $index ]['title']   = $trip_extra->post_title;
			$_trip_extras[ $index ]['content'] = '';
			// $_trip_extras[$index]['regular_price'] = 0;
			// $_trip_extras[$index]['sale_price'] = 0;
			// $_trip_extras[$index]['is_sale'] = false;
			// $_trip_extras[$index]['is_required'] = false;
			// $_trip_extras[$index]['unit'] = 'unit';
			if ( false !== $tour_extras_metas && is_array( $tour_extras_metas ) ) {
				$_trip_extras[ $index ]['content'] = isset( $tour_extras_metas['extras_item_description'] ) ? $tour_extras_metas['extras_item_description'] : '';
				$_trip_extras[ $index ]['excerpt'] = isset( $tour_extras_metas['extras_item_description'] ) ? wp_trim_words( $tour_extras_metas['extras_item_description'], 15 ) : '';
				// $_trip_extras[$index]['regular_price'] = money_format( '%i', $tour_extras_metas['extras_item_price'] );
				if ( class_exists( 'WP_Travel_Tour_Extras_Core' ) ) {
					$price      = isset( $tour_extras_metas['extras_item_price'] ) ? $tour_extras_metas['extras_item_price'] : 0;
					$sale_price = isset( $tour_extras_metas['extras_item_sale_price'] ) ? $tour_extras_metas['extras_item_sale_price'] : 0;

					$tour_extras_metas['extras_item_price']      = wptravel_get_formated_price( $price );
					$tour_extras_metas['extras_item_sale_price'] = wptravel_get_formated_price( $sale_price );

					$_trip_extras[ $index ]['tour_extras_metas'] = $tour_extras_metas;
					$_trip_extras[ $index ]['link']              = get_permalink( $trip_extra->ID );
					$_trip_extras[ $index ]['is_sale']           = isset( $tour_extras_metas['extras_item_sale_price'] ) && $tour_extras_metas['extras_item_sale_price'] > 0;
					$_trip_extras[ $index ]['sale_price']        = isset( $tour_extras_metas['extras_item_sale_price'] ) ? $tour_extras_metas['extras_item_sale_price'] : 0;
					$_trip_extras[ $index ]['is_required']       = isset( $tour_extras_metas['extras_is_required'] ) && 'yes' === $tour_extras_metas['extras_is_required'];
					$_trip_extras[ $index ]['unit']              = isset( $tour_extras_metas['extras_item_unit'] ) ? $tour_extras_metas['extras_item_unit'] : 0;
				}
			}
			$index++;
		}

		return array(
			'code'        => 'WP_TRAVEL_TRIP_EXTRAS',
			'trip_extras' => $_trip_extras,
		);
	}

	public static function update_trip( $trip_id, $trip_data ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( $trip_id );

		if ( ! is_object( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( ! empty( $trip_data->pricings ) ) {
			WP_Travel_Helpers_Pricings::update_pricings( $trip_id, $trip_data->pricings );
		}

		$is_fixed_departure = ! empty( $trip_data->is_fixed_departure ) ? 'yes' : 'no';
		update_post_meta( $trip_id, 'wp_travel_fixed_departure', $is_fixed_departure );

		$dates = ( 'no' === $is_fixed_departure ) ? array() : $trip_data->dates;
		if ( ! empty( $dates ) ) {
			WP_Travel_Helpers_Trip_Dates::update_dates( $trip_id, $trip_data->dates );
		} else {
			WP_Travel_Helpers_Trip_Dates::remove_dates( $trip_id );
		}

		if ( ! empty( $trip_data->pricing_type ) ) {
			update_post_meta( $trip_id, 'wp_travel_pricing_option_type', esc_attr( $trip_data->pricing_type ) );
		}

		$trip = self::get_trip( $trip_id );

		if ( is_wp_error( $trip ) || 'WP_TRAVEL_TRIP_INFO' !== $trip['code'] ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_TRIP',
			array(
				'trip' => $trip['trip'],
			)
		);
	}
}
