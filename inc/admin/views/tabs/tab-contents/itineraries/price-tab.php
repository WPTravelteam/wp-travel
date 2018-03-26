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
<table class="form-table pricing-tab">
	<tr class="table-inside-heading">
		<th colspan="2">
			<h3>Pricing</h3>
		</th>
	</tr>
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
		<td><label for="wp-travel-enable-sale"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label></td>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_enable_sale" type="checkbox" id="wp-travel-enable-sale" <?php checked( $enable_sale, 1 ); ?> value="1" " />							
					<span class="switch">
				  </span>
				 
				</label>
			</span>
			 <span class="wp-travel-enable-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
			
		</td>
	</tr>
	<tr style="<?php echo esc_attr( $sale_price_style ); ?>">
		<td><label for="wp-travel-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span><input <?php echo $sale_price_attribute; ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></td>
	</tr>



	<tr class="table-inside-heading">
		<td class="pricing-repeater"><label for="wp-travel-enable-pricing-options"><?php esc_html_e( 'Pricing Options', 'wp-travel' ); ?></label>
		</td>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_enable_pricing-options" type="checkbox" id="wp-travel-enable-pricing-options" checked="checked" value="1" "="">							
					<span class="switch">
				  </span>
				 
				</label>
			</span>
			 <span class="wp-travel-enable-pricing-options checkbox-with-label">Check to enable different pricing options.</span>
			
		</td>

		
	</tr>
	<tr>
		<td colspan="2" class="pricing-repeater">
			<div id="wp-travel-pricing-options">
				<p class="description">Select different pricing category with its different sale price</p>
				<!-- <div class="repeat-row">
					<select>
						<option value="everyone"><?php esc_html_e( 'Everyone', 'wp-travel' ); ?></option>
						<option value="group-of-four"><?php esc_html_e( 'Group of Four', 'wp-travel' ); ?></option>
					</select>
					<span class="wp-travel-currency-symbol">$</span>
					<input type="number" name="" min="1" step="any" placeholder="Sale Price" />
				</div>
				<div class="repeat-row">
					<select>
						<option value="everyone"><?php esc_html_e( 'Everyone', 'wp-travel' ); ?></option>
						<option value="group-of-four"><?php esc_html_e( 'Custom', 'wp-travel' ); ?></option>
					</select>
					
					<span class="wp-travel-currency-symbol">$</span>
					<input type="number" name="" min="1" step="any" placeholder="Sale Price" />
					<input type="text" name="label" placeholder="Label" />
					<div class="wp-travel-remove-pricing-option">
						<a href="#" class="button button-primary wp-travel-pricing-remove" title="remove this field"><i class="remove-item">x</i></a>
					</div>

				</div> -->
				<div id="tab-accordion" class="tab-accordion">
			<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">

			</div>
		</div>
			</div>
			<div class="wp-travel-add-pricing-option clearfix">
				<input type="button" value="Add New Question" class="button button-primary wp-travel-pricing-add-new" title="Add New Pricing Option" />
			</div>
			<script type="text/html" id="tmpl-wp-travel-pricing-options">

				<div class="panel panel-default">
				<div class="panel-heading" role="tab" id="heading-{{data.random}}">
				<h4 class="panel-title">
				<div class="wp-travel-sorting-handle"></div>
				<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">

					<span bind="faq_question_{{data.random}}"><?php echo esc_html( 'FAQ?', 'wp-travel' ); ?></span>

				<span class="collapse-icon"></span>
				</a>
				<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
				</h4>
				</div>
				<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
				<div class="panel-body">
				<div class="panel-wrap">
					<label><?php esc_html_e( 'Enter Your Question', 'wp-travel' ); ?></label>
					<input bind="faq_question_{{data.random}}" type="text" name="wp_travel_faq_question[]" placeholder="FAQ Question?" class="faq-question-text" value="">
				</div>
				<textarea rows="6" name="wp_travel_faq_answer[]" placeholder="Write Your Answer."></textarea>
				</div>
				</div>
				</div>
			</script>

		</td>
	</tr>

	<?php
	/**
	 * Hook Added.
	 *
	 * @since 1.0.5
	 */
	do_action( 'wp_travel_itinerary_after_sale_price', $post->ID ); ?>
	<?php
	// WP Travel Standard Paypal merged. since 1.2.1	
	$wp_travel_minimum_partial_payout = wp_travel_minimum_partial_payout( $post->ID );
	if ( $wp_travel_minimum_partial_payout < 1 ) {
		$wp_travel_minimum_partial_payout = '';
	}
	$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 )? $settings['minimum_partial_payout']  : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

	$trip_price = wp_travel_get_actual_trip_price( $post->ID );

	$payout_percent = wp_travel_get_payout_percent( $post->ID );
	$use_global = wp_travel_use_global_payout_percent( $post->ID ); 
	
	$custom_payout_class = '';

	if ( 1 == $use_global ) {

		$custom_payout_class = 'display:none';

	} ?>
	<tr style="display:none">
		<td><label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Minimum Payout', 'wp-travel' ); ?></label></td>
		<td>
			<span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span>
			<input type="number" step="0.01" name="wp_travel_minimum_partial_payout" id="wp-travel-minimum-partial-payout" value="<?php echo esc_attr( $wp_travel_minimum_partial_payout ); ?>" />
			<span class="description"><?php esc_html_e( 'Default : ', 'wp-travel' ); echo sprintf( '%s&percnt; of %s%s', esc_html( $default_payout_percent ), esc_html( $currency_symbol ), esc_html( $trip_price ) ) ?></span>
		</td>
	</tr>

	<tr>
		<td><label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label></td>
		<td>
			<span class="use-global" >
				<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input id="wp-travel-minimum-partial-payout-percent-use-global" type="checkbox" name="wp_travel_minimum_partial_payout_use_global" <?php checked( $use_global, 1 ); ?> value="1" /> 
					<span class="switch">
				  </span>
				 
				</label>
			</span>
			<span class="wp-travel-enable-sale">
				<?php esc_html_e( 'Use Global', 'wp-travel' ) ?> 	<?php echo sprintf( '%s &percnt;', esc_html( $default_payout_percent ) ) ?>		
			</span>
						
			</span>
		</td>
	</tr>
	<tr style="<?php echo esc_attr( $custom_payout_class ); ?>" >
		<td>
			<label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Custom Min. Payout (%)', 'wp-travel' ); ?></label>
		</td>
		<td>
			<input type="number" min="1" max="100" step="0.01" name="wp_travel_minimum_partial_payout_percent" id="wp-travel-minimum-partial-payout-percent" value="<?php echo esc_attr( $payout_percent ); ?>" />
		</td>
	</tr>
	<?php // Ends WP Travel Standard Paypal merged. since 1.2.1 ?>
</table>
