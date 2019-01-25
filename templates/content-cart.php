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

$checkout_page_url = wp_travel_get_checkout_url();

if ( isset( $settings['checkout_page_id'] ) ) {
	$checkout_page_id  = $settings['checkout_page_id'];
	$checkout_page_url = get_permalink( $checkout_page_id );
}

$pax_label = __( 'Pax', 'wp-travel' );
$max_attr  = '';

// For old form
$trip_id       = ( isset( $_GET['trip_id'] ) && '' !== $_GET['trip_id'] ) ? $_GET['trip_id'] : '';
$trip_duration = ( isset( $_GET['trip_duration'] ) && '' !== $_GET['trip_duration'] ) ? $_GET['trip_duration'] : 1;

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
$settings        = wp_travel_get_settings();
$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
$per_person_text = wp_travel_get_price_per_text( $trip_id );

// Print Errors / Notices.
wp_travel_print_notices();
?>

<!-- CART HTML START -->
<div class="ws-theme-cart-page">
	<form action="<?php echo esc_url( $checkout_page_url ); ?>" method="post">

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
				<?php
				foreach ( $trips as $cart_id => $trip ) :
					$pricing_label = false;
					$trip_id       = $trip['trip_id'];
					// $price			= $trip['price']; // Price of single qty.
					$trip_price      = $trip['trip_price'];
					$trip_duration   = isset( $trip['trip_duration'] ) ? $trip['trip_duration'] : '';
					$trip_start_date = isset( $trip['trip_start_date'] ) && ! empty( $trip['trip_start_date'] ) ? wp_travel_format_date( $trip['trip_start_date'], true, 'Y-m-d' ) : false;

					$pax                = $trip['pax'];
					$price_key          = isset( $trip['price_key'] ) ? $trip['price_key'] : '';
					$enable_partial     = $trip['enable_partial'];
					$trip_price_partial = $trip['trip_price_partial'];

					$pax_label     = isset( $trip['pax_label'] ) ? $trip['pax_label'] : '';
					$max_available = isset( $trip['max_available'] ) ? $trip['max_available'] : '';

					$trip_extras = isset( $trip['trip_extras'] ) ? $trip['trip_extras'] : array();

					$pricing_name = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
					$pax_limit    = '';
					/**
					 * Customization Starts.
					 */
					if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {
						$inventory = new WP_Travel_Util_Inventory();
						// $available_pax = $inventory->get_available_pax( $trip_id, $price_key );
						$pax_limit = $inventory->get_pricing_option_pax_limit( $trip_id, $price_key );
					}
					$data_max_pax  = apply_filters( 'wp_travel_data_max_pax', $max_available, $pax_limit );
					$max_available = apply_filters( 'wp_travel_available_pax', $max_available, $trip_id, $price_key );

					$min_available = isset( $trip['min_available'] ) ? $trip['min_available'] : '1';
					$max_attr      = '';
					$min_attr      = 'min="1"';
					if ( $max_available ) {
						$max_attr = 'max="' . $max_available . '" data-max="' . $data_max_pax . '"';
					}
					/**
					 * Customization Ends.
					 */
					$min_available = isset( $trip['min_available'] ) ? $trip['min_available'] : '1';
					$min_attr      = 'min="1"';
					// if ( $max_available ) {
					// $max_attr = 'max="' . $max_available . '"';
					// }
					if ( $min_available ) {
						$min_attr = 'min="' . $min_available . '"';
					}
					$single_trip_total = wp_travel_get_formated_price( $trip_price * $pax );

					$price_per = 'trip-default';

					if ( isset( $trip['price_key'] ) && ! empty( $trip['price_key'] ) ) {
						$price_per = wp_travel_get_pricing_variation_price_per( $trip['trip_id'], $trip['price_key'] );
					}

					if ( 'trip-default' === $price_per ) {
						$price_per = get_post_meta( $trip['trip_id'], 'wp_travel_price_per', true );
					}

					if ( 'group' === $price_per ) {

						$single_trip_total = wp_travel_get_formated_price( $trip_price );

					}

					?>

					<tr class="responsive-cart has_options_selected">
						<td class="product-remove" >
							<a href="" class="wp-travel-cart-remove tooltip-area" data-cart-id="<?php echo esc_attr( $cart_id ); ?>" title="<?php esc_attr_e( 'Remove this tour', 'wp-travel' ); ?>">×</a> 
						</td>
						<td class="product-thumbnail" >
							<a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>">
							<?php echo wp_kses( wp_travel_get_post_thumbnail( $trip_id ), wp_travel_allowed_html( array( 'img' ) ) ); ?>
							</a> 
						</td>
						<td class="product-name" colspan="2" data-title="Tour">
							<div class="item_cart">
								<h4>
									<a href="<?php echo esc_html( get_permalink( $trip_id ) ); ?>">
										<?php echo esc_html( $pricing_name ); ?>
									</a>
								</h4>
								<?php
								if ( $trip_start_date ) :
									?>
									<span class="variation">
										<span><strong><?php esc_html_e( 'Booking Date:', 'wp-travel' ); ?></strong></span>
										<span>
										<?php echo esc_html( $trip_start_date ); ?>
										</span>
									</span>
								<?php endif; ?>
							</div>
						</td>
						<td class="product-price" data-title="Price">
							<?php if ( ! empty( $trip_price ) && '0' !== $trip_price ) : ?>
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
								<!--<span class="label"><?php echo esc_html( ucfirst( $pax_label ) ); ?></span>-->
								<!-- Customization Starts. -->
								<input type="number" class="input-text wp-travel-pax text wp-travel-trip-pax wp-travel-trip-<?php echo esc_attr( $trip_id ); ?>" data-trip="wp-travel-trip-<?php echo esc_attr( $trip_id ); ?>" data-trip-id="<?php echo esc_attr( $trip_id ); ?>" step="1" <?php echo $min_attr; ?> <?php echo $max_attr; ?> name="pax" value="<?php echo esc_attr( $pax ); ?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
								<?php
								$group_size       = get_post_meta( $trip_id, 'wp_travel_group_size', true );
								$total_booked_pax = wp_travel_get_total_booked_pax( $trip_id, false );
								?>
								<input type="hidden" class="wp-travel-customize-group-size" value="<?php echo esc_attr( $group_size ); ?>" >
								<input type="hidden" class="wp-travel-customize-booked-group-size" value="<?php echo esc_attr( $total_booked_pax ); ?>" >
								
								<!-- Customization Ends. -->								
								<input type="hidden" name="cart_id" value="<?php echo esc_attr( $cart_id ); ?>" >
								<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ); ?>" >
							</div>
						</td>
						<td class="product-subtotal text-right" data-title="Total">
							<?php if ( ! empty( $trip_price ) && '0' !== $trip_price ) : ?>
								<div class="item_cart">
									<p>
										<strong><span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-trip-total"> <?php echo esc_html( $single_trip_total ); ?> </span></strong>
									</p>
								</div>
							<?php endif; ?>
						</td>
					</tr>
					<tr class="child_products">
						<td colspan="8">
							<?php do_action( 'wp_travel_tour_extras_cart_block', $trip_extras, $cart_id ); ?>
						</td>
					</tr>
				<?php endforeach; ?>					
			</tbody>
		</table>
		<?php $cart_amounts = $wt_cart->get_total(); ?>
			<table class="ws-theme-cart-list table-total-info">
				<?php
				$discounts = $wt_cart->get_discounts();
				if ( is_array( $discounts ) && ! empty( $discounts ) ) :
					?>
					<tr>
						<th>
							<strong><?php esc_html_e( 'Coupon Discount: ', 'wp-travel' ); ?><span class="tax-percent">
								<?php echo esc_html( $discounts['value'] ); ?> ( <?php echo 'percentage' === $discounts['type'] ? ' %' : wp_travel_get_currency_symbol(); ?> )
							</span></strong>
						</th>
						<td  class="text-right">
							<strong> - <span class="wp-travel-tax ws-theme-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><?php echo esc_html( wp_travel_get_formated_price( $cart_amounts['discount'] ) ); ?></strong>
						</td>
					</tr>

				<?php endif; ?>
				<?php if ( $tax_rate = wp_travel_is_taxable() ) : ?>
					<tr>
						<th>
							<p><strong><?php esc_html_e( 'Subtotal:', 'wp-travel' ); ?></strong></p>
							<strong><?php esc_html_e( 'Tax: ', 'wp-travel' ); ?> 
							<span class="tax-percent">
								<?php
								echo esc_html( $tax_rate );
								esc_html_e( '%', 'wp-travel' );
								?>
							</span></strong>
						</th>
						<td  class="text-right">
							<p><strong><span class="wp-travel-sub-total ws-theme-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><?php echo esc_html( wp_travel_get_formated_price( $cart_amounts['sub_total'] ) ); ?></strong></p>
							<strong><span class="wp-travel-tax ws-theme-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><?php echo esc_html( wp_travel_get_formated_price( $cart_amounts['tax'] ) ); ?></strong>
						</td>
					</tr>
				<?php endif; ?>
				<?php if ( ! empty( $trip_price ) && '0' !== $trip_price ) : ?>	
				<tr>
					<th colspan="2">
						<strong><?php echo esc_html__( 'Total', 'wp-travel' ); ?></strong>
						<p class="total">
							<strong><?php echo wp_travel_get_currency_symbol(); ?><span class="wp-travel-total ws-theme-currencySymbol"><?php echo esc_html( wp_travel_get_formated_price( $cart_amounts['total'] ) ); ?></span></strong>
						</p>
					</th>
				</tr>
				<?php endif; ?>
				<tr>
					<td>
						<div class="coupon">
							<input type="text" name="wp_travel_coupon_code_input" class="input-text" id="coupon_code" value="" placeholder="<?php echo esc_attr__( 'Coupon Code', 'wp-travel' ); ?>"> 
							<input type="submit" class="button wp-travel-apply-coupon-btn" name="apply_coupon" value="<?php echo esc_attr__( 'Apply Coupon', 'wp-travel' ); ?>">
						</div>
					</td>

					<td>
						<div class="actions">
							<button disabled onclick="javascript:void(0)"  class="btn_full wp-travel-update-cart-btn update-cart" ><?php esc_html_e( 'Update Cart', 'wp-travel' ); ?></button>					
							<input type="submit" class="btn_full book-now-btn" value="<?php esc_html_e( 'Proceed to checkout', 'wp-travel' ); ?>">
						</div>
					</td>	
				</tr>
			</table>
	</form>
</div>
