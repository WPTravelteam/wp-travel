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
$settings = wp_travel_get_settings();

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );

$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
$trip_end_date   = get_post_meta( $trip_id, 'wp_travel_end_date', true );
$trip_price      = wp_travel_get_trip_price( $trip_id );
$enable_sale     = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );

$trip_duration = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
$trip_duration_night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

$currency_code 	= ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
$per_person_text = wp_travel_get_price_per_text( $trip_id );
$sale_price      = wp_travel_get_trip_sale_price( $trip_id ); 

$wp_travel_enable_pricing_options = get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true );
$wp_travel_enable_multiple_fixed_departue = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );?>

<div id="<?php echo isset( $tab_key ) ? esc_attr( $tab_key ) : 'booking'; ?>" class="tab-list-content">
<?php
$enable_checkout = apply_filters( 'wp_travel_enable_checkout', true );
$available_pax = false;
$booked_pax = false;
$pax_limit = false;
$general_sold_out = false;
$inventory_enabled_for_option = false;

$force_checkout = apply_filters( 'wp_travel_is_force_checkout_enabled', false );

if ( ( $enable_checkout  ) || $force_checkout ) :

	$status_col = false;

	if( class_exists( 'WP_Travel_Util_Inventory' ) ) {

		$inventory = new WP_Travel_Util_Inventory();
	
		$status_col = get_post_meta( $trip_id, 'status_column_enable', true );

		$status_col = ( $status_col && 'no' === $status_col ) ? false : true;


		$available_pax = $inventory->get_available_pax( $trip_id );
		$available_pax = apply_filters( 'wp_travel_available_pax', $available_pax, $trip_id, '' );
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
			'{booked_pax}'    => $booked_pax,
			'{pax_limit}'     => $pax_limit,
			
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
	$trip_multiple_dates_data = get_post_meta( $post->ID, 'wp_travel_multiple_trip_dates', true );

	if ( 'yes' === $wp_travel_enable_pricing_options && is_array( $trip_pricing_options_data ) && count( $trip_pricing_options_data ) !== 0 ) :

		$list_type = isset( $settings['trip_pricing_options_layout'] )  ? $settings['trip_pricing_options_layout'] : 'by-pricing-option';

		if ( $list_type === 'by-pricing-option' ) {
				// Default pricing options template.
				do_action( 'wp_travel_booking_princing_options_list', $trip_pricing_options_data );

		} else {
			if ( 'yes' === $wp_travel_enable_multiple_fixed_departue && 'yes' === $fixed_departure && ( ! empty( $trip_multiple_dates_data ) && is_array( $trip_multiple_dates_data ) ) ) {
				// Date listing template.
				do_action( 'wp_travel_booking_departure_date_list', $trip_multiple_dates_data );
			} else {
				do_action( 'wp_travel_booking_princing_options_list', $trip_pricing_options_data );
			}
		}
		
	else : ?>
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
								<input placeholder="<?php esc_html_e( 'Arrival date', 'wp-travel' ); ?>" name="trip_date" type="text" class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
							</div>
							<div class="date-to">
								<input placeholder="<?php esc_html_e( 'Departure date', 'wp-travel' ); ?>" name="trip_departure_date" type="text" class="wp-travel-pricing-dates" required data-parsley-trigger="change" data-parsley-required-message="<?php echo esc_attr__( 'Please Select a Date', 'wp-travel' ); ?>">
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
						<?php
						if ( class_exists( 'WP_Travel_Util_Inventory' ) )  :
							// display price unavailable text						
							$no_price_text 	= isset( $settings['price_unavailable_text'] ) && '' !== $settings['price_unavailable_text'] ? $settings['price_unavailable_text'] : '';
							echo esc_html( $no_price_text );
						else : ?>
							<div class="price">
								<span class="availabily-heading-label"><?php echo esc_html__( 'price:', 'wp-travel' ); ?></span>
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
							</div>
						<?php endif; ?>
						<div class="action">
							<?php if ( $inventory_enabled_for_option && $general_sold_out ) : ?>
										
								<p class="wp-travel-sold-out"><?php echo $sold_out_btn_rep_msg; ?></p>
										
							<?php else : ?>
								<?php $pax = 1; ?>

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
				<?php do_action('wp_travel_trip_extras'); ?>
			</div>
		</div>
	<?php endif; ?>
<?php else : ?>
	<?php  echo wp_travel_get_booking_form(); ?>
<?php endif; ?>
</div>