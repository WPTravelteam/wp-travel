<?php
	global $post;

	$price 		= get_post_meta( $post->ID, 'wp_travel_price', true );
	$sale_price = get_post_meta( $post->ID, 'wp_travel_sale_price', true );
	
	$enable_sale = get_post_meta( $post->ID, 'wp_travel_enable_sale', true );
	
	$sale_price_attribute = 'disabled="disabled"';
	$sale_price_style = 'display:none';
	if ( $enable_sale ) {
		$sale_price_attribute = '';
		$sale_price_style = '';
	}

	$settings = wp_travel_get_settings();
	$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] :'';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	$price_per = get_post_meta( $post->ID, 'wp_travel_price_per', true );
	if ( ! $price_per ) {
		$price_per = 'person';
	}
?>
<table class="form-table">
	<tr>
		<td><label for="wp-travel-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label></td>
		<td>
			<?php $price_per_fields = wp_travel_get_price_per_fields(); ?>
			<?php if ( is_array( $price_per_fields ) && count( $price_per_fields ) > 0 ) : ?>
				<select name="wp_travel_price_per">
					<?php foreach ( $price_per_fields as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ) ?>" <?php selected( $val, $price_per ) ?> ><?php echo esc_html( $label, 'wp-travel' ) ?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</td>
	</tr>
	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span><input type="number" min="0" step="0.01" name="wp_travel_price" id="wp-travel-price" value="<?php echo esc_attr( $price ); ?>" /></td>
	</tr>

	<tr>
		<td><label for="wp-travel-price"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label></td>
		<td>
			
			<label>
				<input type="checkbox" name="wp_travel_enable_sale" id="wp-travel-enable-sale" <?php checked( $enable_sale, 1 ); ?> value="1" />
				<span class="wp-travel-enable-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
			</label>
		</td>
	</tr>
	<tr style="<?php echo esc_attr( $sale_price_style ); ?>">
		<td><label for="wp-travel-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span><input <?php echo $sale_price_attribute; ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></td>
	</tr>
	<?php
	/**
	 * Hook Added.
	 *
	 * @since 1.0.5
	 */
	do_action( 'wp_travel_itinerary_after_sale_price', $post->ID ); ?>
</table>
