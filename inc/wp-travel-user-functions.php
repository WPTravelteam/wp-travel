<?php
/**
 * User Functions.
 *
 * @package Wp_Travel
 */
/**
 * Prevent any user who cannot 'edit_posts' (subscribers, customers etc) from seeing the admin bar.
 *
 * Note: get_option( 'wp_travel_lock_down_admin', true ) is a deprecated option here for backwards compatibility. Defaults to true.
 *
 * @access public
 * @param bool $show_admin_bar
 * @return bool
 */
function wp_travel_disable_admin_bar( $show_admin_bar ) {
	if ( apply_filters( 'wp_travel_disable_admin_bar', ! current_user_can( 'edit_posts' ) ) ) {
		$show_admin_bar = false;
	}

	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'wp_travel_disable_admin_bar', 10, 1 );

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

		$settings = wp_travel_get_settings();

		$generate_username_from_email = isset( $settings['generate_username_from_email'] ) ? $settings['generate_username_from_email'] : 'no';
		$generate_user_password = isset( $settings['generate_user_password'] ) ? $settings['generate_user_password'] : 'no';

		// Check the email address.
		if ( empty( $email ) || ! is_email( $email ) ) {
			return new WP_Error( 'registration-error-invalid-email', __( 'Please provide a valid email address.', 'wp-travel' ) );
		}

		if ( email_exists( $email ) ) {
			return new WP_Error( 'registration-error-email-exists', apply_filters( 'wp_travel_registration_error_email_exists', __( 'An account is already registered with your email address. Please log in.', 'wp-travel' ), $email ) );
		}

		// Handle username creation.
		if ( 'no' === $generate_username_from_email || ! empty( $username ) ) {
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
		if ( 'yes' === $generate_user_password && empty( $password ) ) {
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

/**
 * Get endpoint URL.
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @param  string $endpoint  Endpoint slug.
 * @param  string $value     Query param value.
 * @param  string $permalink Permalink.
 *
 * @return string
 */
function wp_travel_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink ) {
		$permalink = get_permalink();
	}

	// Map endpoint to options.
	$query_class = new WP_Travel_Query();
	$query_vars  = $query_class->get_query_vars();
	$endpoint    = ! empty( $query_vars[ $endpoint ] ) ? $query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . trailingslashit( $endpoint );

		if ( $value ) {
			$url .= trailingslashit( $value );
		}

		$url .= $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'wp_travel_get_endpoint_url', $url, $endpoint, $value, $permalink );
}

/**
 * Returns the url to the lost password endpoint url.
 *
 * @return string
 */
function wp_travel_lostpassword_url() {
	$default_url = wp_lostpassword_url();
	// Avoid loading too early.
	if ( ! did_action( 'init' ) ) {
		$url = $default_url;
	} else {
		// Don't redirect to the WP Travel endpoint on global network admin lost passwords.
		if ( is_multisite() && isset( $_GET['redirect_to'] ) && false !== strpos( wp_unslash( $_GET['redirect_to'] ), network_admin_url() ) ) { // WPCS: input var ok, sanitization ok.
			$url = $default_url;
		} else {
			$wp_travel_account_page_url    = wp_travel_get_page_permalink( 'wp-travel-dashboard' );
			$wp_travel_account_page_exists = wp_travel_get_page_id( 'wp-travel-dashboard' ) > 0;

			if ( $wp_travel_account_page_exists ) {
				$url = $wp_travel_account_page_url . '?action=lost-pass';
			} else {
				$url = $default_url;
			}
		}
	}
	return apply_filters( 'wp_travel_lostpassword_url', $url, $default_url );
}
