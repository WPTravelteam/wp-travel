<?php
/**
 * Dashboard Booking Tab.
 *
 * @package WP_Travel
 */

/**
 * Display Booking tab content.
 *
 * @param array $args Tab args.
 */
if ( ! function_exists( 'wptravel_account_tab_content' ) ) {
	/**
	 * Account tab content HTML.
	 *
	 * @param array $args Tab content arguments.
	 *
	 * @since 2.3.0
	 */
	function wptravel_account_tab_content( $args ) {

		$bookings = $args['bookings'];
		$bookings = is_array( $bookings ) ? array_unique( $bookings ) : $bookings;
		global $wp;
		$detail_link  = home_url( $wp->request ) . '#bookings';
		$back_link    = $detail_link;
		$request_data = WP_Travel::get_sanitize_request();

		if ( $request_data ) { // @phpcs:ignore
			wptravel_print_notices();
			$booking_id    = isset( $request_data['detail_id'] ) ? absint( $request_data['detail_id'] ) : 0;
			$details       = wptravel_booking_data( $booking_id );
			$payment_data  = wptravel_payment_data( $booking_id );
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
			$status_list = wptravel_get_payment_status();
			if ( is_array( $details ) && count( $details ) > 0 ) {
				?>
				<div class="my-order my-order-details">
					<div class="view-order">
						<div class="order-list">
							<div class="order-wrapper">
								<h3><?php esc_html_e( 'Your Booking Details', 'wp-travel' ); ?> <a href="<?php echo esc_url( $back_link ); ?>"><?php esc_html_e( '(Back)', 'wp-travel' ); ?></a></h3>
								<?php wptravel_view_booking_details_table( $booking_id ); ?>
							</div>
							<?php echo WpTravel_Helpers_Payment::render_payment_details( $booking_id ); // @phpcs:ignore ?>
						</div>
					</div>
				</div>
				<?php
			}
		} else {
			$is_administrator = current_user_can( 'administrator' );
			?>
			<div class="my-order">
				<?php if ( ( ! empty( $bookings ) && is_array( $bookings ) ) || $is_administrator ) : ?>
					<div class="view-order">
						<div class="order-list">
							<div class="order-wrapper">
								<?php
								$paged      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
								$query_args = array(
									'post_type' => 'itinerary-booking',
									'paged'     => $paged,
								);
								if ( ! $is_administrator ) {
									$query_args['post__in'] = $bookings;
								}
								/**
								 * Hook to filter query args of custom booking listings.
								 *
								 * @since 5.3.1
								 */
								$query_args = apply_filters( 'wptravel_dashboard_bookings_query_args', $query_args );
								// The Query
								$the_query = new WP_Query( $query_args );

								?>
								<h3><?php esc_html_e( 'Your Bookings', 'wp-travel' ); ?> (<?php echo esc_html( $the_query->found_posts ); ?>)</h3>
								<div class="table-wrp">
									<?php
									/**
									 * Hook to add content before booking list starts.
									 *
									 * @since 5.3.1
									 */
									do_action( 'wptravel_dashboard_bookings_before_list', $args ); ?>
									<table class="order-list-table">
										<thead>
											<tr>
												<th><?php esc_html_e( 'Booking ID', 'wp-travel' ); ?></th>
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
										// The Loop
										if ( $the_query->have_posts() ) {
											while ( $the_query->have_posts() ) {
												$the_query->the_post();

												$b_id = get_the_ID();

												$bkd_trip_id    = get_post_meta( $b_id, 'wp_travel_post_id', true );
												$booking_status = get_post_status( $b_id );

												if ( ! $bkd_trip_id ) {
													// Quick fix booking list hide from dashboard if booking updated form admin [ meta - wp_travel_post_id is not updated ].
													$order_details = get_post_meta( $b_id, 'order_items_data', true ); // Multiple Trips.
													if ( $order_details && is_array( $order_details ) && count( $order_details ) > 0 ) : // Multiple.
														$travel_date = '';
														foreach ( $order_details as $order_detail ) :
															$bkd_trip_id = $order_detail['trip_id'];
															break;
														endforeach;
													endif;
												}

												if ( 'publish' !== $booking_status ) {
													continue;
												}

												$payment_info = wptravel_booking_data( $b_id );

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
												$detail_link = add_query_arg( 'detail_id', $b_id, $detail_link );
												$detail_link = add_query_arg( '_nonce', WP_Travel::create_nonce(), $detail_link );
												?>
												<tr class="tbody-content">
													<td class="name" data-title="#<?php echo esc_html( $b_id ); ?>">
														<div class="name-title">
															<a href="<?php echo esc_url( $detail_link ); ?>">#<?php echo esc_html( $b_id ); ?></a>
														</div>
													</td>
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
															$status_lists = wptravel_get_payment_status();
															$status       = $status_lists[ $payment_status ];
															echo esc_html( $status['text'] );
															?>
														</div>
													</td>
													<td class="product-subtotal" data-title="<?php esc_html_e( 'Total Price', 'wp-travel' ); ?>">
														<div class="order-list-table">
														<p>
														<strong>
														<span class="wp-travel-trip-total"> <?php echo wptravel_get_formated_price_currency( $total_price, false, '', $b_id ); // @phpcs:ignore ?> </span>
														</strong>
														</p>
														</div>
													</td>
													<td class="product-subtotal" data-title="<?php esc_html_e( 'Paid', 'wp-travel' ); ?>">
														<div class="order-list-table">
														<p>
														<strong>
														<span class="wp-travel-trip-total"> <?php echo wptravel_get_formated_price_currency( $paid_amount, false, '', $b_id ); // @phpcs:ignore ?> </span>
														</strong>
														</p>
														</div>
													</td>
													<td class="payment-mode" data-title="<?php esc_html_e( 'Detail', 'wp-travel' ); ?>">
															<div class="contact-title">
																<a href="<?php echo esc_url( $detail_link ); ?>"><?php esc_html_e( 'Detail', 'wp-travel' ); ?></a>
														</div>
													</td>
													<?php do_action( 'wp_travel_dashboard_booking_table_content_after_detail', $b_id, $ordered_data, $payment_info ); ?>
												</tr>
												<?php
											}
										}
										?>
										</tbody>
									</table>
								<?php
								/**
								 * Hook to add content after booking list table.
								 *
								 * @since 5.3.1
								 */
								do_action( 'wptravel_dashboard_bookings_after_list' ); ?>
								</div>
							</div>
						</div>
						<?php
						$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
						$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
						wptravel_pagination( $pagination_range, $max_num_pages, $the_query, '#bookings' );
						wp_reset_postdata();

						?>
						<div class="book-more">
							<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book more ?', 'wp-travel' ); ?></a>
						</div>
					</div>
				<?php else :
					$query_data = new WP_Query( [
						'post_type' => 'itinerary-booking',
						'post_per_page' => -1
					]);
					$booking_id_list = [];
					$user_ac = wp_get_current_user();
					$user_detail_st_class = $user_ac->data;
					$user_emails = $user_detail_st_class->user_email;
					while ( $query_data->have_posts() ) {
						$query_data->the_post();
						$bk_id = get_the_ID();
						$backend_add = get_post_meta( $bk_id, 'wp_travel_backend_booking_add', true );
						if ( $backend_add == 'yes' ) {
							$traveler_emails = get_post_meta( $bk_id, 'wp_travel_email_traveller', true );
							$cart_prod = array_key_first( $traveler_emails );
							$tvr_email = isset( $traveler_emails[$cart_prod] ) && isset( $traveler_emails[$cart_prod][0] ) ? $traveler_emails[$cart_prod][0] : '';
							if ( ! empty($tvr_email ) && $tvr_email ==  $user_emails ) {
								$booking_id_list[] = get_the_ID();
							}
							
						}
					}
					if ( ! empty( $booking_id_list ) && is_array( $booking_id_list ) && count( $booking_id_list ) ) {
						?>
						<div class="view-order">
							<div class="order-list">
								<div class="order-wrapper">
									<?php
									$paged      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
									$query_args = array(
										'post_type' => 'itinerary-booking',
										'paged'     => $paged,
										'post__in'	=> $booking_id_list,
									);
									/**
									 * Hook to filter query args of custom booking listings.
									 *
									 * @since 5.3.1
									 */
									$query_args = apply_filters( 'wptravel_dashboard_bookings_query_args', $query_args );
									// The Query
									$the_query = new WP_Query( $query_args );

									?>
									<h3><?php esc_html_e( 'Your Bookings', 'wp-travel' ); ?> (<?php echo esc_html( $the_query->found_posts ); ?>)</h3>
									<div class="table-wrp">
										<?php
										/**
										 * Hook to add content before booking list starts.
										 *
										 * @since 5.3.1
										 */
										do_action( 'wptravel_dashboard_bookings_before_list', $args ); ?>
										<table class="order-list-table">
											<thead>
												<tr>
													<th><?php esc_html_e( 'Booking ID', 'wp-travel' ); ?></th>
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
											// The Loop
											if ( $the_query->have_posts() ) {
												while ( $the_query->have_posts() ) {
													$the_query->the_post();

													$b_id = get_the_ID();

													$bkd_trip_id    = get_post_meta( $b_id, 'wp_travel_post_id', true );
													$booking_status = get_post_status( $b_id );

													if ( ! $bkd_trip_id ) {
														// Quick fix booking list hide from dashboard if booking updated form admin [ meta - wp_travel_post_id is not updated ].
														$order_details = get_post_meta( $b_id, 'order_items_data', true ); // Multiple Trips.
														if ( $order_details && is_array( $order_details ) && count( $order_details ) > 0 ) : // Multiple.
															$travel_date = '';
															foreach ( $order_details as $order_detail ) :
																$bkd_trip_id = $order_detail['trip_id'];
																break;
															endforeach;
														endif;
													}

													if ( 'publish' !== $booking_status ) {
														continue;
													}

													$payment_info = wptravel_booking_data( $b_id );

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
													$detail_link = add_query_arg( 'detail_id', $b_id, $detail_link );
													$detail_link = add_query_arg( '_nonce', WP_Travel::create_nonce(), $detail_link );
													?>
													<tr class="tbody-content">
														<td class="name" data-title="#<?php echo esc_html( $b_id ); ?>">
															<div class="name-title">
																<a href="<?php echo esc_url( $detail_link ); ?>">#<?php echo esc_html( $b_id ); ?></a>
															</div>
														</td>
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
																$status_lists = wptravel_get_payment_status();
																$status       = $status_lists[ $payment_status ];
																echo esc_html( $status['text'] );
																?>
															</div>
														</td>
														<td class="product-subtotal" data-title="<?php esc_html_e( 'Total Price', 'wp-travel' ); ?>">
															<div class="order-list-table">
															<p>
															<strong>
															<span class="wp-travel-trip-total"> <?php echo wptravel_get_formated_price_currency( $total_price, false, '', $b_id ); // @phpcs:ignore ?> </span>
															</strong>
															</p>
															</div>
														</td>
														<td class="product-subtotal" data-title="<?php esc_html_e( 'Paid', 'wp-travel' ); ?>">
															<div class="order-list-table">
															<p>
															<strong>
															<span class="wp-travel-trip-total"> <?php echo wptravel_get_formated_price_currency( $paid_amount, false, '', $b_id ); // @phpcs:ignore ?> </span>
															</strong>
															</p>
															</div>
														</td>
														<td class="payment-mode" data-title="<?php esc_html_e( 'Detail', 'wp-travel' ); ?>">
																<div class="contact-title">
																	<a href="<?php echo esc_url( $detail_link ); ?>"><?php esc_html_e( 'Detail', 'wp-travel' ); ?></a>
															</div>
														</td>
														<?php do_action( 'wp_travel_dashboard_booking_table_content_after_detail', $b_id, $ordered_data, $payment_info ); ?>
													</tr>
													<?php
												}
											}
											?>
											</tbody>
										</table>
									<?php
									/**
									 * Hook to add content after booking list table.
									 *
									 * @since 5.3.1
									 */
									do_action( 'wptravel_dashboard_bookings_after_list' ); ?>
									</div>
								</div>
							</div>
							<?php
							$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
							$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
							wptravel_pagination( $pagination_range, $max_num_pages, $the_query, '#bookings' );
							wp_reset_postdata();

							?>
							<div class="book-more">
								<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book more ?', 'wp-travel' ); ?></a>
							</div>
						</div> <?php
					} else {
					?>

					<div class="no-order">
						<p>
							<?php esc_html_e( 'You have not booked any trips', 'wp-travel' ); ?>
							<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book one now ?', 'wp-travel' ); ?></a>
						</p>
					</div>
					<?php } ?>
				<?php endif; ?>
			</div>
			<?php
		}
	}
	wptravel_account_tab_content( $args );
}
