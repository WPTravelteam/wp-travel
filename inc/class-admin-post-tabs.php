<?php
/**
 * Admin post meta tabs.
 *
 * @package WP Travel
 */

/**
 * Admin post tabs class.
 */
class WP_Travel_Admin_Post_Tabs {

	/**
	 * Constructor.
	 */
	public function __construct() {

	}

	function get_all_tabs() {
		$tabs = array();
		return apply_filters( 'wp_travel_admin_post_tabs', $tabs );
	}

	/**
	 * Get All tabs.
	 *
	 * @return array Tabs array.
	 */
	public function get_tabs( $post_type ) {
		$tabs = self::get_all_tabs();
		if ( isset( $tabs[ $post_type ] ) && ! empty( $tabs[ $post_type ] ) ) {
			return $tabs[ $post_type ];
		}

		return false;
	}
}
