<?php
/**
 * Bank Deposit Settings.
 *
 * @package wp-travel/inc/gateways/bank-deposit
 */

function wp_travel_bank_deposit_default_settings_fields( $settings ) {
	$settings['payment_option_bank_deposit'] = 'no';
	return $settings;
}

add_filter( 'wp_travel_settings_fields', 'wp_travel_bank_deposit_default_settings_fields' );

 /**
  * Bank Deposit Settings HTML.
  *
  * @param Array $args Arguments.
  */
function wp_travel_settings_bank_deposit( $args ) {
	if ( ! $args ) {
		return;
	}
	$settings = $args['settings'];

	$payment_option_bank_deposit = isset( $settings['payment_option_bank_deposit'] ) ? $settings['payment_option_bank_deposit'] : 'no';

	?>
	<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Bank Deposit Settings', 'wp-travel' ); ?></h3>
	<table class="form-table wp-travel-enable-payment-wrapper">

		<tr >
			<th><label for="payment_option_bank_deposit"><?php esc_html_e( 'Enable ', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
					<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_bank_deposit ); ?> name="payment_option_bank_deposit" id="payment_option_bank_deposit" class="wp-travel-enable-payment" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="payment_option_bank_deposit"><?php esc_html_e( 'Check to enable Bank Deposit.', 'wp-travel' ); ?></label></p>
			</td>
		</tr>
	</table>

	<?php
}

 add_action( 'wp_travel_payment_gateway_fields_bank_deposit', 'wp_travel_settings_bank_deposit' );
