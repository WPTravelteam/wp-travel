<?php
/**
 * Admin Assets Class.
 *
 * @package WP_Travel
 */

if ( ! class_exists( 'WP_Travel_Coupons_Pro_Admin_Assets' ) ) :
	/**
	 * Admin Assets Class.
	 */
	class WP_Travel_Coupons_Pro_Admin_Assets { // @phpcs:ignore

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->assets_path = plugin_dir_url( WP_TRAVEL_COUPON_PRO_PLUGIN_FILE );
			add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		}
		/**
		 * Load Scripts
		 */
		public function scripts() {

			$screen = get_current_screen();

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$allowed_screen = array( 'wp-travel-coupons', 'edit-wp-travel-coupons', 'tour-extras', 'edit-tour-extras' );

			if ( in_array( $screen->id, $allowed_screen, true ) ) {

				wp_enqueue_script( 'wp-travel-coupons-backend-js', $this->assets_path . 'assets/js/wp-travel-coupons-backend.js', array( 'jquery', 'jquery-ui-tabs', 'tooltipster-min-js', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION, true );

				wp_enqueue_script( 'tooltipster-min-js', $this->assets_path . 'assets/js/lib/tooltipster/js/tooltipster.bundle' . $suffix . '.js', array( 'jquery', 'jquery-ui-tabs' ), WP_TRAVEL_VERSION, true );

			}

		}
		/**
		 * Load Styles
		 */
		public function styles() {

			$screen = get_current_screen();
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$allowed_screen = array( 'wp-travel-coupons', 'edit-wp-travel-coupons', 'tour-extras', 'edit-tour-extras' );
			if ( in_array( $screen->id, $allowed_screen, true ) ) {
				wp_enqueue_style( 'wp-travel-coupons-backend-css', $this->assets_path . 'assets/css/wp-travel-coupons-backend.css', array(), WP_TRAVEL_VERSION );
				wp_enqueue_style( 'tooltipster-min-css', $this->assets_path . 'assets/css/lib/tooltipster/css/tooltipster.bundle' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
				wp_enqueue_style( 'tooltipster-min-borderless', $this->assets_path . 'assets/css/lib/tooltipster/css/plugins/tooltipster/sideTip/themes/tooltipster-sideTip-borderless.min.css', array(), WP_TRAVEL_VERSION );
			}
		}
	}

endif;

new WP_Travel_Coupons_Pro_Admin_Assets();
