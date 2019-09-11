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
?>
<div class="order-wrapper">
	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wp-travel' ); ?></h3>
	<div id="order_review" class="wp-travel-checkout-review-order">
		<table class="shop_table wp-travel-checkout-review-order-table">
			<thead>
				<tr>
					<th class="product-name"><?php esc_html_e( 'Trip', 'wp-travel' ); ?></th>
					<th class="product-total text-right"><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
					<th style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="product-total text-right f-partial-payment"><?php esc_html_e( 'Partial', 'wp-travel' ); ?></th>
				</tr>
			</thead>
			<tbody>

				<?php foreach ( $trips as $cart_id => $trip ) : ?>
					<?php

					$trip_id       = $trip['trip_id'];
					$trip_price    = $trip['trip_price'];
					$trip_duration = isset( $trip['trip_duration'] ) ? $trip['trip_duration'] : '';

					$pax                = ! empty( $trip['pax'] ) ? $trip['pax'] : 1;
					$price_key          = isset( $trip['price_key'] ) ? $trip['price_key'] : '';
					$pricing_name       = wp_travel_get_trip_pricing_name( $trip_id, $price_key );
					$enable_partial     = $trip['enable_partial'];
					$trip_price_partial = isset( $trip['trip_price_partial'] ) ? $trip['trip_price_partial'] : $trip_price;

					$pax_label = isset( $trip['pax_label'] ) ? $trip['pax_label'] : '';

					// $single_trip_total         = wp_travel_get_formated_price( $trip_price * $pax );
					$single_trip_total = wp_travel_get_formated_price( $trip_price ); // Applies to categorized pricing @since 3.0.0
					// $single_trip_total_partial = wp_travel_get_formated_price( $trip_price_partial * $pax );
					$single_trip_total_partial = wp_travel_get_formated_price( $trip_price_partial ); // Applies to categorized pricing @since 3.0.0

					$trip_extras = isset( $trip['trip_extras'] ) ? $trip['trip_extras'] : array();

					$price_per = 'trip-default';

					if ( ! empty( $price_key ) ) {
						$price_per = wp_travel_get_pricing_variation_price_per( $trip_id, $price_key );
					}

					if ( 'trip-default' === $price_per ) {
						$price_per = get_post_meta( $trip_id, 'wp_travel_price_per', true );
					}

					if ( 'group' === $price_per ) {

						$single_trip_total         = wp_travel_get_formated_price( $trip_price );
						$single_trip_total_partial = wp_travel_get_formated_price( $trip_price_partial );

						$price_per_label = '( ' . $pax . __( ' Pax', 'wp-travel' ) . ' )';

					} else {
						$price_per_label = ' × ' . $pax . ' /' . $pax_label;
					}

					$cart_trip = isset( $trip['trip'] ) ? $trip['trip'] : array();

					?>
					<!-- New Layout @since 3.0.0 -->
					<tr class="product-name">
						<td colspan="2">
							<?php echo esc_html( $pricing_name ); ?>
						</td>
					</tr>
					<?php
					if ( count( $cart_trip ) > 0 ) :
						foreach ( $cart_trip as $category_id => $category ) {
							$category_type = isset( $category['type'] ) ? $category['type'] : '';
							$price_per     = isset( $category['price_per'] ) ? $category['price_per'] : 'person';

							$price = $category['price'];
							$pax   = $category['pax'];
							if ( 'group' !== $price_per ) {
								$price *= $pax;
								?>
								<tr class="person-count">
									<td class="left">
										<span style="display:table-row"><?php echo esc_html( esc_html( $category_type ) ); ?></span>
										<?php echo sprintf( '%2$s x %1$s', wp_travel_get_formated_price_currency( $category['price'] ), esc_html( $pax ) ); ?>
									</td>
									<td class="right">
										<?php echo wp_travel_get_formated_price_currency( $price ); ?>
									</td>
								</tr>
								<!-- <li class="person-count">
									<?php //echo sprintf( '%3$s %2$s x %1$s = %4$s', wp_travel_get_formated_price_currency( $category['price'] ), esc_html( $pax ), esc_html( $category_type ), wp_travel_get_formated_price_currency( $price ) ); ?>
								</li> -->
								<?php
							} else {
								?>
								<tr class="person-count">
									<td class="left">
										<span style="display:table-row"><?php echo sprintf( esc_html__( 'Group (%s)', 'wp-travel' ), esc_html( $pax ) ); ?></span>
										<?php echo sprintf( '%2$s x %1$s', wp_travel_get_formated_price_currency( $category['price'] ), '1', esc_html( $category_type ) ); ?>
									</td>
									<td class="right">
										<?php echo wp_travel_get_formated_price_currency( $price ); ?>
									</td>
								</tr>
								<?php
							}
						}
					endif;
					?>
					<!-- ./ End new layout -->

					<?php do_action( 'wp_travel_tour_extras_mini_cart_block', $trip_extras, $cart_id, $trip_id, $price_key ); ?>

				<?php endforeach; ?>

			</tbody>
			<tfoot>
				<?php $cart_amounts = $wt_cart->get_total(); ?>
				<?php
				$discounts = $wt_cart->get_discounts();
				if ( is_array( $discounts ) && ! empty( $discounts ) ) :
					?>

					<tr>
						<th>
							<span><strong><?php esc_html_e( 'Coupon Discount ', 'wp-travel' ); ?> </strong></span>
						</th>
						<td  class="text-right">
							<strong>- <?php echo wp_travel_get_formated_price_currency( $cart_amounts['discount'] ); ?></strong>
						</td>
						<td style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">

							<?php if ( 0 === $cart_amounts['discount_partial'] ) : ?>

								<p><strong><span class="wp-travel-tax ws-theme-currencySymbol">--</strong></p>

							<?php else : ?>

								<strong><?php echo wp_travel_get_formated_price_currency( $cart_amounts['discount_partial'] ); ?></strong>

							<?php endif; ?>

						</td>
					</tr>

				<?php endif; ?>
				<?php if ( $tax_rate = wp_travel_is_taxable() ) : ?>
					<tr>
						<th>
							<p><strong><?php esc_html_e( 'Subtotal', 'wp-travel' ); ?></strong></p>
							<p><strong><?php esc_html_e( 'Tax: ', 'wp-travel' ); ?>
							<span class="tax-percent">
								<?php
								echo esc_html( $tax_rate );
								esc_html_e( '%', 'wp-travel' );
								?>
							</span></strong></p>
						</th>
						<td  class="text-right">
							<p><strong><span class="wp-travel-sub-total"><?php echo wp_travel_get_formated_price_currency( $cart_amounts['sub_total'] ); ?></span></strong></p>
							<p><strong><span class="wp-travel-tax"><?php echo wp_travel_get_formated_price_currency( $cart_amounts['tax'] ); ?></span></strong></p>
						</td>
						<td style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">
							<p><strong><span class="wp-travel-sub-total"><?php echo wp_travel_get_formated_price_currency( $cart_amounts['sub_total_partial'] ); ?></span></strong></p>

							<?php if ( 0 === $cart_amounts['tax_partial'] ) : ?>

								<p><strong><span class="wp-travel-tax ">--</strong></p>

							<?php else : ?>

								<p><strong><span class="wp-travel-tax "><?php echo wp_travel_get_formated_price_currency( $cart_amounts['tax_partial'] ); ?></span></strong></p>

							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="order-total ">
				<th><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
				<td class="text-right"><strong><span class="wp-travel-total-price-amount amount"><?php echo wp_travel_get_formated_price_currency( $cart_amounts['total'] ); ?></span></strong> </td>
				<td style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">
					<strong><span class="wp-travel-total-price-amount amount"><?php echo wp_travel_get_formated_price_currency( $cart_amounts['total_partial'] ); ?></span></strong> </td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>
<?php
if ( is_array( $trips ) && count( $trips ) > 0 ) {
	foreach ( $trips as $trip ) {
		$first_trip_id      = $trip['trip_id'];
		$checkout_for_title = ( get_the_title( $first_trip_id ) ) ? get_the_title( $first_trip_id ) : __( 'Trip Book', 'wp-travel' );
		break;
	}
	?>
	<!--only used in instamojo for now --><input type="hidden" id="wp-travel-checkout-for" value="<?php echo esc_attr( $checkout_for_title ); ?>" >
	<?php
}
