<?php
class WP_Travel_Admin_Assets {
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	public static function assets() {
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );
		if ( in_array( $screen->id, $allowed_screen ) ) {
			wp_enqueue_editor();
			$deps = include_once sprintf( '%s/app/build/admin-trip-options.asset.php', WP_TRAVEL_EXTENDED_FILE_PATH );
			$deps['dependencies'][] = 'jquery';
			wp_enqueue_script( 'wp-travel-admin-trip-options', plugin_dir_url( WP_TRAVEL_EXTENDED_FILE ) . '/app/build/admin-trip-options.js', $deps['dependencies'], $deps['version'], true );

			wp_enqueue_style( 'wp-travel-admin-trip-options-style', plugin_dir_url( WP_TRAVEL_EXTENDED_FILE ) . '/app/build/admin-trip-options.css', array( 'wp-components' ), $deps['version'] );
		}
	}
}

WP_Travel_Admin_Assets::init();
