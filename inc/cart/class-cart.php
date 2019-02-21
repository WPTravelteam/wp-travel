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
	 * Cart id/ name.
	 *
	 * @var string
	 */
	private $cart_id;

	/**
	 * Limit of item in cart.
	 *
	 * @var integer
	 */
	private $item_limit = 0;

	/**
	 * Limit of quantity per item.
	 *
	 * @var integer
	 */
	private $quantity_limit = 99;

	/**
	 * Cart items.
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Cart Discounts.
	 *
	 * @var array
	 */
	private $discounts = array();

	/**
	 * Cart item attributes.
	 *
	 * @var array
	 */
	private $attributes = array();

	/**
	 * Cart errors.
	 *
	 * @var array
	 */
	private $errors = array();

	/**
	 * Initialize shopping cart.
	 *
	 * @return void
	 */
	function __construct() {
		$this->cart_id = 'wp_travel_cart';

		// Read cart data on load.
		add_action( 'plugins_loaded', array( $this, 'read_cart_onload' ), 1 );
	}

	/**
	 * Output of cart shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		wp_travel_get_template_part( 'content', 'cart' );
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

	// @since 1.3.2
	/**
	 * Add an item to cart.
	 *
	 * @param int   $id    An unique ID for the item.
	 * @param int   $price Price of item.
	 * @param int   $qty   Quantity of item.
	 * @param array $attrs Item attributes.
	 * @return boolean
	 */
	public function add( $trip_id, $trip_price = 0, $pax, $price_key = '', $attrs = array() ) {

		$arrival_date = isset( $attrs['arrival_date'] ) ? $attrs['arrival_date'] : '';

		$cart_item_id = $this->wp_travel_get_cart_item_id( $trip_id, $price_key, $arrival_date );

		if ( class_exists( 'WP_Travel_Util_Inventory' ) ) {

			$inventory = new WP_Travel_Util_Inventory();

			$inventory_enabled = $inventory->is_inventory_enabled( $trip_id );
			$available_pax     = $inventory->get_available_pax( $trip_id, $price_key, $arrival_date );

			/**
			 * Customization Starts.
			 */
			$available_pax = apply_filters( 'wp_travel_available_pax', $available_pax, $trip_id, $price_key );
			/**
			 * Customization Ends.
			 */

			if ( $inventory_enabled && $available_pax ) {

				if ( $pax > $available_pax ) {

					WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . sprintf( __( 'Requested pax size of %1$s exceeds the available pax limit ( %2$s ) for this trip. Available pax is set for booking.', 'wp-travel' ), $pax, $available_pax ), 'error' );

					$pax = $available_pax;

					$this->quantity_limit = $pax;

				}
			}
		}

		// Add product id.
		$this->items[ $cart_item_id ]['trip_id']    = $trip_id;
		$this->items[ $cart_item_id ]['trip_price'] = $trip_price;
		$this->items[ $cart_item_id ]['pax']        = $pax;
		$this->items[ $cart_item_id ]['price_key']  = $price_key;
		// error_log( print_r( $attrs, true ) );
		// error_log( print_r( $this->items, true ) );
		// For additional cart item attrs.
		if ( is_array( $attrs ) && count( $attrs ) > 0 ) {
			foreach ( $attrs as $key => $attr ) {
				$this->items[ $cart_item_id ][ $key ] = $attr;
			}
		}
		
		// error_log( print_r( $this->items, true ) );
		$this->write();
		return true;
	}

	/**
	 * Write changes to cart session.
	 */
	private function write() {
		$cart_attributes_session_name = $this->cart_id . '_attributes';
		$items                        = array();

		foreach ( $this->items as $id => $item ) {
			if ( ! $id ) {
				continue;
			}
			$items[ $id ] = $item;
		}

		$cart['cart_items'] = $items;
		$cart['discounts']  = $this->discounts;

		$cart_items = WP_Travel()->session->set( $this->cart_id, $cart );
		// Cookie data to enable data info in js.
		ob_start();
		setcookie( 'wp_travel_cart', wp_json_encode( $cart ), time() + 604800, '/' );
		ob_end_flush();
	}
	/**
	 * Read items from cart session.
	 */
	private function read() {
		$cart            = WP_Travel()->session->get( $this->cart_id );
		$cart_items      = $cart['cart_items'];
		$this->discounts = isset( $cart['discounts'] ) ? $cart['discounts'] : array();

		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $id => $item ) {
				if ( empty( $item ) ) {
					continue;
				}
				$this->items[ $id ] = $item;
			}
		}
	}

	/**
	 * Update item quantity.
	 *
	 * @param  int   $cart_item_id  ID of targed item.
	 * @param  int   $qty          Quantity.
	 * @param  array $attr         Attributes of item.
	 * @return boolean
	 */
	public function update( $cart_item_id, $pax, $trip_extras = false, $attr = array() ) {

		if ( $pax < 1 ) {
			return $this->remove( $cart_item_id );
		}

		// Update quantity.
		if ( isset( $this->items[ $cart_item_id ] ) ) {

			/**
			 * Customization Starts.
			 */
			$max_available = $this->items[ $cart_item_id ]['max_available'];
			$trip_id       = $this->items[ $cart_item_id ]['trip_id'];
			$price_key     = $this->items[ $cart_item_id ]['price_key'];

			$max_available = apply_filters( 'wp_travel_available_pax', $max_available, $trip_id, $price_key );
			/**
			 * Customization Ends.
			 */

			$this->items[ $cart_item_id ]['pax'] = ( $max_available && $pax > $max_available ) ? $max_available : $pax;

			if ( $trip_extras ) {

				if ( is_array( $trip_extras ) && ! empty( $trip_extras ) ) {

					$this->items[ $cart_item_id ]['trip_extras'] = $trip_extras;

				}
			}

			if ( $max_available && $pax > $max_available ) {

				WP_Travel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . sprintf( __( 'Requested pax size of %1$s exceeds the available pax limit ( %2$s ) for this trip. Available pax is set for booking.', 'wp-travel' ), $pax, $max_available ), 'error' );

			}

			$this->write();
			return true;
		}
		return false;
	}

	/**
	 * Add Discount Values
	 */
	public function add_discount_values( $coupon_id, $discount_type, $discount_value ) {

		$this->discounts['type']      = $discount_type;
		$this->discounts['value']     = $discount_value;
		$this->discounts['coupon_id'] = $coupon_id;

		$this->write();

		return true;

	}
	/**
	 * Get discounts
	 */
	public function get_discounts() {

		return $this->discounts;
	}

	/**
	 * Get list of items in cart.
	 *
	 * @return array An array of items in the cart.
	 */
	public function getItems() {
		return $this->items;
	}

	public function cart_empty_message() {
		$url = get_post_type_archive_link( WP_TRAVEL_POST_TYPE );
		echo ( __( sprintf( 'Your cart is empty please <a href="%s"> click here </a> to add trips.', $url ), 'wp-travel' ) );
	}
	/**
	 * Clear all items in the cart.
	 */
	public function clear() {
		$this->items      = array();
		$this->attributes = array();
		$this->discounts  = array();
		$this->write();
	}

	/**
	 * Read cart items on load.
	 *
	 * @return void
	 */
	function read_cart_onload() {
		$this->read();
	}

	/**
	 * Remove item from cart.
	 *
	 * @param integer $id ID of targeted item.
	 */
	public function remove( $id ) {
		unset( $this->items[ $id ] );
		unset( $this->attributes[ $id ] );
		$this->write();
	}
	// /**
	// * Remove cart trip extras.
	// */
	// public function remove_trip_extras
	function get_total() {

		$trips = $this->items;

		$discounts = $this->discounts;

		$sub_total       = 0;
		$tax_amount      = 0;
		$discount_amount = 0;

		$sub_total_partial       = 0;
		$tax_amount_partial      = 0;
		$discount_amount_partial = 0;

		// Total amount without tax.
		if ( is_array( $trips ) && count( $trips ) > 0 ) {
			foreach ( $trips as $cart_id => $trip ) :

				$trip_price         = $trip['trip_price'];
				$trip_price_partial = $trip['trip_price_partial'];
				$pax                = ! empty( $trip['pax'] ) ? $trip['pax'] : 1;

				$single_trip_total         = wp_travel_get_formated_price( $trip_price * $pax );
				$single_trip_total_partial = wp_travel_get_formated_price( $trip_price_partial * $pax );

				$price_per = 'trip-default';

				if ( isset( $trip['price_key'] ) && ! empty( $trip['price_key'] ) ) {
					$price_per = wp_travel_get_pricing_variation_price_per( $trip['trip_id'], $trip['price_key'] );
				}

				if ( 'trip-default' === $price_per ) {
					$price_per = get_post_meta( $trip['trip_id'], 'wp_travel_price_per', true );
				}

				if ( 'group' === $price_per ) {

					$single_trip_total         = wp_travel_get_formated_price( $trip_price );
					$single_trip_total_partial = wp_travel_get_formated_price( $trip_price_partial );

				}
				$sub_total         += $single_trip_total;
				$sub_total_partial += $single_trip_total_partial;

				$trip_extras_total = 0;

				if ( isset( $trip['trip_extras'] ) && ! empty( $trip['trip_extras'] ) && isset( $trip['trip_extras']['id'] ) && is_array( $trip['trip_extras']['id'] ) ) {

					foreach ( $trip['trip_extras']['id'] as $k => $e_id ) {

						$trip_extras_data = get_post_meta( $e_id, 'wp_travel_tour_extras_metas', true );

						$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : 0;
						$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;
						$unit       = isset( $trip_extras_data['extras_item_unit'] ) && ! empty( $trip_extras_data['extras_item_unit'] ) ? $trip_extras_data['extras_item_unit'] : 0;

						if ( $sale_price ) {
							$price = $sale_price;
						}

						$qty                = isset( $trip['trip_extras']['qty'][ $k ] ) && ! empty( $trip['trip_extras']['qty'][ $k ] ) ? $trip['trip_extras']['qty'][ $k ] : 1;
						$trip_extras_total += wp_travel_get_formated_price( $price * $qty );
					}
				}

				$sub_total         += $trip_extras_total;
				$sub_total_partial += $trip_extras_total;
			endforeach;
		}

		$sub_total = apply_filters( 'wp_travel_cart_sub_total', wp_travel_get_formated_price( $sub_total ) );

		// Discounts Calculation.
		if ( ! empty( $discounts ) ) {

			$d_typ = $discounts['type'];
			$d_val = $discounts['value'];

			if ( 'fixed' === $d_typ ) {
				$discount_amount = wp_travel_get_formated_price( $d_val );
				$discount_amount_partial = wp_travel_get_formated_price( $d_val );
			} elseif ( 'percentage' === $d_typ ) {
				$discount_amount = wp_travel_get_formated_price( ( $sub_total * $d_val ) / 100 );
				$discount_amount_partial = wp_travel_get_formated_price( ( $sub_total_partial * $d_val ) / 100 );
			}
		}

		// Totals after discount.
		$total_trip_price_after_dis         = $sub_total - $discount_amount;
		$total_trip_price_partial_after_dis = $sub_total_partial - $discount_amount_partial;

		// Adding tax to sub total;
		if ( $tax_rate = wp_travel_is_taxable() ) :
			$tax_amount         = wp_travel_get_formated_price( ( $total_trip_price_after_dis * $tax_rate ) / 100 );
			$tax_amount_partial = wp_travel_get_formated_price( ( $total_trip_price_partial_after_dis * $tax_rate ) / 100 );
		endif;

		// Totals after tax.
		$total_trip_price         = $total_trip_price_after_dis + $tax_amount;
		$total_trip_price_partial = $total_trip_price_partial_after_dis + $tax_amount_partial;

		$get_total = array(
			'sub_total'         => $total_trip_price_after_dis,
			'tax'               => $tax_amount,
			'discount'          => $discount_amount,
			'total'             => $total_trip_price,

			// Total payble amount // Same as above price if partial payment not enabled.
			'sub_total_partial' => $total_trip_price_partial_after_dis,
			'tax_partial'       => $tax_amount_partial,
			'discount_partial'  => $discount_amount_partial,
			'total_partial'     => $total_trip_price_partial,
		);

		$get_total = apply_filters( 'wp_travel_cart_get_total_fields', $get_total );
		return $get_total;
	}
	/**
	 * Return cart item id as per $trip_id and $price_key.
	 *
	 * @param   $trip_id    Number  Trip / Post id of the trip
	 * @param   $price_key  String  Pricing Key.
	 *
	 * @return  String  cart item id.
	 *
	 * @since   1.5.8
	 */
	public function wp_travel_get_cart_item_id( $trip_id, $price_key = '', $start_date = '' ) {

		$cart_item_id = ( isset( $price_key ) && '' !== $price_key ) ? $trip_id . '_' . $price_key : $trip_id;
		$cart_item_id = ( isset( $start_date ) && '' !== $start_date ) ? $cart_item_id . '_' . $start_date : $cart_item_id;
		return apply_filters( 'wp_travel_filter_cart_item_id', $cart_item_id, $trip_id, $price_key );
	}
}

new WP_Travel_Cart();

// Set cart global variable.
$GLOBALS['wt_cart'] = new WP_Travel_Cart();
