<?php
/**
 * WP Travel Checkout.
 *
 * @package WP Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wt_cart;
$trips = $wt_cart->getItems();
				
if ( ! $trips ) {
	$wt_cart->cart_empty_message();
	return;
} 

$settings = wp_travel_get_settings();

$checkout_page_url = get_home_url();		
if ( isset( $settings['checkout_page_id'] ) ) {
	$checkout_page_id = $settings['checkout_page_id'];
	$checkout_page_url = get_permalink( $checkout_page_id );
}

$pax_label = __( 'Pax', 'wp-travel' );
$max_attr = '';

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
$settings        = wp_travel_get_settings();
$currency_code 	 = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
$per_person_text = wp_travel_get_price_per_text( $trip_id );
?>

<!-- CART HTML START -->
<div class="ws-theme-cart-page">
	<form action="<?php echo esc_url( $checkout_page_url ); ?>" method="get">
		<!-- <input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ); ?>" > -->
			<!-- <input type="hidden" name="trip_date" value="<?php echo esc_attr( $trip_requested_date ); ?>" > -->
		
		<table class="ws-theme-cart-list">
			<thead>
				<tr>
					<th></th>
					<th></th>
					<th colspan="2"><?php esc_html_e( 'Tour', 'wp-travel' ); ?></th>
					<th><?php esc_html_e( 'Price', 'wp-travel' ); ?></th>
					<th><?php esc_html_e( 'PAX', 'wp-travel' ); ?></th>
					<th class="text-right"><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php $total_trip_price = 0; ?>
				<?php foreach ( $trips as $cart_id => $trip ) : ?>
					<?php
					$trip_id 		= $trip['trip_id'];
					// $price			= $trip['price']; // Price of single qty.
					$trip_price		= $trip['trip_price']; 
					$trip_duration	= isset( $trip['trip_duration'] ) ? $trip['trip_duration'] : '';
					
					$pax			= $trip['pax'];
					$price_key		= isset( $trip['price_key'] ) ? $trip['price_key'] : '';
					$enable_partial	= $trip['enable_partial'];
					$payable_price	= $trip['payable_price'];
					
					$pax_label 		= isset( $trip['pax_label'] ) ? $trip['pax_label'] : '';
					$max_available	= isset( $trip['max_available'] ) ? $trip['max_available'] : '';
					
					$max_attr = '';
					if ( $max_available ) {
						$max_attr = 'max="' . $max_available . '"';
					}

					$single_trip_total =  wp_travel_get_formated_price( $trip_price * $pax );


					$total_trip_price += $single_trip_total;
					
					?>

					<tr class="responsive-cart">
						<td class="product-remove" >
							<a href="" class="wp-travel-cart-remove tooltip-area" data-cart-id="<?php echo esc_attr( $cart_id ) ?>" title="<?php esc_attr_e( 'Remove this tour', 'wp-travel' ) ?>">×</a> 
						</td>
						<td class="product-thumbnail" >
							<a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>">
							<?php echo wp_kses( wp_travel_get_post_thumbnail( $trip_id ), wp_travel_allowed_html( array( 'img' ) ) ); ?>
							</a> 
						</td>
						<td class="product-name" colspan="2" data-title="Tour">
							<div class="item_cart">
								<h4>
									<a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>"><?php echo esc_html( get_the_title( $trip_id ) ); ?></a>
								</h4>
								<?php 
								if ( 'yes' === $fixed_departure && '' !== $trip_start_date ) :
								?>
									<span class="variation">
										<span><strong><?php esc_html_e( 'Booking Date:', 'wp-travel' ); ?></strong></span>
										<span>
										<?php $date_format = get_option( 'date_format' ); ?>
											<?php if ( ! $date_format ) : ?>
												<?php $date_format = 'jS M, Y'; ?>
											<?php endif; ?>
											<?php echo esc_html( date( $date_format, strtotime( $trip_start_date ) ) ); ?>
										</span>
									</span>
								<?php endif; ?>
							</div>
						</td>
						<td class="product-price" data-title="Price">
							<?php if ( '' !== $price || '0' !== $price ) : ?>
								<span class="person-count">
									<ins>
										<span class="wp-travel-trip-price" payment_price="<?php echo esc_attr( @$trip_tax_details['actual_trip_price'] ); ?>" trip_price="<?php echo esc_attr( $trip_price ); ?>" >
											<?php echo apply_filters( 'wp_travel_itinerary_price', sprintf( ' %s %s ', $currency_symbol, $trip_price ), $currency_symbol, $trip_price ); ?>
										</span>
									</ins>/<?php echo esc_html( ucfirst( $pax_label ) ); ?>
								</span>
							<?php endif; ?>
						</td>
						<td class="product-quantity" data-title="PAX">
							<div class="st_adults">
								<span class="label"><?php echo esc_html( ucfirst( $pax_label ) ); ?></span>
								<input type="number" class="input-text wp-travel-pax text" step="1" min="0" <?php echo $max_attr; ?> name="pax" value="<?php echo esc_attr( $pax ); ?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
								<input type="hidden" name="cart_id" value="<?php echo esc_attr( $cart_id ) ?>" >
							</div>
						</td>
						<td class="product-subtotal text-right" data-title="Total">
							<div class="item_cart">
								<p>
									<strong><span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-trip-total"> <?php echo esc_html( $single_trip_total ) ?> </span></strong>
								</p>
							</div>
						</td> 
					</tr>

				<?php endforeach; ?>						
			</tbody>
		</table>
		
		<table class="ws-theme-cart-list table-total-info">
			<?php if ( wp_travel_is_trip_price_tax_enabled() && isset( $trip_tax_details['tax_percentage'] ) && '' !== $trip_tax_details['tax_percentage'] && 'inclusive' !== $trip_tax_details['tax_type'] ) : ?>
				<tr>
					<th>
						<p><strong><?php esc_html_e( 'Subtotal', 'wp-travel' ); ?></strong></p>
						<p><strong><?php esc_html_e( 'Tax: ', 'wp-travel' ); ?> 
						<span class="tax-percent">
							<?php
							if ( isset( $trip_tax_details['tax_percentage'] ) ) {
								echo esc_html( $trip_tax_details['tax_percentage'] );
								esc_html_e( '%', 'wp-travel' );
							} 
						?>
						</span></strong></p>
					</th>
					<td  class="text-right">
						<p><strong><span class="wp-travel-sub-total ws-theme-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>0</strong></p>
						<p><strong><span class="wp-travel-tax ws-theme-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span>0</strong></p>
					</td>
				</tr>
			<?php endif; ?>
			<tr>
				<th>
					<strong><?php echo esc_html( 'Total', 'wp-travel' ); ?></strong>
				</th>
				<td  class="text-right">
					<p class="total">
						<strong><?php echo wp_travel_get_currency_symbol(); ?><span class="wp-travel-total ws-theme-currencySymbol"><?php echo esc_html( wp_travel_get_formated_price( $total_trip_price ) ); ?></span></strong>
					</p>
				</td>
			</tr>
		</table>
		<div>
			<div class="actions">
				<a href="javascript:void(0)" class="btn_full wp-travel-update-cart-btn" ><?php esc_html_e( 'Update Cart', 'wp-travel' ) ?></a>					
				<input type="submit" class="btn_full book-now-btn" value="<?php esc_html_e( 'Proceed to checkout', 'wp-travel' ) ?>">
			</div>
		</div>
	</form>
</div>