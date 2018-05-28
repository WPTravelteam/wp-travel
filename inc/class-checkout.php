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

/**
 * WP Travel Checkout Shortcode Class.
 */
class WP_Travel_Checkout {

	/**
	 * Constructor.
	 */
	function __construct() {
	}

	/**
	 * Output of checkout shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		if ( ! isset( $_REQUEST['trip_id'] ) ) {
			esc_html_e( 'Your Cart is empty', 'wp-travel' );
			return;
		}

		$trip_id = $_REQUEST['trip_id'];

		if ( $trip_id > 0 ) {
			$max_pax = get_post_meta( $trip_id, 'wp_travel_group_size', true );
		}
		$pax_size = 1;
		if ( isset( $_REQUEST['pax'] ) && ( $max_pax && $_REQUEST['pax'] <= $max_pax ) ){
			$pax_size = $_REQUEST['pax'];
		}
		$price_per = wp_travel_get_price_per_text( $trip_id );
		$settings = wp_travel_get_settings();

		$require_login_to_checkout = isset( $settings['enable_checkout_customer_registration'] ) ? $settings['enable_checkout_customer_registration'] : 'no';

		if ( 'yes' === $settings['enable_checkout_customer_registration'] && ! is_user_logged_in() ) {

			return wp_travel_get_template_part( 'account/form', 'login' );

		}

		//Pricing Options Merge.
		$enable_pricing_options = get_post_meta( $trip_id, 'wp_travel_enable_pricing_options', true );

		$pricing_key = ( isset( $_GET['price_key'] ) && '' !== $_GET['price_key'] ) ? $_GET['price_key'] : '';

		$valid_pricing_key  = false;

		if ( isset( $_GET['price_key'] ) && '' !== $_GET['price_key'] ) {

			$valid_pricing_key = self::is_pricing_key_valid( $trip_id, $pricing_key );

			if ( ! $valid_pricing_key ) {

				esc_html_e( 'Invalid Pricing key', 'wp-travel' );

				return;

			}
		}
		$trip_requested_date = isset( $_GET['trip_date'] ) && '' !== $_GET['trip_date'] ? $_GET['trip_date'] : '';
		// Validate Request date.
		if ( isset( $_GET['trip_date'] ) && '' !== $_GET['trip_date']  ) {

			$valid_pricing_date = self::is_request_date_valid( $trip_id, $pricing_key, urldecode( $_GET['trip_date'] ) );

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

							$trip_price_original = $trip_price;
								if ( strtolower( $price_per ) === 'person' ) {
								$trip_price *= $pax_size;
							}
				endforeach;
			}
		}
		else {
			$trip_tax_details = wp_travel_process_trip_price_tax( $trip_id );
			if ( isset( $trip_tax_details['tax_type'] ) && 'inclusive' === $trip_tax_details['tax_type'] ) {
				$trip_price = $trip_tax_details['actual_trip_price'];
			} else {
				$trip_price = $trip_tax_details['trip_price'];
			}
			$trip_price_original = $trip_price;
			if ( strtolower( $price_per ) === 'person' ) {
				$trip_price *= $pax_size;
			}
		}

		?>
		<div class="wp-travel-billing">            
			<div class="wp-travel-tab-wrapper">
				<div class="col-md-7 clearfix columns" >
					<h3><?php esc_html_e( 'Billing info', 'wp-travel' ) ?></h3>
					<?php wp_travel_get_booking_form() ?>
				</div>

				<div class="col-md-5 columns">
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
								<tr class="cart_item">
								<td class="product-name">
									<?php echo esc_html( get_the_title( $trip_id ) ); ?> &nbsp; <strong class="product-quantity">× <span class="wp-travel-cart-pax"><?php echo esc_html( $pax_size ); ?></span><?php printf( ' pax' ); ?> </strong> 
								</td>
								<td class="product-total">
								<span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="product-total-price amount" payment_price="<?php echo @$trip_tax_details['actual_trip_price'] ?>" trip_price="<?php echo $trip_price_original; ?>" >0</span>
								</td>
								</tr>
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
								<td><strong><span class="wp-travel-Price-currencySymbol"><?php echo wp_travel_get_currency_symbol() ?></span><span class="wp-travel-total-price-amount amount">0</span></strong> </td>
								</tr>
							</tfoot>
						</table>
					</div>
					</div>
				</div>
			</div>        
		</div>
		<style>
		.wp-travel-tab-wrapper .wp-travel-booking-form-wrapper form{width:100%; padding-left:0; padding-right:0}
		</style>
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

new WP_Travel_Checkout();
