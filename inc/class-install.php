<?php
/**
 * For activation or deactivation of plugin.
 *
 * @package WP_Travel
 */

/**
 * WP Travel install class.
 */
class WP_Travel_Install { // @phpcs:ignore

	/**
	 * Constructor.
	 */
	public function __construct() {
		register_deactivation_hook( WP_TRAVEL_PLUGIN_FILE, array( $this, 'deactivate' ) );
	}

	/**
	 * Deactivation callback.
	 */
	public function deactivate() {
		do_action( 'wp_travel_deactivated' ); // @phpcs:ignore
	}
}

new WP_Travel_Install();
