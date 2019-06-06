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

		$tab_hook_prefix = "wp_travel_tabs_content_{$collection}";
		$tabs            = $this->list_by_collection( $collection );

		$tab_content_directory = $collection;
		if ( WP_TRAVEL_POST_TYPE == $collection && 'itineraries' != $collection ) { // directory must remain same if somebody changed their post type.
			$tab_content_directory = 'itineraries';
		}

		if ( is_array( $tabs ) && count( $tabs ) > 0 ) {
			foreach ( $tabs as $tab_key => $tab ) {
				$filename          = str_replace( '_', '-', $tab_key ) . '.php';
				$callback_file     = sprintf( '%sinc/admin/views/tabs/tab-contents/%s/%s', WP_TRAVEL_ABSPATH, $tab_content_directory, $filename );
				$callback_function = isset( $tab['callback'] ) ? $tab['callback'] : '';
				if ( file_exists( $callback_file ) ) {
					require_once $callback_file;
				}
				if ( ! empty( $callback_function ) && function_exists( $callback_function ) ) {
					add_action( "{$tab_hook_prefix}_{$tab_key}", $callback_function, 12, 2 );
				}
			}
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
