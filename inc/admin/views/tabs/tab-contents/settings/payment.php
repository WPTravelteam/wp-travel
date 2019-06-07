<?php
/**
 * Callback for Payment tab.
 *
 * @param  Array $tab  List of tabs.
 * @param  Array $args Settings arg list.
 */
function wp_travel_settings_callback_payment( $tab, $args ) {
	$settings = $args['settings'];

	$partial_payment          = $settings['partial_payment'];
	$minimum_partial_payout   = $settings['minimum_partial_payout'];
	$trip_tax_enable          = $settings['trip_tax_enable'];
	$trip_tax_percentage      = $settings['trip_tax_percentage'];
	$trip_tax_price_inclusive = $settings['trip_tax_price_inclusive'];
	?>

	<table class="form-table">
		<tr>
			<th><label for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ); ?></label></th>
			<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input value="no" name="partial_payment" type="hidden" />
					<input type="checkbox" value="yes" <?php checked( 'yes', $partial_payment ); ?> name="partial_payment" id="partial_payment"/>
					<span class="switch">
				</span>

				</label>
			</span>
				<p class="description"><?php esc_html_e( 'Enable Partial Payment while booking.', 'wp-travel' ); ?>
				</p>
			</td>
		</tr>
		<tr id="wp-travel-minimum-partial-payout">
			<th><label for="minimum_partial_payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label></th>
			<td>
				<input type="range" min="1" max="100" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout" class="wt-slider" />
				<label><input type="number" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout_output" />%</label>
				<p class="description"><?php esc_html_e( 'Minimum percent of amount to pay while booking.', 'wp-travel' ); ?></p>
			</td>
		</tr>
	</table>


	<?php
		wp_travel_do_deprecated_action( 'wp_travel_payment_gateway_fields', array( $args ), '2.0.4', 'wp_travel_payment_gateway_fields_{$gateway }' );

		// @since 2.0.0
		$sorted_gateways = wp_travel_sorted_payment_gateway_lists();
		// Sorting.
		if ( is_array( $sorted_gateways ) && count( $sorted_gateways ) > 0 ) : ?>
			<h3 class="wp-travel-section-title"><?php esc_html_e( 'Payment Gateways', 'wp-travel' ); ?></h3>
			<div class="tab-accordion main-setting wp-travel-accordion has-handler" id="wp-travel-payment-accordion">
				<div class="panel-group wp-travel-sorting-tabs wp-travel-sorting-tabs"  role="tablist" aria-multiselectable="true" >
				<?php foreach ( $sorted_gateways as $gateway => $gateway_label ) : ?>
					<div class="panel panel-default">
						<div class="panel-heading"  role="tab" id="heading-<?php echo esc_attr( $gateway ); ?>">
							<h4 class="panel-title">
								<div class="wp-travel-sorting-handle"></div>
								<a role="button" data-toggle="collapse" data-parent="#wp-travel-payment-accordion" href="#collapse-<?php echo esc_attr( $gateway ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $gateway ); ?>">
								<?php echo $gateway_label ? esc_html( $gateway_label ) : __( 'Payment', 'wp-travel' ); ?>
								</a>
							</h4>
						</div>
						<div id="collapse-<?php echo esc_attr( $gateway ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $gateway ); ?>">

							<div class="panel-body">
								<div class="panel-wrap">
									<?php do_action( 'wp_travel_payment_gateway_fields_' . $gateway, $args ); ?>
								</div>
							</div>
						</div>
					</div>
					<input type="hidden" name="sorted_gateways[]" value="<?php echo esc_attr( $gateway ); ?>" >
				<?php endforeach; ?>
				</div>
			</div>
		<?php endif; ?>
	

	<br>
	<?php
		$args = array(
			'title'       => __( 'Need more payment gateway options ?', 'wp-travel' ),
			'content'     => '',
			'link'       => 'https://wptravel.io/wp-travel-pro/',
        	'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'        => 'https://wptravel.io/downloads/category/payment-gateways/',
			'link2_label'  => __( 'Check All Payment Gateways', 'wp-travel' ),
			'link3'       => 'http://wptravel.io/contact',
			'link3_label' => __( 'Request a new one', 'wp-travel' ),
		);

		if ( class_exists( 'WP_Travel_Pro' ) ) {
			$args['link'] = $args['link2'];
			$args['link_label'] = $args['link2_label'];
			unset( $args['link2'], $args['link2_label'] );
		}
		wp_travel_upsell_message( $args );
	?>
	<br>
	<table class="form-table">
		<tr>
			<th colspan="2">
				<h3><?php esc_html_e( 'TAX Options', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<tr>
			<th><label for="trip_tax_enable"><?php esc_html_e( 'Enable Tax', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input value="no" name="trip_tax_enable" type="hidden" />
					<input type="checkbox" value="yes" <?php checked( 'yes', $trip_tax_enable ); ?> name="trip_tax_enable" id="trip_tax_enable"/>
					<span class="switch">
				</span>

				</label>
			</span>
				<p class="description"><?php esc_html_e( 'Check to enable Tax options for trips.', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tr id="wp-travel-tax-price-options" >
			<th><label><?php esc_html_e( 'Tax on Trip prices', 'wp-travel' ); ?></label></th>
			<td>
				<label><input <?php checked( 'yes', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="yes" type="radio">
				<?php esc_html_e( 'Yes, I will enter trip prices inclusive of tax', 'wp-travel' ); ?></label>

				<label> <input <?php checked( 'no', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="no" type="radio">
				<?php esc_html_e( 'No, I will enter trip prices exclusive of tax', 'wp-travel' ); ?></label>

				<p class="description"><?php esc_html_e( 'This option will affect how you enter trip prices.', 'wp-travel' ); ?></p>

			</td>
		</tr>
		<tr id="wp-travel-tax-percentage" <?php echo 'yes' == $trip_tax_price_inclusive ? 'style="display:none;"' : 'style="display:table-row;"'; ?> >
			<th><label for="trip_tax_percentage_output"><?php esc_html_e( 'Tax Percentage', 'wp-travel' ); ?></label></th>
			<td>

				<label><input type="number" min="0" max="100" step="0.01" value="<?php echo esc_attr( $trip_tax_percentage ); ?>" name="trip_tax_percentage" id="trip_tax_percentage_output" />%</label>
				<p class="description"><?php esc_html_e( 'Trip Tax percentage added to trip price.', 'wp-travel' ); ?></p>

			</td>
		</tr>
	</table>
	<script type="text/javascript" defer>
		const payment_option_change = function(){
			var gateway_fields = jQuery(this).closest('.form-table').find('.payment-gateway-fields');
			jQuery(this).is(':checked') ? gateway_fields.fadeIn(): gateway_fields.fadeOut() ;
		}
		jQuery(document).ready(function($) {
			$('.enable-payment-gateway').on( 'click change', payment_option_change );
			$('.enable-payment-gateway').trigger( 'change' );
		});
	</script>
	<?php
}

function wp_travel_standard_paypal_settings_callback( $args ) {

	$settings = $args['settings'];

	$paypal_email             = $settings['paypal_email'];
	$payment_option_paypal    = $settings['payment_option_paypal'];
	?>
	<table class="form-table form-table-payment">
		
		<tr>
			<th><label for="payment_option_paypal"><?php esc_html_e( 'Enable Paypal', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
				<input value="no" name="payment_option_paypal" type="hidden" />
					<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_paypal ); ?> name="payment_option_paypal" id="payment_option_paypal" class="enable-payment-gateway" />
					<span class="switch">
				</span>

				</label>
			</span>
				<p class="description"><?php esc_html_e( 'Check to enable Standard PayPal payment.', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tbody class="payment-gateway-fields">
		
			<tr id="wp-travel-paypal-email" >
				<th><label for="paypal_email"><?php esc_html_e( 'Paypal Email', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $paypal_email ); ?>" name="paypal_email" id="paypal_email"/>
					<p class="description"><?php esc_html_e( 'PayPal email address that receive payment.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}

add_action( 'wp_travel_payment_gateway_fields_paypal', 'wp_travel_standard_paypal_settings_callback' );
