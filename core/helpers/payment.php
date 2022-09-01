<?php
/**
 * Helpers Payment.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Payment class.
 *
 * @since 5.0.0
 */
class WpTravel_Helpers_Payment { // @phpcs:ignore

	/**
	 * Get Payment details while booking trips.
	 *
	 * @param int $booking_id Trip Booking ID.
	 *
	 * @since 5.0.0
	 *
	 * @return array
	 */
	public static function render_payment_details( $booking_id ) {

		if ( ! $booking_id ) {
			return;
		}

		$payment_data = wptravel_payment_data( $booking_id );
		$status_list  = wptravel_get_payment_status();

		ob_start();
		if ( $payment_data && count( $payment_data ) > 0 ) {
			$payment_id   = wptravel_get_payment_id( $booking_id );
			$payment_slip = get_post_meta( $payment_id, 'wp_travel_payment_slip_name', true );
			?>
			<h3><?php esc_html_e( 'Payment Details', 'wp-travel' ); ?></h3>
			<table class="wp-travel-table-content my-order-payment-details"  cellpadding="0" cellspacing="0" height="100%" width="100%" style="text-align: left;">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Date', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Payment ID / Txn ID', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Payment Method', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Payment Amount', 'wp-travel' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $payment_data as $payment_args ) {
						if ( isset( $payment_args['data'] ) && ( is_object( $payment_args['data'] ) || is_array( $payment_args['data'] ) ) ) :
							$payment_amount = get_post_meta( $payment_args['payment_id'], 'wp_travel_payment_amount', true );
							?>
							<tr>
								<td><?php echo esc_html( $payment_args['payment_date'] ); ?></td>
								<td>
									<?php
									echo esc_html( $payment_args['payment_id'] );
									if ( 'bank_deposit' === $payment_args['payment_method'] ) {
										$txn_id = get_post_meta( $payment_args['payment_id'], 'txn_id', true );
										if ( ! empty( $txt_id ) ) {
											echo ' / ' . esc_html( $txt_id );
										}
									}
									?>
								</td>
								<td>
									<?php
									$gateway_lists = wptravel_payment_gateway_lists();

									// use payment method key in case of payment disabled or deactivated.
									$payment_method = isset( $gateway_lists[ $payment_args['payment_method'] ] ) ? $gateway_lists[ $payment_args['payment_method'] ] : $payment_args['payment_method'];

									echo esc_html( $payment_method );

									if ( 'bank_deposit' === $payment_args['payment_method'] ) {
										$payment_id   = $payment_args['payment_id'];
										$payment_slip = get_post_meta( $payment_id, 'wp_travel_payment_slip_name', true );
										if ( ! empty( $payment_slip ) ) {
											$img_url = content_url( WP_TRAVEL_SLIP_UPLOAD_DIR . '/' . $payment_slip );
											?>
											<a href="<?php echo esc_url( $img_url ); ?>" class="wp-travel-payment-receipt"><span class="dashicons dashicons-media-document"></span> <?php esc_html_e( 'View Payment Receipt', 'wp-travel' ); ?></a>
											<?php
										}
									}
									?>
		
								</td>
								<td>
									<?php
									if ( $payment_amount > 0 ) :
                                        echo wptravel_get_formated_price_currency( $payment_amount, false, '', $booking_id ); //@phpcs:ignore
									endif;
									?>
								</td>
							</tr>
							<?php
						endif;
					}
					?>
				</tbody>
			</table>
			<?php
		}
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
