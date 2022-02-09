<?php
/**
 * Admin Localize file.
 *
 * @package WP_Travel
 */

/**
 * WpTravel_Localize_Admin class.
 */
class WpTravel_Localize_Admin {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'localize_data' ) );
	}

	/**
	 * Localize data function.
	 *
	 * // @todo Need to Move this into into WpTravel_Helpers_Localize::get(); of WpTravel_Frontend_Assets class.
	 *
	 * @return void
	 */
	public static function localize_data() {
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries', 'wptravel_template', 'edit-wptravel_template' );
		$settings       = wptravel_get_settings();

		$translation_array = array(
			'_nonce'             => wp_create_nonce( 'wp_travel_nonce' ),
			'admin_url'          => admin_url(),
			'dev_mode'           => wptravel_dev_mode(),
			'currency'           => $settings['currency'],
			'currency_position'  => $settings['currency_position'],
			'currency_symbol'    => wptravel_get_currency_symbol(),
			'number_of_decimals' => $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0,
			'decimal_separator'  => $settings['decimal_separator'] ? $settings['decimal_separator'] : '.',
			'thousand_separator' => $settings['thousand_separator'] ? $settings['thousand_separator'] : ',',
		);
		//print_r($screen->id);die;
		// trip edit page.
		if ( in_array( $screen->id, $allowed_screen, true ) ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-admin-trip-options', '_wp_travel', $translation_array );
		}

		// Coupon Page.
		if ( 'wp-travel-coupons' === $screen->id ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-coupons-backend-js', '_wp_travel', $translation_array );
		}

		$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true ); // @phpcs:ignore
		$react_settings_enable = apply_filters( 'wptravel_settings_react_enabled', $react_settings_enable );
		if ( $react_settings_enable && WP_Travel::is_page( 'settings', true ) ) { // settings page.
		}
		wp_localize_script( 'wp-travel-admin-settings', '_wp_travel', $translation_array );  // temp fixes to use localized data.
	}
}

WpTravel_Localize_Admin::init();
