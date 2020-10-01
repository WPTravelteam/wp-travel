<?php
class WP_Travel_Localize_Admin {
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'localize_data' ) );
	}

	public static function localize_data() {
		$screen                = get_current_screen();
		$allowed_screen        = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );
		

		$translation_array = array(
			'_nonce'    => wp_create_nonce( 'wp_travel_nonce' ),
			'admin_url' => admin_url(),
		);
		// trip edit page.
		if ( in_array( $screen->id, $allowed_screen ) ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-admin-trip-options', '_wp_travel', $translation_array );
		} 
		$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true );
		if ( $react_settings_enable && 'itinerary-booking_page_settings2' == $screen->id ) { // settings page
			wp_localize_script( 'wp-travel-admin-settings', '_wp_travel', $translation_array );
		}
	}
}

WP_Travel_Localize_Admin::init();
