<?php
/**
 * Bank deposit Settings.
 *
 * @package wp-travel/inc/gateways/bank-deposit
 */

function wp_travel_bank_deposit_default_settings_fields( $settings ) {
	$settings['payment_option_bank_deposit'] = 'no';
	$settings['wp_travel_bank_deposits']      = array();
	$settings['wp_travel_bank_deposit_description'] = '';
	// $settings['wp_travel_bank_deposit_allowed_file'] = 'jpg, png';
	return $settings;
}

add_filter( 'wp_travel_settings_fields', 'wp_travel_bank_deposit_default_settings_fields' );


 /**
  * Bank deposit Settings HTML.
  *
  * @param Array $args Arguments.
  */
function wp_travel_settings_bank_deposit( $args ) {
	if ( ! $args ) {
		return;
	}
	$settings = $args['settings'];

	$payment_option_bank_deposit = isset( $settings['payment_option_bank_deposit'] ) ? $settings['payment_option_bank_deposit'] : 'no';
	$wp_travel_bank_deposit_description = isset( $settings['wp_travel_bank_deposit_description'] ) ? $settings['wp_travel_bank_deposit_description'] : '';
	
	$field_style = ( 'yes' === $payment_option_bank_deposit ) ? 'display:table-row-group' : 'display:none';

	?>
	<table class="form-table wp-travel-enable-payment-wrapper bank-deposite">
			
		<tr >
			<th><label for="payment_option_bank_deposit"><?php esc_html_e( 'Enable ', 'wp-travel' ); ?></label></th>
			<td>
				<label for="payment_option_bank_deposit">
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
						<input type="hidden" value="no" name="payment_option_bank_deposit" />
						<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_bank_deposit ); ?> name="payment_option_bank_deposit" id="payment_option_bank_deposit" class="wp-travel-enable-payment" />
							<span class="switch">
							</span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( 'Check to enable Bank deposit.', 'wp-travel' ); ?></p>
				</label>
			</td>
		</tr>

		
		<tbody class="wp-travel-enable-payment-body" style="<?php echo esc_attr( $field_style ); ?>">
			
			<tr >
				<th><label for="wp_travel_bank_deposit_description"><?php esc_html_e( 'Description ', 'wp-travel' ); ?></label></th>
				<td>
					<textarea name="wp_travel_bank_deposit_description" id="wp_travel_bank_deposit_description" cols="30" rows="10"><?php echo esc_html( $wp_travel_bank_deposit_description ); ?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<h4>
						<label for=""><?php esc_html_e( 'Account Detail', 'wp-travel' ); ?></label>
					</h4>
					<table class="wp-travel-account-detail widefat">
						<thead>
							<tr>
								<th></td>
								<th><?php esc_html_e( 'Account Name', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Account Number', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Bank Name', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Sort Code', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'IBAN', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'BIC/Swift', 'wp-travel' ); ?></th>
								<th colspan="2"><?php esc_html_e( 'Action', 'wp-travel' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$bank_deposits = $settings['wp_travel_bank_deposits'];
							if ( isset( $bank_deposits['account_name'] ) && is_array( $bank_deposits['account_name'] ) && count( $bank_deposits['account_name'] ) > 0 ) {
								foreach ( $bank_deposits['account_name'] as $i => $account_name ) {
									$account_number = isset( $bank_deposits['account_number'][ $i ] ) ? $bank_deposits['account_number'][ $i ] : '';
									$bank_name      = isset( $bank_deposits['bank_name'][ $i ] ) ? $bank_deposits['bank_name'][ $i ] : '';
									$sort_code      = isset( $bank_deposits['sort_code'][ $i ] ) ? $bank_deposits['sort_code'][ $i ] : '';
									$iban           = isset( $bank_deposits['iban'][ $i ] ) ? $bank_deposits['iban'][ $i ] : '';
									$swift          = isset( $bank_deposits['swift'][ $i ] ) ? $bank_deposits['swift'][ $i ] : '';
									$enable         = isset( $bank_deposits['enable'][ $i ] ) ? $bank_deposits['enable'][ $i ] : 'no';
									?>
									<tr data-index="<?php echo esc_attr( $i ); ?>">
										<td><div class="wp-travel-sorting-handle"></div></td>
										<td>
											<input type="text" name="wp_travel_bank_deposits[account_name][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_account_name" value="<?php echo esc_attr( $account_name ); ?>" >
										</td>

										<td>
											<input type="text" name="wp_travel_bank_deposits[account_number][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_account_number" value="<?php echo esc_attr( $account_number ); ?>">
										</td>

										<td>
											<input type="text" name="wp_travel_bank_deposits[bank_name][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_bank_name" value="<?php echo esc_attr( $bank_name ); ?>">
										</td>

										<td>
											<input type="text" name="wp_travel_bank_deposits[sort_code][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_sort_code" value="<?php echo esc_attr( $sort_code ); ?>">
										</td>

										<td>
											<input type="text" name="wp_travel_bank_deposits[iban][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_iban" value="<?php echo esc_attr( $iban ); ?>">
										</td>

										<td>
											<input type="text" name="wp_travel_bank_deposits[swift][<?php echo esc_attr( $i ); ?>]" class="wp_travel_bank_deposit_swift" value="<?php echo esc_attr( $swift ); ?>">
										</td>
										<td>
											<span class="show-in-frontend checkbox-default-design">
												<label data-on="ON" data-off="OFF">
													<input value="no" name="wp_travel_bank_deposits[enable][<?php echo esc_attr( $i ); ?>]" type="hidden" />
													<input type="checkbox" value="yes" <?php checked( 'yes', $enable ); ?> name="wp_travel_bank_deposits[enable][<?php echo esc_attr( $i ); ?>]" />
													<span class="switch">
												</span>
												</label>
											</span>
										</td>
										<td class="align-center">
											<a href="#" class="wp-travel-close-row">X</a>
										</td>
									</tr>
									<?php
								}
							}
							?>
							
						</tbody>
					</table>
					<button class="button btn-add-new-bank-deposite">Add New</button>
					
				</td>
			</tr>
		
		</tbody>
		
	</table>
	<style>
	.wp-travel-enable-payment-wrapper.bank-deposite td input{
		width:120px;
	}
	.wp-travel-close-row{
		display: block;
		position:absolute;
		right:0;
		z-index:11;
		padding: 10px;
	}
	</style>
	<script type="text/html" id="tmpl-add-new-bank-deposite">
		<tr data-index="{{data.index}}">
			<td><div class="wp-travel-sorting-handle"></div></td>
			<td>
				<input type="text" name="wp_travel_bank_deposits[account_name][{{data.index}}]" class="wp_travel_bank_deposit_account_name" value="" >
			</td>

			<td>
				<input type="text" name="wp_travel_bank_deposits[account_number][{{data.index}}]" class="wp_travel_bank_deposit_account_number" value="">
			</td>

			<td>
				<input type="text" name="wp_travel_bank_deposits[bank_name][{{data.index}}]" class="wp_travel_bank_deposit_bank_name" value="">
			</td>

			<td>
				<input type="text" name="wp_travel_bank_deposits[sort_code][{{data.index}}]" class="wp_travel_bank_deposit_sort_code" value="">
			</td>

			<td>
				<input type="text" name="wp_travel_bank_deposits[iban][{{data.index}}]" class="wp_travel_bank_deposit_iban" value="">
			</td>

			<td>
				<input type="text" name="wp_travel_bank_deposits[swift][{{data.index}}]" class="wp_travel_bank_deposit_swift" value="">
			</td>
			<td class="align-center">
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="wp_travel_bank_deposits[enable][{{data.index}}]" type="hidden" />
						<input type="checkbox" value="yes" name="wp_travel_bank_deposits[enable][{{data.index}}]" />
						<span class="switch">
					</span>
					</label>
				</span>
			</td>
			<td class="align-center">
				<a href="#" class="wp-travel-close-row">X</a>
			</td>
		</tr>
	</script>
	<script>
		jQuery(document).ready( function($){
			$(document).on( 'click', '.btn-add-new-bank-deposite', function(e) {
				e.preventDefault();
				var new_index = 0;
				$.each( $(this).siblings('.wp-travel-account-detail').find('tbody tr'), function(){
					if ( $(this).data('index') > new_index ) {
						new_index = $(this).data('index');
					}
				} );
				new_index += 1;

				var template = wp.template('add-new-bank-deposite');
				$(this).siblings('.wp-travel-account-detail').find('tbody').append(template({index : new_index}));
			} );

			$( document ).on( 'click', '.wp-travel-close-row', function( e ) {
				e.preventDefault();
				var y = confirm( 'Are you sure you want to remove?' );
				if ( y ) {
					$(this).closest( 'tr' ).fadeOut().remove();
				}

			} );
		} );
	</script>
	<?php
}

 add_action( 'wp_travel_payment_gateway_fields_bank_deposit', 'wp_travel_settings_bank_deposit' );
 