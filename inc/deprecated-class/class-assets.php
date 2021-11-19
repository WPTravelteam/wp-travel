<?php
/**
 * For assets on WP Travel.
 *
 * @package WP_Travel
 */

use WpTravel\deprecated\WpTravelDeprecatedClassTrait;

if ( ! class_exists( 'WP_Travel_Assets' ) ) {
	/**
	 * WP Travel install class.
	 */
	class WP_Travel_Assets extends WpTravel_Assets { // @phpcs:ignore
		use WpTravelDeprecatedClassTrait;
		/**
		 * Assets path.
		 *
		 * @var string
		 */
		private static $assets_path;

		/**
		 * Frontend assets.
		 */
		public static function frontend() {
			self::deprecated_class( '4.4.7', 'WpTravel_Assets' );
			parent::frontend();
		}

		/**
		 * Admin assets.
		 */
		public static function admin() {
			self::deprecated_class( '4.4.7', 'WpTravel_Assets' );
			parent::admin();
		}

		/**
		 * Registered Scripts to enqueue.
		 *
		 * @since 2.0.7
		 */
		public static function register_scripts() {
			self::deprecated_class( '4.4.7', 'WpTravel_Assets' );
			parent::register_scripts();
		}

	}
}
