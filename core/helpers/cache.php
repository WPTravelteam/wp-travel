<?php
/**
 * Helpers cache.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;
/**
 * WP_Travel_Helpers_Cache class.
 */
class WP_Travel_Helpers_Cache { // @phpcs:ignore

	/**
	 * Set constants to prevent caching by some plugins.
	 *
	 * @param  mixed $return Value to return. Previously hooked into a filter.
	 * @return mixed
	 */
	public static function set_nocache_constants( $return = true ) {
		WP_Travel::define( 'DONOTCACHEPAGE', true );
		WP_Travel::define( 'DONOTCACHEOBJECT', true );
		WP_Travel::define( 'DONOTCACHEDB', true );
		return $return;
	}
}
