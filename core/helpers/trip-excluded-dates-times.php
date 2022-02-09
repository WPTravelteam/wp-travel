<?php
/**
 * Helpers class for trip excluded dates and times.
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
 * Helpers class for trip excluded dates and times.
 */
class WpTravel_Helpers_Trip_Excluded_Dates_Times {

	/**
	 * Table name.
	 *
	 * @var string $table_name.
	 */
	private static $table_name = 'wt_excluded_dates_times';

	/**
	 * Return trip dates and times.
	 *
	 * @param int $trip_id Trip ID.
	 */
	public static function get_dates_times( $trip_id = false ) {

		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		global $wpdb;

		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wt_excluded_dates_times WHERE `trip_id` = %d", $trip_id ) );
		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_EXCLUDED_DATE_TIME' );
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
			$dates[ $index ]['trip_time']               = ! empty( $result->time ) ? $result->time : '';
			$dates[ $index ]['recurring_weekdays_type'] = '';
			if ( ! empty( $result->days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_days';
			} elseif ( ! empty( $result->date_days ) ) {
				$dates[ $index ]['recurring_weekdays_type'] = 'every_date_days';
			}
			$index++;
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_EXCLUDED_DATES_TIMES',
			array(
				'dates_times' => $dates,
			)
		);
	}

	/**
	 * Update trip dates and times.
	 *
	 * @param int   $trip_id Trip ID.
	 * @param array $dates Dates array.
	 */
	public static function update_dates_times( $trip_id, $dates ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $dates ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_EXCLUDED_DATES_TIMES' );
		}

		// Remove old dates and time.
		self::remove_dates_times( $trip_id );

		$response = array();
		foreach ( $dates as $date ) {
			$response[] = self::add_individual_date_time( $trip_id, $date );
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_TRIP_EXCLUDED_DATES_TIMES',
			array(
				'dates_times' => $response,
			)
		);
	}

	/**
	 * Add date and time to individual trips.
	 *
	 * @param int    $trip_id Trip ID.
	 * @param string $date Trip date.
	 */
	public static function add_individual_date_time( $trip_id, $date ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		if ( empty( $date ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_EXCLUDED_DATE' );
		}
		global $wpdb;
		$table = $wpdb->prefix . self::$table_name;
		$wpdb->insert(
			$table,
			array(
				'trip_id'    => $trip_id,
				'title'      => ! empty( $date['title'] ) ? $date['title'] : '',
				'recurring'  => ! empty( $date['is_recurring'] ) ? absint( $date['is_recurring'] ) : 0,
				'years'      => ! empty( $date['years'] ) ? $date['years'] : '',
				'months'     => ! empty( $date['months'] ) ? $date['months'] : '',
				'weeks'      => ! empty( $date['weeks'] ) ? $date['weeks'] : '',
				'days'       => ! empty( $date['days'] ) ? $date['days'] : '',
				'date_days'  => ! empty( $date['date_days'] ) ? $date['date_days'] : '',
				'start_date' => ! empty( $date['start_date'] ) ? $date['start_date'] : '',
				'end_date'   => ! empty( $date['end_date'] ) ? $date['end_date'] : '',
				'time'       => ! empty( $date['trip_time'] ) ? $date['trip_time'] : '',
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
	 * Remove date and time to individual trips.
	 *
	 * @param int $trip_id Trip ID.
	 */
	public static function remove_dates_times( $trip_id ) {
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
}
