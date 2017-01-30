<?php
class WP_Travel_Admin_Menu {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menus' ) );
	}
	function add_menus() {
		add_submenu_page( 'edit.php?post_type=itineraries', __( 'WP Travel Settings', 'wp-travel' ), __( 'Settings', 'wp-travel' ), 'manage_options', 'settings', array( 'WP_Travel_Admin_Settings', 'setting_page_callback' ) );

	}
}

new WP_Travel_Admin_Menu();
