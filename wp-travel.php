<?php
/**
 * Plugin Name: WP Travel
 * Plugin URI: http://wptravel.io/
 * Description: The best choice for a Travel Agency, Tour Operator or Destination Management Company, wanting to manage packages more efficiently & increase sales.
 * Version: 4.4.7
 * Author: WP Travel
 * Author URI: http://wptravel.io/downloads/
 * Requires at least: 5.4.1
 * Requires PHP: 5.5
 * Tested up to: 5.6.2
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
		public $version = '4.4.7';

		/**
		 * WP Travel API version.
		 *
		 * @var string
		 */
		public $api_version = 'v1';

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
		 * @see WPTravel()
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
			$api_version = apply_filters( 'wp_travel_api_version', $this->api_version );
			$this->define( 'WP_TRAVEL_POST_TYPE', 'itineraries' );
			$this->define( 'WP_TRAVEL_POST_TITLE', __( 'trips', 'wp-travel' ) );
			$this->define( 'WP_TRAVEL_POST_TITLE_SINGULAR', __( 'trip', 'wp-travel' ) );
			$this->define( 'WP_TRAVEL_PLUGIN_FILE', __FILE__ );
			$this->define( 'WP_TRAVEL_ABSPATH', dirname( __FILE__ ) . '/' );
			$this->define( 'WP_TRAVEL_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'WP_TRAVEL_PLUGIN_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->define( 'WP_TRAVEL_TEMPLATE_PATH', 'wp-travel/' );
			$this->define( 'WP_TRAVEL_VERSION', $this->version );
			$this->define( 'WP_TRAVEL_API_VERSION', $api_version );
			$this->define( 'WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT', array( 10 ) ); // In percent.
			$this->define( 'WP_TRAVEL_SLIP_UPLOAD_DIR', 'wp-travel-slip' ); // In percent.
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function init_hooks() {

			register_activation_hook( __FILE__, array( $this, 'wptravel_activation' ) );
			add_action( 'activated_plugin', array( $this, 'wptravel_plugin_load_first_order' ) );
			add_action( 'after_setup_theme', array( $this, 'wptravel_setup_environment' ) );

			add_action( 'init', array( 'WP_Travel_Post_Types', 'init' ) );

			// Set priority to move submenu.
			$sbumenus         = wptravel_get_submenu();
			$priority_enquiry = isset( $sbumenus['bookings']['enquiries']['priority'] ) ? $sbumenus['bookings']['enquiries']['priority'] : 10;
			$priority_extras  = isset( $sbumenus['bookings']['extras']['priority'] ) ? $sbumenus['bookings']['extras']['priority'] : 10;
			add_action( 'init', array( 'WP_Travel_Post_Types', 'register_enquiries' ), $priority_enquiry );
			add_action( 'init', array( 'WP_Travel_Post_Types', 'register_tour_extras' ), $priority_extras );

			add_action( 'init', array( 'Wp_Travel_Taxonomies', 'init' ) );

			add_action( 'init', 'wptravel_book_now', 99 );
			add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
			add_action( 'wp_enqueue_scripts', array( 'WP_Travel_Assets', 'frontend' ) );
			add_action( 'wp_head', array( 'WP_Travel_Assets', 'styles_filter' ), 7 ); // @since 4.0.6
			add_action( 'wp_footer', array( 'WP_Travel_Assets', 'scripts_filter' ), 11 ); // @since 4.0.6
			if ( $this->is_request( 'admin' ) ) {
				add_action( 'admin_enqueue_scripts', array( 'WP_Travel_Assets', 'admin' ) );

				// To delete transient.
				add_action( 'admin_init', 'wptravel_admin_init' ); // @since 1.0.7
				// add_action( 'admin_menu', 'wp_travel_marketplace_menu');

				$this->tabs     = new WP_Travel_Admin_Tabs();
				$this->uploader = new WP_Travel_Admin_Uploader();

				add_action( 'current_screen', array( $this, 'conditional_includes' ) );
			}
			$this->session = new WP_Travel_Session();
			$this->notices = new WP_Travel_Notices();
			$this->coupon  = new WP_Travel_Coupon();

			// For Network.
			add_action( 'network_admin_menu', array( $this, 'wp_travel_network_menu' ) );
			/**
			 * To resolve the pages mismatch issue when using WPML.
			 *
			 * @since 3.1.8
			 */
			add_filter( 'wp_travel_wpml_object_id', array( $this, 'get_wp_travel_page_id_by_locale' ), 11, 2 );

			/**
			 * To resolve the pages mismatch issue when using WPML.
			 *
			 * @since 3.1.8
			 */
			add_filter( 'option_wp_travel_settings', array( $this, 'filter_wp_travel_settings' ), 11, 2 );
			self::reject_cache_in_checkout();
		}

		/**
		 * To resolve the pages mismatch issue when using WPML.
		 *
		 * @since 3.1.8
		 */
		public function filter_wp_travel_settings( $value, $option ) {
			$settings_keys = array(
				'cart_page_id',
				'checkout_page_id',
				'dashboard_page_id',
				'thank_you_page_id',
			);

			foreach ( $settings_keys as $skey ) {
				if ( isset( $value[ $skey ] ) ) {
					$page_id        = apply_filters( 'wptravel_wpml_object_id', (int) $value[ $skey ], $skey, true );
					$value[ $skey ] = $page_id;
				}
			}

			return $value;
		}

		/**
		 * To resolve the pages mismatch issue when using WPML.
		 *
		 * @since 3.1.8
		 */
		public function get_wp_travel_page_id_by_locale( $page_id, $option ) {
			$_page_id = apply_filters( 'wpml_object_id', $page_id, 'page', true );
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
				$_page_id = get_option( "wp_travel_{$option}_" . ICL_LANGUAGE_CODE, $_page_id );
			}
			return $_page_id;
		}

		public function wptravel_network_menu() {
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
		private function init_shortcodes() {
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
		private function init_sidebars() {
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
			include sprintf( '%s/inc/class-wp-travel-emails.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/payments/wp-travel-payments.php', dirname( __FILE__ ) );
			include sprintf( '%s/inc/class-install.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/currencies.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/countries.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/booking-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/post-duplicator.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/form-fields.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/trip-enquiries.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-itinerary.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/helpers.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/deprecated-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-session.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-notices.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/template-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-addons-settings.php', WP_TRAVEL_ABSPATH ); // @since 3.0.1

			include sprintf( '%s/inc/coupon/wp-travel-coupon.php', WP_TRAVEL_ABSPATH );

			include_once sprintf( '%s/inc/gateways/standard-paypal/class-wp-travel-gateway-paypal-request.php', WP_TRAVEL_ABSPATH );
			include_once sprintf( '%s/inc/gateways/standard-paypal/paypal-functions.php', WP_TRAVEL_ABSPATH );
			include_once sprintf( '%s/inc/gateways/bank-deposit/bank-deposit.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/email-template-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-wp-travel-email.php', WP_TRAVEL_ABSPATH );
			// Open Graph Tags @since 1.7.6
			include sprintf( '%s/inc/og-tags.php', WP_TRAVEL_ABSPATH );

			include sprintf( '%s/inc/class-ajax.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-post-types.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-post-status.php', WP_TRAVEL_ABSPATH );
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
			 *
			 * @since 1.2.6
			 */
			include sprintf( '%s/inc/class-wp-travel-query.php', WP_TRAVEL_ABSPATH );

			// User Modules.
			include sprintf( '%s/inc/wp-travel-user-functions.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-wp-travel-user-account.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/class-wp-travel-form-handler.php', WP_TRAVEL_ABSPATH );

			// Pointers Class Includes.
			include sprintf( '%s/inc/admin/class-admin-pointers.php', WP_TRAVEL_ABSPATH );

			// Include Sidebars Class.
			include sprintf( '%s/inc/class-sidebars.php', WP_TRAVEL_ABSPATH );
			/**
			 * Include Cart and Checkout Classes.
			 *
			 * @since 1.2.3
			 */
			include sprintf( '%s/inc/cart/class-cart.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/cart/class-checkout.php', WP_TRAVEL_ABSPATH );
			include sprintf( '%s/inc/cron/class-wp-travel-cron.php', WP_TRAVEL_ABSPATH );

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
				include sprintf( '%s/inc/admin/class-admin-booking.php', WP_TRAVEL_ABSPATH );
			}

			if ( $this->is_request( 'frontend' ) ) {

				include sprintf( '%s/inc/class-wp-travel-extras-frontend.php', WP_TRAVEL_ABSPATH );
			}

			// Additional.
			require WP_TRAVEL_ABSPATH . '/core/helpers/response_codes.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/error_codes.php';

			// Actions.
			require WP_TRAVEL_ABSPATH . '/core/actions/register_taxonomies.php';
			require WP_TRAVEL_ABSPATH . '/core/actions/activation.php';

			// Libraries.
			require WP_TRAVEL_ABSPATH . '/core/lib/cart.php';

			// Helpers.
			require WP_TRAVEL_ABSPATH . '/core/helpers/settings.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/modules.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/media.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trip-pricing-categories-taxonomy.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trip-extras.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trip-dates.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trip-excluded-dates-times.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/pricings.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trip-pricing-categories.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/trips.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/cart.php';
			require WP_TRAVEL_ABSPATH . '/core/helpers/rest-api.php';

			// Ajax.
			require WP_TRAVEL_ABSPATH . '/core/ajax/settings.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trip-pricing-categories-taxonomy.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trip-extras.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trip-pricing-categories.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trip-dates.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trip-excluded-dates-times.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/pricings.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/cart.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/coupon.php';
			require WP_TRAVEL_ABSPATH . '/core/ajax/trips.php';

			// Assets.
			// include WP_TRAVEL_ABSPATH.'/core/assets/frontend.php';
			// include WP_TRAVEL_ABSPATH.'/core/assets/admin.php';
			// include WP_TRAVEL_ABSPATH.'/core/localize/admin.php';

			// Views
			// include WP_TRAVEL_ABSPATH.'/core/views/admin/trip-metabox.php';

			/**
			 * App Part
			 */

			// Front End.
			require WP_TRAVEL_ABSPATH . '/app/inc/admin/metabox-trip-edit.php';
			require WP_TRAVEL_ABSPATH . '/app/inc/admin/assets.php';
			require WP_TRAVEL_ABSPATH . '/app/inc/admin/localize.php';

			// Front End
			require WP_TRAVEL_ABSPATH . '/app/inc/frontend/single-itinerary-hooks.php';
			require WP_TRAVEL_ABSPATH . '/app/inc/frontend/assets.php';

		}

		/**
		 * Include admin files conditionally.
		 */
		public function conditional_includes() {
			if ( ! get_current_screen() ) {
				return;
			}
			$screen = get_current_screen();
			switch ( $screen->id ) {
				case 'options-permalink':
					include sprintf( '%s/inc/admin/class-admin-permalink-settings.php', WP_TRAVEL_ABSPATH );
					break;
				case 'plugins':
				case 'plugins-network':
					include sprintf( '%s/inc/admin/class-admin-plugin-screen-updates.php', WP_TRAVEL_ABSPATH );
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
		function wptravel_activation( $network_enabled ) {
			// Check for PHP Compatibility
			global $wp_version;
			$min_php_ver = '5.3.29';
			if ( version_compare( PHP_VERSION, $min_php_ver, '<' ) ) {

				$flag    = __( 'PHP', 'wp-travel' );
				$version = sprintf( __( '%s or higher', 'wp-travel' ), $min_php_ver );
				deactivate_plugins( basename( __FILE__ ) );
				$message = sprintf( __( 'WP Travel plugin requires %1$s version %2$s to work.', 'wp-travel' ), $flag, $version );
				wp_die(
					$message,
					__( 'Plugin Activation Error', 'wp-travel' ),
					array(
						'response'  => 200,
						'back_link' => true,
					)
				);
			}

			// Flush Rewrite rule.
			WP_Travel_Post_Types::init();
			Wp_Travel_Taxonomies::init();
			flush_rewrite_rules();

			/**
			 * Insert cart and checkout pages
			 *
			 * @since 1.2.3
			 */
			include_once sprintf( '%s/inc/admin/admin-helper.php', WP_TRAVEL_ABSPATH );

			$pages = apply_filters(
				'wp_travel_create_pages',
				array(
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
				wptravel_create_page( esc_sql( $page['name'] ), 'wp_travel_' . $key . '_page_id', $page['title'], $page['content'], ! empty( $page['parent'] ) ? wptravel_get_page_id( $page['parent'] ) : '' );
			}

			$migrations = array(
				array( 'name' => '103-104', 'version' => '1.0.3' ), // 'name' => 'name of file', 'version' => 'Migrate if current version is greater than this'.
				array( 'name' => '104-105', 'version' => '1.0.4' ),
				array( 'name' => 'update-121', 'version' => '1.2.0' ),
				array( 'name' => '175-176', 'version' => '1.7.5' ),
				array( 'name' => '193-194', 'version' => '1.9.3' ),
				array( 'name' => '303-304', 'version' => '3.0.3' ),
				array( 'name' => '322-323', 'version' => '3.2.2' ),
				array( 'name' => '400', 'version' => '4.0.0' ),
				array( 'name' => '404', 'version' => '4.0.4' ),
			);
			self::migration_includes( $migrations );

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
			 *
			 * @since 1.3.7
			 */
			self::create_roles();
		}

		/**
		 * Include all Migration files
		 *
		 * @param array $files List of migration files.
		 * @since WP Travel 4.4.0
		 * @return void
		 */
		public static function migration_includes( $files ) {

			$current_db_version = get_option( 'wp_travel_version' );
			if ( empty( $current_db_version ) ) {
				return; // No need to run migration in case of new user
			}

			$include_path = sprintf( '%s/upgrade', WP_TRAVEL_ABSPATH );
			foreach ( $files as $file ) {
				if ( version_compare( WP_TRAVEL_VERSION, $file['version'], '>' ) ) {
					include_once sprintf( '%s/%s.php', $include_path, $file['name'] );
				}
			}
		}

		function wptravel_setup_environment() {
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
			$image_size = apply_filters(
				'wp_travel_image_size',
				array(
					'width'  => 365,
					'height' => 215,
				)
			);
			$width      = $image_size['width'];
			$height     = $image_size['height'];
			add_image_size( 'wp_travel_thumbnail', $width, $height, true );
		}

		function wptravel_plugin_load_first_order() {
			$path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
			if ( $plugins = get_option( 'active_plugins' ) ) {
				if ( $key = array_search( $path, $plugins ) ) {
					array_splice( $plugins, $key, 1 );
					array_unshift( $plugins, $path );
					update_option( 'active_plugins', $plugins );
				}
			}
		}

		/**
		 * Return if the page is WP Travel Page.
		 *
		 * @param string  $slug       page slug.
		 * @param boolean $admin_page check if page is admin page.
		 *
		 * @since WP Travel 4.4.2
		 * @return boolean
		 */
		public static function is_page( $slug, $admin_page = false ) {

			if ( $admin_page ) {
				$screen  = get_current_screen();
				switch ( $slug ) {
					case 'settings':
						$pages = array( 'itinerary-booking_page_settings', 'itinerary-booking_page_settings2' );
							return in_array( $screen->id, $pages );
					break;
				}
				return;
			} else {
				global $post;
				$page_id  = (int) get_the_ID();
				$settings = wptravel_get_settings();

				switch ( $slug ) {
					case 'cart':
						$cart_page_id = isset( $settings['cart_page_id'] ) ? (int) $settings['cart_page_id'] : 0;
						return (int) $cart_page_id === $page_id;
					break;
					case 'checkout':
						$checkout_page_id = isset( $settings['checkout_page_id'] ) ? (int) $settings['checkout_page_id'] : 0;
						return (int) $checkout_page_id === $page_id;
					break;
					case 'dashboard':
						$dashboard_page_id = isset( $settings['dashboard_page_id'] ) ? (int) $settings['dashboard_page_id'] : 0;
						return ( (int) $dashboard_page_id === $page_id || wptravel_post_content_has_shortcode( 'wp_travel_user_account' ) || apply_filters( 'wp_travel_is_account_page', false ) );
					break;
					case 'archive':
						return ( is_post_type_archive( WP_TRAVEL_POST_TYPE ) || is_tax( array( 'itinerary_types', 'travel_locations', 'travel_keywords', 'activity' ) ) ) && ! is_search();
					break;
				}
				return;
			}
			return;
		}

		/**
		 * Create WP Travel nonce in case of any request.
		 *
		 * @since WP Travel 4.4.7
		 * @return boolean
		 */
		public static function create_nonce() {
			// Use _nonce as input name.
			return wp_create_nonce( 'wp_travel_nonce' );
		}

		/**
		 * Verify WP Travel nonce in case of any request.
		 *
		 * @since WP Travel 4.4.7
		 * @return boolean
		 */
		public static function verify_nonce( $return_bool = false ) {
			/**
			 * Nonce Verification.
			 */
			if ( ! isset( $_REQUEST['_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_nonce'] ) ), 'wp_travel_nonce' ) ) {
				if ( $return_bool ) {
					return false;
				}
				$error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
				return WP_Travel_Helpers_REST_API::response( $error );
			}
			return true;
		}

		/**
		 * Get WP Travel request.
		 *
		 * @since WP Travel 4.4.7
		 * @return boolean
		 */
		public static function get_sanitize_request( $method = 'get' ) {
			if ( ! self::verify_nonce( true ) ) { // verify nonce.
				return array();
			}
			$data = array();
			switch ( $method ) {
				case 'post':
					$data = wptravel_sanitize_array( ( $_POST ) ); // @phpcs:ignore
					break;
				case 'request':
					$data = wptravel_sanitize_array( ( $_REQUEST ) ); // @phpcs:ignore
					break;
				default:
					$data = wptravel_sanitize_array( ( $_GET ) ); // @phpcs:ignore
					break;
			}
			return $data;
		}

		/**
		 * To disable cache and never cache cookies in WP Travel Checkout page. Setting checkout uri to exclude page in cache plugin.
		 *
		 * @return void
		 */
		public static function reject_cache_in_checkout() {

			$active_plugins   = get_option( 'active_plugins' );
			$settings         = wptravel_get_settings();
			$checkout_page_id = ! empty( $settings['checkout_page_id'] ) ? ( $settings['checkout_page_id'] ) : '';
			$slug             = array(
				'checkout' => get_post_field( 'post_name', $checkout_page_id ),
				'cart'     => 'wp_travel_cart',
			);
			$support_plugins  = array(
				'wp_rocket' => 'wp-rocket/wp-rocket.php', // plugin-folder/plugin-file.php.
			);

			$support_plugins = apply_filters( 'wp_travel_reject_checkout_cache_plugin', $support_plugins );

			// For WP Rocket Plugin.
			if ( in_array( $support_plugins['wp_rocket'], $active_plugins, true ) ) {
				$options = get_option( 'wp_rocket_settings' );

				// For checkout page.
				if ( ! in_array( '/' . $slug['checkout'] . '/', $options['cache_reject_uri'], true ) ) {
					$options['cache_reject_uri'][] = '/' . $slug['checkout'] . '/';
					update_option( 'wp_rocket_settings', $options );
				}
				// For cart page in cookies.
				if ( ! in_array( $slug['cart'], $options['cache_reject_cookies'], true ) ) {
					$options['cache_reject_cookies'][] = $slug['cart'];
					update_option( 'wp_rocket_settings', $options );
				}
			}

			do_action( 'wp_travel_reject_checkout_cache_plugin_action', $support_plugins ); // @since 4.4.4
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
function WPTravel() {
	return WP_Travel::instance();
}

// Start WP Travel.
WPTravel();
