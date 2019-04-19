<?php
/**
 * Template file for WP Travel inventory tab.
 *
 * @package WP Travel
 */

if ( ! function_exists( 'wp_travel_trip_callback_inventory' ) ) {

	function wp_travel_trip_callback_inventory() {

		if ( ! class_exists( 'WP_Travel_Inventory_Management_Core' ) ) :
			$args = array(
				'title'      => __( 'Need to add your inventory options?', 'wp-travel' ),
				'content'    => __( 'By upgrading to Pro, you can add your inventory options in all of your trips !', 'wp-travel' ),
				'link'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
				'link_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
			);
			wp_travel_upsell_message( $args );
		endif;

		do_action( 'wp_travel_trip_inventory_tab_content', $args );
	}
}

