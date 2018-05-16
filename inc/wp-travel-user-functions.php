<?php
/**
 * User Functions.
 *
 * @package Wp_Travel
 */
if ( ! function_exists( 'wp_travel_create_new_customer' ) ) {

	/**
	 * Create a new customer.
	 *
	 * @param  string $email Customer email.
	 * @param  string $username Customer username.
	 * @param  string $password Customer password.
	 * @return int|WP_Error Returns WP_Error on failure, Int (user ID) on success.
	 */
	function wp_travel_create_new_customer( $email, $username = '', $password = '' ) {

		// Check the email address.
		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'wp-travel' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'registration-error-email-exists', apply_filters( 'wp_travel_registration_error_email_exists', __( 'An account is already registered with your email address. Please log in.', 'wp-travel' ), $email ) );
		}

		// Handle username creation.
		if ( 'no' === get_option( 'wp_travel_registration_generate_username' ) || ! empty( $username ) ) {
			$username = sanitize_user( $username );

			if ( empty( $username ) || ! validate_username( $username ) ) {
				return new WP_Error( 'registration-error-invalid-username', __( 'Please enter a valid account username.', 'wp-travel' ) );
			}

			if ( username_exists( $username ) ) {
				return new WP_Error( 'registration-error-username-exists', __( 'An account is already registered with that username. Please choose another.', 'wp-travel' ) );
			}
		} else {
			$username = sanitize_user( current( explode( '@', $email ) ), true );

			// Ensure username is unique.
			$append     = 1;
			$o_username = $username;

			while ( username_exists( $username ) ) {
				$username = $o_username . $append;
				$append++;
			}
		}

		// Handle password creation.
		if ( 'yes' === get_option( 'wp_travel_registration_generate_password' ) && empty( $password ) ) {
			$password           = wp_generate_password();
			$password_generated = true;
		} elseif ( empty( $password ) ) {
			return new WP_Error( 'registration-error-missing-password', __( 'Please enter an account password.', 'wp-travel' ) );
		} else {
			$password_generated = false;
		}

		// Use WP_Error to handle registration errors.
		$errors = new WP_Error();

		// do_action( 'wp_travel_register_post', $username, $email, $errors );

		$errors = apply_filters( 'wp_travel_registration_errors', $errors, $username, $email );

		if ( $errors->get_error_code() ) {
			return $errors;
		}

		$new_customer_data = apply_filters( 'wp_travel_new_customer_data', array(
			'user_login' => $username,
			'user_pass'  => $password,
			'user_email' => $email,
			'role'       => 'wp-travel-customer',
		) );

		$customer_id = wp_insert_user( $new_customer_data );

		if ( is_wp_error( $customer_id ) ) {
			return new WP_Error( 'registration-error', '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . __( 'Couldn&#8217;t register you&hellip; please contact us if you continue to have problems.', 'wp-travel' ) );
		}

		do_action( 'wp_travel_created_customer', $customer_id, $new_customer_data, $password_generated );

		return $customer_id;
	}
}

/**
 * Login a member (set auth cookie and set global user object).
 *
 * @param int $customer_id
 */
function wp_travel_set_customer_auth_cookie( $customer_id ) {
	global $current_user;

	$current_user = get_user_by( 'id', $customer_id );

	wp_set_auth_cookie( $customer_id, true );
}
