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

	/**
	 * Booking Form.
	 *
	 * @return HTMl Html content.
	 */
	public static function wp_travel_booking_form() {
		global $post;
		ob_start();
		?>
		<style>
			.booking-form form{display: none;}
		</style>
		<div class="booking-form">
			<div class="wp-travel-book-now"><button type="submit" value="submit" class="wp-book-btn default-travel-btn">Book Now</button></div>
			<?php echo wp_travel_get_booking_form(); ?>
			<input type="reset" value="Close" class="wp-travel-booking-reset" >
		</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return apply_filters( 'wp_travel_booking_form_contents', $content );
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

		$client_email = $_POST[ 'wp_travel_email' ];

		$admin_email = get_option( 'admin_email' );
		$title = 'Booking - ' . $trip_code;

		$message = '<p>First name : ' . $_POST['wp_travel_fname'] . '</p>';
		$message .= '<p>Middle name : ' . $_POST['wp_travel_mname'] . '</p>';
		$message .= '<p>Last name : ' . $_POST['wp_travel_lname'] . '</p>';
		$message .= '<p>Country : ' . $_POST['wp_travel_country'] . '</p>';
		$message .= '<p>Address : ' . $_POST['wp_travel_address'] . '</p>';
		$message .= '<p>Phone : ' . $_POST['wp_travel_phone'] . '</p>';
		$message .= '<p>PAX : ' . $_POST['wp_travel_pax'] . '</p>';
		$message .= '<p>Info : ' . $_POST['wp_travel_note'] . '</p>';

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
				'message' => __( 'Your Item Has Been added but the email could not be sent.' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.' ),
			) );
		}
		$post_array = array(
			'post_title' => $title,
			'post_content' => $message,
			'post_status' => 'publish',
			'post_slug'=>uniqid(),
			'post_type' => 'itinerary-booking'
			);
		$orderID = wp_insert_post( $post_array );
		update_post_meta( $orderID, 'order_data', $_POST );

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

			if(!$order_ids){
				$order_ids = [];
			}

			array_push( $order_ids, [ 'order_id'=>$orderID,'count'=>$_POST['wp_travel_pax'], 'date'=>$_POST['wp_travel_date'] ] );

			update_post_meta( $_POST['wp_travel_post_id'], 'order_ids', $order_ids );
		}

	}
}
