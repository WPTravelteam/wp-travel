<?php 
/**
 * Itinerary Pricing Options Template
 *
 * This template can be overridden by copying it to yourtheme/wp-travel/content-pricing-options.php.
 *
 * HOWEVER, on occasion wp-travel will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * @see 	    http://docs.wensolutions.com/document/template-structure/
 * @author      WenSolutions
 * @package     wp-travel/Templates
 * @since       1.1.5
 */
global $post;
global $wp_travel_itinerary;

$trip_id = $post->ID;

$no_details_found_message = '<p class="wp-travel-no-detail-found-msg">' . __( 'No details found.', 'wp-travel' ) . '</p>';
$trip_content	= $wp_travel_itinerary->get_content() ? $wp_travel_itinerary->get_content() : $no_details_found_message;
$trip_outline	= $wp_travel_itinerary->get_outline() ? $wp_travel_itinerary->get_outline() : $no_details_found_message;
$trip_include	= $wp_travel_itinerary->get_trip_include() ? $wp_travel_itinerary->get_trip_include() : $no_details_found_message;
$trip_exclude	= $wp_travel_itinerary->get_trip_exclude() ? $wp_travel_itinerary->get_trip_exclude() : $no_details_found_message;
$gallery_ids 	= $wp_travel_itinerary->get_gallery_ids();

$wp_travel_itinerary_tabs = wp_travel_get_frontend_tabs();

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );
$trip_price      = wp_travel_get_trip_price( $trip_id );
$enable_sale     = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );

$trip_duration = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

$settings = wp_travel_get_settings();
$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
$per_person_text = wp_travel_get_price_per_text( $trip_id );
$sale_price      = wp_travel_get_trip_sale_price( $trip_id ); 

$wp_travel_enable_pricing_options = get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true ); ?>

<div id="<?php echo isset( $tab_key ) ? esc_attr( $tab_key ) : 'booking'; ?>" class="tab-list-content">
<?php
$enable_checkout = apply_filters( 'wp_travel_enable_checkout', true );
$available_pax = false;
$booked_pax = false;
$pax_limit = false;
$general_sold_out = false;
$inventory_enabled_for_option = false;

if ( $enable_checkout && wp_travel_is_payment_enabled() && 0 !== $trip_price ) :

	$status_column = false;
	$status_col = false;

	if( class_exists( 'WP_Travel_Util_Inventory' ) ) {

		$inventory = new WP_Travel_Util_Inventory();
	
		$status_col = get_post_meta( $trip_id, 'status_column_enable', true );

		$status_col = ( $status_col && 'no' === $status_col ) ? false : true;

		
		$available_pax = $inventory->get_available_pax( $trip_id );
		$booked_pax = $inventory->get_booking_pax_count( $trip_id );
		$pax_limit = $inventory->get_pax_limit( $trip_id );
		
		$status_msg = get_post_meta( $trip_id, 'wp_travel_inventory_status_message_format', true );

		$inventory_enabled_for_option = $inventory->is_inventory_enabled( $trip_id );
		
		if ( ! $status_msg ) {
			$status_msg = __('Pax Available: {available_pax} / {pax_limit}', 'wp-travel');
		}
		
		if ( ! $inventory_enabled_for_option || 0 === $pax_limit ) {

			$status_msg = __( 'N/A', 'wp-travel' );

		}
		$general_status_tags = array(

			'{available_pax}' => $available_pax,
			'{booked_pax}' => $booked_pax,
			'{pax_limit}' => $pax_limit,
			
		);

		$general_status_msg = str_replace( array_keys( $general_status_tags ), $general_status_tags, $status_msg );

		$general_sold_out = $available_pax === 0 ? true : false;

		$wp_travel_inventory_sold_out_action = get_post_meta( $trip_id, 'wp_travel_inventory_sold_out_action', true );

		$sold_out_btn_rep_msg = get_post_meta( $trip_id, 'wp_travel_inventory_sold_out_message', true );
		
		if ( 'allow_trip_enquiry' === $wp_travel_inventory_sold_out_action ) {
			$sold_out_btn_rep_msg = wp_travel_utilities__get_inquiry_link();
		}

	}
	
	$trip_pricing_options_data = get_post_meta( $post->ID, 'wp_travel_pricing_options', true );

	if ( 'yes' === $wp_travel_enable_pricing_options && is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) !== 0 ) :

		if ( is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) !== 0 ) : ?>
			<div id="wp-travel-date-price" class="detail-content">
				<div class="availabily-wrapper">
					<ul class="availabily-list additional-col">
						<li class="availabily-heading clearfix">
								<div class="date-from">
									<?php echo esc_html__( 'Pricing Name', 'wp-travel' ); ?>
								</div>
							<div class="status">
								<?php echo esc_html__( 'Min Group Size', 'wp-travel' ); ?>
							</div>
							<div class="status">
								<?php echo esc_html__( 'Max Group Size', 'wp-travel' ); ?>
							</div>
							<?php if( $status_col ) : ?>

								<div class="status">
									<?php echo esc_html__( 'Status', 'wp-travel' ); ?>
								</div>

							<?php endif; ?>
							<div class="price">
								<?php echo esc_html__( 'Price', 'wp-travel' ); ?>
							</div>
							<div class="action">
								&nbsp;
							</div>
						</li>
						<?php foreach ( $trip_pricing_options_data as $price_key => $pricing ) :
							// Set Vars.
							$pricing_name         = isset( $pricing['pricing_name'] ) ? $pricing['pricing_name'] : '';
							$price_key          = isset( $pricing['price_key'] ) ? $pricing['price_key'] : '';
							$pricing_type         = isset( $pricing['type'] ) ? $pricing['type'] : '';
							$pricing_custom_label = isset( $pricing['custom_label'] ) ? $pricing['custom_label'] : '';
							$pricing_option_price = isset( $pricing['price'] ) ? $pricing['price'] : '';
							$pricing_sale_enabled = isset( $pricing['enable_sale'] ) ? $pricing['enable_sale'] : '';
							$pricing_sale_price   = isset( $pricing['sale_price'] ) ? $pricing['sale_price'] : '';
							$pricing_min_pax      = isset( $pricing['min_pax'] ) ? $pricing['min_pax'] : '';
							$pricing_max_pax      = isset( $pricing['max_pax'] ) ? $pricing['max_pax'] : '';

							$available_dates = wp_travel_get_pricing_variation_start_dates( $post->ID, $price_key );

							$pricing_sold_out = false;

							if( class_exists( 'WP_Travel_Util_Inventory' ) ) {

								$available_pax = $inventory->get_available_pax( $trip_id, $price_key );
								$booked_pax = $inventory->get_booking_pax_count( $trip_id, $price_key );
								$pax_limit = $inventory->get_pricing_option_pax_limit( $trip_id, $price_key );

								$pricing_status_tags = array(

									'{available_pax}' => $available_pax,
									'{booked_pax}' => $booked_pax,
									'{pax_limit}' => $pax_limit,
									
								);

								//Admin message.
								$pricing_status_msg = str_replace( array_keys( $pricing_status_tags ), $pricing_status_tags, $status_msg );

								$inventory_enabled_for_option = $inventory->is_inventory_enabled( $trip_id, $price_key );

								if ( ! $inventory_enabled_for_option ) {

									$pricing_status_msg = __( 'N/A', 'wp-travel' );

								}

								$pricing_sold_out = $available_pax === 0 ? true : false;
								
							}

						?>
						<li id="pricing-<?php echo esc_attr( $price_key ); ?>" class="availabily-content clearfix">
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Pricing Name:', 'wp-travel' ); ?></span> <span><?php echo esc_html( $pricing_name ); ?></span>
							</div>
							<div class="status">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Min Group Size:', 'wp-travel' ); ?></span>
								<span><?php echo ! empty( $pricing_min_pax ) ? esc_html( $pricing_min_pax . ' pax' ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
							</div>
							<div class="status">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Max Group Size:', 'wp-travel' ); ?></span>
								<span><?php echo ! empty( $pricing_max_pax ) ? esc_html( $pricing_max_pax . ' pax' ) : esc_html__( 'No size limit', 'wp-travel' ); ?></span>
							</div>
							<?php if( $status_col ) : ?>

							<div class="status">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
								<span><?php echo esc_html( $pricing_status_msg ); ?></span>
							</div>

							<?php endif; ?>
							<div class="price">
								<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
								<?php if ( '' !== $pricing_option_price || '0' !== $pricing_option_price ) : ?>

									<?php if ( 'yes' === $pricing_sale_enabled ) : ?>
										<del>
											<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price ); ?></span>
										</del>
									<?php endif; ?>
										<span class="person-count">
											<ins>
												<span>
													<?php
													if ( 'yes' === $pricing_sale_enabled ) {
														echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $pricing_sale_price ), $currency_symbol, $pricing_sale_price );
													} else {
														echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $pricing_option_price ), $currency_symbol, $pricing_option_price );
													}
													?>
												</span>
											</ins>/<?php echo esc_html( $per_person_text ); ?>
										</span>
								<?php endif; ?>
							</div>
							<div class="action">
							
								<?php if ( $pricing_sold_out ) : ?>
									
									<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
									
								<?php else : ?>
									<a href="#0" class="btn btn-primary btn-sm btn-inverse show-booking-row"><?php echo esc_html__( 'Select', 'wp-travel' ); ?></a>
								<?php endif; ?>
							</div>
							<div class="wp-travel-booking-row">
								<div class="wp-travel-calender-column no-padding ">
								<?php if ( $available_dates ) : ?>
									<label for=""><?php echo esc_html__( 'Select a Date:', 'wp-travel' ); ?></label>
									<input name="trip_date" type="text" data-available-dates="<?php echo esc_attr( wp_json_encode( $available_dates ) ); ?>" readonly class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
								<?php 
								else : ?>
									<label for=""><?php echo esc_html__( 'Select a Date:', 'wp-travel' ); ?></label>
									<input name="trip_date" type="text" readonly class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
								<?php endif;
								?>
								</div>
								<div class="wp-travel-calender-aside">
								<?php 
								$pricing_default_types = wp_travel_get_pricing_variation_options();

								$pricing_type_label = ( 'custom' === $pricing_type ) ? $pricing_custom_label : $pricing_default_types[ $pricing_type ];

								$max_attr = '';
								$min_attr = 'min=1';
								if ( '' !== $pricing_max_pax ) {

									$max_attr = 'max=' . $pricing_max_pax;
								}
								if ( '' !== $pricing_min_pax ) {
									$min_attr = 'min=' . $pricing_min_pax;
								}

								?>
									<div class="col-sm-3">
										<label for=""><?php echo esc_html( ucfirst( $pricing_type_label ) ); ?></label>
										<input name="pax" type="number" <?php echo esc_attr( $min_attr ); ?> <?php echo esc_attr( $max_attr ); ?> placeholder="<?php echo esc_attr__( 'size', 'wp-travel' ); ?>" required data-parsley-trigger="change">
									</div>
									<div class="add-to-cart">
											<input type="hidden" name="trip_id" value="<?php echo esc_attr( get_the_ID() ) ?>" />
											<input type="hidden" name="price_key" value="<?php echo esc_attr( $price_key ) ?>" />
									<?php 
										$button = '<a href="%s" data-parent-id="pricing-'.esc_attr( $price_key ).'" class="btn add-to-cart-btn btn-primary btn-sm btn-inverse">%s</a>';
										$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
										if ( 'yes' !== $fixed_departure ) :
											$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url ); ?>
											<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ) ?>" />
										<?php										
										endif;
										
										$cart_url = add_query_arg( 'price_key', $price_key, $cart_url );
										printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
									?>
									</div>
								</div>
							</div>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
		<?php endif; ?>
	<?php else : ?>
		<div id="wp-travel-date-price" class="detail-content">
			<div class="availabily-wrapper">
				<ul class="availabily-list <?php echo 'yes' === $fixed_departure ? 'additional-col' : ''; ?>">
					<li class="availabily-heading clearfix">
							<div class="date-from">
								<?php echo esc_html__( 'Start', 'wp-travel' ); ?>
							</div>
							<div class="date-to">
								<?php echo esc_html__( 'End', 'wp-travel' ); ?>
							</div>
						<div class="status">
							<?php echo esc_html__( 'Group Size', 'wp-travel' ); ?>
						</div>
						<?php if( $status_col ) : ?>

								<div class="status">
									<?php echo esc_html__( 'Status', 'wp-travel' ); ?>
								</div>

							<?php endif; ?>
						<div class="price">
							<?php echo esc_html__( 'Price', 'wp-travel' ); ?>
						</div>
						<div class="action">
							&nbsp;
						</div>
					</li>
					<li class="availabily-content clearfix" id="trip-duration-content">
						<?php if ( 'yes' == $fixed_departure ) : ?>
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html__( 'start:', 'wp-travel' ); ?></span>
								<?php echo esc_html( date_i18n( 'l', strtotime( $trip_start_date ) ) );  ?>
								<?php $date_format = get_option( 'date_format' ); ?>
								<?php if ( ! $date_format ) : ?>
									<?php $date_format = 'jS M, Y'; ?>
								<?php endif; ?>
								<span><?php echo esc_html( date_i18n( $date_format, strtotime( $trip_start_date ) ) );  ?></span>
								<input type="hidden" name="trip_date" value="<?php echo esc_attr( $trip_start_date ); ?>">
							</div>
							<div class="date-to">
								<span class="availabily-heading-label"><?php echo esc_html__( 'end:', 'wp-travel' ); ?></span>
								<?php echo esc_html( date_i18n( 'l', strtotime( $trip_end_date ) ) );  ?>
								<span><?php echo esc_html( date_i18n( $date_format, strtotime( $trip_end_date ) ) );  ?></span>
								<input type="hidden" name="trip_departure_date" value="<?php echo esc_attr( $trip_end_date ); ?>">
							</div>
						<?php else : ?>
							<div class="date-from">
								<span class="availabily-heading-label"><?php echo esc_html__( 'start:', 'wp-travel' ); ?></span>
								<input placeholder="<?php esc_html_e( 'Arrival date', 'wp-travel' ); ?>" name="trip_date" type="text" readonly class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
							</div>
							<div class="date-to">
								<input placeholder="<?php esc_html_e( 'Departure date', 'wp-travel' ); ?>" name="trip_departure_date" type="text" readonly class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
							</div>
						<?php endif; ?>
						<div class="status">
							<span class="availabily-heading-label"><?php echo esc_html__( 'Group Size:', 'wp-travel' ); ?></span>
							<span><?php echo esc_html( wp_travel_get_group_size() ); ?></span>
						</div>
						<?php if( $status_col ) : ?>

							<div class="status">
								<span class="availabily-heading-label"><?php echo esc_html__( 'Status:', 'wp-travel' ); ?></span>
								<span><?php echo esc_html( $general_status_msg ); ?></span>
							</div>

							<?php endif; ?>
						<div class="price">
							<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
							<?php if ( '' != $trip_price || '0' != $trip_price ) : ?>

								<?php if ( $enable_sale ) : ?>
									<del>
										<span><?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?></span>
									</del>
								<?php endif; ?>
									<span class="person-count">
										<ins>
											<span>
												<?php
												if ( $enable_sale ) {
													echo apply_filters( 'wp_travel_itinerary_sale_price', sprintf( ' %s %s', $currency_symbol, $sale_price ), $currency_symbol, $sale_price );
												} else {
													echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price );
												}
												?>
											</span>
										</ins>/<?php echo esc_html( $per_person_text ); ?>
									</span>
							<?php endif; ?>
						</div>
						<div class="action">
							<?php if ( $inventory_enabled_for_option && $general_sold_out ) : ?>
										
								<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
										
							<?php else : ?>
								<?php
								$pax = 1;
								// $price_per_text = strtolower( wp_travel_get_price_per_text($trip_id) );
								// if ( 'person' == $price_per_text ) {
								// 	$wp_travel_itinerary = new WP_Travel_Itinerary( get_post( $post_id ) );
								// 	$group_size = $wp_travel_itinerary->get_group_size();
								// 	if ( $group_size ) {
								// 		$pax = $group_size;
								// 	}
								// }
								?>

									<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ) ?>">
									<input type="hidden" name="pax" value="<?php echo esc_attr( $pax ) ?>">
									<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ) ?>">
									<input type="hidden" name="trip_duration_night" value="<?php echo esc_attr( $trip_duration_night ) ?>">
									<?php 
										$button = '<a href="%s" class="btn btn-primary add-to-cart-btn btn-sm btn-inverse" data-parent-id="trip-duration-content">%s</a>';
										$cart_url = add_query_arg( 'trip_id', get_the_ID(), wp_travel_get_cart_url() );
										if ( 'yes' !== $fixed_departure ) :
											$cart_url = add_query_arg( 'trip_duration', $trip_duration, $cart_url );
										endif;
										printf( $button, esc_url( $cart_url ), esc_html__( 'Book now', 'wp-travel' ) );
									?>
							<?php endif; ?>
						</div>
					</li>
				</ul>
			</div>
		</div>
	<?php endif; ?>
<?php else : ?>
	<?php  echo wp_travel_get_booking_form(); ?>
<?php endif; ?>
</div>