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
		wptravel_get_template_part( 'content', 'cart' );
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

	// @since 1.3.2
	/**
	 * Add an item to cart.
	 *
	 * @param int   $args    Mixed. trip id if WP Travel below 4.4.2 else all cart args.
	 * @param int   $trip_price Price of item.
	 * @param int   $trip_price_partial   Price partial.
	 * @param int   $pax   Quantity of item.
	 * @param array $attrs Item attributes.
	 * @todo Need to remove all attributes excepct $args.
	 * @return boolean
	 */
	public function add( $args, $trip_price = 0, $trip_price_partial = 0, $pax = 1, $price_key = '', $attrs = array() ) {
		if ( is_array( $args ) ) { // add to cart args. $args since WP Travel 4.4.2
			$trip_id            = isset( $args['trip_id'] ) ? $args['trip_id'] : 0;
			$trip_price         = isset( $args['trip_price'] ) ? $args['trip_price'] : 0;
			$trip_price_partial = isset( $args['trip_price_partial'] ) ? $args['trip_price_partial'] : 0;
			$pax                = isset( $args['pax'] ) ? $args['pax'] : array();
			$price_key          = isset( $args['price_key'] ) ? $args['price_key'] : '';
			$attrs              = isset( $args['attrs'] ) ? $args['attrs'] : array();
		} else {
			$trip_id = $args;
		}

		$arrival_date = isset( $attrs['arrival_date'] ) ? $attrs['arrival_date'] : '';
		$cart_item_id = $this->wptravel_get_cart_item_id( $trip_id, $price_key, $arrival_date );

		// For additional cart item attrs.
		if ( is_array( $attrs ) && count( $attrs ) > 0 ) {
			foreach ( $attrs as $key => $attr ) {
				$this->items[ $cart_item_id ][ $key ] = $attr;
			}
		}

		if ( is_array( $pax ) ) : // New Add to cart. Pax array as per categories[adult, child] @since 3.0.0
			$this->items[ $cart_item_id ]['trip_id']            = $trip_id;
			$this->items[ $cart_item_id ]['trip_price']         = wptravel_get_formated_price( $trip_price );
			$this->items[ $cart_item_id ]['trip_price_partial'] = wptravel_get_formated_price( $trip_price_partial );
		else :
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

						WPTravel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . sprintf( __( 'Requested pax size of %1$s exceeds the available pax limit ( %2$s ) for this trip. Available pax is set for booking.', 'wp-travel' ), $pax, $available_pax ), 'error' );

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

		endif;
		$this->clear_discount_values();
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

		$cart_items = WPTravel()->session->set( $this->cart_id, $cart );
		// Cookie data to enable data info in js.
		ob_start();
		setcookie( 'wp_travel_cart', wp_json_encode( $cart ), time() + 604800, '/' );
		ob_end_flush();
	}
	/**
	 * Read items from cart session.
	 */
	private function read() {
		$cart            = WPTravel()->session->get( $this->cart_id );
		$cart_items      = ! empty( $cart['cart_items'] ) ? $cart['cart_items'] : array(); // Checking if not empty to remove php notice on log. @since 4.3.0.
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
	 * Update item quantity ( PAX ) and price and remove from cart if qty is 0.
	 *
	 * @param  int   $cart_item_id ID of targed item.
	 * @param  int   $pax          Quantity.
	 * @param  int   $trip_extras  Extras.
	 * @param  array $attr         Attributes of item.
	 * @return boolean
	 */
	public function update( $cart_item_id, $pax, $trip_extras = false, $attr = array() ) {

		// Remove from cart if qty is 0 or less than 1.
		if ( is_array( $pax ) || is_object( $pax ) ) {
			$pax = (array) $pax;
			if ( empty( $pax ) ) {
				return $this->remove( $cart_item_id );
			}
		} else {
			if ( $pax < 1 ) {
				return $this->remove( $cart_item_id );
			}
		}

		// Update quantity.
		if ( isset( $this->items[ $cart_item_id ] ) ) {
			if ( is_array( $pax ) ) { // New @since 3.0.0.
				/**
				 * Customization Starts.
				 */
				// $max_available = $this->items[ $cart_item_id ]['max_available'];
				$trip_id       = $this->items[ $cart_item_id ]['trip_id'];
				$trip_price    = $this->items[ $cart_item_id ]['trip_price'];
				$pricing_id    = $this->items[ $cart_item_id ]['pricing_id'];
				$cart_trip     = $this->items[ $cart_item_id ]['trip'];
				$max_available = $this->items[ $cart_item_id ]['max_available'];

				$trip_price         = 0;
				$trip_price_partial = 0;
				foreach ( $pax as $category_id => $pax_value ) {
					if ( $pax_value < 1 ) {
						unset( $this->items[ $cart_item_id ]['trip'][ $category_id ] );
						continue;
					}
					$this->items[ $cart_item_id ]['trip'][ $category_id ]['pax'] = $pax_value;

					$args           = array(
						'trip_id'     => $trip_id,
						'pricing_id'  => $pricing_id,
						'category_id' => $category_id,
					);
					$category_price = WP_Travel_Helpers_Pricings::get_price( $args );

					if ( function_exists( 'wptravel_group_discount_price' ) ) { // From Group Discount addons.
						$group_trip_price = wptravel_group_discount_price( $trip_id, $pax_value, $pricing_id, $category_id );

						if ( $group_trip_price ) {
							$category_price = $group_trip_price;
						}
					}
					$category_price_partial = $category_price;

					// if ( $this->items[ $cart_item_id ]['enable_partial'] ) {
					if ( wptravel_is_partial_payment_enabled() ) {
						$percent                = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );
						$category_price_partial = ( $category_price * $percent ) / 100;
					}
					// Updating individual category price. [ Price may change if group discount applies. so need to update individual category price as well].
					$this->items[ $cart_item_id ]['trip'][ $category_id ]['price']         = $category_price;
					$this->items[ $cart_item_id ]['trip'][ $category_id ]['price_partial'] = $category_price_partial;

					// multiply category_price by pax to add in trip price if price per is person.
					if ( 'person' == $cart_trip[ $category_id ]['price_per'] ) {
						$category_price         *= $pax_value;
						$category_price_partial *= $pax_value;
					}
					// add price.
					$trip_price         += $category_price;
					$trip_price_partial += $category_price_partial;

				}
				// Custom Trip Price.
				if ( isset( $_REQUEST['trip_price'] ) && $_REQUEST['trip_price'] > 0 ) {
					$trip_price = floatval( $_REQUEST['trip_price'] );
				}

				$this->items[ $cart_item_id ]['trip_price']         = $trip_price;
				$this->items[ $cart_item_id ]['trip_price_partial'] = $trip_price_partial;

				if ( $trip_extras ) {

					if ( is_array( $trip_extras ) && ! empty( $trip_extras ) ) {
						$this->items[ $cart_item_id ]['trip_extras'] = $trip_extras;
					}
				}
				$cart_pax = array_sum( $pax ); // Sum of pax of all pricing category.

				// if ( $this->items[ $cart_item_id ]['enable_partial'] ) {
				// 	$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );

				// 	$this->items[ $cart_item_id ]['partial_payout_figure'] = $payout_percent;

				// 	if ( $payout_percent > 0 ) {
				// 		$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
				// 		$trip_price_partial = wptravel_get_formated_price( $trip_price_partial );
				// 	}
				// 	$this->items[ $cart_item_id ]['trip_price_partial'] = $trip_price_partial;
				// }
				if ( $max_available && $cart_pax > $max_available ) {
					WPTravel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . sprintf( __( 'Requested pax size of %1$s exceeds the available pax limit ( %2$s ) for this trip. Available pax is set for booking.', 'wp-travel' ), $cart_pax, $max_available ), 'error' );
				}
			} else {
				/**
				 * Customization Starts.
				 */
				$max_available = $this->items[ $cart_item_id ]['max_available'];
				$trip_id       = $this->items[ $cart_item_id ]['trip_id'];
				$price_key     = $this->items[ $cart_item_id ]['price_key'];
				$pricing_id    = $this->items[ $cart_item_id ]['pricing_id'];

				$trip_price = $this->items[ $cart_item_id ]['trip_price'];
				// Custom Trip Price.
				if ( isset( $_REQUEST['trip_price'] ) && $_REQUEST['trip_price'] > 0 ) {
					$trip_price = floatval( $_REQUEST['trip_price'] );
				}
				if ( function_exists( 'wptravel_group_discount_price' ) ) { // From Group Discount addons.
					$group_trip_price = wptravel_group_discount_price( $trip_id, $pax, $pricing_id, $category_id, $price_key );
					if ( $group_trip_price ) {
						$trip_price = $group_trip_price;
					}
				}

				$trip_price_partial = $trip_price;
				if ( $this->items[ $cart_item_id ]['enable_partial'] ) {
					$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $trip_id );

					$this->items[ $cart_item_id ]['partial_payout_figure'] = $payout_percent;

					if ( $payout_percent > 0 ) {
						$trip_price_partial = ( $trip_price * $payout_percent ) / 100;
						$trip_price_partial = wptravel_get_formated_price( $trip_price_partial );
					}
					$this->items[ $cart_item_id ]['trip_price_partial'] = $trip_price_partial;
				}

				$max_available = apply_filters( 'wp_travel_available_pax', $max_available, $trip_id, $price_key );

				$this->items[ $cart_item_id ]['pax']        = ( $max_available && $pax > $max_available ) ? $max_available : $pax;
				$this->items[ $cart_item_id ]['trip_price'] = $trip_price;

				if ( $trip_extras ) {

					if ( is_array( $trip_extras ) && ! empty( $trip_extras ) ) {
						$this->items[ $cart_item_id ]['trip_extras'] = $trip_extras;
					}
				}

				if ( $max_available && $pax > $max_available ) {
					WPTravel()->notices->add( '<strong>' . __( 'Error:', 'wp-travel' ) . '</strong> ' . sprintf( __( 'Requested pax size of %1$s exceeds the available pax limit ( %2$s ) for this trip. Available pax is set for booking.', 'wp-travel' ), $pax, $max_available ), 'error' );
				}
			}

			$this->write();
			$this->clear_discount_values();
			return true;
		}
		return false;
	}

	/**
	 * Add Discount Values
	 *
	 * @param $coupon_code Coupon Code. @since 3.1.7
	 */
	public function add_discount_values( $coupon_id, $discount_type, $discount_value, $coupon_code = null ) {

		$this->discounts['type']        = $discount_type;
		$this->discounts['value']       = $discount_value;
		$this->discounts['coupon_id']   = $coupon_id;
		$this->discounts['coupon_code'] = is_null( $coupon_code ) ? '' : $coupon_code;

		global $wt_cart;

		$items = $this->getItems();

		// loop cart items here.
		foreach ( $items as $cart_item_id => $item ) {
			$trip_id = $item['trip_id'];
			unset( $this->items[ $cart_item_id ]['discount'] ); // clear discount amount on individual. [this will not conflict while using multiple coupon applied for multiple trips].
			if ( WPTravel()->coupon->is_discountable( $coupon_id, $trip_id ) ) {
				$item_total = $this->get_item_total( $cart_item_id ); // Total of individual item including extras.
				if ( 'fixed' === $discount_type ) {
					$discount_amount = $discount_value;
				} elseif ( 'percentage' === $discount_type ) {
					$discount_amount = ( $item_total * $discount_value ) / 100;
				}
				$this->items[ $cart_item_id ]['discount'] = $discount_amount;  // Discount amount applied to individual trip total.
			}
		}

		$this->write();

		return true;

	}

	/**
	 * This will clear discount values. Call this function while add/remove item from cart.
	 *
	 * @since WP Travel 4.4.7
	 */
	public function clear_discount_values() {
		$this->discounts = array();

		$items = $this->getItems();
		// loop cart items here.
		foreach ( $items as $cart_item_id => $item ) {
			unset( $this->items[ $cart_item_id ]['discount'] ); // clear discount amount on individual. [this will not conflict while using multiple coupon applied for multiple trips].
		}
		$this->write();
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
		echo sprintf( __( 'Your cart is empty please <a href="%s"> click here </a> to add trips.', 'wp-travel' ), esc_url( $url ) );
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
		$this->clear_discount_values();
		$this->write();
	}
	// /**
	// * Remove cart trip extras.
	// */
	// public function remove_trip_extras
	function get_total( $with_discount = true ) {

		$trips = $this->items;

		$discounts = apply_filters( 'wp_travel_trip_discounts', $this->discounts, $trips );

		$cart_total      = 0;
		$tax_amount      = 0;
		$discount_amount = 0;

		$cart_total_partial      = 0;
		$tax_amount_partial      = 0;
		$discount_amount_partial = 0;

		/**
		 * @since 4.0.0
		 */
		$settings        = wptravel_get_settings();
		$wp_travel_react = isset( $settings['wp_travel_switch_to_react'] ) && 'yes' === $settings['wp_travel_switch_to_react'];

		// Total amount without tax.
		if ( is_array( $trips ) && count( $trips ) > 0 ) {
			foreach ( $trips as $cart_id => $trip ) :
				$cart_total         += $this->get_item_total( $cart_id ); // Total excluding discount.
				$cart_total_partial += $this->get_item_total( $cart_id, true ); // Total excluding discount.
			endforeach;
		}

		$cart_total = apply_filters( 'wp_travel_cart_sub_total', wptravel_get_formated_price( $cart_total ) );

		// Discounts Calculation.
		if ( ! empty( $discounts ) && $with_discount ) { // $with_discount will help to get actual total while calculating discount.
			$coupon_id                         = $this->discounts['coupon_id'];
			$discount_applicable_total         = WPTravel()->coupon->get_discount_applicable_total( $coupon_id );
			$discount_applicable_total_partial = WPTravel()->coupon->get_discount_applicable_total( $coupon_id, true ); // Partial discount.

			$d_typ = $discounts['type'];
			$d_val = $discounts['value'];

			if ( 'fixed' === $d_typ ) {
				$discount_amount = $discount_amount_partial = $d_val;
				if ( $discount_amount_partial >= $discount_applicable_total_partial ) {
					$discount_amount_partial = 0;
				}
			} elseif ( 'percentage' === $d_typ ) {
				$discount_amount         = ( $discount_applicable_total * $d_val ) / 100;
				$discount_amount_partial = ( $discount_applicable_total_partial * $d_val ) / 100;
			}
		}

		// Totals after discount.
		$total_trip_price_after_dis         = $cart_total - $discount_amount;
		$total_trip_price_partial_after_dis = $cart_total_partial - $discount_amount_partial;

		// Adding tax to sub total;
		if ( $tax_rate = WP_Travel_Helpers_Trips::get_tax_rate() ) :
			$tax_amount         = ( $total_trip_price_after_dis * $tax_rate ) / 100;
			$tax_amount_partial = ( $total_trip_price_partial_after_dis * $tax_rate ) / 100;
		endif;

		// Calcualtion of Total Amount.
		$total_trip_price         = $total_trip_price_after_dis + $tax_amount;
		$total_trip_price_partial = $total_trip_price_partial_after_dis + $tax_amount_partial;

		$get_total = array(
			'cart_total'         => wptravel_get_formated_price( $cart_total ), // Effective for multiple cart items[cart_total].
			'discount'           => wptravel_get_formated_price( $discount_amount ),
			'sub_total'          => wptravel_get_formated_price( $total_trip_price_after_dis ),
			'tax'                => wptravel_get_formated_price( $tax_amount ),
			'total'              => wptravel_get_formated_price( $total_trip_price ),

			// Total payble amount // Same as above price if partial payment not enabled.
			'cart_total_partial' => wptravel_get_formated_price( $cart_total_partial ),
			'discount_partial'   => wptravel_get_formated_price( $discount_amount_partial ),
			'sub_total_partial'  => wptravel_get_formated_price( $total_trip_price_partial_after_dis ),
			'tax_partial'        => wptravel_get_formated_price( $tax_amount_partial ),
			'total_partial'      => wptravel_get_formated_price( $total_trip_price_partial ),
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
	public function wptravel_get_cart_item_id( $trip_id, $price_key = '', $start_date = '' ) {

		$cart_item_id = ( isset( $price_key ) && '' !== $price_key ) ? $trip_id . '_' . $price_key : $trip_id;
		$cart_item_id = ( isset( $start_date ) && '' !== $start_date ) ? $cart_item_id . '_' . $start_date : $cart_item_id;
		return apply_filters( 'wp_travel_filter_cart_item_id', $cart_item_id, $trip_id, $price_key );
	}

	/**
	 * Return true if multiple cart item is allowed.
	 *
	 * @since WP Travel 4.4.2
	 */
	public static function allow_multiple_items() {
		return apply_filters( 'wp_travel_allow_multiple_cart_items', false );
	}

	/**
	 * Get trip total along with extras. Gross total only.
	 *
	 * @since WP Travel 4.4.7
	 */
	public function get_item_total( $cart_item_id = '', $partial_total = false ) {
		if ( ! $cart_item_id ) {
			return;
		}
		$items = $this->items;
		$item  = isset( $items[ $cart_item_id ] ) ? $items[ $cart_item_id ] : array();
		$trip_extras = isset( $item['trip_extras'] ) ? (array) $item['trip_extras'] : array();

		// Total Calculation.
		$item_total         = $item['trip_price'];
		$item_total_partial = isset( $item['trip_price_partial'] ) ? $item['trip_price_partial'] : $item['trip_price']; // quick fix.
		if ( isset( $trip_extras ) && ! empty( $trip_extras ) && isset( $trip_extras['id'] ) && is_array( $trip_extras['id'] ) ) {
			foreach ( $trip_extras['id'] as $k => $e_id ) {
				$trip_extras_data = get_post_meta( $e_id, 'wp_travel_tour_extras_metas', true );

				$price      = isset( $trip_extras_data['extras_item_price'] ) && ! empty( $trip_extras_data['extras_item_price'] ) ? $trip_extras_data['extras_item_price'] : 0;
				$sale_price = isset( $trip_extras_data['extras_item_sale_price'] ) && ! empty( $trip_extras_data['extras_item_sale_price'] ) ? $trip_extras_data['extras_item_sale_price'] : false;

				// Filter to add the custom price for the tour extras.
				$price      = apply_filters( 'wp_travel_trip_extras_custom_prices', $price, $e_id, $item['trip_id'] );
				$sale_price = apply_filters( 'wp_travel_trip_extras_custom_sale_prices', $sale_price, $e_id, $item['trip_id'] );
				// Filter to add the custom price for the tour extras.
				if ( $sale_price ) {
					$price = $sale_price;
				}
				$qty         = isset( $trip_extras['qty'][ $k ] ) && ! empty( $trip_extras['qty'][ $k ] ) ? $trip_extras['qty'][ $k ] : 1;
				$extra_price = wptravel_get_formated_price( $price * $qty );

				$item_total += $extra_price;

				// Partial extras calc
				if ( wptravel_is_partial_payment_enabled() ) {
					$payout_percent = WP_Travel_Helpers_Pricings::get_payout_percent( $item['trip_id'] );
					$extra_price    = ( $extra_price * $payout_percent ) / 100;
				}
				$item_total_partial += $extra_price;
			}
		}
		return $partial_total ? $item_total_partial : $item_total;
	}
}

new WP_Travel_Cart();

// Set cart global variable.
$GLOBALS['wt_cart'] = new WP_Travel_Cart();
