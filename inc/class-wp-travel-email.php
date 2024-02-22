<?php
/**
 * Handle/Send Booking/Payment Emails
 *
 * @since 4.4.2
 * @package WP_Travel
 */

if ( ! class_exists( 'WP_Travel_Email' ) ) {
	 /**
	  * WP Travel email templates class.
	  */
	class WP_Travel_Email extends WP_Travel_Emails {
 // @phpcs:ignore

		/**
		 * Settings.
		 *
		 * @var $settings WP Travel Settings.
		 */
		public $settings;

		/**
		 * Email ID/s of Admin.
		 *
		 * @var $admin_email WPtravel admin email.
		 */
		public $admin_email;

		/**
		 * Website Name.
		 *
		 * @var $sitename Name of website.
		 */
		public $sitename;

		/**
		 * Constructor.
		 */
		public function __construct() {

			$this->settings = wptravel_get_settings();
			$this->sitename = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			if ( is_multisite() ) {
				$this->sitename = get_network()->site_name;
			}
			/**
			 * Action hook run after Inventory data update.
			 *
			 * @deprecated 4.7.1
			 */
			add_action( 'wptravel_action_after_inventory_update', array( $this, 'send_booking_emails' ) );

			/**
			 * Action hook run after Inventory data update.
			 *
			 * @since 5.0.0
			 */
			add_action( 'wptravel_action_send_booking_email', array( $this, 'send_booking_email' ), 10, 3 );
			
		}

		/**
		 * Send Booking emails to client and admin. This Method is deprecated @since 4.7.1. Need to remove in future.
		 *
		 * @param array $args Data to send booking email.
		 *
		 * @since 4.4.2
		 * @deprecated 4.7.1
		 */
		public function send_booking_emails( $args ) {
			$this->admin_email = apply_filters( 'wp_travel_booking_admin_emails', get_option( 'admin_email' ) ); // @phpcs:ignore

			$customer_email = $args['customer_email'];

			if ( is_array( $customer_email ) ) {
				$first_key      = key( $customer_email );
				$customer_email = isset( $customer_email[ $first_key ] ) && isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';
			}
			$reply_to_email = isset( $this->settings['wp_travel_from_email'] ) ? $this->settings['wp_travel_from_email'] : $this->admin_email;

			$email      = new WP_Travel_Emails();
			$email_tags = $this->get_email_tags( $args ); // Supported email tags.

			$send_email_to_admin = $this->settings['send_booking_email_to_admin']; // 'yes' By default.
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

				if ( ! wp_mail( $this->admin_email, $email_subject, $email_content, $headers, array( $attachment ) ) ) {
					WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
				}
			}
			$send_mail = apply_filters( 'wptravel_send_booking_email_to_client', true );
			if ( true === $send_mail ) {
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

				if ( ! wp_mail( $customer_email, $email_subject, $email_content, $headers, array( $attachment ) ) ) {
					WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
				}
			}
		}

		/**
		 * Send Booking emails to client and admin.
		 *
		 * @param int   $booking_id Booking id.
		 * @param array $request_data POST request data.
		 * @since 5.0.0
		 */
		public function send_booking_email( $booking_id, $request_data, $new_trip_id ) {
			
			$this->admin_email = apply_filters( 'wp_travel_booking_admin_emails', get_option( 'admin_email' ) ); // @phpcs:ignore

			$customer_email_ids = isset( $request_data['wp_travel_email_traveller'] ) ? $request_data['wp_travel_email_traveller'] : array();

			$settings = wptravel_get_settings();
			if( class_exists( 'WooCommerce' ) && $settings['enable_woo_checkout'] == 'yes' ){
				if( !isset( $_REQUEST['key']) ){
					return;
				}

				$order_data = wc_get_order(wc_get_order_id_by_order_key($_REQUEST['key']))->data;

				if( !empty( $order_data ) ){ 

					$customer_email_ids = array(
						strtotime("now") => array(
							'0' =>	$order_data['billing']['email'] 
						)
					);
				}
				
			}
			// var_dump( $customer_email_ids );
			if ( empty( $customer_email_ids ) ) {
				return;
			}

			$all_customer_email_ids = array(); // include emails of all trips added in cart. This will helps you to send emails to all travellers.
			foreach ( $customer_email_ids as $cart_item_id => $email_ids ) {
				foreach ( $email_ids as $email_id ) {
					$all_customer_email_ids[] = $email_id;
				}
			}

			$customer_email = $all_customer_email_ids[0];

			$reply_to_email = isset( $this->settings['wp_travel_from_email'] ) ? $this->settings['wp_travel_from_email'] : $this->admin_email;

			$email      = new WP_Travel_Emails();
			$email_tags = $this->get_tags( $booking_id, $request_data ); // Supported email tags.
			/**
			 * @since 6.5.0
			 * Send itinerary pdf in booking email.
			 * @required Download_Core
			 * 
			 */
			$attachments = apply_filters( 'wp_travel_email_itinerary_pdf_attachment', false );
			$attachment = array();
			$wt_settings_send_pdf = get_option( 'itinerary_pdf_send_booking_mail' );
			if ( $wt_settings_send_pdf ||  $attachments ) {
				foreach ( $new_trip_id as $indexs => $id ) {
					if ( class_exists( 'WP_Travel_Downloads_Core' ) ) {
						WP_Travel_Downloads_Core::email_attachment_generate_pdf( $id, false );
						$dir                   = trailingslashit( WP_TRAVEL_ITINERARY_PATH );
						$trips_name            = get_the_title( $id );
						$downloadable_filename = $trips_name . '.pdf';
						$attachment[]            = $dir . $downloadable_filename;
					}
				}

			} else {
				foreach ( $new_trip_id as $indexs => $id ) {
					$wt_trip_email_itineray_pdf = get_post_meta( $id, 'send_booking_maile_attached_itinerary_pdf', true );
					if ( $wt_trip_email_itineray_pdf ) {
						if ( class_exists( 'WP_Travel_Downloads_Core' ) ) {
							WP_Travel_Downloads_Core::email_attachment_generate_pdf( $id, false );
							$dir                   = trailingslashit( WP_TRAVEL_ITINERARY_PATH );
							$trips_name            = get_the_title( $id );
							$downloadable_filename = $trips_name . '.pdf';
							$attachment[]            = $dir . $downloadable_filename;
						}
					}
				}
			}

			if( $settings['enable_woo_checkout'] == 'no' ){
				$send_email_to_admin = $this->settings['send_booking_email_to_admin']; // 'yes' By default.
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
					$email_content      = str_replace( array_keys( $email_tags ), $email_tags, $email_content );
					$amdin_send_booking = apply_filters( 'wp_travel_booking_mail_sent_to_admin', true );
					if ( $amdin_send_booking == true ) {
						if ( ! wp_mail( $this->admin_email, $email_subject, $email_content, $headers, $attachment ) ) {
							WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
						}
					}
				}

				/**
				 * Hooks to enable/disable booking email to client.
				 *
				 * @since 5.3.1
				 */
				$send_mail = apply_filters( 'wptravel_send_booking_email_to_client', true );
				if ( true === $send_mail ) {
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

					if ( ! wp_mail( $customer_email, $email_subject, $email_content, $headers, $attachment ) ) {
						WPTravel()->notices->add( __( 'Your trip has been booked but the email could not be sent. Possible reason: your host may have disabled the mail() function.', 'wp-travel' ), 'error' );
					}
				}
			}
		}

		/**
		 * Booking Email Tags. Deprecated in 4.7.1 use WP_Travel_Email::get_tags() instead.
		 *
		 * @param array $args Email tag args.
		 *
		 * @deprecated 4.7.1
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
			$requests         = WP_Travel::get_sanitize_request( 'request' );
			$first_name       = isset( $requests['wp_travel_fname_traveller'] ) ? $requests['wp_travel_fname_traveller'] : '';
			$last_name        = isset( $requests['wp_travel_lname_traveller'] ) ? $requests['wp_travel_lname_traveller'] : '';
			$customer_country = isset( $requests['wp_travel_country_traveller'] ) ? $requests['wp_travel_country_traveller'] : '';
			$customer_phone   = isset( $requests['wp_travel_phone_traveller'] ) ? $requests['wp_travel_phone_traveller'] : '';
			$customer_email   = isset( $requests['wp_travel_email_traveller'] ) ? $requests['wp_travel_email_traveller'] : '';

			reset( $first_name );
			$first_key = key( $first_name );

			$first_name = isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ? $first_name[ $first_key ][0] : '';
			$last_name  = isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ? $last_name[ $first_key ][0] : '';

			$customer_gender   = isset( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'] ) ? get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'][array_key_first( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'])][0] : '';

			if( apply_filters( 'wptravel_traveller_salutation', true ) ==  true ){
				if( $customer_gender == 'male' ){
					$salutation = __( 'Mr ', 'wp-travel' );
				}elseif( $customer_gender == 'female' ){
					$salutation = __( 'Miss ', 'wp-travel' );
				}else{
					$salutation = '';
				}
			}else{
				$salutation = '';
			}
			

			$customer_name    = $salutation.$first_name . ' ' . $last_name;
			$customer_country = isset( $customer_country[ $first_key ] ) && isset( $customer_country[ $first_key ][0] ) ? $customer_country[ $first_key ][0] : '';
			$customer_phone   = isset( $customer_phone[ $first_key ] ) && isset( $customer_phone[ $first_key ][0] ) ? $customer_phone[ $first_key ][0] : '';
			$customer_email   = isset( $customer_email[ $first_key ] ) && isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';

			$customer_address = isset( $requests['wp_travel_address'] ) ? $requests['wp_travel_address'] : '';
			$customer_note    = isset( $requests['wp_travel_note'] ) ? $requests['wp_travel_note'] : '';

			// Bank Deposite table.
			$bank_deposit_table = '';
			if ( isset( $requests['wp_travel_payment_gateway'] ) && 'bank_deposit' === $requests['wp_travel_payment_gateway'] ) {
				$bank_deposit_table = wptravel_get_bank_deposit_account_table( false );
			}

			$email_tags = array(
				'{sitename}'               => $this->sitename,
				'{trip_id}'                => $trip_id,
				'{itinerary_link}'         => get_permalink( $trip_id ),
				'{itinerary_title}'        => wptravel_get_trip_pricing_name( $trip_id, $price_key ),
				'{booking_id}'             => $booking_id,
				'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
				'{booking_no_of_pax}'      => $pax,
				'{booking_scheduled_date}' => esc_html__( 'N/A', 'wp-travel' ), // always N/A. Need to remove this in future.
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
			$email_tags = apply_filters( 'wp_travel_admin_booking_email_tags', $email_tags, $booking_id ); // @phpcs:ignore
			return $email_tags;
		}

		/**
		 * Booking Email Tags.
		 *
		 * @param int   $booking_id Booking Id.
		 * @param array $request_data All POST Request data.
		 *
		 * @since 5.0.0
		 * @return array
		 */
		public function get_tags( $booking_id = 0, $request_data = array() ) {
			$settings = wptravel_get_settings();
			global $wt_cart;
			$discounts   = $wt_cart->get_discounts();
			$coupon_code = ! empty( $discounts['coupon_code'] ) ? ( $discounts['coupon_code'] ) : '';
			$reply_to_email = isset( $this->settings['wp_travel_from_email'] ) ? $this->settings['wp_travel_from_email'] : $this->admin_email;
			$items = $wt_cart->getItems();

			// Cart Datas.
			$trip_ids   = array();
			$price_keys = array();
			$paxs       = array();

			// Tags.
			$itinerary_links  = array();
			$itinerary_titles = array();
			$arrival_dates    = array(); // date along with time.
			$departure_dates  = array();
			$trip_times       = array();
			$total_pax = 0;
			if ( is_array( $items ) && 0 < count( $items ) ) {
				foreach ( $items as $key => $item ) {
					$trip_id        = isset( $item['trip_id'] ) ? $item['trip_id'] : 0;
					$price_key      = isset( $item['price_key'] ) ? $item['price_key'] : '';
					$arrival_date   = isset( $item['arrival_date'] ) ? $item['arrival_date'] : '';  // date along with time.
					$departure_date = isset( $item['departure_date'] ) ? $item['departure_date'] : '';
					$time           = isset( $item['trip_time'] ) ? $item['trip_time'] : '';

					$trip_ids[]   = $trip_id;
					$price_keys[] = $price_key;
					$paxs[]       = isset( $item['pax'] ) ? $item['pax'] : '';

					// Tags values.
					$itinerary_links[]  = get_permalink( $trip_id );
					$itinerary_titles[] = wptravel_get_trip_pricing_name( $trip_id, $price_key );
					$arrival_dates[]    = $arrival_date;
					$departure_dates[]  = $departure_date;
					$trip_times[]       = $time;

					// Total data.
					$total_pax += $item['pax'];
				}
			}

			$first_name       = isset( $request_data['wp_travel_fname_traveller'] ) ? $request_data['wp_travel_fname_traveller'] : array();
			$last_name        = isset( $request_data['wp_travel_lname_traveller'] ) ? $request_data['wp_travel_lname_traveller'] : array();
			$customer_country = isset( $request_data['wp_travel_country_traveller'] ) ? $request_data['wp_travel_country_traveller'] : array();
			$customer_phone   = isset( $request_data['wp_travel_phone_traveller'] ) ? $request_data['wp_travel_phone_traveller'] : array();
			$customer_email   = isset( $request_data['wp_travel_email_traveller'] ) ? $request_data['wp_travel_email_traveller'] : array();
			

			if( apply_filters( 'wptravel_traveller_salutation', true ) ==  true ){
				$customer_gender   = isset( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'] ) ? get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'][array_key_first( get_post_meta( $booking_id, 'order_data', true )['wp_travel_gender_traveller'])][0] : '';
				
				if( $customer_gender == 'male' ){
					$salutation = __( 'Mr ', 'wp-travel' );
				}elseif( $customer_gender == 'female' ){
					$salutation = __( 'Miss ', 'wp-travel' );
				}else{
					$salutation = '';
				}
			}else{
				$salutation = '';
			}
			

			reset( $first_name );
			$first_key = key( $first_name );

			$first_name = isset( $first_name[ $first_key ] ) && isset( $first_name[ $first_key ][0] ) ? $first_name[ $first_key ][0] : '';
			$last_name  = isset( $last_name[ $first_key ] ) && isset( $last_name[ $first_key ][0] ) ? $last_name[ $first_key ][0] : '';

			$customer_name    = $salutation . $first_name . ' ' . $last_name;
			$customer_country = isset( $customer_country[ $first_key ] ) && isset( $customer_country[ $first_key ][0] ) ? $customer_country[ $first_key ][0] : '';
			$customer_phone   = isset( $customer_phone[ $first_key ] ) && isset( $customer_phone[ $first_key ][0] ) ? $customer_phone[ $first_key ][0] : '';
			$customer_email   = isset( $customer_email[ $first_key ] ) && isset( $customer_email[ $first_key ][0] ) ? $customer_email[ $first_key ][0] : '';

			$customer_address = isset( $request_data['wp_travel_address'] ) ? sanitize_text_field( wp_unslash( $request_data['wp_travel_address'] ) ) : '';
			$customer_note    = isset( $request_data['wp_travel_note'] ) ? sanitize_text_field( wp_unslash( $request_data['wp_travel_note'] ) ) : '';

			// Bank Deposite table.
			$bank_deposit_table = '';
			if ( isset( $request_data['wp_travel_payment_gateway'] ) && 'bank_deposit' === $request_data['wp_travel_payment_gateway'] ) {
				$bank_deposit_table = wptravel_get_bank_deposit_account_table( false );
			}
			$trip_time_get = '';
			if ( is_array( $trip_times ) && count( $trip_times ) > 0 ) {
				foreach ( $trip_times as $keys => $time_values ) {
					$trip_time_get .= ' ' . $time_values;
				} 
			} 
			$itineraries = get_post_meta( $trip_id, 'wp_travel_trip_itinerary_data', true );


			if( class_exists( 'WooCommerce' ) && $settings['enable_woo_checkout'] == 'yes' ){
				
				if( !isset( $_REQUEST['key']) ){
					return;
				}

				$order_data = wc_get_order(wc_get_order_id_by_order_key($_REQUEST['key']))->data;

				if( !empty( $order_data ) ){ 
					$customer_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];
					$customer_country = $order_data['billing']['country'];
					$customer_address = $order_data['billing']['address_1'];
					$customer_phone = $order_data['billing']['phone'];
					$customer_email = $order_data['billing']['email'];
				}
				
			}
			$email_tags  = array(
				'{sitename}'               => $this->sitename,
				'{trip_id}'                => $trip_id,
				// '{itineraries}'            => $this->$itineraries,
				'{itinerary_link}'         => get_permalink( $trip_id ), // @deprecated.
				'{itinerary_title}'        => wptravel_get_trip_pricing_name( $trip_id, $price_key ), // @deprecated.
				'{booking_arrival_date}'   => $arrival_date, // @deprecated.
				'{booking_departure_date}' => $departure_date,  // @deprecated.
				'{booking_selected_time}'  => apply_filters( 'wp_travel_booking_email_trip_time', $trip_time_get, $items, $trip_times ),  // @deprecated.
				'{booking_scheduled_date}' => esc_html__( 'N/A', 'wp-travel' ), // @deprecated.
				'{customer_name}'          => $customer_name,
				'{customer_country}'       => $customer_country,
				'{customer_address}'       => $customer_address,
				'{customer_phone}'         => $customer_phone,
				'{customer_email}'         => $customer_email,

				'{booking_id}'             => $booking_id,
				'{booking_no_of_pax}'      => $total_pax,
				'{booking_edit_link}'      => get_edit_post_link( $booking_id ),
				'{booking_coupon_code}'    => $coupon_code,
				'{customer_note}'          => $customer_note,
				'{bank_deposit_table}'     => $bank_deposit_table,
				'{booking_details}'        => WpTravel_Helpers_Booking::render_booking_details( $booking_id ),
				'{traveler_details}'       => WpTravel_Helpers_Booking::render_traveler_details( $booking_id ),
				'{payment_details}'        => WpTravel_Helpers_Payment::render_payment_details( $booking_id ),

			);

			$email_tags = apply_filters( 'wp_travel_admin_booking_email_tags', $email_tags, $booking_id ); // @phpcs:ignore

			$email_data = array(
				'from' => $reply_to_email,
				'to'   => $customer_email,
			);
			
			if( $request_data['wp_travel_booking_option'] == 'booking_only' || ( class_exists( 'WooCommerce' ) && $settings['enable_woo_checkout'] == 'yes' ) ){
				$email          = new WP_Travel_Emails();
				$headers = $email->email_headers( $reply_to_email, $reply_to_email );
				// $email_data = array(
				// 	'from' => $reply_to_email,
				// 	'to'   => $customer_email,
				// );

				do_action( 'wp_travel_after_payment_email_sent', $booking_id, $email_data, $email_tags ); // @since 3.0.6 for invoice.
			}
			
			
			return $email_tags;

		}
	}

}
new WP_Travel_Email();
