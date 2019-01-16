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

	$booking_id   = sanitize_text_field( wp_unslash( $_GET['detail_id'] ) );
	$details      = wp_travel_booking_data( $booking_id );
	$payment_data = wp_travel_payment_data( $booking_id );

	if ( is_array( $details ) && count( $details ) > 0 ) {
		?>
		<div class="my-order my-order-details">
			<div class="view-order">
				<div class="order-list">
					<div class="order-wrapper">
						<h3><?php esc_html_e( 'Your Bookings Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
						<div class="table-wrp">
							<table>
								<tr>
									<th><?php esc_html_e( 'Title', 'wp-travel' ); ?></th>
									<th><?php esc_html_e( 'Detail', 'wp-travel' ); ?></th>
								</tr>
								<?php
								foreach ( $details as $order_title => $detail ) :
									if ( 'mode' === $order_title ) {
										continue;
									}
									if ( in_array( $order_title, array( 'total_price', 'paid_amount', 'due_amount' ) ) ) {
										$detail = sprintf( '%s %0.2f', wp_travel_get_currency_symbol(), $detail );
									} else {
										$detail = ucfirst( $detail );
									}
									?>
									<tr>
									<td><?php echo esc_html( str_replace( '_', ' ', ucfirst( $order_title ) ) ); ?></td>
									<td><?php echo esc_html( $detail ); ?></td>
									</tr>
								<?php endforeach; ?>
							</table>
						</div>
					</div>
					<?php
					if ( $payment_data && count( $payment_data ) > 0 ) {
						?>
						<h3><?php esc_html_e( 'Payment Details', 'wp-travel' ); ?></h3>
						<table>
							<tr>
								<th><?php esc_html_e( 'Date', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment ID', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Method', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Amount', 'wp-travel' ); ?></th>
							</tr>
							<?php
							foreach ( $payment_data as $payment_args ) {
								if ( isset( $payment_args['data'] ) && ( is_object( $payment_args['data'] ) || is_array( $payment_args['data'] ) ) ) :
									$payment_amount = get_post_meta( $payment_args['payment_id'], 'wp_travel_payment_amount', true );
									?>
									<tr>
										<td><?php echo esc_html( $payment_args['payment_date'] ); ?></td>
										<td><?php echo esc_html( $payment_args['payment_id'] ); ?></td>
										<td><?php echo esc_html( $payment_args['payment_method'] ); ?></td>
										<td>
											<?php
											if ( $payment_amount > 0 ) :
												echo esc_html( sprintf( ' %s %s ', wp_travel_get_currency_symbol(), $payment_amount ) );
											endif;
											?>
										</td>
									</tr>
									<?php
								endif;
							}
							?>
						</table>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
	}
	do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id );
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
								<th><?php esc_html_e( 'Contact Name', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Mode', 'wp-travel' ); ?></th>
								<th class="text-right"><?php esc_html_e( 'Total Price', 'wp-travel' ); ?></th>
								<th class="text-right"><?php esc_html_e( 'Paid', 'wp-travel' ); ?></th>
								<th class="text-right"><?php esc_html_e( 'Detail', 'wp-travel' ); ?></th>
								<?php do_action( 'wp_travel_dashboard_booking_table_title_after_detail' ); ?>
							</tr>
						</thead>
					<tbody>
					<?php
					foreach ( $bookings as $key => $b_id ) :

						$bkd_trip_id    = get_post_meta( $b_id, 'wp_travel_post_id', true );
						$booking_status = get_post_status( $b_id );

						if ( ! $bkd_trip_id ) {
							continue;
						}

						if ( 'publish' !== $booking_status ) {
							continue;
						}

						$payment_info = wp_travel_booking_data( $b_id );

						$booking_status = $payment_info['booking_status'];
						$payment_status = $payment_info['payment_status'];
						$payment_mode   = $payment_info['payment_mode'];
						$total_price    = $payment_info['total_price'];
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

							<td class="c-name" data-title="<?php esc_html_e( 'Contact Name', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php echo esc_html( $fname . ' ' . $lname ); ?>
								</div>
							</td>

							<td class="booking-status" data-title="<?php esc_html_e( 'Booking Status', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php echo esc_html( $booking_status ); ?>
								</div>
							</td>

							<td class="payment-status" data-title="<?php esc_html_e( 'Payment Status', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php echo esc_html( $payment_status ); ?>
								</div>
							</td>

							<td class="payment-mode" data-title="<?php esc_html_e( 'Payment Mode', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php echo esc_html( $payment_mode ); ?>
							<?php do_action( 'wp_travel_dashboard_booking_after_payment_mode', $ordered_data, $payment_info ); ?>
								</div>
							</td>

							<td class="product-subtotal text-right" data-title="<?php esc_html_e( 'Total Price', 'wp-travel' ); ?>">
								<div class="order-list-table">
								<p>
								<strong>
								<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
								<span class="wp-travel-trip-total"> <?php echo esc_html( $total_price ); ?> </span>
								</strong>
								</p>
								</div>
							</td>
							<td class="product-subtotal text-right" data-title="<?php esc_html_e( 'Paid', 'wp-travel' ); ?>">
								<div class="order-list-table">
								<p>
								<strong>
								<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
								<span class="wp-travel-trip-total"> <?php echo esc_html( $paid_amount ); ?> </span>
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
				<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book more?', 'wp-travel' ); ?></a>
			</div>
		  </div>
		<?php else : ?>
		  <div class="no-order">
			  <p>
			<?php esc_html_e( 'You have not booked any trips', 'wp-travel' ); ?>
			<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book one now?', 'wp-travel' ); ?></a>
			  </p>
		  </div>
		<?php endif; ?>
	</div>
	<?php
}
