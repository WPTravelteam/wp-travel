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
		$status_color = isset( $details['payment_status'] ) && isset( $status_list[ $details['payment_status'] ]['color'] ) ? $status_list[ $details['payment_status'] ]['color'] : '';
		?>
		<div class="my-order my-order-details">
			<div class="view-order">
				<div class="order-list">
					<div class="order-wrapper">
						<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
						<div class="table-wrp">
							<!-- Started Here -->
							<div class="my-order-single-content-wrap">
								<?php if ( wp_travel_is_payment_enabled() ) : ?>
									<div class="my-order-single-sidebar">
										<h3 class="my-order-single-title"><?php esc_html_e( 'Payment Status', 'wp-travel' ); ?></h3>
										<div class="my-order-status my-order-status-<?php echo esc_html( $details['payment_status'] ); ?>" style="background:<?php echo esc_attr( $status_color ); ?>" ><?php echo esc_html( ucfirst( $details['payment_status'] ) ); ?></div>
										<?php do_action( 'wp_travel_dashboard_booking_after_detail', $booking_id ); ?>
									</div>
								<?php endif; ?>
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
													<?php
													if ( $order_details && is_array( $order_details ) && count( $order_details ) > 0 ) : // Multiple.
														foreach ( $order_details as $order_detail ) :
															$trip_id      = $order_detail['trip_id'];
															$price_key    = $order_detail['price_key'];
															$pricing_name = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
															?>
															<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>, 
															<?php
														endforeach;
													else :
														$pricing_name = wp_travel_get_trip_pricing_name( $trip_id );
														?>
														<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>" target="_blank"><?php echo esc_attr( $pricing_name ); ?></a>
													<?php endif; ?>
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
										foreach ( $fname as $cart_key => $first_names ) :
											if ( is_array( $first_names ) && count( $first_names ) > 0 ) :
												$trip_id      = $order_details[ $cart_key ]['trip_id'];
												$price_key    = $order_details[ $cart_key ]['price_key'];
												$pricing_name = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
												?>
												<div class="my-order-single-traveller-info">
													<h3 class="my-order-single-title"><?php esc_html_e( sprintf( 'Travelers info [ %s ]', $pricing_name ), 'wp-travel' ); ?></h3>

													<?php foreach ( $first_names as $key => $first_name ) :
														// if ( '' === $first_name && '' === $lname[ $cart_key ][ $key ] ) {
														// 	continue;
														// }
														?>
														<div class="my-order-single-field clearfix">
															<span class="my-order-head"><?php esc_html_e( sprintf( 'Traveller %s :', $key + 1 ), 'wp-travel' ); ?></span>
															<span class="my-order-tail"><?php echo esc_html( $first_name . ' ' . $lname[ $cart_key ][ $key ] ); ?></span>
														</div>
													<?php endforeach; ?>
												</div>
												<?php
											endif;
										endforeach;
									endif;
									?>

									<?php
									if ( isset( $details['total'] ) && $details['total'] > 0 ) :
										?>
									<div class="my-order-single-price-breakdown">
										<h3 class="my-order-single-title"><?php echo esc_html_e( 'Price Breakdown', 'wp-travel' ); ?></h3>
										<div class="my-order-price-breakdown">
											<?php

											if ( $order_details ) { // Multiple Trips. Now from 1.8.3 it also included in single trip.
												$order_prices = get_post_meta( $booking_id, 'order_totals', true );
												foreach ( $order_details as $order_detail ) {
													$pax        = $order_detail['pax'];
													$trip_price = $order_detail['trip_price'];
													$total      = wp_travel_get_formated_price( $trip_price * $pax );
													?>
													<div class="my-order-price-breakdown-base-price-wrap">
														<div class="my-order-price-breakdown-base-price">
															<span class="my-order-head"><?php echo esc_html( get_the_title( $order_detail['trip_id'] ) ); ?></span>
															<span class="my-order-tail">
																<span class="my-order-price-detail">(<?php echo sprintf( '%s x %s%s', $pax, wp_travel_get_currency_symbol(), $trip_price ); ?>) </span>
																<span class="my-order-price"><?php echo wp_travel_get_currency_symbol() . esc_html( $total ); ?></span>
															</span>
														</div>
													</div>
													<?php
													if ( isset( $order_detail['trip_extras'] ) && isset( $order_detail['trip_extras']['id'] ) && count( $order_detail['trip_extras']['id'] ) > 0 ) :
														$extras = $order_detail['trip_extras'];

														?>
													
														<div class="my-order-price-breakdown-additional-service">
															<h3 class="my-order-price-breakdown-additional-service-title"><?php esc_html_e( 'Additional Services', 'wp-travel' ); ?></h3>
															<?php
															foreach ( $order_detail['trip_extras']['id'] as $k => $extra_id ) :

																$trip_extras_data = get_post_meta( $extra_id, 'wp_travel_tour_extras_metas', true );

																$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
																$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

																if ( $sale_price ) {
																	$price = $sale_price;
																}

																$qty = isset( $extras['qty'][ $k ] ) ? $extras['qty'][ $k ] : 1;

																$price = wp_travel_get_formated_price( $price );
																$total = wp_travel_get_formated_price( $price * $qty );
																?>
																<div class="my-order-price-breakdown-additional-service-item clearfix">
																	<span class="my-order-head"><?php echo esc_html( get_the_title( $extra_id ) ); ?> (<?php echo sprintf( '%s x %s%s', $extras['qty'][ $k ], wp_travel_get_currency_symbol(), $price ); ?> )</span>
																	<span class="my-order-tail my-order-right"><?php echo esc_html( wp_travel_get_currency_symbol() . $total ); ?></span>
																</div>
															<?php endforeach; ?>

														</div>

														<?php
													endif;
												}
											} else { // single Trips.
												?>
												<div class="my-order-price-breakdown-base-price-wrap">
													<div class="my-order-price-breakdown-base-price">
														<span class="my-order-head"><?php echo esc_html( get_the_title( $trip_id ) ); ?></span>
														<span class="my-order-tail">
															<span class="my-order-price-detail"> x <?php echo esc_html( $pax ) . ' ' . __( 'Person/s', 'wp-travel' ); ?> </span>
															<span class="my-order-price"><?php echo wp_travel_get_currency_symbol() . esc_html( $details['sub_total'] ); ?></span>
														</span>
													</div>
												</div>
												<?php
											}
											?>

											<div class="my-order-price-breakdown-summary clearfix">
												<div class="my-order-price-breakdown-sub-total">
													<span class="my-order-head"><?php esc_html_e( 'Sub Total Price', 'wp-travel' ); ?></span>
													<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['sub_total'] ); ?></span>
												</div>

												<?php if ( $details['discount'] ) : ?>
													<div class="my-order-price-breakdown-coupon-amount">
														<span class="my-order-head"><?php esc_html_e( 'Discount Price', 'wp-travel' ); ?></span>
														<span class="my-order-tail my-order-right">- <?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['discount'] ); ?></span>
													</div>
												<?php endif; ?>

												<div class="my-order-price-breakdown-tax-due">
													<span class="my-order-head"><?php esc_html_e( 'Tax', 'wp-travel' ); ?> </span>
													<span class="my-order-tail my-order-right"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['tax'] ); ?></span>
												</div>
											</div>
										</div>
										<div class="my-order-single-total-price clearfix">
											<div class="my-order-single-field clearfix">
												<span class="my-order-head"><?php esc_html_e( 'Total', 'wp-travel' ); ?></span>
												<span class="my-order-tail"><?php echo wp_travel_get_currency_symbol() . ' ' . esc_html( $details['total'] ); ?></span>
											</div>
										</div>
									</div>
									<?php endif; ?>
								</div>
							</div>							
						</div>
					</div>
					<?php
					if ( $payment_data && count( $payment_data ) > 0 ) {
						?>
						<h3><?php esc_html_e( 'Payment Details', 'wp-travel' ); ?></h3>
						<table class="my-order-payment-details">
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
