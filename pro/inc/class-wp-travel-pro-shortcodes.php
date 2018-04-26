<?php
/**
 * Shortcode callbacks.
 *
 * @package wp-travel\inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WP travel Pro Shortcode class.
 *
 * @class Wp_Travel_Pro_Shortcodes
 */
class Wp_Travel_Pro_Shortcodes extends Wp_Travel_Shortcodes {
	/**
	 * Constructor
	 */
	public function __construct() {

		// add user shortcode.
		// add_filter( 'wp_travel_shortcodes', array( $this, 'wp_travel_user_account_shortcode' ) );
		add_shortcode( 'wp_travel_user_account', array( $this, 'user_account' ) );

	}
	/**
	 * Add User Account shortcode
	 */
	public function wp_travel_user_account_shortcode( $shortcodes ) {

		$shortcodes['wp_travel_user_account'] = __CLASS__ . '::user_account';

		return $shortcodes;

	}
	/**
	 * Add user Account shortcode.
	 *
	 * @return string
	 */
	public static function user_account() {
		return parent::shortcode_wrapper( array( 'Wp_Travel_User_Account', 'output' ) );
	}



}
new Wp_Travel_Pro_Shortcodes();
