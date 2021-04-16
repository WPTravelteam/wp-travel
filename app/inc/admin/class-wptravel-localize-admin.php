<?php
/**
 * Admin Localize file.
 *
 * @package wp-travel/upgrade.
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
	 * @return void
	 */
	public static function localize_data() {
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );

		$translation_array = array(
			'_nonce'    => wp_create_nonce( 'wp_travel_nonce' ),
			'admin_url' => admin_url(),
			'dev_mode'  => wptravel_dev_mode()
		);
		// trip edit page.
		if ( in_array( $screen->id, $allowed_screen, true ) ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-admin-trip-options', '_wp_travel', $translation_array );
		}

		// Coupon Page.
		if ( 'wp-travel-coupons' === $screen->id ) {
			wp_localize_script( 'wp-travel-coupons-backend-js', '_wp_travel', $translation_array );
		}

		$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true ); // @phpcs:ignore
		$react_settings_enable = apply_filters( 'wptravel_settings_react_enabled', $react_settings_enable );
		if ( $react_settings_enable && WP_Travel::is_page( 'settings', true ) ) { // settings page.
			wp_localize_script( 'wp-travel-admin-settings', '_wp_travel', $translation_array );
		}
	}
}

WpTravel_Localize_Admin::init();
