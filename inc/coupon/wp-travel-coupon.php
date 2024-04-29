<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Travel_Coupon_Pro' ) ) :

	/**
	 * Main WP_Travel_Coupon_Pro Class (singleton).
	 *
	 * @since 1.0.0
	 */
	final class WP_Travel_Coupon_Pro {

		/**
		 * Plugin Name.
		 *
		 * @var string
		 */
		public $plugin_name = 'wp-travel-coupon-pro';

		/**
		 * Assets Path.
		 *
		 * @var string
		 */
		public $assets_path;

		/**
		 * The single instance of the class.
		 *
		 * @var WP Travel
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Admin Settings Page.
		 *
		 * @var string
		 */
		static $collection = 'settings';

		/**
		 * Main WP_travel_coupon_pro Instance.
		 * Ensures only one instance of WP_travel_coupon_pro is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see WP_travel_coupon_pro()
		 * @return WP_travel_coupon_pro - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WP_Travel Constructor.
		 */
		public function __construct() {

			$this->define_constants();
			$this->assets_path = plugin_dir_url( WP_TRAVEL_COUPON_PRO_PLUGIN_FILE ) . 'assets/';
			$this->includes();
			$settings = wptravel_get_settings();
			if( $settings['enable_woo_checkout'] == 'no' ){ 
				$this->init_hooks();
			}
		}

		/**
		 * Define Constants.
		 */
		private function define_constants() {
			$this->define( 'WP_TRAVEL_COUPON_POST_TYPE', 'wp-travel-coupons' );
			$this->define( 'WP_TRAVEL_COUPON_PRO_PLUGIN_FILE', __FILE__ );
			$this->define( 'WP_TRAVEL_COUPON_PRO_ABSPATH', dirname( __FILE__ ) . '/' );
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function init_hooks() {
			// register_activation_hook( __FILE__, array( $this, 'coupons_activation' ) );
			$priority = 10;
			if ( function_exists( 'wptravel_get_submenu' ) ) {
				$sbumenus = wptravel_get_submenu();
				$priority = isset( $sbumenus['bookings']['coupon']['priority'] ) ? $sbumenus['bookings']['coupon']['priority'] : $priority;
			}
			add_action( 'init', array( 'WP_Travel_Coupons_Pro_Install', 'init' ), $priority );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name  Name of constant.
		 * @param  string $value Value of constant.
		 * @return void
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @return void
		 */
		public function includes() {

			include sprintf( '%s/inc/class-install.php', WP_TRAVEL_COUPON_PRO_ABSPATH );

			include sprintf( '%s/inc/class-coupon.php', WP_TRAVEL_COUPON_PRO_ABSPATH );

			// Include Public Assets Class.
			include sprintf( '%s/inc/public/class-public-assets.php', WP_TRAVEL_COUPON_PRO_ABSPATH );

			if ( $this->is_request( 'admin' ) ) {
				include sprintf( '%s/inc/admin/class-admin-metaboxes.php', WP_TRAVEL_COUPON_PRO_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-assets.php', WP_TRAVEL_COUPON_PRO_ABSPATH );
			}
		}
		/**
		 * Activation Hook.
		 */
		public function coupons_activation() {
			// Flush Rewrite rule.
			$coupons_pro_install = new WP_Travel_Coupons_Pro_Install();
			$coupons_pro_install::init();

			flush_rewrite_rules();
		}


		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin':
					return is_admin();
				case 'ajax':
					return defined( 'DOING_AJAX' );
				case 'cron':
					return defined( 'DOING_CRON' );
				case 'frontend':
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

	}
endif;
/**
 * Main instance of WP Travel Coupons.
 *
 * Returns the main instance of WP_travel_Coupons to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WP Travel Coupons
 */
function wptravel_coupon_pro() {
	return WP_Travel_Coupon_Pro::instance();
}

// Start WP Travel Coupons.
wptravel_coupon_pro();
