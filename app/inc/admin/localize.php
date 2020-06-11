<?php
class WP_Travel_Localize_Admin {
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'localize_data' ) );
	}

	public static function localize_data() {
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );
		if ( in_array( $screen->id, $allowed_screen ) ) {
			$translation_array = array(
				'postID' => get_the_ID(),
				'_nonce' => wp_create_nonce( 'wp_travel_nonce' ),
				'admin_url' => admin_url(),
			);
			wp_localize_script( 'wp-travel-admin-trip-options', '_wp_travel', $translation_array );
		}
	}
}

WP_Travel_Localize_Admin::init();
