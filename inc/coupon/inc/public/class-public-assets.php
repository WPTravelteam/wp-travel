<?php
/**
 * Public Assets Class.
 *
 * @package WP_Travel
 */

if ( ! class_exists( 'WP_Travel_Coupons_Pro_Public_Assets' ) ) :
	/**
	 * Admin Assets Class.
	 */
	class WP_Travel_Coupons_Pro_Public_Assets {
		public $assets_path;
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->assets_path = plugin_dir_url( WP_TRAVEL_COUPON_PRO_PLUGIN_FILE );

		}
		/**
		 * Load Scripts
		 */
		public function scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		}
		/**
		 * Load Styles
		 */
		public function styles() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		}


	}

endif;

new WP_Travel_Coupons_Pro_Public_Assets();
