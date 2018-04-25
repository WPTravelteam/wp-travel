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
<div class="order-wrapper">
	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wp-travel' ) ?></h3>
	<div id="order_review" class="wp-travel-checkout-review-order">
		<table class="shop_table wp-travel-checkout-review-order-table">
			<thead>
				<tr>
				<th class="product-name"><?php esc_html_e( 'Trip', 'wp-travel' ) ?></th>
				<th class="product-total"><?php esc_html_e( 'Total', 'wp-travel' ) ?></th>
				</tr>
			</thead>
			<tbody>

				<?php $total_trip_price = 0; ?>
				<?php foreach ( $trips as $cart_id => $trip ) : ?>
					<?php
					$trip_id 		= $trip['trip_id'];
					$trip_price		= $trip['trip_price']; 
					$trip_duration	= isset( $trip['trip_duration'] ) ? $trip['trip_duration'] : '';
					
					$pax			= $trip['pax'];
					$price_key		= isset( $trip['price_key'] ) ? $trip['price_key'] : '';
					$enable_partial	= $trip['enable_partial'];
					$trip_price_partial	= $trip['trip_price_partial'];
					
					$pax_label 		= isset( $trip['pax_label'] ) ? $trip['pax_label'] : '';
					
					$single_trip_total =  wp_travel_get_formated_price( $trip_price * $pax );									
					$total_trip_price += $single_trip_total; ?>

					<tr class="cart_item">
						<td class="product-name">
							<?php echo esc_html( get_the_title( $trip_id ) ); ?> &nbsp; <strong class="product-quantity">× <span class="wp-travel-cart-pax"><?php echo esc_html( $pax ); ?></span><?php printf( $pax_label ); ?> </strong> 
						</td>
						<td class="product-total">
						<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="product-total-price amount" ><?php echo esc_html( $single_trip_total ) ?></span>
						</td>
					</tr>

				<?php endforeach; ?>

			</tbody>
			<tfoot>
				<?php if ( wp_travel_is_trip_price_tax_enabled() && isset( $trip_tax_details['tax_percentage'] ) && '' !== $trip_tax_details['tax_percentage'] && 'inclusive' !== $trip_tax_details['tax_type'] ) { ?>
					<tr class="cart-subtotal">
						<th><?php esc_html_e( 'Subtotal', 'wp-travel' )?></th>
						<td data-title="Subtotal"><span class="Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-sub-total">0</span></td>
					</tr>

					<tr class="cart-subtotal">
						<th>
							<?php
							if ( isset( $trip_tax_details['tax_percentage'] ) ) {
								echo esc_html( $trip_tax_details['tax_percentage'] );
								esc_html_e( '% Tax ', 'wp-travel' );
							} ?>
						</th>
						<td data-title="tax"><span class="Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-tax">0</span></td>
					</tr>
				<?php } ?>               
				<tr class="order-total">
				<th><?php esc_html_e( 'Total', 'wp-travel' ) ?></th>
				<td><strong><span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="wp-travel-total-price-amount amount"><?php echo esc_html( wp_travel_get_formated_price( $total_trip_price ) ) ?></span></strong> </td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>