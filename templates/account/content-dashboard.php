<?php
/**
 * User dashboard template.
 *
 * @package WP_Travel
 */

// Print Errors / Notices.
WP_Travel()->notices->print_notices( 'error', true );
WP_Travel()->notices->print_notices( 'success', true );

// Set User.
$current_user    = $args;
$bookings        = get_user_meta( $current_user->ID, 'wp_travel_user_bookings', true );
$bookings_glance = false;
// Resverse Chronological Order For Bookings.
if ( ! empty( $bookings ) && is_array( $bookings ) ) {
	$bookings        = array_reverse( $bookings );
	$bookings_glance = array_slice( $bookings, 0, 5 );
}

$biling_glance_data = get_user_meta( $current_user->ID, 'wp_travel_customer_billing_details', true );

?>
<div class="dashboard-tab">
	<ul class="resp-tabs-list ver_1">
		<li><i class="fa fa-tachometer" aria-hidden="true"></i><?php esc_html_e( 'Dashboard', 'wp-travel' ); ?></li>
		<li id="wp-tab-mybookings"><i class="fa fa-th-list" aria-hidden="true"></i><?php esc_html_e( 'My Bookings', 'wp-travel' ); ?></li>
		<li id="wp-tab-myaddress"><i class="fa fa-address-book-o" aria-hidden="true"></i><?php esc_html_e( 'Address', 'wp-travel' ); ?></li>
		<li id="wp-tab-myaccount"><i class="fa fa-user" aria-hidden="true"></i><?php esc_html_e( 'Account', 'wp-travel' ); ?></li>
		<li><i class="fa fa-power-off" aria-hidden="true"></i><?php esc_html_e( 'Logout', 'wp-travel' ); ?></li>
	</ul>
	<div class="resp-tabs-container ver_1">
		<div>
			<p><?php esc_html_e( 'Hello, ', 'wp-travel' ); ?><strong><?php echo esc_html( $current_user->display_name ); ?></strong></p>

			<p><?php esc_html_e( 'From your account dashboard you can view your recent Bookings, manage your billing address and edit your password and account details.', 'wp-travel' ); ?></p>
			<div class="lists clearfix">
				<div class="list-item">
					<div class="list-item-wrapper">
						<div class="item">
							<a href="#" class="dashtab-nav" data-tabtitle="wp-tab-mybookings"><strong><?php esc_html_e( 'My Bookings', 'wp-travel' ); ?></strong></a>
							<div class="box-content">
							<?php if ( ! empty( $bookings_glance ) && is_array( $bookings_glance ) ) : ?>
								<ul>
									<?php
									foreach ( $bookings_glance as $key => $bk_id ) :

										$trip_id = get_post_meta( $bk_id, 'wp_travel_post_id', true );

										if ( ! $trip_id ) {
											continue;
										}
										?>
										<li>
											<a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>"><?php echo esc_html( get_the_title( $trip_id ) ); ?></a>
										</li>

									<?php
									endforeach;
									?>
								</ul>
								<a href="#" data-tabtitle="wp-tab-mybookings" class="dashtab-nav"><strong><?php esc_html_e( 'View All', 'wp-travel' ); ?></strong></a>
								<?php else : ?>
									<p>
										<?php esc_html_e( 'You haven&lsquo;t booked any trips yet.', 'wp-travel' ); ?>
									</p>
									<a href="<?php echo esc_url( get_post_type_archive_link( WP_TRAVEL_POST_TYPE ) ); ?>"><?php esc_html_e( 'Book some trips now', 'wp-travel' ); ?></a>
								<?php endif; ?>
							</div>
							<div class="box-actions">
							</div>
						</div>
					</div>
				</div>
				<div class="list-item">
					<div class="list-item-wrapper">
						<div class="item">
							<a href="#" class="dashtab-nav" data-tabtitle="wp-tab-myaddress"><strong><?php esc_html_e( 'Address', 'wp-travel' ); ?></strong></a>
							<div class="box-content">
								<?php if ( is_array( $biling_glance_data ) && ! empty( $biling_glance_data ) ) : ?>
									<p>
										<?php echo esc_html( $biling_glance_data['billing_address'] ); ?><br>
										<?php echo esc_html( $biling_glance_data['billing_city'] ); ?><br>
										<?php echo esc_html( $biling_glance_data['billing_zip_code'] ); ?><br>
										<?php echo esc_html( $biling_glance_data['billing_country'] ); ?><br>
									</p>
								<?php endif; ?>
							</div>
							<div class="box-actions">
								<a href="#" data-tabtitle="wp-tab-myaddress" class="action dashtab-nav edit" href="#">
									<i class="fa fa-pencil" aria-hidden="true"></i>
									<span><?php esc_html_e( 'Edit', 'wp-travel' ); ?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
				<div class="list-item">
					<div class="list-item-wrapper">
						<div class="item">
							<a href="#" class="dashtab-nav" data-tabtitle="wp-tab-myaccount"><strong><?php esc_html_e( 'Account Info', 'wp-travel' ); ?></strong></a>
							<div class="box-content">
								<p>
									<?php echo esc_html( $current_user->first_name . ' ' . $current_user->last_name ); ?><br>
									<?php echo esc_html( $current_user->user_email ); ?><br>
								</p>
							</div>
							<div class="box-actions">
								<a data-tabtitle="wp-tab-myaccount" class="action edit dashtab-nav" href="#">
									<i class="fa fa-pencil" aria-hidden="true"></i>
									<span><?php esc_html_e( 'Edit', 'wp-travel' ); ?></span>
								</a>
								<a href="#" data-tabtitle="wp-tab-myaccount" class="action dashtab-nav action change-password">
									<?php esc_html_e( 'Change Password', 'wp-travel' ); ?></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
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
										</tr>
									</thead>
									<tbody>
									<?php 
									foreach ( $bookings as $key => $b_id ) :

										$bkd_trip_id = get_post_meta( $b_id, 'wp_travel_post_id', true );

										if ( ! $bkd_trip_id ) {
											continue;
										}

										$ordered_data = get_post_meta( $b_id, 'order_data', true );
										$booking_status = get_post_meta( $b_id, 'wp_travel_booking_status', true );
										$booking_status = ! empty( $booking_status ) ? $booking_status : 'N/A';

										$payment_id = get_post_meta( $b_id, 'wp_travel_payment_id', true );
										$payment_status = 'N/A';
										$payment_mode   = 'N/A';
										$trip_price     = isset( $ordered_data['wp_travel_trip_price'] ) ? $ordered_data['wp_travel_trip_price'] : 0;
										if ( $payment_id ) {
											$payment_status = get_post_meta( $payment_id, 'wp_travel_payment_status', true );
											$payment_mode = get_post_meta( $payment_id, 'wp_travel_payment_mode' , true );

											if ( 'paid' === $payment_status ) {
												$trip_price = ( get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) ) ? get_post_meta( $payment_id, 'wp_travel_payment_amount', true ) : 0;
											}	
										}
									?>			
										<tr class="tbody-content">

											<td class="name" data-title="name">
												<div class="name-title">
														<a href="<?php echo esc_url( get_the_permalink( $bkd_trip_id ) ); ?>"><?php echo esc_html( get_the_title( $bkd_trip_id ) ); ?></a>
												</div>
											</td>

											<td class="c-name" data-title="Contact Name">
												<div class="contact-title">
														<?php echo esc_html( $ordered_data['wp_travel_fname'] . ' ' . $ordered_data['wp_travel_lname'] ); ?>
												</div>
											</td>

											<td class="booking-status" data-title="Booking Status">
												<div class="contact-title">
														<?php echo esc_html( $booking_status ); ?>
												</div>
											</td>

											<td class="payment-status" data-title="Payment Status">
												<div class="contact-title">
												<?php echo esc_html( $payment_status ); ?>
												</div>
											</td>

											<td class="payment-mode" data-title="Payment Mode">
												<div class="contact-title">
														<?php echo esc_html( $payment_mode ); ?>
												</div>
											</td>

											<td class="product-subtotal text-right" data-title="Total">
												<div class="order-list-table">
													<p>
														<strong>
															<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>
															<span class="wp-travel-trip-total"> <?php echo esc_html( $trip_price ); ?> </span>
														</strong>
													</p>
												</div>
											</td> 
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

		<div class="clearfix">
			<div class="payment-content">
				<div class="title">
					<h3><?php esc_html_e( 'Billing Address', 'wp-travel' ); ?></h3>
				</div>
				<?php 
				echo wp_travel_get_template_html( 'account/form-edit-billing.php', 
				array(
				'user'   => $current_user,
				) );
			?>
			</div>
		</div> 

		<div class="account-setting">
			<div class="title">
				<h3><?php esc_html_e( 'My Account', 'wp-travel' ); ?></h3>
			</div>
			<?php 
				echo wp_travel_get_template_html( 'account/form-edit-account.php', 
				array(
				'user'   => $current_user,
				) );
			?>
		</div>
		<div class="log-out">
			<div class="title">
				<h3><?php esc_html_e( 'Log Out?', 'wp-travel' ); ?></h3>
				<span>
					<?php esc_html_e( 'Are you sure want to log out?', 'wp-travel' ); ?>
					<a href="<?php echo wp_logout_url( wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ); ?>"><?php esc_html_e( 'Log Out', 'wp-travel' ); ?></a>
				</span>
			</div>

		</div>

	</div>
</div>

