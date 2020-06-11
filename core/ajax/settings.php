<?php
class WP_Travel_Ajax_Settings {

	/**
	 * Initialize Ajax requests.
	 */
	public static function init() {
		 // Remove item from cart.
		add_action( 'wp_ajax_wp_travel_get_settings', array( __CLASS__, 'get_settings' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_settings', array( __CLASS__, 'get_settings' ) );
	}

	public static function get_settings() {
		/**
		 * Permission Check
		 */

		$permission = self::get_settings_permissions_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$response = WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_SETTINGS',
			array(
				'settings' => wp_travel_get_settings(),
			)
		);
		WP_Travel_Helpers_REST_API::response( $response );
	}
	public static function get_settings_permissions_check() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'wp_travel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}

		return true;
	}
}

WP_Travel_Ajax_Settings::init();
