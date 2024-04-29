<?php
class WP_Travel_Ajax_Trip_Pricing_Categories_Taxonomy {
	public static function init() {
		// Remove item from trip
		add_action( 'wp_ajax_wp_travel_get_trip_pricing_categories_terms', array( __CLASS__, 'get_trip_pricing_categories_terms' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_pricing_categories_terms', array( __CLASS__, 'get_trip_pricing_categories_terms' ) );

		add_action( 'wp_ajax_wp_travel_get_trip_pricing_categories_term', array( __CLASS__, 'get_trip_pricing_categories_term' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_pricing_categories_term', array( __CLASS__, 'get_trip_pricing_categories_term' ) );
	}

	public static function get_trip_pricing_categories_terms() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$response = WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy::get_trip_pricing_categories_terms();
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function get_trip_pricing_categories_term() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		WP_Travel::verify_nonce();
		$category_id = ! empty( $_GET['pricing_category_id'] ) ? absint( $_GET['pricing_category_id'] ) : 0;
		$response    = WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy::get_trip_pricing_categories_term( $category_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}
}

WP_Travel_Ajax_Trip_Pricing_Categories_Taxonomy::init();