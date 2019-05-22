<?php
class WP_Travel_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}
	/**
	 * Add / Remove Menu items.
	 */
	public function add_menus() {

		$all_submenus = wp_travel_get_submenu();
		
		foreach ( $all_submenus as $type => $submenus ) {
			$parent_slug = '';
			if ( 'bookings' === $type ) {
				$parent_slug = 'edit.php?post_type=itinerary-booking';
			}
			$submenus = wp_travel_sort_array_by_priority( $submenus );
			foreach ( $submenus as $submenu ) {
				if ( ! isset( $submenu['page_title'] ) || ! isset( $submenu['menu_title'] ) || ! isset( $submenu['menu_slug'] )  || ! isset( $submenu['callback'] ) ) {
					continue;
				}
				$capability = isset( $submenu['capability'] ) ? $submenu['capability'] : 'manage_options';
				add_submenu_page( $parent_slug, $submenu['page_title'], $submenu['menu_title'], $capability, $submenu['menu_slug'], $submenu['callback'] );
			}
		}

		// Remove from menu.
		remove_submenu_page( 'edit.php?post_type=itinerary-booking', 'sysinfo' );
		global $submenu;
		unset( $submenu['edit.php?post_type=itinerary-booking'][10] ); // Removes 'Add New'.
	}
}

new WP_Travel_Admin_Menu();
