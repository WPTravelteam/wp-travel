<?php
/**
 * Callback for Email tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_email( $tab, $args ) {
	$settings = $args['settings'];

		$send_booking_email_to_admin = $settings['send_booking_email_to_admin'];

		// Booking Admin Email.
		$booking_admin_email_settings = $settings['booking_admin_template_settings'];
		// Booking Client Email.
		$booking_client_email_settings = $settings['booking_client_template_settings'];

		// Payment Admin Email.
		$payment_admin_email_settings = $settings['payment_admin_template_settings'];
		// Payment Client Email.
		$payment_client_email_settings = $settings['payment_client_template_settings'];

		// Enquiry Admin Email.
		$enquiry_admin_email_settings = $settings['enquiry_admin_template_settings'];

		?>
		<?php do_action( 'wp_travel_tab_content_before_email', $args ); ?>
		<?php
		$upsell_args = array(
			'title'      => __( 'Want to get more e-mail customization options?', 'wp-travel' ),
			'content'    => __( 'By upgrading to Pro, you can get features like multiple email notifications, email footer powered by text removal options and more !', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
			'type'        => array( 'wp-travel-utilities' ),
		);
		wp_travel_upsell_message( $upsell_args );
		?>
		<table class="form-table">
			<tr>
				<th clospan="2">
					<h3><?php esc_html_e( 'General Options', 'wp-travel' ); ?></h3>
				</th>
			</tr>

			<tr>
				<th>
					<label for="wp_travel_global_from_email"><?php esc_html_e( 'From Email', 'wp-travel' ); ?></label>
				</th>
				<td>
					<input value="<?php echo isset( $args['settings']['wp_travel_from_email'] ) ? $args['settings']['wp_travel_from_email'] : get_option( 'admin_email' ); ?>" type="email" name="wp_travel_from_email" id="wp_travel_global_from_email">
					
					<p class="description"><label for="enable_multiple_travellers"><?php esc_html_e( 'Email address to send email from.', 'wp-travel' ); ?></label></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_tab_content_before_booking_tamplate', $args ); ?>
		<h3 class="wp-travel-section-title"><?php esc_html_e( 'Email Templates', 'wp-travel' ); ?></h3>
		<div class="wp-collapse-open clearfix">
			<a href="#" class="open-all-link" data-parent="wp-travel-tab-content-email" ><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
			<a href="#" class="close-all-link" data-parent="wp-travel-tab-content-email" style="display:none;" ><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
		</div>

		<div id="wp-travel-email-global-accordion" class="email-global-accordion tab-accordion wp-travel-accordion">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingOne">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
							<?php esc_html_e( 'Booking Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="wp-travel-email-template-options">

									<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="send_booking_email_to_admin"><?php esc_html_e( 'Send Email', 'wp-travel' ); ?></label>
											</th>
											<td>
												<span class="show-in-frontend checkbox-default-design">
													<label data-on="ON" data-off="OFF">
														<input value="no" name="send_booking_email_to_admin" type="hidden" />
														<input <?php checked( $send_booking_email_to_admin, 'yes' ); ?> value="yes" name="send_booking_email_to_admin" id="send_booking_email_to_admin" type="checkbox" />
														<span class="switch"></span>
													</label>
													<p class="description"><?php _e( 'Enable or disable Email notification to admin', 'wp-travel' ); ?></p>
												</span>
											</td>
										</tr>
									<?php do_action( 'wp_travel_utils_booking_notif' ); ?>
										<tr>
											<th>
												<label for="booking-admin-email-sub"><?php esc_html_e( 'Booking Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $booking_admin_email_settings['admin_subject']; ?>" type="text" name="booking_admin_template_settings[admin_subject]" id="booking-admin-email-sub" type="text">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-admin-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $booking_admin_email_settings['admin_title']; ?>" name="booking_admin_template_settings[admin_title]" id="booking-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-admin-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp-travel-color-picker-wrapper">
													<input class="wp-travel-color-field" value = "<?php echo $booking_admin_email_settings['admin_header_color']; ?>" type="text" name="booking_admin_template_settings[admin_header_color]" id="booking-admin-email-header-color">
												</div>
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $booking_admin_email_settings['email_content'] ) && '' !== $booking_admin_email_settings['email_content'] ? $booking_admin_email_settings['email_content'] : wp_travel_booking_admin_default_email_content();
												wp_editor( $content, 'booking_admin_email_content', $settings = array( 'textarea_name' => 'booking_admin_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

											<?php
											/**
											 * Add Support Multiple Booking admin Template.
											 */
											do_action( 'wp_travel_multiple_booking_admin_template_settings', $booking_admin_email_settings );
											?>

									</table>

									<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="booking-client-email-sub"><?php esc_html_e( 'Booking Client Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $booking_client_email_settings['client_subject']; ?>" type="text" name="booking_client_template_settings[client_subject]" id="booking-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-client-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $booking_client_email_settings['client_title']; ?>" name="booking_client_template_settings[client_title]" id="booking-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-client-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp-travel-color-picker-wrapper">
													<input class="wp-travel-color-field" value = "<?php echo $booking_client_email_settings['client_header_color']; ?>" type="text" name="booking_client_template_settings[client_header_color]" id="booking-client-email-header-color">
												</div>
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $booking_client_email_settings['email_content'] ) && '' !== $booking_client_email_settings['email_content'] ? $booking_client_email_settings['email_content'] : wp_travel_booking_client_default_email_content();
												wp_editor( $content, 'booking_client_email_content', $settings = array( 'textarea_name' => 'booking_client_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

											<?php
											/**
											 * Add Support Multiple Booking client Template.
											 */
											do_action( 'wp_travel_multiple_booking_client_template', $booking_client_email_settings );
											?>

									</table>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingTwo">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="true" aria-controls="collapseTwo">
									<?php esc_html_e( 'Payment Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="wp-travel-email-template-options">

								<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<?php do_action( 'wp_travel_utils_payment_notif' ); ?>
										<tr>
											<th>
												<label for="payment-admin-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_admin_email_settings['admin_subject']; ?>" type="text" name="payment_admin_template_settings[admin_subject]" id="payment-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_admin_email_settings['admin_title']; ?>" name="payment_admin_template_settings[admin_title]" id="payment-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp-travel-color-picker-wrapper">
													<input class="wp-travel-color-field" value = "<?php echo $payment_admin_email_settings['admin_header_color']; ?>" type="text" name="payment_admin_template_settings[admin_header_color]" id="payment-admin-email-header-color">
												</div>
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $payment_admin_email_settings['email_content'] ) && '' !== $payment_admin_email_settings['email_content'] ? $payment_admin_email_settings['email_content'] : wp_travel_payment_admin_default_email_content();
												wp_editor( $content, 'payment_admin_email_content', $settings = array( 'textarea_name' => 'payment_admin_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

											<?php
											/**
											 * Add Support Multiple payment admin Template.
											 */
											do_action( 'wp_travel_multiple_payment_admin_template', $payment_admin_email_settings );
											?>

									</table>

									<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="payment-client-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_client_email_settings['client_subject']; ?>" type="text" name="payment_client_template_settings[client_subject]" id="payment-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_client_email_settings['client_title']; ?>" name="payment_client_template_settings[client_title]" id="payment-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp-travel-color-picker-wrapper">
													<input class="wp-travel-color-field" value = "<?php echo $payment_client_email_settings['client_header_color']; ?>" type="text" name="payment_client_template_settings[client_header_color]" id="payment-client-email-header-color">
												</div>
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $payment_client_email_settings['email_content'] ) && '' !== $payment_client_email_settings['email_content'] ? $payment_client_email_settings['email_content'] : wp_travel_payment_client_default_email_content();
												wp_editor( $content, 'payment_client_email_content', $settings = array( 'textarea_name' => 'payment_client_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

											<?php
											/**
											 * Add Support Multiple Payment client Template.
											 */
											do_action( 'wp_travel_multiple_payment_client_template', $payment_client_email_settings );
											?>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingThree">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="true" aria-controls="collapseThree">
									<?php esc_html_e( 'Enquiry Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="wp-travel-email-template-options">

								<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<?php do_action( 'wp_travel_utils_enquiries_notif' ); ?>
										<tr>
											<th>
												<label for="enquiry-admin-email-sub"><?php esc_html_e( 'Enquiry Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $enquiry_admin_email_settings['admin_subject']; ?>" type="text" name="enquiry_admin_template_settings[admin_subject]" id="enquiry-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-title"><?php esc_html_e( 'Enquiry Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $enquiry_admin_email_settings['admin_title']; ?>" name="enquiry_admin_template_settings[admin_title]" id="enquiry-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-header-color"><?php esc_html_e( 'Enquiry Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp-travel-color-picker-wrapper">
													<input class="wp-travel-color-field" value = "<?php echo $enquiry_admin_email_settings['admin_header_color']; ?>" type="text" name="enquiry_admin_template_settings[admin_header_color]" id="enquiry-admin-email-header-color">
												</div>
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $enquiry_admin_email_settings['email_content'] ) && '' !== $enquiry_admin_email_settings['email_content'] ? $enquiry_admin_email_settings['email_content'] : wp_travel_enquiries_admin_default_email_content();
												wp_editor( $content, 'enquiry_admin_email_content', $settings = array( 'textarea_name' => 'enquiry_admin_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

									</table>

										<?php do_action( 'wp_travel_enquiry_customer_email_settings' ); ?>

								</div>

							</div>
						</div>
					</div>
				</div>

					<?php
					// @since 1.8.0
					do_action( 'wp_travel_email_template_settings_after_enquiry', $tab, $args )
					?>
			</div>
		</div>
			<?php
}
