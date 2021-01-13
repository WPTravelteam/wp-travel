<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/deprecated
 */

function wp_travel_get_pricing_name_by_key( $key = null ) {
	wp_travel_deprecated_function( 'wp_travel_get_pricing_name_by_key', '3.0.7', 'wp_travel_get_pricing_category_by_key' );
	return wp_travel_get_default_trip_tabs( $key );
}
