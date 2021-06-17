<?php
/**
 * Frontend assets file.
 *
 * @package WP Travel.
 */

/**
 * WpTravel_Frontend_Assets class.
 */
class WpTravel_Frontend_Assets {
	/**
	 * Url Upto plugin dir.
	 *
	 * @var string
	 */
	private static $plugin_path;

	/**
	 * Url Upto plugin app dir.
	 *
	 * @var string
	 */
	private static $app_path;

	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		self::$plugin_path = untrailingslashit( plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) );
		self::$app_path    = untrailingslashit( sprintf( '%s/%s', self::$plugin_path, 'app' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'assets' ) );
	}

	/**
	 * Assets enqueue.
	 *
	 * @return void
	 */
	public static function assets() {
		self::register_scripts();
		$all_localized = self::get_localized_data();
		$wp_travel     = isset( $all_localized['wp_travel'] ) ? $all_localized['wp_travel'] : array(); // localized data for WP Travel below V 4.0.

		$settings     = wptravel_get_settings();
		$switch_to_v4 = $settings['wp_travel_switch_to_react'];

		if ( ! wptravel_can_load_bundled_scripts() ) {
			wp_enqueue_style( 'wp-travel-frontend' );
			// Need to load fontawesome and wp-travel-fa css after frontend.
			wp_enqueue_style( 'font-awesome-css' );
			wp_enqueue_style( 'wp-travel-fa-css' );

			if ( WP_Travel::is_pages() ) {
				// Styles.
				wp_enqueue_style( 'wp-travel-single-itineraries' ); // For new layout.
				wp_enqueue_style( 'wp-travel-popup' );
				wp_enqueue_style( 'easy-responsive-tabs' );
				// wp_enqueue_style( 'wp-travel-itineraries' );
				// fontawesome.
				wp_enqueue_style( 'wp-travel-user-css' );

				// Scripts.
				wp_enqueue_script( 'wp-travel-view-mode' );
				wp_enqueue_script( 'wp-travel-accordion' );

				wp_enqueue_script( 'wp-travel-accordion' );
				wp_enqueue_script( 'wp-travel-booking' );
				wp_enqueue_script( 'moment' );
				wp_enqueue_script( 'wp-travel-popup' );
				wp_enqueue_script( 'wp-travel-script' );
				wp_enqueue_script( 'easy-responsive-tabs' );
				wp_enqueue_script( 'collapse-js' );
				wp_enqueue_script( 'wp-travel-cart' );

				if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
					// Parsley For Frontend Single Trips.
					wp_enqueue_script( 'jquery-parsley' );
				}

				// for GMAP.
				$api_key         = '';
				$get_maps        = wptravel_get_maps();
				$current_map     = $get_maps['selected'];
				$show_google_map = ( 'google-map' === $current_map ) ? true : false;
				$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map ); // phpcs:ignore
				$show_google_map = apply_filters( 'wptravel_load_google_maps_api', $show_google_map );
				if ( isset( $settings['google_map_api_key'] ) && '' !== $settings['google_map_api_key'] ) {
					$api_key = $settings['google_map_api_key'];
				}
				if ( '' !== $api_key && true === $show_google_map ) {
					wp_enqueue_script( 'wp-travel-maps' );
				}
			}

			/**
			 * Assets needed on WP Travel Archive Page.
			 *
			 * @since 4.0.4
			 */
			if ( WP_Travel::is_page( 'archive' ) ) {
				wp_enqueue_script( 'wp-travel-view-mode' );
			}

			if ( WP_Travel::is_page( 'checkout' ) ) { // Assets needed for Checkout page.
				wp_enqueue_script( 'wp-travel-modernizer' );
				wp_enqueue_script( 'wp-travel-sticky-kit' );
			}
		} else {
			if ( WP_Travel::is_pages() ) {
				wp_localize_script( 'wp-travel-frontend-bundle', 'wp_travel', $wp_travel );
				wp_enqueue_script( 'wp-travel-frontend-bundle' );
			}
		}

		// Load if payment is enabled.
		if ( wptravel_can_load_payment_scripts() ) {
			wp_enqueue_script( 'wp-travel-payment-frontend-script' );
		}

		if ( is_singular( 'itineraries' ) ) {
			// Localize the script with new data.
			if ( 'yes' === $switch_to_v4 ) {
				$_wp_travel = isset( $all_localized['_wp_travel'] ) ? $all_localized['_wp_travel'] : array();
				wp_localize_script( 'wp-travel-frontend-booking-widget', '_wp_travel', $_wp_travel );
				wp_enqueue_script( 'wp-travel-frontend-booking-widget' );
				wp_enqueue_style( 'wp-travel-frontend-booking-widget-style' );
			}
		}

		// Styles for all Pages.
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jquery-datepicker-lib' );

		// Scripts for all .
		wp_localize_script( 'jquery-datepicker-lib', 'wp_travel', $wp_travel );

		wp_enqueue_script( 'wp-travel-widget-scripts' ); // Need to enqueue in all pages to work enquiry widget in WP Page and posts as well.
		wp_enqueue_script( 'jquery-datepicker-lib' );
		wp_enqueue_script( 'jquery-datepicker-lib-eng' );
	}

	/**
	 * Registered Scripts to enqueue.
	 *
	 * @since 4.6.4
	 */
	public static function register_scripts() {
		$suffix           = wptravel_script_suffix();
		$all_dependencies = self::get_block_dependencies(); // Dependency & version for Block JS.
		$settings         = wptravel_get_settings();

		// Getting Locale to fetch Localized calender js.
		$lang_code            = explode( '-', get_bloginfo( 'language' ) );
		$locale               = $lang_code[0];
		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%s/assets/js/lib/datepicker/i18n/', self::$app_path );

		$wp_content_file_url = WP_CONTENT_URL . '/languages/wp-travel/datepicker/';
		$default_url         = sprintf( '%s/assets/js/lib/datepicker/i18n/', self::$app_path );

		$filename = 'datepicker.' . $locale . '.js';

		if ( file_exists( trailingslashit( $wp_content_file_path ) . $filename ) ) {
			$datepicker_i18n_file = trailingslashit( $wp_content_file_url ) . $filename;
		} elseif ( file_exists( trailingslashit( $default_path ) . $filename ) ) {
			$datepicker_i18n_file = $default_url . $filename;
		} else {
			$datepicker_i18n_file = $default_url . 'datepicker.en.js';
		}
		// End of Getting Locale to fetch Localized calender js.

		// General Libraries.
		$scripts = array(
			'jquery-datepicker-lib'     => array(
				'src'       => self::$app_path . '/assets/js/lib/datepicker/datepicker.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'jquery-datepicker-lib-eng' => array(
				'src'       => $datepicker_i18n_file,
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'jquery-parsley'            => array(
				'src'       => self::$app_path . '/assets/js/lib/parsley/parsley.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => false,
			),
			'wp-travel-modernizer'      => array(
				'src'       => self::$app_path . '/assets/js/lib/modernizer/modernizr.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-sticky-kit'      => array(
				'src'       => self::$app_path . '/assets/js/lib/sticky-kit/sticky-kit.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-popup'           => array(
				'src'       => self::$app_path . '/assets/js/lib/jquery.magnific-popup/jquery.magnific-popup.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'easy-responsive-tabs'      => array(
				'src'       => self::$app_path . '/assets/js/lib/easy-responsive-tabs/easy-responsive-tabs.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-slick'           => array(
				'src'       => self::$app_path . '/assets/js/lib/slick/slick.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'select2-js'           => array(
				'src'       => self::$app_path . '/assets/js/lib/select2/select2.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-isotope'         => array( // added since @3.1.7.
				'src'       => self::$app_path . '/assets/js/lib/isotope/isotope.pkgd.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),

			'collapse-js'               => array(
				'src'       => self::$app_path . '/assets/js/collapse' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-widget-scripts'  => array(
				'src'       => self::$app_path . '/assets/js/wp-travel-widgets' . $suffix . '.js',
				'deps'      => array( 'jquery', 'jquery-ui-slider', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-accordion'       => array(
				'src'       => self::$app_path . '/assets/js/wp-travel-accordion' . $suffix . '.js',
				'deps'      => array( 'jquery', 'jquery-ui-accordion' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
		);

		$styles = array(
			'wp-travel-slick'           => array(
				'src'   => self::$app_path . '/assets/css/lib/slick/slick.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'wp-travel-popup'           => array(
				'src'   => self::$app_path . '/assets/css/lib/magnific-popup/magnific-popup.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'easy-responsive-tabs'      => array(
				'src'   => self::$app_path . '/assets/css/lib/easy-responsive-tabs/easy-responsive-tabs.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'font-awesome-css'          => array(
				'src'   => self::$app_path . '/assets/css/lib/font-awesome/css/fontawesome-all.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'wp-travel-fa-css'          => array(
				'src'   => self::$app_path . '/assets/css/lib/font-awesome/css/wp-travel-fa-icons.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'jquery-datepicker-lib'     => array(
				'src'   => self::$app_path . '/assets/css/lib/datepicker/datepicker.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'wp-travel-fonts-bundle'    => array(
				'src'   => self::$app_path . '/assets/css/lib/font-awesome/css/wp-travel-fonts.bundle.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'select2-style'             => array(
				'src'   => self::$app_path . '/assets/css/lib/select2/select2.min.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'wp-travel-frontend'        => array(
				'src'   => self::$app_path . '/build/wp-travel-front-end' . $suffix . '.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),

			'wp-travel-frontend-bundle' => array(
				'src'   => self::$app_path . '/build/wp-travel-frontend.bundle.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
		);

		// for GMAP.
		$api_key = '';

		$get_maps    = wptravel_get_maps();
		$current_map = $get_maps['selected'];

		$show_google_map = ( 'google-map' === $current_map ) ? true : false;
		$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map ); // phpcs:ignore
		$show_google_map = apply_filters( 'wptravel_load_google_maps_api', $show_google_map );

		if ( isset( $settings['google_map_api_key'] ) && '' !== $settings['google_map_api_key'] ) {
			$api_key = $settings['google_map_api_key'];
		}

		if ( '' !== $api_key && true === $show_google_map ) {

			$scripts['google-map-api'] = array(
				'src'       => 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key,
				'deps'      => array(),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['jquery-gmaps']   = array(
				'src'       => self::$app_path . '/assets/js/lib/gmaps/gmaps.min.js',
				'deps'      => array( 'jquery', 'google-map-api' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
		}

		// Frontend Specific.
		if ( self::is_request( 'frontend' ) ) {
			$scripts['wp-travel-script'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-front-end.js',
				'deps'      => array( 'jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-cart']   = array(
				'src'       => self::$app_path . '/assets/js/cart.js',
				'deps'      => array( 'jquery', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			$scripts['wp-travel-view-mode'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-view-mode' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			$scripts['wp-travel-payment-frontend-script'] = array(
				'src'       => self::$app_path . '/assets/js/payment' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-booking']                 = array(
				'src'       => self::$app_path . '/assets/js/booking' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-frontend-bundle']         = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-frontend.bundle.js',
				'deps'      => array(
					'jquery',
					'jquery-ui-accordion',
					'jquery-datepicker-lib-eng',
					'jquery-ui-slider',
				),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-maps']                    = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-front-end-map.js',
				'deps'      => array( 'jquery', 'jquery-gmaps' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			// Block Scripts.
			$booking_widget_deps = $all_dependencies['frontend-booking-widget'];

			$scripts['wp-travel-frontend-booking-widget'] = array(
				'src'       => self::$app_path . '/build/frontend-booking-widget' . $suffix . '.js',
				'deps'      => $booking_widget_deps['dependencies'],
				'ver'       => $booking_widget_deps['version'],
				'in_footer' => true,
			);

			// Block Styles.
			$styles['wp-travel-frontend-booking-widget-style'] = array(
				'src'   => self::$app_path . '/build/frontend-booking-widget' . $suffix . '.css',
				'deps'  => array(),
				'ver'   => $booking_widget_deps['version'],
				'media' => 'all',
			);

		}

		// Admin Specific.
		if ( self::is_request( 'admin' ) ) {

			// Main Styles for all admin pages.
			$styles['wp-travel-back-end'] = array(
				'src'   => self::$app_path . '/build/wp-travel-back-end' . $suffix . '.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			);

			// Required Scripts for all admin pages.
			$scripts['wp-travel-fields-scripts'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-fields-scripts' . $suffix . '.js',
				'deps'      => array( 'select2-js' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-tabs']           = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-tabs' . $suffix . '.js',
				'deps'      => array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'wp-color-picker', 'select2-js', 'jquery-ui-accordion' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['jquery-chart']             = array(
				'src'       => self::$app_path . '/assets/js/lib/chartjs/Chart.bundle.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['jquery-chart-custom']      = array(
				'src'       => self::$app_path . '/assets/js/lib/chartjs/chart-custom.js',
				'deps'      => array( 'jquery', 'jquery-chart', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wptravel-uploader']        = array(
				'src'       => self::$app_path . '/assets/js/jquery.wptraveluploader' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-media-upload']   = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-media-upload' . $suffix . '.js',
				'deps'      => array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			$admin_depencency = array( 'jquery', 'jquery-ui-tabs', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'wp-travel-media-upload', 'jquery-ui-sortable', 'jquery-ui-accordion', 'moment' );
			if ( '' !== $api_key && true === $show_google_map ) {
				$admin_depencency[] = 'jquery-gmaps';
			}
			$scripts['wp-travel-admin-script'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-back-end' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			// Trip Edit.
			$trip_edit_deps                               = $all_dependencies['admin-trip-options'];
			$scripts['wp-travel-admin-trip-options']      = array(
				'src'       => self::$app_path . '/build/admin-trip-options' . $suffix . '.js',
				'deps'      => $trip_edit_deps['dependencies'],
				'ver'       => $trip_edit_deps['version'],
				'in_footer' => true,
			);
			$styles['wp-travel-admin-trip-options-style'] = array(
				'src'   => self::$app_path . '/build/admin-trip-options' . $suffix . '.css',
				'deps'  => array( 'wp-components' ),
				'ver'   => $trip_edit_deps['version'],
				'media' => 'all',
			);

			// Settings.
			$trip_edit_deps                           = $all_dependencies['admin-settings'];
			$scripts['wp-travel-admin-settings']      = array(
				'src'       => self::$app_path . '/build/admin-settings' . $suffix . '.js',
				'deps'      => $trip_edit_deps['dependencies'],
				'ver'       => $trip_edit_deps['version'],
				'in_footer' => true,
			);
			$styles['wp-travel-admin-settings-style'] = array(
				'src'   => self::$app_path . '/build/admin-settings' . $suffix . '.css',
				'deps'  => array( 'wp-components', 'font-awesome-css' ),
				'ver'   => $trip_edit_deps['version'],
				'media' => 'all',
			);

		}

		// Register scripts and styles.
		$registered = array(
			'scripts' => $scripts,
			'styles'  => $styles,
		);

		$registered         = apply_filters( 'wptravel_registered_scripts', $registered );
		$registered_styles  = isset( $registered['styles'] ) ? $registered['styles'] : array();
		$registered_scripts = isset( $registered['scripts'] ) ? $registered['scripts'] : array();

		// Registered Styles.
		foreach ( $registered_styles as $handler => $script ) {
			wp_register_style( $handler, $script['src'], $script['deps'], $script['ver'], $script['media'] );
		}

		// Registered Scripts.
		foreach ( $registered_scripts as $handler => $script ) {
			wp_register_script( $handler, $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
		}
	}


	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 * @return bool
	 */
	private static function is_request( $type ) {
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
	 * Assets Dependency.
	 */
	public static function get_block_dependencies() {
		$dependenccies = array();

		// Front end booking widget.
		$booking_widget = include_once sprintf( '%sapp/build/frontend-booking-widget.asset.php', WP_TRAVEL_ABSPATH );
		if ( ! wptravel_can_load_bundled_scripts() ) {
			$booking_widget['dependencies'][] = 'jquery-datepicker-lib';
		} else {
			$booking_widget['dependencies'][] = 'wp-travel-frontend-bundle';
		}
		$dependenccies['frontend-booking-widget'] = $booking_widget;
		// End of Front end booking widget.

		// Admin Trip edit.
		$trip_edit                           = include_once sprintf( '%sapp/build/admin-trip-options.asset.php', WP_TRAVEL_ABSPATH );
		$trip_edit['dependencies'][]         = 'jquery';
		$dependenccies['admin-trip-options'] = $trip_edit;
		// End of Admin Trip edit.

		// Admin Settings.
		$trip_edit                       = include_once sprintf( '%sapp/build/admin-settings.asset.php', WP_TRAVEL_ABSPATH );
		$trip_edit['dependencies'][]     = 'jquery';
		$dependenccies['admin-settings'] = $trip_edit;
		// End of Admin Settings.

		return $dependenccies; // it will return all block dependency along with compled version.
	}

	/**
	 * Assets Dependency.
	 */
	public static function get_localized_data() {
		global $post;
		$localized_data = array();
		$settings       = wptravel_get_settings();

		// Getting Locale to fetch Localized calender js.
		$lang_code            = explode( '-', get_bloginfo( 'language' ) );
		$locale               = $lang_code[0];
		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%s/app/assets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );
		$filename             = 'datepicker.' . $locale . '.js';
		if ( ! file_exists( trailingslashit( $wp_content_file_path ) . $filename ) && ! file_exists( trailingslashit( $default_path ) . $filename ) ) {
			$locale = 'en';
		}

		$rdp_locale       = get_locale();
		$rdp_locale_array = explode( '_',  $rdp_locale );
		if ( is_array( $rdp_locale_array ) && count( $rdp_locale_array ) > 1 && strtoupper( $rdp_locale_array[0] ) === strtoupper( $rdp_locale_array[1] ) ) {
			$rdp_locale = $rdp_locale_array[0];
		}
		$rdp_locale = str_replace( '_', '', $rdp_locale );
		// Frontend Localized Strings for React block.
		if ( self::is_request( 'frontend' ) ) {
			$trip_id    = $post->ID;
			$_wp_travel = array();
			$trip       = WP_Travel_Helpers_Trips::get_trip( $trip_id );
			if ( ! is_wp_error( $trip ) && 'WP_TRAVEL_TRIP_INFO' === $trip['code'] ) {
				$_wp_travel['trip_data']          = $trip['trip'];
				$_wp_travel['currency']           = $settings['currency'];
				$_wp_travel['currency_symbol']    = wptravel_get_currency_symbol();
				$_wp_travel['cart_url']           = wptravel_get_cart_url();
				$_wp_travel['ajax_url']           = admin_url( 'admin-ajax.php' );
				$_wp_travel['rdp_locale']         = $rdp_locale;
				$_wp_travel['_nonce']             = wp_create_nonce( 'wp_travel_nonce' );
				$_wp_travel['currency_position']  = $settings['currency_position'];
				$_wp_travel['thousand_separator'] = $settings['thousand_separator'] ? $settings['thousand_separator'] : ',';
				$_wp_travel['decimal_separator']  = $settings['decimal_separator'] ? $settings['decimal_separator'] : '.';
				$_wp_travel['number_of_decimals'] = $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0;
				$_wp_travel['date_format']        = get_option( 'date_format' );
				$_wp_travel['date_format_moment'] = wptravel_php_to_moment_format( get_option( 'date_format' ) );
				$_wp_travel['time_format']        = get_option( 'time_format' );
				$_wp_travel['trip_date_listing']  = $settings['trip_date_listing'];
				$_wp_travel['build_path']         = esc_url( trailingslashit( plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build' ) );
			}
			$_wp_travel['strings']      = WpTravel_Helpers_Strings::get();
			$_wp_travel['itinerary_v2'] = wptravel_use_itinerary_v2_layout();
	
			$localized_data['_wp_travel'] = $_wp_travel;
	
			// Localized varialble for old trips less than WP Travel 4.0.
			$wp_travel = array(
				'currency_symbol'    => wptravel_get_currency_symbol(),
				'currency_position'  => $settings['currency_position'],
				'thousand_separator' => $settings['thousand_separator'],
				'decimal_separator'  => $settings['decimal_separator'],
				'number_of_decimals' => $settings['number_of_decimals'],
	
				'prices'             => wptravel_get_itinereries_prices_array(), // Used to get min and max price to use it in range slider filter widget.
				'locale'             => $locale,
				'nonce'              => wp_create_nonce( 'wp_travel_frontend_security' ),
				'_nonce'             => wp_create_nonce( 'wp_travel_nonce' ),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'strings'            => WpTravel_Helpers_Strings::get(),
				// Need map data enhancement.
				'lat'                => ! empty( $map_data['lat'] ) ? ( $map_data['lat'] ) : '',
				'lng'                => ! empty( $map_data['lng'] ) ? ( $map_data['lng'] ) : '',
				'loc'                => ! empty( $map_data['loc'] ) ? ( $map_data['loc'] ) : '',
				'zoom'               => $settings['google_map_zoom_level'],
				'cartUrl'            => wptravel_get_cart_url(),
				'checkoutUrl'        => wptravel_get_checkout_url(), // @since 4.3.2
				'isEnabledCartPage'  => WP_Travel_Helpers_Cart::is_enabled_cart_page(), // @since 4.3.2
			);
			if ( wptravel_can_load_payment_scripts() ) {
	
				global $wt_cart;
	
				$cart_amounts   = $wt_cart->get_total();
				$trip_price     = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : '';
				$payment_amount = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : '';
	
				$wp_travel['payment']['currency_code']   = $settings['currency'];
				$wp_travel['payment']['currency_symbol'] = wptravel_get_currency_symbol();
				$wp_travel['payment']['price_per']       = wptravel_get_price_per_text( $trip_id, '', true );
				$wp_travel['payment']['trip_price']      = $trip_price;
				$wp_travel['payment']['payment_amount']  = $payment_amount;
			}
			$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel, $settings ); // phpcs:ignore
			$wp_travel                   = apply_filters( 'wptravel_frontend_data', $wp_travel, $settings );
			$localized_data['wp_travel'] = $wp_travel;
		}

		if ( self::is_request( 'admin' ) ) {
			// Booking Chart Data. Need to merge in wp_travel or _wp_travel.
			$booking_data      = wptravel_get_booking_data();
			$stat_data         = isset( $booking_data['stat_data'] ) ? $booking_data['stat_data'] : array();
			$labels            = isset( $stat_data['stat_label'] ) ? $stat_data['stat_label'] : array();
			$datas             = isset( $stat_data['data'] ) ? $stat_data['data'] : array();
			$data_label        = isset( $stat_data['data_label'] ) ? $stat_data['data_label'] : array();
			$data_bg_color     = isset( $stat_data['data_bg_color'] ) ? $stat_data['data_bg_color'] : array();
			$data_border_color = isset( $stat_data['data_border_color'] ) ? $stat_data['data_border_color'] : array();

			$max_bookings  = isset( $booking_data['max_bookings'] ) ? $booking_data['max_bookings'] : 0;
			$max_pax       = isset( $booking_data['max_pax'] ) ? $booking_data['max_pax'] : 0;
			$top_countries = ( isset( $booking_data['top_countries'] ) && count( $booking_data['top_countries'] ) > 0 ) ? $booking_data['top_countries'] : array( 'N/A' );
			$top_itinerary = ( isset( $booking_data['top_itinerary'] ) && count( $booking_data['top_itinerary'] ) > 0 ) ? $booking_data['top_itinerary'] : array(
				'name' => esc_html__( 'N/A', 'wp-travel' ),
				'url'  => '',
			);

			$booking_stat_from = isset( $booking_data['booking_stat_from'] ) ? $booking_data['booking_stat_from'] : '';
			$booking_stat_to   = isset( $booking_data['booking_stat_to'] ) ? $booking_data['booking_stat_to'] : '';

			$wp_travel_stat_data = array();
			foreach ( $datas as $key => $data ) {
				$wp_travel_stat_data[] = array(
					'label'           => $data_label[ $key ],
					'backgroundColor' => $data_bg_color[ $key ],
					'borderColor'     => $data_border_color[ $key ],
					'data'            => $data,
					'fill'            => false,
				);
			}
			$wp_travel_chart_data = array(
				'ajax_url'          => 'admin-ajax.php',
				'chart_title'       => esc_html__( 'Chart Stat', 'wp-travel' ),
				'labels'            => wp_json_encode( $labels ),
				'datasets'          => wp_json_encode( $wp_travel_stat_data ),
				'max_bookings'      => $max_bookings,
				'max_pax'           => $max_pax,
				'top_countries'     => implode( ', ', $top_countries ),
				'top_itinerary'     => $top_itinerary,
				// Show more / less top countries.
				'show_more_text'    => __( 'More', 'wp-travel' ),
				'show_less_text'    => __( 'Less', 'wp-travel' ),
				'show_char'         => 18,

				'booking_stat_from' => $booking_stat_from,
				'booking_stat_to'   => $booking_stat_to,
				'compare_stat'      => false,
			);
			if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) { // phpcs:ignore
				$compare_stat_from = isset( $booking_data['compare_stat_from'] ) ? $booking_data['compare_stat_from'] : '';
				$compare_stat_to   = isset( $booking_data['compare_stat_to'] ) ? $booking_data['compare_stat_to'] : '';

				$compare_max_bookings  = isset( $booking_data['compare_max_bookings'] ) ? $booking_data['compare_max_bookings'] : 0;
				$compare_max_pax       = isset( $booking_data['compare_max_pax'] ) ? $booking_data['compare_max_pax'] : 0;
				$compare_top_countries = ( isset( $booking_data['compare_top_countries'] ) && count( $booking_data['compare_top_countries'] ) > 0 ) ? $booking_data['compare_top_countries'] : array( 'N/A' );
				$compare_top_itinerary = ( isset( $booking_data['compare_top_itinerary'] ) && count( $booking_data['compare_top_itinerary'] ) > 0 ) ? $booking_data['compare_top_itinerary'] : array(
					'name' => esc_html__( 'N/A', 'wp-travel' ),
					'url'  => '',
				);

				$wp_travel_chart_data['compare_stat_from']     = $compare_stat_from;
				$wp_travel_chart_data['compare_stat_to']       = $compare_stat_to;
				$wp_travel_chart_data['compare_max_bookings']  = $compare_max_bookings;
				$wp_travel_chart_data['compare_max_pax']       = $compare_max_pax;
				$wp_travel_chart_data['compare_top_countries'] = implode( ', ', $compare_top_countries );
				$wp_travel_chart_data['compare_top_itinerary'] = $compare_top_itinerary;
				$wp_travel_chart_data['compare_stat']          = true;
				$wp_travel_chart_data['total_sales_compare']   = $booking_data['total_sales_compare'];
			}
			$wp_travel_chart_data                   = apply_filters( 'wptravel_chart_data', $wp_travel_chart_data );
			$localized_data['wp_travel_chart_data'] = $wp_travel_chart_data;
			// End of Booking Chart Data.

			// Map & Gallery Data. Need to merge in wp_travel or _wp_travel.
			$map_data                                     = wptravel_get_map_data();
			$wp_travel_gallery_data                       = array(
				'ajax'            => admin_url( 'admin-ajax.php' ),
				'lat'             => isset( $map_data['lat'] ) ? $map_data['lat'] : '',
				'lng'             => isset( $map_data['lng'] ) ? $map_data['lng'] : '',
				'loc'             => isset( $map_data['loc'] ) ? $map_data['loc'] : '',
				'labels'          => array(
					'uploader_files_computer' => __( 'Select Files from Your Computer', 'wp-travel' ),
				),
				'drag_drop_nonce' => wp_create_nonce( 'wp-travel-drag-drop-nonce' ),
			);
			$date_format                                  = get_option( 'date_format' );
			$js_date_format                               = wptravel_date_format_php_to_js();
			$moment_date_format                           = wptravel_moment_date_format( $date_format );
			$wp_travel_gallery_data['js_date_format']     = $js_date_format;
			$wp_travel_gallery_data['moment_date_format'] = $moment_date_format;

			$wp_travel_gallery_data = apply_filters( 'wp_travel_localize_gallery_data', $wp_travel_gallery_data ); // phpcs:ignore
			$wp_travel_gallery_data = apply_filters( 'wptravel_localize_gallery_data', $wp_travel_gallery_data );
			// end of Map & Gallery Data.
			$localized_data['wp_travel_drag_drop_uploader'] = $wp_travel_gallery_data;

			// @since WP Travel 4.6.4
			$_wp_travel_admin = array(
				'strings' => WpTravel_Helpers_Strings::get(),
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'_nonce'  => wp_create_nonce( 'wp_travel_nonce' ),
			);

			$localized_data['_wp_travel_admin'] = $_wp_travel_admin;

		}

		return $localized_data;
	}
}

WpTravel_Frontend_Assets::init();
