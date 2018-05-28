<?php
/**
 * User dashboard template.
 *
 * @package WP_Travel
 */

// Set User.
$current_user = $args;
$bookings     = get_user_meta( $current_user->ID, 'wp_travel_user_bookings', true );

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
							<?php if ( ! empty( $bookings ) && is_array( $bookings ) ) : ?>
								<ul>
									<?php
										foreach ( $bookings as $key => $bk_id ) :

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
								<?php else : ?>
									<p>
										<?php esc_html_e( 'You haven&lsquo;t order anything yet.', 'wp-travel' ); ?>
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
								<p>
									Kathmandu<br>
									Nepal<br>
									9812345678<br>
								</p>
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
									sunil regmi<br>
									regmi.sunil@wensolutions.com<br>
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
				<!-- <div class="list-item">
					<div class="list-item-wrapper">
						<div class="item">
							<a href="#"><strong>Payment Option</strong></a>
							<div class="box-content">
								<p>
									Kathmandu<br>
									Nepal<br>
									9812345678<br>
								</p>
							</div>
							<div class="box-actions">
								<a class="action edit" href="#">
									<i class="fa fa-pencil" aria-hidden="true"></i>
									<span>Edit</span>
								</a>
							</div>
						</div>
					</div>
				</div> -->
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
											<th></th>
											<th>Tour</th>
											<th>Contact Name</th>
											<th>Booking Status</th>
											<th>Payment Status</th>
											<th>Payment Mode</th>
											<th class="text-right">Total Price</th>
										</tr>
									</thead>
									<tbody>
									<?php 
									foreach ( $bookings as $key => $b_id ) :
										$bkd_trip_id = get_post_meta( $b_id, 'wp_travel_post_id', true );
										$ordered_data = get_post_meta( $b_id, 'order_data', true );

										if ( ! $bkd_trip_id ) {
											continue;
										}
										echo '<pre>'; print_r( $ordered_data ); echo '</pre>';
									?>			
										<tr class="tbody-content">
											<td class="product-thumbnail">
												<a href="<?php echo esc_url( get_the_permalink( $bkd_trip_id ) ); ?>">
												<img src="<?php echo esc_url( wp_travel_get_post_thumbnail_url( get_the_ID(), 'thumbnail' ) ); ?>">
											</a> 
											</td>
											<td class="name" data-title="name">
												<div class="name-title">
														<a href="<?php echo esc_url( get_the_permalink( $bkd_trip_id ) ); ?>"><?php echo esc_html( get_the_title( $bkd_trip_id ) ); ?></a>
												</div>
											</td>
											<td class="price" data-title="Price">
												<span class="price-per-pack">
													<ins>
														<span>
															$ 500.00 
														</span>
													</ins>/pax						
												</span>
											</td>
											<td class="product-quantity" data-title="PAX">
												<div class="st_adults">
													<span>
														3
													</span>
												</div>
											</td>
											<td class="product-subtotal text-right" data-title="Total">
												<div class="order-list-table">
													<p>
														<strong>
															<span class="woocommerce-Price-currencySymbol">$</span>
															<span class="wp-travel-trip-total"> 1500.00 </span>
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
					<h3>Billing Address</h3>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label required">Address:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label required">City:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Company:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label required">Zip/Postal code:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Province:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label required">Conntry:</label>
						<div class="col-sm-8 col-md-9">
							<select class="form-control " data-placeholder="Conntry" tabindex="-1" aria-hidden="true">
								<option value="">Conntry</option>   
								<option value="Thai">Thai</option>
								<option value="Malaysian">Malaysian</option>
								<option value="Indonesian">Indonesian</option>
								<option value="American">American</option>
								<option value="England">England</option>
								<option value="German">German</option>
								<option value="Polish">Polish</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Mobile no.:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Land Line:</label>
						<div class="col-sm-8 col-md-9">
							<input type="text" class="form-control" value="">
						</div>
					</div>
				</div>
				<p><strong>*</strong>marked fields are required</p>
				<input type="submit" name="submit" value="Save">
			</div>
		</div> 


		<div class="account-setting">
			<div class="title">
				<h3>My Account</h3>
			</div>
			<div class="form-horizontal clearfix">
				<div class="form-group gap-20">
					<label class="col-sm-4 col-md-3 control-label">First Name:</label>
					<div class="col-sm-8 col-md-9">
						<input type="text" class="form-control" value="">
					</div>
				</div>
			</div>
			<div class="form-horizontal clearfix">
				<div class="form-group gap-20">
					<label class="col-sm-4 col-md-3 control-label">Last Name:</label>
					<div class="col-sm-8 col-md-9">
						<input type="text" class="form-control" value="">
					</div>
				</div>
			</div>
			<div class="form-horizontal clearfix">
				<div class="form-group gap-20">
					<label class="col-sm-4 col-md-3 control-label">E-mail:</label>
					<div class="col-sm-8 col-md-9">
						<input type="text" class="form-control" value="">
					</div>
				</div>
			</div>
			<div class="form-horizontal clearfix">
				<div class="form-group gap-20">
					<label class="col-sm-4 col-md-3 control-label">Change Password</label>
					<div class="col-sm-8 col-md-9">
						<div class="onoffswitch">
							<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch">
							<label class="onoffswitch-label" for="myonoffswitch">
							  <span class="onoffswitch-inner"></span>
							  <span class="onoffswitch-switch"></span>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="ch-password clearfix">
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Current Password:</label>
						<div class="col-sm-8 col-md-9">
							<input type="password" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">New Password:</label>
						<div class="col-sm-8 col-md-9">
							<input type="password" class="form-control" value="">
						</div>
					</div>
				</div>
				<div class="form-horizontal clearfix">
					<div class="form-group gap-20">
						<label class="col-sm-4 col-md-3 control-label">Conform New Password:</label>
						<div class="col-sm-8 col-md-9">
							<input type="password" class="form-control" value="">
						</div>
					</div>
				</div>
			<input type="submit" name="submit" value="Save Change">

			</div>
		</div>
		<div class="log-out">
			<div class="title">
				<h3>Log Out?</h3>
				<span>
					Are you sure want to log out?
					<a href="<?php echo wp_logout_url( wp_travel_get_page_permalink( 'wp-travel-dashboard' ) ); ?>"><?php esc_html_e( 'Log Out', 'wp-travel' ); ?></a>
				</span>
			</div>

		</div>

	</div>
</div>

