<?php
/**
 * Callback function of cart & checkout tab.
 *
 * @package WP Travel
 */

/**
 * Callback Function For Inventory Content Tabs
 *
 * @param string $tab  tab name 'inventory'.
 * @param array  $args arguments function arugments.
 * @return void
 */
function wptravel_trip_callback_cart_checkout( $tab, $args ) {
	if ( ! $tab ) {
		return;
	}
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'       => __( 'Need to add your checkout options?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can add your checkout options for all of your trips !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
	endif;

	wptravel_do_deprecated_action( 'wp_travel_trip_cart_checkout_tab_content', $args, '4.4.7', 'wptravel_trip_cart_checkout_tab_content' ); //@phpcs:ignore
	do_action( 'wptravel_trip_cart_checkout_tab_content', $args );
}

