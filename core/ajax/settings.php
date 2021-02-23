<?php
class WP_Travel_Ajax_Settings {

	/**
	 * Initialize Ajax requests.
	 */
	public static function init() {
		 // get settings.
		add_action( 'wp_ajax_wptravel_get_settings', array( __CLASS__, 'get_settings' ) );
		add_action( 'wp_ajax_nopriv_wptravel_get_settings', array( __CLASS__, 'get_settings' ) );

		// Update settings.
		add_action( 'wp_ajax_wp_travel_update_settings', array( __CLASS__, 'update_settings' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_settings', array( __CLASS__, 'update_settings' ) );
		
	}

	public static function get_settings() {
		/**
		 * Permission Check
		 */

		WP_Travel::verify_nonce();

		$response = WP_Travel_Helpers_Settings::get_settings();

		WP_Travel_Helpers_REST_API::response( $response );
	}


	public static function update_settings() {
		/**
		 * Permission Check
		 */

		WP_Travel::verify_nonce();

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		$response  = WP_Travel_Helpers_Settings::update_settings( $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Settings::init();
