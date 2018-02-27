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
     * @since 2.2.3
     */
    public static function output(){

        echo 'Checkout Page Details';




    }
	
}

new WP_Travel_Checkout();
