<?php
/**
 * Shortcode callbacks.
 *
 * @package wp-travel\inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP travel Shortcode class.
 *
 * @class WP_Pattern
 * @version	1.0.0
 */
class Wp_Travel_Shortcodes {

	public function init() {
	    add_shortcode( 'WP_TRAVEL_ITINERARIES', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
	    add_shortcode( 'wp_travel_itineraries', array( $this, 'wp_travel_get_itineraries_shortcode' ) );
 	}

	/**
	 * Booking Form.
	 *
	 * @return HTMl Html content.
	 */
	public static function wp_travel_get_itineraries_shortcode(  $atts, $content = '' ) {

		$type = isset( $atts['type'] ) ? $atts['type'] : '';

		$iti_id = isset( $atts['itinerary_id'] ) ? absint($atts['itinerary_id']) : '';

		$id   = isset( $atts['id'] ) ? $atts['id'] : 0;
		$id   = absint( $id );
		$slug = isset( $atts['slug'] ) ? $atts['slug'] : '';
		$limit = isset( $atts['limit'] ) ? $atts['limit'] : 20;
		$limit = absint( $limit );


		$args = array(
			'post_type' 		=> 'itineraries',
			'posts_per_page' 	=> $limit,
			'status'       => 'published',
		);

	if( ! empty( $iti_id ) ) :

		$args['p'] 	= $iti_id;
		
	else :

		$taxonomies = array( 'itinerary_types', 'travel_locations' );
		// if type is taxonomy.
		if ( in_array( $type, $taxonomies ) ) {

			if (  $id > 0 ) {
				$args['tax_query']	 = array(
										array(
											'taxonomy' => $type,
											'field'    => 'term_id',
											'terms'    => $id,
											),
										);
			} elseif ( '' !== $slug ) {
				$args['tax_query']	 = array(
										array(
											'taxonomy' => $type,
											'field'    => 'slug',
											'terms'    => $slug,
											),
										);
			}
		} elseif ( 'featured' === $type ) {
			$args['meta_key']   = 'wp_travel_featured';
			$args['meta_query'] = array(
								array(
									'key'     => 'wp_travel_featured',
									'value'   => 'yes',
									// 'compare' => 'IN',
								),
							);
		}

	endif;
		
		$query = new WP_Query( $args ); ?>
		<div class="wp-travel-itinerary-items">
			<?php $col_per_row = apply_filters( 'wp_travel_itineraries_col_per_row' , '3' ); ?>
			<?php if ( $query->have_posts() ) : ?>
				<ul class="wp-travel-itinerary-list col-<?php esc_attr_e( $col_per_row, 'wp-travel' ) ?>-per-row">
				<?php while( $query->have_posts() ) : $query->the_post(); ?>
					<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item' ); ?>
				<?php endwhile; ?>
				</ul>
			<?php else : ?>
				<?php wp_travel_get_template_part( 'shortcode/itinerary', 'item-none' ); ?>
			<?php endif; ?>
		</div>
		<?php wp_reset_query();
	}

	/** Send Email after clicking Book Now. */
	public static function wp_traval_book_now() {

		if ( ! isset( $_POST[ 'wp_travel_book_now' ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['wp_travel_security'],  'wp_travel_security_action' ) ) {
			return;
		}
		if ( ! isset( $_POST['wp_travel_post_id'] ) ) {
			return;
		}

		$trip_code = wp_traval_get_trip_code( $_POST['wp_travel_post_id'] );
		$title = 'Booking - ' . $trip_code;

		$post_array = array(
			'post_title' => $title,
			'post_content' => '',
			'post_status' => 'publish',
			'post_slug' => uniqid(),
			'post_type' => 'itinerary-booking',
			);
		$orderID = wp_insert_post( $post_array );
		update_post_meta( $orderID, 'order_data', $_POST );

		$trip_id = sanitize_text_field( $_POST['wp_travel_post_id'] );
		$booking_count = get_post_meta( $trip_id, 'wp_travel_booking_count', true );
		$booking_count = ( isset( $booking_count ) && '' != $booking_count ) ? $booking_count : 0;
		$new_booking_count = $booking_count + 1;
		update_post_meta( $trip_id, 'wp_travel_booking_count', sanitize_text_field( $new_booking_count ) );

		$post_ignore = array( '_wp_http_referer', 'wp_travel_security', 'wp_travel_book_now' );
		foreach ( $_POST as $meta_name => $meta_val ) {
			if ( in_array( $meta_name , $post_ignore ) ) {
				continue;
			}
			update_post_meta( $orderID, $meta_name, sanitize_text_field( $meta_val ) );
		}

		if ( array_key_exists( 'wp_travel_date', $_POST ) ) {

			$pax_count_based_by_date = get_post_meta( $_POST['wp_travel_post_id'], 'total_pax_booked', true );

			if( ! array_key_exists( $_POST['wp_travel_date'], $pax_count_based_by_date ) ) {
				$pax_count_based_by_date[ $_POST['wp_travel_date'] ] = 'default';
			}

			$pax_count_based_by_date[$_POST['wp_travel_date']] += $_POST['wp_travel_pax'];

			update_post_meta($_POST['wp_travel_post_id'],'total_pax_booked', $pax_count_based_by_date);

			$order_ids = get_post_meta($_POST['wp_travel_post_id'],'order_ids',true);

			if ( ! $order_ids ) {
				$order_ids = [];
			}

			array_push( $order_ids, [ 'order_id'=>$orderID,'count'=>$_POST['wp_travel_pax'], 'date'=>$_POST['wp_travel_date'] ] );

			update_post_meta( $_POST['wp_travel_post_id'], 'order_ids', $order_ids );
		}

		$settings = wp_traval_get_settings();

		$send_booking_email_to_admin = ( isset( $settings['send_booking_email_to_admin'] ) && '' !== $settings['send_booking_email_to_admin'] ) ? $settings['send_booking_email_to_admin'] : 'yes';


		// Prepare variables to assign in email.
		$client_email = $_POST['wp_travel_email'];

		$admin_email = get_option( 'admin_email' );

		// Email Variables.
		if ( is_multisite() ) {
			$sitename = get_network()->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$sitename = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		}
		$booking_id 		  	= $orderID;
		$itinerary_id 			= sanitize_text_field( $_POST['wp_travel_post_id'] );
		$itinerary_title 		= get_the_title( $itinerary_id );

		$booking_no_of_pax 		= 'N/A';
		$booking_scheduled_date = 'N/A';
		$booking_arrival_date 	= $_POST['wp_travel_arrival_date'];
		$booking_departure_date = $_POST['wp_travel_departure_date'];

		$customer_name 		  	= $_POST['wp_travel_fname'] . ' ' . $_POST['wp_travel_lname'];
		$customer_country 		= $_POST['wp_travel_country'];
		$customer_address 		= $_POST['wp_travel_address'];
		$customer_phone 		= $_POST['wp_travel_phone'];
		$customer_email 		= $_POST['wp_travel_email'];
		$customer_note 			= $_POST['wp_travel_note'];

		$email_tags = array(
			'{sitename}'				=> $sitename,
			'{itinerary_link}'			=> get_permalink( $itinerary_id ),
			'{itinerary_title}'			=> $itinerary_title,
			'{booking_id}'				=> $booking_id,
			'{booking_edit_link}'		=> get_edit_post_link( $booking_id ),
			'{booking_no_of_pax}'		=> $booking_no_of_pax,
			'{booking_scheduled_date}'	=> $booking_scheduled_date,
			'{booking_arrival_date}'	=> $booking_arrival_date,
			'{booking_departure_date}'	=> $booking_departure_date,

			'{customer_name}'			=> $customer_name,
			'{customer_country}'		=> $customer_country,
			'{customer_address}'		=> $customer_address,
			'{customer_phone}'			=> $customer_phone,
			'{customer_email}'			=> $customer_email,
			'{customer_note}'			=> $customer_note,
		);
		apply_filters( 'wp_travel_admin_email_tags', $email_tags );

		// Send mail to admin if booking email is set to yes.
		if ( 'yes' == $send_booking_email_to_admin ) {

			$message = wp_travel_admin_email_template();
			$message = str_replace( array_keys( $email_tags ), $email_tags, $message );

			// To send HTML mail, the Content-type header must be set.
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			// Create email headers.
			$headers .= 'From: ' . $client_email . "\r\n" .
			'Reply-To: ' . $client_email . "\r\n" .
			'X-Mailer: PHP/' . phpversion();

			if ( ! wp_mail( $admin_email, wp_specialchars_decode( $title ), $message, $headers ) ) {
				wp_send_json( array(
					'result'  => 0,
					'message' => __( 'Your Item Has Been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' ),
				) );
			}
		}

		// Send email to client.
		$message = wp_travel_customer_email_template();
		$message = str_replace( array_keys( $email_tags ), $email_tags, $message );

		// To send HTML mail, the Content-type header must be set.
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

		// Create email headers.
		$headers .= 'From: ' . $admin_email . "\r\n" .
		'Reply-To: ' . $admin_email . "\r\n" .
		'X-Mailer: PHP/' . phpversion();

		if ( ! wp_mail( $client_email, wp_specialchars_decode( $title ), $message, $headers ) ) {
			wp_send_json( array(
				'result'  => 0,
				'message' => __( 'Your Item Has Been added but the email could not be sent.', 'wp-travel' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.', 'wp-travel' ),
			) );
		}
	}
}
