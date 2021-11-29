<?php
/**
 * For assets on WP Travel.
 *
 * @package WP_Travel
 */

if ( ! class_exists( 'WpTravel_Assets' ) ) {
	/**
	 * WP Travel install class.
	 */
	class WpTravel_Assets {
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

		}

		/**
		 * Admin assets.
		 */
		public static function admin() {

		}





		/**
		 * Styles filter.
		 *
		 * @return void
		 */
		public static function styles_filter() {
			$load_optimized_scripts = wptravel_can_load_bundled_scripts();
			if ( ! $load_optimized_scripts ) {
				return;
			}

			wp_enqueue_style( 'wp-travel-frontend-bundle' );
			wp_enqueue_style( 'wp-travel-pro-bundle' );

			global $wp_styles;

			$items_in_frontend_bundle = array(
				'wp-travel-frontend',
				'wp-travel-popup',
				'easy-responsive-tabs',
				// 'wp-travel-itineraries',
				'wp-travel-user-css',
				'jquery-datepicker',
				'wp-travel-slick',
				// Bundled in wp-travel-frontend-bundle.
				'font-awesome-css',
				'wp-travel-fa-css',
			);

			$queued_styles = array_keys( $wp_styles->registered );

			$items_in_pro_bundle  = apply_filters(
				'wp-travel-pro-bundle-items', // phpcs:ignore
				array(
					'scripts' => array(),
					'styles'  => array(),
				)
			);
			$items_in_pro_bundle  = apply_filters(
				'wptravel_pro_bundle_items',
				array(
					'scripts' => array(),
					'styles'  => array(),
				)
			);
			$styles_in_pro_bundle = $items_in_pro_bundle['styles'];

			$all_styles = array_merge( $items_in_frontend_bundle, $styles_in_pro_bundle );

			$wpt_enqueued_styles = array_intersect( $all_styles, $queued_styles );

			if ( count( array_intersect( array( 'font-awesome-css', 'wp-travel-fa-css' ), $wpt_enqueued_styles ) ) > 0 ) {
				wp_enqueue_style( 'wp-travel-fonts-bundle' );
			}

			foreach ( $wpt_enqueued_styles as $handle ) {
				wp_deregister_style( $handle );
			}

		}

		/**
		 * Filters and Loads Bundled Scripts.
		 *
		 * @since 4.0.6
		 */
		public static function scripts_filter() {
			$load_optimized_scripts = wptravel_can_load_bundled_scripts();
			if ( ! $load_optimized_scripts ) {
				return;
			}

			$wp_travel_handles = array(
				'jquery-datepicker-lib',
				'jquery-datepicker-lib-eng',
				'wp-travel-moment',
				'jquery-parsley',
				'wp-travel-widget-scripts',
				'wp-travel-accordion',
				'wptravel-hc-sticky',
				'wp-travel-popup',
				'easy-responsive-tabs',
				'collapse-js',
				'wp-travel-slick',
				'wp-travel-isotope',
				'wp-travel-script',
				'wp-travel-cart',
				'wp-travel-view-mode',
				'wp-travel-payment-frontend-script',
				'wp-travel-booking',
				'wp-travel-lib-bundle',
				'wp-travel-frontend-bundle',
				'jquery-isotope-pkgd-js',
			);

			$items_in_frontend_bundle = array(
				'jquery-datepicker-lib',
				'wp-travel-popup',
				'wp-travel-slick',
				'wp-travel-moment',
				'jquery-parsley',
				'wp-travel-accordion',
				'wptravel-hc-sticky',
				'collapse-js',
				'easy-responsive-tabs',
				'wp-travel-isotope',
				'wp-travel-widget-scripts',
				'wp-travel-booking',
				'wp-travel-script',
				'wp-travel-cart',
				'wp-travel-view-mode',
			);

			global $wp_scripts;
			$queued_scripts   = $wp_scripts->queue;
			$register_scripts = $wp_scripts->registered;

			$wp_travel_addon_handles = apply_filters( 'wp-travel-script-handles', array() ); // phpcs:ignore
			$wp_travel_addon_handles = apply_filters( 'wptravel_script_handles', $wp_travel_addon_handles );
			$items_in_pro_bundle     = apply_filters(
				'wp-travel-pro-bundle-items', // phpcs:ignore
				array(
					'scripts' => array(),
					'styles'  => array(),
				)
			);

			$items_in_pro_bundle = apply_filters( 'wptravel_pro_bundle_items', $items_in_pro_bundle );

			$scripts_in_pro_bundle = $items_in_pro_bundle['scripts'];

			$all_handles = array_unique( array_merge( $items_in_frontend_bundle, $wp_travel_addon_handles, $scripts_in_pro_bundle ) );

			$wpt_enqueued_scripts = array_intersect( $queued_scripts, $all_handles );

			if ( count( $wpt_enqueued_scripts ) < 0 ) {
				wp_enqueue_script( 'wp-travel-frontend-bundle' );
			}

			if ( count( array_intersect( $wpt_enqueued_scripts, $scripts_in_pro_bundle ) ) > 0 ) {
				wp_enqueue_script( 'wp-travel-pro-bundle' );
			}

			foreach ( $wpt_enqueued_scripts as $key => $handle ) {
				if ( in_array( $handle, $items_in_frontend_bundle, true ) || in_array( $handle, $scripts_in_pro_bundle, true ) ) {
					wp_dequeue_script( $handle );
					unset( $wpt_enqueued_scripts[ $key ] );
					continue;
				}

				$registered = $register_scripts[ $handle ];

				$new_deps = $registered->deps;
				foreach ( $registered->deps as $index => $dep ) {
					if ( in_array( $dep, $items_in_frontend_bundle, true ) ) {
						unset( $new_deps[ $index ] );
					}
				}
				$wp_scripts->registered[ $addon_handle ]->deps = $new_deps;
			}
		}
	}
}
