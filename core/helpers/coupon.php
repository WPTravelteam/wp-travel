<?php
/**
 * Helpers Coupon.
 *
 * @package WP_Travel
 * @since 5.0.0
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Coupon class.
 *
 * @since 5.0.0
 */
class WpTravel_Helpers_Coupon {

	/**
	 * Get Coupon Meta data along with coupon code.
	 *
	 * @param int $coupon_id Coupon id.
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function get_coupon( $coupon_id ) {
		if ( ! $coupon_id ) {
			global $post;
			if ( ! $post ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_COUPON_ID' );
			}
			$coupon_id = $post->ID;
		}
		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );

		// General Tab.
		$general_tab                = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
		$general_tab['coupon_code'] = get_post_meta( $coupon_id, 'wp_travel_coupon_code', true ); // Saved individually to search code.

		$coupon                = new WP_Travel_Coupon();
		$general_tab['status'] = ucfirst( $coupon->get_coupon_status( $coupon_id ) );

		// Restriction Tab.
		$restriction_tab                   = isset( $coupon_metas['restriction'] ) ? $coupon_metas['restriction'] : array();
		$restriction_tab['coupon_user_id'] = get_post_meta( $coupon_id, 'coupon_user_id', true );  // Saved individually to search logged in user.

		$trips       = wptravel_get_itineraries_array();
		$mapped_trip = array();
		foreach ( $trips as $id => $title ) {
			$mapped_trip[] = array(
				'id'    => $id,
				'title' => $title,
			);
		}
		$coupons = array(
			'general'     => $general_tab,
			'restriction' => $restriction_tab,

			'options'     => array(
				'trips' => $mapped_trip,
				'users' => get_users(),
			),
		);
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_COUPON',
			array(
				'coupon' => $coupons,
			)
		);
	}

	/**
	 * Get all strings used in WP Travel.
	 *
	 * @param int   $coupon_id Coupon id.
	 * @param array $data Coupon data.
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function update_coupon( $coupon_id, $data ) {
		if ( ! $coupon_id ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_COUPON_ID' );
		}

		$coupon_metas = get_post_meta( $coupon_id, 'wp_travel_coupon_metas', true );
		if ( ! $coupon_metas || ! is_array( $coupon_metas ) ) {
			$coupon_metas = array();
		}

		// General Tab.
		$general = isset( $data['general'] ) ? $data['general'] : array();
		unset( $general['status'] ); // coupon code is saved in seperate meta wp_travel_coupon_code.
		$coupon_metas['general'] = $general;
		$coupon_code             = $general['coupon_code'];

		// Restriction Tab.
		$restriction                 = isset( $data['restriction'] ) ? $data['restriction'] : array();
		$coupon_metas['restriction'] = $restriction;
		$coupon_user_id              = $restriction['coupon_user_id'];

		// Update Coupons data.
		update_post_meta( $coupon_id, 'wp_travel_coupon_metas', $coupon_metas );
		update_post_meta( $coupon_id, 'wp_travel_coupon_code', $coupon_code );
		update_post_meta( $coupon_id, 'coupon_user_id', $coupon_user_id );

		$coupon = get_post( $coupon_id );
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_COUPON',
			array(
				'coupon' => $coupon,
			)
		);
	}

	/**
	 * This will return default coupon code for any user if the coupon is for that specific user.
	 *
	 * @since 5.0.0
	 * @return string default coupon code.
	 */
	public static function get_default_coupon() {
		$coupon_code = '';
		if ( is_user_logged_in() ) {
			$user    = wp_get_current_user();
			$user_id = $user->data->ID;

			global $wpdb;
			$meta_key = 'coupon_user_id';

			$results = $wpdb->get_results( // @phpcs:ignore
				$wpdb->prepare(
					"
					SELECT post_id
					FROM $wpdb->postmeta Meta 
					join
					$wpdb->posts P
					on Meta.post_id = P.ID
					WHERE Meta.meta_key = %s
					AND Meta.meta_value = %s and P.post_status = 'publish'
				",
					$meta_key,
					esc_sql( $user_id )
				)
			);
			if ( ! empty( $results ) ) {
				foreach ( $results as $result ) {
					$coupon_id = $result->post_id;
					$total     = WPTravel()->coupon->get_discount_applicable_total( $coupon_id );
					if ( $total ) { // if discount applicable total price is greater than 0 that mean the current coupon is valid for trip.
						$coupon_code = get_post_meta( $coupon_id, 'wp_travel_coupon_code', true );
						break;
					}
				}
			}
		}
		return $coupon_code;
	}

}
