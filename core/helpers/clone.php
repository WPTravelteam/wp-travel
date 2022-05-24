<?php
/**
 * Helpers clone.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;
/**
 * WP_Travel_Helpers_Clone class.
 */
class WpTravel_Helpers_Clone {

	/**
	 * Clone all trip and its data.
	 *
	 * @param  array $payload All request data for clone trip.
	 * @return bool
	 */
	/**
	 * Ajax callback function to clone trip. use instead.
	 *
	 * @since 1.7.6
	 * @deprecated 5.0.1
	 */
	public static function clone_trip( $payload ) {
		if ( ! isset( $payload['post_id'] ) ) {
			return;
		}
		global $wpdb;

		$trip_id   = absint( $payload['post_id'] );
		$post_type = get_post_type( $trip_id );

		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}
		$trip = get_post( $trip_id );

		$post_array = array(
			'post_title'   => $trip->post_title,
			'post_content' => $trip->post_content,
			'post_status'  => 'draft',
			'post_type'    => WP_TRAVEL_POST_TYPE,
			'post_excerpt' => $trip->post_excerpt,
		);

		// Cloning trip.
		$new_trip_id = wp_insert_post( $post_array );

		// Cloning trip meta.
		$all_old_meta = get_post_meta( $trip_id );

		if ( is_array( $all_old_meta ) && count( $all_old_meta ) > 0 ) {
			foreach ( $all_old_meta as $meta_key => $meta_value_array ) {
				if ( 'wp_travel_booking_count' === $meta_key ) {
					continue;
				}
				$meta_value = isset( $meta_value_array[0] ) ? $meta_value_array[0] : '';

				if ( '' !== $meta_value ) {
					$meta_value = maybe_unserialize( $meta_value );
				}
				if ( 'wp_travel_trip_code' === $meta_key ) {
					$meta_value = str_replace( $trip_id, $new_trip_id, $meta_value );
				}
				update_post_meta( $new_trip_id, $meta_key, $meta_value );
			}
		}

		// Cloning taxonomies.
		$trip_taxonomies = array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' );
		$custom_filters  = get_option( 'wp_travel_custom_filters_option', array() );
		if ( is_array( $custom_filters ) && ! empty( $custom_filters ) ) {
			foreach ( $custom_filters as $slug => $value ) {
				$trip_taxonomies[] = $slug;
			}
		}
		foreach ( $trip_taxonomies as $taxonomy ) {
			$trip_terms      = get_the_terms( $trip_id, $taxonomy );
			$trip_term_names = array();
			if ( is_array( $trip_terms ) && count( $trip_terms ) > 0 ) {
				foreach ( $trip_terms as $post_terms ) {
					$trip_term_names[] = $post_terms->name;
				}
			}
			wp_set_object_terms( $new_trip_id, $trip_term_names, $taxonomy );
		}

		// Clone Price table data.
		$pricing_ids = array(); // To add cloned pricing ids into cloned dates table.
		$pricings    = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_pricings WHERE trip_id=%d", $trip_id ) );
		if ( ! empty( $pricings ) ) {
			foreach ( $pricings as $pricing ) {
				$pricing_data = array(
					'title'           => $pricing->title,
					'max_pax'         => $pricing->max_pax,
					'min_pax'         => $pricing->min_pax,
					'has_group_price' => $pricing->has_group_price,
					'group_prices'    => maybe_unserialize( $pricing->group_prices ),
					'trip_extras'     => $pricing->trip_extras,
				);

				$pricing_id              = $pricing->id;
				$pricing_insert_response = WpTravel_Helpers_Pricings::add_individual_pricing( $new_trip_id, $pricing_data );

				if ( is_array( $pricing_insert_response ) && isset( $pricing_insert_response['code'] ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $pricing_insert_response['code'] ) {
					$new_pricing_id     = $pricing_insert_response['pricing_id'];
					$pricing_categories = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_price_category_relation WHERE `pricing_id` = %d", $pricing_id ) );
					if ( ! empty( $pricing_categories ) ) {
						foreach ( $pricing_categories as $pricing_category ) {
							$category = array(
								'id'              => $pricing_category->pricing_category_id,
								'price_per'       => $pricing_category->price_per,
								'regular_price'   => $pricing_category->regular_price,
								'is_sale'         => $pricing_category->is_sale,
								'sale_price'      => $pricing_category->sale_price,
								'has_group_price' => $pricing_category->has_group_price,
								'group_prices'    => maybe_unserialize( $pricing_category->group_prices ),
								'default_pax'     => $pricing_category->default_pax,
							);
							WpTravel_Helpers_Trip_Pricing_Categories::add_individual_pricing_category( $new_pricing_id, $category );
						}
					}
					$pricing_ids[ $new_pricing_id ] = $pricing_id; // assign pricing id to used in date table.
				}
			}
		}
		// Date Migration.
		$dates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_dates WHERE `trip_id` = %d", $trip_id ) );
		if ( ! empty( $dates ) ) {
			foreach ( $dates as $date ) {

				// Add new pricing id in newly inserted date.
				$pricing_ids_of_date = $date->pricing_ids;
				$new_temp_ids        = array();
				if ( ! empty( $pricing_ids_of_date ) ) {
					$temp_ids = explode( ',', $pricing_ids_of_date );
					foreach ( $temp_ids as $temp_id ) {
						if ( false !== $key = array_search( $temp_id, $pricing_ids ) ) {
							$new_temp_ids[] = $key;
						}
					}
				}
				$new_pricing_ids = implode( ',', $new_temp_ids );

				$new_date = array(
					'title'        => $date->title,
					'is_recurring' => $date->recurring,
					'years'        => $date->years,
					'months'       => $date->months,
					'weeks'        => $date->weeks,
					'days'         => $date->days,
					'date_days'    => $date->date_days,
					'start_date'   => $date->start_date,
					'end_date'     => $date->end_date,
					'trip_time'    => $date->trip_time,
					'pricing_ids'  => $new_pricing_ids,
				);
				WpTravel_Helpers_Trip_Dates::add_individual_date( $new_trip_id, $new_date );
			}
		}
		// Exclued Date Migration.
		$exclude_dates = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_excluded_dates_times WHERE `trip_id` = %d", $trip_id ) );
		if ( ! empty( $exclude_dates ) ) {
			foreach ( $exclude_dates as $exclude_date ) {
				$new_exclude_date = array(
					'title'      => $exclude_date->title,
					'recurring'  => $exclude_date->recurring,
					'years'      => $exclude_date->years,
					'months'     => $exclude_date->months,
					'weeks'      => $exclude_date->weeks,
					'days'       => $exclude_date->days,
					'date_days'  => $exclude_date->date_days,
					'start_date' => $exclude_date->start_date,
					'end_date'   => $exclude_date->end_date,
					'trip_time'  => $exclude_date->time,
				);
				WpTravel_Helpers_Trip_Excluded_Dates_Times::add_individual_date_time( $new_trip_id, $new_exclude_date );
			}
		}
		wp_send_json( array( 'true' ) );
	}
}
