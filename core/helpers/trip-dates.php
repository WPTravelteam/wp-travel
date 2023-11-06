<?php
/**
 * Helper class for the trip dates.
 *
 * @package WP_Travel
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper class for the trip dates.
 */
class WpTravel_Helpers_Trip_Dates {

	/**
	 * WP Travel table name key.
	 *
	 * @var string $table_name.
	 */
	private static $table_name = 'wt_dates';

	/**
	 * Return the trip dates.
	 *
	 * @param int $trip_id Trip ID.
	 */
	public static function get_dates( $trip_id = false ) {

		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_dates WHERE `trip_id` = %d", $trip_id ) );
		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_DATES' );
		}

		$dates = array();
		$index = 0;
		foreach ( $results as $result ) {
			$dates[ $index ]['id']           = absint( $result->id );
			$dates[ $index ]['title']        = $result->title;
			$dates[ $index ]['years']        = empty( $result->years ) ? 'every_year' : $result->years;
			$dates[ $index ]['months']       = empty( $result->months ) ? 'every_month' : $result->months;
			$dates[ $index ]['days']         = empty( $result->days ) ? '' : $result->days;
			$dates[ $index ]['date_days']    = empty( $result->date_days ) ? '' : $result->date_days;
			$dates[ $index ]['start_date']   = $result->start_date;
			$dates[ $index ]['end_date']     = $result->end_date;
			$dates[ $index ]['is_recurring'] = ! empty( $result->recurring ) && class_exists( 'WP_Travel_Pro' ) ? true : false;
			/**
			 * @since 6.1.0
			 */
			$dates[ $index ] ['enable_time']            = ! empty( absint( $result->id ) ) && class_exists( 'WP_Travel_Utilities_Core' ) ? get_post_meta( absint( $result->id ), 'wp_travel_trip_time_enable', true ) : false;
			$dates[ $index ] ['twentyfour_time_format']            = ! empty( absint( $result->id ) ) && class_exists( 'WP_Travel_Utilities_Core' ) ? get_post_meta( absint( $result->id ), 'wp_travel_trip_twentyfour_time_format', true ) : false;
			$dates[ $index ]['trip_time']               = ! empty( $result->trip_time ) && class_exists( 'WP_Travel_Utilities_Core' ) ? $result->trip_time : ''; // Time is utilities features.
			$dates[ $index ]['pricing_ids']             = ! empty( $result->pricing_ids ) ? $result->pricing_ids : '';
			$dates[ $index ]['recurring_weekdays_type'] = '';
			if ( ! empty( $result->days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_days';
			} elseif ( ! empty( $result->date_days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_date_days';
			}
			$index++;
		}

		if ( ! is_admin() ) {
			/**
			 * Filter to change available dates data as per trip.
			 *
			 * @since 5.2.3
			 * @since 5.2.4 Only availble this hook for frontend.
			 */
			$dates = apply_filters( 'wptravel_trip_dates', $dates, $trip_id );
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_DATES',
			array(
				'dates' => $dates,
			)
		);
	}

	/**
	 * Update the trip dates.
	 *
	 * @param int   $trip_id Trip ID.
	 * @param array $dates Trip Dates.
	 */
	public static function update_dates( $trip_id, $dates ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $dates ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_DATES' );
		}

		$trip_dates = array(); // collection of trip dates to get next departure date.
		foreach ( $dates as $date ) {
			if ( $date['start_date'] && gmdate( 'Y-m-d ', strtotime( $date['start_date'] ) ) >= gmdate( 'Y-m-d' ) ) {
				$trip_dates[] = $date['start_date'];
			}
			self::add_individual_date( $trip_id, $date );
		}

		if ( is_array( $trip_dates ) && count( $trip_dates ) > 0 ) {
			usort( $trip_dates, 'wptravel_date_sort' );
			update_post_meta( $trip_id, 'trip_date', $trip_dates[0] ); // To sort trip according to date.
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_DATES',
			array(
				'dates' => $dates,
			)
		);
	}

	/**
	 * Add individual date to trips.
	 *
	 * @param int   $trip_id Trip ID.
	 * @param array $date Trip date array.
	 */
	public static function add_individual_date( $trip_id, $date ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $date ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_DATE' );
		}
		global $wpdb;
		$table       = $wpdb->prefix . self::$table_name;
		$date_id     = ! empty( $date['id'] ) ? $date['id'] : '';
		$pricing_ids = ! empty( $date['pricing_ids'] ) ? $date['pricing_ids'] : '';
		if ( $pricing_ids ) { // Need to sort pricing id in dates table to display pricing as per sorted.
			$result      = $wpdb->get_row( $wpdb->prepare( "SELECT GROUP_CONCAT( id ORDER BY sort_order ASC ) AS pricing_ids FROM {$wpdb->prefix}wt_pricings WHERE trip_id=%d AND id IN( $pricing_ids )", $trip_id ) ); // @phpcs:ignore
			$pricing_ids = $result->pricing_ids;
		}
		$dates_data = array(
			'trip_id'     => $trip_id,
			'title'       => ! empty( $date['title'] ) ? $date['title'] : '',
			'recurring'   => ! empty( $date['is_recurring'] ) ? absint( $date['is_recurring'] ) : 0,
			'years'       => ! empty( $date['years'] ) ? $date['years'] : '',
			'months'      => ! empty( $date['months'] ) ? $date['months'] : '',
			'weeks'       => ! empty( $date['weeks'] ) ? $date['weeks'] : '',
			'days'        => ! empty( $date['days'] ) ? $date['days'] : '',
			'date_days'   => ! empty( $date['date_days'] ) ? $date['date_days'] : '',
			'start_date'  => ! empty( $date['start_date'] ) ? $date['start_date'] : '',
			'end_date'    => ! empty( $date['end_date'] ) ? $date['end_date'] : '',
			'trip_time'   => ! empty( $date['trip_time'] ) ? $date['trip_time'] : '',
			'pricing_ids' => $pricing_ids,
		);
		if ( $date_id ) {
			$wpdb->update(
				$table,
				$dates_data,
				array( 'id' => $date_id ),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				),
				array( '%d' )
			);
			/**
			 * @since 6.1.0
			 */
			update_post_meta( $date_id, 'wp_travel_trip_time_enable', ! empty( $date['enable_time'] ) ? $date['enable_time'] : false );
			update_post_meta( $date_id, 'wp_travel_trip_twentyfour_time_format', ! empty( $date['twentyfour_time_format'] ) ? $date['twentyfour_time_format'] : false );
		} else {
			$wpdb->insert(
				$table,
				$dates_data,
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
				)
			);
			$inserted_id = $wpdb->insert_id;
			if ( empty( $inserted_id ) ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_ADDING_TRIP_DATE' );
			}
			/**
			 * @since 6.1.0
			 */
			$date['ids'] = $inserted_id ? $inserted_id : 0;
			update_post_meta( $inserted_id, 'wp_travel_trip_time_enable', ! empty( $date['enable_time'] ) ? $date['enable_time'] : false );
			update_post_meta( $inserted_id, 'wp_travel_trip_twentyfour_time_format', ! empty( $date['twentyfour_time_format'] ) ? $date['twentyfour_time_format'] : false );
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_ADDED_TRIP_DATE',
			array(
				'date' => $date,
			)
		);
	}

	/**
	 * Remove trip dates.
	 *
	 * @param int $trip_id Trip ID.
	 */
	public static function remove_dates( $trip_id ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		/**
		 * @since 6.1.0
		 */
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table WHERE `trip_id` = %d", $trip_id ) );
		if ( ! empty( $results ) ) {
			foreach ( $results as $date_result ) {
				delete_post_meta( $date_result->id, 'wp_travel_trip_time_enable' );
				delete_post_meta( $date_result->id, 'wp_travel_trip_twentyfour_time_format' );
			}
		}

		$result = $wpdb->delete( $table, array( 'trip_id' => absint( $trip_id ) ), array( '%d' ) );

		if ( false === $result ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_DELETING_TRIP_DATES' );
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response( 'WP_TRAVEL_REMOVED_TRIP_DATES' );

	}

	/**
	 * Remove individual trip dates.
	 *
	 * @param int $date_id Trip date ID.
	 */
	public static function remove_individual_date( $date_id ) {
		if ( empty( $date_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_DATE_ID' );
		}

		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		/**
		 * @since 6.1.0
		 */
		delete_post_meta( $date_id, 'wp_travel_trip_time_enable' );
		delete_post_meta( $date_id, 'wp_travel_trip_twentyfour_time_format' );
		$result = $wpdb->delete( $table, array( 'id' => $date_id ), array( '%d' ) );

		if ( false === $result ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_ERROR_DELETING_DATE' );
		}

		WP_Travel_Helpers_Trip_Pricing_Categories::remove_trip_pricing_categories( $date_id );

		return WP_Travel_Helpers_Response_Codes::get_success_response( 'WP_TRAVEL_REMOVED_TRIP_DATE' );
	}

	/**
	 * Check whether it is fixed departure trip or not.
	 *
	 * @param int     $trip_id Trip id of the trip.
	 * @param boolean $check_for_multiple_departure Only for Legacy version less than V4.
	 * @since 4.4.5
	 */
	public static function is_fixed_departure( $trip_id, $check_for_multiple_departure = false ) {
		if ( ! $trip_id ) {
			return;
		}

		$post_type = get_post_type( $trip_id );
		if ( WP_TRAVEL_POST_TYPE !== $post_type ) {
			return;
		}
		$fd = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
		$fd = apply_filters( 'wp_travel_fixed_departure_defalut', $fd ); // @phpcs:ignore
		$fd = apply_filters( 'wptravel_fixed_departure_defalut', $fd );

		$switch_to_v4         = wptravel_is_react_version_enabled();
		$wp_travel_user_since = get_option( 'wp_travel_user_since' );
		if ( version_compare( $wp_travel_user_since, '4.0.0', '>=' ) || $switch_to_v4 ) {
			return 'yes' === $fd;
		} else { // Legacy.
			if ( $check_for_multiple_departure ) { // Check if multiple fixed departure enable along with fixed departure enabled.
				$multiple_fd = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );
				return ( 'yes' === $fd && 'yes' === $multiple_fd );
			} else {
				return 'yes' === $fd;
			}
		}
	}
}
