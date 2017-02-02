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
		ob_start(); ?>
		<form action="" method="post">
			<?php wp_nonce_field( 'wp_travel_security_action', 'wp_travel_security' ); ?>
			<div>
				<label for="country"><?php esc_html_e( 'Country' ); ?></label>
				<input type="text" id="country" name="country">
			</div>
			<div>
				<label for="name"><?php esc_html_e( 'First Name' ); ?></label>
				<input type="text" id="fname" name="firstname">
			</div>
			<div>
				<label for="Address"><?php esc_html_e( 'Address' ); ?></label>
				<input type="text" id="address" name="address">
			</div>
			<div>
				<label for="Email"><?php esc_html_e( 'Email' ); ?></label>
				<input type="email" id="email" name="email">
			</div>
			<div>
				<label for="Phone"><?php esc_html_e( 'Phone' ); ?></label>
				<input type="text" id="phone" name="phone">
			</div>
			<div>
				<label for="some-text"><?php esc_html_e( 'Some Text' ); ?></label>
				<textarea placeholder="<?php esc_html_e( 'Some text...' ); ?>"></textarea>
			</div>
			<input type="submit" name="wp_travel_book_now" id="wp-travel-book-now" value="Book Now">
		</form>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}

	/** Send Email after clicking Book Now. */
	function wp_traval_book_now() {
		if ( ! isset( $_POST[ 'wp_travel_book_now' ] ) ) {
			return;
		}
		$client_email = 'test@test.com';

		$admin_email = get_option( 'admin_email' );
		$title = 'Book Trip';

		$message = 'test message';

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

	}
}
