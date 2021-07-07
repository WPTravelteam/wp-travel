<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/**
 * Wp Travel Process Trip Price Tax.
 *
 * @param  int $post_id post id.
 * @param  int $price Price.
 * @return mixed Price or tax detail.
 */
function wptravel_process_trip_price_tax_by_price( $post_id, $price ) {

	wptravel_deprecated_function( 'wptravel_process_trip_price_tax_by_price', '4.4.9' );
	if ( ! $post_id || ! $price ) {
		return 0;
	}
	$settings = wptravel_get_settings();

	$trip_price = $price;

	if ( WP_Travel_Helpers_Trips::is_tax_enabled() ) {

		$tax_details         = array();
		$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
		$trip_price          = $price;
		$tax_percentage      = isset( $settings['trip_tax_percentage'] ) ? $settings['trip_tax_percentage'] : 13;

		if ( 0 === $trip_price || '' === $tax_percentage ) {

			return array( 'trip_price' => $trip_price );
		}

		if ( 'yes' === $tax_inclusive_price ) {

			$tax_details['tax_type']          = 'inclusive';
			$tax_details['tax_percentage']    = $tax_percentage;
			$actual_trip_price                = ( 100 * $trip_price ) / ( 100 + $tax_percentage );
			$tax_details['trip_price']        = $actual_trip_price;
			$tax_details['actual_trip_price'] = $trip_price;

			return $tax_details;

		} else {

			$tax_details['tax_type']          = 'excluxive';
			$tax_details['trip_price']        = $trip_price;
			$tax_details['tax_percentage']    = $tax_percentage;
			$tax_details['actual_trip_price'] = number_format( ( $trip_price + ( ( $trip_price * $tax_percentage ) / 100 ) ), 2, '.', '' );

			return $tax_details;
		}
	}
	return array( 'trip_price' => $trip_price );
}

/**
 * Get Map Data
 *
 * @param Number $trip_id Trip id.
 *
 * @return Array
 */
function get_wp_travel_map_data( $trip_id = null ) { // @phpcs:ignore
	wptravel_deprecated_function( 'get_wp_travel_map_data', '4.4.9', 'wptravel_get_map_data' );
	return wptravel_get_map_data( $trip_id );
}
