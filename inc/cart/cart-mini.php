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

function wp_travel_key_by( $items, $key_by = 'id' ) {
	$key_by_array = array();
	foreach ( $items as $item ) {
		$key_by_array[ $item[ $key_by ] ] = $item;
	}
	return $key_by_array;
}

function wp_travel_checkout_category_total() {

}
$settings      = wp_travel_get_settings();
$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

if ( 'yes' === get_option( 'wp_travel_migrate_400', 'no' ) ) {
	if ( class_exists( 'WP_Travel_Helpers_Cart' ) ) {
		$cart = WP_Travel_Helpers_Cart::get_cart();

		$cart_items = isset( $cart['cart']['cart_items'] ) ? $cart['cart']['cart_items'] : array();
		// error_log( print_r( $cart, true ) );
		// echo 'I\'m here';
	}
	?>
	<div class="order-wrapper">
		<div class="wp-travel-cart-sidebar">
			<div id="shopping-cart">
				<div class="cart-summary">
					<div class="cart-header">
						<h4 class="title"><img src="./img/shopping-cart.svg" alt=""><?php _e( 'Your Order', 'wp-travel' ); ?></h4>
						<p class="subtitle"><?php echo wp_kses_post( sprintf( __( 'You have selected %s items in your cart', 'wp-travel' ), '<strong data-wpt-cart-item-count="">' . count( $cart_items ) . '</strong>' ) ); ?></p>
					</div>
					<ul class="cart-summary-content list-group">
					<?php
					foreach ( $cart_items as $cart_id => $cart_item ) {
						$pricing_id   = $cart_item['pricing_id'];
						$pricings     = $cart_item['trip_data']['pricings']; // all pricings
						$cart_pricing = null;
						$trip_data    = $cart_item['trip_data'];
						foreach ( $pricings as $pricing ) { // getting pricing here.
							$pricing = (array) $pricing;
							if ( $pricing['id'] == $pricing_id ) {
								$cart_pricing = $pricing;
								break;
							}
						}
						$categories = isset( $cart_pricing['categories'] ) ? wp_travel_key_by( $cart_pricing['categories'] ) : array(); // All categories.
						// print_r( $categories );
						$trip_extras = isset( $cart_pricing['trip_extras'] ) ? wp_travel_key_by( $cart_pricing['trip_extras'] ) : array(); // All trip extras.

						$cart_extras = (array) $cart_item['extras'];
						if ( ! empty( $cart_extras ) ) {
							$cart_extras = array_combine( $cart_extras['id'], $cart_extras['qty'] );
						}

						$cart_pax = (array) $cart_item['trip'];
						// print_r( $cart_pax );
						$cart_total = 0;
						?>
						<li class="list-group-item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
							<button type="button" class="del-btn" data-l10n="<?php echo esc_attr( sprintf( __( 'Are you sure you want to remove \'%s\' from cart?', 'wp-travel' ), $trip_data['title'] ) ); ?>">×</button>
							<div>
								<div class="content-left">
									<?php echo get_the_post_thumbnail( $trip_data['id'], 'thumbnail' ); ?>
								</div>
								<div class="content-right">
									<h5><a href="javascript:void(0);"><?php echo esc_html( $trip_data['title'] ); ?></a></h5>
									<div class="meta-content">
									<?php
									// $category_total = 0;
									// foreach ( $cart_pax as $category_id => $detail ) {
									// $category = $categories[$category_id];
									// $ctitle   = $category['term_info']['title'];
									// $pax      = (int) $detail['pax'];

									// $price_per_group = $category['price_per'] == 'group';

									// $category_price = $category['is_sale'] ? $category['sale_price'] : $category['regular_price'];
									// $category_total += $pax * (float) $category_price;
									// echo "<span><span data-wpt-category-count=\"" . esc_attr( $category_id ) . "\">{$pax}</span> x {$ctitle}</span>";
									// }
									// $cart_total += $category_total;
									?>
									</div>
								</div>
								<div class="price">
									<span><?php echo $currency_symbol . '<span data-wpt-item-total="' . $cart['cart']['cart_total_regular'] . '">' . $cart['cart']['cart_total_regular'] . '</span>'; ?></span>
									<a href="javascript:void(0);" class="edit" data-wpt-target-cart-id="<?php echo esc_attr( $cart_id ); ?>" data-wpt-target-trip="<?php echo esc_attr( $trip_data['id'] ); ?>" data-wpt-target-pricing="<?php echo esc_attr( $cart_pricing['id'] ); ?>"><svg enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m384.721 0-323.626 323.627-61.095 188.373 188.374-61.094 323.626-323.627zm84.853 127.279-42.427 42.427-84.853-84.853 42.426-42.427zm-388.611 232.331 71.427 71.428-32.036 10.39-49.782-49.782zm14.501-27.925 225.617-225.618 31.82 31.82-225.618 225.617zm53.032 53.032 225.618-225.619 31.82 31.82-225.618 225.619zm-88.313 38.965 28.136 28.136-41.642 13.505z"></path></g></svg> Edit</a>
								</div>
							</div>
							<div class="update-fields-collapse" style="display: none;">
								<form class="wp-travel__cart-item" action="">
									<?php
									// $category_total = 0;
									foreach ( $cart_pax as $category_id => $detail ) {
										$category = $categories[ $category_id ];
										$ctitle   = $category['term_info']['title'];
										$pax      = (int) $detail['pax'];

										$price_per_group = $category['price_per'] == 'group';

										$category_price = $category['is_sale'] ? $category['sale_price'] : $category['regular_price'];

										$category_total = $price_per_group ? $category_price : $pax * (float) $category_price;
										$category_total = $pax > 0 ? $category_total : 0;

										$min_pax = ! empty( $category['default_pax'] ) ? $category['default_pax'] : 0;
										$max_pax = ! empty( $cart_pricing['max_pax'] ) ? $cart_pricing['max_pax'] : 999;
										?>
										<div class="wp-travel-form-group" data-wpt-category="<?php echo esc_attr( $category_id ); ?>">
											<label for="adult"><?php echo esc_html( $ctitle ); ?><?php echo $category['price_per'] == 'group' ? '(' . __( 'Group', 'wp-travel' ) . ')' : ''; ?></label>
											<div>
												<div class="qty-spinner input-group bootstrap-touchspin bootstrap-touchspin-injected">
													<span class="input-group-btn input-group-prepend">
														<button data-wpt-count-down class="btn" type="button">-</button>
													</span>
													<input readonly type="number" max="<?php echo (int) $max_pax < (int) $min_pax ? 999 : (int) $max_pax; ?>" min="<?php echo (int) $min_pax; ?>" data-wpt-category-count-input="<?php echo esc_attr( $pax ); ?>" name="adult" class="wp-travel-form-control wp-travel-cart-category-qty qty form-control" min="1" value="<?php echo esc_attr( $pax ); ?>">
													<span class="input-group-btn input-group-prepend">
														<button data-wpt-count-up class="btn" type="button">+</button>
													</span>
												</div>
												<span class="prices">
													<?php echo $price_per_group ? '' : ' x ' . $currency_symbol . '<span data-wpt-category-price="' . $category_price . '">' . $category_price . '</span>'; ?>  <strong><?php echo $currency_symbol . '<span data-wpt-category-total="' . $category_total . '">' . $category_total . '</span>'; ?></strong>
												</span>
											</div>
										</div>
										<?php
									}

									if ( count( $trip_extras ) > 0 ) {
										echo '<h4>' . __( 'Trip Extras:', 'wp-travel' ) . '</h4>';
										foreach ( $trip_extras as $tx ) {
											$title = isset( $tx['title'] ) ? $tx['title'] : '';
											?>
											<div class="wp-travel-form-group" data-wpt-tx="<?php echo esc_attr( $tx['id'] ); ?>">
												<label for="tour-extras-<?php echo esc_attr( $tx['id'] ); ?>"><?php echo esc_html( $title ); ?></label>
												<?php
												if ( isset( $tx['tour_extras_metas'] ) ) :
													$tx_count    = isset( $cart_extras[ $tx['id'] ] ) ? (int) $cart_extras[ $tx['id'] ] : 0;
													$tx_price    = $tx['is_sale'] ? $tx['tour_extras_metas']['extras_item_sale_price'] : $tx['tour_extras_metas']['extras_item_price'];
													$tx_total    = $tx_count * (int) $tx_price;
													$tx_min_attr = isset( $tx['is_required'] ) && $tx['is_required'] ? 'min="1"' : '';
													$cart_total += $tx_total;
													$required    = isset( $tx['is_required'] ) && $tx['is_required']
													?>
												<div>
													<div class="input-group">
														<span class="input-group-btn input-group-prepend">
															<button class="btn" type="button" data-wpt-count-down>-</button>
														</span>
														<input readonly <?php echo $required ? 'required min="1"' : ''; ?> type="number" data-wpt-tx-count-input="<?php echo esc_attr( $tx_count ); ?>" <?php echo esc_attr( $tx_min_attr ); ?> name="" id="" class="wp-travel-form-control wp-travel-cart-extras-qty qty form-control" min="1" value="<?php echo esc_attr( $tx_count ); ?>">
														<span class="input-group-btn input-group-append"><button class="btn" type="button" data-wpt-count-up>+</button></span></div>
														<span class="prices">
															<?php echo ' x ' . $currency_symbol . '<span data-wpt-tx-price="' . $tx_price . '">' . $tx_price . '</span>' . ' = <strong>' . $currency_symbol . '<span data-wpt-tx-total="' . $tx_total . '">' . $tx_total . '</span>' . '</strong>'; ?>
														</span>
												</div>
												<?php endif; ?>
											</div>
											<?php
										}
									}
									?>
									<div class="trip-submit">
										<button type="submit" class="btn btn-primary"><?php esc_html_e( 'Update', 'wp-travel' ); ?></button>
									</div>
								</form>
							</div>
						</li>
						<?php
					}
					?>
					</ul>
					<div class="cart-summary-bottom">
						<div class="flex-wrapper">
							<form id="wp-travel-coupon-form" action="" class="update-cart-form">
								<?php
								$coupon_applied = $cart['cart']['coupon_applied'];
								$readonly       = '';
								$disabled       = '';
								if ( $coupon_applied ) {
									$readonly = 'readonly';
									$disabled = 'disabled="disabled"';
									$coupon   = (array) $cart['cart'];
								}
								?>
								<div class="field-inline">
									<input type="text" <?php echo esc_attr( $readonly ); ?> class="coupon-input-field" placeholder="<?php esc_attr_e( 'Enter promo code', 'wp-travel' ); ?>">
									<button type="submit" <?php echo esc_attr( $disabled ); ?> class="btn btn-primary" data-success-l10n="<?php esc_attr_e( 'Coupon Applied.', 'wp-travel' ); ?>">
										<?php $coupon_applied ? esc_html_e( 'Coupon Applied', 'wp-travel' ) : esc_html_e( 'Apply Coupon', 'wp-travel' ); ?>
									</button>
								</div>
							</form>
							<div class="price-calculate">
								<div class="total-price">
									<p><?php esc_html_e( 'Total:', 'wp-travel' ); ?>
										<strong>
											<?php echo $currency_symbol; ?>
											<?php echo $cart['cart']['cart_total'] < $cart['cart']['cart_total_regular'] ? '<del data-wpt-cart-full-total="">' . $cart['cart']['cart_total_regular'] . '</del>' : '<del data-wpt-cart-full-total=""></del>'; ?>
											<?php echo '<span data-wpt-cart-total="' . $cart['cart']['cart_total'] . '">' . $cart['cart']['cart_total'] . '</span>'; ?>
										</strong>
									</p>
								</div>
							</div>
						</div>
						<a href="javascript:void(0);" class="btn btn-dark checkout-btn"><?php esc_html_e( 'Proceed to Pay', 'wp-travel' ); ?></a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
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
							$price         = $category['price'];
							$price_partial = $category['price_partial'];
							$pax           = $category['pax'];

							if ( 'custom' === $category_type && isset( $category['custom_label'] ) && ! empty( $category['custom_label'] ) ) {
								$label = $category['custom_label'];
							} else {
								$label = wp_travel_get_pricing_category_by_key( $category_type );
							}
							if ( 'group' !== $price_per ) {
								$price         *= $pax;
								$price_partial *= $pax;
								$args           = array(
									'trip_id'       => $trip_id,
									'price_partial' => $price_partial,
									'pax'           => $pax,
								);
								$price_partial  = apply_filters( 'wp_travel_cart_mini_custom_partial_value', $args );
								$price_partial  = is_array( $price_partial ) && isset( $price_partial['price_partial'] ) ? $price_partial['price_partial'] : $price_partial;
								?>
								<tr class="person-count">
									<td class="left">
										<span style="display:table-row">
											<?php echo esc_html( $label ); ?>
										</span>
										<?php echo sprintf( '%2$s x %1$s', wp_travel_get_formated_price_currency( $category['price'] ), esc_html( $pax ) ); ?>
									</td>
									<td class="right">
										<?php echo wp_travel_get_formated_price_currency( $price ); ?>
									</td>
									<td style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="right product-total text-right f-partial-payment"><?php echo wp_travel_get_formated_price_currency( $price_partial ); ?></td>
								</tr>
								<?php
							} else {
								?>
								<tr class="person-count">
									<td class="left">
										<span style="display:table-row"><?php echo sprintf( esc_html__( 'Group (%s)', 'wp-travel' ), esc_html( $pax ) ); ?></span>
										<?php echo sprintf( '%2$s x %1$s', wp_travel_get_formated_price_currency( $category['price'] ), '1', esc_html( $label ) ); ?>
									</td>
									<td class="right">
										<?php echo wp_travel_get_formated_price_currency( $price ); ?>
									</td>
									<td style="display:<?php echo wp_travel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="right product-total text-right f-partial-payment"><?php echo wp_travel_get_formated_price_currency( $price_partial ); ?></td>
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
