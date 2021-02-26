<?php
/**
 * Trip Ajax actions. Get, update trips.
 *
 * @package core
 */

/**
 * Ajax class to get and update trips.
 */
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

	/**
	 * Update Trip.
	 */
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

		// Nonce already verified.
		$trip_id   = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0; //@phpcs:ignore
		$post_type = get_post_type_object( WP_TRAVEL_POST_TYPE );

		if ( ! current_user_can( $post_type->cap->edit_post, $trip_id ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$post_data = json_decode( file_get_contents( 'php://input' ), true ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = wptravel_sanitize_array( $post_data, true );
		$response  = WP_Travel_Helpers_Trips::update_trip( $trip_id, $post_data );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Check permission before proceed.
	 */
	public static function update_trip_permission_check() {

		// already sanitized.
		$requests = WP_Travel::get_sanitize_request( 'request' );

		// Empty parameter.
		if ( empty( $requests['trip_id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( absint( $requests['trip_id'] ) );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}

		if ( $trip && ! self::check_update_permission( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
		}

		return true;
	}

	/**
	 * Checks update permission check.
	 *
	 * @param object $post Post object.
	 */
	protected static function check_update_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		return current_user_can( $post_type->cap->edit_post, $post->ID );
	}

	/**
	 * Get Trip data.
	 */
	public static function get_trip() {

		$permission = self::get_trip_permission_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}

		$trip_id  = ! empty( $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : 0;
		$response = WP_Travel_Helpers_Trips::get_trip( $trip_id );
		WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Check permission before proceed.
	 */
	public static function get_trip_permission_check() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'wp_travel_nonce' ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
		}
		// Empty parameter.
		if ( empty( $_REQUEST['trip_id'] ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( sanitize_text_field( wp_unslash( $_REQUEST['trip_id'] ) ) );
		if ( is_wp_error( $trip ) ) {
			return $trip;
		}

		if ( $trip ) {
			return self::check_read_permission( $trip );
		}

		return true;
	}

	/**
	 * Check permission before proceed.
	 */
	protected static function check_read_permission( $post ) {
		$post_type = get_post_type_object( $post->post_type );

		// Is the post readable?
		if ( 'publish' === $post->post_status || current_user_can( $post_type->cap->read_post, $post->ID ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get trips.
	 */
	public static function get_trips() {
		self::filter_trips();
	}

	/**
	 * Filter Trips according to Param.
	 */
	public static function filter_trips() {

		/**
		 * Permission and nonce Check
		 */
		$permission = self::get_trips_permissions_check();

		if ( is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		} elseif ( false === $permission || null === $permission ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
			WP_Travel_Helpers_REST_API::response( $error );
		}
		/**
		 * Return list of filtered trips according to conditions. Nonce already checked.
		 */
		$start_date       = ! empty( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : ''; // @phpcs:ignore
		$end_date         = ! empty( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : ''; // @phpcs:ignore
		$travel_locations = ! empty( $_GET['travel_locations'] ) ? sanitize_text_field( wp_unslash( $_GET['travel_locations'] ) ) : ''; // @phpcs:ignore
		$itinerary_types  = ! empty( $_GET['itinerary_types'] ) ? sanitize_text_field( wp_unslash( $_GET['itinerary_types'] ) ) : ''; // @phpcs:ignore
		$max_pax          = ! empty( $_GET['max_pax'] ) ? absint( $_GET['max_pax'] ) : ''; // @phpcs:ignore

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
		 * Permission and nonce Check
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
		 */
		$start_date       = ! empty( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : ''; // @phpcs:ignore
		$end_date         = ! empty( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : ''; // @phpcs:ignore
		$min_price        = ! empty( $_GET['min_price'] ) ? sanitize_text_field( wp_unslash( $_GET['min_price'] ) ) : 0; // @phpcs:ignore
		$max_price        = ! empty( $_GET['max_price'] ) ? sanitize_text_field( wp_unslash( $_GET['max_price'] ) ) : 0; // @phpcs:ignore

		// Not used yet to get trip id.
		$travel_locations = ! empty( $_GET['travel_locations'] ) ? sanitize_text_field( wp_unslash( $_GET['travel_locations'] ) ) : ''; // @phpcs:ignore
		$itinerary_types  = ! empty( $_GET['itinerary_types'] ) ? sanitize_text_field( wp_unslash( $_GET['itinerary_types'] ) ) : ''; // @phpcs:ignore
		$max_pax          = ! empty( $_GET['max_pax'] ) ? absint( $_GET['max_pax'] ) : ''; // @phpcs:ignore

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

		// Empty parameter. Nonce already verified.
		if ( empty( $_REQUEST['trip_id'] ) ) { // @phpcs:ignore
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$trip_id = absint( $_REQUEST['trip_id'] ); // @phpcs:ignore

		$wp_travel_use_global_tabs    = get_post_meta( $trip_id, 'wp_travel_use_global_tabs', true );
		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );

		$default_tabs = wptravel_get_default_trip_tabs();
		$tabs         = wptravel_get_admin_trip_tabs( $trip_id, $enable_custom_itinerary_tabs );

		$response = WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_TABS',
			array(
				'trip_tabs' => $tabs,
			)
		);

		return WP_Travel_Helpers_REST_API::response( $response );
	}

	/**
	 * Nonce.
	 */
	public static function get_trips_permissions_check() {
		/**
		 * Nonce Verification.
		 */
		if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'wp_travel_nonce' ) ) {
			$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
			return WP_Travel_Helpers_REST_API::response( $error );
		}

		return true;
	}
}

WP_Travel_Ajax_Trips::init();
