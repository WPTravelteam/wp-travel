<?php
/**
 * WP Data Update for above version 1.9.3
 *
 * @package wp-travel/upgrade
 */

$migrate_194 = get_option( 'wp_travel_migrate_194' );

if ( $migrate_194 && 'yes' === $migrate_194 ) {
	return;
}

$custom_post_type = WP_TRAVEL_POST_TYPE;
$query1           = "SELECT ID from {$wpdb->posts}  where post_type='$custom_post_type' and post_status in( 'publish', 'draft' )";
$post_ids         = $wpdb->get_results( $query1 );

if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
	foreach ( $post_ids as $custom_post ) {
		$custom_post_id = $custom_post->ID;

		$pricing_option_type = wp_travel_get_pricing_option_type( $custom_post_id );
		$pricing_options     = get_post_meta( $custom_post_id, 'wp_travel_pricing_options', true );

		if ( 'multiple-price' === $pricing_option_type && is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
			// Need to update wp_travel_trip_price which is used to filter by price in archive page.
			$price_key = wp_travel_get_min_price_key( $pricing_options );
			$price     = wp_travel_get_actual_trip_price( $custom_post_id, $price_key );
			if ( $price ) {
				update_post_meta( $custom_post_id, 'wp_travel_trip_price', $price );
			}
		}
	}
	update_option( 'wp_travel_migrate_194', 'yes' );
}
