<?php
/**
 * WP Data Update for above version 3.0.3
 *
 * @package wp-travel/upgrade
 */

$migrate_304 = get_option( 'wp_travel_migrate_304' );

if ( $migrate_304 && 'yes' === $migrate_304 ) {
	return;
}

$custom_post_type = WP_TRAVEL_POST_TYPE;
$query1           = "SELECT ID from {$wpdb->posts}  where post_type='$custom_post_type' and post_status in( 'publish', 'draft' )";
$post_ids         = $wpdb->get_results( $query1 );

if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
	foreach ( $post_ids as $custom_post ) {
		$trip_id = $custom_post->ID;
		delete_site_transient( "_transient_wt_booking_count_{$trip_id}" );
	}
	update_option( 'wp_travel_migrate_304', 'yes' );
}
