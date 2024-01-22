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
if ( ! function_exists( 'wptravel_payments_tab_content' ) ) {
	function wptravel_payments_tab_content( $args ) {

		$bookings = $args['bookings'];
		$bookings = is_array( $bookings ) ? array_unique( $bookings ) : $bookings;
		global $wp;
		$detail_link  = home_url( $wp->request ) . '#bookings';
		$back_link    = $detail_link;
		$request_data = WP_Travel::get_sanitize_request();

		$is_administrator = current_user_can( 'administrator' );
		?>
		<div class="my-order">
			<?php if ( $is_administrator ) : ?>
				<div class="view-order">
					<div class="order-list">
						<div class="order-wrapper">
							<?php
							$paged = ( get_query_var( 'paged' ) && isset( $_GET[ 'payment' ] ) ) ? get_query_var( 'paged' ) : 1; // @phpcs:ignore
							$query_args = array(
								'post_type'  => 'wp-travel-payment',
								'paged'      => $paged,
								'meta_query' => array(
									array(
										'key'     => 'wp_travel_payment_amount',
										'value'   => 0,
										'compare' => '>',
									),
								),
							);
							/**
							 * Hook to filter query args of payment listings.
							 *
							 * @since 5.3.1
							 */
							$query_args = apply_filters( 'wptravel_dashboard_payments_query_args', $query_args );
							// The Query
							$the_query = new WP_Query( $query_args );

							?>
							<h3><?php esc_html_e( 'Payments', 'wp-travel' ); ?> (<?php echo esc_html( $the_query->found_posts ); ?>)</h3>
							<div class="table-wrp">
							<table class="order-list-table">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Payment Date', 'wp-travel' ); ?></th>
										<th><?php esc_html_e( 'Payment ID', 'wp-travel' ); ?></th>
										<th><?php esc_html_e( 'Payment Method', 'wp-travel' ); ?></th>
										<th><?php esc_html_e( 'Payment Amount', 'wp-travel' ); ?></th>
										<th><?php esc_html_e( 'Action', 'wp-travel' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php

									// The Loop
									if ( $the_query->have_posts() ) {
										while ( $the_query->have_posts() ) {
											$the_query->the_post();
											$payment_id = get_the_ID();

											$payment_amount = get_post_meta( $payment_id, 'wp_travel_payment_amount', true );
											$payment_method = get_post_meta( $payment_id, 'wp_travel_payment_gateway', true );
											$payment_method = str_replace( '_', ' ', $payment_method );
											$meta_name      = sprintf( '_%s_args', $payment_method );

											$ajax_url = admin_url( 'admin-ajax.php' );
											$ajax_url = add_query_arg( 'booking_id', $payment_id, $ajax_url );
											$ajax_url = add_query_arg( 'payment_id', $payment_id, $ajax_url );
											$ajax_url = add_query_arg( 'action', 'wptravel_get_payment_details_html', $ajax_url );
											$ajax_url = add_query_arg( '_nonce', WP_Travel::create_nonce(), $ajax_url );
											?>
											<tr class="tbody-content">
												<td class="name" data-title="<?php esc_html_e( 'Payment Date', 'wp-travel' ); ?>">
													<div class="name-title">
														<?php echo esc_html( get_the_date() ); ?>
													</div>
												</td>
												<td class="name" data-title="<?php esc_html_e( 'Payment ID', 'wp-travel' ); ?>">
													<div class="name-title">
														#<?php echo esc_html( $payment_id ); ?>
													</div>
												</td>
												<td class="booking-status" data-title="<?php esc_html_e( 'Payment Method', 'wp-travel' ); ?>">
													<div class="contact-title">
														<?php echo esc_html( $payment_method ); ?>
													</div>
												</td>
												<td class="payment-status" data-title="<?php esc_html_e( 'Payment Amount', 'wp-travel' ); ?>">
													<div class="contact-title">
														<?php echo wptravel_get_formated_price_currency( $payment_amount ); // @phpcs:ignore ?>
													</div>
												</td>
												<td class="payment-status" data-title="<?php esc_html_e( 'Action', 'wp-travel' ); ?>">
													<div class="contact-title">
														<a class="wptravel-payment-popup" href="<?php echo esc_url( $ajax_url ); ?>">
															<?php esc_html_e( 'Detail', 'wp-travel' ); ?>
														</a>
													</div>
												</td>
											</tr>
											<?php
										}
									}
									?>
								</tbody>
							</table>
							</div>
						</div>
					</div>
					<?php
					$pagination_range = apply_filters( 'wp_travel_pagination_range', 2 );
					$max_num_pages    = apply_filters( 'wp_travel_max_num_pages', '' );
					wptravel_pagination( $pagination_range, $max_num_pages, $the_query, '#payments' );
					wp_reset_postdata();

					?>
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
		<script>
			jQuery(function ($) {
				jQuery('.wptravel-payment-popup').magnificPopup({
					type: 'ajax',
					preloader: false,
					midClick: false,
				});
			});
		</script>
		<?php
	}
	wptravel_payments_tab_content( $args );
}
