<?php
class WP_Travel_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}
	/**
	 * Add / Remove Menu items.
	 */
	public function add_menus() {
		// Trips Submenu.
		add_submenu_page( 'edit.php?post_type=itinerary-booking', __( 'System Status', 'wp-travel' ), __( 'Status', 'wp-travel' ), 'manage_options', 'sysinfo', array( 'WP_Travel_Admin_Settings', 'get_system_info' ) );

		// WP Travel Submenu.
		add_submenu_page( 'edit.php?post_type=itinerary-booking', __( 'Reports', 'wp-travel' ), __( 'Reports', 'wp-travel' ), 'manage_options', 'booking_chart', 'get_booking_chart' );
		add_submenu_page( 'edit.php?post_type=itinerary-booking', __( 'WP Travel Settings', 'wp-travel' ), __( 'Settings', 'wp-travel' ), 'manage_options', 'settings', array( 'WP_Travel_Admin_Settings', 'setting_page_callback' ) );
		add_submenu_page( 'edit.php?post_type=itinerary-booking', __( 'Marketplace', 'wp-travel' ), __( 'Marketplace', 'wp-travel' ), 'manage_options', 'wp-travel-marketplace', 'wp_travel_marketplace_page' );

		// Remove from menu.
		remove_submenu_page( 'edit.php?post_type=itinerary-booking', 'sysinfo' );
		global $submenu;
		unset( $submenu['edit.php?post_type=itinerary-booking'][10] ); // Removes 'Add New'.
	}
}

new WP_Travel_Admin_Menu();
