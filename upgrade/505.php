<?php
/**
 * WP Travel Data Update for version 5.0.2 and above.
 *
 * @package WP_Travel
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wptravel_migrate_data_to_505' ) ) {

	/**
	 * Migrate the_content in meta key wp_travel_overview.
	 */
	function wptravel_migrate_data_to_505() {
		if ( 'yes' === get_option( 'wptravel_overview_migrate_505', 'no' ) ) {
			return;
		}
		global $wpdb;
		$post_type = WP_TRAVEL_POST_TYPE;
		$post_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts} where post_type=%s and post_status in( 'publish', 'draft' )", $post_type ) );

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $trip ) {
				$trip_id       = $trip->ID;
				$tirp          = get_post( $trip_id );
				$overview      = $tirp->post_content;
				$overview_meta = get_post_meta( $trip_id, 'wp_travel_overview', true );
				if ( ! trim( $overview_meta ) ) {
					update_post_meta( $trip_id, 'wp_travel_overview', $overview );
				}
			}
			update_option( 'wptravel_overview_migrate_505', 'yes' );
		}
	}
	wptravel_migrate_data_to_505();
}