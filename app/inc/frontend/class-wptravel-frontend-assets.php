<?php
/**
 * Frontend assets file.
 *
 * @package WP Travel.
 */

/**
 * WpTravel_Frontend_Assets class.
 */
class WpTravel_Frontend_Assets {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Assets enqueue.
	 *
	 * @return void
	 */
	public static function assets() {
		if ( is_singular( 'itineraries' ) ) {
			global $post;
			$deps = include_once sprintf( '%sapp/build/frontend-booking-widget.asset.php', WP_TRAVEL_ABSPATH );
			if ( ! wptravel_can_load_bundled_scripts() ) {
				$deps['dependencies'][] = 'jquery-datepicker-lib';
			} else {
				$deps['dependencies'][] = 'wp-travel-frontend-bundle';
			}
			$suffix = wptravel_script_suffix();
			wp_register_script( 'wp-travel-frontend-booking-widget', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build/frontend-booking-widget' . $suffix . '.js', $deps['dependencies'], $deps['version'], true );
			wp_enqueue_style( 'wp-travel-frontend-booking-widget-style', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build/frontend-booking-widget' . $suffix . '.css', array(), $deps['version'] );
			wp_enqueue_style( 'wp-travel-frontend-main-style', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build/main.css', array(), $deps['version'] );

			// Localize the script with new data.
			$localized_string = array();
			$settings         = wptravel_get_settings();

			$trip = WP_Travel_Helpers_Trips::get_trip( $post->ID );
			if ( ! is_wp_error( $trip ) && 'WP_TRAVEL_TRIP_INFO' === $trip['code'] ) {
				$localized_string['trip_data']          = $trip['trip'];
				$localized_string['currency']           = $settings['currency'];
				$localized_string['currency_symbol']    = wptravel_get_currency_symbol();
				$localized_string['cart_url']           = wptravel_get_cart_url();
				$localized_string['ajax_url']           = admin_url( 'admin-ajax.php' );
				$localized_string['_nonce']             = wp_create_nonce( 'wp_travel_nonce' );
				$localized_string['currency_position']  = $settings['currency_position'];
				$localized_string['thousand_separator'] = $settings['thousand_separator'] ? $settings['thousand_separator'] : ',';
				$localized_string['decimal_separator']  = $settings['decimal_separator'] ? $settings['decimal_separator'] : '.';
				$localized_string['number_of_decimals'] = $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0;
				$localized_string['date_format']        = get_option( 'date_format' );
				$localized_string['date_format_moment'] = wptravel_php_to_moment_format( get_option( 'date_format' ) );
				$localized_string['time_format']        = get_option( 'time_format' );
				$localized_string['trip_date_listing']  = $settings['trip_date_listing'];
				$localized_string['build_path']         = esc_url( trailingslashit( plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build' ) );
			}
			$localized_string['strings']      = wptravel_get_strings();
			$localized_string['itinerary_v2'] = wptravel_use_itinerary_v2_layout();
			wp_localize_script( 'wp-travel-frontend-booking-widget', '_wp_travel', $localized_string );

			wp_enqueue_script( 'wp-travel-frontend-booking-widget' );
		}
	}
}

WpTravel_Frontend_Assets::init();

