<?php
/**
 * Depricated Functions.
 *
 * @package wp-travel/inc/deprecated
 */


/**
 * Get Map Data
 *
 * @param Number $trip_id Trip id.
 *
 * @return Array
 */
function wptravel_get_strings() {
	wptravel_deprecated_function( 'wptravel_get_strings', '4.6.4', 'WpTravel_Helpers_Strings::get()' );
	return WpTravel_Helpers_Strings::get();
}
