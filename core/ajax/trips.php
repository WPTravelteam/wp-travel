<?php
class WP_Travel_Ajax_Trips {

	/**
	 * Initialize Ajax requests.
	 */
	public static function init() {
		 // Remove item from cart.
		add_action( 'wp_ajax_wp_travel_update_trip', array( __CLASS__, 'update_trip' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_update_trip', array( __CLASS__, 'update_trip' ) );

		// Get item from trip.
		add_action( 'wp_ajax_wp_travel_get_trip', array( __CLASS__, 'get_trip' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip', array( __CLASS__, 'get_trip' ) );

		// Filter item.
		add_action( 'wp_ajax_wp_travel_filter_trips', array( __CLASS__, 'filter_trips' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_filter_trips', array( __CLASS__, 'filter_trips' ) );

		// Filter trip ids .
		add_action( 'wp_ajax_wp_travel_get_trip_ids', array( __CLASS__, 'get_trip_ids' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_ids', array( __CLASS__, 'get_trip_ids' ) );

		// Trip tab.
		add_action( 'wp_ajax_wp_travel_get_trip_tabs', array( __CLASS__, 'trip_tabs' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_tabs', array( __CLASS__, 'trip_tabs' ) );
	}

	public static function update_trip() {
		/**
		 * Permission Check
		 */

		$permission = self::get_trip_permission_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$trip_id   = ! empty( $_GET['trip_id'] ) ? $_GET['trip_id'] : 0;
		$post_type = get_post_type_object( WP_TRAVEL_POST_TYPE );

		if ( ! current_user_can( $post_type->cap->edit_post, $trip_id ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$postData = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		
		$response = WP_Travel_Helpers_Trips::update_trip( $trip_id, $postData );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function update_trip_permission_check() {

		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'wp_travel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}

		// Empty parameter.
		if ( empty( $_REQUEST['trip_id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( $_REQUEST['trip_id'] );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}

		if ( $trip && ! self::check_update_permission( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
		}

		return true;
	}

	protected static function check_update_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		return current_user_can( $post_type->cap->edit_post, $post->ID );
	}

	public static function get_trip() {

		$permission = self::get_trip_permission_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$trip_id  = ! empty( $_GET['trip_id'] ) ? $_GET['trip_id'] : 0;
		$response = WP_Travel_Helpers_Trips::get_trip( $trip_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	public static function get_trip_permission_check() {

		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'wp_travel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}
		// Empty parameter.
		if ( empty( $_REQUEST['trip_id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( $_REQUEST['trip_id'] );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}

		if ( $trip ) {
			return self::check_read_permission( $trip );
		}

		return true;
	}

	protected static function check_read_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		// Is the post readable?
		if ( 'publish' === $post->post_status || current_user_can( $post_type->cap->read_post, $post->ID ) ) {
			return true;
		}

		return false;
	}

	public static function get_trips() {
		self::filter_trips();
	}

	/**
	 * Filter Trips according to Param.
	 */
	public static function filter_trips() {

		/**
		 * Permission Check
		 */
		$permission = self::get_trips_permissions_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}
		 /**
		 * Return list of filtered trips according to conditions.
		 *
		 * @todo Check Nonce.
		 */

		$start_date       = ! empty( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date         = ! empty( $_GET['end_date'] ) ? $_GET['end_date'] : '';
		$travel_locations = ! empty( $_GET['travel_locations'] ) ? $_GET['travel_locations'] : '';
		$itinerary_types  = ! empty( $_GET['itinerary_types'] ) ? $_GET['itinerary_types'] : '';
		$max_pax          = ! empty( $_GET['max_pax'] ) ? $_GET['max_pax'] : '';

		$args = array(
			'start_date'       => $start_date,
			'end_date'         => $end_date,
			'travel_locations' => $travel_locations,
			'max_pax'          => $max_pax,
			'itinerary_types'  => $itinerary_types,
		);

		$response = WP_Travel_Helpers_Trips::filter_trips( $args );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Filter Trips according to Param.
	 */
	public static function get_trip_ids() {

		/**
		 * Permission Check
		 */
		$permission = self::get_trips_permissions_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}
		 /**
		 * Return list of filtered trips according to conditions.
		 *
		 * @todo Check Nonce.
		 */

		$start_date       = ! empty( $_GET['start_date'] ) ? $_GET['start_date'] : '';
		$end_date         = ! empty( $_GET['end_date'] ) ? $_GET['end_date'] : '';
		$min_price        = ! empty( $_GET['min_price'] ) ? $_GET['min_price'] : 0;
		$max_price        = ! empty( $_GET['max_price'] ) ? $_GET['max_price'] : 0;
		$travel_locations = ! empty( $_GET['travel_locations'] ) ? $_GET['travel_locations'] : ''; // Not used yet to get trip id
		$itinerary_types  = ! empty( $_GET['itinerary_types'] ) ? $_GET['itinerary_types'] : ''; // Not used yet to get trip id
		$max_pax          = ! empty( $_GET['max_pax'] ) ? $_GET['max_pax'] : ''; // Not used yet to get trip id

		$args = array(
			'start_date'       => $start_date,
			'end_date'         => $end_date,
			'min_price'        => $min_price,
			'max_price'        => $max_price,
			'travel_locations' => $travel_locations,
			'max_pax'          => $max_pax,
			'itinerary_types'  => $itinerary_types,
		);

		$response = WP_Travel_Helpers_Trips::get_trip_ids( $args );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Filter Trips according to Param.
	 */
	public static function trip_tabs() {

		/**
		 * Permission Check
		 */
		$permission = self::get_trips_permissions_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		// Empty parameter.
		if ( empty( $_REQUEST['trip_id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$trip_id = $_REQUEST['trip_id'];

		$wp_travel_use_global_tabs    = get_post_meta( $trip_id, 'wp_travel_use_global_tabs', true );
		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );

		$default_tabs = wp_travel_get_default_trip_tabs();
		$tabs         = wp_travel_get_admin_trip_tabs( $trip_id, $enable_custom_itinerary_tabs );

		$response = WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_TABS',
			array(
				'trip_tabs' => $tabs,
			)
		);

		return WP_Travel_Helpers_REST_API::response( $response );
	}


	public static function get_trips_permissions_check() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_nonce'], 'wp_travel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
			return WP_Travel_Helpers_REST_API::response( $error );
		}

		return true;
	}
}

WP_Travel_Ajax_Trips::init();
