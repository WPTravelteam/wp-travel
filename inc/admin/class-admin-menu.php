<?php
class WP_Travel_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}
	function add_menus() {
		add_submenu_page( 'edit.php?post_type=itineraries', __( 'WP Travel Settings', 'wp-travel' ), __( 'Settings', 'wp-travel' ), 'manage_options', 'settings', array( 'WP_Travel_Admin_Settings', 'setting_page_callback' ) );

		add_submenu_page( 'edit.php?post_type=itineraries', __( 'System Status', 'wp-travel' ), __( 'Status', 'wp-travel' ), 'manage_options', 'sysinfo', array( 'WP_Travel_Admin_Settings', 'get_system_info' ) );

		// Remove from menu.
		remove_submenu_page( 'edit.php?post_type=itineraries', 'sysinfo');
	}
}

new WP_Travel_Admin_Menu();
