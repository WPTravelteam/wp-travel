<?php
class WP_Travel_Admin_Metabox_Trip_Edit {
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'register_meta_box' ) );
	}

	public static function register_meta_box() {
		$settings        = wp_travel_get_settings();
		$switch_to_react = $settings['wp_travel_switch_to_react'];
		if ( 'yes' === $switch_to_react ) {
			add_meta_box( 'wp-travel-trip-options', esc_html__( 'Trip Options', 'wp-travel' ), array( __CLASS__, 'meta_box_callback' ), WP_TRAVEL_POST_TYPE, 'advanced', 'high' );
		}
	}

	public static function meta_box_callback() {
		echo '<div id="wp-travel-trip-options-wrap"></div>';
	}


}

WP_Travel_Admin_Metabox_Trip_Edit::init();
