<?php
/**
 * Admin tabs.
 *
 * @package WP Travel
 * @author WEN Solutions
 */

/**
 * Admin tabs class.
 */
class WP_Travel_Admin_Tabs {

	/**
	 * Get All tabs.
	 *
	 * @return array Tabs array.
	 */
	public static function list_all() {
		$tabs = array();
		return apply_filters( 'wp_travel_admin_tabs', $tabs );
	}

	/**
	 * Get tab.
	 *
	 * @param  string $collection Tab key.
	 * @return array      Tabs.
	 */
	public static function list_by_collection( $collection ) {
		$tabs = self::list_all();
		if ( isset( $tabs[ $collection ] ) && ! empty( $tabs[ $collection ] ) ) {
			return $tabs[ $collection ];
		}

		return false;
	}

	/**
	 * Load tab template.
	 *
	 * @param  string $collection Collection name.
	 * @param  array  $args       Args to pass in template.
	 */
	public function load( $collection, $args = array() ) {
		$tabs = self::list_by_collection( $collection );
		$i = 0;
		if ( empty( $tabs ) ) {
			return false;
		}
		include sprintf( '%s/inc/admin/views/tabs/tabs.php', WP_TRAVEL_ABSPATH );
	}

	/**
	 * Load tab content.
	 *
	 * @param  string $path Template path.
	 * @param  array  $args Args for template.
	 */
	public function content( $path, $args = array() ) {
		include sprintf( '%s/inc/admin/views/tabs/tab-contents/%s', WP_TRAVEL_ABSPATH, $path );
	}
}
