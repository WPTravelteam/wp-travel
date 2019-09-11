<?php
/**
 * Plugin Name: WP Travel
 * Plugin URI: http://wptravel.io/
 * Description: The best choice for a Travel Agency, Tour Operator or Destination Management Company, wanting to manage packages more efficiently & increase sales.
 * Version: 3.0.0
 * Author: WEN Solutions
 * Author URI: http://wptravel.io/downloads/
 * Requires at least: 4.4
 * Requires PHP: 5.5
 * Tested up to: 5.2.3
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
		public $version = '3.0.0';
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
			$this->init_shortcodes();
			$this->init_sidebars();
		}

		/**
		 * Define WP Travel Constants.
		 */
		private function define_constants() {
			$this->define( 'WP_TRAVEL_POST_TYPE', 'itineraries' );
			$this->define( 'WP_TRAVEL_POST_TITLE', __( 'trips', 'wp-travel' ) );
			$this->define( 'WP_TRAVEL_POST_TITLE_SINGULAR', __( 'trip', 'wp-travel' ) );
			$this->define( 'WP_TRAVEL_PLUGIN_FILE', __FILE__ );
			$this->define( 'WP_TRAVEL_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'WP_TRAVEL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WP_TRAVEL_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define( 'WP_TRAVEL_TEMPLATE_PATH', 'wp-travel/' );
			$this->define( 'WP_TRAVEL_VERSION', $this->version );
			$this->define( 'WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT', 10 ); // In percent.
			$this->define( 'WP_TRAVEL_SLIP_UPLOAD_DIR', 'wp-travel-slip' ); // In percent.
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, 'wp_travel_activation' ) );
			add_action( 'activated_plugin', array( $this, 'wp_travel_plugin_load_first_order' ) );
			add_action( 'after_setup_theme', array( $this, 'wp_travel_setup_environment' ) );

			add_action( 'init', array( 'WP_Travel_Post_Types', 'init' ) );

			// Set priority to move submenu.
			$sbumenus = wp_travel_get_submenu();
			$priority_enquiry = isset( $sbumenus['bookings']['enquiries']['priority'] ) ? $sbumenus['bookings']['enquiries']['priority'] : 10;
			$priority_extras = isset( $sbumenus['bookings']['extras']['priority'] ) ? $sbumenus['bookings']['extras']['priority'] : 10;
			add_action( 'init', array( 'WP_Travel_Post_Types', 'register_enquiries' ), $priority_enquiry );
			add_action( 'init', array( 'WP_Travel_Post_Types', 'register_tour_extras' ), $priority_extras );

			add_action( 'init', array( 'Wp_Travel_Taxonomies', 'init' ) );

			add_action( 'init', 'wp_travel_book_now', 99 );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( 'WP_Travel_Assets', 'frontend' ) );
			if ( $this->is_request( 'admin' ) ) {
				add_action( 'admin_enqueue_scripts', array( 'WP_Travel_Assets', 'admin' ) );

				// To delete transient.
				add_action( 'admin_init', 'wp_travel_admin_init' ); // @since 1.0.7
				// add_action( 'admin_menu', 'wp_travel_marketplace_menu');

				$this->tabs = new WP_Travel_Admin_Tabs();
				$this->uploader = new WP_Travel_Admin_Uploader();

				add_action( 'current_screen', array( $this, 'conditional_includes' ) );
			}
			$this->session = new WP_Travel_Session();
			$this->notices = new WP_Travel_Notices();
			$this->coupon  = new WP_Travel_Coupon();

			// For Network.
			add_action('network_admin_menu', array( $this, 'wp_travel_network_menu' ) );
		}

		public function wp_travel_network_menu() {
			add_menu_page( __( 'Settings', 'wp-travel' ), __( 'WP Travel', 'wp-travel' ), 'manae_options', 'wp_travel_network_settings', array( 'WP_Travel_Network_Settings', 'setting_page_callback' ), 'dashicons-wp-travel', 10 );
		}

		/**
		 * Load localisation files.
		 */
		public function load_textdomain() {
			$locale = is_admin() && function_exists( 'get_user_locale' ) ? get_user_locale() : get_locale();
			$locale = apply_filters( 'plugin_locale', $locale, 'wp-travel' );
			unload_textdomain( 'wp-travel' );

			load_textdomain( 'wp-travel', WP_LANG_DIR . '/wp-travel/wp-travel-' . $locale . '.mo' );
			load_plugin_textdomain( 'wp-travel', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages' );
		}
		/**
		 * Define constant if not already set.
		 *
		 * @param  string $name  Name of constant.
		 * @param  string $value Value of constant.
		 * @return void
		 */

		/**
		 * Init Shortcode for WP Travel.
		 */
		private function init_shortcodes(){
			$plugin_shortcode = new Wp_Travel_Shortcodes();
			$plugin_shortcode->init();
		}
		public function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}
		/**
		 * Init Sidebars for WP Travel.
		 */
		private function init_sidebars(){
			$plugin_sidebars = new Wp_Travel_Sidebars();
			$plugin_sidebars->init();
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @return void
		 */
		function includes() {
			include sprintf( '%s/inc/class-assets.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-default-form-fields.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-email-template.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/payments/wp-travel-payments.php',  dirname( __FILE__ ) );
			include sprintf( '%s/inc/license/wp-travel-license.php',  dirname( __FILE__ ) );
			include sprintf( '%s/inc/class-install.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/currencies.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/countries.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/booking-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/form-fields.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/trip-enquiries.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-itinerary.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/helpers.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/deprecated-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-session.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-notices.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/template-functions.php', WP_TRAVEL_ABSPATH );

			include sprintf( '%s/inc/coupon/wp-travel-coupon.php', WP_TRAVEL_ABSPATH );

			include_once sprintf( '%s/inc/gateways/standard-paypal/class-wp-travel-gateway-paypal-request.php', WP_TRAVEL_ABSPATH );
			include_once sprintf( '%s/inc/gateways/standard-paypal/paypal-functions.php', WP_TRAVEL_ABSPATH );
			include_once sprintf( '%s/inc/gateways/bank-deposit/bank-deposit.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/email-template-functions.php', WP_TRAVEL_ABSPATH );
			// Open Graph Tags @since 1.7.6
			include sprintf( '%s/inc/og-tags.php', WP_TRAVEL_ABSPATH );

			include sprintf( '%s/inc/class-ajax.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-post-types.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-taxonomies.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-itinerary-template.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-shortcode.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-widget-search.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-widget-featured.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-widget-location.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-widget-trip-type.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-widget-sale-widget.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-search-filters-widget.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/widgets/class-wp-travel-trip-enquiry-form-widget.php', WP_TRAVEL_ABSPATH );

			/**
			 * Include Query Classes.
			 * @since 1.2.6
			 */
			include sprintf( '%s/inc/class-wp-travel-query.php', WP_TRAVEL_ABSPATH );

			// User Modules.
			include sprintf( '%s/inc/wp-travel-user-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-wp-travel-user-account.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-wp-travel-form-handler.php', WP_TRAVEL_ABSPATH );

			// Pointers Class Includes.
			include sprintf( '%s/inc/admin/class-admin-pointers.php', WP_TRAVEL_ABSPATH );

			//Include Sidebars Class.
			include sprintf( '%s/inc/class-sidebars.php', WP_TRAVEL_ABSPATH );
			/**
			 * Include Cart and Checkout Classes.
			 * @since 1.2.3
			 */
			include sprintf( '%s/inc/cart/class-cart.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/cart/class-checkout.php', WP_TRAVEL_ABSPATH );

			if ( $this->is_request( 'admin' ) ) {
				include sprintf( '%s/inc/admin/admin-helper.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/admin-notices.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-uploader.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-tabs.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-metaboxes.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/extras/class-tour-extras-admin-metabox.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-settings.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-network-settings.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-menu.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-admin-status.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-dashboard-widgets.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/class-wp-travel-term-meta.php', WP_TRAVEL_ABSPATH );
				include sprintf( '%s/inc/admin/tablenav.php', WP_TRAVEL_ABSPATH );
			}

			if ( $this->is_request( 'frontend' ) ) {

				include sprintf( '%s/inc/class-wp-travel-extras-frontend.php', WP_TRAVEL_ABSPATH );
			}

			if ( ! class_exists( 'WP_Travel_Plugin_Updater' ) ) {
				// load our custom updater.
				include sprintf( '%s/inc/license/wp-travel-plugin-updater.php', WP_TRAVEL_ABSPATH );
			}

		}

		/**
		 * Include admin files conditionally.
		 */
		public function conditional_includes() {
			if ( ! $screen = get_current_screen() ) {
				return;
			}

			switch ( $screen->id ) {
				case 'options-permalink' :
					include sprintf( '%s/inc/admin/class-admin-permalink-settings.php', WP_TRAVEL_ABSPATH );
				break;
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
		 * Create roles and capabilities.
		 */
		public static function create_roles() {
			global $wp_roles;

			if ( ! class_exists( 'WP_Roles' ) ) {
				return;
			}

			if ( ! isset( $wp_roles ) ) {
				$wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
			}

			// Customer role.
			add_role(
				'wp-travel-customer',
				__( 'WP Travel Customer', 'wp-travel' ),
				array(
					'read' => true,
				)
			);
		}
		/**
		 * WP Travel Activation.
		 */
		function wp_travel_activation() {
			// Check for PHP Compatibility
			global $wp_version;
			$min_php_ver = '5.3.29';
			if ( version_compare( PHP_VERSION, $min_php_ver, '<' ) ) {

				$flag 	 = __( 'PHP', 'wp-travel' );
				$version = sprintf( __( '%s or Higher', 'wp-travel' ), $min_php_ver );

				deactivate_plugins( basename( __FILE__ ) );

				$message = sprintf( __( 'WP Travel plugin requires %1$s version %2$s or greater to work.', 'wp-travel' ), $flag, $version );

				wp_die( $message,__('Plugin Activation Error', 'wp-travel' ),  array( 'response'=>200, 'back_link'=>TRUE ) );
			}

			// Flush Rewrite rule.
			WP_Travel_Post_Types::init();
			Wp_Travel_Taxonomies::init();
			flush_rewrite_rules();

			$itineraries = get_posts( array( 'post_type' => 'itineraries', 'post_status' => 'publish' ) );
			if ( count( $itineraries ) > 0 ) {
				foreach( $itineraries as $itinerary ) {
					$post_id = $itinerary->ID;
					$trip_price = get_post_meta( $post_id, 'wp_travel_trip_price', true );
					if ( $trip_price > 0 ) {
						continue;
					}

					$enable_sale = get_post_meta( $post_id, 'wp_travel_enable_sale', true );

					if ( $enable_sale ) {
						$trip_price = wp_travel_get_trip_sale_price( $post_id );
					} else {
						$trip_price = wp_travel_get_trip_price( $post_id );
					}
					update_post_meta( $post_id, 'wp_travel_trip_price', $trip_price );
				}
			}
			// Added Date Formatting for filter.
			if ( count( $itineraries ) > 0 ) {
				foreach( $itineraries as $itinerary ) {
					$post_id         = $itinerary->ID;
					$fixed_departure = get_post_meta( $post_id, 'wp_travel_fixed_departure', true );
					if ( 'no' == $fixed_departure ) {
						continue;
					}
					$wp_travel_start_date = get_post_meta( $post_id, 'wp_travel_start_date', true );
					$wp_travel_end_date   = get_post_meta( $post_id, 'wp_travel_end_date', true );

					if ( '' !== $wp_travel_start_date ) {

						$wp_travel_start_date = strtotime( $wp_travel_start_date );
						$wp_travel_start_date = date( 'Y-m-d', $wp_travel_start_date );
						update_post_meta( $post_id, 'wp_travel_start_date', $wp_travel_start_date );
					}

					if ( '' !== $wp_travel_end_date ) {

						$wp_travel_end_date = strtotime( $wp_travel_end_date );
						$wp_travel_end_date = date( 'Y-m-d', $wp_travel_end_date );
						update_post_meta( $post_id, 'wp_travel_end_date', $wp_travel_end_date );
					}
				}
			}

			/**
			 * Insert cart and checkout pages
			 *
			 * @since 1.2.3
			 */

			include_once sprintf( '%s/inc/admin/admin-helper.php', WP_TRAVEL_ABSPATH );

				$pages = apply_filters(
					'wp_travel_create_pages', array(
						'wp-travel-cart'      => array(
							'name'    => _x( 'wp-travel-cart', 'Page slug', 'wp-travel' ),
							'title'   => _x( 'WP Travel Cart', 'Page title', 'wp-travel' ),
							'content' => '[' . apply_filters( 'wp_travel_cart_shortcode_tag', 'wp_travel_cart' ) . ']',
						),
						'wp-travel-checkout'  => array(
							'name'    => _x( 'wp-travel-checkout', 'Page slug', 'wp-travel' ),
							'title'   => _x( 'WP Travel Checkout', 'Page title', 'wp-travel' ),
							'content' => '[' . apply_filters( 'wp_travel_checkout_shortcode_tag', 'wp_travel_checkout' ) . ']',
						),
						'wp-travel-dashboard' => array(
							'name'    => _x( 'wp-travel-dashboard', 'Page slug', 'wp-travel' ),
							'title'   => _x( 'WP Travel Dashboard', 'Page title', 'wp-travel' ),
							'content' => '[' . apply_filters( 'wp_travel_account_shortcode_tag', 'wp_travel_user_account' ) . ']',
						),
					)
				);

			foreach ( $pages as $key => $page ) {
				wp_travel_create_page( esc_sql( $page['name'] ), 'wp_travel_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wp_travel_get_page_id( $page['parent'] ) : '' );
			}

			if ( version_compare( $this->version, '1.0.4', '>' ) ) {
				include sprintf( '%s/upgrade/104-105.php', WP_TRAVEL_ABSPATH );
			}
			if ( version_compare( $this->version, '1.0.6', '>' ) ) {
				include_once sprintf( '%s/upgrade/106-110.php', WP_TRAVEL_ABSPATH );
			}
			if ( version_compare( $this->version, '1.2.0', '>' ) ) {
				include_once sprintf( '%s/upgrade/update-121.php', WP_TRAVEL_ABSPATH );
			}
			if ( version_compare( $this->version, '1.7.5', '>' ) ) {
				include_once sprintf( '%s/upgrade/175-176.php', WP_TRAVEL_ABSPATH );
			}
			if ( version_compare( $this->version, '1.9.3', '>' ) ) {
				include_once sprintf( '%s/upgrade/193-194.php', WP_TRAVEL_ABSPATH );
			}
			$current_db_version = get_option( 'wp_travel_version' );
			if ( WP_TRAVEL_VERSION !== $current_db_version ) {
				if ( empty( $current_db_version ) ) {
					update_option( 'wp_travel_user_since', WP_TRAVEL_VERSION ); // @since 3.0.0
					update_option( 'wp_travel_user_after_multiple_pricing_category', 'yes' ); // option is used to hide option 'Enable multiple category on pricing' and single pricng option @since 3.0.0 
				}
				update_option( 'wp_travel_version', WP_TRAVEL_VERSION );
			}
			// Update marketplace data transient.
			delete_transient( 'wp_travel_marketplace_addons_list' );

			/**
			 * Define Roles.
			 * @since 1.3.7
			 */
			self::create_roles();
		}

		function wp_travel_setup_environment() {
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
		 * Add Image size.
		 *
		 * @since 1.0.0
		 */
		private function add_image_sizes() {
			$image_size = apply_filters( 'wp_travel_image_size', array( 'width' => 365, 'height' => 215 ) );
			$width = $image_size['width'];
			$height = $image_size['height'];
			add_image_size( 'wp_travel_thumbnail', $width, $height, true );
		}

		function wp_travel_plugin_load_first_order() {
			$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
			if ( $plugins = get_option( 'active_plugins' ) ) {
				if ( $key = array_search( $path, $plugins ) ) {
					array_splice( $plugins, $key, 1 );
					array_unshift( $plugins, $path );
					update_option( 'active_plugins', $plugins );
				}
			}
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
