<?php
/**
 * Tour extras General Tab Contents
 *
 * @package WP Travel
 */

function wp_travel_tour_extras_gallery_tab_callback() {

	if ( ! class_exists( 'WP_Travel_Tour_Extras_Core' ) ) :
		$args = array(
			'title'       => __( 'Want to use above pro features?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can get features with gallery, detail extras page in Front-End and more !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://themepalace.com/downloads/wp-travel-tour-extras/',
			'link2_label' => __( 'Get Tour Extras Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
	do_action( 'wp_travel_tour_extras_gallery_tab_content' ); // @since 2.0.4
}
