<?php
/**
 * Helpers Email.
 *
 * @package WP_Travel
 * @since 4.7.1
 */

/**
 * WpTravel_Helpers_Emails class.
 */
class WpTravel_Helpers_Emails {

	/**
	 * Sitename.
	 *
	 * @var string
	 */
	public $site_name;

	/**
	 * Initialize Email Class.
	 */
	public static function init() {
		self::$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		if ( is_multisite() ) {
			self::$site_name = get_network()->site_name;
		}
	}

	/**
	 * Booking And Payment email tags.
	 *
	 * @param string $type Type of tags.
	 * @since 4.7.1
	 * @return array
	 */
	public static function default_email_tags( $type = 'booking' ) {
		$tags = array();
		switch ( $type ) {
			case 'booking':
				$booking_email_tags = array(
					'{sitename}'               => self::$site_name,
					'{itinerary_link}'         => 'http://example.com/itinerary/trip1',
					'{itinerary_title}'        => 'trip one',
					'{booking_id}'             => '#1234',
					'{booking_edit_link}'      => 'http://example.com/itinerary/trip1',
					'{booking_no_of_pax}'      => '5',
					'{booking_arrival_date}'   => '2021-06-21',
					'{booking_departure_date}' => '',
					'{booking_selected_time}'  => '15:00',
					'{booking_coupon_code}'    => 'COUPON20',

					'{customer_name}'          => 'Jhon doe',
					'{customer_country}'       => 'USA',
					'{customer_address}'       => '1234567890',
					'{customer_phone}'         => '1234567890',
					'{customer_email}'         => 'test@test.com',
					'{customer_note}'          => 'This is trip note',
					'{bank_deposit_table}'     => '',
				);

				$tags = apply_filters( 'wptravel_admin_booking_default_email_tags', $booking_email_tags );
				break;
		}
		return $tags;
	}

	/**
	 * Booking And Payment email tags.
	 *
	 * @param string $type Type of tags.
	 * @param array  $args Required arguments to generate email tags.
	 * @since 4.7.1
	 * @return array
	 */
	public static function get_email_tags( $type = 'booking', $args ) {
		$tags = array();

		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : '';
		switch ( $type ) {
			case 'booking':
				$booking_email_tags = array(
					'{sitename}'               => self::$site_name,
					'{itinerary_link}'         => 'http://example.com/itinerary/trip1',
					'{itinerary_title}'        => 'trip one',
					'{booking_id}'             => '#1234',
					'{booking_edit_link}'      => 'http://example.com/itinerary/trip1',
					'{booking_no_of_pax}'      => '5',
					'{booking_arrival_date}'   => '2021-06-21',
					'{booking_departure_date}' => '',
					'{booking_selected_time}'  => '15:00',
					'{booking_coupon_code}'    => 'COUPON20',

					'{customer_name}'          => 'Jhon doe',
					'{customer_country}'       => 'USA',
					'{customer_address}'       => '1234567890',
					'{customer_phone}'         => '1234567890',
					'{customer_email}'         => 'test@test.com',
					'{customer_note}'          => 'This is trip note',
					'{bank_deposit_table}'     => '',
				);

				$tags = apply_filters( 'wptravel_booking_email_tags', $booking_email_tags );
				break;
		}
		return $tags;
	}
}
