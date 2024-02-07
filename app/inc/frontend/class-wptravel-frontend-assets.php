<?php
/**
 * Frontend assets file.
 *
 * @package WP_Travel
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
		$all_localized = WpTravel_Helpers_Localize::get();

		$wp_travel     = isset( $all_localized['wp_travel'] ) ? $all_localized['wp_travel'] : array(); // localized data for WP Travel below V 4.0.

		$settings     = wptravel_get_settings();
		$switch_to_v4 = wptravel_is_react_version_enabled();

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
				// wp_enqueue_script( 'wp-travel-view-mode' );
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
					wp_enqueue_script( 'jquery-parsley' ); // Maybe already enqueued from form fields.
					// wp_localize_script( 'jquery-parsley', 'error_string', [
					// 	'defaultMessage' => __( "This value seems to be invalid.", 'wp-travel' ),
					// 	'type' => [
					// 	'email' => __( "This value should be a valid email.", 'wp-travel' ),
					// 	'url' => __( "This value should be a valid url.", 'wp-travel' ),
					// 	'number' => __( "This value should be a valid number.", 'wp-travel' ),
					// 	'integer' => __( "This value should be a valid integer.", 'wp-travel' ),
					// 	'digits' => __( "This value should be digits.", 'wp-travel' ),
					// 	'alphanum' => __( "This value should be alphanumeric.", 'wp-travel' )
					// 	],
					// 	'notblank' => __( "This value should not be blank.", 'wp-travel' ),
					// 	'required' => __( "This value is required.", 'wp-travel' ),
					// 	'pattern' => __( "This value seems to be invalid.", 'wp-travel' ),
					// 	'min' => __( "This value should be greater than or equal to %s.", 'wp-travel' ),
					// 	'max' => __( "This value should be lower than or equal to %s.", 'wp-travel' ),
					// 	'range' => __( "This value should be between %s and %s.", 'wp-travel' ),
					// 	'minlength' => __( "This value is too short. It should have %s characters or more.", 'wp-travel' ),
					// 	'maxlength' => __( "This value is too long. It should have %s characters or fewer.", 'wp-travel' ),
					// 	'length' => __( "This value length is invalid. It should be between %s and %s characters long.", 'wp-travel' ),
					// 	'mincheck' => __( "You must select at least %s choices.", 'wp-travel' ),
					// 	'maxcheck' => __( "You must select %s choices or fewer.", 'wp-travel' ),
					// 	'check' => __( "You must select between %s and %s choices.", 'wp-travel' ),
					// 	'equalto' => __( "This value should be the same.", 'wp-travel' ),
					// 	'euvatin' => __( "It's not a valid VAT Identification Number.", 'wp-travel' )
					// ] );
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
			if ( WP_Travel::is_page( 'archive' ) || WP_Travel::is_page( 'search' ) ) {
				wp_enqueue_script( 'wp-travel-view-mode' );
			}

			if ( WP_Travel::is_page( 'checkout' ) ) { // Assets needed for Checkout page.
				wp_enqueue_script( 'wptravel-hc-sticky' );
			}
			if ( WP_Travel::is_page( 'single' ) ) {
				wp_enqueue_script( 'wp-travel-slick' );
				wp_enqueue_style( 'wp-travel-slick' );
			}
		} else {
			wp_localize_script( 'wp-travel-frontend-bundle', 'wp_travel', $wp_travel ); // Fix wp_travel undefined in frontpage in case of pro activate.
			if ( WP_Travel::is_pages() ) {
				wp_enqueue_script( 'wp-travel-frontend-bundle' );
			}
		}

		// Load if payment is enabled.
		if ( wptravel_can_load_payment_scripts() ) {
			wp_enqueue_script( 'wp-travel-payment-frontend-script' );
		}

		// Localize the script with new data.
		if ( $switch_to_v4 ) {
			$_wp_travel = isset( $all_localized['_wp_travel'] ) ? $all_localized['_wp_travel'] : array();
			wp_localize_script( 'wp-travel-frontend-booking-widget', '_wp_travel', $_wp_travel );
			wp_enqueue_script( 'wp-travel-frontend-booking-widget' );
			wp_enqueue_style( 'wp-travel-frontend-booking-widget-style' );
		}
	

		// Styles for all Pages.
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'jquery-datepicker-lib' );

		// Scripts for all .
		wp_localize_script( 'jquery-datepicker-lib', 'wp_travel', $wp_travel );

		wp_enqueue_script( 'wp-travel-widget-scripts' ); // Need to enqueue in all pages to work enquiry widget in WP Page and posts as well.
		wp_enqueue_script( 'jquery-datepicker-lib' );
		wp_enqueue_script( 'jquery-datepicker-lib-eng' );

		wp_localize_script( 'wp-travel-script', '_wp_travel_check_for_pro', array( 'is_enable' => class_exists('WP_Travel_Pro') ) );
		wp_localize_script( 'wp-travel-script', '_wp_travel_check_cp_by_billing', array( 'is_enable' => isset( wptravel_get_settings()['enable_CP_by_billing_address'] ) ? wptravel_get_settings()['enable_CP_by_billing_address']: '' ) );
		wp_localize_script( 'wp-travel-script', '_wp_travel_check_cp_enable', array( 'is_enable' => isset( wptravel_get_settings()['enable_conditional_payment'] ) ? wptravel_get_settings()['enable_conditional_payment']: '' ) );
		wp_localize_script( 'wp-travel-script', '_wp_travel_conditional_payment_list', isset( wptravel_get_settings()['conditional_payment_list'] ) ? wptravel_get_settings()['conditional_payment_list'] : array() );
		wp_localize_script( 'wp-travel-script', '_wp_travel_active_payment', wptravel_get_active_gateways()['active'] );
	}

	/**
	 * Registered Scripts to enqueue.
	 *
	 * @since 4.6.4
	 */
	public static function register_scripts() {
		$suffix = wptravel_script_suffix();
		// $suffix           = ''; // Temp fixes due to build issue.
		$all_dependencies = self::get_block_dependencies(); // Dependency & version for Block JS.
		$settings         = wptravel_get_settings();

		// Getting Locale to fetch Localized calender js.
		$lang_code            = explode( '-', get_bloginfo( 'language' ) );
		$locale               = $lang_code[0];
		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%s/app/assets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );

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
			'jquery-datepicker-lib'       => array(
				'src'       => self::$app_path . '/assets/js/lib/datepicker/datepicker.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'jquery-datepicker-lib-eng'   => array(
				'src'       => $datepicker_i18n_file,
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'jquery-parsley'              => array(
				'src'       => self::$app_path . '/assets/js/lib/parsley/parsley.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => false,
			),
			'wptravel-hc-sticky'          => array(
				'src'       => self::$app_path . '/assets/js/lib/hc-sticky/hc-sticky.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-popup'             => array(
				'src'       => self::$app_path . '/assets/js/lib/jquery.magnific-popup/jquery.magnific-popup.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'easy-responsive-tabs'        => array(
				'src'       => self::$app_path . '/assets/js/lib/easy-responsive-tabs/easy-responsive-tabs.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-slick'             => array(
				'src'       => self::$app_path . '/assets/js/lib/slick/slick.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'select2-js'                  => array(
				'src'       => self::$app_path . '/assets/js/lib/select2/select2.min.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-isotope'           => array( // added since @3.1.7.
				'src'       => self::$app_path . '/assets/js/lib/isotope/isotope.pkgd.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),

			'collapse-js'                 => array(
				'src'       => self::$app_path . '/assets/js/collapse' . $suffix . '.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-widget-scripts'    => array(
				'src'       => self::$app_path . '/assets/js/wp-travel-widgets' . $suffix . '.js',
				'deps'      => array( 'jquery', 'jquery-ui-slider', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-accordion'         => array(
				'src'       => self::$app_path . '/assets/js/wp-travel-accordion' . $suffix . '.js',
				'deps'      => array( 'jquery', 'jquery-ui-accordion' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),
			'wp-travel-admin-pointers-js' => array(
				'src'       => self::$app_path . '/assets/js/wp-travel-backend-pointers.js',
				'deps'      => array( 'wp-pointer' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			),

		);

		if ( self::is_request( 'admin' ) ) {
			if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
				$scripts['wp-travel-setup-page-js'] = array(
					'src'       => self::$app_path . '/build/admin-setup-page.js',
					'deps'      => array( 'wp-editor', 'jquery', 'wp-element' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);
			}
		}

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
				'src'   => self::$app_path . '/build/wp-travel-front-end.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			),
			'wp-travel-frontend-v2'     => array(
				'src'   => self::$app_path . '/build/wp-travel-front-end-v2.css',
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
			'wptravel-admin-widgets'    => array(
				'src'   => self::$app_path . '/build/wptravel-admin-widgets.css',
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
				'src'       => 'https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $api_key . '&callback=Function.prototype',
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
				'deps'      => array( 'easy-responsive-tabs', 'jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion', 'wp-travel-slick' ),
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
				'deps'      => array( 'jquery', 'wptravel-hc-sticky' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$bundle_deps                                  = array(
				'jquery',
				'jquery-ui-accordion',
				'jquery-datepicker-lib-eng',
				'jquery-ui-slider',
				'easy-responsive-tabs', // prashant
			);

			if ( '' !== $api_key && true === $show_google_map ) {
				$bundle_deps[] = 'jquery-gmaps';
			}
			$scripts['wp-travel-frontend-bundle'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-frontend.bundle.js',
				'deps'      => $bundle_deps,
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);
			$scripts['wp-travel-maps']            = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-front-end-map.js',
				'deps'      => array( 'jquery', 'jquery-gmaps' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			// Block Scripts.
			$booking_widget_deps = $all_dependencies['frontend-booking-widget'];

			$scripts['wp-travel-frontend-booking-widget'] = array(
				'src'       => self::$app_path . '/build/frontend-booking-widget.js',
				'deps'      => $booking_widget_deps['dependencies'],
				'ver'       => $booking_widget_deps['version'],
				'in_footer' => true,
			);

			// Block Styles.
			$styles['wp-travel-frontend-booking-widget-style'] = array(
				'src'   => self::$app_path . '/build/frontend-booking-widget.css',
				'deps'  => array(),
				'ver'   => $booking_widget_deps['version'],
				'media' => 'all',
			);

		}

		// Admin Specific.
		if ( self::is_request( 'admin' ) ) {
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();

				if ( isset( $screen->is_block_editor ) && ! $screen->is_block_editor ) {
					// Main Styles for all admin pages.
					$styles['wp-travel-back-end'] = array(
						'src'   => self::$app_path . '/build/wp-travel-back-end.css',
						'deps'  => array(),
						'ver'   => WP_TRAVEL_VERSION,
						'media' => 'all',
					);
				}
			}

			$styles['wp-travel-setup-page'] = array(
				'src'   => self::$app_path . '/build/admin-setup-page.css',
				'deps'  => array(),
				'ver'   => WP_TRAVEL_VERSION,
				'media' => 'all',
			);

			$styles['wptravel-admin'] = array(
				'src'   => self::$app_path . '/build/admin.css',
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

			$admin_script_handler = array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-accordion' );
			if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
				$admin_script_handler[] = 'wp-travel-setup-page-js';
			}

			$scripts['wp-travel-admin-script'] = array(
				'src'       => self::$app_path . '/assets/js/wp-travel-back-end' . $suffix . '.js',
				'deps'      => $admin_script_handler,
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			$scripts['wptravel-yoast'] = array(
				'src'       => self::$app_path . '/assets/js/yoast.js',
				'deps'      => array( 'jquery' ),
				'ver'       => WP_TRAVEL_VERSION,
				'in_footer' => true,
			);

			// Trip Edit.
			$trip_edit_deps                               = $all_dependencies['admin-trip-options'];
			$scripts['wp-travel-admin-trip-options']      = array(
				'src'       => self::$app_path . '/build/admin-trip-options.js',
				'deps'      => $trip_edit_deps['dependencies'],
				'ver'       => $trip_edit_deps['version'],
				'in_footer' => true,
			);
			$styles['wp-travel-admin-trip-options-style'] = array(
				'src'   => self::$app_path . '/build/admin-trip-options.css',
				'deps'  => array( 'wp-components' ),
				'ver'   => $trip_edit_deps['version'],
				'media' => 'all',
			);

			// Settings.
			$trip_edit_deps                           = $all_dependencies['admin-settings'];
			$scripts['wp-travel-admin-settings']      = array(
				'src'       => self::$app_path . '/build/admin-settings.js',
				'deps'      => $trip_edit_deps['dependencies'],
				'ver'       => $trip_edit_deps['version'],
				'in_footer' => true,
			);
			$styles['wp-travel-admin-settings-style'] = array(
				'src'   => self::$app_path . '/build/admin-settings.css',
				'deps'  => array( 'wp-components', 'font-awesome-css' ),
				'ver'   => $trip_edit_deps['version'],
				'media' => 'all',
			);

			// Coupon.
			$coupon_deps                      = $all_dependencies['admin-coupon'];
			$scripts['wptravel-admin-coupon'] = array(
				'src'       => self::$app_path . '/build/admin-coupon.js',
				'deps'      => $coupon_deps['dependencies'],
				'ver'       => $coupon_deps['version'],
				'in_footer' => true,
			);

			$styles['wptravel-admin-coupon'] = array(
				'src'   => self::$app_path . '/build/admin-coupon.css',
				'deps'  => array( 'wp-components', 'font-awesome-css' ),
				'ver'   => $coupon_deps['version'],
				'media' => 'all',
			);
			// enquiry
			$enquiry_deps                      = $all_dependencies['admin-enquiry'];
			$scripts['wptravel-admin-enquiry'] = array(
				'src'       => self::$app_path . '/build/admin-enquiry.js',
				'deps'      => $coupon_deps['dependencies'],
				'ver'       => $coupon_deps['version'],
				'in_footer' => true,
			);

			// Legacy Widgets.
			// $legacy_widget_deps                 = $all_dependencies['legacy-widgets'];
			// $scripts['wptravel-legacy-widgets'] = array(
			// 'src'       => self::$app_path . '/build/legacy-widgets' . $suffix . '.js',
			// 'deps'      => $legacy_widget_deps['dependencies'],
			// 'ver'       => $legacy_widget_deps['version'],
			// 'in_footer' => true,
			// );
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
			wp_set_script_translations( $handler, 'wp-travel', plugin_dir_path( __FILE__ ) . 'i18n/languages' );
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

		// Admin coupon.
		$admin_coupon                   = include_once sprintf( '%sapp/build/admin-coupon.asset.php', WP_TRAVEL_ABSPATH );
		$admin_coupon['dependencies'][] = 'jquery';
		$dependenccies['admin-coupon']  = $admin_coupon;
		// End of Admin coupon.

		// Admin enquiry.
		$admin_enquiry                   = include_once sprintf( '%sapp/build/admin-enquiry.asset.php', WP_TRAVEL_ABSPATH );
		$admin_enquiry['dependencies'][] = 'jquery';
		$dependenccies['admin-enquiry']  = $admin_enquiry;
		// End of Admin enquiry.

		// Legacy widget.
		// $legacy_widgets                   = include_once sprintf( '%sapp/build/legacy-widgets.asset.php', WP_TRAVEL_ABSPATH );
		// $legacy_widgets['dependencies'][] = 'jquery';
		// $dependenccies['legacy-widgets']  = $legacy_widgets;
		// End of Legacy widget.

		return $dependenccies; // it will return all block dependency along with compled version.
	}

	/**
	 * Assets Dependency.
	 *
	 * @deprecated 5.0.1
	 */
	public static function get_localized_data() {
		return WpTravel_Helpers_Localize::get();
	}
}

WpTravel_Frontend_Assets::init();
