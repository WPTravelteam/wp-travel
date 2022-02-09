<?php
/**
 * Upgrade changes migration in version 175 to 176.
 *
 * @package WP_Travel
 */

if ( ! function_exists( 'wptravel_upgrade_175_176' ) ) {

	/**
	 * Upgrade changes migration.
	 */
	function wptravel_upgrade_175_176() {

		global $wpdb;

		$wptravel_datepicker_default_format = 'm/d/Y';
		$wptravel_date_format               = get_option( 'date_format' );
		$wptravel_date_migrated             = get_option( 'wp_travel_date_migrate_176' );

		if ( $wptravel_date_migrated && 'yes' === $wptravel_date_migrated ) {
			return;
		}

		$wptravel_post_type = WP_TRAVEL_POST_TYPE;
		$wptravel_post_ids  = $wpdb->get_results( $wpdb->prepare( "SELECT ID from {$wpdb->posts}  where post_type=%s and post_status in( 'publish', 'draft' )", $wptravel_post_type ) );

		if ( is_array( $wptravel_post_ids ) && count( $wptravel_post_ids ) > 0 ) {
			foreach ( $wptravel_post_ids as $wptravel_post_id ) {
				$wptravel_trip_id   = $wptravel_post_id->ID;
				$wptravel_all_dates = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value from {$wpdb->postmeta} where post_id=%d and meta_key in ( 'wp_travel_start_date', 'wp_travel_end_date', 'wp_travel_multiple_trip_dates', 'wp_travel_trip_itinerary_data' )", $wptravel_trip_id ) );
				if ( is_array( $wptravel_all_dates ) && count( $wptravel_all_dates ) > 0 ) {
					foreach ( $wptravel_all_dates as $wptravel_date_data ) {
						if ( 'wp_travel_start_date' !== $wptravel_date_data->meta_key || 'wp_travel_end_date' !== $wptravel_date_data->meta_key ) {

							$wptravel_dates = $wptravel_date_data->meta_value;
							$wptravel_dates = ( $wptravel_dates ) ? maybe_unserialize( $wptravel_dates ) : '';
							if ( is_array( $wptravel_dates ) && count( $wptravel_dates ) > 0 ) {
								// Multiple Dates.
								if ( 'wp_travel_multiple_trip_dates' === $wptravel_date_data->meta_key ) {
									foreach ( $wptravel_dates as $key => $date ) {
										if ( '' !== $date['start_date'] ) {
											$start_date = $date['start_date'];

											$date1 = DateTime::createFromFormat( $wptravel_datepicker_default_format, $start_date );
											// Converting Date format to WP Date format.
											if ( $date1 ) {
												$start_date = $date1->format( $wptravel_date_format );
											}

											$wptravel_dates[ $key ]['start_date'] = $start_date;
										}
										if ( '' !== $date['end_date'] ) {
											$end_date = $date['end_date'];

											$date1 = DateTime::createFromFormat( $wptravel_datepicker_default_format, $end_date );
											// Converting Date format to WP Date format.
											if ( $date1 ) {
												$end_date = $date1->format( $wptravel_date_format );
											}

											$wptravel_dates[ $key ]['end_date'] = $end_date;
										}
									}
								}
								// Trip Itineraries.
								if ( 'wp_travel_trip_itinerary_data' === $wptravel_date_data->meta_key ) {
									foreach ( $wptravel_dates as $key => $date ) {
										if ( '' !== $date['date'] ) {

											$start_date = $date['date'];

											$date1 = DateTime::createFromFormat( $wptravel_datepicker_default_format, $start_date );
											// Converting Date format to WP Date format.
											if ( $date1 ) {
												$start_date = $date1->format( $wptravel_date_format );
											}

											$wptravel_dates[ $key ]['date'] = $start_date;
										}
									}
								}
							}
							$wptravel_dates = maybe_serialize( $wptravel_dates );
							$wpdb->get_results( $wpdb->prepare( "UPDATE {$wpdb->postmeta}  SET meta_value=%s where post_id=%d and meta_key=%s", $wptravel_dates, $wptravel_trip_id, $wptravel_date_data->meta_key ) );

						}
					}
				}
			}
			update_option( 'wp_travel_date_migrate_176', 'yes' );
		}

	}
	wptravel_upgrade_175_176();
}
