<?php
class WP_Travel_Ajax_Settings {

	/**
	 * Initialize Ajax requests.
	 */
	public static function init() {
		 // get settings.
		add_action( 'wp_ajax_wptravel_get_settings', array( __CLASS__, 'get_settings' ) );
		add_action( 'wp_ajax_nopriv_wptravel_get_settings', array( __CLASS__, 'get_settings' ) );

		// Update settings.
		add_action( 'wp_ajax_wp_travel_update_settings', array( __CLASS__, 'update_settings' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_settings', array( __CLASS__, 'update_settings' ) );

		// Force Migrate to v4.
		add_action( 'wp_ajax_wptravel_force_migrate', array( __CLASS__, 'force_migrate_to_v4' ) );
		add_action( 'wp_ajax_nopriv_wptravel_force_migrate', array( __CLASS__, 'force_migrate_to_v4' ) );

		add_action( 'wp_ajax_wptravel_wpml_migrate', array( __CLASS__, 'force_migrate_wpml' ) );
		add_action( 'wp_ajax_nopriv_wptravel_wpml_migrate', array( __CLASS__, 'force_migrate_wpml' ) );
		// add_action( 'init', array( __CLASS__, 'wp_travel_trip_date_price' ) );
	}

	public static function get_settings() {
		/**
		 * Permission Check
		 */

		WP_Travel::verify_nonce();

		$response = WP_Travel_Helpers_Settings::get_settings();

		WP_Travel_Helpers_REST_API::response( $response );
	}


	public static function update_settings() {
		/**
		 * Permission Check
		 */

		WP_Travel::verify_nonce();

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		$response  = WP_Travel_Helpers_Settings::update_settings( $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function force_migrate_to_v4() {
		/**
		 * Permission Check
		 */
		WP_Travel::verify_nonce();

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		if ( isset( $post_data['force_migrate_to_v4'] ) && $post_data['force_migrate_to_v4'] ) {
			if ( ! function_exists( 'wptravel_update_to_400' ) ) {
				WP_Travel_Actions_Activation::migrations(); // to include functin defination.
			}
			$response = wptravel_update_to_400( @$network_enabled, true );
			WP_Travel_Helpers_REST_API::response( $response );
		}
	}
	/**
	 * @since 6.4.0
	 * when click migrate button in in setting of debug page button 
	 * migrate all price and date in to post meta 
	 */
	public static function force_migrate_wpml() {
		/**
		 * Permission Check
		 */

		 WP_Travel::verify_nonce();

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );
		global $wpdb;
		$db_prefix       = $wpdb->prefix;
		$date_table      = $db_prefix . 'wt_dates';
		$price_table     = $db_prefix . 'wt_pricings';
		$price_cat_table = $db_prefix . 'wt_price_category_relation';
		if ( isset( $post_data['wpml_migrations'] ) ) {
			if ( $post_data['wpml_migrations'] == true ) {
				$posts = new WP_Query(
					array(
						'post_type'      => WP_TRAVEL_POST_TYPE,
						'posts_per_page' => -1,
					)
				);
				while ( $posts->have_posts() ) {
					$posts->the_post();
					$trip_data  = WpTravel_Helpers_Trips::get_trip( get_the_ID() );
					$trip_id    = get_the_ID();
					$date       = $wpdb->get_results( "select * from {$date_table} where trip_id={$trip_id}" );
					$trips      = isset( $trip_data['trip'] ) ? $trip_data['trip'] : array();
					$trip_price = ! empty( $trips ) && isset( $trips['pricings'] ) ? $trips['pricings'] : array();
					$res        = update_post_meta( $trip_id, 'wp_travel_trip_price_categorys', $trip_price );
					$trip_date  = ! empty( $trips ) && isset( $trips['dates'] ) ? $trips['dates'] : array();
					if ( ! empty( $date ) && count( $date ) > 0 ) {
						foreach ( $date as $key => $value ) {
							if ( ! empty( $trip_date ) && count( $trip_date ) > 0 ) {
								$trip_date[ $key ]['years']  = isset( $value->years ) ? $value->years : '';
								$trip_date[ $key ]['months'] = isset( $value->months ) ? $value->monthes : '';
							}
						}
					}
					$responce = update_post_meta( $trip_id, 'wp_travel_trips_dates', $trip_date );
				}
			}
		}
		return wp_send_json_success( 'success' );
	}
}

WP_Travel_Ajax_Settings::init();
