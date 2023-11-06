<?php
/**
 * Admin Assets file.
 *
 * @package WP_Travel
 * 
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
		WpTravel_Frontend_Assets::register_scripts();

		global $wp_scripts;
		$queued_scripts     = $wp_scripts->queue;
		$registered_scripts = $wp_scripts->registered;

		$all_localized                = WpTravel_Helpers_Localize::get();
		$wp_travel_chart_data         = isset( $all_localized['wp_travel_chart_data'] ) ? $all_localized['wp_travel_chart_data'] : array();
		$wp_travel_drag_drop_uploader = isset( $all_localized['wp_travel_drag_drop_uploader'] ) ? $all_localized['wp_travel_drag_drop_uploader'] : array();
		$_wp_travel_admin             = isset( $all_localized['_wp_travel_admin'] ) ? $all_localized['_wp_travel_admin'] : array();

		$screen = get_current_screen();

		if ( WP_Travel::is_pages( true ) ) {
			// Styles.
			wp_enqueue_media();
			wp_enqueue_style( 'font-awesome-css' );
			wp_enqueue_style( 'select2-style' );
			wp_enqueue_style( 'wp-travel-popup' );
			wp_enqueue_style( 'jquery-datepicker-lib' );

			// Scripts.
			wp_enqueue_script( 'wp-travel-fields-scripts' );
			wp_enqueue_script( 'wp-travel-tabs' );
			wp_enqueue_script( 'wp-travel-accordion' );
			wp_enqueue_script( 'wp-travel-popup' );

			if ( ! $screen->is_block_editor ) {
				wp_enqueue_style( 'wp-travel-back-end' );
			}
		}

		if ( is_network_admin() ) {
			wp_enqueue_style( 'wp-travel-back-end' );
		}

		if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
			wp_enqueue_style( 'wp-travel-setup-page' );
		}

		if ( WP_Travel::is_page( 'reports', true ) ) {
			wp_localize_script( 'jquery-chart-custom', 'wp_travel_chart_data', $wp_travel_chart_data );
			wp_enqueue_script( 'jquery-chart-custom' );
		}

		if ( WP_Travel::is_page( 'itineraries', true ) || WP_Travel::is_page( 'enquiry', true ) || WP_Travel::is_page( 'templates', true ) ) {
			$backend_depencency = array( 'jquery', 'jquery-ui-tabs', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'wp-travel-media-upload', 'jquery-ui-sortable', 'jquery-ui-accordion', 'moment' );
			if ( isset( $registered_scripts['google-map-api'] ) ) {
				// This will only registered if set google map as default map and has api key.
				wp_enqueue_script( 'google-map-api' );
			}

			wp_enqueue_script( 'wptravel-uploader' );

			wp_enqueue_script( 'jquery-parsley' );
			wp_localize_script( 'jquery-parsley', 'error_string', [
				'defaultMessage' => __( "This value seems to be invalid.", 'wp-travel' ),
				'type' => [
				'email' => __( "This value should be a valid email.", 'wp-travel' ),
				'url' => __( "This value should be a valid url.", 'wp-travel' ),
				'number' => __( "This value should be a valid number.", 'wp-travel' ),
				'integer' => __( "This value should be a valid integer.", 'wp-travel' ),
				'digits' => __( "This value should be digits.", 'wp-travel' ),
				'alphanum' => __( "This value should be alphanumeric.", 'wp-travel' )
				],
				'notblank' => __( "This value should not be blank.", 'wp-travel' ),
				'required' => __( "This value is required.", 'wp-travel' ),
				'pattern' => __( "This value seems to be invalid.", 'wp-travel' ),
				'min' => __( "This value should be greater than or equal to %s.", 'wp-travel' ),
				'max' => __( "This value should be lower than or equal to %s.", 'wp-travel' ),
				'range' => __( "This value should be between %s and %s.", 'wp-travel' ),
				'minlength' => __( "This value is too short. It should have %s characters or more.", 'wp-travel' ),
				'maxlength' => __( "This value is too long. It should have %s characters or fewer.", 'wp-travel' ),
				'length' => __( "This value length is invalid. It should be between %s and %s characters long.", 'wp-travel' ),
				'mincheck' => __( "You must select at least %s choices.", 'wp-travel' ),
				'maxcheck' => __( "You must select %s choices or fewer.", 'wp-travel' ),
				'check' => __( "You must select between %s and %s choices.", 'wp-travel' ),
				'equalto' => __( "This value should be the same.", 'wp-travel' ),
				'euvatin' => __( "It's not a valid VAT Identification Number.", 'wp-travel' )
			] );
			wp_localize_script( 'wp-travel-media-upload', 'wp_travel_drag_drop_uploader', $wp_travel_drag_drop_uploader );
			wp_enqueue_script( 'wp-travel-media-upload' );
		}

		if ( WP_Travel::is_page( 'itineraries', true ) || WP_Travel::is_page( 'settings', true ) ) {
			wp_enqueue_script( 'collapse-js' );
		}

		wp_enqueue_style( 'wptravel-admin' ); // enqueued for wp travel menu icon.
		wp_localize_script( 'wp-travel-admin-script', '_wp_travel_admin', $_wp_travel_admin );
		wp_enqueue_script( 'wp-travel-admin-script' ); // backend.js.
		wp_enqueue_script( 'wptravel-legacy-widgets' );

		if ( WP_Travel::is_page( 'itineraries', true ) && function_exists( 'wpseo_auto_load' ) ) {
			wp_enqueue_script( 'wptravel-yoast' ); // Yoast Compatibility.
		}

		// Block Trip edit.
		if ( WP_Travel::is_page( 'itineraries', true ) || WP_Travel::is_page( 'settings', true ) || WP_Travel::is_page( 'enquiry', true ) || WP_Travel::is_page( 'templates', true ) ) { // why this enqueue for settings page.?
			wp_enqueue_editor();
			wp_enqueue_style( 'wp-travel-admin-trip-options-style' );
			wp_enqueue_script( 'wp-travel-admin-trip-options' );
		}

		// Block Settings.
		if ( WP_Travel::is_page( 'settings', true ) ) {
		}
		wp_enqueue_style( 'wp-travel-admin-settings-style' );
		wp_enqueue_script( 'wp-travel-admin-settings' ); // temp fixes to use localized data.
		// Block Coupon.
		if ( WP_Travel::is_page( 'coupon', true ) ) {
			wp_enqueue_style( 'wptravel-admin-coupon' );
			wp_enqueue_script( 'wptravel-admin-coupon' );
		}
		// Block Enquiries.
		if ( WP_Travel::is_page( 'enquiry', true ) ) {
			wp_enqueue_style( 'wptravel-admin-enquiry' );
			wp_enqueue_script( 'wptravel-admin-enquiry' );
		}
		wp_enqueue_style( 'wptravel-admin-widgets' );
		// wp_enqueue_style( 'wp-travel-frontend' );
	}
}

WpTravel_Admin_Assets::init();
