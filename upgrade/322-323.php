<?php

$price_migrated = get_option( 'wp_travel_price_migrate_323' );

if ( $price_migrated && 'yes' === $price_migrated ) {
	return;
}
global $wpdb;

$post_type = WP_TRAVEL_POST_TYPE;
$query1    = "SELECT ID from {$wpdb->posts}  where post_type='$post_type' and post_status in( 'publish', 'draft' )";
$post_ids  = $wpdb->get_results( $query1 );
if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
	foreach ( $post_ids as $trip ) {
		$trip_id    = $trip->ID;
		$args       = array(
			'trip_id' => $trip_id,
		);
		$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );

		update_post_meta( $trip_id, 'wp_travel_trip_price', $trip_price );
	}
	update_option( 'wp_travel_price_migrate_323', 'yes' );
}