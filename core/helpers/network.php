<?php
/**
 * Helpers class to do network action in multisite.
 *
 * @package WP_Travel
 */

/**
 * Network Helper.
 */
class WP_Travel_Helpers_Network { // @phpcs:ignore.

	/**
	 * Create table on add new site.
	 *
	 * @param object $new_site New site data in object.
	 */
	public static function on_site_create( $new_site ) {
		if ( ! $new_site ) {
			return;
		}

		$blog_id = $new_site->blog_id;
		WP_Travel_Actions_Activation::add_db_tables( false, $blog_id );

	}

	/**
	 * Delete table on deleting site.
	 *
	 * @param object $old_site New site data in object.
	 */
	public static function on_site_delete( $old_site ) {
		if ( ! $old_site ) {
			return;
		}
		$blog_id = $old_site->blog_id;
		WP_Travel_Actions_Activation::remove_db_tables( $blog_id );
	}
}
