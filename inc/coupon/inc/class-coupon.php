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
		'coupon_code'        => '',
		'coupon_value'       => 0,
		'coupon_expiry_date' => null,
		'coupon_type'        => 'fixed',
		'usage_count'        => 0,
		'restricted_trips'   => array(),
		'usage_limit'        => 0,
	);

	public function __construct() {
		add_action( 'wp_travel_action_before_booking_process', array( $this, 'process_update_count' ) );
	}

	/**
	 * Update usese count action.
	 */
	public function process_update_count() {
		global $wt_cart;
		if ( isset( $wt_cart ) ) {
			$discounts = $wt_cart->get_discounts();
			if ( is_array( $discounts ) && ! empty( $discounts ) ) :
				$this->update_usage_count( $discounts['coupon_id'] );
			endif;
		}
	}
	/**
	 * Get coupon id.
	 */
	public function get_coupon_id_by_code( $string ) {

		global $wpdb;

		$meta_key = 'wp_travel_coupon_code';

		$sql = $wpdb->prepare(
			"
			SELECT post_id
			FROM $wpdb->postmeta
			WHERE meta_key = %s
			AND meta_value = %s
		",
			$meta_key,
			esc_sql( $string )
		);

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
	 * get discount type
	 */
	public function get_discount_type( $coupon_id ) {

		// General Tab Data.
		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$general_tab  = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
		$coupon_type  = isset( $general_tab['coupon_type'] ) ? $general_tab['coupon_type'] : 'fixed';

		return $coupon_type;

	}
	/**
	 * get discount type
	 * Note: [Deprecated due to not suitable naming. use get_discount_value of this class ]
	 */
	public function get_discount_amount( $coupon_id ) {

		// General Tab Data.
		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$general_tab  = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
		$coupon_value = isset( $general_tab['coupon_value'] ) ? $general_tab['coupon_value'] : '';

		return $coupon_value;

	}

	/**
	 * Return the Discount value. Value may be either in flat amount or in percent depending upon the discount type.
	 *
	 * @param Number $coupon_id Current coupon/post id.
	 *
	 * @since 4.4.7
	 * @return Number Discount value
	 */
	public function get_discount_value( $coupon_id ) {
		// Meta.
		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		return isset( $coupon_metas['general'] ) && isset( $coupon_metas['general']['coupon_value'] ) && $coupon_metas['general']['coupon_value'] ? $coupon_metas['general']['coupon_value'] : 0;
	}

	/**
	 * get usage count
	 */
	public function get_usage_count( $coupon_id ) {

		$usage_count = get_post_meta( $coupon_id, 'wp_travel_coupon_uasge_count', true );

		return ! empty( $usage_count ) ? $usage_count : 0;
	}
	/**
	 * Update usage count.
	 */
	public function update_usage_count( $coupon_id ) {

		$old_value = $this->get_usage_count( $coupon_id );

		$value = $old_value + 1;

		$value = absint( $value );

		return update_post_meta( $coupon_id, 'wp_travel_coupon_uasge_count', $value );

	}
	/**
	 * Allowed trip_ids
	 *
	 * @todo Do not use. Need to deprecated this method. use is_discountable instead
	 */
	public function trip_ids_allowed( $coupon_id, $trip_ids ) {

		if ( empty( $coupon_id ) ) {

			return false;
		}

		if ( empty( $trip_ids ) || ! is_array( $trip_ids ) ) {

			return false;

		}

		$coupon_metas     = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		// Field Values.
		$restricted_trips = isset( $restrictions_tab['restricted_trips'] ) ? $restrictions_tab['restricted_trips'] : array();

		if ( empty( $restricted_trips ) ) {

			return true;
		}

		foreach ( $trip_ids as $key => $trip_id ) {

			if ( in_array( $trip_id, $restricted_trips ) ) {

				return true;
			}
		}

		return false;

	}

	/**
	 * Check whether trip has discount or not.
	 *
	 * @param Number $coupon_id ID of the coupon.
	 * @param Number $trip_id Trip id to check whether this trip has discount or not.
	 *
	 * @since 4.4.7
	 *
	 * @return Boolean
	 */
	public function is_discountable( $coupon_id, $trip_id ) {

		if ( empty( $coupon_id ) || empty( $trip_id ) ) {
			return false;
		}

		$coupon_metas     = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		// Field Values.
		$restricted_trips = isset( $restrictions_tab['restricted_trips'] ) ? $restrictions_tab['restricted_trips'] : array();

		if ( empty( $restricted_trips ) ) {
			return true;
		}
		if ( in_array( $trip_id, $restricted_trips ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Check whether trip has uses count. if uses count set and cross the limit then this will treat as not usable.
	 *
	 * @param Number $coupon_id ID of the coupon.
	 *
	 * @since 4.4.7
	 *
	 * @return Boolean
	 */
	public function is_limit_exceed( $coupon_id ) {

		$coupon_metas        = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab    = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$coupon_limit_number = isset( $restrictions_tab['coupon_limit_number'] ) ? $restrictions_tab['coupon_limit_number'] : '';

		if ( ! empty( $coupon_limit_number ) ) {
			$usage_count = WPTravel()->coupon->get_usage_count( $coupon_id );
			if ( absint( $usage_count ) >= absint( $coupon_limit_number ) ) {
				return true;
			}
		}
		return false;
	}


	/**
	 * Check whether trip has for specific user or not.
	 *
	 * @param Number $coupon_id ID of the coupon.
	 *
	 * @since 5.0.0
	 *
	 * @return Boolean
	 */
	public function valid_for_user( $coupon_id ) {

		$coupon_metas     = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		$restrictions_tab = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$coupon_user_id   = isset( $restrictions_tab['coupon_user_id'] ) ? $restrictions_tab['coupon_user_id'] : '';

		if ( ! empty( $coupon_user_id ) ) {
			if ( ! is_user_logged_in() ) {
				return false;
			}
			$current_user    = wp_get_current_user();
			$current_user_id = $current_user->data->ID;
			if ( absint( $current_user_id ) === absint( $coupon_user_id ) ) {
				return true;
			}
			return false;
		}
		return true;
	}

	/**
	 * Get Discount applicable total/partial total along with extras price.
	 *
	 * @param Number $coupon_id ID of the coupon.
	 * @param Bool   $partial_total Either return partial total or full total.
	 *
	 * @since 4.4.7
	 *
	 * @return Boolean
	 */
	public function get_discount_applicable_total( $coupon_id, $partial_total = false ) {
		if ( ! $coupon_id ) {
			return 0;
		}
		global $wt_cart;

		$items = $wt_cart->getItems();

		$discount_applicable_total = 0;

		// loop cart items here.
		if ( ! $partial_total ) {
			foreach ( $items as $cart_item_id => $item ) {
				$trip_id = $item['trip_id'];
				if ( WPTravel()->coupon->is_discountable( $coupon_id, $trip_id ) ) {
					$discount_applicable_total += $wt_cart->get_item_total( $cart_item_id );
				}
			}
		} else {
			// Implement Partial in Total amount. It treat as parital is also applied in extras items.
			foreach ( $items as $cart_item_id => $item ) {
				$trip_id = $item['trip_id'];
				if ( WPTravel()->coupon->is_discountable( $coupon_id, $trip_id ) ) {
					$item_total = $wt_cart->get_item_total( $cart_item_id );

					if ( wptravel_is_partial_payment_enabled() ) {
						$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );

						$item_total                 = ( $item_total * $payout_percent ) / 100;
						$discount_applicable_total += $item_total;
					} else {
						$discount_applicable_total += $item_total;
					}
				}
			}
		}
		return wptravel_get_formated_price( $discount_applicable_total );
	}

	/**
	 * Is Valid Check Coupon Validity check
	 */
	public function is_coupon_valid( $coupon_id ) {

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
			$date_now  = new DateTime();
			$test_date = new DateTime( $coupon_expiry_date );

			// Check Expiry Date.
			$date_now  = $date_now->format( 'Y-m-d' );
			$test_date = $test_date->format( 'Y-m-d' );

			if ( strtotime( $date_now ) > strtotime( $test_date ) ) {

				return false;

			}
		}

		return true;

	}

	/**
	 * Get Coupon Status.
	 */
	public function get_coupon_status( $coupon_id ) {

		if ( ! $coupon_id || empty( $coupon_id ) ) {

			return false;
		}
		if ( ! get_post_meta( $coupon_id, 'wp_travel_coupon_code', true ) ) {
			return;
		}

		if ( ! $this->is_coupon_valid( $coupon_id ) ) {

			return 'inactive';

		}

		// Activity by usage count.
		$usage_count = $this->get_usage_count( $coupon_id );
		$limit       = $this->get_coupon_meta( $coupon_id, 'restriction', 'coupon_limit_number' );

		if ( ! empty( $limit ) ) {

			return ( $limit <= $usage_count ) ? 'limit_exceed' : 'active';
		}

		return 'active';

	}
}
