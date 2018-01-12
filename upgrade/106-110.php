<?php
/**
 * Upgrade Functions.
 *
 * @package wp-travel/upgrade
 */

/**
 * Update Table meta key name.
 */
function wp_travel_post_type_change() {
	global $wpdb;

	$query1 = "UPDATE {$wpdb->posts}  SET post_type = replace(post_type, 'itineraries', 'trip')";
	$wpdb->get_results( $query1 );
	flush_rewrite_rules();
}
// wp_travel_post_type_change();
