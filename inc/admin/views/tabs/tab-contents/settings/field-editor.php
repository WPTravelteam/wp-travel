<?php

if ( ! function_exists( 'wp_travel_settings_callback_field_editor' ) ) {
	/**
	 * Callback for Field Editor tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function wp_travel_settings_callback_field_editor( $tab, $args ) {
		if ( ! class_exists( 'WP_Travel_Field_Editor_Core' ) ) :
			$args = array(
				'title'      => __( 'Want to customize your Traveler fields, billing fields and more?', 'wp-travel' ),
				'content'    => __( 'By upgrading to Pro, you can customize your Fields for Trip enquiry, Billing and travelers fields.!', 'wp-travel' ),
				'link'       => 'https://wptravel.io/downloads/wp-travel-field-editor/',
				'link_label' => __( 'Get WP Travel Field Editor', 'wp-travel' ),
			);
			wp_travel_upsell_message( $args );
		endif;
		do_action( 'wp_travel_settings_tab_field_editor_fields', $args );
	}
}
