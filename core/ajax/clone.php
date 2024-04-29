<?php
/**
 * Ajax request for colne trip.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class to clone.
 */
class WpTravel_Ajax_Clone {
	/**
	 * Initialize Ajax request for clone trip.
	 *
	 * @since 5.0.0
	 */
	public static function init() {

		// Clone Trip @since 1.7.6.
		add_action( 'wp_ajax_wp_travel_clone_trip', array( __CLASS__, 'clone_trip' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_clone_trip', array( __CLASS__, 'clone_trip' ) );

	}

	/**
	 * Clone.
	 *
	 * @since 5.0.1
	 */
	public static function clone_trip() {

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		// Nonce already verified above and data will be sanitize with wptravel_sanitize_array function.
		$payload = wptravel_sanitize_array( $_POST ); // @phpcs:ignore

		$response = WpTravel_Helpers_Clone::clone_trip( $payload );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WpTravel_Ajax_Clone::init();
