<?php
/**
 * Pricing Tab meta Contents.
 *
 * @package WP_Travel
 */
function trip_callback_price() {

	global $post;
	$post_id        = $post->ID;
	$date_format    = get_option( 'date_format' );
	$js_date_format = wp_travel_date_format_php_to_js();
	
	$start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );
	
	// @since 1.8.3
	if ( ! empty( $start_date ) && ! wp_travel_is_ymd_date( $start_date ) ) {
		$start_date = wp_travel_format_ymd_date( $start_date );
	}
	if ( ! empty( $end_date ) && ! wp_travel_is_ymd_date( $end_date ) ) {
		$end_date = wp_travel_format_ymd_date( $end_date );
	}
	
	$group_size = get_post_meta( $post_id, 'wp_travel_group_size', true );
	
	$fixed_departure           = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
	$fixed_departure           = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure           = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
	$multiple_fixed_departures = get_post_meta( $post_id, 'wp_travel_enable_multiple_fixed_departue', true );
	$multiple_fixed_departures = apply_filters( 'wp_travel_multiple_fixed_departures', $multiple_fixed_departures );
	
	$enable_pricing_options = wp_travel_is_enable_pricing_options( $post_id );
	
	$pricing_option_type = wp_travel_get_pricing_option_type( $post_id );
	
	$enable_inventory_for_trip = get_post_meta( $post_id, 'enable_trip_inventory', true );
	
	$trip_duration       = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration       = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;
	
	$price       = get_post_meta( $post_id, 'wp_travel_price', true );
	$price       = $price ? $price : '';
	$sale_price  = get_post_meta( $post_id, 'wp_travel_sale_price', true );
	$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	
	$trip_pricing_options_data = get_post_meta( $post_id, 'wp_travel_pricing_options', true );
	
	$trip_multiple_date_options = get_post_meta( $post_id, 'wp_travel_multiple_trip_dates', true );
	
	$sale_price_attribute   = 'disabled="disabled"';
	$sale_price_style_class = 'hidden';
	if ( 'yes' === $enable_sale ) {
		$sale_price_attribute   = '';
		$sale_price_style_class = '';
	}
	
		$settings        = wp_travel_get_settings();
		$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
		$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
	
		$price_per = get_post_meta( $post_id, 'wp_travel_price_per', true );
	if ( ! $price_per ) {
		$price_per = 'person';
	}
	
	// CSS Class for Single and Multiple Pricing option fields.
	$single_pricing_option_class   = 'single-price-option-row';
	$multiple_pricing_option_class = 'multiple-price-option-row'; ?>
		
	<h3><?php echo esc_html( 'Pricing', 'wp-travel' ); ?></h3>
	<div class="form_field">
		<label class="label_title" for="wp-travel-pricing-option-type"><?php esc_html_e( 'Pricing Option', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<?php
			$pricing_types = wp_travel_get_pricing_option_list();
			if ( is_array( $pricing_types ) && count( $pricing_types ) > 0 ) {
				?>
				<select name="wp_travel_pricing_option_type" id="wp-travel-pricing-option-type" class="wp-travel-select2" data-hide-search="true">
					<?php foreach ( $pricing_types as $value => $pricing_label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $pricing_option_type, $value ); ?> ><?php echo esc_html( $pricing_label ); ?></option>
					<?php endforeach; ?>
				</select>
				<?php
			}
			?>
		</div>
	</div>

	<!-- Single Pricing options fields -->
	<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
		<label class="label_title" for="wp-travel-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<?php $price_per_fields = wp_travel_get_price_per_fields(); ?>
			<?php if ( is_array( $price_per_fields ) && count( $price_per_fields ) > 0 ) : ?>
				<select name="wp_travel_price_per" id="wp-travel-price-per" class="wp-travel-select2" data-hide-search="true" >
					<?php foreach ( $price_per_fields as $val => $label ) : ?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, $price_per ); ?> ><?php echo esc_html( $label, 'wp-travel' ); ?></option>
					<?php endforeach; ?>
				</select>
			<?php endif; ?>
		</div>
	</div>

	<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
		<label class="label_title" for="wp-travel-group-size"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" />
		</div>
	</div>

	<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
		<label class="label_title" for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input type="number" min="0.01" step="0.01" name="wp_travel_price" id="wp-travel-price" value="<?php echo esc_attr( $price ); ?>" />
		</div>
	</div>

	<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
		<label class="label_title" for="wp-travel-enable-sale"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input value="no" name="wp_travel_enable_sale" type="hidden" />
				<input type="checkbox" value="yes" <?php checked( 'yes', $enable_sale ); ?> name="wp_travel_enable_sale" id="wp-travel-enable-sale" class="onoffswitch-checkbox" />
				<label class="onoffswitch-label" for="wp-travel-enable-sale">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
			<figcaption> <label for="wp-travel-enable-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></label></figcaption>
		</div>
	</div>

	<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $sale_price_style_class ); ?>">
		<label class="label_title" for="wp-travel-sale-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input <?php echo esc_attr( $sale_price_attribute ); ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" />
		</div>
	</div>
	<!-- / Single Pricing options fields -->

	<!-- Multiple Pricing options fields -->
	<div class="price-option-row <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
		<div id="wp-travel-pricing-options" >
			<?php
			if ( is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) != 0 ) :
				$collapse_style = 'display:block';
			else :
				$collapse_style = 'display:none';
			endif;
			?>
				
			<div id="price-accordion" class="tab-accordion price-accordion">
				<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">
					<?php
					if ( is_array( $trip_pricing_options_data ) && '' !== $trip_pricing_options_data ) :
						foreach ( $trip_pricing_options_data as $key => $pricing ) {
							// Set Vars.
							$pricing_name         = isset( $pricing['pricing_name'] ) ? $pricing['pricing_name'] : '';
							$pricing_key          = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';
							$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
							$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';
							$pricing_option_price = isset( $pricing['price'] ) ? $pricing['price'] : '';
							$pricing_sale_enabled = isset( $pricing['enable_sale'] ) ? $pricing['enable_sale'] : '';
							$pricing_sale_price   = isset( $pricing['sale_price'] ) ? $pricing['sale_price'] : '';
							$pricing_price_per    = isset( $pricing['price_per'] ) ? $pricing['price_per'] : '';
							$pricing_min_pax      = isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : '';
							$pricing_max_pax      = isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : '';
							$enable_inventory     = isset( $pricing['enable_inventory'] ) ? $pricing['enable_inventory'] : 'no';

							// Pricing Label.
							$custom_pricing_label_attribute = 'disabled="disabled"';
							$custom_pricing_label_style     = 'display:none';

							// Pricing Sale.
							$custom_pricing_sale_price_attribute = 'disabled="disabled"';
							$custom_pricing_sale_price_class     = 'hidden';

							// Check for label.
							if ( 'custom' === $pricing_type ) {
								$custom_pricing_label_attribute = '';
								$custom_pricing_label_style     = '';
							}
							// Check for sale.
							if ( 'yes' === $pricing_sale_enabled ) {
								$custom_pricing_sale_price_attribute = '';
								$custom_pricing_sale_price_class     = '';
							}
							?>
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $key ); ?>">
									<h4 class="panel-title">
										<div class="wp-travel-sorting-handle"></div>
											<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-<?php echo esc_attr( $key ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $key ); ?>">
												<span bind="pricing_option_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $pricing_name ); ?></span>
												<span class="collapse-icon"></span>
											</a>
										<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
									</h4>
								</div>
								<div id="collapse-<?php echo esc_attr( $key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>">
									<div class="panel-body">
										<div class="panel-wrap">
											
											<div class="form_field">
												<label class="label_title" for="pricing_name_<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<input id="pricing_name_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-name" required bind="pricing_option_<?php echo esc_attr( $key ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][pricing_name]" value="<?php echo esc_attr( $pricing_name ); ?>">
													<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price_key]" value="<?php echo esc_attr( $pricing_key ); ?>">
													
													<figcaption><?php printf( __( 'Create a unique name for your pricing option.' ) ); ?></figcaption>
												</div>
											</div>

											<div class="form_field">
												<label class="label_title" for="pricing_type_<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Select a category', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<?php
													$pricing_variation_options = wp_travel_get_pricing_variation_options();
													if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
														?>
														<select name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][type]" class="wp-travel-pricing-options-list wp-travel-select2" id="pricing_type_<?php echo esc_attr( $key ); ?>">
															<?php
															foreach ( $pricing_variation_options as $option => $value ) {
																?>
																<option <?php selected( $pricing_type, $option ); ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $value ); ?></option>
																<?php
															}
															?>
														</select>
													<?php endif; ?>
												</div>
											</div>

											<div style="<?php echo esc_attr( $custom_pricing_label_style ); ?>" <?php echo esc_attr( $custom_pricing_label_attribute ); ?> class="form_field custom-pricing-label-wrap">
												<label class="label_title"><?php esc_html_e( 'Custom pricing Label', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<input value="<?php echo esc_attr( $pricing_custom_label ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][custom_label]" placeholder="name" />
												</div>
											</div>

											<div class="form_field">
												<label for="price_<?php echo esc_attr( $key ); ?>" class="label_title"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input id="price_<?php echo esc_attr( $key ); ?>" bindPrice="pricing_variation_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-main-price" required value="<?php echo esc_attr( $pricing_option_price ); ?>" type="number" min="1" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price]">
												</div>
											</div>

											<div class="form_field">
												<label class="label_title" for="wp-travel-enable-sale-<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<div class="onoffswitch">
														<input name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][enable_sale]" type="checkbox" class="onoffswitch-checkbox wp-travel-enable-variation-price-sale" <?php checked( $pricing_sale_enabled, 'yes' ); ?> value="yes" id="wp-travel-enable-sale-<?php echo esc_attr( $key ); ?>" >
														<label class="onoffswitch-label" for="wp-travel-enable-sale-<?php echo esc_attr( $key ); ?>">
															<span class="onoffswitch-inner"></span>
															<span class="onoffswitch-switch"></span>
														</label>
													</div>
													<figcaption><label for="wp-travel-enable-sale-<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></label></figcaption>
												</div>
											</div>

											<div <?php echo esc_attr( $custom_pricing_sale_price_attribute ); ?> class="form_field <?php echo esc_attr( $custom_pricing_sale_price_class ); ?>">
												<label for="sale_price_<?php echo esc_attr( $key ); ?>" class="label_title"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input id="sale_price_<?php echo esc_attr( $key ); ?>" bindSale="pricing_variation_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-sale-price" type="number" min="1" max="<?php echo esc_attr( $pricing_option_price ); ?>" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][sale_price]" id="" value="<?php echo esc_attr( $pricing_sale_price ); ?>" <?php echo esc_attr( $pricing_sale_enabled == 'yes' ? 'required="required"' : '' ); ?>  >
												</div>
											</div>

											<div class="form_field">
												<label for="price_per_<?php echo esc_attr( $key ); ?>" class="label_title"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<select id="price_per_<?php echo esc_attr( $key ); ?>" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price_per]" class="wp-travel-select2" data-hide-search="true">
														<option value="trip-default" <?php selected( $pricing_price_per, 'trip-default' ); ?>><?php esc_html_e( 'Trip Default', 'wp-travel' ); ?></option>
														<option value="person" <?php selected( $pricing_price_per, 'person' ); ?>><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
														<option value="group" <?php selected( $pricing_price_per, 'group' ); ?>><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
													</select>
												</div>
											</div>

											<div class="form_field">
												<label class="label_title"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
												<div class="subject_input">
													<input class="pricing-opt-min-pax" value="<?php echo esc_attr( $pricing_min_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][min_pax]" placeholder="Min PAX"  min="1" />

													<input class="pricing-opt-max-pax" value="<?php echo esc_attr( $pricing_max_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][max_pax]" placeholder="Max PAX"  min="<?php echo esc_attr( ( $pricing_min_pax ) ? $pricing_min_pax : 1 ); ?>" />
												</div>
											</div>
											<div class="form_field">
												<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $key ); ?>
											</div>
											<?php if ( class_exists( 'WP_Travel_Util_Inventory' ) && 'yes' === $enable_inventory_for_trip ) : ?>
												
												<div class="form_field">
													<label class="label_title"><?php esc_html_e( 'Enable Inventory', 'wp-travel' ); ?></label>
													<div class="subject_input">
														<div class="onoffswitch">
															<input name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][enable_inventory]" type="checkbox" class="onoffswitch-checkbox" <?php checked( $enable_inventory, 'yes' ); ?> value="yes">
															<label class="onoffswitch-label" for="wp-travel-enable-sale">
																<span class="onoffswitch-inner"></span>
																<span class="onoffswitch-switch"></span>
															</label>
														</div>
														<p class="wp-travel-enable-inventory description"><?php esc_html_e( 'Check to enable Inventory for this pricing option."SOLD OUT" message will be shown when the Max Pax value is exceeded by the booked pax.', 'wp-travel' ); ?></p>
													</div>
												</div>

											<?php endif; ?>
										</div>
										<?php
										/**
										 * @since 1.9.2
										 *
										 * @hooked 
										 */
										do_action( 'wp_travel_pricing_option_content_after_trip_extra', $post_id, $key, $pricing );
										?>
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
		
		<!-- Add New Pricing Options -->
		<div class="wp-travel-add-pricing-option clearfix text-right">
			<input type="button" value="<?php esc_html_e( 'Add New Pricing Option', 'wp-travel' ); ?>" class="button button-primary wp-travel-pricing-add-new" title="<?php esc_html_e( 'Add New Pricing Option', 'wp-travel' ); ?>" />
		</div>
	</div>

	<!-- Dates [ single and multiple date pricing options]. -->
	<h3><?php esc_html_e( 'Dates', 'wp-travel' ); ?></h3>

	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row %s %s', $single_pricing_option_class, $multiple_pricing_option_class ) ); ?>">
		<label class="label_title" for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ); ?> class="onoffswitch-checkbox" />							
				<label class="onoffswitch-label" for="wp-travel-fixed-departure">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
	</div>
	
	<!-- // Single pricing option and trip duration -->
	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row wp-travel-trip-duration-row' ) ); ?>">
		<label class="label_title" for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Day(s)', 'wp-travel' ); ?>
			<input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night(s)', 'wp-travel' ); ?> 
		</div>
	</div>

	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row wp-travel-enable-multiple-dates' ) ); ?>">
		<label class="label_title" for="wp-travel-enable-multiple-fixed-departure"><?php esc_html_e( 'Enable Multiple Dates', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<div class="onoffswitch">
				<input type="checkbox" name="wp_travel_enable_multiple_fixed_departue" id="wp-travel-enable-multiple-fixed-departure" value="yes" <?php checked( 'yes', $multiple_fixed_departures ); ?> class="onoffswitch-checkbox" />						
				<label class="onoffswitch-label" for="wp-travel-enable-multiple-fixed-departure">
					<span class="onoffswitch-inner"></span>
					<span class="onoffswitch-switch"></span>
				</label>
			</div>
		</div>
	</div>

	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row wp-travel-fixed-departure-row %s', $single_pricing_option_class ) ); ?>">
		<label class="label_title" for="wp-travel-start-date"><?php esc_html_e( 'Starting Date', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" />
		</div>
	</div>

	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row wp-travel-fixed-departure-row %s', $single_pricing_option_class ) ); ?>">
		<label class="label_title" for="wp-travel-end-date"><?php esc_html_e( 'Ending Date', 'wp-travel' ); ?></label>
		<div class="subject_input">
		<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" />
		</div>
	</div>

	<!-- Multiple Dates row -->

	<table class="form-table ">	
		<tr class="price-option-row <?php echo esc_attr( $multiple_pricing_option_class ); ?>" id="wp-variations-multiple-dates" >
	
		<?php if ( is_array( $trip_pricing_options_data ) && '' !== $trip_pricing_options_data ) : ?>
	
			<td colspan="2" class="pricing-repeater">
				<?php
				if ( is_array( $trip_multiple_date_options ) && count( $trip_multiple_date_options ) != 0 ) :
					$collapse_style = 'display:block';
				else :
					$collapse_style = 'display:none';
				endif;
				?>
				<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_style ); ?>">
					<a href="#" data-parent="wp-variations-multiple-dates" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
					<a data-parent="wp-variations-multiple-dates" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
				</div>
				<p class="description"><?php echo esc_html( 'You can select different dates for each category.', 'wp-travel' ); ?></p>
	
				<div class="tab-accordion date-accordion">	
					<div id="date-options-data" class="panel-group wp-travel-sorting-tabs" role="tablist" aria-multiselectable="true">
						<?php
						if ( is_array( $trip_multiple_date_options ) && count( $trip_multiple_date_options ) !== 0 ) :
							foreach ( $trip_multiple_date_options as $date_key => $date_option ) {
								// Set Vars.
								$date_label = isset( $date_option['date_label'] ) ? $date_option['date_label'] : '';
								$start_date = isset( $date_option['start_date'] ) ? $date_option['start_date'] : '';
								$end_date   = isset( $date_option['end_date'] ) ? $date_option['end_date'] : '';
								// @since 1.8.3
								if ( ! empty( $start_date ) && ! wp_travel_is_ymd_date( $start_date ) ) {
									$start_date = wp_travel_format_ymd_date( $start_date );
								}
								if ( ! empty( $end_date ) && ! wp_travel_is_ymd_date( $end_date ) ) {
									$end_date = wp_travel_format_ymd_date( $end_date );
								}
								$pricing_options = isset( $date_option['pricing_options'] ) ? $date_option['pricing_options'] : array();
								?>
								<div class="panel panel-default">
									<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $date_key ); ?>">
										<h4 class="panel-title">
											<div class="wp-travel-sorting-handle"></div>
												<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-<?php echo esc_attr( $date_key ); ?>" aria-expanded="false" aria-controls="collapse-<?php echo esc_attr( $date_key ); ?>" class="collapsed">
													<span bind="wp_travel_multiple_dates_<?php echo esc_attr( $date_key ); ?>"><?php echo esc_attr( $date_label ); ?></span>
													<span class="collapse-icon"></span>
												</a>
											<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
										</h4>
									</div>
	
									<div id="collapse-<?php echo esc_attr( $date_key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $date_key ); ?>" aria-expanded="true">
										<div class="panel-body">
											<div class="panel-wrap">
												<div class="form_field">
													<label class="label_title"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
													<div class="subject_input">
														<input class="wp-travel-variation-date-label" value="<?php echo esc_attr( $date_label ); ?>"  bind="wp_travel_multiple_dates_<?php echo esc_attr( $date_key ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
													</div>
												</div>
												<div class="form_field">
													<label class="label_title"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
													<div class="subject_input">
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $start_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $end_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
													</div>
												</div>
												<?php do_action( 'wp_travel_price_tab_after_multiple_date', $post_id, $date_key ); ?>
												<div class="form_field">
													<label class="label_title"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
													<div class="subject_input">
	
														<div class="custom-multi-select">
															<?php
															$count_options_data    = count( $trip_pricing_options_data );
															$count_pricing_options = count( $pricing_options );
															$multiple_checked_all  = '';
															if ( $count_options_data == $count_pricing_options ) {
																$multiple_checked_all = 'checked=checked';
															}
	
															$multiple_checked_text = __( 'Select multiple', 'wp-travel' );
															if ( $count_pricing_options > 0 ) {
																$multiple_checked_text = $count_pricing_options . __( ' item selected', 'wp-travel' );
															}
															?>
															<span class="select-main">
																<span class="selected-item"><?php echo esc_html( $multiple_checked_text ); ?></span> 
																<span class="carret"></span> 
																<span class="close"></span>
																<ul class="wp-travel-multi-inner">
																	<li class="wp-travel-multi-inner">
																		<label class="checkbox wp-travel-multi-inner">
																			<input <?php echo esc_attr( $multiple_checked_all ); ?> type="checkbox"  id="wp-travel-multi-input-1" class="wp-travel-multi-inner multiselect-all" value="multiselect-all">  Select all
																		</label>
																	</li>
																	<?php
																	foreach ( $trip_pricing_options_data as $pricing_opt_key => $pricing_option ) {
																		$checked            = '';
																		$selecte_list_class = '';
																		if ( in_array( $pricing_option['price_key'], $pricing_options ) ) {
																			$checked            = 'checked=checked';
																			$selecte_list_class = 'selected';
																		}
																		?>
																		<li class="wp-travel-multi-inner <?php echo esc_attr( $selecte_list_class ); ?>">
																			<label class="checkbox wp-travel-multi-inner ">
																				<input <?php echo esc_attr( $checked ); ?> name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][pricing_options][]" type="checkbox" id="wp-travel-multi-input-<?php echo esc_attr( $pricing_opt_key ); ?>" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $pricing_option['price_key'] ); ?>">  <?php echo esc_html( $pricing_option['pricing_name'] ); ?>
																			</label>
																		</li>
																	<?php } ?>
																</ul>
															</span>
														</div>
	
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
					<div class="wp-travel-add-date-option clearfix text-right">
						<input type="button" value="<?php esc_html_e( 'Add New date', 'wp-travel' ); ?>" class="button button-primary wp-travel-multiple-dates-add-new" title="<?php esc_html_e( 'Add New Date', 'wp-travel' ); ?>" />
					</div>
				</div>
			</td>
		<?php elseif ( is_array( $trip_pricing_options_data ) && '' !== $trip_pricing_options_data ) : ?>
			<td colspan="2"><p class="description"><?php echo esc_html__( 'Please Enable Multiple Pricing Options and update add/edit multiple dates ', 'wp-travel' ); ?></p></td>
		<?php else : ?>
			<td colspan="2"><p class="description"><?php echo esc_html__( 'Please Add Multiple Pricing Options and update to add multiple dates ', 'wp-travel' ); ?></p></td>
		<?php endif; ?>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-title">
			<th colspan="2">
				<h3><?php echo esc_html( 'Tour Extras', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<div class="form_field price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-content">
			<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = false, $key = 'wp_travel_tour_extras', $table_row = true ); ?>
		</div>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<th colspan="2">
				<h3><?php echo esc_html( 'Payout', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<?php
		/**
		 * Hook Added.
		 *
		 * @since 1.0.5
		 */
		do_action( 'wp_travel_itinerary_after_sale_price', $post_id );
		
		// WP Travel Standard Paypal merged. since 1.2.1
		
		$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
	
		$payout_percent = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_percent', true );
		$payout_percent = $payout_percent ? $payout_percent : $default_payout_percent;

		$use_global = wp_travel_use_global_payout_percent( $post_id );
	
		$custom_payout_class = ( 1 == $use_global ) ? 'hidden' : '';
		?>
	</table>

	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row %s %s', $single_pricing_option_class, $multiple_pricing_option_class ) ); ?>">
		<label class="label_title" for="wp-travel-minimum-partial-payout-percent-use-global"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<!-- <span class="use-global" > -->
				<div class="onoffswitch">
					<input id="wp-travel-minimum-partial-payout-percent-use-global" type="checkbox" name="wp_travel_minimum_partial_payout_use_global" <?php checked( $use_global, 1 ); ?> value="1" class="onoffswitch-checkbox" /> 
					<label class="onoffswitch-label" for="wp-travel-minimum-partial-payout-percent-use-global">
						<span class="onoffswitch-inner"></span>
						<span class="onoffswitch-switch"></span>
					</label>
				</div>
			<!-- </span> -->
			<figcaption><label for="wp-travel-minimum-partial-payout-percent-use-global"><?php esc_html_e( 'Use Global ', 'wp-travel' ); echo sprintf( '%s&percnt;', esc_html( $default_payout_percent ) ); ?></label></figcaption>
		</div>
	</div>
	<div class="<?php echo esc_attr( sprintf( 'form_field price-option-row %s %s %s', $single_pricing_option_class, $multiple_pricing_option_class, $custom_payout_class ) ); ?>" >
		<label class="label_title" for="wp-travel-minimum-partial-payout-percent"><?php esc_html_e( 'Custom Min. Payout (%)', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="number" min="1" max="100" step="0.01" name="wp_travel_minimum_partial_payout_percent" id="wp-travel-minimum-partial-payout-percent" value="<?php echo esc_attr( $payout_percent ); ?>" />
		</div>
	</div>
	<?php do_action( 'wp_travel_itinerary_price_tab_table_last_row', $post_id ); ?>

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
					<div class="form_field">
							<label for="pricing_name_{{data.random}}" class="label_title"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<input class="wp-travel-variation-pricing-name" required="required" bind="pricing_option_{{data.random}}" type="text" id="pricing_name_{{data.random}}" name="wp_travel_pricing_options[{{data.random}}][pricing_name]" value="">
								<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[{{data.random}}][price_key]" value="">
								<p class="description"><?php echo esc_html__( 'Create a unique name for your pricing option', 'wp-travel' ); ?></p>
							</div>
						</div>
						<div class="form_field">
							<label class="label_title"><?php esc_html_e( 'Select a category', 'wp-travel' ); ?></label>
							<div class="subject_input">
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

						<div style="display:none" class="form_field custom-pricing-label-wrap">
							<label class="label_title"><?php esc_html_e( 'Custom pricing Label', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<input type="text" name="wp_travel_pricing_options[{{data.random}}][custom_label]" placeholder="name" />
							</div>
						</div>

						<div class="form_field">
							<label for="price_{{data.random}}" class="label_title"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
								<input id="price_{{data.random}}" bindPrice="pricing_variation_{{data.random}}" required="required" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][price]">
							</div>
						</div>

						<div class="form_field">
							<label class="label_title"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<div class="onoffswitch">
									<input name="wp_travel_pricing_options[{{data.random}}][enable_sale]" type="checkbox" class="onoffswitch-checkbox wp-travel-enable-variation-price-sale" value="yes">			
									<label class="onoffswitch-label" for="wp-travel-enable-sale">
										<span class="onoffswitch-inner"></span>
										<span class="onoffswitch-switch"></span>
									</label>
								</div>
								<span class="wp-travel-enable-sale wp-travel-enable-variation-price-sale"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></span>
							</div>
						</div>

						<div class="form_field" style="display:none">
							<label for="sale_price_{{data.random}}" class="label_title"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
								<input id="sale_price_{{data.random}}" bindSale="pricing_variation_{{data.random}}" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][sale_price]">
							</div>
						</div>

						<div class="form_field">
							<label for="price_per_{{data.random}}" class="label_title"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<select id="price_per_{{data.random}}" name="wp_travel_pricing_options[{{data.random}}][price_per]">
									<option value="trip-default"><?php esc_html_e( 'Trip Default', 'wp-travel' ); ?></option>
									<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
									<option value="group"><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
								</select>
							</div>
						</div>

						<div class="form_field">
							<label class="label_title"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<input class="pricing-opt-min-pax" type="number" name="wp_travel_pricing_options[{{data.random}}][min_pax]" placeholder="Min PAX"  min="1" />

								<input class="pricing-opt-max-pax" type="number" name="wp_travel_pricing_options[{{data.random}}][max_pax]" placeholder="Max PAX"  min="1" />
							</div>
						</div>
						<div class="form_field">
						<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $key = '{{data.random}}' ); ?>
						</div>
						<?php if ( class_exists( 'WP_Travel_Util_Inventory' ) && 'yes' === $enable_inventory_for_trip ) : ?>

							<div class="form_field">
								<label class="label_title"><?php esc_html_e( 'Enable Inventory', 'wp-travel' ); ?></label>
								<div class="subject_input">
									<div class="onoffswitch">
										<input name="wp_travel_pricing_options[{{data.random}}][enable_inventory]" type="checkbox" class="onoffswitch-checkbox" value="yes">
										<label class="onoffswitch-label" for="wp-travel-enable-sale">
											<span class="onoffswitch-inner"></span>
											<span class="onoffswitch-switch"></span>
										</label>
									</div>
									<span class=""><?php esc_html_e( 'Check to enable Inventory for this pricing option."SOLD OUT" message will be shown when the Max Pax value is exceeded by the booked pax.', 'wp-travel' ); ?></span>
								</div>
							</div>

						<?php endif; ?>

					</div>
					<?php
					/**
						* @since 1.9.2
						*
						* @hooked 
						*/
					do_action( 'wp_travel_pricing_option_content_after_trip_extra_repeator', '{{data.random}}' );
					?>
				</div>
			</div>
		</div>
	</script>
	<!-- Pricing Template End -->

	<!-- Template Script for dates -->
	<script type="text/html" id="tmpl-wp-travel-multiple-dates">
		<div class="panel panel-default">
			<div class="panel-heading" role="tab" id="heading-{{data.random}}">
				<h4 class="panel-title">
					<div class="wp-travel-sorting-handle"></div>
						<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="false" aria-controls="collapse-{{data.random}}" class="collapsed">

							<span bind="wp_travel_multiple_dates_{{data.random}}"><?php echo esc_html( 'Multiple Date 1', 'wp-travel' ); ?></span>

							<span class="collapse-icon"></span>
						</a>
					<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
				</h4>
			</div>
			<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}" aria-expanded="true">
				<div class="panel-body">
					<div class="panel-wrap">
						<div class="form_field">
							<label class="label_title"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<input class="wp-travel-variation-date-label" bind="wp_travel_multiple_dates_{{data.random}}" name="wp_travel_multiple_trip_dates[{{data.random}}][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
							</div>
						</div>
						<div class="form_field">
							<label class="label_title"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
							<div class="subject_input">
								<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
								<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
							</div>
						</div>
						<?php do_action( 'wp_travel_price_tab_after_multiple_date_template', $post_id ); ?>
						<div class="form_field">
							<label class="label_title"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
							<div class="subject_input">
							
								<div class="custom-multi-select">
									<span class="select-main">
										<span class="selected-item"><?php esc_html_e( 'Select multiple', 'wp-travel' ); ?></span> 
										<span class="carret"></span> 
										<span class="close"></span>
										<ul class="wp-travel-multi-inner">
											<li class="wp-travel-multi-inner">
												<label class="checkbox wp-travel-multi-inner">
													<input type="checkbox"  id="wp-travel-multi-input-1" class="wp-travel-multi-inner multiselect-all" value="multiselect-all">  Select all
												</label>
											</li>
											<?php
											foreach ( $trip_pricing_options_data as $pricing_opt_key => $pricing_option ) {
												?>
												<li class="wp-travel-multi-inner">
													<label class="checkbox wp-travel-multi-inner ">
														<input name="wp_travel_multiple_trip_dates[{{data.random}}][pricing_options][]" type="checkbox" id="wp-travel-multi-input-{{data.random}}" class="wp-travel-multi-inner multiselect-value" value="<?php echo esc_attr( $pricing_option['price_key'] ); ?>">  <?php echo esc_html( $pricing_option['pricing_name'] ); ?>
													</label>
												</li>
											<?php } ?>
										</ul>
									</span>

								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</script>
	<!-- Template Script for dates ends-->

	<?php
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title' => __( 'Need More Options ?', 'wp-travel' ),
			'content' => __( 'By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel' ),
			'link' => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
}
