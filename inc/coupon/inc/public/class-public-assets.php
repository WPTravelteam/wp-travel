<?php
/**
 * Public Assets Class.
 *
 * @package WP_Travel_Coupons_Pro
 */

if ( ! class_exists( 'WP_Travel_Coupons_Pro_Public_Assets' ) ) :
	/**
	 * Admin Assets Class.
	 */
	class WP_Travel_Coupons_Pro_Public_Assets {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->assets_path = plugin_dir_url( WP_TRAVEL_COUPON_PRO_PLUGIN_FILE );
			add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		}
		/**
		 * Load Scripts
		 */
		public function scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_script( 'wp-travel-coupons-frontend-js', $this->assets_path . 'assets/js/wp-travel-coupons-frontend' . $suffix . '.js', array( 'jquery' ), '', true );

		}
		/**
		 * Load Styles
		 */
		public function styles() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_enqueue_style( 'wp-travel-coupons-frontend-css', $this->assets_path . 'assets/css/wp-travel-coupons-frontend' . $suffix . '.css' );

		}


	}

endif;

new WP_Travel_Coupons_Pro_Public_Assets();
