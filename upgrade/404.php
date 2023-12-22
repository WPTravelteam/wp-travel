<?php
/**
 * WP Travel Data Update for above version 4.0.4
 *
 * @package WP_Travel
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( 'yes' === get_option( 'wp_travel_price_migrate_404', 'no' ) ) {
	return;
}

if ( ! function_exists( 'wptravel_migrate_data_to_404' ) ) {

	/**
	 * Migrate old trip prices to new database in version 4.0.4
	 */
	function wptravel_migrate_data_to_404() {
		global $wpdb;
		$post_type = WP_TRAVEL_POST_TYPE;
		$post_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts} where post_type=%s and post_status in( 'publish', 'draft' )", $post_type ) );

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $trip ) {
				$trip_id    = $trip->ID;
				$args       = array(
					'trip_id' => $trip_id,
				);
				$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
				update_post_meta( $trip_id, 'wp_travel_trip_price', $trip_price );
			}
			update_option( 'wp_travel_price_migrate_404', 'yes' );
		}
	}
}

wptravel_migrate_data_to_404();