<?php
/**
 * For activation or deactivation of plugin.
 *
 * @package WP Travel
 */

/**
 * WP Travel install class.
 */
class WP_Travel_Install {

	/**
	 * Constructor.
	 */
	function __construct() {
		register_deactivation_hook( WP_TRAVEL_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Deactivation callback.
	 */
	function deactivate() {
		do_action( 'wp_travel_deactivated' );
	}
}

new WP_Travel_Install();
