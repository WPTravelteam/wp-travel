<?php
/**
 * Admin Assets file.
 *
 * @package wp-travel/app/inc/admin.
 */

/**
 * WpTravel_Admin_Assets class.
 */
class WpTravel_Admin_Assets {

	/**
	 * Init.
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Admin assets.
	 */
	public static function assets() {
		$screen                = get_current_screen();
		$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true ); // @phpcs:ignore
		$react_settings_enable = apply_filters( 'wptravel_settings_react_enabled', $react_settings_enable );
		$allowed_screen        = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries', 'itinerary-booking_page_settings' );
		if ( in_array( $screen->id, $allowed_screen, true ) || ( $react_settings_enable && WP_Travel::is_page( 'settings', true ) ) ) {
			wp_enqueue_editor();
			$deps                   = include_once sprintf( '%s/app/build/admin-trip-options.asset.php', WP_TRAVEL_ABSPATH );
			$deps['dependencies'][] = 'jquery';
			wp_enqueue_script( 'wp-travel-admin-trip-options', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/app/build/admin-trip-options.js', $deps['dependencies'], $deps['version'], true );

			wp_enqueue_style( 'wp-travel-admin-trip-options-style', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/app/build/admin-trip-options.css', array( 'wp-components' ), $deps['version'] );
		}

		// settings_screen.
		if ( $react_settings_enable && WP_Travel::is_page( 'settings', true ) ) {
			$deps                   = include_once sprintf( '%s/app/build/admin-settings.asset.php', WP_TRAVEL_ABSPATH );
			$deps['dependencies'][] = 'jquery';
			wp_enqueue_script( 'wp-travel-admin-settings', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/app/build/admin-settings.js', $deps['dependencies'], $deps['version'], true );
			wp_enqueue_style( 'wp-travel-admin-settings-style', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/app/build/admin-settings.css', array( 'wp-components', 'font-awesome-css' ), $deps['version'] );
		}
	}
}

WpTravel_Admin_Assets::init();
