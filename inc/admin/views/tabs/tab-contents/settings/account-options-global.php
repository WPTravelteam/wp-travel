<?php
/**
 * Callback for Account Settings Tab.
 *
 * @param Array $tab List of tabs.
 * @param Array $args Settings arg List.
 */
function settings_callback_account_options_global( $tab, $args ) {

	$settings                                = $args['settings'];
	$enable_checkout_customer_registration   = $settings['enable_checkout_customer_registration'];
	$enable_my_account_customer_registration = $settings['enable_my_account_customer_registration'];
	$generate_username_from_email            = $settings['generate_username_from_email'];
	$generate_user_password                  = $settings['generate_user_password'];
	?>

	<div class="form_field">
		<label class="label_title" for="enable_checkout_customer_registration"><?php esc_html_e( 'Require login to book', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="enable_checkout_customer_registration" type="hidden" />
				<input <?php checked( $enable_checkout_customer_registration, 'yes' ); ?> value="yes" name="enable_checkout_customer_registration" id="enable_checkout_customer_registration" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="enable_checkout_customer_registration">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="enable_checkout_customer_registration"><?php esc_html_e( sprintf( 'Require Customer login before booking.' ), 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field">
		<label class="label_title" for="enable_my_account_customer_registration"><?php esc_html_e( 'Enable Customer Registragion', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="enable_my_account_customer_registration" type="hidden" />
				<input <?php checked( $enable_my_account_customer_registration, 'yes' ); ?> value="yes" name="enable_my_account_customer_registration" id="enable_my_account_customer_registration" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="enable_my_account_customer_registration">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="enable_my_account_customer_registration"><?php echo esc_html__( 'Enable customer registration on the "My Account" page.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field">
		<label class="label_title" for="generate_username_from_email"><?php esc_html_e( 'Generate Username on Registragion', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="generate_username_from_email" type="hidden" />
				<input <?php checked( $generate_username_from_email, 'yes' ); ?> value="yes" name="generate_username_from_email" id="generate_username_from_email" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="generate_username_from_email">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="generate_username_from_email"><?php echo esc_html__( ' Automatically generate username from customer email.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field">
		<label class="label_title" for="generate_user_password"><?php esc_html_e( 'Generate Password on Registragion', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="generate_user_password" type="hidden" />
				<input <?php checked( $generate_user_password, 'yes' ); ?> value="yes" name="generate_user_password" id="generate_user_password" type="checkbox" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="generate_user_password">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="generate_user_password"><?php echo esc_html__( ' Automatically generate customer password', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>	
	<?php
}
