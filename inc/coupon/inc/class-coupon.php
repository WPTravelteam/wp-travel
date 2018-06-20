<?php
/**
 * WP_Travel_Coupon Main Class
 */
class WP_Travel_Coupon {

	/**
	 * Coupons Data array, with defaults.
	 *
	 * @since 1.4.0
	 * @var array
	 */
	protected $data = array(
		'coupon_code'                 => '',
		'coupon_value'                => 0,
		'coupon_expiry_date'          => null,
		'coupon_type'                 => 'fixed',
		'usage_count'                 => 0,
		'restricted_trips'            => array(),
		'usage_limit'                 => 0,
	);

	public function __construct() {

	}

	/**
	 * Get coupon id.
	 */
	public function get_coupon_id_by_code( $string ) {

		global $wpdb;

		$meta_key  = 'wp_travel_coupon_code';

		$sql = $wpdb->prepare( "
			SELECT post_id
			FROM $wpdb->postmeta
			WHERE meta_key = %s 
			AND meta_value = %s
		", $meta_key, esc_sql( $string ) );

		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {

			return false;
		}

		return $results['0']->post_id;

	}
	/**
	 * Get Coupon Metas
	 *
	 * @param int    $coupon_id coupon id.
	 * @param string $tab tab key.
	 * @param string $key meta key.
	 * @return mixed meta value or false.
	 */
	public function get_coupon_meta( $coupon_id, $tab, $key ) {

		if ( empty( $coupon_id ) || empty( $key ) || empty( $tab ) ) {

			return false;
		}

		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );

		if ( ! $coupon_metas ) {

			return false;

		}

		if ( is_array( $coupon_metas ) && ! empty( $coupon_metas ) ) {

			return isset( $coupon_metas[ $tab ][ $key ] ) ? $coupon_metas[ $tab ][ $key ] : false;

		}

		return false;

	}

	/**
	 * get usage count
	 */
	public function get_usage_count( $coupon_id ) {

		return get_post_meta( $coupon_id, 'wp_travel_coupon_uasge_count', true );
	}
	/**
	 * Update usage count.
	 */
	public function update_usage_count( $coupon_id, $value ) {

		return update_post_meta( $coupon_id, 'wp_travel_coupon_uasge_count', $value );

	}
	/**
	 * Allowed trip_ids
	 */
	public function trip_ids_allowed( $coupon_id, $trip_ids ) {

		if ( empty( $coupon_id ) ) {

			return false;
		}

		if ( empty( $trip_ids ) || ! is_array( $trip_ids ) ) {

			return false;

		}

		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
 		$restrictions_tab  = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		// Field Values.
		$restricted_trips     = isset( $restrictions_tab['restricted_trips'] ) ? $restrictions_tab['restricted_trips'] : array();

		if ( empty( $restricted_trips ) ) {

			return false;
		}

		foreach ( $trip_ids as $key => $trip_id ){

			if ( in_array( $trip_id, $restricted_trips ) ) {

				return true;
			}

		}

		return false;

	}

	/**
	 * Is Valid Check Coupon Validity check
	 */
	public function is_coupon_valid( $coupon_id ){

		if ( empty( $coupon_id ) ) {
			return false;
		}

		$coupon_metas       = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$general_tab        = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
		$coupon_expiry_date = isset( $general_tab['coupon_expiry_date'] ) ? $general_tab['coupon_expiry_date'] : '';

		// Check Coupon Status.
		$coupon_status = get_post_status( $coupon_id );

		if ( 'publish' !== $coupon_status ) {
			return false;
		}
		if ( ! empty( $coupon_expiry_date ) ) {

			// Check Expiry Date.
			$date_now  = ( new DateTime() )->format( 'Y-m-d' );
			$test_date = ( new DateTime( $expiry_date ) )->format( 'Y-m-d' );

			if ( strtotime( $date_now ) > strtotime( $test_date ) ) {

				return false;

			}

		}

		return true;

	}



}

// new WP_Travel_Coupon();
