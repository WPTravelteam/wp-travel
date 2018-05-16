<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Handle frontend forms.
 *
 * @class 		Wp_Travel_Form_Handler
 * @version		1.3.3
 * @category	Class
 */
class Wp_Travel_Form_Handler {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'template_redirect', array( __CLASS__, 'redirect_reset_password_link' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
		add_action( 'wp_loaded', array( __CLASS__, 'process_registration' ), 20 );
	}

	/**
	 * Process the login form.
	 */
	public static function process_login() {

		$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$nonce_value = isset( $_POST['wp-travel-login-nonce'] ) ? $_POST['wp-travel-login-nonce'] : $nonce_value;

		if ( ! empty( $_POST['login'] ) && wp_verify_nonce( $nonce_value, 'wp-travel-login' ) ) {

			try {
				$creds = array(
					'user_login'    => trim( $_POST['username'] ),
					'user_password' => $_POST['password'],
					'remember'      => isset( $_POST['rememberme'] ),
				);

				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . $validation_error->get_error_message() );
				}

				if ( empty( $creds['user_login'] ) ) {
					throw new Exception( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Username is required.', 'wp-travel' ) );
				}

				// On multisite, ensure user exists on current site, if not add them before allowing login.
				if ( is_multisite() ) {
					$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

					if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
						add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
					}
				}

				// Perform the login.
				$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );

				if ( is_wp_error( $user ) ) {
					$message = $user->get_error_message();
					$message = str_replace( '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', '<strong>' . esc_html( $creds['user_login'] ) . '</strong>', $message );
					throw new Exception( $message );
				} else {

					if ( ! empty( $_POST['redirect'] ) ) {
						$redirect = $_POST['redirect'];
					} elseif ( wp_travel_get_raw_referer() ) {
						$redirect = wp_travel_get_raw_referer();
					} else {
						$redirect = wc_get_page_permalink( 'wp-travel-dashboard' );
					}

					wp_redirect( wp_validate_redirect( apply_filters( 'wp_travel_login_redirect', remove_query_arg( 'wp_travel_error', $redirect ), $user ), wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ) );

					exit;
				}
			} catch ( Exception $e ) {

				WP_Travel()->notices->add( apply_filters( 'wp_travel_login_errors', __( '<strong>Error :</strong>Invalid Username or Password', 'wp-travel' ) ), 'error' );

			}
		} elseif ( isset( $_POST['username'] ) && empty( $_POST['username'] ) ) {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_login_errors', __( '<strong>Error :</strong>Username can not be empty', 'wp-travel' ) ), 'error' );

		}
	}

	/**
	 * Process the registration form.
	 */
	public static function process_registration() {
		$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$nonce_value = isset( $_POST['wp-travel-register-nonce'] ) ? $_POST['wp-travel-register-nonce'] : $nonce_value;

		if ( ! empty( $_POST['register'] ) && wp_verify_nonce( $nonce_value, 'wp-travel-register' ) ) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			$email    = $_POST['email'];

			try {
				$validation_error = new WP_Error();
				$validation_error = apply_filters( 'wp_travel_process_registration_errors', $validation_error, $username, $password, $email );

				if ( $validation_error->get_error_code() ) {
					throw new Exception( $validation_error->get_error_message() );
				}

				$new_customer = wp_travel_create_new_customer( sanitize_email( $email ), $username, $password );

				if ( is_wp_error( $new_customer ) ) {
					throw new Exception( $new_customer->get_error_message() );
				}

				if ( apply_filters( 'wp_travel_registration_auth_new_customer', true, $new_customer ) ) {
					wp_travel_set_customer_auth_cookie( $new_customer );
				}

				if ( ! empty( $_POST['redirect'] ) ) {
					$redirect = wp_sanitize_redirect( $_POST['redirect'] );
				} elseif ( wp_travel_get_raw_referer() ) {
					$redirect = wp_travel_get_raw_referer();
				} else {
					$redirect = wp_travel_get_page_permalink( 'wp-travel-dashboard' );
				}

				wp_redirect( wp_validate_redirect( apply_filters( 'wp_travel_register_redirect', remove_query_arg( 'wp_travel_error', $redirect ), $user ), wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ) );
				exit;

			} catch ( Exception $e ) {
				WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . $e->getMessage(), 'error' );
			}
		}
	}

	/**
	 * Remove key and login from query string, set cookie, and redirect to account page to show the form.
	 */
	public static function redirect_reset_password_link() {
		if ( wp_travel_is_account_page() && ! empty( $_GET['key'] ) && ! empty( $_GET['login'] ) ) {
			$value = sprintf( '%s:%s', wp_unslash( $_GET['login'] ), wp_unslash( $_GET['key'] ) );
			// WC_Shortcode_My_Account::set_reset_password_cookie( $value );

			wp_safe_redirect( add_query_arg( 'show-reset-form', 'true', wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ) );
			exit;
		}
	}
}

Wp_Travel_Form_Handler::init();
