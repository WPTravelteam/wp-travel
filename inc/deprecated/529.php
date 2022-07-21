<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/**
 * Default Pricing content. Unused @since 4.0.0.
 *
 * @deprecated 5.2.9
 */
function wptravel_booking_default_princing_list_content( $trip_id ) {

	if ( '' == $trip_id ) {
		return;
	}
	wptravel_deprecated_function( 'wptravel_booking_default_princing_list_content', '5.2.9' );
}

/**
 * Listings by Departure Date. Unused @since 4.0.0.
 *
 * @deprecated 5.2.9
 */
function wptravel_booking_fixed_departure_list_content( $trip_id ) {
	if ( '' == $trip_id ) {
		return;
	}
	wptravel_deprecated_function( 'wptravel_booking_fixed_departure_list_content', '5.2.9' );
}

