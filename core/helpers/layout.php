<?php
/**
 * WP Travel Layout Version
 *
 * @package WP_Travel
 */

/**
 * WP Travel Layout Version function.
 *
 * @since 5.1.0
 * @return Boolean
 */
function wptravel_layout_version() {
	$wp_travel_user_since = get_option( 'wp_travel_user_since' );

	$version = 'v2'; // Current Default Version.

	// Legacy Uses.
	if ( version_compare( $wp_travel_user_since, '5.1', '<' ) ) {
		$version = 'v1';
	}

	/**
	 * Filter the WP Travel Layout Version to use layout.
	 *
	 * @since 5.1.0
	 */
	$version = apply_filters( 'wptravel_layout_version', $version );
	return $version;
}

// function wptravel_is_v1_template() {

// }
