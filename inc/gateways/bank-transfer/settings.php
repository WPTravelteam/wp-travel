<?php
/**
 * Bank Transfer Settings.
 *
 * @package wp-travel/inc/gateways/bank-transfer
 */

function wp_travel_bank_transfer_default_settings_fields( $settings ) {
	$settings['payment_option_bank_transfer'] = 'no';
	$settings['wp_travel_bank_transfer']      = array();
	return $settings;
}

add_filter( 'wp_travel_settings_fields', 'wp_travel_bank_transfer_default_settings_fields' );


 /**
  * Bank Transfer Settings HTML.
  *
  * @param Array $args Arguments.
  */
function wp_travel_settings_bank_transfer( $args ) {
	if ( ! $args ) {
		return;
	}
	$settings = $args['settings'];

	$payment_option_bank_transfer = isset( $settings['payment_option_bank_transfer'] ) ? $settings['payment_option_bank_transfer'] : 'no';

	$bank_transfer = $settings['wp_travel_bank_transfer'];

	// dd( $bank_transfer );

	$account_name   = isset( $bank_transfer['account_name'] ) ? $bank_transfer['account_name'] : '';
	$account_number = isset( $bank_transfer['account_number'] ) ? $bank_transfer['account_number'] : '';
	$bank_name      = isset( $bank_transfer['bank_name'] ) ? $bank_transfer['bank_name'] : '';
	$sort_code      = isset( $bank_transfer['sort_code'] ) ? $bank_transfer['sort_code'] : '';
	$iban           = isset( $bank_transfer['iban'] ) ? $bank_transfer['iban'] : '';
	$swift          = isset( $bank_transfer['swift'] ) ? $bank_transfer['swift'] : '';

	$field_style = ( 'yes' === $payment_option_bank_transfer ) ? 'display:table-row-group' : 'display:none';

	?>
	<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Bank Transfer Settings', 'wp-travel' ); ?></h3>
	<table class="form-table wp-travel-enable-payment-wrapper">
			
		<tr >
			<th><label for="payment_option_bank_transfer"><?php esc_html_e( 'Enable ', 'wp-travel' ); ?></label></th>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
					<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_bank_transfer ); ?> name="payment_option_bank_transfer" id="payment_option_bank_transfer" class="wp-travel-enable-payment" />
						<span class="switch">
						</span>
					</label>
				</span>
				<p class="description"><label for="payment_option_bank_transfer"><?php esc_html_e( 'Check to enable Bank Transfer.', 'wp-travel' ); ?></label></p>
			</td>
		</tr>

		
		<tbody class="wp-travel-enable-payment-body" style="<?php echo esc_attr( $field_style ); ?>">
			<tr>
				<td colspan="2">
					<h4>
						<label for=""><?php esc_html_e( 'Account Detail', 'wp-travel' ); ?></label>
					</h4>
				</td>
			</tr>
			
			<tr>
				<td colspan="2">
					<table>
						<thead>
							<tr>
								<td></td>
								<td><?php esc_html_e( 'Account Name' ); ?></td>
								<td><?php esc_html_e( 'Account Number' ); ?></td>
								<td><?php esc_html_e( 'Bank Name' ); ?></td>
								<td><?php esc_html_e( 'Sort Code' ); ?></td>
								<td><?php esc_html_e( 'IBAN' ); ?></td>
								<td><?php esc_html_e( 'BIC/Swift' ); ?></td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><div class="wp-travel-sorting-handle"></div></td>
								<td>
									<input type="text" name="wp_travel_bank_transfer[account_name]" id="wp_travel_bank_transfer_account_name" value="<?php echo esc_attr( $account_name ); ?>" >
								</td>

								<td>
									<input type="text" name="wp_travel_bank_transfer[account_number]" id="wp_travel_bank_transfer_account_number" value="<?php echo esc_attr( $account_number ); ?>">
								</td>

								<td>
									<input type="text" name="wp_travel_bank_transfer[bank_name]" id="wp_travel_bank_transfer_bank_name" value="<?php echo esc_attr( $bank_name ); ?>">
								</td>

								<td>
									<input type="text" name="wp_travel_bank_transfer[sort_code]" id="wp_travel_bank_transfer_sort_code" value="<?php echo esc_attr( $sort_code ); ?>">
								</td>

								<td>
									<input type="text" name="wp_travel_bank_transfer[iban]" id="wp_travel_bank_transfer_iban" value="<?php echo esc_attr( $iban ); ?>">
								</td>

								<td>
									<input type="text" name="wp_travel_bank_transfer[swift]" id="wp_travel_bank_transfer_swift" value="<?php echo esc_attr( $swift ); ?>">
								</td>
								
							</tr>
							
						</tbody>
					</table>
					
				</td>
			</tr>
		
		</tbody>
		
	</table>
	<script type="text/javascript">
		
		jQuery(document).ready(function($) {
			
		});
	</script>
	<?php
}

 add_action( 'wp_travel_payment_gateway_fields_bank_transfer', 'wp_travel_settings_bank_transfer' );
