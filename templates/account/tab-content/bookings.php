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


	$customer_note = get_post_meta( $booking_id, 'wp_travel_note', true );
	$travel_date   = get_post_meta( $booking_id, 'wp_travel_arrival_date', true );
	$trip_id       = get_post_meta( $booking_id, 'wp_travel_post_id', true );
	
	$title         = get_the_title( $trip_id );
	$pax           = get_post_meta( $booking_id, 'wp_travel_pax', true );

	// Billing fields.
	$billing_address = get_post_meta( $booking_id, 'wp_travel_address', true );
	$billing_city    = get_post_meta( $booking_id, 'billing_city', true );
	$billing_country = get_post_meta( $booking_id, 'wp_travel_country', true );
	$billing_postal  = get_post_meta( $booking_id, 'billing_postal', true );

	// Travelers info.
	$fname = get_post_meta( $booking_id, 'wp_travel_fname_traveller', true );
	$lname = get_post_meta( $booking_id, 'wp_travel_lname_traveller', true );

	if ( is_array( $details ) && count( $details ) > 0 ) {
		?>
		<div class="my-order my-order-details">
			<div class="view-order">
				<div class="order-list">
					<div class="order-wrapper">
						<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
						<div class="table-wrp">
							<!-- Started Here -->
							<div class="my-order-single-content-wrap">
								<div class="my-order-single-sidebar">
									<h3 class="my-order-single-title"><?php esc_html_e( 'Payment Status' ); ?></h3>
									<div class="my-order-status my-order-status-<?php echo esc_html( $details['payment_status'] ); ?>"><?php echo esc_html( ucfirst( $details['payment_status'] ) ); ?></div>
	
									<?php do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id ); ?>
									
									<!-- <h3 class="my-order-single-sub-title">Payment Gateway</h3>
									<form action="" class="my-order-payment-gateway">
										<div class="my-order-single-field">
											<input type="radio" name="my-order-payment-gateway" value="Standard Paypal" id="my-order-standard-paypal">
											<label for="my-order-standard-paypal">Standard Paypal</label>
										</div>
										<div class="my-order-single-field">
											<input type="radio" name="my-order-payment-gateway" value="Khalti" id="my-order-khalti">
											<label for="my-order-khalti">Khalti</label>
										</div>
										<div class="my-order-single-field">
											<input type="radio" name="my-order-payment-gateway" value="Stripe Checkout" id="my-order-stripe-checkout">
											<label for="my-order-stripe-checkout">Stripe Checkout</label>
										</div>
									</form>
									<a class="my-order-single-payment-button my-order-single-button" href="#">Make an Online Payment</a> -->
								</div>
								<div class="my-order-single-content">
									<div class="row">
										<div class="col-md-6">
											<h3 class="my-order-single-title"><?php esc_html_e( 'Order Status', 'wp-travel' ); ?></h3>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Order Number :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo sprintf( '#%s', $booking_id ); ?></span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Booking Date :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo get_the_date( '', $booking_id ); ?></span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Tour :', 'wp-travel' ); ?></span>
												<span class="my-order-tail">
													<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $title ); ?></a>
												</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Travel Date :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo wp_travel_format_date( $travel_date ); ?></span>
											</div>
											<div class="my-order-single-field my-order-additional-note clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Customer\'s Note :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo esc_html( $customer_note ); ?></span>
											</div>
										</div>
										<div class="col-md-6">
											<h3 class="my-order-single-title"><?php esc_html_e( 'Billing Detail', 'wp-travel' ); ?></h3>
										
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'City :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo esc_html( $billing_city ); ?></span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Country :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo esc_html( $billing_country ); ?></span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Postal :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo esc_html( $billing_postal ); ?></span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Address :', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo esc_html( $billing_address ); ?></span>
											</div>
											
										</div>
									</div>
									<?php
									if ( is_array( $fname ) && count( $fname ) > 0 ) :
										foreach ( $fname as $booking_trip_id => $first_names  ) :
											if ( is_array( $first_names ) && count( $first_names ) > 0 ) :
											?>
												<div class="my-order-single-traveller-info">
													<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Travelers info [ %s ]', get_the_title( $booking_trip_id ) ), 'wp-travel' ); ?></h3>
													
													<?php foreach ( $first_names as $key => $first_name ) : ?>
														<div class="my-order-single-field clearfix">
															<span class="my-order-head"><?php esc_html_e( sprintf( 'Traveller %s :', $key + 1 ), 'wp-travel' ); ?></span>
															<span class="my-order-tail"><?php echo esc_html( $first_name . ' ' . $lname[ $booking_trip_id ][ $key ] ); ?></span>
														</div>
													<?php endforeach; ?>
												</div>
											<?php
											endif;
										endforeach;
									endif; ?>

									<div class="my-order-single-price-breakdown">
										<h3 class="my-order-single-title"><?php echo esc_html_e( 'Price Breakdown', 'wp-travel' ); ?></h3>
										<div class="my-order-price-breakdown">
											<?php
											$order_details = get_post_meta( $booking_id, 'order_items_data', true ); // Multiple Trips.
																		
											if ( $order_details ) {
												$order_prices = get_post_meta( $booking_id, 'order_totals', true );
												foreach( $order_details as $order_detail ) {
												?>
													<div class="my-order-price-breakdown-base-price-wrap">
														<div class="my-order-price-breakdown-base-price">
															<span class="my-order-head"><?php echo esc_html( get_the_title( $order_detail['trip_id'] ) ) ?></span>
															<span class="my-order-tail">
																<span class="my-order-price-detail"> x <?php echo esc_html( $order_detail['pax'] ) . ' '. __( 'Person/s', 'wp-travel' ); ?> </span>
																<span class="my-order-price"><?php echo wp_travel_get_currency_symbol().esc_html( $order_detail['trip_price'] ) ?></span>
															</span>
														</div>
													</div>
													
												<?php
												}
		
											} else { // single Trips. ?>
												<div class="my-order-price-breakdown-base-price-wrap">
													<div class="my-order-price-breakdown-base-price">
														<span class="my-order-head"><?php echo esc_html( get_the_title( $trip_id ) ) ?></span>
														<span class="my-order-tail">
															<span class="my-order-price-detail"> x <?php echo esc_html( $pax ) . ' '. __( 'Person/s', 'wp-travel' ); ?> </span>
															<span class="my-order-price"><?php echo wp_travel_get_currency_symbol().esc_html( $details['sub_total'] ) ?></span>
														</span>
													</div>
												</div>
											<?php
											} 
											?>


											<!-- <div class="my-order-price-breakdown-additional-service">
												<h3 class="my-order-price-breakdown-additional-service-title">Additional Services</h3>
												<div class="my-order-price-breakdown-additional-service-item clearfix">
													<span class="my-order-head">Umbrella (1 x $8)</span>
													<span class="my-order-tail my-order-right">$8.00</span>
												</div>
												<div class="my-order-price-breakdown-additional-service-item clearfix">
													<span class="my-order-head">Cleaning fee (5 x $9)</span>
													<span class="my-order-tail my-order-right">$45.00</span>
												</div>
												<div class="my-order-price-breakdown-additional-service-item clearfix">
													<span class="my-order-head">Tip for tour guide (5 x $20)</span>
													<span class="my-order-tail my-order-right">$100.00</span>
												</div>
											</div> -->
											
											<div class="my-order-price-breakdown-summary">
												<div class="my-order-price-breakdown-sub-total">
													<span class="my-order-head"><?php esc_html_e( 'Sub Total Price', 'wp-travel' ); ?></span>
													<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['sub_total'] ) ?></span>
												</div>
												<!-- <div class="my-order-price-breakdown-coupon-code">
													<span class="my-order-head">Coupon Code :</span>
													<span class="my-order-tail">
														<span class="my-order-coupon-code">10PSpecial</span>
														<span class="my-order-coupon-text">10%</span>
													</span>
												</div> -->
												<?php if ( $details['discount'] ) : ?>
													<div class="my-order-price-breakdown-coupon-amount">
														<span class="my-order-head"><?php esc_html_e( 'Discount Price', 'wp-travel' ); ?></span>
														<span class="my-order-tail my-order-right">- <?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['discount'] ) ?></span>
													</div>
												<?php endif; ?>
												<!-- <div class="my-order-price-breakdown-tax-rate">
													<span class="my-order-head">Tax Rate</span>
													<span class="my-order-tail my-order-right">9%</span>
												</div> -->
												<div class="my-order-price-breakdown-tax-due">
													<span class="my-order-head"><?php esc_html_e( 'Tax', 'wp-travel' ) ?> </span>
													<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['tax'] ) ?></span>
												</div>
												<!-- <div class="my-order-price-breakdown-service-fee">
													<span class="my-order-head">Paypal Service Fee (3%)</span>
													<span class="my-order-tail my-order-right">$201.20</span>
												</div> -->
											</div>
											<div class="clear"></div>
										</div>
										<div class="my-order-single-total-price clearfix">
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Total', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['total'] ) ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- <table>
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
							</table> -->
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
	// do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id );
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
								<!-- <th><?php // esc_html_e( 'Contact Name', 'wp-travel' ); ?></th> -->
								<th><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></th>
								<!-- <th><?php // esc_html_e( 'Payment Mode', 'wp-travel' ); ?></th> -->
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
							continue;
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

							<!-- <td class="c-name" data-title="<?php // esc_html_e( 'Contact Name', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php // echo esc_html( $fname . ' ' . $lname ); ?>
								</div>
							</td> -->

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

							<!-- <td class="payment-mode" data-title="<?php // esc_html_e( 'Payment Mode', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php // echo esc_html( $payment_mode ); ?>
							<?php // do_action( 'wp_travel_dashboard_booking_after_payment_mode', $ordered_data, $payment_info ); ?>
								</div>
							</td> -->

							<td class="product-subtotal" data-title="<?php esc_html_e( 'Total Price', 'wp-travel' ); ?>">
								<div class="order-list-table">
								<p>
								<strong>
								<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
								<span class="wp-travel-trip-total"> <?php echo esc_html( $total_price ); ?> </span>
								</strong>
								</p>
								</div>
							</td>
							<td class="product-subtotal" data-title="<?php esc_html_e( 'Paid', 'wp-travel' ); ?>">
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
