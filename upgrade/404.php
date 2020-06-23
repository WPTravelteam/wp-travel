<?php
/**
 * WP Travel Data Update for above version 4.0.4
 *
 * @package wp-travel/upgrade
 */
if ( 'yes' === get_option( 'wp_travel_price_migrate_404', 'no' ) ) {
	return;
}


if ( ! function_exists( 'wp_travel_migrate_data_to_404' ) ) {
	function wp_travel_migrate_data_to_404() {
		global $wpdb;
		$post_type = WP_TRAVEL_POST_TYPE;
		$post_ids  = $wpdb->get_results( "SELECT ID from {$wpdb->posts} where post_type='{$post_type}' and post_status in( 'publish', 'draft' )" );

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $trip ) {
				$trip_id    = $trip->ID;
				$trip_price = wp_travel_get_price( $trip_id ); // getting min price of trip.
				update_post_meta( $trip_id, 'wp_travel_trip_price', $trip_price );
			}
			update_option( 'wp_travel_price_migrate_404', 'yes' );
		}
	}
}

wp_travel_migrate_data_to_404();
