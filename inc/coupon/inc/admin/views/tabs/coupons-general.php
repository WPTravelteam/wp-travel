<?php
/**
 * Coupons General Tab Callback.
 *
 * @package WP_Tarvel_Coupons_Pro
 */

 global $post;
 // General Tab Data.
 $coupon_metas       = get_post_meta( $post->ID, 'wp_travel_coupon_metas', true );
 $general_tab        = isset( $coupon_metas['general'] ) ? $coupon_metas['general'] : array();
 $coupon_code        = get_post_meta( $post->ID, 'wp_travel_coupon_code', true );
 // Field Values.
 $coupon_active      = isset( $general_tab['coupon_active'] ) ? $general_tab['coupon_active'] : 'yes';
 $coupon_code        = ! empty( $coupon_code ) ? $coupon_code : '';
 $coupon_type        = isset( $general_tab['coupon_type'] ) ? $general_tab['coupon_type'] : 'fixed';
 $coupon_value       = isset( $general_tab['coupon_value'] ) ? $general_tab['coupon_value'] : '';
 $coupon_expiry_date = isset( $general_tab['coupon_expiry_date'] ) ? $general_tab['coupon_expiry_date'] : '';

 $coupon = new WP_Travel_Coupon();

 $coupon_id =  $coupon->get_coupon_id_by_code( $coupon_code  );

 var_dump( $coupon_id );

?>

<table class="form-table">
	<tbody>
		<tr>
			<td>
				<label for="currency"><?php esc_html_e( 'Coupon Status ', 'wp-travel' ); ?></label>
			</td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input <?php checked( $coupon_active , 'yes' ); ?> value="1" name="wp_travel_coupon[general][coupon_active]" id="coupon_active" type="checkbox" />						
						<span class="switch">
						</span>
					</label>
				</span>

				<span class="wp-travel-info-msg">
					Active
				</span>
				<span class="wp-travel-error-msg">
					Expired
				</span>
			</td>
		<tr>
		<tr>
			<td>
				<label for="coupon-code"><?php esc_html_e( 'Coupon Code', 'wp-travel' ); ?></label>
				<span class="tooltip-area" title="<?php esc_html_e( 'Unique Identifier for the coupon.', 'wp-travel' ); ?>">
               		<i class="fa fa-question-circle" aria-hidden="true"></i>
           		</span>
			</td>
			<td>
				<input type="text" id="coupon-code" name="wp_travel_coupon_code" placeholder="<?php echo esc_attr__( 'WP-TRAVEL-350', 'wp-travel' ); ?>" value="<?php echo esc_attr( $coupon_code ); ?>">
			</td>
		</tr>
		<tr>
			<td><label for="coupon-type"><?php esc_html_e( 'Coupon Type', 'wp-travel' ); ?></label>
				<span class="tooltip-area" title="<?php esc_html_e( 'Coupon Type: Fixed Discount Amount or Percentage discount( Applies to cart total price ).', 'wp-travel' ); ?>">
               		<i class="fa fa-question-circle" aria-hidden="true"></i>
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
					<i class="fa fa-question-circle" aria-hidden="true"></i>
				</span>
			</td>
			<td>
				<input type="number" min="1" <?php echo 'percentage' === $coupon_type ? 'max="100"' : ''; ?> step="0.01" id="coupon-value" name="wp_travel_coupon[general][coupon_value]" placeholder="<?php echo esc_attr__( 'Coupon Value', 'wp-travel' ); ?>" value="<?php echo esc_attr( $coupon_value ); ?>">
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
               		<i class="fa fa-question-circle" aria-hidden="true"></i>
           		</span>
			</label>
			</td>
			<td>
				<input type="text" class="wp-travel-datepicker" id="coupon-expiry-date" name="wp_travel_coupon[general][coupon_expiry_date]" readonly value="<?php echo esc_attr( $coupon_expiry_date ); ?>">
			</td>
		</tr>

	</tbody>
</table>