<?php
/**
 * Cron
 */

if ( ! class_exists( 'WP_Travel_Cron' ) ) {

	/**
	 * Class for the reminder email.
	 */
	class WP_Travel_Cron {

		/**
		 * Initialize class.
		 *
		 * @return void
		 */
		public static function init() {
			add_action( 'cron_schedules', array( __CLASS__, 'add_cron_recurrence_interval' ) );
			self::set_cron_job();
			add_action( 'wp_travel_cron_schedule', array( __CLASS__, 'trigger_cron_job' ) );
			self::clear_cron_job();
		}

		/**
		 * Add the custom cron intervals.
		 *
		 * @param array $schedules
		 * @return void
		 */
		public static function add_cron_recurrence_interval( $schedules ) {
			$schedules['wt_twicedaily'] = array(
				'interval' => 43200, // In secs.
				'display'  => 'Twice Daily',
			);
			return $schedules;
		}

		/**
		 * If cron job is not already scheduled then scheduled one and start job in selected schedules.
		 */
		public static function set_cron_job() {
			$settings             = wp_travel_get_settings();
			$reminder_email_cycle = 'wt_twicedaily';

			if ( ! wp_next_scheduled( 'wp_travel_cron_schedule' ) ) {
				wp_schedule_event( time(), $reminder_email_cycle, 'wp_travel_cron_schedule' );
			}
		}

		/**
		 * Clear cron job on plugin deactivation.
		 *
		 * @return void
		 */
		public static function _clear_cron_job() {
			wp_clear_scheduled_hook( 'wp_travel_cron_schedule' );
		}

		/**
		 * Calls the plugin deactivation hook.
		 *
		 * @return void
		 */
		public static function clear_cron_job() {
			register_deactivation_hook( WP_TRAVEL_PLUGIN_FILE, array( __CLASS__, '_clear_cron_job' ) );
		}

		/**
		 * Triggers the cron job according to the schedule.
		 *
		 * @return void
		 */
		public static function trigger_cron_job() {
			global $wpdb;
			$custom_post_type = WP_TRAVEL_POST_TYPE;
			$query1           = "SELECT ID from {$wpdb->posts}  where post_type='$custom_post_type' and post_status in( 'publish', 'draft' )";
			$post_ids         = $wpdb->get_results( $query1 );

			if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
				foreach ( $post_ids as $custom_post ) {
					$custom_post_id                = $custom_post->ID;
					$wp_travel_multiple_trip_dates = get_post_meta( $custom_post_id, 'wp_travel_multiple_trip_dates', true );
					$trip_dates                    = array();
					if ( is_array( $wp_travel_multiple_trip_dates ) ) : // @since 4.0.4 To prevent Warning.
						foreach ( $wp_travel_multiple_trip_dates as $date_key => $date_value ) {

							if ( isset( $date_value['start_date'] ) && '' !== $date_value['start_date'] ) {
								$start_date   = $date_value['start_date'];
								$trip_dates[] = $start_date;
							}
						}
						$wp_travel_multiple_trip_dates = ( wp_unslash( $wp_travel_multiple_trip_dates ) );
						$trip_dates                    = wp_unslash( array_unique( $trip_dates ) ); // Filter unique date.
						$trip_dates                    = wp_travel_filter_expired_date( $trip_dates );
						usort( $trip_dates, 'wp_travel_date_sort' );
					endif;

					update_post_meta( $custom_post_id, 'trip_dates', $trip_dates );
					if ( is_array( $trip_dates ) && isset( $trip_dates[0] ) ) {
						update_post_meta( $custom_post_id, 'trip_date', $trip_dates[0] ); // Use it in sorting according to trip dates. @since 3.0.5
					}
				}
			}

		}
	}
}
WP_Travel_Cron::init();
