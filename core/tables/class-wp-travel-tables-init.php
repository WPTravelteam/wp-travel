<?php
/**
 * REST API: WP_Travel_Tables_Init class
 *
 * @todo Use of namespace.
 * @package WP_Travel
 * @since 4.4.5
 */

if ( ! class_exists( 'WP_Travel_Tables_Init' ) ) {
	/**
	 * Init the main Rest class.
	 */
	class WP_Travel_Tables_Init {
		/**
		 * The single instance of the class.
		 *
		 * @var WP Travel
		 * @since 4.4.5
		 */
		protected static $_instance = null;

		/**
		 * Main WP_Travel_Tables_Init Instance.
		 * Ensures only one instance of WP_Travel_Tables_Init is loaded or can be loaded.
		 *
		 * @since 4.4.5
		 * @static
		 * @see WP_Travel_Tables_Init()
		 * @return WP_Travel_Tables_Init - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * WP_Travel_Tables_Init Constructor.
		 */
		public function __construct() {
			$this->includes();
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since 4.4.5
		 * @return void
		 */
		private function init_hooks() {
			add_action( 'rest_api_init', array( 'WP_Travel_Rest_API', 'init' ) );
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since 4.4.5
		 * @return void
		 */
		public function includes() {
			$api_version = defined( 'WP_TRAVEL_API_VERSION' ) ? WP_TRAVEL_API_VERSION : 'v1';
			include_once sprintf( '%s/core/REST/%s/class-wp-travel-rest-api.php', WP_TRAVEL_ABSPATH, $api_version );
		}
	}

	/**
	 * Rest Function to call.
	 */
	function wptravel_Tables_init() {
		return WP_Travel_Tables_Init::instance();
	}

	// Start WP Travel Rest API.
	wp_travel_Tables_init();
}
