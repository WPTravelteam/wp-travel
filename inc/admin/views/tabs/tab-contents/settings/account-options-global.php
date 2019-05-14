<?php
/**
 * Callback for Account Settings Tab.
 *
 * @param Array $tab List of tabs.
 * @param Array $args Settings arg List.
 */
function wp_travel_settings_callback_account_options_global( $tab, $args ) {

	$settings                                = $args['settings'];
	$enable_checkout_customer_registration   = $settings['enable_checkout_customer_registration'];
	$enable_my_account_customer_registration = $settings['enable_my_account_customer_registration'];
	$generate_username_from_email            = $settings['generate_username_from_email'];
	$generate_user_password                  = $settings['generate_user_password'];
	?>
	<table class="form-table">
		<tr>
			<th>
				<label for="currency"><?php esc_html_e( 'Customer Registration', 'wp-travel' ); ?></label>
			</th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="enable_checkout_customer_registration" type="hidden" />
						<input <?php checked( $enable_checkout_customer_registration, 'yes' ); ?> value="yes" name="enable_checkout_customer_registration" id="enable_checkout_customer_registration" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="enable_checkout_customer_registration"><?php echo esc_html__( 'Require Customer login or register before booking.', 'wp-travel' ); ?></label></p>
			</td>
			</tr>
			<tr>
			<th></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="enable_my_account_customer_registration" type="hidden" />
						<input <?php checked( $enable_my_account_customer_registration, 'yes' ); ?> value="yes" name="enable_my_account_customer_registration" id="enable_my_account_customer_registration" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="enable_my_account_customer_registration"><?php echo esc_html__( 'Enable customer registration on the "My Account" page.', 'wp-travel' ); ?></label></p>
			</td>
		<tr>
		<tr>
			<th>
				<label for="currency"><?php esc_html_e( 'Account Creation', 'wp-travel' ); ?></label>
			</th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="generate_username_from_email" type="hidden" />
						<input <?php checked( $generate_username_from_email, 'yes' ); ?> value="yes" name="generate_username_from_email" id="generate_username_from_email" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="generate_username_from_email"><?php echo esc_html__( ' Automatically generate username from customer email.', 'wp-travel' ); ?></label></p>
			</td>
			</tr>
			<tr>
			<th></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="generate_user_password" type="hidden" />
						<input <?php checked( $generate_user_password, 'yes' ); ?> value="yes" name="generate_user_password" id="generate_user_password" type="checkbox" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="generate_user_password"><?php echo esc_html__( ' Automatically generate customer password', 'wp-travel' ); ?></label></p>
			</td>
		</tr>
	</table>
	<?php
}
