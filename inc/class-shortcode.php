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
	function wp_travel_booking_form() {
		global $post;
		ob_start();
		?>
		
		<div class="booking-form">
			<div class="wp-travel-book-now"><button>Book Now</button></div>
			<form action="" method="post" style="display: none">
				<input type="hidden" name="trip_id" value="<?php echo $post->ID; ?>">
				<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>

				<div class="col-sm-4">
					<label for="wp-trevel-fname"><?php esc_html_e( 'First Name' ); ?></label>
					<input type="text" id="wp-trevel-fname" name="wp_travel_fname">
				</div>
				<div class="col-sm-4">
					<label for="wp-trevel-mname"><?php esc_html_e( 'Middle Name' ); ?></label>
					<input type="text" id="wp-trevel-mname" name="wp_travel_mname">
				</div>
				<div class="col-sm-4">
					<label for="wp-trevel-lname"><?php esc_html_e( 'Last Name' ); ?></label>
					<input type="text" id="wp-trevel-lname" name="wp_travel_lname">
				</div>

				<div class="col-sm-12">
					<label for="wp-trevel-country"><?php esc_html_e( 'Country' ); ?></label>
					<?php $countries = wp_travel_get_countries(); ?>
					<?php if ( count( $countries ) > 0 ) : ?>
					<select id="wp-trevel-country" name="wp_travel_country">
						<?php foreach ( $countries as $short_name => $name ) : ?>
							<option value="<?php esc_html_e( $short_name, 'wp-travel' ) ?>"><?php esc_html_e( $name, 'wp-travel' ) ?></option>
						<?php endforeach; ?>			      
				    </select>
				    <?php endif; ?>
				</div>
				<div class="col-sm-6">
					<label for="wp-travel-address"><?php esc_html_e( 'Address' ); ?></label>
					<input type="text" id="wp-travel-address" name="wp_travel_address">
				</div>
				<div class="col-sm-6">
					<label for="wp-travel-phone"><?php esc_html_e( 'Phone' ); ?></label>
					<input type="number" id="wp-travel-phone" name="wp_travel_phone">
				</div>
				<div class="col-sm-6">
					<label for="wp-travel-email"><?php esc_html_e( 'Email' ); ?></label>
					<input type="email" id="wp-travel-email" name="wp_travel_email">
				</div>
				<div class="col-sm-6">
					<label for="wp-travel-pax"><?php esc_html_e( 'No of PAX' ); ?></label>
					<input type="number" id="wp-travel-pax" name="wp_travel_pax">
				</div>
				<div class="col-sm-12">
					<label for="wp-travel-note"><?php esc_html_e( 'Note' ); ?></label>
					<textarea name="wp_travel_note" id="wp-travel-note" placeholder="<?php esc_html_e( 'Some text...' ); ?>" rows="6" cols="150"></textarea>
				</div>
				<div class="col-sm-12">
					<input type="hidden" name="wp_travel_post_id" value="<?php echo $post->ID; ?>" >
					<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book Now">
				</div>
			</form>
		</div>
		
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
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
				'message' => __( 'Your Item Has Beed added but the email could not be sent.' ) . "<br />\n" . __( 'Possible reason: your host may have disabled the mail() function.' ),
			) );
		}
		$post_array = array(
			'post_title' => $title,
			'post_content' => $message,
			'post_status' => 'draft',
			'post_type' => 'itinerary-booking'
			);
		wp_insert_post( $post_array );

	}
}
