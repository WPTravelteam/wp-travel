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
		add_action( 'wp_loaded', array( __CLASS__, 'process_login' ), 20 );
	}

	/**
	 * Process the login form.
	 */
	public static function process_login() {

		$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
		$nonce_value = isset( $_POST['wp-travel-login-nonce'] ) ? $_POST['wp-travel-login-nonce'] : $nonce_value;

		if ( ! empty( $_POST['username'] ) && wp_verify_nonce( $nonce_value, 'wp-travel-login' ) ) {

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

					// if ( ! empty( $_POST['redirect'] ) ) {
					// 	$redirect = $_POST['redirect'];
					// } elseif ( wc_get_raw_referer() ) {
					// 	$redirect = wc_get_raw_referer();
					// } else {
					// 	$redirect = wc_get_page_permalink( 'myaccount' );
					// }

					// wp_redirect( wp_validate_redirect( apply_filters( 'woocommerce_login_redirect', remove_query_arg( 'wc_error', $redirect ), $user ), wc_get_page_permalink( 'myaccount' ) ) );

					wp_redirect( 'http://localhost/testsite/dashboard-2/' );
					exit;
				}
			} catch ( Exception $e ) {

				WP_Travel()->notices->add( apply_filters( 'wp_travel_login_errors', __( '<strong>Error :</strong>Invalid Username or Password', 'wp-travel' ) ), 'error' );

			}
		} else {

			WP_Travel()->notices->add( apply_filters( 'wp_travel_login_errors', __( '<strong>Error :</strong>Username can not be empty', 'wp-travel' ) ), 'error' );

		}
	}
}

Wp_Travel_Form_Handler::init();
