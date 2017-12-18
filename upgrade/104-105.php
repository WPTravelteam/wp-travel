<?php
/**
 * Upgrade Functions.
 *
 * @package wp-travel/upgrade
 */

/**
 * Update Table meta key name.
 */
function update_table_fieldname() {
	global $wpdb;

	$query1 = "UPDATE {$wpdb->postmeta} p_postmeta SET meta_key = replace(meta_key, 'wp_traval_lat', 'wp_travel_lat')";
	$query2 = "UPDATE {$wpdb->postmeta} wp_postmeta SET meta_key = replace(meta_key, 'wp_traval_lng', 'wp_travel_lng')";
	$query3 = "UPDATE {$wpdb->postmeta} wp_postmeta SET meta_key = replace(meta_key, 'wp_traval_location', 'wp_travel_location')";
	$wpdb->get_results( $query1 );
	$wpdb->get_results( $query2 );
	$wpdb->get_results( $query3 );
}
update_table_fieldname();
