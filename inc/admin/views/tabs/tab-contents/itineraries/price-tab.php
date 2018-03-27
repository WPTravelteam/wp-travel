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
				<div id="price-accordion" class="tab-accordion price-accordion">
					<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">
						
					</div>
				</div>
			</div>
			<div class="wp-travel-add-pricing-option clearfix">
				<input type="button" value="Add New Pricing Option" class="button button-primary wp-travel-pricing-add-new" title="Add New Pricing Option" />
			</div>
			<script type="text/html" id="tmpl-wp-travel-pricing-options">

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-{{data.random}}">
						<h4 class="panel-title">
							<div class="wp-travel-sorting-handle"></div>
								<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">

									<span bind="faq_question_{{data.random}}"><?php echo esc_html( 'Pricing Option 1', 'wp-travel' ); ?></span>

									<span class="collapse-icon"></span>
								</a>
							<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
						</h4>
					</div>
					<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Select a category', 'wp-travel' ); ?></label>
									<div class="two-third">
										<select>
											<option value="everyone"><?php esc_html_e( 'Custom', 'wp-travel' ); ?></option>
											<option value="group-of-four"><?php esc_html_e( 'Group of Four', 'wp-travel' ); ?></option>
										</select>
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Custom pricing name', 'wp-travel' ); ?></label>
									<div class="two-third">
										<input type="text" name="" placeholder="name" />
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
									<div class="two-third">
										<span class="wp-travel-currency-symbol">$</span>
										<input type="number" min="1" name="" id="" value="300">
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third">Enable Sale</label>
									<div class="two-third">
										<span class="show-in-frontend checkbox-default-design">
											<label data-on="ON" data-off="OFF">
												<input name="wp_travel_enable_sale" type="checkbox" id="wp-travel-enable-sale" checked="checked" value="1" "="">							
												<span class="switch">
											  </span>
											 
											</label>
										</span>
										<span class="wp-travel-enable-sale">Check to enable sale.</span>
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
									<div class="two-third">
										<span class="wp-travel-currency-symbol">$</span>
										<input type="number" min="1" name="" id="" value="300">
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
									<div class="two-third">
										<input type="number" name="" placeholder="Min PAX"  min="1" />

										<input type="number" name="" placeholder="Max PAX"  min="1" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</script>

		</td>
	</tr>

	<tr>
		<td class="pricing-repeater"><label for="wp-travel-multiple-date-options"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label>
		</td>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_enable_pricing-options" type="checkbox" id="wp-travel-multiple-date-options" checked="checked" value="1" "="">							
					<span class="switch">
				  </span>
				 
				</label>
			</span>
			 <span class="wp-travel-enable-pricing-options checkbox-with-label">Check to enable different date options.</span>
			
		</td>
	</tr>


	<tr>
		<td colspan="2" class="pricing-repeater">
			<p class="description">You can select different dates for each category.</p>





			<div id="date-accordion" class="tab-accordion date-accordion">
					<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">
						
					

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-594">
						<h4 class="panel-title">
							<div class="wp-travel-sorting-handle"></div>
								<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-594" aria-expanded="false" aria-controls="collapse-594" class="collapsed">

									<span bind="faq_question_594">Multiple Date 1</span>

									<span class="collapse-icon"></span>
								</a>
							<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
						</h4>
					</div>
					<div id="collapse-594" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-594" aria-expanded="true">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="repeat-row">
									<label class="one-third">Add a Label</label>
									<div class="two-third">
										<input type="text" placeholder="Your Text here" />
									</div>
								</div>
								<div class="repeat-row">
									<label class="one-third">Select a Date</label>
									<div class="two-third">
										<input type="text" data-language="en" class="datepicker-here" placeholder="Start Date" />
										<input type="text" data-language="en" class="datepicker-here" placeholder="End Date" />
									</div>
								</div>
								<div class="repeat-row">
									<label class="one-third">Select a Category</label>
									<div class="two-third">
										<div class="custom-multi-select">
											<span class="select-main">
												<span class="selected-item">Select multiple</span> 
												<span class="carret"></span> 
												<span class="close"></span>
												<ul>
													<li>
														<a tabindex="0" class="multiselect-all">
															<label class="checkbox">
																<input type="checkbox" value="multiselect-all">  Select all
															</label>
														</a>
													</li>
													<li>
														<a tabindex="0" class="multiselect">
															<label class="checkbox">
																<input type="checkbox" value="multiselect">  Adult
															</label>
														</a>
													</li>
													<li>
														<a tabindex="0" class="multiselect">
															<label class="checkbox">
																<input type="checkbox" value="multiselect">  Children
															</label>
														</a>
													</li>
													<li>
														<a tabindex="0" class="multiselect">
															<label class="checkbox">
																<input type="checkbox" value="multiselect">  Group of four
															</label>
														</a>
													</li>
												</ul>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
				</div>
		
		</td>
	</tr>

<script type="text/javascript">
	
	jQuery('.select-main .close').hide();
	jQuery(document).on('click','.select-main .close', function(){
		$(this).siblings('.active').removeClass('active');
		$(this).siblings('.carret').show();
		$(this).hide();
		var countSelected = jQuery('.select-main li.selected').length
		jQuery('.select-main .selected-item').html(countSelected + ' item selected');
	});
	jQuery('.select-main').click(function(){
		if($(this).find('ul.active').length == 0){
			$(this).children('ul').addClass('active');
			$(this).children('.close').show();
			$(this).children('.carret').hide();
		} else{
			// $(this).children('.carret').show();
			// $(this).children('.close').hide();
			// $(this).children('ul').removeClass('active');
		}
	});
	// $("body").click(function(e){
	//     if(e.target.className !== "select-main")
	//     {
	//       jQuery('.select-main').children('.carret').show();
	// 		jQuery('.select-main').children('.close').hide();
	// 		jQuery('.select-main').children('ul').removeClass('active');
	//     }
	//   }
	// );
	jQuery('.select-main li input').change(function($) { //on change do stuff
		jQuery(this).parent().parent().parent('li').toggleClass('selected');
	});
	// jQuery(document).on('click', '.select-main li a', function(event) {
	// 	jQuery(this).parent('li').toggleClass('selected');
	// });

</script>


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
