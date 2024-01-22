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

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$response = WP_Travel_Helpers_Settings::get_settings();

		WP_Travel_Helpers_REST_API::response( $response );
	}


	public static function update_settings() {
		/**
		 * Permission Check
		 */
		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		/**
		 * solve ajax request in server
		 */
		$post_data = file_get_contents( 'php://input' ); // Added 2nd Parameter to resolve issue with objects.
		// $post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		$post_data     = is_string( $post_data ) ? json_decode( $post_data, true ) : $post_data; // check ajax string data
		$new_post_data = is_string( $post_data ) ? json_decode( $post_data, true ) : $post_data; // check after ajax data is converted in to array of not
		$new_post_data = wptravel_sanitize_array( $new_post_data, true );
		$response      = WP_Travel_Helpers_Settings::update_settings( $new_post_data );
		WP_Travel_Helpers_REST_API::response( $response );
		// $post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		// $post_data = wptravel_sanitize_array( $post_data, true );  // wp kses for some editor content in email settings.
		// $response  = WP_Travel_Helpers_Settings::update_settings( $post_data );
		// WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function force_migrate_to_v4() {
		/**
		 * Permission Check
		 */
		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

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

		if ( ! current_user_can( 'manage_options' ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );
		global $wpdb;
		$db_prefix       = $wpdb->prefix;
		$date_table      = $db_prefix . 'wt_dates';
		$price_table     = $db_prefix . 'wt_pricings';
		$price_cat_table = $db_prefix . 'wt_price_category_relation';
		$settings        = WP_Travel_Helpers_Settings::get_settings();
		if ( isset( $settings['settings'] ) && isset( $settings['settings']['wpml_migrations'] ) ) {
			if ( $settings['settings']['wpml_migrations'] == true ) {
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
				/**
				 * @since 6.4.1
				 * migrate custom filter
				 */
				$custom_filter = get_option( 'wp_travel_custom_filters_option' );
				if ( ! empty( $custom_filter ) && count( $custom_filter ) > 0 ) {
					$new_term_options = $custom_filter;
					foreach ( $custom_filter as $slug => $val ) {
						$term_id   = isset( $val['term_id'] ) ? $val['term_id'] : 0;
						$term_name = isset( $val['label'] ) ? $val['label'] : '';
						if ( empty( $term_id ) && $term_id == 0 ) {
							$term_array = array(
								'slug'        => $slug,
								'description' => '',
							);
							$term_res   = wp_insert_term( $term_name, 'wp_travel_custom_filters', $term_array );
							if ( ! is_wp_error( $term_res ) && ! empty( $term_res ) ) {
								$new_term_options[ $slug ]['term_id'] = isset( $term_res['term_id'] ) ? $term_res['term_id'] : 0;
							}
						}
					}
					update_option( 'wp_travel_custom_filters_option', $new_term_options );
				}
			}
		}
		return wp_send_json_success( 'success' );
	}
}

WP_Travel_Ajax_Settings::init();
