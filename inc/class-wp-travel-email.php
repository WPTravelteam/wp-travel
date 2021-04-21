<?php
/**
 * Handle/Send Booking/Payment Emails
 *
 * @since WP Travel 4.4.2
 * @package WP Travel
 */

/**
 * WP Travel email templates class.
 */
class WP_Travel_Email extends WP_Travel_Emails {

	/**
	 * Settings.
	 */
	public $settings;

	/**
	 * Email ID/s of Admin.
	 */
	public $admin_email;

	public $site_name;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->settings = wptravel_get_settings();
		$this->sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
		if ( is_multisite() ) {
			$this->sitename = get_network()->site_name;
		}
		add_action( 'wptravel_action_after_inventory_update', array( $this, 'send_booking_emails' ) );
	}


	/**
	 * Send Booking emails to client and admin.
	 *
	 * @param array $args Data to send booking email.
	 * @since WP Travel 4.4.2
	 */
	public function send_booking_emails( $args ) {

		$this->admin_email = apply_filters( 'wp_travel_booking_admin_emails', get_option( 'admin_email' ) );

		$customer_email = $args['customer_email'];

		if ( is_array( $customer_email ) ) {
			$first_key      = key( $customer_email );
			$customer_email = isset( $customer_email[ $first_key ] ) && isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';
		}
		$reply_to_email = isset( $this->settings['wp_travel_from_email'] ) ? $this->settings['wp_travel_from_email'] : $this->admin_email;

		$email      = new WP_Travel_Emails();
		$email_tags = $this->get_email_tags( $args ); // Supported email tags.

		$send_email_to_admin = $this->settings['send_booking_email_to_admin']; // Default 'yes'
		if ( 'yes' === $send_email_to_admin ) { // Send mail to admin if booking email is set to yes.
			$email_template = $email->wptravel_get_email_template( 'bookings', 'admin' );

			$email_content  = $email_template['mail_header'];
			$email_content .= $email_template['mail_content'];
			$email_content .= $email_template['mail_footer'];

			// To send HTML mail, the Content-type header must be set.
			$headers = $email->email_headers( $reply_to_email, $customer_email );

			// Email Subject.
			$email_subject = str_replace( array_keys( $email_tags ), $email_tags, $email_template['subject'] ); // Added email tag support from ver 4.1.5.
			// Email Content.
			$email_content = str_replace( array_keys( $email_tags ), $email_tags, $email_content );

			if ( ! wp_mail( $this->admin_email, $email_subject, $email_content, $headers ) ) {
				WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
			}
		}

		// Send mail to client.
		$email_template = $email->wptravel_get_email_template( 'bookings', 'client' );

		$email_content  = $email_template['mail_header'];
		$email_content .= $email_template['mail_content'];
		$email_content .= $email_template['mail_footer'];

		// To send HTML mail, the Content-type header must be set.
		$headers = $email->email_headers( $reply_to_email, $reply_to_email );

		// Email Subject.
		$email_subject = str_replace( array_keys( $email_tags ), $email_tags, $email_template['subject'] ); // Added email tag support from ver 4.1.5.
		// Email Content.
		$email_content = str_replace( array_keys( $email_tags ), $email_tags, $email_content );

		if ( ! wp_mail( $customer_email, $email_subject, $email_content, $headers ) ) {
			WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
		}
	}

	/**
	 * Booking Email Tags.
	 *
	 * @param array $args Email tag args.
	 *
	 * @return array
	 */
	public function get_email_tags( $args ) {

		global $wt_cart;
		$discounts   = $wt_cart->get_discounts();
		$coupon_code = ! empty( $discounts['coupon_code'] ) ? ( $discounts['coupon_code'] ) : '';

		$trip_id        = isset( $args['trip_id'] ) ? $args['trip_id'] : 0;
		$booking_id     = isset( $args['booking_id'] ) ? $args['booking_id'] : 0;
		$price_key      = isset( $args['price_key'] ) ? $args['price_key'] : '';
		$pax            = isset( $args['pax'] ) ? $args['pax'] : '';
		$arrival_date   = isset( $args['arrival_date'] ) ? $args['arrival_date'] : ''; // date along with time.
		$departure_date = isset( $args['departure_date'] ) ? $args['departure_date'] : '';
		$trip_time      = isset( $args['time'] ) ? $args['time'] : '';

		// Customer Details.[nonce already verified before calling this method].
		$first_name       = isset( $_POST['wp_travel_fname_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_fname_traveller'] ) ) : '';
		$last_name        = isset( $_POST['wp_travel_lname_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_lname_traveller'] ) ) : '';
		$customer_country = isset( $_POST['wp_travel_country_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_country_traveller'] ) ) : '';
		$customer_phone   = isset( $_POST['wp_travel_phone_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_phone_traveller'] ) ) : '';
		$customer_email   = isset( $_POST['wp_travel_email_traveller'] ) ? wptravel_sanitize_array( wp_unslash( $_POST['wp_travel_email_traveller'] ) ) : '';

		reset( $first_name );
		$first_key = key( $first_name );

		$first_name = isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ? $first_name[ $first_key ][0] : '';
		$last_name  = isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ? $last_name[ $first_key ][0] : '';

		$customer_name    = $first_name . ' ' . $last_name;
		$customer_country = isset( $customer_country[ $first_key ] ) && isset( $customer_country[ $first_key ][0] ) ? $customer_country[ $first_key ][0] : '';
		$customer_phone   = isset( $customer_phone[ $first_key ] ) && isset( $customer_phone[ $first_key ][0] ) ? $customer_phone[ $first_key ][0] : '';
		$customer_email   = isset( $customer_email[ $first_key ] ) && isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';

		$customer_address = isset( $_POST['wp_travel_address'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_address'] ) ) : '';
		$customer_note    = isset( $_POST['wp_travel_note'] ) ? sanitize_text_field( wp_unslash( $_POST['wp_travel_note'] ) ) : '';

		// Bank Deposite table.
		$bank_deposit_table = '';
		if ( isset( $_POST['wp_travel_payment_gateway'] ) && 'bank_deposit' === $_POST['wp_travel_payment_gateway'] ) {
			$bank_deposit_table = wptravel_get_bank_deposit_account_table( false );
		}

		$email_tags        = array(
			'{sitename}'               => $this->sitename,
			'{itinerary_link}'         => get_permalink( $trip_id ),
			'{itinerary_title}'        => wptravel_get_trip_pricing_name( $trip_id, $price_key ),
			'{booking_id}'             => $booking_id,
			'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
			'{booking_no_of_pax}'      => $pax,
			'{booking_scheduled_date}' => esc_html__( 'N/A', 'wp-travel' ), // always N/A. @todo: Need to remove this in future.
			'{booking_arrival_date}'   => $arrival_date,
			'{booking_departure_date}' => $departure_date,
			'{booking_selected_time}'  => $trip_time,
			'{booking_coupon_code}'    => $coupon_code,
			'{customer_name}'          => $customer_name,
			'{customer_country}'       => $customer_country,
			'{customer_address}'       => $customer_address,
			'{customer_phone}'         => $customer_phone,
			'{customer_email}'         => $customer_email,
			'{customer_note}'          => $customer_note,
			'{bank_deposit_table}'     => $bank_deposit_table,
		);
		return $email_tags = apply_filters( 'wp_travel_admin_booking_email_tags', $email_tags, $booking_id );
	}

}
new WP_Travel_Email();
