<?php
/**
 * WP Travel Cart.
 *
 * @package WP Travel
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WP Travel Cart Shortcode Class.
 */
class WP_Travel_Cart {

	/**
	 * Constructor.
	 */
	function __construct() {

	}

	/**
	 * Output of cart shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {

		$trip_id = ( isset( $_GET['trip_id'] ) && '' !== $_GET['trip_id'] ) ? $_GET['trip_id'] : '';
		$trip_duration = ( isset( $_GET['trip_duration'] ) && '' !== $_GET['trip_duration'] ) ? $_GET['trip_duration'] : 1;

		if ( '' === $trip_id ) {
			esc_html_e( 'Your cart is empty', 'wp-travel' );
			return;
		}
		$settings = wp_travel_get_settings();
		$trip_tax_details = wp_travel_process_trip_price_tax( $trip_id );
		if ( isset( $trip_tax_details['tax_type'] ) && 'inclusive' === $trip_tax_details['tax_type'] ) {

			$trip_price = $trip_tax_details['actual_trip_price'];
		} else {
			$trip_price = $trip_tax_details['trip_price'];
		}
		$checkout_page_id = '';
		$checkout_page_url = get_home_url();

		$pax_label = __( 'Pax', 'wp-travel' );
		$max_attr = '';

		if ( isset( $settings['checkout_page_id'] ) ) {
			$checkout_page_id = $settings['checkout_page_id'];
			$checkout_page_url = get_permalink( $checkout_page_id );
		}

		$enable_pricing_options = get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true );

		$pricing_key = ( isset( $_GET['price_key'] ) && '' !== $_GET['price_key'] ) ? $_GET['price_key'] : '';

		$pax_val = ( isset( $_GET['pax'] ) && '' !== $_GET['pax'] ) ? $_GET['pax'] : '1';

		$valid_pricing_key  = false;

		if ( isset( $_GET['price_key'] ) && '' !== $_GET['price_key'] ) {

			$valid_pricing_key = self::is_pricing_key_valid( $trip_id, $pricing_key );

			if ( ! $valid_pricing_key ) {

				esc_html_e( 'Invalid Pricing key', 'wp-travel' );

				return;

			}
		}
		// Validate Request date.
		if ( isset( $_GET['trip_date'] ) && '' !== $_GET['trip_date']  ) {

			$valid_pricing_date = self::is_request_date_valid( $trip_id, $pricing_key, $_GET['trip_date'] );

			if ( ! $valid_pricing_date ) {

				esc_html_e( 'Invalid pricing option date', 'wp-travel' );

				return;

			}
		}

		if ( $valid_pricing_key && '' !== $pricing_key && 'yes' === $enable_pricing_options ) {

			$pricing_data = wp_travel_get_pricing_variation( $trip_id, $pricing_key );

			if ( is_array( $pricing_data ) && '' !== $pricing_data ) {

				foreach ( $pricing_data as $p_ky => $pricing ) :

					$trip_price  = $pricing['price'];
					$enable_sale = isset( $pricing['enable_sale'] ) && 'yes' === $pricing['enable_sale'] ? true : false;

					$taxable_price = $pricing['price'];

						if ( $enable_sale && isset( $pricing['sale_price'] ) && '' !== $pricing['sale_price'] ) {
							$sale_price    = $pricing['sale_price'];
							$taxable_price = $sale_price;
						}

						$trip_tax_details = wp_travel_process_trip_price_tax_by_price( $trip_id, $taxable_price );
						if ( isset( $trip_tax_details['tax_type'] ) && 'inclusive' === $trip_tax_details['tax_type'] ) {
							$trip_price = $trip_tax_details['actual_trip_price'];
						} else {
								$trip_price = $trip_tax_details['trip_price'];
							}
							// Product Metas.
							$trip_start_date = isset( $_GET['trip_date'] ) && '' !== $_GET['trip_date'] ? $_GET['trip_date'] : '';
							$pax_label       = isset( $pricing['type'] ) && 'custom' === $pricing['type'] && '' !== $pricing['custom_label'] ? $pricing['custom_label'] : $pricing['type'];
							$max_available   = isset( $pricing['max_pax'] ) && '' !== $pricing['max_pax'] ? true : false;

							if ( $max_available ) {
								$max_attr = 'max=' . $pricing['max_pax'];
							}
				endforeach;
			}
		}
		else {
			// Product Metas.
			$enable_sale     = get_post_meta( $trip_id, 'wp_travel_enable_sale', true );
			$sale_price      = wp_travel_get_trip_sale_price( $trip_id );
			$trip_start_date = get_post_meta( $trip_id, 'wp_travel_start_date', true );
			$max_available   = get_post_meta( $trip_id, 'wp_travel_group_size', true );
			if ( $max_available ) {
				$max_attr = 'max=' . $max_available;
			}
		}

		$fixed_departure = get_post_meta( $trip_id, 'wp_travel_fixed_departure', true );
		$settings        = wp_travel_get_settings();
		$currency_code 	 = ( isset( $settings['currency'] ) ) ? $settings['currency'] : '';
		$currency_symbol = wp_travel_get_currency_symbol( $currency_code );
		$per_person_text = wp_travel_get_price_per_text( $trip_id );

		?>
		<!-- CART HTML START -->
		<div class="ws-theme-cart-page">
		<form action="<?php echo esc_url( $checkout_page_url ); ?>" method="get">
			<input type="hidden" name="trip_id" value="<?php echo esc_attr( $trip_id ); ?>" >
			<input type="hidden" name="trip_duration" value="<?php echo esc_attr( $trip_duration ); ?>" >
			<table class="ws-theme-cart-list">
				<thead>
					<tr>
						<th></th>
						<th colspan="2"><?php esc_html_e( 'Tour', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'Price', 'wp-travel' ); ?></th>
						<th><?php esc_html_e( 'PAX', 'wp-travel' ); ?></th>
						<th class="text-right"><?php esc_html_e( 'Total', 'wp-travel' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<tr class="responsive-cart">
						<!-- <td class="product-remove" >
							<a href="#0" class="remove tooltip-area" title="Remove this Tour">×</a> 
						</td> -->
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
						<?php if ( '' !== $trip_price || '0' !== $trip_price ) : ?>
								<span class="person-count">
									<ins>
										<span class="wp-travel-trip-price" payment_price="<?php echo esc_attr( @$trip_tax_details['actual_trip_price'] ); ?>" trip_price="<?php echo esc_attr( $trip_price ); ?>" >
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
							<?php endif; ?>
						</td>
						<td class="product-quantity" data-title="PAX">
							<div class="st_adults">
								<span class="label"><?php echo esc_html( ucfirst( $pax_label ) ); ?></span>
								<input type="number" class="input-text wp-travel-pax text" step="1" min="1" <?php echo $max_attr; ?> name="pax" value="<?php echo esc_attr( $pax_val ); ?>" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric">
							</div>
						</td>
						<td class="product-subtotal text-right" data-title="Total">
							<div class="item_cart">
								<p>
									<strong><span class="woocommerce-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol(); ?></span><span class="wp-travel-trip-total"> 0 </span></strong>
								</p>
							</div>
						</td> 
					</tr>
					</tbody>
					</table>
					<table class="ws-theme-cart-list table-total-info">
					<?php if ( wp_travel_is_trip_price_tax_enabled() && isset( $trip_tax_details['tax_percentage'] ) && '' !== $trip_tax_details['tax_percentage'] && 'inclusive' !== $trip_tax_details['tax_type'] ) {
						?>
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
					<?php } ?>
						<tr>
							<th>
								<strong><?php echo esc_html( 'Total', 'wp-travel' ); ?></strong>
							</th>
							<td  class="text-right">
								<p class="total">
									<strong><?php echo wp_travel_get_currency_symbol(); ?><span class="wp-travel-total ws-theme-currencySymbol">0</span></strong>
								</p>
							</td>
						</tr>
					</table>
				<div>
					<div class="actions">
					<input type="submit" class="btn_full book-now-btn" value="<?php esc_html_e( 'Proceed to checkout', 'wp-travel' ) ?>">
					</div>
				</div>
			</form>
		</div>
		<!-- CART HTML END -->
		<script>
			function calculate_price() {
				var sub_total_including_tax = 0;
				var sub_total = 0;
				var price_per = wt_payment.price_per;

				jQuery( '.ws-theme-cart-list tbody tr.responsive-cart' ).each( function(){
					no_of_pax = jQuery(this).find( '.wp-travel-pax' ).val();

					var payment_price = jQuery(this).find('.wp-travel-trip-price').attr('payment_price'); // price including tax
					var trip_price = jQuery(this).find('.wp-travel-trip-price').attr('trip_price'); // price excluding tax

					if (price_per.toLowerCase().slice(0, 6) === 'person') {                        
						payment_price = parseFloat(payment_price) * parseFloat(no_of_pax);
						trip_price = parseFloat(trip_price) * parseFloat(no_of_pax);
						trip_price = trip_price.toFixed(2);
					}
					// Individual Price total.
					jQuery(this).find('.wp-travel-trip-total').html( trip_price );

					sub_total += parseFloat( trip_price );
					total += sub_total;

					var tax_fields = jQuery('.wp-travel-sub-total').length;
					if ( tax_fields > 0 ) {
						sub_total_including_tax += parseFloat( payment_price );                        
					}
				   
				} );

				// Sub Total
				if (sub_total.toFixed)
					sub_total = parseFloat( sub_total ).toFixed(2);

				var total = sub_total;

				if ( sub_total_including_tax.toFixed ) {
					sub_total_including_tax = parseFloat( sub_total_including_tax ).toFixed(2);
					if ( sub_total_including_tax > total ) {
						tax = parseFloat( sub_total_including_tax - total ).toFixed(2);
						total = sub_total_including_tax;
						jQuery('.wp-travel-tax').html( tax );
					}
				}          

				jQuery('.wp-travel-sub-total').html( sub_total );                    
				jQuery('.wp-travel-total').html( total );
			}	

			jQuery(document).ready( function($){
				calculate_price();
				$(document).on( 'change', '.wp-travel-pax', function() {
					calculate_price();
				} );
			});
		</script>
	<?php
	}

	/**
	 * Validate pricing Key
	 *
	 * @return bool true | false.
	 */
	public static function is_pricing_key_valid( $trip_id, $pricing_key ) {

		if ( '' === $trip_id || '' === $pricing_key ) {

			return false;
		}

		//Get Pricing variations.
		$pricing_variations = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

			$result = array_filter($pricing_variations, function( $single ) use ( $pricing_key ) {
				return in_array( $pricing_key, $single, true );
			});
			return ( '' !== $result && count( $result ) > 0 ) ? true : false;
		}
		return false;

	}

	/**
	 * Validate date
	 *
	 * @return bool true | false.
	 */
	public static function is_request_date_valid( $trip_id, $pricing_key, $test_date ) {

		if ( '' === $trip_id || '' === $pricing_key || '' === $test_date ) {

			return false;
		}

		$trip_multiple_date_options = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = wp_travel_get_pricing_variation_start_dates( $trip_id, $pricing_key );

		if ( 'yes' === $trip_multiple_date_options && is_array( $available_dates ) && ! empty( $available_dates ) ) {

			return in_array( $test_date, $available_dates, true );
		}
		else {

			$date_now  = ( new DateTime() )->format( 'Y-m-d' );
			$test_date = ( new DateTime( $test_date ) )->format( 'Y-m-d' );

			if ( strtotime( $date_now ) <= strtotime( $test_date ) ) {

				return true;
			}

			return false;

		}
	}

}

new WP_Travel_Cart();
