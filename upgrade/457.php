<?php
/**
 * WP Travel Data Update for above version 4.0.4
 *
 * @package WP_Travel
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wptravel_migrate_data_to_457' ) ) {

	/**
	 * Update Enable Sale meta.
	 */
	function wptravel_migrate_data_to_457() {
		if ( 'yes' === get_option( 'wptravel_price_migrate_457', 'no' ) ) {
			return;
		}
		global $wpdb;
		$post_type = WP_TRAVEL_POST_TYPE;
		$post_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts} where post_type=%s and post_status in( 'publish', 'draft' )", $post_type ) );

		if ( is_array( $post_ids ) ) {
			foreach ( $post_ids as $trip ) {
				$trip_id = $trip->ID;
				$args    = array(
					'trip_id' => $trip_id,
				);
				$enable  = WpTravel_Helpers_Trips::is_sale_enabled( $args );
				update_post_meta( $trip_id, 'wptravel_enable_sale', $enable );
			}
			update_option( 'wptravel_price_migrate_457', 'yes' );
		}
	}
	wptravel_migrate_data_to_457();
}