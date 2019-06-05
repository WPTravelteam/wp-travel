<?php
/**
 * Tour extras General Tab Contents
 *
 * @package WP Travel
 */

function wp_travel_coupons_general_tab_callback() {

	global $post;
	// General Tab Data.
	$coupon_metas = get_post_meta( $post->ID, 'wp_travel_coupon_metas', true );
	$general_tab  = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
	$coupon_code  = get_post_meta( $post->ID, 'wp_travel_coupon_code', true );
	// Field Values.
	$coupon_active      = isset( $general_tab['coupon_active'] ) ? $general_tab['coupon_active'] : 'yes';
	$coupon_code        = ! empty( $coupon_code ) ? $coupon_code : '';
	$coupon_type        = isset( $general_tab['coupon_type'] ) ? $general_tab['coupon_type'] : 'fixed';
	$coupon_value       = isset( $general_tab['coupon_value'] ) ? $general_tab['coupon_value'] : '';
	$coupon_expiry_date = isset( $general_tab['coupon_expiry_date'] ) ? $general_tab['coupon_expiry_date'] : '';

	$date_format    = get_option( 'date_format' );
	$js_date_format = wp_travel_date_format_php_to_js();

	$old_date_format = 'm/d/Y';
	if ( ! empty( $coupon_expiry_date ) && ! wp_travel_is_ymd_date( $coupon_expiry_date ) ) {
		$coupon_expiry_date = wp_travel_format_ymd_date( $coupon_expiry_date, $old_date_format );
	}

	$coupon    = new WP_Travel_Coupon();
	$coupon_id = $coupon->get_coupon_id_by_code( $coupon_code );

	?>

	<table class="form-table">
		<tbody>
		<?php if ( $coupon_id ) : ?>
				<tr>
					<td>
						<label for="currency"><?php esc_html_e( 'Coupon Status ', 'wp-travel' ); ?></label>
					</td>
					<td>
					<?php

						$coupon_status = $coupon->get_coupon_status( $coupon_id );

					if ( 'active' === $coupon_status ) {
						?>

								<span class="wp-travel-info-msg">
								<?php echo esc_html( 'Active', 'wp-travel' ); ?>
								</span>

							<?php

					} else {

						?>

								<span class="wp-travel-error-msg">
								<?php echo esc_html( 'Expired', 'wp-travel' ); ?>
								</span>

							<?php

					}

					?>
					</td>
				<tr>
			<?php endif; ?>
			<tr>
				<td>
					<label for="coupon-code"><?php esc_html_e( 'Coupon Code', 'wp-travel' ); ?></label>
					<span class="tooltip-area" title="<?php esc_html_e( 'Unique Identifier for the coupon.', 'wp-travel' ); ?>">
						<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					</span>
				</td>
				<td>
					<input required="required" type="text" id="coupon-code" name="wp_travel_coupon_code" placeholder="<?php echo esc_attr__( 'WP-TRAVEL-350', 'wp-travel' ); ?>" value="<?php echo esc_attr( $coupon_code ); ?>">
					<input id="wp-travel-coupon-id" type="hidden" value="<?php echo esc_attr( $coupon_id ); ?>">
				</td>
			</tr>
			<tr id="wp-travel-coupon_code-error" style="display:none;">
				<td colspan="2"><span class="wp-travel-error"><strong><?php echo esc_html( 'Error :', 'wp-travel' ); ?></strong><?php esc_html_e( 'Coupon Code already used. Please choose a unique coupon code', 'wp-travel' ); ?></span></td>
			</tr>
			<tr>
				<td><label for="coupon-type"><?php esc_html_e( 'Coupon Type', 'wp-travel' ); ?></label>
					<span class="tooltip-area" title="<?php esc_html_e( 'Coupon Type: Fixed Discount Amount or Percentage discount( Applies to cart total price ).', 'wp-travel' ); ?>">
						<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					</span>
				</td>
				<td>
					<select id="coupon-type" name="wp_travel_coupon[general][coupon_type]">
						<option value="fixed" <?php selected( $coupon_type, 'fixed' ); ?>><?php esc_html_e( 'Fixed Discount', 'wp-travel' ); ?></option>
						<option value="percentage" <?php selected( $coupon_type, 'percentage' ); ?>><?php esc_html_e( 'Percentage Discount', 'wp-travel' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="coupon-value"><?php esc_html_e( 'Coupon Value', 'wp-travel' ); ?></label>
					<span class="tooltip-area" title="<?php esc_html_e( 'Coupon value amount/percentage', 'wp-travel' ); ?>">
						<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					</span>
				</td>
				<td>
					<input required="required" type="number" min="1" <?php echo 'percentage' === $coupon_type ? 'max="100"' : ''; ?> step="0.01" id="coupon-value" name="wp_travel_coupon[general][coupon_value]" placeholder="<?php echo esc_attr__( 'Coupon Value', 'wp-travel' ); ?>" value="<?php echo esc_attr( $coupon_value ); ?>">
					<span <?php echo 'percentage' === $coupon_type ? 'style="display:none;"' : ''; ?> id="coupon-currency-symbol" class="wp-travel-currency-symbol">
							<?php echo wp_travel_get_currency_symbol(); ?>
					</span>

					<span <?php echo 'fixed' === $coupon_type ? 'style="display:none;"' : ''; ?> id="coupon-percentage-symbol" class="wp-travel-currency-symbol">
							<?php echo '%'; ?>
					</span>
				</td>
			</tr>
			<tr>
				<td><label for="coupon-expiry-date"><?php esc_html_e( 'Coupon Expiry Date', 'wp-travel' ); ?>
				<span class="tooltip-area" title="<?php esc_html_e( 'Coupon expiration date. Leave blank to disable expiration.', 'wp-travel' ); ?>">
						<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					</span>
				</label>
				</td>
				<td>
					<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" type="text" class="wp-travel-datepicker" id="coupon-expiry-date" name="wp_travel_coupon[general][coupon_expiry_date]" readonly value="<?php echo esc_attr( $coupon_expiry_date ); ?>">
				</td>
			</tr>

		</tbody>
	</table>
	<?php
}
