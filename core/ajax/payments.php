<?php
class WP_Travel_Ajax_Payments { // @phpcs:ignore
	/**
	 * Initialize Ajax request for Enquiry.
	 *
	 * @since 5.0.0
	 */
	public static function init() {

		// For admin Get  enquiry details
		add_action( 'wp_ajax_wptravel_get_payment_details_html', array( __CLASS__, 'get_payment_details_html' ) );
		add_action( 'wp_ajax_wptravel_get_payment_details_html', array( __CLASS__, 'get_payment_details_html' ) );
	}

	/**
	 * Get Payment Details HTML.
	 *
	 * @since 5.3.1
	 */
	public static function get_payment_details_html() {
		$permission = WP_Travel::verify_nonce();

		if ( ! $permission || is_wp_error( $permission ) ) {
			WP_Travel_Helpers_REST_API::response( $permission );
		}

		$payload = WP_Travel::get_sanitize_request();

		$payment_id = trim( $payload['payment_id'] );

		global $wpdb;

		// $row = $wpdb->get_row( 'SELECT * FROM ' . $wpdb->postmeta . " WHERE meta_key='wp_travel_payment_id' and meta_value='$payment_id'" );
		// $booking_id = $row->post_id;

		$booking_id = get_post_meta( $payment_id, 'wp_travel_payment_booking_id', true );
		?>
		<div class="my-order my-order-details" style="margin:50px">
		
			<div class="view-order">
				<div class="order-list" style="padding:20px">
					<div class="order-wrapper">
						<?php wptravel_view_booking_details_table( $booking_id, true ); ?>
					</div>
					<?php echo WpTravel_Helpers_Payment::render_payment_details( $booking_id ); // @phpcs:ignore ?>
				</div>
			</div>
		</div>
		<?php
	}
}

WP_Travel_Ajax_Payments::init();
