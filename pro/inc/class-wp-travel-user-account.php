<?php
/**
 * Wp_Travel_User_Account.
 *
 * @package WP Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Travel Checkout Shortcode Class.
 */
class Wp_Travel_User_Account {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Output of checkout shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		global $wt_cart;


		// Check cart class is loaded or abort
		if ( is_null( $wt_cart ) ) {
			return;
		}

		if ( ! is_user_logged_in() ) {

			// After password reset, add confirmation message.
			if ( ! empty( $_GET['password-reset'] ) ) {

			}

			if ( isset( $wp->query_vars['lost-password'] ) ) {
				self::lost_password();
			} else {
				// Get user login.

				include sprintf( '%s/templates/account/form-login.php', WP_TRAVEL_PRO_ABSPATH );
			}
		}
	}
	/**
	 * Lost Password Form.
	 */
	public static function lost_password() {


	}

}

