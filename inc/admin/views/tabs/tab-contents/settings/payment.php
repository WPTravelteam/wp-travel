<?php
/**
 * Callback for Payment tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function settings_callback_payment( $tab, $args ) {
	$settings = $args['settings'];

	$partial_payment          = $settings['partial_payment'];
	$minimum_partial_payout   = $settings['minimum_partial_payout'];
	$paypal_email             = $settings['paypal_email'];
	$payment_option_paypal    = $settings['payment_option_paypal'];
	$trip_tax_enable          = $settings['trip_tax_enable'];
	$trip_tax_percentage      = $settings['trip_tax_percentage'];
	$trip_tax_price_inclusive = $settings['trip_tax_price_inclusive'];
	?>

	<div class="form_field">
		<label class="label_title" for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="partial_payment" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $partial_payment ); ?> name="partial_payment" id="partial_payment" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="partial_payment">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="partial_payment"><?php esc_html_e( 'Enable partial payment while booking.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>
	
	<div class="form_field" id="wp-travel-minimum-partial-payout">
		<label class="label_title" for="minimum_partial_payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="range" min="1" max="100" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout" class="wt-slider" />
			
			<label><input type="number" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout_output" />%</label>

			<figcaption><label for="minimum_partial_payout_output"><?php esc_html_e( 'Minimum percent of amount to pay while booking.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<?php do_action( 'wp_travel_payment_gateway_fields', $args ); ?>
	
	<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Standard Paypal', 'wp-travel' ); ?></h3>
	
	<div class="form_field">
		<label class="label_title" for="payment_option_paypal"><?php esc_html_e( 'Enable Paypal', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
			<input value="no" name="payment_option_paypal" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_paypal ); ?> name="payment_option_paypal" id="payment_option_paypal" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="payment_option_paypal">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="payment_option_paypal"><?php esc_html_e( 'Check to enable standard PayPal payment.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field" id="wp-travel-paypal-email" >
		<label class="label_title" for="paypal_email"><?php esc_html_e( 'Paypal Email', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="text" value="<?php echo esc_attr( $paypal_email ); ?>" name="paypal_email" id="paypal_email"/>

			<figcaption><label for="paypal_email"><?php esc_html_e( 'Check to enable standard PayPal payment.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>
	<?php
		$args = array(
			'title'      => __( 'Need more payment gateway options ?', 'wp-travel' ),
			'content'    => '',
			'link'       => 'http://wptravel.io/downloads/',
			'link_label'  => __( 'Check All Payment Gateways', 'wp-travel' ),
			'link2'      => 'http://wptravel.io/contact',
			'link2_label' => __( 'Request a new one', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	?>
	
	
	<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'TAX Options', 'wp-travel' ); ?></h3>

	<div class="form_field">
		<label class="label_title" for="trip_tax_enable"><?php esc_html_e( 'Enable Tax for Trip Price', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="trip_tax_enable" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $trip_tax_enable ); ?> name="trip_tax_enable" id="trip_tax_enable" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="trip_tax_enable">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption><label for="trip_tax_enable"><?php esc_html_e( 'Check to enable Tax options for trips.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<!-- <div class="form_field" id="wp-travel-tax-price-options">
		<label class="label_title" for=""><?php esc_html_e( 'Trip prices entered with tax', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<label><input <?php checked( 'yes', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="yes" type="radio">
				<?php esc_html_e( 'Yes, I will enter trip prices inclusive of tax', 'wp-travel' ); ?></label>

			<label> <input <?php checked( 'no', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="no" type="radio">
			<?php esc_html_e( 'No, I will enter trip prices exclusive of tax', 'wp-travel' ); ?></label>
				
			<figcaption><?php esc_html_e( 'This option will affect how you enter trip prices.', 'wp-travel' ); ?></figcaption>
		</div>
	</div> -->
	
	<!-- <div class="form_field" id="wp-travel-tax-percentage" <?php echo 'yes' == $trip_tax_price_inclusive ? 'style="display:none;"' : 'style="display:table-row;"'; ?> >
		<label class="label_title" for="trip_tax_percentage_output"><?php esc_html_e( 'Tax Percentage (%)', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="number" min="0" max="100" step="0.01" value="<?php echo esc_attr( $trip_tax_percentage ); ?>" name="trip_tax_percentage" id="trip_tax_percentage_output" />
			<figcaption><label for="trip_tax_percentage_output"><?php esc_html_e( 'Trip Tax percentage added to trip price.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div> -->

	<div class="form_field" id="wp-travel-tax-percentage">
	<label class="label_title" for="trip_tax_percentage_output"><?php esc_html_e( 'Tax Percentage (%)', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="wp-radio">
				<div class="radio">
						<input id="radio-1" name="radio" type="radio" checked />
						<label for="radio-1" class="radio-label">Checked</label>
					</div>

					<div class="radio">
						<input id="radio-2" name="radio" type="radio" />
						<label for="radio-2" class="radio-label">Unchecked</label>
					</div>
					</div>
		</div>
	</div>
	<?php
}
