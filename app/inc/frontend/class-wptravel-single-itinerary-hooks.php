<?php
/**
 * Single Itinerary hooks file.
 *
 * @package WP_Travel
 */

/**
 * WpTravel_Single_Itinerary_Hooks class.
 */
class WpTravel_Single_Itinerary_Hooks {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_travel_single_trip_after_booknow', array( __CLASS__, 'replace_booknow_button' ) ); // @phpcs:ignore
		add_action( 'wptravel_single_trip_after_booknow', array( __CLASS__, 'replace_booknow_button' ) );
	}

	/**
	 * Booknow button replace function.
	 *
	 * @return void
	 */
	public static function replace_booknow_button() {
		?>
		<div id="wp-travel-booking-widget"></div>
		<?php
	}
}

