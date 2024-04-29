<?php
/**
 * Trip Ajax actions. Get, update trips.
 *
 * @package WP_Travel
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
		// add_action( 'wp_ajax_wp_travel_filter_trips', array( __CLASS__, 'filter_trips' ) );
		// add_action( 'wp_ajax_nopriv_wp_travel_filter_trips', array( __CLASS__, 'filter_trips' ) );

		// Filter trip ids .
		// add_action( 'wp_ajax_wp_travel_get_trip_ids', array( __CLASS__, 'get_trip_ids' ) );
		// add_action( 'wp_ajax_nopriv_wp_travel_get_trip_ids', array( __CLASS__, 'get_trip_ids' ) );

		// Trip tab.
		add_action( 'wp_ajax_wp_travel_get_trip_tabs', array( __CLASS__, 'trip_tabs' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_get_trip_tabs', array( __CLASS__, 'trip_tabs' ) );
		/**
		 * Fixed Trip extra gallery image in backend
		 *
		 * @since 6.2.0
		 */
		add_action( 'wp_ajax_envira_gallery_load_image', array( __CLASS__, 'get_extra_gallery' ) );
		add_action( 'wp_ajax_nopriv_envira_gallery_load_image', array( __CLASS__, 'get_extra_gallery' ) );
		/**
		 * Inventory testing
		 * @since 6.5.0
		 */
		add_action( 'wp_ajax_inventory_testing', array( __CLASS__, 'wp_travel_inventory_checking' ) );
		add_action( 'wp_ajax_nopriv_inventory_testing', array( __CLASS__, 'wp_travel_inventory_checking' ) );

		add_action( 'wp_ajax_wp_travel_use_inventory_empty_cart', array( __CLASS__, 'wp_travel_cart_empty_for_inventory' ) );
		add_action( 'wp_ajax_nopriv_wp_travel_use_inventory_empty_cart', array( __CLASS__, 'wp_travel_cart_empty_for_inventory' ) );
	}

	public static function get_extra_gallery() {

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			$nonce_trip_extra  = $_POST['nonce'];
			$gallery_id        = $_POST['id'];
			$gallerys_data     = wp_get_attachment_image_src( (int) $gallery_id );
			$gallery_thumnails = wp_json_encode(
				array(
					'id'  => $gallery_id,
					'url' => $gallerys_data[0],

				)
			);
		}
	}

	/**
	 * Update Trip.
	 */
	public static function update_trip() {
		/**
		 * Permission Check
		 */

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
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
		
		/**
		 * solve json get error
		 */
		$post_data = file_get_contents( 'php://input' ); // Added 2nd Parameter to resolve issue with objects.
		$post_data = is_string( $post_data ) ? json_decode( $post_data, true ) : $post_data;
		$new_post_data = is_string( $post_data  ) ? json_decode( $post_data, true ) : $post_data;
		$new_post_data = wptravel_sanitize_array( $new_post_data, true );
		$response  = WP_Travel_Helpers_Trips::update_trip( $trip_id, $new_post_data );
		// $post_data = wptravel_sanitize_array( $post_data, true );
		// $response  = WP_Travel_Helpers_Trips::update_trip( $trip_id, $post_data );
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

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}

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
	// public static function get_trip_ids() {

	// 	/**
	// 	 * Permission and nonce Check
	// 	 */
	// 	$permission = self::get_trips_permissions_check();

	// 	if ( is_wp_error( $permission ) ) {
	// 		WP_Travel_Helpers_REST_API::response( $permission );
	// 	} elseif ( false === $permission || null === $permission ) {
	// 		$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
	// 		WP_Travel_Helpers_REST_API::response( $error );
	// 	}
	// 	/**
	// 	 * Return list of filtered trips according to conditions.
	// 	 */
	// 	$start_date = ! empty( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : ''; // @phpcs:ignore
	// 	$end_date   = ! empty( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : ''; // @phpcs:ignore
	// 	$min_price  = ! empty( $_GET['min_price'] ) ? sanitize_text_field( wp_unslash( $_GET['min_price'] ) ) : 0; // @phpcs:ignore
	// 	$max_price  = ! empty( $_GET['max_price'] ) ? sanitize_text_field( wp_unslash( $_GET['max_price'] ) ) : 0; // @phpcs:ignore

	// 	// Not used yet to get trip id.
	// 	$travel_locations = ! empty( $_GET['travel_locations'] ) ? sanitize_text_field( wp_unslash( $_GET['travel_locations'] ) ) : ''; // @phpcs:ignore
	// 	$itinerary_types  = ! empty( $_GET['itinerary_types'] ) ? sanitize_text_field( wp_unslash( $_GET['itinerary_types'] ) ) : ''; // @phpcs:ignore
	// 	$max_pax          = ! empty( $_GET['max_pax'] ) ? absint( $_GET['max_pax'] ) : ''; // @phpcs:ignore

	// 	$args = array(
	// 		'start_date'       => $start_date,
	// 		'end_date'         => $end_date,
	// 		'min_price'        => $min_price,
	// 		'max_price'        => $max_price,
	// 		'travel_locations' => $travel_locations,
	// 		'max_pax'          => $max_pax,
	// 		'itinerary_types'  => $itinerary_types,
	// 	);

	// 	$response = WP_Travel_Helpers_Trips::get_trip_ids( $args );
	// 	WP_Travel_Helpers_REST_API::response( $response );
	// }

	/**
	 * Filter Trips according to Param.
	 */
	public static function trip_tabs() {

		/**
		 * Permission Check
		 */

		$user = wp_get_current_user();
		$allowed_roles = array( 'editor', 'administrator', 'author' );

		if ( !array_intersect( $allowed_roles, $user->roles ) ) {
			return wp_send_json( array( 'result' => 'Authentication error' ) );
		}
		
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
	 * @since 6.5.0 
	 * wp travel inventory checking on checkout page
	 */
	public static function wp_travel_inventory_checking() {

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		header( 'Content-Type: application/json' );
		$json_str = file_get_contents( 'php://input' );
		$json_obj = json_decode(  $json_str );
		if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {
			$inventory_enable = 'no';
			foreach ( $json_obj->wp_travel_trip as $keys => $value ) {
				if ( isset( $value->trip_id ) ) {
					$trip_id = $value->trip_id;
					$inv = get_post_meta( $trip_id, 'enable_trip_inventory' );
					if ( isset( $inv[0] ) && $inv[0] == 'yes' ) {
						$inventory_enable = $inv[0];
						$pricing_id = isset( $value->pricing_id ) ? $value->pricing_id : '';
						$select_date = isset( $value->trip_start_date ) ? $value->trip_start_date : '';
						$trp_time = '';
						$available_pax = '';
						$inventory_size = '';
						$select_pax = isset( $value->pax ) ? $value->pax : 0;
						if ( class_exists( 'WP_Travel_Helpers_Inventory' ) ) {
							$inventorys = WP_Travel_Helpers_Inventory::get_inventory( array(
								'trip_id' => $trip_id,
								'pricing_id'	=> $pricing_id,
								'selected_date'	=> $select_date,
								'times'			=> $trp_time
							) );
							if ( isset( $inventorys['inventory'] ) ) {
								foreach ( $inventorys['inventory'] as $key => $val ) {
									$available_pax = isset( $val['pax_available'] ) ? $val['pax_available'] : 0;
									if ( $select_pax > $available_pax ) {
										return WP_Travel_Helpers_REST_API::response( array(
											'code' => 'WP_TRAVEL_INVENTORY_TESTING',
											'inventory_available' => 'no_pax',
										) );
									}
								}
							}
						}
					} 
				}
			}
		}
		return WP_Travel_Helpers_REST_API::response( array(
			'code' => 'WP_TRAVEL_INVENTORY_TESTING',
			'inventory_available' => 'yes',
		) );
	}
	public static function wp_travel_cart_empty_for_inventory() {

		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		header( 'Content-Type: application/json' );
		$json_str = file_get_contents( 'php://input' );
		$json_obj = json_decode(  $json_str );
		global $wt_cart;
		$wt_cart->clear(); 
		return WP_Travel_Helpers_REST_API::response( array(
			'code' => 'WP_TRAVEL_INVENTORY_TESTING',
			'inventory_available' => 'cart_empty',
		) );
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
