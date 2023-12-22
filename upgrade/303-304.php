<?php
/**
 * WP Data Update for above version 3.0.3
 *
 * @package WP_Travel
 */

$wptravel_migrate_304 = get_option( 'wp_travel_migrate_304' );

if ( $wptravel_migrate_304 && 'yes' === $wptravel_migrate_304 ) {
	return;
}
global $wpdb;

$wptravel_post_ids = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts}  where post_type=%s and post_status in( 'publish', 'draft' )", WP_TRAVEL_POST_TYPE ) );

if ( is_array( $wptravel_post_ids ) && count( $wptravel_post_ids ) > 0 ) {
	foreach ( $wptravel_post_ids as $wptravel_custom_post ) {
		$wptravel_trip_id = $wptravel_custom_post->ID;
		delete_site_transient( "_transient_wt_booking_count_{$wptravel_trip_id}" );
	}
	update_option( 'wp_travel_migrate_304', 'yes' );
}