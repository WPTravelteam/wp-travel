<?php
/**
 * Template file for WP Travel inventory tab.
 *
 * @package WP_Travel
 */

/**
 * Callback Function For Downloads Content Tabs
 *
 * @param string $tab  tab name 'downloads'.
 * @param array  $args arguments function arugments.
 */
function wptravel_trip_callback_downloads( $tab, $args ) {
	if ( 'downloads' !== $tab ) {
		return;
	}
	if ( ! class_exists( 'WP_Travel_Downloads_Core' ) ) :
		$args = array(
			'title'      => __( 'Need to add your downloads?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can add your downloads in all of your trips !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
			'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
		);
		wptravel_upsell_message( $args );
	endif;

	// Need to deprecate this hook and replace with wptravel_trip_downloads_tab_content.
	do_action( 'wp_travel_trip_downloads_tab_content', $args ); // @phpcs:ignore
}
