<?php


$itineraries = get_posts(
	array(
		'post_type'   => 'itineraries',
		'post_status' => 'publish',
	)
);
if ( count( $itineraries ) > 0 ) {
	foreach ( $itineraries as $itinerary ) {
		$post_id    = $itinerary->ID;
		$trip_price = get_post_meta( $post_id, 'wptravel_trip_price', true );
		if ( $trip_price > 0 ) {
			continue;
		}

		$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );

		if ( $enable_sale ) {
			$trip_price = get_post_meta( $post_id, 'wp_travel_sale_price', true );
		} else {
			$trip_price = get_post_meta( $post_id, 'wp_travel_price', true );
		}
		update_post_meta( $post_id, 'wptravel_trip_price', $trip_price );
	}
}
// Added Date Formatting for filter.
if ( count( $itineraries ) > 0 ) {
	foreach ( $itineraries as $itinerary ) {
		$post_id         = $itinerary->ID;
		$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
		if ( 'no' == $fixed_departure ) {
			continue;
		}
		$wp_travel_start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
		$wp_travel_end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );

		if ( '' !== $wp_travel_start_date ) {

			$wp_travel_start_date = strtotime( $wp_travel_start_date );
			$wp_travel_start_date = date( 'Y-m-d', $wp_travel_start_date );
			update_post_meta( $post_id, 'wp_travel_start_date', $wp_travel_start_date );
		}

		if ( '' !== $wp_travel_end_date ) {

			$wp_travel_end_date = strtotime( $wp_travel_end_date );
			$wp_travel_end_date = date( 'Y-m-d', $wp_travel_end_date );
			update_post_meta( $post_id, 'wp_travel_end_date', $wp_travel_end_date );
		}
	}
}
