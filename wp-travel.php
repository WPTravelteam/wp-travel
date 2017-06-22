<?php
/**
 * Plugin Name: WP Travel
 * Plugin URI: http://www.wensolutions.com/plugins/trip
 * Description: This plugin is used to add trip for any travel and tour site
 * Version: 1.0.0
 * Author: WEN Solutions
 * Author URI: http://wensolutions.com
 * Requires at least: 4.4
 * Tested up to: 4.7
 *
 * Text Domain: wp-travel
 * Domain Path: /i18n/languages/
 *
 * @package WP Travel
 * @category Core
 * @author WenSolutions
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Travel' ) ) :

	/**
	 * Main WP_Travel Class (singleton).
	 *
	 * @since 1.0.0
	 */
	final class WP_Travel {

		/**
		 * WP Travel version.
		 *
		 * @var string
		 */
		public $version = '1.0.0';
		/**
		 * The single instance of the class.
		 *
		 * @var WP Travel
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main WP_Travel Instance.
		 * Ensures only one instance of WP_Travel is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see WP_Travel()
		 * @return WP_Travel - Main instance.
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
		function __construct() {
			$this->define_constants();
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Define WC Constants.
		 */
		private function define_constants() {
			$this->define( 'WP_TRAVEL_PLUGIN_FILE', __FILE__ );
			$this->define( 'WP_TRAVEL_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'WP_TRAVEL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WP_TRAVEL_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define( 'WP_TRAVEL_TEMPLATE_PATH', 'wp-travel/' );
			$this->define( 'WP_TRAVEL_VERSION', $this->version );
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, 'wp_travel_activation' ) );
			add_action( 'after_setup_theme', array( $this, 'wp_travel_setup_environment' ) );

			add_action( 'init', array( 'WP_Travel_Post_Types', 'init' ) );
			add_action( 'init', array( 'Wp_Travel_Taxonomies', 'init' ) );

			add_shortcode( 'WP_TRAVEL_BOOKING', array( 'Wp_Travel_Shortcodes', 'wp_travel_booking_form' ) );
			add_action( 'init', array( 'Wp_Travel_Shortcodes', 'wp_traval_book_now' ), 99 );

			if ( $this->is_request( 'admin' ) ) {
				$this->tabs = new WP_Travel_Admin_Tabs();
				$this->uploader = new WP_Travel_Admin_Uploader();
			}
			$this->session = new WP_Travel_Session();
			$this->notices = new WP_Travel_Notices();
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
		function includes() {
			include sprintf( '%s/inc/class-frontend-assets.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/currencies.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/countries.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/booking-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/helpers.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-session.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-notices.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/template-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-ajax.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-post-types.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-taxonomies.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-itinerary-template.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-shortcode.php', WP_TRAVEL_ABSPATH );

			if ( $this->is_request( 'admin' ) ) {
				include sprintf( '%s/inc/admin/class-admin-uploader.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-tabs.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-metaboxes.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-assets.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-settings.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-menu.php', WP_TRAVEL_ABSPATH );
			}
		}

		/**
		 * What type of request is this?
		 *
		 * @param  string $type admin, ajax, cron or frontend.
		 * @return bool
		 */
		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}
		/**
		 * WP Travel Activation.
		 */
		function wp_travel_activation() {

			// Flush Rewrite rule.
			$post_type = new WP_Travel_Post_Types();
			$post_type::init();
			$taxonomy = new Wp_Travel_Taxonomies();
			$taxonomy::init();
			flush_rewrite_rules();

			add_image_size( 'wp_travel_thumbnail', 255, 150, false );
		}

		function wp_travel_setup_environment () {
			$this->add_thumbnail_support();
			$this->add_image_sizes();
		}

		/**
		 * Ensure post thumbnail support is turned on.
		 */
		private function add_thumbnail_support() {
			if ( ! current_theme_supports( 'post-thumbnails' ) ) {
				add_theme_support( 'post-thumbnails' );
			}
			add_post_type_support( 'itineraries', 'thumbnail' );
		}

		/**
		 * Add Image site.
		 *
		 * @since 1.0.0
		 */
		private function add_image_sizes() {
			add_image_size( 'wp_travel_thumbnail', 255, 150, true );
		}

	}
endif;
/**
 * Main instance of WP Travel.
 *
 * Returns the main instance of WP_Travel to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return WP Travel
 */
function WP_Travel() {
	return WP_Travel::instance();
}

// Start WP Travel.
WP_Travel();
