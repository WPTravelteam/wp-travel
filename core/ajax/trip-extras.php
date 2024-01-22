<?php
class WP_Travel_Ajax_Trip_Extras {
	public static function init() {
		add_action( 'wp_ajax_wp_travel_get_trip_extras', array( __CLASS__, 'get_trip_extras' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_extras', array( __CLASS__, 'get_trip_extras' ) );

		add_action( 'wp_ajax_wp_travel_search_trip_extras', array( __CLASS__, 'search_trip_extras' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_search_trip_extras', array( __CLASS__, 'search_trip_extras' ) );
	}

	public static function get_trip_extras() {

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$args = array();

		$payload = json_decode( file_get_contents( 'php://input' ) );
		$payload = is_object( $payload ) ? (array) $payload : array();
		$payload = wptravel_sanitize_array( $payload ); // Paylod to get extras data in frontend booking widget.
		if ( isset( $payload['trip_ids'] ) && count( $payload['trip_ids'] ) > 0 ) {
			$args['post__in'] = $payload['trip_ids'];
		}

		$response = WP_Travel_Helpers_Trip_Extras::get_trip_extras( $args );

		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function search_trip_extras() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$requests = WP_Travel::get_sanitize_request();

		$args['s'] = ! empty( $requests['keyword'] ) ? sanitize_text_field( $requests['keyword'] ) : '';
		$response  = WP_Travel_Helpers_Trip_Extras::get_trip_extras( $args );
		WP_Travel_Helpers_REST_API::response( $response );
	}

}

WP_Travel_Ajax_Trip_Extras::init();
