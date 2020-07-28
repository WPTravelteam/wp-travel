<?php
class WP_Travel_Frontend_Assets {
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function assets() {
		if ( is_singular( 'itineraries' ) ) {
			global $post;
			$deps                   = include_once sprintf( '%sapp/build/frontend-booking-widget.asset.php', WP_TRAVEL_ABSPATH );
			if ( ! wp_travel_can_load_bundled_scripts() ) {
				$deps['dependencies'][] = 'jquery-datepicker-lib';
			} else {
				$deps['dependencies'][] = 'wp-travel-frontend-bundle';
			}
			wp_register_script( 'wp-travel-frontend-booking-widget', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build/frontend-booking-widget.js', $deps['dependencies'], $deps['version'], true );
			wp_enqueue_style( 'wp-travel-frontend-booking-widget-style', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build/frontend-booking-widget.css', array(), $deps['version'] );

			// Localize the script with new data
			$translation_array = array();
			$settings          = wp_travel_get_settings();

			$trip = WP_Travel_Helpers_Trips::get_trip( $post->ID );
			if ( ! is_wp_error( $trip ) && 'WP_TRAVEL_TRIP_INFO' === $trip['code'] ) {
				$translation_array['trip_data']          = $trip['trip'];
				$translation_array['currency']           = $settings['currency'];
				$translation_array['currency_symbol']    = wp_travel_get_currency_symbol();
				$translation_array['cart_url']           = wp_travel_get_cart_url();
				$translation_array['ajax_url']           = admin_url( 'admin-ajax.php' );
				$translation_array['_nonce']             = wp_create_nonce( 'wp_travel_nonce' );
				$translation_array['currency_position']  = $settings['currency_position'];
				$translation_array['thousand_separator'] = $settings['thousand_separator'];
				$translation_array['decimal_separator']  = $settings['decimal_separator'];
				$translation_array['number_of_decimals'] = $settings['number_of_decimals'];
				$translation_array['date_format']        = get_option( 'date_format' );
				$translation_array['time_format']        = get_option( 'time_format' );
			}
			$translation_array['strings'] = wp_travel_get_strings();
			wp_localize_script( 'wp-travel-frontend-booking-widget', '_wp_travel', $translation_array );

			wp_enqueue_script( 'wp-travel-frontend-booking-widget' );
		}
	}
}

WP_Travel_Frontend_Assets::init();
