<?php
/**
 * REST API: V1 init
 *
 * @package WP Travel API Core
 * @subpackage API Core
 * @since WP Travel 4.4.5
 */

if ( ! class_exists( 'WP_Travel_Rest_API' ) ) {
	/**
	 * Rest API V1 Class.
	 */
	class WP_Travel_Rest_API {


		/**
		 * Init singletone class.
		 */
		public static function init() {
			// Init here due to include file and endpoints may be added/removed as per version.
			self::includes();
			WP_Travel_REST_Bookings_Controller::init()->register_routes();
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 *
		 * @since WP Travel 4.4.5
		 * @return void
		 */
		public static function includes() {
			$api_version = defined( 'WP_TRAVEL_API_VERSION' ) ? WP_TRAVEL_API_VERSION : 'v2';
			include_once sprintf( '%score/REST/%s/endpoints/class-wp-travel-rest-bookings.php', WP_TRAVEL_ABSPATH, $api_version );
		}

	}
}
