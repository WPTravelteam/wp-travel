<?php
/**
 * WP Travel Checkout.
 *
 * @package WP_Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'wptravel_key_by' ) ) {
	function wptravel_key_by( array $object, $key_by = 'id' ) {
		return array_column(
			$object,
			null,
			$key_by
		);
	}
}

$settings      = wptravel_get_settings();
$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';

$currency_symbol = wptravel_get_currency_symbol( $currency_code );
$strings         = WpTravel_Helpers_Strings::get();
$edit_label      = $strings['edit'];

if ( wptravel_is_react_version_enabled() ) {
	if ( class_exists( 'WP_Travel_Helpers_Cart' ) ) {
		$cart = WP_Travel_Helpers_Cart::get_cart();

		$cart_items = isset( $cart['cart']['cart_items'] ) ? $cart['cart']['cart_items'] : array();
	}

	$coupon_applied = isset( $cart['cart']['coupon']['coupon_id'] );
	$readonly       = '';
	$disabled       = '';
	$coupon_code    = WpTravel_Helpers_Coupon::get_default_coupon();
	$coupon_type    = '';
	if ( $coupon_applied ) {
		$readonly    = 'readonly';
		$disabled    = 'disabled="disabled"';
		$coupon      = (array) $cart['cart'];
		$coupon_code = $cart['cart']['coupon']['coupon_code'];
		$coupon_type = $cart['cart']['coupon']['type'];
	}

	?>
	
	<div class="order-wrapper">
		<div class="wp-travel-cart-sidebar">
			<div id="shopping-cart">
				<div class="cart-summary">
					<div id="loader" class="wp-travel-cart-loader" style="display:none;"><svg version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path d="M256.001,0c-8.284,0-15,6.716-15,15v96.4c0,8.284,6.716,15,15,15s15-6.716,15-15V15C271.001,6.716,264.285,0,256.001,0z"></path><path d="M256.001,385.601c-8.284,0-15,6.716-15,15V497c0,8.284,6.716,15,15,15s15-6.716,15-15v-96.399 C271.001,392.316,264.285,385.601,256.001,385.601z"></path><path d="M196.691,123.272l-48.2-83.485c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49l48.2,83.485 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012C198.375,139.62,200.833,130.446,196.691,123.272z"></path><path d="M389.491,457.212l-48.199-83.483c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49 l48.199,83.483c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012 C391.175,473.56,393.633,464.386,389.491,457.212z"></path><path d="M138.274,170.711L54.788,122.51c-7.176-4.144-16.348-1.685-20.49,5.49c-4.142,7.174-1.684,16.348,5.49,20.49 l83.486,48.202c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.503 C147.906,184.027,145.448,174.853,138.274,170.711z"></path><path d="M472.213,363.51l-83.484-48.199c-7.176-4.142-16.349-1.684-20.49,5.491c-4.142,7.175-1.684,16.349,5.49,20.49 l83.484,48.199c2.363,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502 C481.845,376.825,479.387,367.651,472.213,363.51z"></path><path d="M111.401,241.002H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h96.401c8.284,0,15-6.716,15-15 S119.685,241.002,111.401,241.002z"></path><path d="M497,241.002h-96.398c-8.284,0-15,6.716-15,15s6.716,15,15,15H497c8.284,0,15-6.716,15-15S505.284,241.002,497,241.002z"></path><path d="M143.765,320.802c-4.142-7.175-13.314-9.633-20.49-5.49l-83.486,48.2c-7.174,4.142-9.632,13.316-5.49,20.49 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012l83.486-48.2 C145.449,337.15,147.907,327.976,143.765,320.802z"></path><path d="M477.702,128.003c-4.142-7.175-13.315-9.632-20.49-5.49l-83.484,48.2c-7.174,4.141-9.632,13.315-5.49,20.489 c2.778,4.813,7.82,7.503,13.004,7.503c2.544,0,5.124-0.648,7.486-2.012l83.484-48.2 C479.386,144.351,481.844,135.177,477.702,128.003z"></path><path d="M191.201,368.239c-7.174-4.144-16.349-1.685-20.49,5.49l-48.2,83.485c-4.142,7.174-1.684,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502l48.2-83.485 C200.833,381.555,198.375,372.381,191.201,368.239z"></path><path d="M384.001,34.3c-7.175-4.144-16.349-1.685-20.49,5.49l-48.199,83.483c-4.143,7.174-1.685,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.502l48.199-83.483 C393.633,47.616,391.175,38.442,384.001,34.3z"></path></svg></div>
					<div class="cart-header">
						<h4 class="title"><svg enable-background="new 0 0 511.343 511.343" height="20" viewBox="0 0 511.343 511.343" width="20" xmlns="http://www.w3.org/2000/svg"><path d="m490.334 106.668h-399.808l-5.943-66.207c-.972-10.827-10.046-19.123-20.916-19.123h-42.667c-11.598 0-21 9.402-21 21s9.402 21 21 21h23.468c12.825 142.882-20.321-226.415 24.153 269.089 1.714 19.394 12.193 40.439 30.245 54.739-32.547 41.564-2.809 102.839 50.134 102.839 43.942 0 74.935-43.826 59.866-85.334h114.936c-15.05 41.455 15.876 85.334 59.866 85.334 35.106 0 63.667-28.561 63.667-63.667s-28.561-63.667-63.667-63.667h-234.526c-15.952 0-29.853-9.624-35.853-23.646l335.608-19.724c9.162-.538 16.914-6.966 19.141-15.87l42.67-170.67c3.308-13.234-6.71-26.093-20.374-26.093zm-341.334 341.337c-11.946 0-21.666-9.72-21.666-21.667s9.72-21.667 21.666-21.667c11.947 0 21.667 9.72 21.667 21.667s-9.72 21.667-21.667 21.667zm234.667 0c-11.947 0-21.667-9.72-21.667-21.667s9.72-21.667 21.667-21.667 21.667 9.72 21.667 21.667-9.72 21.667-21.667 21.667zm47.366-169.726-323.397 19.005-13.34-148.617h369.142z"></path></svg><?php _e( apply_filters( 'wp_travel_chekout_mini_order_txt', 'Your Order' ), 'wp-travel' ); ?></h4>
						<small class="subtitle"><?php echo wp_kses_post( sprintf( __( 'You have selected %s items in your cart', 'wp-travel' ), '<strong data-wpt-cart-item-count="">' . count( $cart_items ) . '</strong>' ) ); ?></small>
					</div>
					<ul class="cart-summary-content list-group">
					<?php
					$cart_number = 1;
					foreach ( $cart_items as $cart_id => $cart_item ) {

						$pricing_id   = $cart_item['pricing_id'];
						$trip_id      = $cart_item['trip_data']['id'];
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
						$categories  = isset( $cart_pricing['categories'] ) ? wptravel_key_by( $cart_pricing['categories'] ) : array(); // All categories.
						$trip_extras = isset( $cart_pricing['trip_extras'] ) ? wptravel_key_by( $cart_pricing['trip_extras'] ) : array(); // All trip extras.
						if ( count( $trip_extras ) > 0 ) {
							$extras_args = array( 'post__in' => $trip_extras );
							$result      = WP_Travel_Helpers_Trip_Extras::get_trip_extras( $extras_args );
							if ( is_array( $result ) && 'WP_TRAVEL_TRIP_EXTRAS' === $result['code'] && isset( $result['trip_extras'] ) && count( $result['trip_extras'] ) > 0 ) {
								$trip_extras = $result['trip_extras'];
							}
						}

						$cart_extras = (array) $cart_item['extras'];
						if ( ! empty( $cart_extras ) ) {
							$cart_extras = array_combine( $cart_extras['id'], $cart_extras['qty'] );
						}

						$cart_pax   = (array) $cart_item['trip'];
						$cart_total = 0;

						$trip_date          = ! empty( $cart_item['arrival_date'] ) ? $cart_item['arrival_date'] : '';
						$trip_time          = apply_filters( 'wp_travel_use_cart_trip_time', '', $cart_item );
						$trip_time          = ! empty( $trip_time ) ? ' at ' . $trip_time : '';
						$pricing_name       = wptravel_get_trip_pricing_name( $trip_id, $pricing_id );
						$trip_total         = $cart_item['trip_total'];
						$trip_total_partial = $cart_item['trip_total_partial'];
						$payout_percent     = $cart_item['payout_percent'];
						$trip_discount      = isset( $cart_item['discount'] ) ? $cart_item['discount'] : 0;
						?>
						<li class="list-group-item" data-cart-id="<?php echo esc_attr( $cart_id ); ?>">
							<div id="loader" class="wp-travel-cart-loader" style="display:none;"><svg version="1.1" x="0px" y="0px" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"><path d="M256.001,0c-8.284,0-15,6.716-15,15v96.4c0,8.284,6.716,15,15,15s15-6.716,15-15V15C271.001,6.716,264.285,0,256.001,0z"></path><path d="M256.001,385.601c-8.284,0-15,6.716-15,15V497c0,8.284,6.716,15,15,15s15-6.716,15-15v-96.399 C271.001,392.316,264.285,385.601,256.001,385.601z"></path><path d="M196.691,123.272l-48.2-83.485c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49l48.2,83.485 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012C198.375,139.62,200.833,130.446,196.691,123.272z"></path><path d="M389.491,457.212l-48.199-83.483c-4.142-7.175-13.316-9.633-20.49-5.49c-7.174,4.142-9.632,13.316-5.49,20.49 l48.199,83.483c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012 C391.175,473.56,393.633,464.386,389.491,457.212z"></path><path d="M138.274,170.711L54.788,122.51c-7.176-4.144-16.348-1.685-20.49,5.49c-4.142,7.174-1.684,16.348,5.49,20.49 l83.486,48.202c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.503 C147.906,184.027,145.448,174.853,138.274,170.711z"></path><path d="M472.213,363.51l-83.484-48.199c-7.176-4.142-16.349-1.684-20.49,5.491c-4.142,7.175-1.684,16.349,5.49,20.49 l83.484,48.199c2.363,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502 C481.845,376.825,479.387,367.651,472.213,363.51z"></path><path d="M111.401,241.002H15c-8.284,0-15,6.716-15,15s6.716,15,15,15h96.401c8.284,0,15-6.716,15-15 S119.685,241.002,111.401,241.002z"></path><path d="M497,241.002h-96.398c-8.284,0-15,6.716-15,15s6.716,15,15,15H497c8.284,0,15-6.716,15-15S505.284,241.002,497,241.002z"></path><path d="M143.765,320.802c-4.142-7.175-13.314-9.633-20.49-5.49l-83.486,48.2c-7.174,4.142-9.632,13.316-5.49,20.49 c2.778,4.813,7.82,7.502,13.004,7.502c2.545,0,5.124-0.648,7.486-2.012l83.486-48.2 C145.449,337.15,147.907,327.976,143.765,320.802z"></path><path d="M477.702,128.003c-4.142-7.175-13.315-9.632-20.49-5.49l-83.484,48.2c-7.174,4.141-9.632,13.315-5.49,20.489 c2.778,4.813,7.82,7.503,13.004,7.503c2.544,0,5.124-0.648,7.486-2.012l83.484-48.2 C479.386,144.351,481.844,135.177,477.702,128.003z"></path><path d="M191.201,368.239c-7.174-4.144-16.349-1.685-20.49,5.49l-48.2,83.485c-4.142,7.174-1.684,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.227-2.69,13.004-7.502l48.2-83.485 C200.833,381.555,198.375,372.381,191.201,368.239z"></path><path d="M384.001,34.3c-7.175-4.144-16.349-1.685-20.49,5.49l-48.199,83.483c-4.143,7.174-1.685,16.348,5.49,20.49 c2.362,1.364,4.941,2.012,7.486,2.012c5.184,0,10.226-2.69,13.004-7.502l48.199-83.483 C393.633,47.616,391.175,38.442,384.001,34.3z"></path></svg></div>
							<div>
								<div class="content-left">
									<div class="img-wrapper">
										<?php
										if ( get_the_post_thumbnail( $trip_data['id'], 'thumbnail' ) ) {
											echo get_the_post_thumbnail( $trip_data['id'], 'thumbnail' );
										} else {
											?>
											<img width="150" height="150" src="<?php echo esc_url( wptravel_get_post_placeholder_image_url() ); ?>" class="attachment-thumbnail size-thumbnail wp-post-image" alt="" loading="lazy">
											<?php
										}
										?>
									</div>

								</div>

								<div class="trip-content">
									<div class="trip-item-name-price">
										<div class="trip-name">
											<h5><a href="<?php echo esc_url( get_the_permalink( $trip_id ) ); ?>"><?php echo esc_html( $pricing_name ); ?></a></h5>
											<?php if ( $coupon_applied && 'fixed' !== $coupon_type ) : ?>
												<span class="tooltip group-discount-button <?php echo esc_attr( ( ! $trip_discount ) ? 'no-discount' : '' ); ?> ">
													<svg version="1.1" x="0px" y="0px" viewBox="0 0 512.003 512.003" style={{ enableBackground: 'new 0 0 512.003 512.003' }}><path d="M477.958,262.633c-2.06-4.215-2.06-9.049,0-13.263l19.096-39.065c10.632-21.751,2.208-47.676-19.178-59.023l-38.41-20.38
															c-4.144-2.198-6.985-6.11-7.796-10.729l-7.512-42.829c-4.183-23.846-26.241-39.87-50.208-36.479l-43.053,6.09
															c-4.647,0.656-9.242-0.838-12.613-4.099l-31.251-30.232c-17.401-16.834-44.661-16.835-62.061,0L193.72,42.859
															c-3.372,3.262-7.967,4.753-12.613,4.099l-43.053-6.09c-23.975-3.393-46.025,12.633-50.208,36.479l-7.512,42.827
															c-0.811,4.62-3.652,8.531-7.795,10.73l-38.41,20.38c-21.386,11.346-29.81,37.273-19.178,59.024l19.095,39.064
															c2.06,4.215,2.06,9.049,0,13.263l-19.096,39.064c-10.632,21.751-2.208,47.676,19.178,59.023l38.41,20.38
															c4.144,2.198,6.985,6.11,7.796,10.729l7.512,42.829c3.808,21.708,22.422,36.932,43.815,36.93c2.107,0,4.245-0.148,6.394-0.452
															l43.053-6.09c4.643-0.659,9.241,0.838,12.613,4.099l31.251,30.232c8.702,8.418,19.864,12.626,31.03,12.625
															c11.163-0.001,22.332-4.209,31.03-12.625l31.252-30.232c3.372-3.261,7.968-4.751,12.613-4.099l43.053,6.09
															c23.978,3.392,46.025-12.633,50.208-36.479l7.513-42.827c0.811-4.62,3.652-8.531,7.795-10.73l38.41-20.38
															c21.386-11.346,29.81-37.273,19.178-59.024L477.958,262.633z M196.941,123.116c29.852,0,54.139,24.287,54.139,54.139
															s-24.287,54.139-54.139,54.139s-54.139-24.287-54.139-54.139S167.089,123.116,196.941,123.116z M168.997,363.886
															c-2.883,2.883-6.662,4.325-10.44,4.325s-7.558-1.441-10.44-4.325c-5.766-5.766-5.766-15.115,0-20.881l194.889-194.889
															c5.765-5.766,15.115-5.766,20.881,0c5.766,5.766,5.766,15.115,0,20.881L168.997,363.886z M315.061,388.888
															c-29.852,0-54.139-24.287-54.139-54.139s24.287-54.139,54.139-54.139c29.852,0,54.139,24.287,54.139,54.139
															S344.913,388.888,315.061,388.888z"></path><path d="M315.061,310.141c-13.569,0-24.609,11.039-24.609,24.608s11.039,24.608,24.609,24.608
															c13.569,0,24.608-11.039,24.608-24.608S328.63,310.141,315.061,310.141z"></path><path d="M196.941,152.646c-13.569,0-24.608,11.039-24.608,24.608c0,13.569,11.039,24.609,24.608,24.609
															c13.569,0,24.609-11.039,24.609-24.609C221.549,163.686,210.51,152.646,196.941,152.646z"></path>
													</svg>
													<span class="discount-price">

													<?php echo wptravel_get_formated_price_currency( $trip_discount ); ?>
													</span>
												</span>
											<?php endif ?>
										</div>
										<span class="trip-price">
											<span data-wpt-item-total="<?php echo esc_attr( $trip_total ); ?>" >
												<?php
												if ( $coupon_applied && $trip_discount && 'percentage' === $coupon_type ) :
													?>
													 <del> <?php endif ?>
													<?php echo wptravel_get_formated_price_currency( $trip_total ); ?>
												<?php
												if ( $coupon_applied && $trip_discount && 'percentage' === $coupon_type ) :
													?>
													 </del> <?php endif ?>
											</span>

											<?php if ( $coupon_applied && $trip_discount && 'percentage' === $coupon_type ) : ?>
												<span data-wpt-item-discounted-total="<?php echo esc_attr( $trip_total - $trip_discount ); ?>" >
													<?php echo wptravel_get_formated_price_currency( $trip_total - $trip_discount ); ?>
												</span>
											<?php endif; ?>
										</span>
									</div>
									<!-- <div style="display:none;" class="trip__partial-payment_detail" data-wpt-trip-partial-total="<?php echo esc_attr( $trip_total_partial ); ?>">
										<div class="partial-payment-info">
											<h5>Pay <?php echo esc_html( $payout_percent ); ?>% upfront</h5>
										</div>
										<span>
											<?php
											if ( $trip_discount ) {
												$partial_discount = ( $trip_discount * $payout_percent ) / 100;

												echo wptravel_get_formated_price_currency( $trip_total_partial - $partial_discount );

											} else {
												echo wptravel_get_formated_price_currency( $trip_total_partial );
											}
											?>
										</span>
									</div> -->
									<div class="trip-meta-content">
										<span class="date">
											<span><?php echo $trip_date . $trip_time; ?></span>
										</span>
										<?php
										// This will only for displaying purpose. Need to change this in update method[wp_travel_group_discount_price] of cart class also to update cart data.
										$total_pricing_pax = 0; // Total Pax for group discount in Pricing need total pax from the pricing.
										foreach ( $cart_pax as $category_id => $detail ) {
											$category           = isset( $categories[ $category_id ] ) ? $categories[ $category_id ] : array(); // undefined offset fixes.
											$ctitle             = isset( $category['term_info']['title'] ) ? esc_html( $category['term_info']['title'] ) : '';
											$pax                = (int) $detail['pax'];
											$total_pricing_pax += $pax;
											if ( $pax < 1 ) {
												continue;
											}
											echo '<span><span data-wpt-category-count="' . esc_attr( $category_id ) . "\">{$pax}</span> x {$ctitle}</span>";
										}
										?>
									</div>
								</div>
							</div>
							<div class="checkout-trip-extras">
								<?php 
									if ( count( $trip_extras ) > 0 && count( $cart_extras ) > 0 ) {
										echo '<h4>' . __( 'Trip Extras:', 'wp-travel' ) . '</h4>';
										foreach ( $trip_extras as $tx ) {
											$title    = isset( $tx['title'] ) ? $tx['title'] : '';
											$tx_count = 0;
											$tx_price = 0;
											?>
											<div class="wp-travel-form-group" data-wpt-tx="<?php echo esc_attr( $tx['id'] ); ?>">
												<label for="tour-extras-<?php echo esc_attr( $tx['id'] ); ?>"><?php echo esc_html( $title ); ?></label>
												<?php
												if ( isset( $tx['tour_extras_metas'] ) ) :
													$tx_count    = isset( $cart_extras[ $tx['id'] ] ) ? (int) $cart_extras[ $tx['id'] ] : 0;
													$tx_price    = $tx['tour_extras_metas']['extras_item_price'];
													$tx_total    = $tx_count * (int) $tx_price;
													$tx_min_attr = isset( $tx['is_required'] ) && $tx['is_required'] ? 'min="1"' : '';
													// $cart_total += $tx_total;
													$required = isset( $tx['is_required'] ) && $tx['is_required'];
												?>
												<div class="input-group">
													<span class="quantity"><?php echo esc_attr( $tx_count ); ?></span>
													<span class="prices">
														<?php echo ' x <span data-wpt-tx-price="' . $tx_price . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $tx_price ) ) . '</span> = ' . '<strong><span data-wpt-tx-total="' . $tx_total . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $tx_total ) ) . '</span>' . '</strong>'; ?>
													</span>
												</div>
												<?php endif; ?>
											</div>
											<?php
										}
									}
								?>
							</div>
							
							<div class="cart-item-items">
								<a href="javascript:void(0);" class="del-btn" data-l10n="<?php echo esc_attr( sprintf( __( 'Are you sure you want to remove \'%s\' from cart?', 'wp-travel' ), $trip_data['title'] ) ); ?>"><i class="wt-icon wt-icon-trash-alt" aria-hidden="true"></i> <?php _e( 'Remove', 'wp-travel' ); ?></a>

								<?php if ( ! $coupon_applied ) : ?>
									<div class="edit-trip">
										<a href="javascript:void(0);" class="edit" data-wpt-target-cart-id="<?php echo esc_attr( $cart_id ); ?>" data-wpt-target-trip="<?php echo esc_attr( $trip_data['id'] ); ?>" data-wpt-target-pricing="<?php echo esc_attr( $cart_pricing['id'] ); ?>"><i class="wt-icon wt-icon-pencil-alt" aria-hidden="true"></i> <?php echo esc_html( $edit_label ); ?></a>
									</div>
								<?php endif; ?>
							</div>
							<div class="update-fields-collapse" >
								<form class="wp-travel__cart-item" action="">
									<?php
									
									$cart_min_pax = 0;
									foreach ( $cart_pax as $category_id => $detail ) {
										$category = isset( $categories[ $category_id ] ) ? $categories[ $category_id ] : array(); // undefined offset fixes.
										$ctitle   = $category['term_info']['title'];
										$pax      = (int) $detail['pax'];

										$price_per_group = $category['price_per'] == 'group';

										$category_price = $category['is_sale'] ? $category['sale_price'] : $category['regular_price'];
										$category_price = $category_price ? $category_price : 0; // Temp fixes.

										$pricing_group_price = isset( $cart_pricing['has_group_price'] ) && $cart_pricing['has_group_price'];
										if ( $pricing_group_price ) {
											$group_prices = $cart_pricing['group_prices'];
											$group_price  = array();
											foreach ( $group_prices as $gp ) {
												if ( $total_pricing_pax >= $gp['min_pax'] && $total_pricing_pax <= $gp['max_pax'] ) {
													$group_price = $gp;
													break;
												}
											}
											$category_price = isset( $group_price['price'] ) ? $group_price['price'] : $category_price;
											$category_price = $category_price ? $category_price : 0; // Temp fixes.
										} elseif ( isset( $category['has_group_price'] ) && $category['has_group_price'] ) {
											$group_prices = $category['group_prices'];
											$group_price  = array();
											foreach ( $group_prices as $gp ) {
												if ( $pax >= $gp['min_pax'] && $pax <= $gp['max_pax'] ) {
													$group_price = $gp;
													break;
												}
											}
											$category_price = isset( $group_price['price'] ) ? $group_price['price'] : $category_price;
											$category_price = $category_price ? $category_price : 0; // Temp fixes.
										}
										// $category_price = apply_filters( 'wp_travel_multiple_currency', $category_price );
										if ( $pricing_group_price ) { // Pricing group price treat as price per only
											$category_total = $pax * (float) $category_price;
										} else {
											$category_total = $price_per_group ? $category_price : $pax * (float) $category_price;
										}

										$min_pax = ! empty( $category['default_pax'] ) ? $category['default_pax'] : 0;
										$max_pax = ! empty( $cart_pricing['max_pax'] ) ? $cart_pricing['max_pax'] : 999;
										?>
										<div class="wp-travel-form-group" data-wpt-category="<?php echo esc_attr( $category_id ); ?>">
											<label for="adult"><?php echo esc_html( $ctitle ); ?><?php echo $category['price_per'] == 'group' ? '(' . __( 'Group', 'wp-travel' ) . ')' : ''; ?></label>
											<div>
												<div class="qty-spinner input-group bootstrap-touchspin bootstrap-touchspin-injected">
													<span class="input-group-btn input-group-prepend" >
														<button data-wpt-count-down class="btn edit-pax-selector-qty" type="button"  data-cart="<?php echo $cart_number ?>" data-minpax="<?php echo $cart_pricing['min_pax'] ?>" data-allpricing="<?php echo $cart_item['trip_data']['enable_pax_all_pricing']; ?>">-</button>
													</span>
													<input type="hidden" class="trip-min-pax" value="<?php $cart_pricing['min_pax'] ?>" >
													<input readonly type="number" max="<?php echo (int) $max_pax < (int) $min_pax ? 999 : (int) $max_pax; ?>" min="<?php echo (int) $min_pax; ?>" data-wpt-category-count-input="<?php echo esc_attr( $pax ); ?>" name="adult" class="wp-travel-form-control wp-travel-cart-category-qty qty form-control edit-pax-<?php echo $cart_number ?>" min="1" value="<?php echo esc_attr( $pax ); ?>">
													<span class="input-group-btn input-group-prepend">
														<button data-wpt-count-up class="btn edit-pax-selector-qty" type="button" data-cart="<?php echo $cart_number ?>" data-minpax="<?php echo $cart_pricing['min_pax'] ?>" data-allpricing="<?php echo $cart_item['trip_data']['enable_pax_all_pricing']; ?>">+</button>
													</span>
												</div>
												<span class="prices">
													<?php echo $price_per_group ? '' : ' x <span data-wpt-category-price="' . $category_price . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $category_price ) ) . '</span>'; ?>  <strong><?php echo '<span data-wpt-category-total="' . $category_total . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $category_total ) ) . '</span>'; ?></strong>
												</span>
											</div>
										</div>
										<?php
										$cart_min_pax = $cart_pricing['min_pax'];
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
															<?php echo ' x <span data-wpt-tx-price="' . $tx_price . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $tx_price ) ) . '</span>' . '<strong><span data-wpt-tx-total="' . $tx_total . '">' . wptravel_get_formated_price_currency( WpTravel_Helpers_Trip_Pricing_Categories::get_converted_price( $tx_total ) ) . '</span>' . '</strong>'; ?>
														</span>
												</div>
												<?php endif; ?>
											</div>
											<?php
										}
									}
									?>
									<div class="trip-submit">
										<button type="submit" disabled="disabled" class="btn btn-primary cart-edit-<?php echo $cart_number ?>"><?php esc_html_e( 'Update', 'wp-travel' ); ?></button>
									</div>
								</form>
							</div>
						</li>
						<?php
						$cart_number++;
					}
					?>
					</ul>
					<?php

					$subtotal      = $cart['cart']['total']['cart_total'];
					$discount      = $cart['cart']['total']['discount'] > 0 ? $cart['cart']['total']['discount'] : 0;
					$total         = $cart['cart']['total']['total'] > 0 ? $cart['cart']['total']['total'] : 0;
					$total_partial = $cart['cart']['total']['total_partial'];

					if ( $coupon_applied && 'percentage' === $coupon_type ) {
						$subtotal = $subtotal - $discount;
					}
					?>
					<ul class="extra-fields">
						<?php if ( $coupon_applied || ( isset( $cart['cart']['total']['tax'] ) && $cart['cart']['total']['tax'] > 0 ) ) : ?>
							<li data-wpt-extra-field>
								<label><?php esc_html_e( 'Subtotal', 'wp-travel' ); ?></label>
								<div class="price"><strong data-wpt-cart-subtotal="<?php echo esc_attr( $subtotal ); ?>"><?php echo wptravel_get_formated_price_currency( $subtotal ); ?></strong></div>
							</li>
							<?php
						endif;
						if ( $coupon_type != 'percentage' ) :
							$display = $cart['cart']['total']['discount'] > 0 ? '' : 'display:none;';
							?>
							<li style="<?php echo esc_attr( $display ); ?>" data-wpt-extra-field>
								<label><?php esc_html_e( 'Discount:', 'wp-travel' ); ?></label>
								<div class="price">
									<strong data-wpt-cart-discount="<?php echo esc_attr( $discount ); ?>"><?php echo '- ' . wptravel_get_formated_price_currency( $discount ); ?></strong>
								</div>
							</li>
							<?php
						endif;
						$display  = $cart['cart']['tax'] ? '' : 'display:none;';
						$tax_rate = $cart['cart']['tax'] ? $cart['cart']['tax'] : 0;
						$tax      = $cart['cart']['tax'] ? $cart['cart']['total']['tax'] : 0;
						?>
						<li style="<?php echo esc_attr( $display ); ?>" data-wpt-extra-field>
							<label>
								<?php
									$tax_label = apply_filters( 'wptravel_checkout_tax_label', esc_html__( ! empty($strings) ? $strings['bookings']['price_tax'] : 'Tax ', 'wp-travel' ) . ' ', $tax_rate, $cart );
									echo sprintf(
										'%s(%s%%)',
										$tax_label,
										$tax_rate
									);
								?>
															
							</label>
							<div class="price"><strong data-wpt-cart-tax="<?php echo esc_attr( $tax ); ?>"><?php echo '+ ' . wptravel_get_formated_price_currency( $tax ); ?></strong></div>
						</li>

						<li data-wpt-trip-total="<?php echo esc_attr( $total ); ?>" class="wp-travel-payable-amount selected-payable-amount" >
							<label>
								<?php
									$total_label = apply_filters( 'wptravel_checkout_total_label', esc_html__( 'Total:', 'wp-travel' ), $cart );
									echo esc_html( $total_label );
								?>
																	
							</label>
							<div class="price"><strong data-wpt-cart-net-total="<?php echo esc_attr( $total ); ?>"><?php echo wptravel_get_formated_price_currency( $total ); ?></strong></div>
						</li>
						<li data-wpt-trip-partial-gross-total="<?php echo esc_attr( $total_partial ); ?>" style="display:none" >
							<label>
								<?php
								$total_upfront_label = apply_filters( 'wptravel_checkout_total_upfront_label', esc_html__( 'Total upfront', 'wp-travel' ), $cart );
								echo esc_html( $total_upfront_label );
								if ( wptravel_get_settings()['partial_payment_amount'] == 'no' ){
									echo '(' . esc_html( $payout_percent ) . '%)';
								}
								
								?>
							</label>
							<div class="price"><strong data-wpt-cart-net-total-partial="<?php echo esc_attr( $total_partial ); ?>"><?php echo wptravel_get_formated_price_currency( $total_partial ); ?></strong></div>
						</li>
					</ul>
					<div class="cart-summary-bottom">
						<div class="flex-wrapper">
							<form id="wp-travel-coupon-form" action="" class="update-cart-form">
								<?php
									$coupon_args  = array(
										'post_type'   => 'wp-travel-coupons',
										'post_status' => 'published',
									);
									$coupon_query = new WP_Query( $coupon_args );
									$coupons      = false;
									while ( $coupon_query->have_posts() ) {
										$coupon_query->the_post();
										$coupon_data = get_post_status();
										if ( $coupon_data == 'publish' ) {
											$coupons = true;
											break;
										}
									}
									if ( $coupons == true ) {
										?>
									<div class="field-inline">
										<input type="text" <?php echo esc_attr( $readonly ); ?> value="<?php echo esc_attr( $coupon_code ); ?>" class="coupon-input-field" placeholder="<?php esc_attr_e( 'Enter promo code', 'wp-travel' ); ?>">
										<button type="submit" <?php echo esc_attr( $disabled ); ?> class="btn btn-primary" data-success-l10n="<?php esc_attr_e( 'Coupon Applied.', 'wp-travel' ); ?>">
											<?php $coupon_applied ? esc_html_e( 'Coupon Applied', 'wp-travel' ) : esc_html_e( 'Apply Coupon', 'wp-travel' ); ?>
										</button>
									</div>
								<?php } ?>
							</form>
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
$trips = $wt_cart->getItems();

if ( ! $trips ) {
	$wt_cart->cart_empty_message();
	return;
}

$settings = wptravel_get_settings();

$checkout_page_url = wptravel_get_checkout_url();
if ( isset( $settings['checkout_page_id'] ) ) {
	$checkout_page_id  = $settings['checkout_page_id'];
	$checkout_page_url = get_permalink( $checkout_page_id );
}


$pax_label = __( 'Pax', 'wp-travel' );
$max_attr  = '';

// For old form
$trip_id       = WP_Travel::verify_nonce( true ) && ( isset( $_GET['trip_id'] ) && '' !== $_GET['trip_id'] ) ? absint( $_GET['trip_id'] ) : '';
$trip_duration = WP_Travel::verify_nonce( true ) && ( isset( $_GET['trip_duration'] ) && '' !== $_GET['trip_duration'] ) ? absint( $_GET['trip_duration'] ) : 1;

$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
$settings        = wptravel_get_settings();
$currency_code   = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
$currency_symbol = wptravel_get_currency_symbol( $currency_code );
$per_person_text = wptravel_get_price_per_text( $trip_id );
?>
<div class="order-wrapper">
	<h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'wp-travel' ); ?></h3>
	<div id="order_review" class="wp-travel-checkout-review-order">
		<table class="shop_table wp-travel-checkout-review-order-table">
			<thead>
				<tr>
					<th class="product-name"><?php esc_html_e( 'Trip', 'wp-travel' ); ?></th>
					<th class="product-total text-right"><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
					<th style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="product-total text-right f-partial-payment"><?php esc_html_e( 'Partial', 'wp-travel' ); ?></th>
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
					$pricing_name       = wptravel_get_trip_pricing_name( $trip_id, $price_key );
					$enable_partial     = $trip['enable_partial'];
					$trip_price_partial = isset( $trip['trip_price_partial'] ) ? $trip['trip_price_partial'] : $trip_price;

					$pax_label = isset( $trip['pax_label'] ) ? $trip['pax_label'] : '';

					// $single_trip_total         = wptravel_get_formated_price( $trip_price * $pax );
					$single_trip_total = wptravel_get_formated_price( $trip_price ); // Applies to categorized pricing @since 3.0.0
					// $single_trip_total_partial = wptravel_get_formated_price( $trip_price_partial * $pax );
					$single_trip_total_partial = wptravel_get_formated_price( $trip_price_partial ); // Applies to categorized pricing @since 3.0.0

					$trip_extras = isset( $trip['trip_extras'] ) ? $trip['trip_extras'] : array();

					$price_per = 'trip-default';

					if ( ! empty( $price_key ) ) {
						$price_per = wptravel_get_pricing_variation_price_per( $trip_id, $price_key );
					}

					if ( 'trip-default' === $price_per ) {
						$price_per = get_post_meta( $trip_id, 'wp_travel_price_per', true );
					}

					if ( 'group' === $price_per ) {

						$single_trip_total         = wptravel_get_formated_price( $trip_price );
						$single_trip_total_partial = wptravel_get_formated_price( $trip_price_partial );

						$price_per_label = '( ' . $pax . __( $strings['bookings']['pax'], 'wp-travel' ) . ' )';

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
								$label = wptravel_get_pricing_category_by_key( $category_type );
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
										<?php echo sprintf( '%2$s x %1$s', wptravel_get_formated_price_currency( $category['price'] ), esc_html( $pax ) ); ?>
									</td>
									<td class="right">
										<?php echo wptravel_get_formated_price_currency( $price ); ?>
									</td>
									<td style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="right product-total text-right f-partial-payment"><?php echo wptravel_get_formated_price_currency( $price_partial ); ?></td>
								</tr>
								<?php
							} else {
								?>
								<tr class="person-count">
									<td class="left">
										<span style="display:table-row"><?php echo sprintf( esc_html__( 'Group (%s)', 'wp-travel' ), esc_html( $pax ) ); ?></span>
										<?php echo sprintf( '%2$s x %1$s', wptravel_get_formated_price_currency( $category['price'] ), '1', esc_html( $label ) ); ?>
									</td>
									<td class="right">
										<?php echo wptravel_get_formated_price_currency( $price ); ?>
									</td>
									<td style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="right product-total text-right f-partial-payment"><?php echo wptravel_get_formated_price_currency( $price_partial ); ?></td>
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
							<strong>- <?php echo wptravel_get_formated_price_currency( $cart_amounts['discount'] ); ?></strong>
						</td>
						<td style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">

							<?php if ( 0 === $cart_amounts['discount_partial'] ) : ?>

								<p><strong><span class="wp-travel-tax ws-theme-currencySymbol">--</strong></p>

							<?php else : ?>

								<strong><?php echo wptravel_get_formated_price_currency( $cart_amounts['discount_partial'] ); ?></strong>

							<?php endif; ?>

						</td>
					</tr>

				<?php endif; ?>
				<?php if ( $tax_rate = WP_Travel_Helpers_Trips::get_tax_rate() ) : ?>
					<tr>
						<th>
							<p><strong><?php esc_html_e( 'Subtotal', 'wp-travel' ); ?></strong></p>
							<p><strong>
							<?php esc_html_e( $strings['bookings']['price_tax'] ? $strings['bookings']['price_tax'] : 'Tax', 'wp-travel' );esc_html_e( ' : ', 'wp-travel' );
							?>
							<span class="tax-percent">
								<?php
								echo esc_html( $tax_rate );
								esc_html_e( '%', 'wp-travel' );
								?>
							</span></strong></p>
						</th>
						<td  class="text-right">
							<p><strong><span class="wp-travel-sub-total"><?php echo wptravel_get_formated_price_currency( $cart_amounts['sub_total'] ); ?></span></strong></p>
							<p><strong><span class="wp-travel-tax"><?php echo wptravel_get_formated_price_currency( $cart_amounts['tax'] ); ?></span></strong></p>
						</td>
						<td style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">
							<p><strong><span class="wp-travel-sub-total"><?php echo wptravel_get_formated_price_currency( $cart_amounts['sub_total_partial'] ); ?></span></strong></p>

							<?php if ( 0 === $cart_amounts['tax_partial'] ) : ?>

								<p><strong><span class="wp-travel-tax ">--</strong></p>

							<?php else : ?>

								<p><strong><span class="wp-travel-tax "><?php echo wptravel_get_formated_price_currency( $cart_amounts['tax_partial'] ); ?></span></strong></p>

							<?php endif; ?>
						</td>
					</tr>
				<?php endif; ?>
				<tr class="order-total ">
				<th><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
				<td class="text-right"><strong><span class="wp-travel-total-price-amount amount"><?php echo wptravel_get_formated_price_currency( $cart_amounts['total'] ); ?></span></strong> </td>
				<td style="display:<?php echo wptravel_is_partial_payment_enabled() ? 'table-cell' : 'none'; ?>;" class="text-right f-partial-payment">
					<strong><span class="wp-travel-total-price-amount amount"><?php echo wptravel_get_formated_price_currency( $cart_amounts['total_partial'] ); ?></span></strong> </td>
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
