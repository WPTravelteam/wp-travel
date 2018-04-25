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
		include sprintf( '%s/inc/cart/cart.php', WP_TRAVEL_ABSPATH );	
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

		$cart_item_id = ( isset( $price_key ) && '' !== $price_key ) ? $trip_id . '_' . $price_key : $trip_id;

		// Add product id.
		$this->items[ $cart_item_id ]['trip_id'] 	= $trip_id;
		$this->items[ $cart_item_id ]['trip_price'] = $trip_price;
		$this->items[ $cart_item_id ]['pax'] 		= $pax;
		$this->items[ $cart_item_id ]['price_key'] 	= $price_key;

		// For additional cart item attrs.
		if ( is_array( $attrs ) && count( $attrs ) > 0 ) {
			foreach ( $attrs as $key => $attr ) {
				$this->items[ $cart_item_id ][ $key ] = $attr;
			}
		}

		$this->write();
		return true;
	}
	
	/**
	 * Write changes to cart session.
	 */
	private function write() {
		$cart_attributes_session_name = $this->cart_id . '_attributes';
		$items = array();

		foreach ( $this->items as $id => $item ) {
			if ( ! $id ) {
				continue;
			}
			$items[ $id ] = $item;
		}
		$cart_items = WP_Travel()->session->set( $this->cart_id, $items );
	}
	/**
	 * Read items from cart session.
	 */
	private function read() {
		$cart_items = WP_Travel()->session->get( $this->cart_id );
		
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
	public function update( $cart_item_id, $pax, $attr = array() ) {

		if ( $pax < 1 ) {
			return $this->remove( $cart_item_id );
		}

		// Update quantity.
		$this->items[ $cart_item_id ]['pax'] = ($pax > $this->quantity_limit) ? $this->quantity_limit : $pax;

		$this->write();

		return true;
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
		echo ( __( sprintf('Your cart is empty please <a href="%s"> click here </a> to add trips.', $url ), 'wp-travel' ) );
	}
	/**
	 * Clear all items in the cart.
	 */
	public function clear() {
		$this->items = array();
		$this->attributes = array();
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
}

new WP_Travel_Cart();

// Set cart global variable.
$GLOBALS['wt_cart'] = new WP_Travel_Cart();