<?php
/**
 * Template file for WP Travel inventory tab.
 *
 * @package WP Travel
 */

/**
 * Callback Function For Inventory Content Tabs
 *
 * @param string $tab  tab name 'inventory'.
 * @param array  $args arguments function arugments.
 * @return Mixed
 */
function wp_travel_trip_callback_cart_checkout( $tab, $args ) {

	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'      => __( 'Need to add your checkout options?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can add your checkout options for all of your trips !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
        	'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;

	do_action( 'wp_travel_trip_cart_checkout_tab_content', $args );
}

