<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/**
 * Return WP Travel Strings. , Modified in 2.0.9
 *
 * @since 2.0.0
 */
function wptravel_get_strings() {
	wptravel_deprecated_function( 'wptravel_get_strings', '4.6.6', 'WpTravel_Helpers_Strings::get()' );
	return WpTravel_Helpers_Strings::get();
}
