<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

function wp_travel_get_default_frontend_tabs( $is_show_in_menu_query = false ) {
	wp_travel_deprecated_function( 'wp_travel_get_default_frontend_tabs', '1.9.3', 'wptravel_get_default_trip_tabs' );
	return wptravel_get_default_trip_tabs( $is_show_in_menu_query );
}
