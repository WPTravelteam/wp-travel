<?php
/**
 * Helper class for the trip dates.
 *
 * @package Wptravel
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
			$dates[ $index ]['id']                      = absint( $result->id );
			$dates[ $index ]['title']                   = $result->title;
			$dates[ $index ]['years']                   = empty( $result->years ) ? 'every_year' : $result->years;
			$dates[ $index ]['months']                  = empty( $result->months ) ? 'every_month' : $result->months;
			$dates[ $index ]['days']                    = empty( $result->days ) ? '' : $result->days;
			$dates[ $index ]['date_days']               = empty( $result->date_days ) ? '' : $result->date_days;
			$dates[ $index ]['start_date']              = $result->start_date;
			$dates[ $index ]['end_date']                = $result->end_date;
			$dates[ $index ]['is_recurring']            = ! empty( $result->recurring ) ? true : false;
			$dates[ $index ]['trip_time']               = ! empty( $result->trip_time ) ? $result->trip_time : '';
			$dates[ $index ]['pricing_ids']             = ! empty( $result->pricing_ids ) ? $result->pricing_ids : '';
			$dates[ $index ]['recurring_weekdays_type'] = '';
			if ( ! empty( $result->days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_days';
			} elseif ( ! empty( $result->date_days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_date_days';
			}
			$index++;
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

		$result     = self::remove_dates( $trip_id );
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
		$table = $wpdb->prefix . self::$table_name;
		$wpdb->insert(
			$table,
			array(
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
				'pricing_ids' => ! empty( $date['pricing_ids'] ) ? $date['pricing_ids'] : '',
			),
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
	 * @since WP Travel 4.4.5
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

		$settings     = wptravel_get_settings();
		$switch_to_v4 = $settings['wp_travel_switch_to_react'];

		$wp_travel_user_since = get_option( 'wp_travel_user_since' );
		if ( version_compare( $wp_travel_user_since, '4.0.0', '>=' ) || 'yes' === $switch_to_v4 ) {
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
