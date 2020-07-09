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

if ( ! function_exists( 'wp_travel_key_by' ) ) {
	function wp_travel_key_by( array $object, $key_by = 'id' ) {
		return array_column(
			$object,
			null,
			$key_by
		);
	}
}

$settings      = wp_travel_get_settings();
$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

$currency_symbol = wp_travel_get_currency_symbol( $currency_code );

if ( wp_travel_is_react_version_enabled() ) {
	if ( class_exists( 'WP_Travel_Helpers_Cart' ) ) {
		$cart = WP_Travel_Helpers_Cart::get_cart();

		$cart_items = isset( $cart['cart']['cart_items'] ) ? $cart['cart']['cart_items'] : array();
	}
	?>
	<div class="order-wrapper">
		<div class="wp-travel-cart-sidebar">
			<div id="shopping-cart">
				<div class="cart-summary">
					<div id="loader" class="wp-travel-cart-loader" style="display:none;"><svg version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path d="M256.001,0c-8.284,0-15,6.716-15,15v96.4c0,8.284,6.716,15,15,15s15-6.716,15-15V15C271.001,6.716,264.285,0,256.001,0z"></path><path d="M256.001,385.601c-8.284,0-15,6.716-15,15V497c0,8.284,6.716,15,15,15s15-6.716,15-15v-96.399 C271.001,392.316,264.285,385.601,256.001,385.601z"></path><path d="M196.691,123.272l-48.2-83.485c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49l48.2,83.485 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012C198.375,139.62,200.833,130.446,196.691,123.272z"></path><path d="M389.491,457.212l-48.199-83.483c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49 l48.199,83.483c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012 C391.175,473.56,393.633,464.386,389.491,457.212z"></path><path d="M138.274,170.711L54.788,122.51c-7.176-4.144-16.348-1.685-20.49,5.49c-4.142,7.174-1.684,16.348,5.49,20.49 l83.486,48.202c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.503 C147.906,184.027,145.448,174.853,138.274,170.711z"></path><path d="M472.213,363.51l-83.484-48.199c-7.176-4.142-16.349-1.684-20.49,5.491c-4.142,7.175-1.684,16.349,5.49,20.49 l83.484,48.199c2.363,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502 C481.845,376.825,479.387,367.651,472.213,363.51z"></path><path d="M111.401,241.002H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h96.401c8.284,0,15-6.716,15-15 S119.685,241.002,111.401,241.002z"></path><path d="M497,241.002h-96.398c-8.284,0-15,6.716-15,15s6.716,15,15,15H497c8.284,0,15-6.716,15-15S505.284,241.002,497,241.002z"></path><path d="M143.765,320.802c-4.142-7.175-13.314-9.633-20.49-5.49l-83.486,48.2c-7.174,4.142-9.632,13.316-5.49,20.49 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012l83.486-48.2 C145.449,337.15,147.907,327.976,143.765,320.802z"></path><path d="M477.702,128.003c-4.142-7.175-13.315-9.632-20.49-5.49l-83.484,48.2c-7.174,4.141-9.632,13.315-5.49,20.489 c2.778,4.813,7.82,7.503,13.004,7.503c2.544,0,5.124-0.648,7.486-2.012l83.484-48.2 C479.386,144.351,481.844,135.177,477.702,128.003z"></path><path d="M191.201,368.239c-7.174-4.144-16.349-1.685-20.49,5.49l-48.2,83.485c-4.142,7.174-1.684,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502l48.2-83.485 C200.833,381.555,198.375,372.381,191.201,368.239z"></path><path d="M384.001,34.3c-7.175-4.144-16.349-1.685-20.49,5.49l-48.199,83.483c-4.143,7.174-1.685,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.502l48.199-83.483 C393.633,47.616,391.175,38.442,384.001,34.3z"></path></svg></div>
					<div class="cart-header">
						<h4 class="title"><svg enable-background="new 0 0 511.343 511.343" height="512" viewBox="0 0 511.343 511.343" width="512" xmlns="http://www.w3.org/2000/svg"><path d="m490.334 106.668h-399.808l-5.943-66.207c-.972-10.827-10.046-19.123-20.916-19.123h-42.667c-11.598 0-21 9.402-21 21s9.402 21 21 21h23.468c12.825 142.882-20.321-226.415 24.153 269.089 1.714 19.394 12.193 40.439 30.245 54.739-32.547 41.564-2.809 102.839 50.134 102.839 43.942 0 74.935-43.826 59.866-85.334h114.936c-15.05 41.455 15.876 85.334 59.866 85.334 35.106 0 63.667-28.561 63.667-63.667s-28.561-63.667-63.667-63.667h-234.526c-15.952 0-29.853-9.624-35.853-23.646l335.608-19.724c9.162-.538 16.914-6.966 19.141-15.87l42.67-170.67c3.308-13.234-6.71-26.093-20.374-26.093zm-341.334 341.337c-11.946 0-21.666-9.72-21.666-21.667s9.72-21.667 21.666-21.667c11.947 0 21.667 9.72 21.667 21.667s-9.72 21.667-21.667 21.667zm234.667 0c-11.947 0-21.667-9.72-21.667-21.667s9.72-21.667 21.667-21.667 21.667 9.72 21.667 21.667-9.72 21.667-21.667 21.667zm47.366-169.726-323.397 19.005-13.34-148.617h369.142z"></path></svg><?php _e( 'Your Order', 'wp-travel' ); ?></h4>
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

						$trip_date = ! empty( $cart_item['arrival_date'] ) ? $cart_item['arrival_date'] : '';
						$trip_time = apply_filters( 'wp_travel_use_cart_trip_time', '', $cart_item );
						$trip_time = ! empty( $trip_time ) ? ' at ' . $trip_time : '';
						?>
						<li class="list-group-item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
							<div id="loader" class="wp-travel-cart-loader" style="display:none;"><svg version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path d="M256.001,0c-8.284,0-15,6.716-15,15v96.4c0,8.284,6.716,15,15,15s15-6.716,15-15V15C271.001,6.716,264.285,0,256.001,0z"></path><path d="M256.001,385.601c-8.284,0-15,6.716-15,15V497c0,8.284,6.716,15,15,15s15-6.716,15-15v-96.399 C271.001,392.316,264.285,385.601,256.001,385.601z"></path><path d="M196.691,123.272l-48.2-83.485c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49l48.2,83.485 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012C198.375,139.62,200.833,130.446,196.691,123.272z"></path><path d="M389.491,457.212l-48.199-83.483c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49 l48.199,83.483c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012 C391.175,473.56,393.633,464.386,389.491,457.212z"></path><path d="M138.274,170.711L54.788,122.51c-7.176-4.144-16.348-1.685-20.49,5.49c-4.142,7.174-1.684,16.348,5.49,20.49 l83.486,48.202c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.503 C147.906,184.027,145.448,174.853,138.274,170.711z"></path><path d="M472.213,363.51l-83.484-48.199c-7.176-4.142-16.349-1.684-20.49,5.491c-4.142,7.175-1.684,16.349,5.49,20.49 l83.484,48.199c2.363,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502 C481.845,376.825,479.387,367.651,472.213,363.51z"></path><path d="M111.401,241.002H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h96.401c8.284,0,15-6.716,15-15 S119.685,241.002,111.401,241.002z"></path><path d="M497,241.002h-96.398c-8.284,0-15,6.716-15,15s6.716,15,15,15H497c8.284,0,15-6.716,15-15S505.284,241.002,497,241.002z"></path><path d="M143.765,320.802c-4.142-7.175-13.314-9.633-20.49-5.49l-83.486,48.2c-7.174,4.142-9.632,13.316-5.49,20.49 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012l83.486-48.2 C145.449,337.15,147.907,327.976,143.765,320.802z"></path><path d="M477.702,128.003c-4.142-7.175-13.315-9.632-20.49-5.49l-83.484,48.2c-7.174,4.141-9.632,13.315-5.49,20.489 c2.778,4.813,7.82,7.503,13.004,7.503c2.544,0,5.124-0.648,7.486-2.012l83.484-48.2 C479.386,144.351,481.844,135.177,477.702,128.003z"></path><path d="M191.201,368.239c-7.174-4.144-16.349-1.685-20.49,5.49l-48.2,83.485c-4.142,7.174-1.684,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502l48.2-83.485 C200.833,381.555,198.375,372.381,191.201,368.239z"></path><path d="M384.001,34.3c-7.175-4.144-16.349-1.685-20.49,5.49l-48.199,83.483c-4.143,7.174-1.685,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.502l48.199-83.483 C393.633,47.616,391.175,38.442,384.001,34.3z"></path></svg></div>
							<button type="button" class="del-btn" data-l10n="<?php echo esc_attr( sprintf( __( 'Are you sure you want to remove \'%s\' from cart?', 'wp-travel' ), $trip_data['title'] ) ); ?>">×</button>
							<div>
								<div class="content-left">
									<?php echo get_the_post_thumbnail( $trip_data['id'], 'thumbnail' ); ?>
								</div>
								<div class="content-right">
									<h5><a href="javascript:void(0);"><?php echo esc_html( $trip_data['title'] ) . '(' . $cart_pricing['title'] . ')'; ?></a></h5>
									<div class="meta-content">
										<span>
											<strong><?php _e( 'Date: ', 'wp-travel' ); ?></strong><span><?php echo $trip_date . $trip_time; ?></span>
										</span>
										<?php
										foreach ( $cart_pax as $category_id => $detail ) {
											$category = $categories[ $category_id ];
											$ctitle   = $category['term_info']['title'];
											$pax      = (int) $detail['pax'];
											echo '<span><span data-wpt-category-count="' . esc_attr( $category_id ) . "\">{$pax}</span> x {$ctitle}</span>";
										}
										// Trip Total Calculation
										$trip_total = $cart_item['trip_price'];
										foreach ( $cart_extras as $xid => $count ) {
											$tx = $trip_extras[ $xid ];
											if ( isset( $tx['tour_extras_metas'] ) ) :
												$tx_price    = $tx['is_sale'] ? $tx['tour_extras_metas']['extras_item_sale_price'] : $tx['tour_extras_metas']['extras_item_price'];
												$tx_total    = (int) $count * (int) $tx_price;
												$trip_total += $tx_total;
											endif;
										}
										?>
									</div>
								</div>
								<div class="price">
									<span><?php echo '<span data-wpt-item-total="' . $trip_total . '">' . wp_travel_get_formated_price_currency( $trip_total ) . '</span>'; ?></span>
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


										if ( isset( $category['has_group_price'] ) && $category['has_group_price'] ) {
											$group_prices = $category['group_prices'];
											$group_price  = array();
											foreach ( $group_prices as $gp ) {
												if ( $pax >= $gp['min_pax'] && $pax <= $gp['max_pax'] ) {
													$group_price = $gp;
													break;
												}
											}
											$category_price = isset( $group_price['price'] ) ? $group_price['price'] : $category_price;
										}
										$category_total = $price_per_group ? $category_price : $pax * (float) $category_price;

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
													<?php echo $price_per_group ? '' : ' x <span data-wpt-category-price="' . $category_price . '">' . wp_travel_get_formated_price_currency( $category_price ) . '</span>'; ?>  <strong><?php echo '<span data-wpt-category-total="' . $category_total . '">' . wp_travel_get_formated_price_currency( $category_total ) . '</span>'; ?></strong>
												</span>
											</div>
										</div>
										<?php
									}

									if ( count( $trip_extras ) > 0 && count( $cart_extras ) > 0 ) {
										echo '<h4>' . __( 'Trip Extras:', 'wp-travel' ) . '</h4>';
										foreach ( $trip_extras as $tx ) {
											if ( ! isset( $cart_extras[ $tx['id'] ] ) || $cart_extras[ $tx['id'] ] <= 0 ) {
												continue;
											}
											$title    = isset( $tx['title'] ) ? $tx['title'] : '';
											$tx_count = 0;
											$tx_price = 0;
											?>
											<div class="wp-travel-form-group" data-wpt-tx="<?php echo esc_attr( $tx['id'] ); ?>">
												<label for="tour-extras-<?php echo esc_attr( $tx['id'] ); ?>"><?php echo esc_html( $title ); ?></label>
												<?php
												if ( isset( $tx['tour_extras_metas'] ) ) :
													$tx_count    = isset( $cart_extras[ $tx['id'] ] ) ? (int) $cart_extras[ $tx['id'] ] : 0;
													$tx_price    = $tx['is_sale'] ? $tx['tour_extras_metas']['extras_item_sale_price'] : $tx['tour_extras_metas']['extras_item_price'];
													$tx_total    = $tx_count * (int) $tx_price;
													$tx_min_attr = isset( $tx['is_required'] ) && $tx['is_required'] ? 'min="1"' : '';
													// $cart_total += $tx_total;
													$required = isset( $tx['is_required'] ) && $tx['is_required'];
													?>
												<div>
													<div class="input-group">
														<span class="input-group-btn input-group-prepend">
															<button class="btn" type="button" data-wpt-count-down>-</button>
														</span>
														<input id="<?php echo esc_attr( 'tx_' . $tx['id'] ); ?>" name="<?php echo esc_attr( 'tx_' . $tx['id'] ); ?>" readonly <?php echo $required ? 'required min="1"' : 'min="0"'; ?> type="text" data-wpt-tx-count-input="<?php echo esc_attr( $tx_count ); ?>" name="" class="wp-travel-form-control wp-travel-cart-extras-qty qty form-control" value="<?php echo esc_attr( $tx_count ); ?>" />
														<span class="input-group-btn input-group-append"><button class="btn" type="button" data-wpt-count-up>+</button></span></div>
														<span class="prices">
															<?php echo ' x <span data-wpt-tx-price="' . $tx_price . '">' . wp_travel_get_formated_price_currency( $tx_price ) . '</span>' . '<strong><span data-wpt-tx-total="' . $tx_total . '">' . wp_travel_get_formated_price_currency( $tx_total ) . '</span>' . '</strong>'; ?>
														</span>
												</div>
												<?php endif; ?>
											</div>
											<?php
										}
									}
									?>
									<div class="trip-submit">
										<button type="submit" disabled="disabled" class="btn btn-primary"><?php esc_html_e( 'Update', 'wp-travel' ); ?></button>
									</div>
								</form>
							</div>
						</li>
						<?php
					}
					?>
					</ul>
					<ul class="extra-fields">
						<li data-wpt-extra-field>
							<label><?php esc_html_e( 'Subtotal', 'wp-travel' ); ?></label>
							<div class="price"><strong data-wpt-cart-subtotal="<?php echo esc_attr( $cart['cart']['total']['cart_total'] ); ?>"><?php echo wp_travel_get_formated_price_currency( $cart['cart']['total']['cart_total'] ); ?></strong></div>
						</li>
						<?php
						$display  = $cart['cart']['total']['discount'] > 0 ? '' : 'display:none;';
						$discount = $cart['cart']['total']['discount'] > 0 ? $cart['cart']['total']['discount'] : 0;
						?>
						<li style="<?php echo esc_attr( $display ); ?>" data-wpt-extra-field>
							<label><?php esc_html_e( 'Discount:', 'wp-travel' ); ?></label>
							<div class="price">
								<strong data-wpt-cart-discount="<?php echo esc_attr( $discount ); ?>"><?php echo '- ' . wp_travel_get_formated_price_currency( $discount ); ?></strong>
							</div>
						</li>
						<?php
						$display  = $cart['cart']['tax'] ? '' : 'display:none;';
						$tax_rate = $cart['cart']['tax'] ? $cart['cart']['tax'] : '';
						$tax      = $cart['cart']['tax'] ? $cart['cart']['total']['tax'] : 0;
						?>
						<li style="<?php echo esc_attr( $display ); ?>" data-wpt-extra-field>
							<label><?php echo sprintf( esc_html__( 'Tax(%s):', 'wp-travel' ), $tax_rate . '%' ); ?></label>
							<div class="price"><strong data-wpt-cart-tax="<?php echo esc_attr( $tax ); ?>"><?php echo '+ ' . wp_travel_get_formated_price_currency( $tax ); ?></strong></div>
						</li>
					</ul>
					<div class="cart-summary-bottom">
						<div class="flex-wrapper">
							<form id="wp-travel-coupon-form" action="" class="update-cart-form">
								<?php
								$coupon_applied = isset( $cart['cart']['coupon']['coupon_id'] );
								$readonly       = '';
								$disabled       = '';
								$coupon_code    = '';
								if ( $coupon_applied ) {
									$readonly    = 'readonly';
									$disabled    = 'disabled="disabled"';
									$coupon      = (array) $cart['cart'];
									$coupon_code = $cart['cart']['coupon']['coupon_code'];
								}
								?>
								<div class="field-inline">
									<input type="text" <?php echo esc_attr( $readonly ); ?> value="<?php echo esc_attr( $coupon_code ); ?>" class="coupon-input-field" placeholder="<?php esc_attr_e( 'Enter promo code', 'wp-travel' ); ?>">
									<button type="submit" <?php echo esc_attr( $disabled ); ?> class="btn btn-primary" data-success-l10n="<?php esc_attr_e( 'Coupon Applied.', 'wp-travel' ); ?>">
										<?php $coupon_applied ? esc_html_e( 'Coupon Applied', 'wp-travel' ) : esc_html_e( 'Apply Coupon', 'wp-travel' ); ?>
									</button>
								</div>
							</form>
							<div class="price-calculate">
								<div class="total-price">
									<p><?php esc_html_e( 'Total:', 'wp-travel' ); ?>
										<strong>
											<?php echo (float) $cart['cart']['total']['cart_total'] > (float) $cart['cart']['total']['total'] ? '<del data-wpt-cart-full-total="">' . wp_travel_get_formated_price_currency( $cart['cart']['total']['cart_total'] ) . '</del>' : '<del data-wpt-cart-full-total=""></del>'; ?>
											<?php echo '<span data-wpt-cart-total="' . $cart['cart']['total']['total'] . '">' . wp_travel_get_formated_price_currency( $cart['cart']['total']['total'] ) . '</span>'; ?>
										</strong>
									</p>
									<p style="display:none;"><?php esc_html_e( 'Payment Amount:', 'wp-travel' ); ?>
										<strong>
											<?php echo '<span data-wpt-cart-partial-total="' . $cart['cart']['total']['total_partial'] . '">' . wp_travel_get_formated_price_currency( $cart['cart']['total']['total_partial'] ) . '</span>'; ?>
										</strong>
									</p>
								</div>
							</div>
						</div>
						<!-- <a href="javascript:void(0);" class="btn btn-dark checkout-btn"><?php esc_html_e( 'Proceed to Pay', 'wp-travel' ); ?></a> -->
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
