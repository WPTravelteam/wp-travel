<?php
/**
 * WP Data Update for above version 1.9.3
 *
 * @package WP_Travel
 */

$wptravel_migrate_194 = get_option( 'wp_travel_migrate_194' );

if ( $wptravel_migrate_194 && 'yes' === $wptravel_migrate_194 ) {
	return;
}
global $wpdb;

$wptravel_custom_post_type = WP_TRAVEL_POST_TYPE;
$wptravel_post_ids         = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts}  where post_type=%s and post_status in( 'publish', 'draft' )", $wptravel_custom_post_type ) );

if ( is_array( $wptravel_post_ids ) && count( $wptravel_post_ids ) > 0 ) {
	foreach ( $wptravel_post_ids as $wptravel_custom_post ) {
		$wptravel_custom_post_id = $wptravel_custom_post->ID;

		$wptravel_pricing_option_type = wptravel_get_pricing_option_type( $wptravel_custom_post_id );
		$wptravel_pricing_options     = get_post_meta( $wptravel_custom_post_id, 'wp_travel_pricing_options', true );

		if ( 'multiple-price' === $wptravel_pricing_option_type && is_array( $wptravel_pricing_options ) && count( $wptravel_pricing_options ) > 0 ) {
			$wptravel_args  = array( 'trip_id' => $wptravel_custom_post_id );
			$wptravel_price = WP_Travel_Helpers_Pricings::get_price( $wptravel_args );
			if ( $wptravel_price ) {
				update_post_meta( $wptravel_custom_post_id, 'wp_travel_trip_price', $wptravel_price );
			}
		}
	}
	update_option( 'wp_travel_migrate_194', 'yes' );
}
