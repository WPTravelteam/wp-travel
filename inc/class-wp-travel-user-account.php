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
	 * Dashboard menus.
	 *
	 * @return array Menus.
	 */
	private static function dashboard_menus() {
		$dashboard_menus = array(
			'dashboard' => array(
				'menu_title' => __( 'Dashboard', 'wp-travel' ),
				'menu_icon' => 'wt-icon wt-icon-tachometer',
				'menu_content_cb' => array( __CLASS__, 'dashboard_menu_dashboard_tab' ),
				'priority' => 10,
			),
			'bookings' => array(
				'menu_title' => __( 'Bookings', 'wp-travel' ),
				'menu_icon' => 'wt-icon wt-icon-th-list',
				'menu_content_cb' => array( __CLASS__, 'dashboard_menu_bookings_tab' ),
				'priority' => 20,
			),
			'address' => array(
				'menu_title' => __( 'Address', 'wp-travel' ),
				'menu_icon' => 'wt-icon-regular wt-icon-address-book',
				'menu_content_cb' => array( __CLASS__, 'dashboard_menu_address_tab' ),
				'priority' => 30,
			),
			'account' => array(
				'menu_title' => __( 'Account', 'wp-travel' ),
				'menu_icon' => 'wt-icon wt-icon-user',
				'menu_content_cb' => array( __CLASS__, 'dashboard_menu_account_tab' ),
				'priority' => 40,
			),
			'logout' => array(
				'menu_title' => __( 'Logout', 'wp-travel' ),
				'menu_icon' => 'wt-icon wt-icon-power-off',
				'menu_content_cb' => array( __CLASS__, 'dashboard_menu_logout_tab' ),
				'priority' => 50,
			),
		);
		return $dashboard_menus = apply_filters( 'wp_travel_user_dashboard_menus', $dashboard_menus );
	}

	public static function dashboard_menu_dashboard_tab( $args ) {
		echo wp_travel_get_template_html( 'account/tab-content/dashboard.php', $args );
	}

	public static function dashboard_menu_bookings_tab( $args ) {
		echo wp_travel_get_template_html( 'account/tab-content/bookings.php', $args );
	}

	public static function dashboard_menu_address_tab( $args ) {
		echo wp_travel_get_template_html( 'account/tab-content/address.php', $args );
	}

	public static function dashboard_menu_account_tab( $args ) {
		echo wp_travel_get_template_html( 'account/tab-content/account.php', $args );
	}

	public static function dashboard_menu_logout_tab( $args ) {
		echo wp_travel_get_template_html( 'account/tab-content/logout.php', $args );
	}

	/**
	 * Output of account shortcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {

		global $wp;

		if ( ! is_user_logged_in() ) {

			// After password reset, add confirmation message.
			if ( ! empty( $_GET['password-reset'] ) ) { ?>

				<p class="col-xs-12 wp-travel-notice-success wp-travel-notice"><?php esc_html_e( 'Your Password has been updated successfully. Please Log in to continue.', 'wp-travel' ); ?></p>

			<?php

			}
			if ( isset( $_GET['action'] ) && 'lost-pass' == $_GET['action'] ) {
				self::lost_password();
			} else {
				// Get user login.
				wp_travel_get_template_part( 'account/form', 'login' );
			}
		} else {
			$current_user = wp_get_current_user();
			$args['current_user'] = $current_user;
			$args['dashboard_menus'] = self::dashboard_menus();
			// Get user Dashboard.
			echo wp_travel_get_template_html( 'account/content-dashboard.php', $args );
		}
	}
	/**
	 * Lost password page handling.
	 */
	public static function lost_password() {
		/**
		 * After sending the reset link, don't show the form again.
		 */
		if ( ! empty( $_GET['reset-link-sent'] ) ) {

			wp_travel_get_template_part( 'account/lostpassword', 'confirm' );

			return;
			/**
			 * Process reset key / login from email confirmation link
			*/
		} elseif ( ! empty( $_GET['show-reset-form'] ) ) {
			if ( isset( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ) && 0 < strpos( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ], ':' ) ) {
				list( $rp_login, $rp_key ) = array_map( 'wp_travel_clean_vars', explode( ':', wp_unslash( $_COOKIE[ 'wp-resetpass-' . COOKIEHASH ] ), 2 ) );
				$user = self::check_password_reset_key( $rp_key, $rp_login );

				// reset key / login is correct, display reset password form with hidden key / login values
				if ( is_object( $user ) ) {

					echo wp_travel_get_template_html( 'account/form-reset-password.php', array(
						'key'   => $rp_key,
						'login' => $rp_login,
					) );

					return;
				}
			}
		}

		// Show lost password form by default.
		wp_travel_get_template_part( 'account/form', 'lostpassword' );
	}

	/**
	 * Retrieves a user row based on password reset key and login.
	 *
	 * @uses $wpdb WordPress Database object
	 *
	 * @param string $key Hash to validate sending user's password
	 * @param string $login The user login
	 *
	 * @return WP_User|bool User's database row on success, false for invalid keys
	 */
	public static function check_password_reset_key( $key, $login ) {
		// Check for the password reset key.
		// Get user data or an error message in case of invalid or expired key.
		$user = check_password_reset_key( $key, $login );

		if ( is_wp_error( $user ) ) {
			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'This key is invalid or has already been used. Please reset your password again if needed.', 'wp-travel' ), 'error' );
			return false;
		}

		return $user;
	}

	/**
	 * Handles sending password retrieval email to customer.
	 *
	 * Based on retrieve_password() in core wp-login.php.
	 *
	 * @uses $wpdb WordPress Database object
	 * @return bool True: when finish. False: on error
	 */
	public static function retrieve_password() {
		$login = trim( $_POST['user_login'] );

		if ( empty( $login ) ) {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Enter an email or username.', 'wp-travel' ), 'error' );

			return false;

		} else {
			// Check on username first, as customers can use emails as usernames.
			$user_data = get_user_by( 'login', $login );
		}

		// If no user found, check if it login is email and lookup user based on email.
		if ( ! $user_data && is_email( $login ) && apply_filters( 'wp_travel_get_username_from_email', true ) ) {
			$user_data = get_user_by( 'email', $login );
		}

		$errors = new WP_Error();

		do_action( 'lostpassword_post', $errors );

		if ( $errors->get_error_code() ) {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . $errors->get_error_message(), 'error' );

			return false;
		}

		if ( ! $user_data ) {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Invalid username or email.', 'wp-travel' ), 'error' );

			return false;
		}

		if ( is_multisite() && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Invalid username or email.', 'wp-travel' ), 'error' );

			return false;
		}

		// redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;

		do_action( 'retrieve_password', $user_login );

		$allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

		if ( ! $allow ) {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Password reset is not allowed for this user.', 'wp-travel' ), 'error' );

			return false;

		} elseif ( is_wp_error( $allow ) ) {

			WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . $allow->get_error_message(), 'error' );

			return false;
		}

		// Get password reset key (function introduced in WordPress 4.4).
		$key = get_password_reset_key( $user_data );

		// Send email notification.
		$email_content = wp_travel_get_template_html( 'emails/customer-lost-password.php', array( 'user_login' => $user_login, 'reset_key' => $key ) );

		// To send HTML mail, the Content-type header must be set.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		$from = get_option( 'admin_email' );
			// Create email headers.
			$headers .= 'From: ' . $from . "\r\n";
			$headers .= 'Reply-To: ' . $from . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

			if ( $user_login && $key ) {

			$user_object     = get_user_by( 'login', $user_login );
			$user_user_login = $user_login;
			$user_reset_key  = $key;
			$user_user_email = stripslashes( $user_object->user_email );
			$user_recipient  = $user_user_email;
			$user_subject    = __( 'Password Reset Request', 'wp-travel' );

			if ( ! wp_mail( $user_recipient, $user_subject, $email_content, $headers ) ) {

				return false;

			}
		}

		return true;
	}

	/**
	 * Handles resetting the user's password.
	 *
	 * @param object $user The user
	 * @param string $new_pass New password for the user in plaintext
	 */
	public static function reset_password( $user, $new_pass ) {
		do_action( 'password_reset', $user, $new_pass );

		wp_set_password( $new_pass, $user->ID );
		self::set_reset_password_cookie();

		wp_password_change_notification( $user );
	}

	/**
	 * Set or unset the cookie.
	 *
	 * @param string $value
	 */
	public static function set_reset_password_cookie( $value = '' ) {
		$rp_cookie = 'wp-resetpass-' . COOKIEHASH;
		$rp_path   = current( explode( '?', wp_unslash( $_SERVER['REQUEST_URI'] ) ) );

		if ( $value ) {
			setcookie( $rp_cookie, $value, 0, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		} else {
			setcookie( $rp_cookie, ' ', time() - YEAR_IN_SECONDS, $rp_path, COOKIE_DOMAIN, is_ssl(), true );
		}
	}

}
