<?php
/**
 * Upgrade Functions.
 *
 * @package WP_Travel
 */

/**
 * Update Table meta key name.
 */
function wptravel_update_table_fieldname() {
	global $wpdb;

	$wpdb->get_results( "UPDATE {$wpdb->postmeta} p_postmeta SET meta_key = replace(meta_key, 'wp_traval_lat', 'wp_travel_lat')" );
	$wpdb->get_results( "UPDATE {$wpdb->postmeta} wp_postmeta SET meta_key = replace(meta_key, 'wp_traval_location', 'wp_travel_location')" );
	$wpdb->get_results( "UPDATE {$wpdb->postmeta} wp_postmeta SET meta_key = replace(meta_key, 'wp_traval_location', 'wp_travel_location')" );
}
wptravel_update_table_fieldname();
