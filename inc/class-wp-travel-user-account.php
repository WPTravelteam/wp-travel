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
	 * Output of account shortcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		// global $wt_cart;

		// // Check cart class is loaded or abort.
		// if ( is_null( $wt_cart ) ) {
		// 	return;
		// }

		if ( ! is_user_logged_in() ) {

			// After password reset, add confirmation message.
			if ( ! empty( $_GET['password-reset'] ) ) {

			}

			if ( isset( $wp->query_vars['lost-password'] ) ) {
				self::lost_password();
			} else {
				// Get user login.
				wp_travel_get_template_part( 'account/form', 'login' );
			}
		} else {
			// Get user Dashboard.
			wp_travel_get_template_part( 'account/content', 'dashboard' );
		}
	}
	/**
	 * Lost password page handling.
	 */
	public static function lost_password() {
		// /**
		//  * After sending the reset link, don't show the form again.
		//  */
		// if ( ! empty( $_GET['reset-link-sent'] ) ) {
		// 	return wc_get_template( 'myaccount/lost-password-confirmation.php' );

		// 	/**
		// 	 * Process reset key / login from email confirmation link
		// 	 */
		// } elseif ( ! empty( $_GET['show-reset-form'] ) ) {
		// 	if ( isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' ) ) {
		// 		list( $rp_login, $rp_key ) = array_map( 'wc_clean', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );
		// 		$user = self::check_password_reset_key( $rp_key, $rp_login );

		// 		// reset key / login is correct, display reset password form with hidden key / login values
		// 		if ( is_object( $user ) ) {
		// 			return wc_get_template( 'myaccount/form-reset-password.php', array(
		// 				'key'   => $rp_key,
		// 				'login' => $rp_login,
		// 			) );
		// 		}
		// 	}
		// }

		// // Show lost password form by default
		// wc_get_template( 'myaccount/form-lost-password.php', array(
		// 	'form' => 'lost_password',
		// ) );
	}

}

