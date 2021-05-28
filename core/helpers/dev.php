<?php
/**
 * WP Travel Dev Mode
 *
 * @package core/helpers
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
 * @since WP Travel 4.6.3
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
