<?php
/**
 * WP Travel Dev Mode
 *
 * @package WP_Travel
 */

/**
 * WP Travel Dev mode function.
 *
 * @return Boolean
 */
function wptravel_dev_mode() {
	if ( defined( 'WPTRAVEL_DEV_MODE' ) ) {
		return WPTRAVEL_DEV_MODE;
	}
	return false;
}

/**
 * WP Travel script suffix function.
 *
 * @since 4.6.3
 * @return String
 */
function wptravel_script_suffix() {
	$settings = wptravel_get_settings();
	$suffix   = (
		( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG )
		||
		( defined( 'WPTRAVEL_DEV_MODE' ) && WPTRAVEL_DEV_MODE )
		||
		'yes' !== $settings['load_minified_scripts']
	) ? '' : '.min';
	return $suffix;
}
