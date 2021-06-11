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

		// Force Migrate to v4.
		add_action( 'wp_ajax_wptravel_force_migrate', array( __CLASS__, 'force_migrate_to_v4' ) );
		add_action( 'wp_ajax_nopriv_wptravel_force_migrate', array( __CLASS__, 'force_migrate_to_v4' ) );
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

	public static function force_migrate_to_v4() {
		/**
		 * Permission Check
		 */
		WP_Travel::verify_nonce();

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		if ( isset( $post_data['force_migrate_to_v4'] ) && $post_data['force_migrate_to_v4'] ) {
			if ( ! function_exists( 'wptravel_update_to_400' ) ) {
				WP_Travel_Actions_Activation::migrations(); // to include functin defination.
			}
			$response = wptravel_update_to_400( @$network_enabled, true );
			WP_Travel_Helpers_REST_API::response( $response );
		}
	}
}

WP_Travel_Ajax_Settings::init();
