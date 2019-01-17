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
						<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
						<div class="table-wrp">
							<!-- Started Here -->
							<div class="my-order-single-content-wrap">
								<div class="my-order-single-sidebar">
									<h3 class="my-order-single-title">Order Status</h3>
									<div class="my-order-status my-order-status-pending">Pending</div>
									<h3 class="my-order-single-sub-title">Payment Gateway</h3>
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
									<a class="my-order-single-payment-button my-order-single-button" href="#">Make an Online Payment</a>
								</div>
								<div class="my-order-single-content">
									<div class="row">
										<div class="col-md-6">
											<h3 class="my-order-single-title">Order Status</h3>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Order Number :</span>
												<span class="my-order-tail">#2</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Booking Date :</span>
												<span class="my-order-tail">January 16, 2019</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Tour :</span>
												<span class="my-order-tail">
													<a href="#" target="_blank">Dubai - All Stunning Places</a>
												</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Travel Date :</span>
												<span class="my-order-tail">January 26, 2019</span>
											</div>
											<div class="my-order-single-field my-order-additional-note clearfix">
												<span class="my-order-head">Customer's Note :</span>
												<span class="my-order-tail">Iure possimus nobis veritatis Nam ut vel mollitia ex incididunt enim dolor exercitation dolores</span>
											</div>
										</div>
										<div class="col-md-6">
											<h3 class="my-order-single-title">Billing Detail</h3>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">First Name :</span>
												<span class="my-order-tail">Chelsea</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Last Name :</span>
												<span class="my-order-tail">Cooper</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Email :</span>
												<span class="my-order-tail">
													<a href="mailto:dasyweqis@mailinator.net">dasyweqis@mailinator.net</a>
												</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Phone :</span>
												<span class="my-order-tail">+697-60-9484210</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Country :</span>
												<span class="my-order-tail">Guinea-Bissau</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Address :</span>
												<span class="my-order-tail">Sed qui est ipsum ut sit nostrum</span>
											</div>
										</div>
										<div class="col-md-12 my-order-single-col-last">
											<h3 class="my-order-single-title">Billing Detail</h3>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">First Name :</span>
												<span class="my-order-tail">Yoshi</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Last Name :</span>
												<span class="my-order-tail">Dyer</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Email :</span>
												<span class="my-order-tail">
													<a href="mailto:pova@mailinator.net">pova@mailinator.net</a>
												</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Phone :</span>
												<span class="my-order-tail">+168-39-4333439</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Country :</span>
												<span class="my-order-tail">Malawi</span>
											</div>
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Address :</span>
												<span class="my-order-tail">Aperiam iste voluptatem aperiam reiciendis qui dicta voluptas tempor repellendus Fugiat</span>
											</div>
										</div>
									</div>
									<div class="my-order-single-traveller-info">
										<h3 class="my-order-single-title">Traveller Info</h3>
										<div class="my-order-single-field clearfix">
											<span class="my-order-head">Traveller 1 :</span>
											<span class="my-order-tail">Ms Britanney Petty</span>
										</div>
										<div class="my-order-single-field clearfix">
											<span class="my-order-head">Traveller 2 :</span>
											<span class="my-order-tail">Ms Kirestin Greene</span>
										</div>
										<div class="my-order-single-field clearfix">
											<span class="my-order-head">Traveller 3 :</span>
											<span class="my-order-tail">Mrs Karen Underwood</span>
										</div>
										<div class="my-order-single-field clearfix">
											<span class="my-order-head">Traveller 4 :</span>
											<span class="my-order-tail">Miss Sopoline Sanders</span>
										</div>
										<div class="my-order-single-field clearfix">
											<span class="my-order-head">Traveller 5 :</span>
											<span class="my-order-tail">Master Vielka Marshall</span>
										</div>
									</div>
									<div class="my-order-single-price-breakdown">
										<h3 class="my-order-single-title">Price Breakdown</h3>
										<div class="my-order-price-breakdown">
											<div class="my-order-price-breakdown-base-price-wrap">
												<div class="my-order-price-breakdown-base-price">
													<span class="my-order-head">Traveller Base Price</span>
													<span class="my-order-tail">
														<span class="my-order-price-detail">5 x $1,200</span>
														<span class="my-order-price">$6,000.00</span>
													</span>
												</div>
											</div>
											<div class="my-order-price-breakdown-additional-service">
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
											</div>
											<div class="my-order-price-breakdown-summary">
												<div class="my-order-price-breakdown-sub-total">
													<span class="my-order-head">Sub Total Price</span>
													<span class="my-order-tail my-order-right">$6,153.00</span>
												</div>
												<div class="my-order-price-breakdown-coupon-code">
													<span class="my-order-head">Coupon Code :</span>
													<span class="my-order-tail">
														<span class="my-order-coupon-code">10PSpecial</span>
														<span class="my-order-coupon-text">10%</span>
													</span>
												</div>
												<div class="my-order-price-breakdown-coupon-amount">
													<span class="my-order-head">Discount Price</span>
													<span class="my-order-tail my-order-right">- $368.70</span>
												</div>
												<div class="my-order-price-breakdown-tax-rate">
													<span class="my-order-head">Tax Rate</span>
													<span class="my-order-tail my-order-right">9%</span>
												</div>
												<div class="my-order-price-breakdown-tax-due">
													<span class="my-order-head">Tax Due</span>
													<span class="my-order-tail my-order-right">$553.77</span>
												</div>
												<div class="my-order-price-breakdown-service-fee">
													<span class="my-order-head">Paypal Service Fee (3%)</span>
													<span class="my-order-tail my-order-right">$201.20</span>
												</div>
											</div>
											<div class="clear"></div>
										</div>
										<div class="my-order-single-total-price clearfix">
											<div class="my-order-single-field clearfix">
												<span class="my-order-head">Total</span>
												<span class="my-order-tail">$6,907.97</span>
											</div>
										</div>
									</div>
								</div>
							</div>

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
								<!-- <th><?php //esc_html_e( 'Contact Name', 'wp-travel' ); ?></th> -->
								<th><?php esc_html_e( 'Booking Status', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></th>
								<!-- <th><?php //esc_html_e( 'Payment Mode', 'wp-travel' ); ?></th> -->
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

							<!-- <td class="c-name" data-title="<?php //esc_html_e( 'Contact Name', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php //echo esc_html( $fname . ' ' . $lname ); ?>
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

							<!-- <td class="payment-mode" data-title="<?php //esc_html_e( 'Payment Mode', 'wp-travel' ); ?>">
								<div class="contact-title">
							<?php //echo esc_html( $payment_mode ); ?>
							<?php //do_action( 'wp_travel_dashboard_booking_after_payment_mode', $ordered_data, $payment_info ); ?>
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
