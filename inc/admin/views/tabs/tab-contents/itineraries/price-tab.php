<?php
/**
 * Pricing Tab meta Contents.
 *
 * @package WP_Travel
 */

global $post;

$start_date	= get_post_meta( $post->ID, 'wp_travel_start_date', true );
$end_date 	= get_post_meta( $post->ID, 'wp_travel_end_date', true );

$fixed_departure = get_post_meta( $post->ID, 'wp_travel_fixed_departure', true );
$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

$trip_duration = get_post_meta( $post->ID, 'wp_travel_trip_duration', true );
$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
$trip_duration_night = get_post_meta( $post->ID, 'wp_travel_trip_duration_night', true );
$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

$price       = get_post_meta( $post->ID, 'wp_travel_price', true );
$sale_price  = get_post_meta( $post->ID, 'wp_travel_sale_price', true );
$enable_sale = get_post_meta( $post->ID, 'wp_travel_enable_sale', true );

$trip_pricing_options_data = get_post_meta( $post->ID , 'wp_travel_pricing_options', true );

$sale_price_attribute = 'disabled="disabled"';
$sale_price_style     = 'display:none';

if ( $enable_sale ) {
	$sale_price_attribute = '';
	$sale_price_style     = '';
}

	$settings        = wp_travel_get_settings();
	$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	$price_per = get_post_meta( $post->ID, 'wp_travel_price_per', true );
if ( ! $price_per ) {
	$price_per = 'person';
}
?>
<table class="form-table pricing-tab">
	<tr class="table-inside-heading">
		<th colspan="2">
			<h3><?php echo esc_html( 'Pricing', 'wp-travel' ); ?></h3>
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
					<span class="switch"></span>
				</label>
			</span>
			<span class="wp-travel-enable-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
		</td>
	</tr>
	<tr style="<?php echo esc_attr( $sale_price_style ); ?>">
		<td><label for="wp-travel-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label></td>
		<td><span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input <?php echo esc_attr( $sale_price_attribute ); ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></td>
	</tr>

	<tr class="table-inside-heading">
		<td class="pricing-repeater"><label for="wp-travel-enable-pricing-options"><?php esc_html_e( 'Pricing Options', 'wp-travel' ); ?></label>
		</td>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_enable_pricing_options" type="checkbox" id="wp-travel-enable-pricing-options" checked="checked" value="1" "="">	
					<span class="switch"></span>
				</label>
			</span>
			<span class="wp-travel-enable-pricing-options checkbox-with-label"><?php echo esc_html__( 'Check to enable different pricing options.', 'wp-travel' ); ?></span>
		</td>
	</tr>
	<tr>
		<td id="wp-travel-multiple-pricing-options" colspan="2" class="pricing-repeater">
			<div id="wp-travel-pricing-options">
				<p class="description"><?php echo esc_html__( 'Select different pricing category with its different sale price', 'wp-travel' ); ?></p>
				<div id="price-accordion" class="tab-accordion price-accordion">
						<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">
						<?php 
						if ( is_array( $trip_pricing_options_data ) && '' !== $trip_pricing_options_data ) : 
						?>
						<div class="wp-collapse-open">
							<a href="#" data-parent="wp-travel-multiple-pricing-options" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ) ?></span></a>
							<a data-parent="wp-travel-multiple-pricing-options" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ) ?></span></a>
						</div>
						<?php
							foreach ( $trip_pricing_options_data as $key => $pricing ) {
							// Set Vars.
								$pricing_name         = isset( $pricing['pricing_name'] ) ? $pricing['pricing_name'] : '';
								$pricing_key          = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';
								$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
								$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';
								$pricing_option_price = isset( $pricing['price'] ) ? $pricing['price'] : '';
								$pricing_sale_enabled = isset( $pricing['enable_sale'] ) ? $princing['enable_sale'] : '';
								$pricing_sale_price   = isset( $pricing['sale_price'] ) ? $pricing['sale_price'] : '';
								$pricing_min_pax      = isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : '';
								$pricing_max_pax      = isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : '';

								// Pricing Label.
								$custom_pricing_label_attribute = 'disabled="disabled"';
								$custom_pricing_label_style     = 'display:none';

								// Pricing Sale.
								$custom_pricing_sale_price_attribute = 'disabled="disabled"';
								$custom_pricing_sale_price_style     = 'display:none';

								// Check for label.
								if ( 'custom' === $pricing_type ) {
									$custom_pricing_label_attribute = '';
									$custom_pricing_label_style     = '';
								}
								// Check for sale.
								if ( 'yes' === $pricing_sale_enabled ) {
									$custom_pricing_sale_price_attribute = '';
									$custom_pricing_sale_price_style     = '';
								}
						?>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $key ); ?>">
									<h4 class="panel-title">
										<div class="wp-travel-sorting-handle"></div>
											<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $key ); ?>">
												<span bind="pricing_option_<?php echo esc_attr( $key ); ?>"><?php echo esc_html($pricing_name); ?></span>
												<span class="collapse-icon"></span>
											</a>
										<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
									</h4>
								</div>
								<div id="collapse-<?php echo esc_attr( $key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>">
									<div class="panel-body">
										<div class="panel-wrap">
										<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input required bind="pricing_option_<?php echo esc_attr( $key ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][pricing_name]" value="<?php echo esc_attr( $pricing_name ); ?>">
													<input type="hidden" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price_key]" value="<?php echo esc_attr( $pricing_key ); ?>">
												</div>
											</div>
											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Select a category', 'wp-travel' ); ?></label>
												<div class="two-third">
												<?php
												$pricing_variation_options = wp_travel_get_pricing_variation_options();
												if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
												?>
													<select name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][type]" class="wp-travel-pricing-options-list">
														<?php 
														foreach ( $pricing_variation_options as $option => $value ) {
														?>
															<option selected( $pricing_type, $key ) value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $value ); ?></option>
														<?php
															}
														?>
													</select>
												<?php endif; ?>
												</div>
											</div>

											<div style="<?php echo esc_attr( $custom_pricing_label_style ); ?>" <?php echo esc_attr( $custom_pricing_label_attribute ); ?> class="repeat-row custom-pricing-label-wrap">
												<label class="one-third"><?php esc_html_e( 'Custom pricing Label', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input value="<?php echo esc_attr( $pricing_custom_label ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][custom_label]" placeholder="name" />
												</div>
											</div>

											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
												<div class="two-third">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input required value="<?php echo esc_attr( $pricing_option_price ); ?>" type="number" min="1" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price]">
												</div>
											</div>

											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
												<div class="two-third">
													<span class="show-in-frontend checkbox-default-design">
														<label data-on="ON" data-off="OFF">
															<input name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][emable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale" <?php checked( $pricing_sale_enabled, "yes" ) ?> value="yes" "="">
															<span class="switch"></span>
														</label>
													</span>
													<span class="wp-travel-enable-variation-price-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
												</div>
											</div>

											<div style="<?php echo esc_attr( $custom_pricing_sale_price_style ); ?>" <?php echo esc_attr( $custom_pricing_sale_price_attribute ); ?> class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
												<div class="two-third">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input type="number" min="1" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][sale_price]" id="" value="<?php echo esc_attr( $pricing_sale_price ); ?>">
												</div>
											</div>

											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input value="<?php echo esc_attr( $pricing_min_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][min_pax]" placeholder="Min PAX"  min="1" />

													<input value="<?php echo esc_attr( $pricing_max_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][max_pax]" placeholder="Max PAX"  min="1" />
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php 
							}
						endif;
						?>
					</div>
				</div>
			</div>
			<div class="wp-travel-add-pricing-option clearfix">
				<input type="button" value="<?php esc_html_e( 'Add New Pricing Option', 'wp-travel' ); ?>" class="button button-primary wp-travel-pricing-add-new" title="<?php esc_html_e( 'Add New Pricing Option', 'wp-travel' ); ?>" />
			</div>
			<!-- Template Script for Pricing Options -->
			<script type="text/html" id="tmpl-wp-travel-pricing-options">
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="heading-{{data.random}}">
						<h4 class="panel-title">
							<div class="wp-travel-sorting-handle"></div>
								<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="true" aria-controls="collapse-{{data.random}}">
									<span bind="pricing_option_{{data.random}}"><?php echo esc_html( 'Pricing Option', 'wp-travel' ); ?></span>
									<span class="collapse-icon"></span>
								</a>
							<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
						</h4>
					</div>
					<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
						<div class="panel-body">
							<div class="panel-wrap">
							<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
									<div class="two-third">
										<input bind="pricing_option_{{data.random}}" type="text" name="wp_travel_pricing_options[{{data.random}}][pricing_name]" value="">
										<input type="hidden" name="wp_travel_pricing_options[{{data.random}}][price_key]" value="">
									</div>
								</div>
								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Select a category', 'wp-travel' ); ?></label>
									<div class="two-third">
									<?php
									$pricing_variation_options = wp_travel_get_pricing_variation_options();
									if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
									?>
										<select  name="wp_travel_pricing_options[{{data.random}}][type]" class="wp-travel-pricing-options-list">
											<?php
											foreach ( $pricing_variation_options as $key => $value ) {
											?>
												<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
											<?php
												}
											?>
										</select>
									<?php endif; ?>
									</div>
								</div>

								<div style="display:none" class="repeat-row custom-pricing-label-wrap">
									<label class="one-third"><?php esc_html_e( 'Custom pricing Label', 'wp-travel' ); ?></label>
									<div class="two-third">
										<input type="text" name="wp_travel_pricing_options[{{data.random}}][custom_label]" placeholder="name" />
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
									<div class="two-third">
										<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
										<input type="number" min="1" name="wp_travel_pricing_options[{{data.random}}][price]">
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
									<div class="two-third">
										<span class="show-in-frontend checkbox-default-design">
											<label data-on="ON" data-off="OFF">
												<input name="wp_travel_pricing_options[{{data.random}}][emable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale" value="yes" "="">			
												<span class="switch"></span>
											</label>
										</span>
										<span class="wp-travel-enable-variation-price-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
									</div>
								</div>

								<div class="repeat-row" style="display:none">
									<label class="one-third"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
									<div class="two-third">
										<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
										<input type="number" min="1" name="wp_travel_pricing_options[{{data.random}}][sale_price]">
									</div>
								</div>

								<div class="repeat-row">
									<label class="one-third"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
									<div class="two-third">
										<input type="number" name="wp_travel_pricing_options[{{data.random}}][min_pax]" placeholder="Min PAX"  min="1" />

										<input type="number" name="wp_travel_pricing_options[{{data.random}}][max_pax]" placeholder="Max PAX"  min="1" />
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</script>
			<!-- Pricing Template End -->
		</td>
	</tr>

	<tr>
		<td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
		<td>
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ) ?> />							
					<span class="switch"></span>
				</label>
			</span>
		</td>
	</tr>
	<tr class="wp-travel-trip-duration-row" style="display:<?php echo ( 'no' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
		<td>
			<input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Day(s)', 'wp-travel' ) ?>
			<input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night(s)', 'wp-travel' ) ?>                
		</td>
	</tr>        

	<tr class="wp-travel-fixed-departure-row" style="display:<?php echo ( 'yes' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp-travel-multiple-fixed-departure"><?php esc_html_e( 'Enable Multiple Dates', 'wp-travel' ); ?></label></td>
		<td><span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input type="checkbox" name="wp_travel_enable_multiple_fixed_departue" id="wp-travel-multiple-fixed-departure" value="yes" <?php // checked( 'yes', $fixed_departure ) ?> />							
					<span class="switch"></span>
				</label>
			</span>
		</td>
	</tr>
	<tr class="wp-travel-fixed-departure-row" style="display:<?php echo ( 'yes' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp-travel-start-date"><?php esc_html_e( 'Starting Date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" /></td>
	</tr>
	<tr class="wp-travel-fixed-departure-row" style="display:<?php echo ( 'yes' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending Date', 'wp-travel' ); ?></label></td>
		<td><input type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" /></td>
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
										<input type="text" data-language="en" class="datepicker-here" readonly placeholder="Start Date" />
										<input type="text" data-language="en" class="datepicker-here" readonly placeholder="End Date" />
									</div>
								</div>
								<div class="repeat-row">
									<label class="one-third">Select pricing options</label>
									<div class="two-third">
										<div class="custom-multi-select">
											<span class="select-main">
												<span class="selected-item">Select multiple</span> 
												<span class="carret"></span> 
												<span class="close"></span>
												<ul class="wp-travel-multi-inner">
													<li class="wp-travel-multi-inner">
														<label class="checkbox wp-travel-multi-inner" for="wp-travel-multi-input-1">
															<input type="checkbox"  id="wp-travel-multi-input-1" class="wp-travel-multi-inner multiselect-all" value="multiselect-all">  Select all
														</label>
													</li>
													<li class="wp-travel-multi-inner">
														<label class="checkbox wp-travel-multi-inner" for="wp-travel-multi-input-2">
															<input type="checkbox" id="wp-travel-multi-input-2" class="wp-travel-multi-inner" value="multiselect">  Adult
														</label>
													</li>
													<li class="wp-travel-multi-inner">
														<label class="checkbox wp-travel-multi-inner" for="wp-travel-multi-input-3">
															<input type="checkbox" id="wp-travel-multi-input-3" class="wp-travel-multi-inner" value="multiselect">  Children
														</label>
													</li>
													<li class="wp-travel-multi-inner">
														<label class="checkbox wp-travel-multi-inner" for="wp-travel-multi-input-4">
															<input type="checkbox" id="wp-travel-multi-input-4" class="wp-travel-multi-inner" value="multiselect">  Group of four
														</label>
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
		$(this).siblings('.wp-travel-active').removeClass('wp-travel-active');
		$(this).siblings('.carret').show();
		$(this).hide();
	});
	jQuery('.select-main').click(function(e){
		if($(this).find('ul.wp-travel-active').length == 0){
			$(this).children('ul').addClass('wp-travel-active');
			$(this).children('.close').show();
			$(this).children('.carret').hide();
		} else{
			$(this).children('.carret').show();
			$(this).children('.close').hide();
			$(this).children('ul').removeClass('wp-travel-active');
		}
	});
	$(document).on("click", function(event){
       var $trigger = $(".select-main");
       if($trigger !== event.target && !$trigger.has(event.target).length){
           $("ul.wp-travel-active").removeClass("wp-travel-active");
           $(".select-main").find('.carret').show();
			$(".select-main").find('.close').hide();
       }            
   });
	jQuery('.select-main li input').change(function($) { //on change do stuff
		jQuery(this).parents('li').toggleClass('selected');
	});

	jQuery('.multiselect-all').change(function($){
		jQuery(this).parents('li').siblings().children('label').trigger('click'); 
	})
	var updateTable = function(event){
		var countSelected = jQuery('.select-main li.selected').length
		jQuery(this).parents('ul').siblings('.selected-item').html(countSelected + ' item selected');
	}
	jQuery(document).on('input click change','input.wp-travel-multi-inner', updateTable)
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
