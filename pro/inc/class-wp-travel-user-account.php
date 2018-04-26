<?php
/**
 * Wp_Travel_User_Account.
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
class Wp_Travel_User_Account {

	/**
	 * Constructor.
	 */
	public function __construct() {
	}

	/**
	 * Output of checkout shotcode.
	 *
	 * @since 2.2.3
	 */
	public static function output() {
		global $wt_cart;

		print_r( $wt_cart );
	}

}

