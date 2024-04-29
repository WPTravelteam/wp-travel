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

add_action( 'wp_travel_cart_empty_message', 'wp_travel_cart_set_empty_message' );

function wp_travel_cart_set_empty_message(){
	global $wt_cart;
	$wt_cart->cart_empty_message();
}

/**
 * WP Travel Checkout Shortcode Class.
 */
class WP_Travel_Checkout { // @phpcs:ignore

	/**
	 * Output of checkout shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		global $wt_cart;
		$trips = $wt_cart->getItems();

		if ( ! $trips ) {
			do_action( 'wp_travel_cart_empty_message' );
			return;
		}
		// Check if login is required for checkout.
		$settings = wptravel_get_settings();

		$require_login_to_checkout = isset( $settings['enable_checkout_customer_registration'] ) ? $settings['enable_checkout_customer_registration'] : 'no';

		if ( 'yes' === $require_login_to_checkout && ! is_user_logged_in() ) {
			return wptravel_get_template_part( 'account/form', 'login' );
		}
		// @since 4.0.7
		do_action( 'wp_travel_before_checkout_page_wrap' ); // @phpcs:ignore
		$hide_mini_cart = apply_filters( 'wp_travel_hide_mini_cart_on_checkout', false ); // @phpcs:ignore
		?>
		<div class="checkout-page-wrap <?php echo $hide_mini_cart ? 'wti_no_mini_cart' : ''; ?>">
			<div class="wp-travel-checkout-section">
				<div class="checkout-block checkout-left">
					<?php include sprintf( '%s/inc/cart/checkout.php', WP_TRAVEL_ABSPATH ); ?>
				</div>
			</div>
			<?php if ( ! $hide_mini_cart ) : ?>
			<div class="wp-travel-minicart">
				<div class="sticky-sidebar">
					<div class="checkout-block checkout-right">
						<?php include sprintf( '%s/inc/cart/cart-mini.php', WP_TRAVEL_ABSPATH ); ?>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Validate pricing Key
	 *
	 * @param Number $trip_id Trip Id of the trip.
	 * @param String $pricing_key Pricing Key of the trip.
	 * @return bool true | false.
	 */
	public static function is_pricing_key_valid( $trip_id, $pricing_key ) {

		if ( '' === $trip_id || '' === $pricing_key ) {

			return false;
		}

		// Get Pricing variations.
		$pricing_variations = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( is_array( $pricing_variations ) && '' !== $pricing_variations ) {

			$result = array_filter(
				$pricing_variations,
				function( $single ) use ( $pricing_key ) {
					return in_array( $pricing_key, $single, true );
				}
			);
			return ( '' !== $result && count( $result ) > 0 ) ? true : false;
		}
		return false;

	}

	/**
	 * Validate date
	 *
	 * @param Number $trip_id Trip Id of the trip.
	 * @param String $pricing_key Pricing Key of the trip.
	 * @param String $test_date Trip date.
	 * @return bool true | false.
	 */
	public static function is_request_date_valid( $trip_id, $pricing_key, $test_date ) {

		if ( '' === $trip_id || '' === $pricing_key || '' === $test_date ) {

			return false;
		}

		$trip_multiple_date_options = get_post_meta( $trip_id, 'wp_travel_enable_multiple_fixed_departue', true );

		$available_dates = wptravel_get_pricing_variation_start_dates( $trip_id, $pricing_key );

		if ( 'yes' === $trip_multiple_date_options && is_array( $available_dates ) && ! empty( $available_dates ) ) {

			return in_array( $test_date, $available_dates, true );
		} else {

			$date_now  = new DateTime();
			$test_date = new DateTime( $test_date );

			// Check Expiry Date.
			$date_now  = $date_now->format( 'Y-m-d' );
			$test_date = $test_date->format( 'Y-m-d' );

			if ( strtotime( $date_now ) <= strtotime( $test_date ) ) {

				return true;
			}

			return false;

		}
	}
}

new WP_Travel_Checkout();
