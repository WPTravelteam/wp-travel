<?php
class WP_Travel_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}
	function add_menus() {
		add_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, __( 'Stat', 'wp-travel' ), __( 'Stat', 'wp-travel' ), 'manage_options', 'booking_chart', 'get_booking_chart' );
		add_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, __( 'WP Travel Settings', 'wp-travel' ), __( 'Settings', 'wp-travel' ), 'manage_options', 'settings', array( 'WP_Travel_Admin_Settings', 'setting_page_callback' ) );
		add_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, __( 'Docs & Support', 'wp-travel' ) ,'<span class="wp-travel-menu-docs-support"> '. __( 'Docs & Support', 'wp-travel' ) . '</span>', 'manage_options', 'docs-support', 'docs_support_page_callback' );
		add_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, __( 'Marketplace', 'wp-travel' ), __( 'Marketplace', 'wp-travel' ), 'manage_options', 'wp-travel-marketplace', 'wp_travel_marketplace_page' );
		add_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, __( 'System Status', 'wp-travel' ), __( 'Status', 'wp-travel' ), 'manage_options', 'sysinfo', array( 'WP_Travel_Admin_Settings', 'get_system_info' ) );

		// Remove from menu.
		remove_submenu_page( 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE, 'sysinfo');
	}
}

new WP_Travel_Admin_Menu();
