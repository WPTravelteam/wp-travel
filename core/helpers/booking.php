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
class WpTravel_Helpers_Booking { // @phpcs:ignore

	/**
	 * Generate HTML for Booking Details.
	 *
	 * @param int   $booking_id Trip Booking ID. 
	 * @since 5.0.0
	 * @return mixed
	 */
	public static function render_booking_details( $booking_id ) {

		global $wt_cart;
		$items = $wt_cart->getItems();

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
					<th><?php esc_html_e( 'Itinerary', 'wp-travel' ); ?></th>
					<th><?php esc_html_e( 'PAX', 'wp-travel' ); ?></th>
					<th><?php esc_html_e( 'Arrival Date', 'wp-travel' ); ?></th>
					<th><?php esc_html_e( 'Departure Date', 'wp-travel' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Order Details.
				foreach ( $items as $item_key => $trip ) {
					$trip_id    = $trip['trip_id'];

					// Values
					$title          = get_the_title( $trip_id );
					$pax            = isset( $trip['pax'] ) ? $trip['pax'] : '';
					$arrival_date   = isset( $trip['arrival_date'] ) && ! empty( $trip['arrival_date'] ) ? wptravel_format_date( $trip['arrival_date'] ) : '';
					$departure_date = isset( $trip['departure_date'] ) && ! empty( $trip['departure_date'] ) ? wptravel_format_date( $trip['departure_date'] ) : '';
					
					$pricing_id   = $trip['pricing_id'];
					$pricing_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, $pricing_id );

					$pricing_title = '';
					if ( ! is_wp_error( $pricing_data ) && isset( $pricing_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_data['code'] ) {
						$pricing       = $pricing_data['pricings'];
						$pricing_title = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
					}

					?>
					<tr>
						<td>
							<a href="<?php echo esc_url( get_permalink( $trip_id ) ); ?>"><strong><?php echo esc_html( $title ); ?></strong></a>
							<br>
							<span class="my-order-pricing"><?php echo esc_html( $pricing_title ); ?></span>
							<span class="my-order-tail">
								<?php if ( ! empty( $trip['trip'] ) ) : ?>
									<?php foreach ( $trip['trip'] as $category_id => $t ) :
										if (  $t['pax'] < 1 ) {
											continue;
										}
										?>
										<span class="my-order-price-detail">(<?php echo esc_html( $t['pax'] ) . ' ' . $t['custom_label'] . ' x ' . wptravel_get_formated_price_currency( $t['price'], false, '', $booking_id ); ?>) </span>
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

											$qty = isset( $extras['qty'][ $k ] ) && $extras['qty'][ $k ] ? $extras['qty'][ $k ] : 1;

											$total = $price * $qty;
											?>
											<div class="my-order-price-breakdown-additional-service-item clearfix">
												<span class="my-order-head"><?php echo esc_html( get_the_title( $extra_id ) ); ?> (<?php echo esc_attr( $qty ) . ' x ' . wptravel_get_formated_price_currency( $price, false, '', $order_id ); ?>)</span>
												<span class="my-order-tail my-order-right"><?php echo wptravel_get_formated_price_currency( $total, false, '', $order_id ); //@phpcs:ignore ?></span>
											</div>
										<?php endforeach; ?>

									</div>

									<?php
								endif;?>
						</td>
						<td><?php echo esc_html( $pax ); ?></td>
						<td><?php echo esc_html( $arrival_date ); ?></td>
						<td><?php echo esc_html( $departure_date ); ?></td>
					</tr>

					<?php
				} ?>
			</tbody>
		</table>
		<?php
		$content = ob_get_contents();

		ob_end_clean();

		return $content;
	}

	/**
	 * Generate HTML for Traveler Details.
	 *
	 * @param int   $booking_id Trip Booking ID. 
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
				foreach ( $items as $item_key => $trip ) {
					$trip_id    = $trip['trip_id'];

					// Values.
					$title        = get_the_title( $trip_id );
					$pricing_id   = $trip['pricing_id'];
					$pricing_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id, $pricing_id );

					$pricing_title = '';
					if ( ! is_wp_error( $pricing_data ) && isset( $pricing_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricing_data['code'] ) {
						$pricing       = $pricing_data['pricings'];
						$pricing_title = isset( $pricing['title'] ) ? $pricing['title'] : $pricing[0]['title'];
					}

					$first_names = isset( $checkout_form_data['wp_travel_fname_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_fname_traveller'][ $item_key ] : array();
					$last_names  = isset( $checkout_form_data['wp_travel_lname_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_lname_traveller'][ $item_key ] : array();
					$countries   = isset( $checkout_form_data['wp_travel_country_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_country_traveller'][ $item_key ] : array();
					$phones      = isset( $checkout_form_data['wp_travel_phone_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_phone_traveller'][ $item_key ] : array();
					$emails      = isset( $checkout_form_data['wp_travel_email_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_email_traveller'][ $item_key ] : array();
					$dobs        = isset( $checkout_form_data['wp_travel_date_of_birth_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_date_of_birth_traveller'][ $item_key ] : array();
					$genders     = isset( $checkout_form_data['wp_travel_gender_traveller'][ $item_key ] ) ? $checkout_form_data['wp_travel_gender_traveller'][ $item_key ] : array();
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
							<th><?php esc_html_e( 'DOB', 'wp-travel' ); ?></th>
							<th><?php esc_html_e( 'Gender', 'wp-travel' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ( $first_names as $key => $first_name ) {
							$last_name = isset( $last_names[ $key ] ) ? $last_names[ $key ] : '';
							$country   = isset( $countries[ $key ] ) ? $countries[ $key ] : '';
							$phone     = isset( $phones[ $key ] ) ? $phones[ $key ] : '';
							$email     = isset( $emails[ $key ] ) ? $emails[ $key ] : '';
							$dob       = isset( $dobs[ $key ] ) ? $dobs[ $key ] : '';
							$gender    = isset( $genders[ $key ] ) ? $genders[ $key ] : '';
							?>
							<tr>
								<td><?php echo esc_html( $first_name ); ?> <?php echo esc_html( $last_name ); ?></td>
								<td><?php echo esc_html( $country ); ?></td>
								<td><?php echo esc_html( $phone ); ?></td>
								<td><?php echo esc_html( $email ); ?></td>
								<td><?php echo esc_html( $dob ); ?></td>
								<td><?php echo esc_html( $gender ); ?></td>
							</tr>
							<?php
						}
						?>
					</tbody>
					<?php
				} ?>
			</tbody>
		</table>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}
}
