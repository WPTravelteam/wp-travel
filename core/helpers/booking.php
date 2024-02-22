<?php
/**
 * Helpers Booking.
 *
 * @package WP_Travel
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Booking class.
 *
 * @since 5.0.0
 */
class WpTravel_Helpers_Booking {
 // @phpcs:ignore

	/**
	 * Generate HTML for Booking Details.
	 *
	 * @param int $booking_id Trip Booking ID.
	 * @since 5.0.0
	 * @return mixed
	 */
	public static function render_booking_details( $booking_id ) {
		if ( class_exists( 'WpTravel_Helpers_Strings' ) ) {
			$strings = WpTravel_Helpers_Strings::get();
		}
		global $wt_cart;
		$items = $wt_cart->getItems();
		$coupon_applied = false;

		
		if ( ! $items ) {
			$items = get_post_meta( $booking_id, 'order_items_data', true );
		}

		if ( ! $items ) {
			return;
		}

		ob_start();
		?>
		<h2 class="wp-travel-order-heading"><?php esc_html_e( 'Booking Details', 'wp-travel' ); ?></h2>

		<table class="wp-travel-table-content" cellpadding="0" cellspacing="0" height="100%" width="100%" style="text-align: left;">
			<thead>
				<tr>
					<th><?php echo apply_filters( 'wp_travel_booking_mail_itinerary', esc_html( 'Itinerary', 'wp-travel' ), $booking_id ); ?></th>
					<th><?php echo apply_filters( 'wp_travel_booking_mail_pax', esc_html( ! empty( $strings ) ? strtoupper( $strings['bookings']['pax'] ) : 'PAX', 'wp-travel' ), $booking_id ); ?></th>
					<th><?php echo apply_filters( 'wp_travel_booking_mail_departure', esc_html( 'Departure Date', 'wp-travel' ), $booking_id ); ?></th>
					<th><?php echo apply_filters( 'wp_travel_booking_mail_arrival', esc_html( 'Arrival Date', 'wp-travel' ), $booking_id ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Order Details.
				global $wpdb;
				foreach ( $items as $item_key => $trip ) {
					$trip_id = $trip['trip_id'];
					$trip_data = WpTravel_Helpers_Trips::get_trip($trip_id)['trip'];
					// Values
					$title          = get_the_title( $trip_id );
					$pax            = 0;

					$arrival_date   = isset( $trip['departure_date'] ) && ! empty( $trip['departure_date'] ) ? wptravel_format_date( $trip['departure_date'] ) : '';
					
					$start_date   = isset( $trip['arrival_date'] ) && ! empty( $trip['arrival_date'] ) ? wptravel_format_date( $trip['arrival_date'] ) : '';
					$end_date = isset( $trip['departure_date'] ) && ! empty( $trip['departure_date'] ) ? wptravel_format_date( $wpdb->get_row( $wpdb->prepare("SELECT * FROM {$wpdb->prefix}wt_dates WHERE id=%s;", isset( $trip['date_id'] ) ? $trip['date_id'] : ''  ) )->end_date ) : '';
					
					/**
					 * Fix for active date format that skips character.
					 * Coverts original date instead of date from active date format to DateTime object.
					 * 
					 * @example Español : j \d\e F \d\e Y
					 * 
					 */
					$departure_date_raw = isset( $trip['arrival_date'] ) && ! empty( $trip['arrival_date'] ) ? $trip['arrival_date'] : '';
					$departure_date = date_create( $departure_date_raw );

					date_add( $departure_date,date_interval_create_from_date_string( ( (int) $trip_data['trip_duration']['days'] - 1 )." days" ) );
					$departure_date = date_format($departure_date,"F j, Y");
					$pricing_id   = $trip['pricing_id'];
					$pricing_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, $pricing_id );

					$pricing_title = '';
					if ( ! is_wp_error( $pricing_data ) && isset( $pricing_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_data['code'] ) {
						$pricing       = $pricing_data['pricings'];
						$pricing_title = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
					}
					
					$pax_price_total = 0;
					$extras_price_total = 0;
					?>
					<tr>
						<td>
							<a href="<?php echo esc_url( get_permalink( $trip_id ) ); ?>"><strong><?php echo esc_html( $title ); ?></strong></a>
							<br>
							<span class="my-order-pricing"><?php echo esc_html( $pricing_title ); ?></span>
							<span class="my-order-tail">
								<?php if ( ! empty( $trip['trip'] ) ) : ?>
									<?php
									foreach ( $trip['trip'] as $category_id => $t ) :
										if ( $t['pax'] < 1 ) {
											continue;
										}
										$pax = $pax + $t['pax'];
										$pax_price_total = $pax_price_total + ( $t['pax'] * $t['price'] );
									?>
										<span class="my-order-price-detail">(<?php echo esc_html( $t['pax'] ) . ' ' . $t['custom_label'] . ' x ' . wptravel_get_formated_price_currency( $t['price'], false, '', $booking_id ); ?>) <?php echo wptravel_get_formated_price_currency( $t['pax'] * $t['price'], false, '', $booking_id ); //@phpcs:ignore ?></span>
									<?php endforeach; ?>
								<?php endif; ?>
							</span>
							<?php
							if ( isset( $trip['trip_extras'] ) && isset( $trip['trip_extras']['id'] ) && count( $trip['trip_extras']['id'] ) > 0 ) :
									$extras = $trip['trip_extras'];
								?>
									<div class="my-order-price-breakdown-additional-service">
										<span><strong><?php esc_html_e( 'Additional Services', 'wp-travel' ); ?></strong></span>
										<?php
										foreach ( $trip['trip_extras']['id'] as $k => $extra_id ) :

											$trip_extras_data = get_post_meta( $extra_id, 'wp_travel_tour_extras_metas', true );

											$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : false;
											$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

											if ( $sale_price ) {
												$price = $sale_price;
											}
											$price = WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $price );
											$qty   = isset( $extras['qty'][ $k ] ) && $extras['qty'][ $k ] ? $extras['qty'][ $k ] : 1;

											$total = $price * $qty;

											$extras_price_total = $extras_price_total + ( $price * $qty );
											?>
											<div class="my-order-price-breakdown-additional-service-item clearfix">
												<span class="my-order-head"><?php echo esc_html( get_the_title( $extra_id ) ); ?> (<?php echo esc_attr( $qty ) . ' x ' . wptravel_get_formated_price_currency( $price, false, '', $booking_id ); ?>)</span>
												<span class="my-order-tail my-order-right"><?php echo wptravel_get_formated_price_currency( $total, false, '', $booking_id ); //@phpcs:ignore ?></span>
											</div>
										<?php endforeach; ?>

									</div>

									<?php
								endif;
							?>
						</td>
						<td><?php echo apply_filters( 'wp_travel_booking_mail_pax_val', esc_html( $pax ), $booking_id ); ?></td>
					
						<?php if( !$trip_data['is_fixed_departure'] ): ?>
							<?php if( isset( $trip_data['trip_duration']["duration_format"] ) && $trip_data['trip_duration']["duration_format"] == 'hour_minute' ): ?>
								<td><?php echo esc_html( $start_date ); ?></td>
								<td><?php echo esc_html( $start_date ); ?></td>
								<?php else: ?>
								<td><?php echo esc_html( $start_date ); ?></td>
								<?php if( (int) $trip_data['trip_duration']['days'] !== 0 ): ?>
									<td><?php echo esc_html( wptravel_format_date( $departure_date ) ); ?></td>
								<?php endif; ?>
							<?php endif; ?>
							<?php else: ?>
								<td><?php echo esc_html( $start_date ); ?></td>
								<?php if( isset( $trip_data['dates'] ) && $trip_data['dates'][0]['is_recurring'] == false ): ?>
									<td><?php echo esc_html( $end_date ); ?></td>
									<?php else: ?>
									<td>
										<?php if( $trip_data['trip_duration']['days'] ): ?>
											<?php echo esc_html( $departure_date ); ?>
											<?php else: ?>
												<?php echo __( 'N\A', 'wp-travel' ); ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>
						<?php endif; ?>
					</tr>
					
					<?php
				}
				if( isset( $trip['discount_type'] ) ){
					$coupon_applied = true;
					$coupon_type = $trip['discount_type'];
					$coupon_code = $trip['coupon_code'];
					$coupon_value = $trip['discount'];
				}
				?>
			
		
		<?php
		if( $coupon_applied ){
		?>	
			<tr>
				<th colspan="4"><h4><?php esc_html_e( 'Coupon Applied', 'wp-travel-pro' ); ?></h4></th>
			</tr>

			<tr>
				<td colspan="2"><p><b><?php esc_html_e( 'Coupon Code:', 'wp-travel-pro' ); ?></b> <?php echo esc_html($coupon_code); ?></p></td>
				<td colspan="2">
				<p><b><?php esc_html_e( 'Discount:', 'wp-travel-pro' ); ?></b> <?php echo wptravel_get_formated_price_currency( $coupon_value, false, '', $booking_id ); //@phpcs:ignore ?></p>
				</td>
			</tr>
			
			</tbody>
		</table>
		<?php }

		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	/**
	 * Generate HTML for Traveler Details.
	 *
	 * @param int $booking_id Trip Booking ID.
	 * @since 5.0.0
	 * @return mixed
	 */
	public static function render_traveler_details( $booking_id ) {
		global $wt_cart;
		$items = $wt_cart->getItems();

		if ( ! $items ) {
			$items = get_post_meta( $booking_id, 'order_items_data', true );
		}

		if ( ! $items ) {
			return;
		}

		// Consist of traveler, billing details.
		$checkout_form_data = get_post_meta( $booking_id, 'order_data', true );

		ob_start();
		?>
		<h2 class="wp-travel-order-heading"><?php esc_html_e( 'Traveler Details', 'wp-travel' ); ?></h2>

		<table class="wp-travel-table-content" cellpadding="0" cellspacing="0" height="100%" width="100%" style="text-align: left;">
			
			<tbody>
				<?php
				// Order Details.
				// array_count_values( $checkout_form_data['wp_travel_fname_traveller'] );

				$traveler_first_names = isset( $checkout_form_data['wp_travel_fname_traveller'] ) ? $checkout_form_data['wp_travel_fname_traveller'] : array();
				$traveler_last_names  = isset( $checkout_form_data['wp_travel_lname_traveller'] ) ? $checkout_form_data['wp_travel_lname_traveller'] : array();
				$traveler_countries   = isset( $checkout_form_data['wp_travel_country_traveller'] ) ? $checkout_form_data['wp_travel_country_traveller'] : array();
				$traveler_phones      = isset( $checkout_form_data['wp_travel_phone_traveller'] ) ? $checkout_form_data['wp_travel_phone_traveller'] : array();
				$traveler_emails      = isset( $checkout_form_data['wp_travel_email_traveller'] ) ? $checkout_form_data['wp_travel_email_traveller'] : array();
				$traveler_dobs        = isset( $checkout_form_data['wp_travel_date_of_birth_traveller'] ) ? $checkout_form_data['wp_travel_date_of_birth_traveller'] : array();
				$traveler_genders     = isset( $checkout_form_data['wp_travel_gender_traveller'] ) ? $checkout_form_data['wp_travel_gender_traveller'] : array();
				if ( count( $items ) > 1 ) {
					
					$indexs = 1;
					foreach ( $items as $item_key => $trip ) {
						$trip_id = $trip['trip_id'];

						// Values.
						$title        = get_the_title( $trip_id );
						$pricing_id   = $trip['pricing_id'];
						$pricing_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, $pricing_id );

						$pricing_title = '';
						if ( ! is_wp_error( $pricing_data ) && isset( $pricing_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_data['code'] ) {
							$pricing       = $pricing_data['pricings'];
							$pricing_title = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
						}
						if ( count( $traveler_first_names ) < 2 ) {
						?>
						<thead>
							<tr>
								<th colspan="6"><?php esc_html_e( 'Trip ' . $indexs . ' : ', 'wp-travel' ); ?> <strong><?php echo esc_html( $title ); ?></strong> / <span class="my-order-pricing"><?php echo esc_html( $pricing_title ); ?></span></th>
							</tr>
							<?php $indexs++; } } if ( count( $traveler_first_names ) < 2 ) { ?>
							<tr>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_name', esc_html_e( 'Traveler Name', 'wp-travel' ) ); ?></th>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_country', esc_html_e( 'Country', 'wp-travel' ) ); ?></th>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_phone', esc_html_e( 'Phone No.', 'wp-travel' ) ); ?></th>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_email', esc_html_e( 'Email', 'wp-travel' ) ); ?></th>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_dob', esc_html_e( 'DOB', 'wp-travel' ) ); ?></th>
								<th><?php apply_filters( 'wp_travel_booking_mail_trv_gender', esc_html_e( 'Gender', 'wp-travel' ) ); ?></th>
							</tr>
						</thead><?php } ?>
						<tbody>
							<?php
							// print_r( $items ) ;
							if ( count( $traveler_first_names ) > 1 ) {
								foreach ( $traveler_first_names as $key => $first_name ) {
									$last_name = isset( $traveler_last_names[ $key ] ) ? $traveler_last_names[ $key ] : '';
									$country   = isset( $traveler_countries[ $key ] ) ? $traveler_countries[ $key ] : '';
									$phone     = isset( $traveler_phones[ $key ] ) ? $traveler_phones[ $key ] : '';
									$email     = isset( $traveler_emails[ $key ] ) ? $traveler_emails[ $key ] : '';
									$dob       = isset( $traveler_dobs[ $key ] ) ? $traveler_dobs[ $key ] : '';
									$gender    = isset( $traveler_genders[ $key ] ) ? $traveler_genders[ $key ] : '';
									$trip_ids  =  isset( $items[$key] ) && isset( $items[$key]['trip_id'] ) ? $items[$key]['trip_id'] : '';
									$titles        = get_the_title( $trip_ids );
									$pricing_ids =  isset( $items[$key] ) && isset( $items[$key]['pricing_id'] ) ? $items[$key]['pricing_id'] : '';
									$pricing_datas = WP_Travel_Helpers_Pricings::get_pricings( $trip_ids, $pricing_ids );
									$pricing_titles = '';
									if ( ! is_wp_error( $pricing_datas ) && isset( $pricing_datas['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_datas['code'] ) {
										$pricing       = $pricing_datas['pricings'];
										$pricing_titles = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
									}
									if( apply_filters( 'wptravel_traveller_salutation', true ) ==  true ){
										if( $gender == 'male' ){
											$salutation = __( 'Mr ', 'wp-travel' );
										}elseif( $gender == 'female' ){
											$salutation = __( 'Miss ', 'wp-travel' );
										}else{
											$salutation = '';
										}
									}else{
										$salutation = '';
									}
									?>
										<thead>
											<tr>
												<th colspan="6"><?php esc_html_e( 'Trip ' . $indexs . ' : ', 'wp-travel' ); ?> <strong><?php echo esc_html( $titles ); ?></strong> / <span class="my-order-pricing"><?php echo esc_html( $pricing_titles ); ?></span></th>
											</tr>
											<?php $indexs++;  ?>
											<tr>
												<th><?php esc_html_e( 'Traveler Name', 'wp-travel' ); ?></th>
												<?php if( apply_filters( 'wp_travel_enable_traveller_country_booking_email', true ) ): ?>
													<th><?php esc_html_e( 'Country', 'wp-travel' ); ?></th>
												<?php endif; ?>
												<th><?php esc_html_e( 'Phone No.', 'wp-travel' ); ?></th>
												<th><?php esc_html_e( 'Email', 'wp-travel' ); ?></th>
												<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
													<th><?php esc_html_e( 'DOB', 'wp-travel' ); ?></th>
												<?php endif; ?>
												<?php if( apply_filters( 'wp_travel_enable_traveller_gender_booking_email', true ) ): ?>
													<th><?php esc_html_e( 'Gender', 'wp-travel' ); ?></th>
												<?php endif; ?>
												
												
											</tr>
										</thead>
									<?php 
									foreach ( $first_name as $indx => $dats ) {
										$traveler_l_name = isset( $last_name[$indx] ) ? $last_name[$indx] : '';
										$traveler_country = isset( $country[$indx] ) ? $country[$indx] : '';

										$traveler_country   = wptravel_get_countries()[$traveler_country];

										$traveler_phone   = isset( $phone[$indx] ) ? $phone[$indx] : '';
										$traveler_email   = isset( $email[$indx] ) ? $email[$indx] : '';
										$traveler_gander  = isset( $gender[$indx] ) ? $gender[$indx] : '';
										$traveler_dob  = isset( $dob[$indx] ) ? $dob[$indx] : '';
									?>
									<tr>
										<td><?php echo $salutation . esc_html( $dats ); ?> <?php echo esc_html( $traveler_l_name ); ?></td>
										<td><?php echo esc_html( $traveler_country ); ?></td>
										<td><?php echo esc_html( $traveler_phone ); ?></td>
										<td><?php echo esc_html( $traveler_email ); ?></td>
										<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
											<td><?php echo esc_html( $traveler_dob ); ?></td>
										<?php endif; ?>
										<?php if( apply_filters( 'wp_travel_enable_traveller_gender_booking_email', true ) ): ?>
											<td><?php echo esc_html( $traveler_gander ); ?></td>
										<?php endif; ?>
										
										
									</tr>
									<?php }
								}
							} else {
								foreach ( $traveler_first_names as $key => $first_name ) {
									$last_name = isset( $traveler_last_names[ $key ] ) ? $traveler_last_names[ $key ][0] : '';
									$country   = isset( $traveler_countries[ $key ] ) ? $traveler_countries[ $key ][0] : '';

									$country   = wptravel_get_countries()[$country];

									$phone     = isset( $traveler_phones[ $key ] ) ? $traveler_phones[ $key ][0] : '';
									$email     = isset( $traveler_emails[ $key ] ) ? $traveler_emails[ $key ][0] : '';
									$dob       = isset( $traveler_dobs[ $key ] ) ? $traveler_dobs[ $key ][0] : '';
									$gender    = isset( $traveler_genders[ $key ] ) ? $traveler_genders[ $key ][0] : '';
									?>
									<tr>
										<td><?php echo esc_html( $first_name[0] ); ?> <?php echo esc_html( $last_name ); ?></td>
										<td><?php echo esc_html( $country ); ?></td>
										<td><?php echo esc_html( $phone ); ?></td>
										<td><?php echo esc_html( $email ); ?></td>
										<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
										<?php endif; ?>
										<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
										<?php endif; ?>
										<td><?php echo esc_html( $dob ); ?></td>
										<td><?php echo esc_html( $gender ); ?></td>
									</tr>
									<?php
								}
							}
							?>
						</tbody>
					<?php
				} else { 
					foreach ( $items as $item_key => $trip ) {
						$trip_id = $trip['trip_id'];
						// Values.
						$title        = get_the_title( $trip_id );
						$pricing_id   = $trip['pricing_id'];
						$pricing_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, $pricing_id );

						$pricing_title = '';
						if ( ! is_wp_error( $pricing_data ) && isset( $pricing_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_data['code'] ) {
							$pricing       = $pricing_data['pricings'];
							$pricing_title = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
						}

						$first_names = isset( $traveler_first_names[ $item_key ] ) 	? $traveler_first_names[ $item_key ] : array();
						$last_names  = isset( $traveler_last_names[ $item_key ] ) 	? $traveler_last_names[ $item_key ] : array();
						$countries   = isset( $traveler_countries[ $item_key ] ) 	? $traveler_countries[ $item_key ] 	: array();
						$phones      = isset( $traveler_phones[ $item_key ] ) 		? $traveler_phones[ $item_key ] 	: array();
						$emails      = isset( $traveler_emails [ $item_key ] ) 		? $traveler_emails [ $item_key ] 	: array();
						$dobs        = isset( $traveler_dobs[ $item_key ] ) 		? $traveler_dobs[ $item_key ] 		: array();
						$genders     = isset( $traveler_genders[ $item_key ] ) 		? $traveler_genders[ $item_key ] 	: array();
						?>
						<thead>
							<tr>
								<th colspan="6"><?php esc_html_e( 'Trip : ', 'wp-travel' ); ?> <strong><?php echo esc_html( $title ); ?></strong> / <span class="my-order-pricing"><?php echo esc_html( $pricing_title ); ?></span></th>
							</tr>
							<tr>
								<th><?php esc_html_e( 'Traveler Name', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Country', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Phone No.', 'wp-travel' ); ?></th>
								<th><?php esc_html_e( 'Email', 'wp-travel' ); ?></th>
								<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
									<th><?php esc_html_e( 'DOB', 'wp-travel' ); ?></th>
								<?php endif; ?>
								
								<?php if( apply_filters( 'wp_travel_enable_traveller_gender_booking_email', true ) ): ?>
									<th><?php esc_html_e( 'Gender', 'wp-travel' ); ?></th>
								<?php endif; ?>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $first_names as $key => $first_name ) {
								$last_name = isset( $last_names[ $key ] ) ? $last_names[ $key ] : '';
								$country   = isset( $countries[ $key ] ) ? $countries[ $key ] : '';

								$country   = wptravel_get_countries()[$country];

								$phone     = isset( $phones[ $key ] ) ? $phones[ $key ] : '';
								$email     = isset( $emails[ $key ] ) ? $emails[ $key ] : '';
								$dob       = isset( $dobs[ $key ] ) ? $dobs[ $key ] : '';
								$gender    = isset( $genders[ $key ] ) ? $genders[ $key ] : '';

								if( apply_filters( 'wptravel_traveller_salutation', true ) ==  true ){
									if( $gender == 'male' ){
										$salutation = __( 'Mr ', 'wp-travel' );
									}elseif( $gender == 'female' ){
										$salutation = __( 'Miss ', 'wp-travel' );
									}else{
										$salutation = '';
									}
								}else{
									$salutation = '';
								}
								?>
								<tr>
									<td><?php echo $salutation . esc_html( $first_name ); ?> <?php echo esc_html( $last_name ); ?></td>
									<td><?php echo esc_html( $country ); ?></td>
									<td><?php echo esc_html( $phone ); ?></td>
									<td><?php echo esc_html( $email ); ?></td>
									<?php if( apply_filters( 'wp_travel_enable_traveller_dob_booking_email', true ) ): ?>
										<td><?php echo esc_html( $dob ); ?></td>
									<?php endif; ?>
									
									<?php if( apply_filters( 'wp_travel_enable_traveller_gender_booking_email', true ) ): ?>
										<td><?php echo esc_html( $gender ); ?></td>
									<?php endif; ?>
									
								</tr>
								<?php
							}
							?>
						</tbody>
						<?php
					}
				}
				?>
			</tbody>
		</table>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
