<?php
/**
 * Upgrade functions.
 *
 * @package WP_Travel
 */

$wptravel_itineraries = get_posts(
	array(
		'post_type'   => 'itineraries',
		'post_status' => 'publish',
	)
);
if ( count( $wptravel_itineraries ) > 0 ) {
	foreach ( $wptravel_itineraries as $wptravel_itinerary ) {
		$wptravel_post_id    = $wptravel_itinerary->ID;
		$wptravel_trip_price = get_post_meta( $wptravel_post_id, 'wp_travel_trip_price', true );
		if ( $wptravel_trip_price > 0 ) {
			continue;
		}

		$wptravel_enable_sale = get_post_meta( $wptravel_post_id, 'wp_travel_enable_sale', true );

		if ( $wptravel_enable_sale ) {
			$wptravel_trip_price = get_post_meta( $wptravel_post_id, 'wp_travel_sale_price', true );
		} else {
			$wptravel_trip_price = get_post_meta( $wptravel_post_id, 'wp_travel_price', true );
		}
		update_post_meta( $wptravel_post_id, 'wp_travel_trip_price', $wptravel_trip_price );
	}
}
// Added Date Formatting for filter.
if ( count( $wptravel_itineraries ) > 0 ) {
	foreach ( $wptravel_itineraries as $wptravel_itinerary ) {
		$wptravel_post_id         = $wptravel_itinerary->ID;
		$wptravel_fixed_departure = get_post_meta( $wptravel_post_id, 'wp_travel_fixed_departure', true );
		if ( 'no' === $wptravel_fixed_departure ) {
			continue;
		}
		$wptravel_start_date = get_post_meta( $wptravel_post_id, 'wp_travel_start_date', true );
		$wptravel_end_date   = get_post_meta( $wptravel_post_id, 'wp_travel_end_date', true );

		if ( '' !== $wptravel_start_date ) {

			$wptravel_start_date = strtotime( $wptravel_start_date );
			$wptravel_start_date = gmdate( 'Y-m-d', $wptravel_start_date );
			update_post_meta( $wptravel_post_id, 'wp_travel_start_date', $wptravel_start_date );
		}

		if ( '' !== $wptravel_end_date ) {

			$wptravel_end_date = strtotime( $wptravel_end_date );
			$wptravel_end_date = gmdate( 'Y-m-d', $wptravel_end_date );
			update_post_meta( $wptravel_post_id, 'wp_travel_end_date', $wptravel_end_date );
		}
	}
}
