<?php
/**
 * Callback for Checkout tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_cart_checkout_settings_global( $tab, $args ) {
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'      => __( 'Need Checkout options ?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can get checkout option features and more !', 'wp-travel' ),
			'link'       => 'https://wptravel.io/wp-travel-pro/',
        	'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
	do_action( 'wp_travel_settings_tab_cart_checkout_fields', $args );
}
