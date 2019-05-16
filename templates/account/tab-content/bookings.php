<?php
/**
 * Booking Tab.
 *
 * @package wp-travel/templates/account/tab-content/
 */

$bookings = $args['bookings'];
global $wp;
$detail_link = home_url( $wp->request ) . '#bookings';
$back_link   = $detail_link;

if ( isset( $_GET['detail_id'] ) && '' !== $_GET['detail_id'] ) {
	wp_travel_print_notices();
	// $pricing_name = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
	$booking_id    = sanitize_text_field( wp_unslash( $_GET['detail_id'] ) );
	$details       = wp_travel_booking_data( $booking_id );
	$payment_data  = wp_travel_payment_data( $booking_id );
	$order_details = get_post_meta( $booking_id, 'order_items_data', true ); // Multiple Trips.



	$customer_note = get_post_meta( $booking_id, 'wp_travel_note', true );
	$travel_date   = get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$trip_id       = get_post_meta( $booking_id, 'wp_travel_post_id', true );

	$title = get_the_title( $trip_id );
	$pax   = get_post_meta( $booking_id, 'wp_travel_pax', true );

	// Billing fields.
	$billing_address = get_post_meta( $booking_id, 'wp_travel_address', true );
	$billing_city    = get_post_meta( $booking_id, 'billing_city', true );
	$billing_country = get_post_meta( $booking_id, 'wp_travel_country', true );
	$billing_postal  = get_post_meta( $booking_id, 'billing_postal', true );

	// Travelers info.
	$fname       = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
	$lname       = get_post_meta( $booking_id, 'wp_travel_lname_traveller', true );
	$status_list = wp_travel_get_payment_status();
	if ( is_array( $details ) && count( $details ) > 0 ) {
		?>
		<div class="my-order my-order-details">
			<div class="view-order">
				<div class="order-list">
					<div class="order-wrapper">
						<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
						<?php wp_travel_view_booking_details_table( $booking_id ); ?>
					</div>
					<?php wp_travel_view_payment_details_table( $booking_id ); ?>
				</div>
			</div>
		</div>
		<?php
	}
} else {
	?>
	<div class="my-order">
		<?php if ( ! empty( $bookings ) && is_array( $bookings ) ) : ?>
			<div class="view-order">
				<div class="order-list">
					<div class="order-wrapper">
						<h3><?php esc_html_e( 'Your Bookings', 'wp-travel' ); ?></h3>
						<div class="table-wrp">
						<table class="order-list-table">
							<thead>
								<tr>
									<th><?php esc_html_e( 'Trip', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Total Price', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Paid', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Detail', 'wp-travel' ); ?></th>
									<?php do_action( 'wp_travel_dashboard_booking_table_title_after_detail' ); ?>
								</tr>
							</thead>
						<tbody>
						<?php
						foreach ( $bookings as $key => $b_id ) :
							$bkd_trip_id    = get_post_meta( $b_id, 'wp_travel_post_id', true );
							$booking_status = get_post_status( $b_id );

							if ( ! $bkd_trip_id ) {
								// Quick fix booking list hide from dashboard if booking updated form admin [ meta - wp_travel_post_id is not updated ]
								$order_details = get_post_meta( $b_id, 'order_items_data', true ); // Multiple Trips.
								if ( $order_details && is_array( $order_details ) && count( $order_details ) > 0 ) : // Multiple.
									$travel_date = '';
									foreach ( $order_details as $order_detail ) :
										$bkd_trip_id      = $order_detail['trip_id'];
										break;
									endforeach;
								endif;
							}

							if ( 'publish' !== $booking_status ) {
								continue;
							}

							$payment_info = wp_travel_booking_data( $b_id );

							$booking_status = $payment_info['booking_status'];
							$payment_status = $payment_info['payment_status'];
							$payment_mode   = $payment_info['payment_mode'];
							$total_price    = $payment_info['total'];
							$paid_amount    = $payment_info['paid_amount'];
							$due_amount     = $payment_info['due_amount'];

							$ordered_data = get_post_meta( $b_id, 'order_data', true );

							$fname = isset( $ordered_data['wp_travel_fname_traveller'] ) ? $ordered_data['wp_travel_fname_traveller'] : '';

							if ( '' !== $fname && is_array( $fname ) ) {
								reset( $fname );
								$first_key = key( $fname );

								$fname = isset( $fname[ $first_key ][0] ) ? $fname[ $first_key ][0] : '';
							} else {
								$fname = isset( $ordered_data['wp_travel_fname'] ) ? $ordered_data['wp_travel_fname'] : '';
							}

							$lname = isset( $ordered_data['wp_travel_lname_traveller'] ) ? $ordered_data['wp_travel_lname_traveller'] : '';

							if ( '' !== $lname && is_array( $lname ) ) {
								reset( $lname );
								$first_key = key( $lname );

								$lname = isset( $lname[ $first_key ][0] ) ? $lname[ $first_key ][0] : '';
							} else {
								$lname = isset( $ordered_data['wp_travel_lname'] ) ? $ordered_data['wp_travel_lname'] : '';
							}
							?>
							<tr class="tbody-content">

								<td class="name" data-title="<?php esc_html_e( 'Trip', 'wp-travel' ); ?>">
									<div class="name-title">
									<a href="<?php echo esc_url( get_the_permalink( $bkd_trip_id ) ); ?>"><?php echo esc_html( get_the_title( $bkd_trip_id ) ); ?></a>
									</div>
								</td>
								<td class="booking-status" data-title="<?php esc_html_e( 'Booking Status', 'wp-travel' ); ?>">
									<div class="contact-title">
								<?php echo esc_html( $booking_status ); ?>
									</div>
								</td>

								<td class="payment-status" data-title="<?php esc_html_e( 'Payment Status', 'wp-travel' ); ?>">
									<div class="contact-title">
										<?php
										$status_lists   = wp_travel_get_payment_status();
										$status = $status_lists[ $payment_status ];
										echo esc_html( $status['text'] );
										// echo esc_html( $payment_status ); ?>
									</div>
								</td>

								<td class="product-subtotal" data-title="<?php esc_html_e( 'Total Price', 'wp-travel' ); ?>">
									<div class="order-list-table">
									<p>
									<strong>
									<span class="wp-travel-trip-total"> <?php echo wp_travel_get_formated_price_currency( $total_price ); ?> </span>
									</strong>
									</p>
									</div>
								</td>
								<td class="product-subtotal" data-title="<?php esc_html_e( 'Paid', 'wp-travel' ); ?>">
									<div class="order-list-table">
									<p>
									<strong>
									<span class="wp-travel-trip-total"> <?php echo wp_travel_get_formated_price_currency( $paid_amount ); ?> </span>
									</strong>
									</p>
									</div>
								</td>
								<td class="payment-mode" data-title="<?php esc_html_e( 'Detail', 'wp-travel' ); ?>">
										<div class="contact-title">
										<?php $detail_link = add_query_arg( 'detail_id', $b_id, $detail_link ); ?>
											<a href="<?php echo esc_url( $detail_link ); ?>"><?php esc_html_e( 'Detail', 'wp-travel' ); ?></a>
									</div>
								</td>
								<?php do_action( 'wp_travel_dashboard_booking_table_content_after_detail', $b_id, $ordered_data, $payment_info ); ?>
							</tr>
								<?php
						endforeach;
						?>
						</tbody>
						<tfoot>
						</tfoot>
						</table>
						</div>
					</div>
				</div>
				<div class="book-more">
					<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book more ?', 'wp-travel' ); ?></a>
				</div>
			</div>
		<?php else : ?>
			<div class="no-order">
				<p>
					<?php esc_html_e( 'You have not booked any trips', 'wp-travel' ); ?>
					<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book one now ?', 'wp-travel' ); ?></a>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<?php
}
