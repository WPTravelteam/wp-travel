<?php
/**
 * Pricing Tab meta Contents.
 *
 * @package WP_Travel
 */
function wp_travel_trip_callback_price() {
	$settings = wp_travel_get_settings();

	$new_multiple_category = $settings['enable_multiple_category_on_pricing']; // @since 3.0.0
	// Implement new multiple category options(child, adult) on multiple pricing option. eg . Pricing A, have adult, child option in one pricing.
	// Also single pricing option is removed form this version (@since 3.0.0) as well for new users.
	if ( 'yes' === $new_multiple_category ) {
		wp_travel_new_pricing_list_admin();
	} else {
		wp_travel_old_pricing_list_admin();
	}
}


function wp_travel_new_pricing_list_admin() {
	global $post;
	$post_id        = $post->ID;
	$date_format    = get_option( 'date_format' );
	$settings       = wp_travel_get_settings();
	$js_date_format = wp_travel_date_format_php_to_js();
	$pricing_types  = wp_travel_get_pricing_option_list();

	$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	$price_per = get_post_meta( $post_id, 'wp_travel_price_per', true );
	if ( ! $price_per ) {
		$price_per = 'person';
	}

	// Only for single pricing option. Legacy pricing.
	$price       = get_post_meta( $post_id, 'wp_travel_price', true );
	$price       = $price ? $price : '';
	$sale_price  = get_post_meta( $post_id, 'wp_travel_sale_price', true );
	$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );
	$sale_price_attribute   = 'disabled="disabled"';
	$sale_price_style_class = 'hidden';

	if ( $enable_sale ) {
		$sale_price_attribute   = '';
		$sale_price_style_class = '';
	}

	// CSS Class for Single and Multiple Pricing option fields.
	$single_pricing_option_class   = 'single-price-option-row';
	$multiple_pricing_option_class = 'multiple-price-option-row';

	// Non Looped Data.
	$current_pricing_type      = wp_travel_get_pricing_option_type( $post_id ); // multiple-pricing by default for new listing.
	$start_date                = get_post_meta( $post_id, 'wp_travel_start_date', true );
	$end_date                  = get_post_meta( $post_id, 'wp_travel_end_date', true );
	$group_size                = get_post_meta( $post_id, 'wp_travel_group_size', true ); // Group size need to implement in multiple pricing
	$trip_duration             = get_post_meta( $post_id, 'wp_travel_trip_duration', true );
	$trip_duration             = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night       = get_post_meta( $post_id, 'wp_travel_trip_duration_night', true );
	$trip_duration_night       = ( $trip_duration_night ) ? $trip_duration_night : 0;
	$fixed_departure           = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
	$fixed_departure           = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure           = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );
	$multiple_fixed_departures = get_post_meta( $post_id, 'wp_travel_enable_multiple_fixed_departue', true );
	$multiple_fixed_departures = apply_filters( 'wp_travel_multiple_fixed_departures', $multiple_fixed_departures );

	// Looped data.
	$trip_pricing_options_data  = get_post_meta( $post_id, 'wp_travel_pricing_options', true );
	$trip_multiple_date_options = get_post_meta( $post_id, 'wp_travel_multiple_trip_dates', true );

	?>
	<table class="form-table pricing-tab">
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td><label for="wp-travel-detail"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label></td>
			<td><input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" /></td>
		</tr>
		<tr class="table-inside-heading">
			<th colspan="2">
				<h3><?php echo esc_html( 'Pricing', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<?php if ( is_array( $pricing_types ) ) : ?>
			<?php if ( count( $pricing_types ) > 1 ) : ?>

				<tr class="pricing-option-title">
					<td><label for="wp-travel-pricing-option-type"><?php esc_html_e( 'Pricing Option', 'wp-travel' ); ?></label></td>
					<td>
						<select name="wp_travel_pricing_option_type" id="wp-travel-pricing-option-type">
							<?php foreach ( $pricing_types as $value => $pricing_label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $current_pricing_type, $value ); ?> ><?php echo esc_html( $pricing_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php
			else :
				$current_pricing_type = 'multiple-price';
				?>
				<input type="hidden" name="wp_travel_pricing_option_type" id="wp-travel-pricing-option-type" value="<?php echo esc_attr( $current_pricing_type ); ?>" >
			<?php endif; ?>
		<?php endif; ?>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label></td>
			<td>
				<?php $price_per_fields = wp_travel_get_price_per_fields(); ?>
				<?php if ( is_array( $price_per_fields ) && count( $price_per_fields ) > 0 ) : ?>
					<select name="wp_travel_price_per">
						<?php foreach ( $price_per_fields as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, $price_per ); ?> ><?php echo esc_html( $label, 'wp-travel' ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</td>
		</tr>

		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label></td>
			<td><div class="field-price-currency-input"><span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input type="number" min="0.01" step="0.01" name="wp_travel_price" id="wp-travel-price" value="<?php echo esc_attr( $price ); ?>" /></div></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-enable-sale"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label></td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input name="wp_travel_enable_sale" type="checkbox" id="wp-travel-enable-sale" <?php checked( $enable_sale, 1 ); ?> value="1" />
						<span class="switch"></span>
					</label>
				</span>
				<p class="wp-travel-enable-sale description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $sale_price_style_class ); ?>">
			<td><label for="wp-travel-sale-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label></td>
			<td><div class="field-price-currency-input"><span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input <?php echo esc_attr( $sale_price_attribute ); ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></div></td>
		</tr>

		<!-- Multiple Priceing field -->
		<tr class="price-option-row <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td  id="wp-travel-multiple-pricing-options" colspan="2" class="pricing-repeater"  >
				<div id="wp-travel-pricing-options" style="padding:20px 0; margin: 0 -10px;">
					<?php
					if ( is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) != 0 ) :
						$collapse_style = 'display:block';
					else :
						$collapse_style = 'display:none';
					endif;
					?>
					<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_style ); ?>">
						<a href="#" data-parent="wp-travel-multiple-pricing-options" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
						<a data-parent="wp-travel-multiple-pricing-options" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
					</div>
					<p class="description"><?php echo esc_html__( 'Select different pricing category with its different sale price', 'wp-travel' ); ?></p>

					<!-- repeator field for pricing option -->
					<div id="price-accordion" class="tab-accordion price-accordion wp-travel-accordion has-handler">
							<div class="panel-group wp-travel-sorting-tabs" id="pricing-options-data" role="tablist" aria-multiselectable="true">
							<?php
							if ( is_array( $trip_pricing_options_data ) && '' !== $trip_pricing_options_data ) :
								foreach ( $trip_pricing_options_data as $pricing_id => $pricing ) {
									// Set Vars.
									$pricing_name = isset( $pricing['pricing_name'] ) ? $pricing['pricing_name'] : '';
									$pricing_min_pax = isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : 0;
									$pricing_max_pax = isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : 0;
									$pricing_key  = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';

									// Old legacy data. Need to migrate to new data. @since 3.0.0
									if ( ! isset( $pricing['categories'] ) ) { // No category and its id. so create new assign pricing id and assign values in the category. WE don't need variable type category id.
										$category_id = $pricing_id;
										$pricing['categories'][ $category_id ] = array(
											'type'         => isset( $pricing['type'] ) ? $pricing['type'] : 'adult',
											'custom_label' => isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '',
											'min_pax'      => isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : 1,
											'max_pax'      => isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : 1,
											'price_per'    => isset( $pricing['price_per'] ) ? $pricing['price_per'] : 'person',
											'price'        => isset( $pricing['price'] ) ? $pricing['price'] : 0,
											'enable_sale'  => isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'] ? $pricing['enable_sale'] : 'no',
											'sale_price'   => isset( $pricing['sale_price'] ) ? $pricing['sale_price'] : 0,
											'tour_extras'  => isset( $pricing['tour_extras'] ) ? $pricing['tour_extras'] : array(),
										);
									}

									// Pricing Label.
									$custom_pricing_label_attribute = 'disabled="disabled"';
									$custom_pricing_label_style     = 'display:none';

									// Pricing Sale.
									$custom_pricing_sale_price_attribute = 'disabled="disabled"';
									$custom_pricing_sale_price_class     = 'hidden';

									?>
									<div class="panel panel-default">
										<div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $pricing_id ); ?>">
											<h4 class="panel-title">
												<div class="wp-travel-sorting-handle"></div>
													<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-<?php echo esc_attr( $pricing_id ); ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $pricing_id ); ?>">
														<span bind="pricing_option_<?php echo esc_attr( $pricing_id ); ?>"><?php echo esc_html( $pricing_name ); ?></span>
														<!-- <span class="collapse-icon"></span> -->
													</a>
												<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
											</h4>
										</div>


										<div id="collapse-<?php echo esc_attr( $pricing_id ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $pricing_id ); ?>">
											<div class="panel-body">
												<div class="panel-wrap">
													<input type="hidden" class="wp-travel-price-key" value="<?php echo esc_attr( $pricing_id ); ?>" >
													<div class="repeat-row">
														<label for="pricing_name_<?php echo esc_attr( $pricing_id ); ?>" class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
														<div class="two-third">
															<div class="field-input">
																<input class="wp-travel-variation-pricing-name" id="pricing_name_<?php echo esc_attr( $pricing_id ); ?>" class="wp-travel-variation-pricing-name" required bind="pricing_option_<?php echo esc_attr( $pricing_id ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][pricing_name]" value="<?php echo esc_attr( $pricing_name ); ?>">
																<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][price_key]" value="<?php echo esc_attr( $pricing_key ); ?>">
																<p class="description"><?php echo esc_html__( 'Create a unique name for your pricing option', 'wp-travel' ); ?></p>
															</div>
														</div>
													</div>
													<div class="repeat-row">
														<label for="pricing_name_<?php echo esc_attr( $pricing_id ); ?>_min_pax" class="one-third"><?php esc_html_e( 'Min Pax:', 'wp-travel' ); ?></label>
														<div class="two-third">
															<div class="field-input">
																<input class="pricing-opt-min-pax" required type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][min_pax]" value="<?php echo esc_attr( $pricing_min_pax ); ?>" min="1">
															</div>
														</div>
													</div>
													<div class="repeat-row">
														<label for="pricing_name_<?php echo esc_attr( $pricing_id ); ?>_max_pax" class="one-third"><?php esc_html_e( 'Max Pax:', 'wp-travel' ); ?></label>
														<div class="two-third">
															<div class="field-input">
																<input class="pricing-opt-max-pax" required type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][max_pax]" value="<?php echo esc_attr( $pricing_max_pax ); ?>" min="1">
															</div>
														</div>
													</div>
													<div class="repeat-row">
														<label for="pricing_name_<?php echo esc_attr( $pricing_id ); ?>" class="one-third"><?php esc_html_e( 'Prices', 'wp-travel' ); ?> </label>
														<?php do_action( 'wp_travel_pricing_add_new_category_button' ); ?>
													</div>

													<div class="new-category-row row new-category-row-heading">
														<div class="repeat-row heading-category">
															<label ><?php esc_html_e( 'Category', 'wp-travel' ); ?></label>
														</div>

														<!-- <div class="repeat-row heading-no-of-pax">
															<label ><?php esc_html_e( 'Min/ Max Pax', 'wp-travel' ); ?></label>
														</div> -->

														<div class="repeat-row heading-price-per">
															<label ><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
														</div>
														<div class="repeat-row heading-price">
															<label for="price_<?php echo esc_attr( $pricing_id ); ?>"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
														</div>
														<div class="repeat-row heading-enable-sale">
															<label><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
														</div>
														<div class="repeat-row heading-sale-price">
															<label for="sale_price_<?php echo esc_attr( $pricing_id ); ?>" ><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
														</div>

													</div>

													<?php
													$categories = isset( $pricing['categories'] ) ? $pricing['categories'] : array();

													if ( is_array( $categories ) && count( $categories ) > 0 ) {
														?>
														<div class="new-category-row row new-category-row-content" id="new-category-row-content-<?php echo esc_attr( $pricing_id ); ?>">

															<?php
															foreach ( $categories as $category_id => $category ) {
																$pricing_type         = isset( $category['type'] ) ? $category['type'] : '';
																$pricing_custom_label = isset( $category['custom_label'] ) ? $category['custom_label'] : '';
																$pricing_option_price = isset( $category['price'] ) ? $category['price'] : '';
																$pricing_sale_enabled = isset( $category['enable_sale'] ) ? $category['enable_sale'] : '';
																$pricing_sale_price   = isset( $category['sale_price'] ) ? $category['sale_price'] : '';
																$pricing_price_per    = isset( $category['price_per'] ) ? $category['price_per'] : '';
																$pricing_min_pax      = isset( $category['min_pax'] ) ? $category['min_pax'] : '';
																$pricing_max_pax      = isset( $category['max_pax'] ) ? $category['max_pax'] : '';
																$enable_inventory     = isset( $category['enable_inventory'] ) ? $category['enable_inventory'] : 'no';

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
																<div class="new-category-repeator">
																	<a href="#" class="wp-travel-remove-pricing-category" >X</a>
																	<div class="repeat-row">
																		<?php
																		$pricing_variation_options = wp_travel_get_pricing_variation_options();
																		if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
																			?>
																			<select data-category-id="<?php echo esc_attr( $category_id ); ?>" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][type]" class="wp-travel-pricing-options-list">
																				<?php
																				foreach ( $pricing_variation_options as $pk => $value ) {
																					?>
																					<option value="<?php echo esc_attr( $pk ); ?>" <?php selected( $pk, $pricing_type ); ?> ><?php echo esc_html( $value ); ?></option>
																					<?php
																				}
																				?>
																			</select>
																			<input type="text" style="display:none" class="custom-pricing-label-wrap-<?php echo esc_attr( $category_id ); ?>" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][custom_label]" placeholder="Custom Label" />
																		<?php endif; ?>
																	</div>
																	<!-- <div class="repeat-row">
																		<input class="pricing-opt-min-pax" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][min_pax]" placeholder="Min"  min="1" value="<?php echo esc_attr( $pricing_min_pax ); ?>" />
																		<input class="pricing-opt-max-pax" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][max_pax]" placeholder="Max"  min="1" value="<?php echo esc_attr( $pricing_max_pax ); ?>" />
																	</div> -->
																	<div class="repeat-row">
																		<select id="price_per_<?php echo esc_attr( $pricing_id ); ?>_<?php echo esc_attr( $category_id ); ?>" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][price_per]">
																			<option value="person" <?php selected( 'person', $pricing_price_per ); ?> ><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
																			<option value="group" <?php selected( 'group', $pricing_price_per ); ?>><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
																		</select>
																	</div>

																	<div class="repeat-row">
																		<div class="field-price-currency-input">
																			<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
																			<input id="price_<?php echo esc_attr( $pricing_id ); ?>" bindPrice="pricing_variation_<?php echo esc_attr( $pricing_id . '-' . $category_id ); ?>" required="required" type="number" min="1" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][price]" value="<?php echo esc_attr( $pricing_option_price ); ?>" >
																		</div>
																	</div>
																	<div class="repeat-row">
																		<div class="two-third">
																			<span class="show-in-frontend checkbox-default-design">
																				<label data-on="ON" data-off="OFF">
																					<input name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][enable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale-new" value="yes" <?php checked( $pricing_sale_enabled, 'yes' ); ?> >
																					<span class="switch"></span>
																				</label>
																			</span>
																			<p class="wp-travel-enable-sale wp-travel-enable-variation-price-sale-new description"><?php esc_html_e( 'Check to enable.', 'wp-travel' ); ?></p>

																		</div>
																	</div>
																	<div class="repeat-row <?php echo ( 'yes' !== $pricing_sale_enabled ) ? 'visibility-hidden' : ''; ?>">
																		<div class="field-price-currency-input">
																			<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
																			<input id="sale_price_<?php echo esc_attr( $pricing_id ); ?>" bindSale="pricing_variation_<?php echo esc_attr( $pricing_id . '-' . $category_id ); ?>" type="number" min="1" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $pricing_id ); ?>][categories][<?php echo esc_attr( $category_id ); ?>][sale_price]" value="<?php echo esc_attr( $pricing_sale_price ); ?>" />
																		</div>
																	</div>

																	<?php do_action( 'wp_travel_pricing_option_content_after_category', $post_id, $pricing_id, $category_id, $category ); ?>
																</div>
																<?php
															}
															?>
														</div>
														<?php
													}
													?>

													<div class="repeat-row">
														<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $pricing_id ); ?>
													</div>

												</div>
												<?php
												/**
												 * @since 1.9.2
												 *
												 * @hooked
												 */
												do_action( 'wp_travel_pricing_option_content_after_trip_extra', $post_id, $pricing_id, $pricing );
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
				<div class="wp-travel-add-pricing-option clearfix text-right">
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
									</a>
								<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
							</h4>
						</div>
						<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
							<div class="panel-body">
								<div class="panel-wrap">
									<input type="hidden" class="wp-travel-price-key" value="{{data.random}}" >

									<div class="repeat-row">
										<label for="pricing_name_{{data.random}}" class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
										<div class="two-third">
											<input class="wp-travel-variation-pricing-name" required="required" bind="pricing_option_{{data.random}}" type="text" id="pricing_name_{{data.random}}" name="wp_travel_pricing_options[{{data.random}}][pricing_name]" value="">
											<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[{{data.random}}][price_key]" value="">
											<p class="description"><?php echo esc_html__( 'Create a unique name for your pricing option', 'wp-travel' ); ?></p>
										</div>
									</div>
									<div class="repeat-row">
										<label for="pricing_name_{{data.random}}_min_pax" class="one-third"><?php esc_html_e( 'Min Pax:', 'wp-travel' ); ?></label>
										<div class="two-third">
											<input class="wp-travel-variation-pricing-min-pax" required="required" type="number" id="pricing_name_{{data.random}}_min_pax" name="wp_travel_pricing_options[{{data.random}}][min_pax]" value="" min="1">
										</div>
									</div>
									<div class="repeat-row">
										<label for="pricing_name_{{data.random}}_max_pax" class="one-third"><?php esc_html_e( 'Max Pax:', 'wp-travel' ); ?></label>
										<div class="two-third">
											<input class="wp-travel-variation-pricing-max-pax" required="required" type="number" id="pricing_name_{{data.random}}_max_pax" name="wp_travel_pricing_options[{{data.random}}][max_pax]" value="" min="1">
										</div>
									</div>
									<div class="repeat-row">
										<label for="pricing_name_{{data.random}}" class="one-third"><?php esc_html_e( 'Prices', 'wp-travel' ); ?></label>
										<?php do_action( 'wp_travel_pricing_add_new_category_button_repeator' ); ?>
									</div>
									<div class="new-category-row row new-category-row-heading">
										<div class="repeat-row heading-category">
											<label ><?php esc_html_e( 'Category', 'wp-travel' ); ?></label>
										</div>
										<div class="repeat-row heading-price-per">
											<label ><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
										</div>
										<div class="repeat-row heading-price">
											<label for="price_{{data.random}}"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
										</div>
										<div class="repeat-row heading-enable-sale">
											<label><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
										</div>
										<div class="repeat-row heading-sale-price">
											<label for="sale_price_{{data.random}}" ><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
										</div>

									</div>

									<div class="new-category-row row new-category-row-content" id="new-category-row-content-{{data.random}}">
										<div class="new-category-repeator">
											<a href="#" class="wp-travel-remove-pricing-category" >X</a>
											<div class="repeat-row">
												<?php
												$pricing_variation_options = wp_travel_get_pricing_variation_options();
												if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
													?>
													<select  name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][type]" class="wp-travel-pricing-options-list">
														<?php
														foreach ( $pricing_variation_options as $key => $value ) {
															?>
															<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
															<?php
														}
														?>
													</select>
													<input type="text" style="display:none" class="custom-pricing-label-wrap" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][custom_label]" placeholder="Custom Label" />
												<?php endif; ?>
											</div>

											<div class="repeat-row">
												<select id="price_per_{{data.random}}_{{data.category_id}}" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][price_per]">
													<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
													<option value="group"><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
												</select>
											</div>

											<div class="repeat-row">
												<div class="field-price-currency-input">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input id="price_{{data.random}}" bindPrice="pricing_variation_{{data.random}}" required="required" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][price]">
												</div>
											</div>
											<div class="repeat-row">
												<div class="two-third">
													<span class="show-in-frontend checkbox-default-design">
														<label data-on="ON" data-off="OFF">
															<input name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][enable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale-new" value="yes">
															<span class="switch"></span>
														</label>
													</span>
													<p class="wp-travel-enable-sale wp-travel-enable-variation-price-sale-new description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>

												</div>
											</div>
											<div class="repeat-row visibility-hidden">
												<div class="field-price-currency-input">
													<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
													<input id="sale_price_{{data.random}}" bindSale="pricing_variation_{{data.random}}" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][sale_price]" />
												</div>
											</div>
											<?php do_action( 'wp_travel_pricing_option_content_after_category_inside_repeator', '{{data.random}}', '{{data.category_id}}' ); ?>

										</div>
									</div>
									<div class="repeat-row">
										<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $key = '{{data.random}}' ); ?>
									</div>
								</div>
								<?php
								/**
								 * @since 1.9.2
								 *
								 * @hooked
								 */
								do_action( 'wp_travel_pricing_option_content_after_trip_extra_repeator', '{{data.random}}', '{{data.category_id}}' );
								?>
							</div>
						</div>
					</div>
				</script>
				<!-- Pricing Template End -->
				<script type="text/html" id="tmpl-wp-travel-pricing-options-category">
					<div class="new-category-repeator">
						<a href="#" class="wp-travel-remove-pricing-category" >X</a>
						<div class="repeat-row">
							<?php
							$pricing_variation_options = wp_travel_get_pricing_variation_options();
							if ( ! empty( $pricing_variation_options ) && is_array( $pricing_variation_options ) ) :
								?>
								<select data-category-id="{{data.category_id}}" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][type]" class="wp-travel-pricing-options-list">
									<?php
									foreach ( $pricing_variation_options as $key => $value ) {
										?>
										<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></option>
										<?php
									}
									?>
								</select>
								<input type="text" style="display:none" class="custom-pricing-label-wrap-{{data.category_id}}" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][custom_label]" placeholder="Custom Label" />
							<?php endif; ?>
						</div>


						<div class="repeat-row">
							<select id="price_per_{{data.random}}_{{data.category_id}}" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][price_per]">
								<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
								<option value="group"><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
							</select>
						</div>

						<div class="repeat-row">
							<div class="field-price-currency-input">
								<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
								<input id="price_{{data.random}}" bindPrice="pricing_variation_{{data.random}}-{{data.category_id}}" required="required" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][price]">
							</div>
						</div>
						<div class="repeat-row">
							<div class="two-third">
								<span class="show-in-frontend checkbox-default-design">
									<label data-on="ON" data-off="OFF">
										<input name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][enable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale-new" value="yes">
										<span class="switch"></span>
									</label>
								</span>
								<p class="wp-travel-enable-sale wp-travel-enable-variation-price-sale-new description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>
							</div>
						</div>
						<div class="repeat-row visibility-hidden">
							<div class="field-price-currency-input">
								<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
								<input id="sale_price_{{data.random}}" bindSale="pricing_variation_{{data.random}}-{{data.category_id}}" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][categories][{{data.category_id}}][sale_price]" />
							</div>
						</div>
						<?php do_action( 'wp_travel_pricing_option_content_after_category_inside_repeator', '{{data.random}}', '{{data.category_id}}' ); ?>
					</div>
				</script>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td colspan="2"><hr></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<th colspan="2">
				<h3><?php esc_html_e( 'Dates', 'wp-travel' ); ?></h3>
			</th>
		</tr>

		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ); ?> />
						<span class="switch"></span>
					</label>
				</span>
			</td>
		</tr>
		<tr class="price-option-row wp-travel-trip-duration-row" >
			<td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
			<td>
				<input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Day(s)', 'wp-travel' ); ?>
				<input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night(s)', 'wp-travel' ); ?>
			</td>
		</tr>
		<tr class="price-option-row  <?php echo esc_attr( $multiple_pricing_option_class ); ?> wp-travel-enable-multiple-dates" >
			<td><label for="wp-travel-enable-multiple-fixed-departure"><?php esc_html_e( 'Enable Multiple Dates', 'wp-travel' ); ?></label></td>
			<td><span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" name="wp_travel_enable_multiple_fixed_departue" id="wp-travel-enable-multiple-fixed-departure" value="yes" <?php checked( 'yes', $multiple_fixed_departures ); ?> />
						<span class="switch"></span>
					</label>
				</span>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-fixed-departure-row" >
			<td><label for="wp-travel-start-date"><?php esc_html_e( 'Starting Date', 'wp-travel' ); ?></label></td>
			<td><input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" class="date-input" /></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-fixed-departure-row">
			<td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending Date', 'wp-travel' ); ?></label></td>
			<td><input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" class="date-input" /></td>
		</tr>

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

				<div class="tab-accordion date-accordion wp-travel-accordion has-handler">
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
												</a>
											<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
										</h4>
									</div>

									<div id="collapse-<?php echo esc_attr( $date_key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $date_key ); ?>" aria-expanded="true">
										<div class="panel-body">
											<div class="panel-wrap">
												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
													<div class="two-third">
														<input class="wp-travel-variation-date-label" value="<?php echo esc_attr( $date_label ); ?>"  bind="wp_travel_multiple_dates_<?php echo esc_attr( $date_key ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
													</div>
												</div>
												<div class="repeat-row">
													<label class="one-third"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
													<div class="two-third">
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $start_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date date-input" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $end_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date date-input" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
													</div>
												</div>
												<?php do_action( 'wp_travel_price_tab_after_multiple_date', $post_id, $date_key ); ?>
												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
													<div class="two-third">

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
						<!-- Template Script for dates -->
						<script type="text/html" id="tmpl-wp-travel-multiple-dates">
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading-{{data.random}}">
									<h4 class="panel-title">
										<div class="wp-travel-sorting-handle"></div>
											<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="false" aria-controls="collapse-{{data.random}}" class="collapsed">

												<span bind="wp_travel_multiple_dates_{{data.random}}"><?php echo esc_html( 'Multiple Date 1', 'wp-travel' ); ?></span>

												<!-- <span class="collapse-icon"></span> -->
											</a>
										<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
									</h4>
								</div>
								<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}" aria-expanded="true">
									<div class="panel-body">
										<div class="panel-wrap">
											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input class="wp-travel-variation-date-label" bind="wp_travel_multiple_dates_{{data.random}}" name="wp_travel_multiple_trip_dates[{{data.random}}][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
												</div>
											</div>
											<div class="repeat-row">
												<label class="one-third"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date date-input" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
													<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date date-input" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
												</div>
											</div>
											<?php do_action( 'wp_travel_price_tab_after_multiple_date_template', $post_id ); ?>
											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
												<div class="two-third">

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
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td colspan="2"><hr></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-title">
			<th colspan="2">
				<h3><?php echo esc_html( 'Tour Extras', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-content">
			<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = false, $key = 'wp_travel_tour_extras', $table_row = true ); ?>
		</tr>

		<?php
		$custom_payout_class = 'custom-payout-option-row';
		if ( 'yes' == $settings['partial_payment'] ) {
			$custom_payout_class = 'custom-payout-option-row global-enabled';
		}

		?>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?> <?php echo esc_attr( $custom_payout_class ); ?>">
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
		?>
		<?php
		// WP Travel Standard Paypal merged. since 1.2.1
		$wp_travel_minimum_partial_payout = wp_travel_minimum_partial_payout( $post_id );
		if ( $wp_travel_minimum_partial_payout < 1 ) {
			$wp_travel_minimum_partial_payout = '';
		}
		$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

		$trip_price = wp_travel_get_actual_trip_price( $post_id );

		$payout_percent = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_percent', true );
		if ( ! $payout_percent ) {
			$payout_percent = wp_travel_get_payout_percent( $post_id );
		}
		$use_global = wp_travel_use_global_payout_percent( $post_id );

		?>
		<tr style="display:none" class="<?php echo esc_attr( $custom_payout_class ); ?>" >
			<td><label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Minimum Payout', 'wp-travel' ); ?></label></td>
			<td>
				<span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span>
				<input type="number" step="0.01" name="wp_travel_minimum_partial_payout" id="wp-travel-minimum-partial-payout" value="<?php echo esc_attr( $wp_travel_minimum_partial_payout ); ?>" />
				<span class="description">
				<?php
				esc_html_e( 'Default : ', 'wp-travel' );
				echo sprintf( '%s&percnt; of %s%s', esc_html( $default_payout_percent ), esc_html( $currency_symbol ), esc_html( $trip_price ) );
				?>
				</span>
			</td>
		</tr>

		<tr class="price-option-row <?php echo esc_attr( sprintf( '%s %s %s', $single_pricing_option_class, $multiple_pricing_option_class, $custom_payout_class ) ); ?>">
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
					<p class="wp-travel-enable-sale description">
						<?php
							esc_html_e( 'Use Global ', 'wp-travel' );
							echo sprintf( '%s&percnt;', esc_html( $default_payout_percent ) );
						?>
					</p>
				</span>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?> <?php echo esc_attr( $custom_payout_class ); ?> "  >
			<td>
				<label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Custom Min. Payout (%)', 'wp-travel' ); ?></label>
			</td>
			<td>
				<input type="number" min="1" max="100" step="0.01" name="wp_travel_minimum_partial_payout_percent" id="wp-travel-minimum-partial-payout-percent" value="<?php echo esc_attr( $payout_percent ); ?>" />
			</td>
		</tr>
		<?php do_action( 'wp_travel_itinerary_price_tab_table_last_row', $post_id ); ?>
	</table>
	<?php
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'       => __( 'Need More Options ?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
}

function wp_travel_old_pricing_list_admin() {
	global $post;
	$post_id        = $post->ID;
	$date_format    = get_option( 'date_format' );
	$settings       = wp_travel_get_settings();
	$js_date_format = wp_travel_date_format_php_to_js();
	$pricing_types  = wp_travel_get_pricing_option_list();

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

	if ( $enable_sale ) {
		$sale_price_attribute   = '';
		$sale_price_style_class = '';
	}

	$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
	$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

	$price_per = get_post_meta( $post_id, 'wp_travel_price_per', true );
	if ( ! $price_per ) {
		$price_per = 'person';
	}

	// CSS Class for Single and Multiple Pricing option fields.
	$single_pricing_option_class   = 'single-price-option-row';
	$multiple_pricing_option_class = 'multiple-price-option-row';
	?>
	<table class="form-table pricing-tab">
		<tr class="table-inside-heading">
			<th colspan="2">
				<h3><?php echo esc_html( 'Pricing', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<?php if ( is_array( $pricing_types ) ) : ?>
			<?php if ( count( $pricing_types ) > 1 ) : ?>

				<tr class="pricing-option-title">
					<td><label for="wp-travel-pricing-option-type"><?php esc_html_e( 'Pricing Option', 'wp-travel' ); ?></label></td>
					<td>
						<select name="wp_travel_pricing_option_type" id="wp-travel-pricing-option-type">
							<?php foreach ( $pricing_types as $value => $pricing_label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $pricing_option_type, $value ); ?> ><?php echo esc_html( $pricing_label ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<?php
			else :
				$pricing_option_type = 'multiple-price';
				?>
				<input type="hidden" name="wp_travel_pricing_option_type" id="wp-travel-pricing-option-type" value="<?php echo esc_attr( $pricing_option_type ); ?>" >
			<?php endif; ?>
		<?php endif; ?>

		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-price-per"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label></td>
			<td>
				<?php $price_per_fields = wp_travel_get_price_per_fields(); ?>
				<?php if ( is_array( $price_per_fields ) && count( $price_per_fields ) > 0 ) : ?>
					<select name="wp_travel_price_per">
						<?php foreach ( $price_per_fields as $val => $label ) : ?>
							<option value="<?php echo esc_attr( $val ); ?>" <?php selected( $val, $price_per ); ?> ><?php echo esc_html( $label, 'wp-travel' ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php endif; ?>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-detail"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label></td>
			<td><input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" /></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-price"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label></td>
			<td><div class="field-price-currency-input"><span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input type="number" min="0.01" step="0.01" name="wp_travel_price" id="wp-travel-price" value="<?php echo esc_attr( $price ); ?>" /></div></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td><label for="wp-travel-enable-sale"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label></td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input name="wp_travel_enable_sale" type="checkbox" id="wp-travel-enable-sale" <?php checked( $enable_sale, 1 ); ?> value="1" />
						<span class="switch"></span>
					</label>
				</span>
				<p class="wp-travel-enable-sale description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $sale_price_style_class ); ?>">
			<td><label for="wp-travel-sale-price"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label></td>
			<td><div class="field-price-currency-input"><span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span><input <?php echo esc_attr( $sale_price_attribute ); ?> type="number" min="1" max="<?php echo esc_attr( $price ); ?>" step="0.01" name="wp_travel_sale_price" id="wp-travel-sale-price" value="<?php echo esc_attr( $sale_price ); ?>" /></div></td>
		</tr>

		<!-- Multiple Priceing field -->
		<tr class="price-option-row <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td  id="wp-travel-multiple-pricing-options" colspan="2" class="pricing-repeater"  >
				<div id="wp-travel-pricing-options" style="padding:20px 0; margin: 0 -10px;">
				<?php
				if ( is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) != 0 ) :
					$collapse_style = 'display:block';
				else :
					$collapse_style = 'display:none';
				endif;
				?>
					<div class="wp-collapse-open" style="<?php echo esc_attr( $collapse_style ); ?>">
						<a href="#" data-parent="wp-travel-multiple-pricing-options" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
						<a data-parent="wp-travel-multiple-pricing-options" style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
					</div>
					<p class="description"><?php echo esc_html__( 'Select different pricing category with its different sale price', 'wp-travel' ); ?></p>
					<div id="price-accordion" class="tab-accordion price-accordion wp-travel-accordion has-handler">
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
													<!-- <span class="collapse-icon"></span> -->
												</a>
											<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
										</h4>
									</div>
									<div id="collapse-<?php echo esc_attr( $key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $key ); ?>">
										<div class="panel-body">
											<div class="panel-wrap">
											<div class="repeat-row">
													<label for="pricing_name_<?php echo esc_attr( $key ); ?>" class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
													<div class="two-third">
														<div class="field-input">
															<input class="wp-travel-variation-pricing-name" id="pricing_name_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-name" required bind="pricing_option_<?php echo esc_attr( $key ); ?>" type="text" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][pricing_name]" value="<?php echo esc_attr( $pricing_name ); ?>">
															<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price_key]" value="<?php echo esc_attr( $pricing_key ); ?>">
															<p class="description"><?php echo esc_html__( 'Create a unique name for your pricing option', 'wp-travel' ); ?></p>
														</div>
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
																<option <?php selected( $pricing_type, $option ); ?> value="<?php echo esc_attr( $option ); ?>"><?php echo esc_html( $value ); ?></option>
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
													<div class="price-currency-input">
														<label for="price_<?php echo esc_attr( $key ); ?>" class="one-third"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
														<div class="two-third">
															<div class="field-price-currency-input">
																<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
																<input id="price_<?php echo esc_attr( $key ); ?>" bindPrice="pricing_variation_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-main-price" required value="<?php echo esc_attr( $pricing_option_price ); ?>" type="number" min="1" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price]" />
															</div>
														</div>
													</div>
												</div>

												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
													<div class="two-third">
														<span class="show-in-frontend checkbox-default-design">
															<label data-on="ON" data-off="OFF">
																<input name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][enable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale" <?php checked( $pricing_sale_enabled, 'yes' ); ?> value="yes">
																<span class="switch"></span>
															</label>
														</span>
														<p class="wp-travel-enable-sale wp-travel-enable-variation-price-sale description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>
													</div>
												</div>

												<div <?php echo esc_attr( $custom_pricing_sale_price_attribute ); ?> class="repeat-row <?php echo esc_attr( $custom_pricing_sale_price_class ); ?>">
													<label for="sale_price_<?php echo esc_attr( $key ); ?>" class="one-third"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
													<div class="two-third">
														<div class="field-price-currency-input">
															<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
															<input id="sale_price_<?php echo esc_attr( $key ); ?>" bindSale="pricing_variation_<?php echo esc_attr( $key ); ?>" class="wp-travel-variation-pricing-sale-price" type="number" min="1" max="<?php echo esc_attr( $pricing_option_price ); ?>" step="0.01" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][sale_price]" id="" value="<?php echo esc_attr( $pricing_sale_price ); ?>" <?php echo esc_attr( $pricing_sale_enabled == 'yes' ? 'required="required"' : '' ); ?>  />
														</div>
													</div>
												</div>

												<div class="repeat-row">
													<label for="price_per_<?php echo esc_attr( $key ); ?>" class="one-third"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
													<div class="two-third">
														<select id="price_per_<?php echo esc_attr( $key ); ?>" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][price_per]">
															<option value="person" <?php selected( $pricing_price_per, 'person' ); ?>><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
															<option value="group" <?php selected( $pricing_price_per, 'group' ); ?>><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
														</select>
													</div>
												</div>

												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
													<div class="two-third">
														<input class="pricing-opt-min-pax" value="<?php echo esc_attr( $pricing_min_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][min_pax]" placeholder="Min PAX"  min="1" />

														<input class="pricing-opt-max-pax" value="<?php echo esc_attr( $pricing_max_pax ); ?>" type="number" name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][max_pax]" placeholder="Max PAX"  min="<?php echo esc_attr( ( $pricing_min_pax ) ? $pricing_min_pax : 1 ); ?>" />
													</div>
												</div>
												<div class="repeat-row">
													<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $key ); ?>
												</div>
												<?php if ( class_exists( 'WP_Travel_Util_Inventory' ) && 'yes' === $enable_inventory_for_trip ) : ?>

													<div class="repeat-row">
														<label class="one-third"><?php esc_html_e( 'Enable Inventory', 'wp-travel' ); ?></label>
														<div class="two-third">
															<span class="show-in-frontend checkbox-default-design">
																<label data-on="ON" data-off="OFF">
																	<input name="wp_travel_pricing_options[<?php echo esc_attr( $key ); ?>][enable_inventory]" type="checkbox" class="" <?php checked( $enable_inventory, 'yes' ); ?> value="yes">
																	<span class="switch"></span>
																</label>
															</span>
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
				<div class="wp-travel-add-pricing-option clearfix text-right">
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
										<!-- <span class="collapse-icon"></span> -->
									</a>
								<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
							</h4>
						</div>
						<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}">
							<div class="panel-body">
								<div class="panel-wrap">
								<div class="repeat-row">
										<label for="pricing_name_{{data.random}}" class="one-third"><?php esc_html_e( 'Pricing Name', 'wp-travel' ); ?></label>
										<div class="two-third">
											<input class="wp-travel-variation-pricing-name" required="required" bind="pricing_option_{{data.random}}" type="text" id="pricing_name_{{data.random}}" name="wp_travel_pricing_options[{{data.random}}][pricing_name]" value="">
											<input class="wp-travel-variation-pricing-uniquekey" type="hidden" name="wp_travel_pricing_options[{{data.random}}][price_key]" value="">
											<p class="description"><?php echo esc_html__( 'Create a unique name for your pricing option', 'wp-travel' ); ?></p>
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
										<label for="price_{{data.random}}" class="one-third"><?php esc_html_e( 'Price', 'wp-travel' ); ?></label>
										<div class="two-third">
											<div class="field-price-currency-input">
												<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
												<input id="price_{{data.random}}" bindPrice="pricing_variation_{{data.random}}" required="required" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][price]">
											</div>
										</div>
									</div>

									<div class="repeat-row">
										<label class="one-third"><?php esc_html_e( 'Enable Sale', 'wp-travel' ); ?></label>
										<div class="two-third">
											<span class="show-in-frontend checkbox-default-design">
												<label data-on="ON" data-off="OFF">
													<input name="wp_travel_pricing_options[{{data.random}}][enable_sale]" type="checkbox" class="wp-travel-enable-variation-price-sale" value="yes">
													<span class="switch"></span>
												</label>
											</span>
											<p class="wp-travel-enable-sale wp-travel-enable-variation-price-sale description"><?php esc_html_e( 'Check to enable sale.', 'wp-travel' ); ?></p>
										</div>
									</div>

									<div class="repeat-row hidden">
										<label for="sale_price_{{data.random}}" class="one-third"><?php esc_html_e( 'Sale Price', 'wp-travel' ); ?></label>
										<div class="two-third">
											<div class="field-price-currency-input">
												<span class="wp-travel-currency-symbol"><?php echo esc_html( $currency_symbol ); ?></span>
												<input id="sale_price_{{data.random}}" bindSale="pricing_variation_{{data.random}}" type="number" min="1" step="0.01" name="wp_travel_pricing_options[{{data.random}}][sale_price]" />
											</div>
										</div>
									</div>

									<div class="repeat-row">
										<label for="price_per_{{data.random}}_{{data.category_id}}" class="one-third"><?php esc_html_e( 'Price Per', 'wp-travel' ); ?></label>
										<div class="two-third">
											<select id="price_per_{{data.random}}_{{data.category_id}}" name="wp_travel_pricing_options[{{data.random}}][price_per]">
												<option value="person"><?php esc_html_e( 'Person', 'wp-travel' ); ?></option>
												<option value="group"><?php esc_html_e( 'Group', 'wp-travel' ); ?></option>
											</select>
										</div>
									</div>

									<div class="repeat-row">
										<label class="one-third"><?php esc_html_e( 'Number of PAX', 'wp-travel' ); ?></label>
										<div class="two-third">
											<input class="pricing-opt-min-pax" type="number" name="wp_travel_pricing_options[{{data.random}}][min_pax]" placeholder="Min PAX"  min="1" />

											<input class="pricing-opt-max-pax" type="number" name="wp_travel_pricing_options[{{data.random}}][max_pax]" placeholder="Max PAX"  min="1" />
										</div>
									</div>

									<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = 'pricing_options', $key = '{{data.random}}' ); ?>

									<?php if ( class_exists( 'WP_Travel_Util_Inventory' ) && 'yes' === $enable_inventory_for_trip ) : ?>

										<div class="repeat-row">
											<label class="one-third"><?php esc_html_e( 'Enable Inventory', 'wp-travel' ); ?></label>
											<div class="two-third">
												<span class="show-in-frontend checkbox-default-design">
													<label data-on="ON" data-off="OFF">
														<input name="wp_travel_pricing_options[{{data.random}}][enable_inventory]" type="checkbox" class="" value="yes">
														<span class="switch"></span>
													</label>
												</span>
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
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td colspan="2"><hr></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<th colspan="2">
				<h3><?php esc_html_e( 'Dates', 'wp-travel' ); ?></h3>
			</th>
		</tr>

		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?>">
			<td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ); ?> />
						<span class="switch"></span>
					</label>
				</span>
			</td>
		</tr>
		<tr class="price-option-row wp-travel-trip-duration-row" >
			<td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
			<td>
				<input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Day(s)', 'wp-travel' ); ?>
				<input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night(s)', 'wp-travel' ); ?>
			</td>
		</tr>
		<tr class="price-option-row  <?php echo esc_attr( $multiple_pricing_option_class ); ?> wp-travel-enable-multiple-dates" >
			<td><label for="wp-travel-enable-multiple-fixed-departure"><?php esc_html_e( 'Enable Multiple Dates', 'wp-travel' ); ?></label></td>
			<td><span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" name="wp_travel_enable_multiple_fixed_departue" id="wp-travel-enable-multiple-fixed-departure" value="yes" <?php checked( 'yes', $multiple_fixed_departures ); ?> />
						<span class="switch"></span>
					</label>
				</span>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-fixed-departure-row" >
			<td><label for="wp-travel-start-date"><?php esc_html_e( 'Starting Date', 'wp-travel' ); ?></label></td>
			<td><input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" class="date-input" /></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-fixed-departure-row">
			<td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending Date', 'wp-travel' ); ?></label></td>
			<td><input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" autocomplete="off" type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" class="date-input" /></td>
		</tr>

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

				<div class="tab-accordion date-accordion wp-travel-accordion has-handler">
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
													<!-- <span class="collapse-icon"></span> -->
												</a>
											<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
										</h4>
									</div>

									<div id="collapse-<?php echo esc_attr( $date_key ); ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $date_key ); ?>" aria-expanded="true">
										<div class="panel-body">
											<div class="panel-wrap">
												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
													<div class="two-third">
														<input class="wp-travel-variation-date-label" value="<?php echo esc_attr( $date_label ); ?>"  bind="wp_travel_multiple_dates_<?php echo esc_attr( $date_key ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
													</div>
												</div>
												<div class="repeat-row">
													<label class="one-third"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
													<div class="two-third">
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $start_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date date-input" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
														<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" value="<?php echo esc_attr( $end_date ); ?>" name="wp_travel_multiple_trip_dates[<?php echo esc_attr( $date_key ); ?>][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date date-input" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
													</div>
												</div>
												<?php do_action( 'wp_travel_price_tab_after_multiple_date', $post_id, $date_key ); ?>
												<div class="repeat-row">
													<label class="one-third"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
													<div class="two-third">

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
						<!-- Template Script for dates -->
						<script type="text/html" id="tmpl-wp-travel-multiple-dates">
							<div class="panel panel-default">
								<div class="panel-heading" role="tab" id="heading-{{data.random}}">
									<h4 class="panel-title">
										<div class="wp-travel-sorting-handle"></div>
											<a role="button" data-toggle="collapse" data-parent="#pricing-options-data" href="#collapse-{{data.random}}" aria-expanded="false" aria-controls="collapse-{{data.random}}" class="collapsed">

												<span bind="wp_travel_multiple_dates_{{data.random}}"><?php echo esc_html( 'Multiple Date 1', 'wp-travel' ); ?></span>

												<!-- <span class="collapse-icon"></span> -->
											</a>
										<span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
									</h4>
								</div>
								<div id="collapse-{{data.random}}" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="heading-{{data.random}}" aria-expanded="true">
									<div class="panel-body">
										<div class="panel-wrap">
											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Add a Label', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input class="wp-travel-variation-date-label" bind="wp_travel_multiple_dates_{{data.random}}" name="wp_travel_multiple_trip_dates[{{data.random}}][date_label]" type="text" placeholder="<?php esc_html_e( 'Your Text Here', 'wp-travel' ); ?>" />
												</div>
											</div>
											<div class="repeat-row">
												<label class="one-third"><?php echo esc_html( 'Select a Date', 'wp-travel' ); ?></label>
												<div class="two-third">
													<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][start_date]" type="text" data-language="en" class=" wp-travel-multiple-start-date date-input" readonly placeholder="<?php echo esc_attr( 'Start Date', 'wp-travel' ); ?>" />
													<input data-date-format="<?php echo esc_attr( $js_date_format ); ?>" name="wp_travel_multiple_trip_dates[{{data.random}}][end_date]" type="text" data-language="en" class=" wp-travel-multiple-end-date date-input" readonly placeholder="<?php echo esc_attr( 'End Date', 'wp-travel' ); ?>" />
												</div>
											</div>
											<?php do_action( 'wp_travel_price_tab_after_multiple_date_template', $post_id ); ?>
											<div class="repeat-row">
												<label class="one-third"><?php esc_html_e( 'Select pricing options', 'wp-travel' ); ?></label>
												<div class="two-third">

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
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?>">
			<td colspan="2"><hr></td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-title">
			<th colspan="2">
				<h3><?php echo esc_html( 'Tour Extras', 'wp-travel' ); ?></h3>
			</th>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> wp-travel-tour-extra-content">
			<?php echo wp_travel_admin_tour_extra_multiselect( $post_id, $context = false, $key = 'wp_travel_tour_extras', $table_row = true ); ?>
		</tr>

		<?php
		$custom_payout_class = 'custom-payout-option-row';
		if ( 'yes' == $settings['partial_payment'] ) {
			$custom_payout_class = 'custom-payout-option-row global-enabled';
		}

		?>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?> <?php echo esc_attr( $custom_payout_class ); ?>">
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
		?>
		<?php
		// WP Travel Standard Paypal merged. since 1.2.1
		$wp_travel_minimum_partial_payout = wp_travel_minimum_partial_payout( $post_id );
		if ( $wp_travel_minimum_partial_payout < 1 ) {
			$wp_travel_minimum_partial_payout = '';
		}
		$default_payout_percent = ( isset( $settings['minimum_partial_payout'] ) && $settings['minimum_partial_payout'] > 0 ) ? $settings['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

		$trip_price = wp_travel_get_actual_trip_price( $post_id );

		$payout_percent = get_post_meta( $post_id, 'wp_travel_minimum_partial_payout_percent', true );
		if ( ! $payout_percent ) {
			$payout_percent = wp_travel_get_payout_percent( $post_id );
		}
		$use_global = wp_travel_use_global_payout_percent( $post_id );

		?>
		<tr style="display:none" class="<?php echo esc_attr( $custom_payout_class ); ?>" >
			<td><label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Minimum Payout', 'wp-travel' ); ?></label></td>
			<td>
				<span class="wp-travel-currency-symbol"><?php esc_html_e( $currency_symbol, 'wp-travel' ); ?></span>
				<input type="number" step="0.01" name="wp_travel_minimum_partial_payout" id="wp-travel-minimum-partial-payout" value="<?php echo esc_attr( $wp_travel_minimum_partial_payout ); ?>" />
				<span class="description">
				<?php
				esc_html_e( 'Default : ', 'wp-travel' );
				echo sprintf( '%s&percnt; of %s%s', esc_html( $default_payout_percent ), esc_html( $currency_symbol ), esc_html( $trip_price ) );
				?>
				</span>
			</td>
		</tr>

		<tr class="price-option-row <?php echo esc_attr( sprintf( '%s %s %s', $single_pricing_option_class, $multiple_pricing_option_class, $custom_payout_class ) ); ?>">
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
					<p class="wp-travel-enable-sale description">
						<?php
							esc_html_e( 'Use Global ', 'wp-travel' );
							echo sprintf( '%s&percnt;', esc_html( $default_payout_percent ) );
						?>
					</p>
				</span>
			</td>
		</tr>
		<tr class="price-option-row <?php echo esc_attr( $single_pricing_option_class ); ?> <?php echo esc_attr( $multiple_pricing_option_class ); ?> <?php echo esc_attr( $custom_payout_class ); ?> "  >
			<td>
				<label for="wp-travel-minimum-partial-payout"><?php esc_html_e( 'Custom Min. Payout (%)', 'wp-travel' ); ?></label>
			</td>
			<td>
				<input type="number" min="1" max="100" step="0.01" name="wp_travel_minimum_partial_payout_percent" id="wp-travel-minimum-partial-payout-percent" value="<?php echo esc_attr( $payout_percent ); ?>" />
			</td>
		</tr>
		<?php do_action( 'wp_travel_itinerary_price_tab_table_last_row', $post_id ); ?>
	</table>
	<?php
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$args = array(
			'title'       => __( 'Need More Options ?', 'wp-travel' ),
			'content'     => __( 'By upgrading to Pro, you can get additional trip specific features like Inventory Options, Custom Sold out action/message and Group size limits. !', 'wp-travel' ),
			'link'        => 'https://wptravel.io/wp-travel-pro/',
			'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
			'link2'       => 'https://wptravel.io/downloads/wp-travel-utilities/',
			'link2_label' => __( 'Get WP Travel Utilities Addon', 'wp-travel' ),
		);
		wp_travel_upsell_message( $args );
	endif;
}
