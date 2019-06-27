<?php
if ( ! class_exists( 'WP_Travel_Assets' ) ) {
	class WP_Travel_Assets {

		private static $assets_path;

		/**
		 * Frontend assets.
		 */
		public static function frontend() {
			self::$assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$settings = wp_travel_get_settings();

			global $post;
			$trip_id = '';
			if ( ! is_null( $post ) ) {
				$trip_id = $post->ID;
			}

			if ( ! is_singular( WP_TRAVEL_POST_TYPE ) && isset( $_GET['trip_id'] ) ) {
				$trip_id = $_GET['trip_id'];
			}

			$map_data       = get_wp_travel_map_data();
			$map_zoom_level = $settings['google_map_zoom_level'];

			// Getting Locale to fetch Localized calender js.
			$lang_code            = explode( '-', get_bloginfo( 'language' ) );
			$locale               = $lang_code[0];
			$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
			$default_path         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );
			$filename = 'datepicker.' . $locale . '.js';
			if ( ! file_exists( trailingslashit( $wp_content_file_path ) . $filename ) && ! file_exists( trailingslashit( $default_path ) . $filename ) ) {
				$locale = 'en';
			}

			// locale ends.
			// Localized varialble.
			$wp_travel = array(
				'currency_symbol'    => wp_travel_get_currency_symbol(),
				'currency_position'  => $settings['currency_position'],
				'thousand_separator' => $settings['thousand_separator'],
				'decimal_separator'  => $settings['decimal_separator'],
				'number_of_decimals' => $settings['number_of_decimals'],

				'prices'             => wp_reavel_get_itinereries_prices_array(), // why this ?
				'locale'             => $locale,
				'nonce'              => wp_create_nonce( 'wp_travel_frontend_security' ),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'strings'            => wp_travel_get_strings(),
				// Need map data enhancement.
				'lat'                => $map_data['lat'],
				'lng'                => $map_data['lng'],
				'loc'                => $map_data['loc'],
				'zoom'               => $map_zoom_level,
			);

			$registered = self::register_scripts();
			$registered_styles = isset( $registered['styles'] ) ? $registered['styles'] : array();
			$registered_scripts = isset( $registered['scripts'] ) ? $registered['scripts'] : array();

			// Registered Styles.
			foreach ( $registered_styles as $handler => $script ) {
				wp_register_style( $handler, $script['src'], $script['deps'], $script['ver'], $script['media'] );
			}

			// Registered Scripts.
			foreach ( $registered_scripts as $handler => $script ) {
				wp_register_script( $handler, $script['src'], $script['deps'], $script['ver'], $script['in_footer'] );
			}

			// Styles
			wp_enqueue_style( 'dashicons' );
			wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Inconsolata' );
			wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Play' );

			wp_enqueue_style( 'wp-travel-frontend' );
			wp_enqueue_style( 'wp-travel-popup' );
			wp_enqueue_style( 'easy-responsive-tabs' );
			wp_enqueue_style( 'wp-travel-itineraries' );
			// fontawesome.
			wp_enqueue_style( 'font-awesome-css' );
			wp_enqueue_style( 'wp-travel-fa-css' );
			wp_enqueue_style( 'wp-travel-user-css' );
			wp_enqueue_style( 'jquery-datepicker-lib' );

			// Scripts.
			wp_enqueue_script( 'wp-travel-view-mode' );
			wp_enqueue_script( 'wp-travel-accordion' );
			wp_enqueue_script( 'wp-travel-widget-scripts' );

			if ( wp_travel_is_checkout_page() ) {
				wp_enqueue_script( 'wp-travel-modernizer' );
				wp_enqueue_script( 'wp-travel-sticky-kit' );
			}

			// Script only for itineraries.
			if ( is_singular( WP_TRAVEL_POST_TYPE ) || wp_travel_is_cart_page() || wp_travel_is_checkout_page() || wp_travel_is_account_page() ) {

				// Add localized vars.
				$wp_travel['cartUrl'] = wp_travel_get_cart_url();

				wp_enqueue_script( 'wp-travel-booking' );
				wp_enqueue_script( 'wp-travel-moment' );
				wp_enqueue_script( 'wp-travel-popup' );
				wp_enqueue_script( 'wp-travel-script' );
				wp_enqueue_script( 'easy-responsive-tabs' );
				wp_enqueue_script( 'collapse-js' );
				wp_enqueue_script( 'wp-travel-cart' );

				if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
					// Parsley For Frontend Single Trips.
					wp_enqueue_script( 'jquery-parsley' );
				}

				// Load if payment is enabled.
				if ( wp_travel_can_load_payment_scripts() ) {

					global $wt_cart;

					$cart_amounts   = $wt_cart->get_total();
					$trip_price     = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : '';
					$payment_amount = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : '';

					$wp_travel['payment']['currency_code']   = $settings['currency'];
					$wp_travel['payment']['currency_symbol'] = wp_travel_get_currency_symbol();
					$wp_travel['payment']['price_per']       = wp_travel_get_price_per_text( $trip_id, '', true );
					$wp_travel['payment']['trip_price']      = $trip_price;
					$wp_travel['payment']['payment_amount']  = $payment_amount;

					wp_enqueue_script( 'wp-travel-payment-frontend-script' );
				}

				// for GMAP.
				$api_key = '';

				$get_maps    = wp_travel_get_maps();
				$current_map = $get_maps['selected'];

				$show_google_map = ( 'google-map' === $current_map ) ? true : false;
				$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map );

				if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
					$api_key = $settings['google_map_api_key'];
				}
				if ( '' != $api_key && $show_google_map ) {
					wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), WP_TRAVEL_VERSION, 1 );

					$gmap_dependency = array( 'jquery', 'google-map-api' );
					wp_register_script( 'jquery-gmaps', self::$assets_path . 'assets/js/lib/gmaps/gmaps.min.js', $gmap_dependency, WP_TRAVEL_VERSION, 1 );
					wp_register_script( 'wp-travel-maps', self::$assets_path . 'assets/js/wp-travel-front-end-map.js', array( 'jquery', 'jquery-gmaps' ), WP_TRAVEL_VERSION, 1 );
					wp_enqueue_script( 'wp-travel-maps' );
				}
				// for GMAP Ends.
				// Enqueued script.
			}

			$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel, $settings );
			wp_localize_script( 'jquery-datepicker-lib', 'wp_travel', $wp_travel );

			wp_enqueue_script( 'jquery-datepicker-lib' );
			wp_enqueue_script( 'jquery-datepicker-lib-eng' );
		}

		/**
		 * Admin assets.
		 */
		public static function admin() {
			self::$assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$screen = get_current_screen();

			// Register styles.
			wp_register_style( 'magnific-popup-css', self::$assets_path . 'assets/css/magnific-popup' . $suffix . '.css' );
			wp_register_style( 'wp-travel-slick', self::$assets_path . 'assets/css/lib/slick/slick.min.css' );
			// fontawesome.
			wp_register_style( 'font-awesome-css', self::$assets_path . 'assets/css/lib/font-awesome/css/fontawesome-all' . $suffix . '.css' );
			wp_register_style( 'select2-style', self::$assets_path . 'assets/css/lib/select2/select2' . $suffix . '.css', array(), '4.0.5' );

			wp_enqueue_media();
			wp_enqueue_style( 'jquery-datepicker', self::$assets_path . 'assets/css/lib/datepicker/datepicker' . $suffix . '.css', array(), WP_TRAVEL_VERSION );

			wp_enqueue_style( 'wp-travel-tabs', self::$assets_path . 'assets/css/wp-travel-tabs' . $suffix . '.css', array( 'wp-color-picker' ), WP_TRAVEL_VERSION );
			wp_enqueue_style( 'wp-travel-back-end', self::$assets_path . 'assets/css/wp-travel-back-end' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
			wp_enqueue_style( 'wp-travel-admin-1-style', self::$assets_path . 'assets/css/wp-travel-admin-1' . $suffix . '.css', array(), WP_TRAVEL_VERSION );

			// Tab for settings page.
			$setting_allowed = array( 'itineraries', 'itinerary-booking_page_wp-travel-marketplace', 'itinerary-booking_page_settings', 'wp-travel-coupons', 'toplevel_page_wp_travel_network_settings-network', 'tour-extras' );

			// Register scripts.
			wp_register_script( 'jquery-datepicker-lib', self::$assets_path . 'assets/js/lib/datepicker/datepicker.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
			wp_register_script( 'jquery-datepicker-lib-eng', self::$assets_path . 'assets/js/lib/datepicker/i18n/datepicker.en.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
			wp_register_script( 'select2-js', self::$assets_path . 'assets/js/lib/select2/select2' . $suffix . '.js', array( 'jquery' ), '4.0.5', true );
			wp_register_script( 'wp-travel-fields-script', self::$assets_path . 'assets/js/wp-travel-fields-scripts' . $suffix . '.js', array( 'select2-js' ), WP_TRAVEL_VERSION, true );
			wp_register_script( 'magnific-popup-script', self::$assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );
			wp_register_script( 'jquery-parsley', self::$assets_path . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );
			wp_register_script( 'wp-travel-accordion', self::$assets_path . 'assets/js/wp-travel-accordion.js', array( 'jquery', 'jquery-ui-accordion' ), WP_TRAVEL_VERSION );

			// Tab for settings page.
			if ( in_array( $screen->id, $setting_allowed ) ) {
				wp_enqueue_style( 'font-awesome-css' );
				wp_enqueue_style( 'select2-style' );
				wp_enqueue_style( 'magnific-popup-css' );

				wp_register_script( 'wp-travel-tabs', self::$assets_path . 'assets/js/wp-travel-tabs' . $suffix . '.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'wp-color-picker', 'select2-js', 'jquery-ui-accordion' ), WP_TRAVEL_VERSION, 1 );
				wp_enqueue_script( 'wp-travel-fields-script' );
				wp_enqueue_script( 'wp-travel-tabs' );
			}
			// @since 1.0.5 // booking stat
			if ( 'itinerary-booking_page_booking_chart' === $screen->id ) {
				wp_register_script( 'jquery-chart', self::$assets_path . 'assets/js/lib/chartjs/Chart.bundle' . $suffix . '.js', array( 'jquery' ), WP_TRAVEL_VERSION );
				wp_register_script( 'jquery-chart-util', self::$assets_path . 'assets/js/lib/chartjs/chart-utils.js', array( 'jquery' ), WP_TRAVEL_VERSION );

				wp_register_script( 'jquery-chart-custom', self::$assets_path . 'assets/js/lib/chartjs/chart-custom.js', array( 'jquery', 'jquery-chart', 'jquery-chart-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION );
				$booking_data      = wp_travel_get_booking_data();
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
					'labels'            => json_encode( $labels ),
					'datasets'          => json_encode( $wp_travel_stat_data ),
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
				if ( isset( $_REQUEST['compare_stat'] ) && 'yes' == $_REQUEST['compare_stat'] ) {
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

					// if ( class_exists( 'WP_travel_paypal' ) && isset( $booking_data['total_sales_compare'] ) ) :
						$wp_travel_chart_data['total_sales_compare'] = $booking_data['total_sales_compare'];
					// endif;
				}
				$wp_travel_chart_data = apply_filters( 'wp_travel_chart_data', $wp_travel_chart_data );
				wp_localize_script( 'jquery-chart-custom', 'wp_travel_chart_data', $wp_travel_chart_data );
				wp_enqueue_script( 'jquery-chart-custom' );
			}

			$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries' );
			if ( in_array( $screen->id, $allowed_screen ) ) {
				$settings = wp_travel_get_settings();
				global $post;
				wp_register_script( 'wp-travel-moment', self::$assets_path . 'assets/js/moment.js', array( 'jquery' ), WP_TRAVEL_VERSION, 1 );

				$map_data = get_wp_travel_map_data();

				$depencency = array( 'jquery', 'jquery-ui-tabs', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'wp-travel-media-upload', 'jquery-ui-sortable', 'jquery-ui-accordion', 'wp-travel-moment' );

				$api_key = '';

				$get_maps    = wp_travel_get_maps();
				$current_map = $get_maps['selected'];

				$show_google_map = ( 'google-map' === $current_map ) ? true : false;
				$show_google_map = apply_filters( 'wp_travel_load_google_maps_api', $show_google_map );

				if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
					$api_key = $settings['google_map_api_key'];
				}
				if ( '' != $api_key && $show_google_map ) {
					wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), WP_TRAVEL_VERSION, 1 );

					wp_register_script( 'jquery-gmaps', self::$assets_path . 'assets/js/lib/gmaps/gmaps' . $suffix . '.js', array( 'jquery', 'google-map-api' ), WP_TRAVEL_VERSION, 1 );
					$depencency[] = 'jquery-gmaps';
				}
				wp_enqueue_script( 'wp-travel-script-2', self::$assets_path . 'assets/js/jquery.wptraveluploader' . $suffix . '.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );

				wp_register_script( 'wp-travel-script', self::$assets_path . 'assets/js/wp-travel-back-end' . $suffix . '.js', $depencency, WP_TRAVEL_VERSION, 1 );

				wp_register_script( 'wp-travel-media-upload', self::$assets_path . 'assets/js/wp-travel-media-upload' . $suffix . '.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION, 1 );

				$wp_travel_gallery_data                       = array(
					'ajax'            => admin_url( 'admin-ajax.php' ),
					'lat'             => $map_data['lat'],
					'lng'             => $map_data['lng'],
					'loc'             => $map_data['loc'],
					'labels'          => array(
						'uploader_files_computer' => __( 'Select Files from Your Computer', 'wp-travel' ),
					),
					'drag_drop_nonce' => wp_create_nonce( 'wp-travel-drag-drop-nonce' ),
				);
				$date_format                                  = get_option( 'date_format' );
				$js_date_format                               = wp_travel_date_format_php_to_js();
				$moment_date_format                           = wp_travel_moment_date_format( $date_format );
				$wp_travel_gallery_data['js_date_format']     = $js_date_format;
				$wp_travel_gallery_data['moment_date_format'] = $moment_date_format;

				$wp_travel_gallery_data = apply_filters( 'wp_travel_localize_gallery_data', $wp_travel_gallery_data );
				wp_localize_script( 'wp-travel-media-upload', 'wp_travel_drag_drop_uploader', $wp_travel_gallery_data );

				// Enqueued script with localized data.
				wp_enqueue_script( 'wp-travel-script' );
				wp_enqueue_script( 'wp-travel-media-upload' );
				wp_enqueue_script( 'jquery-parsley' );
			}

			$allowed_itinerary_general_screens = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-booking_page_settings' );

			if ( in_array( $screen->id, $allowed_itinerary_general_screens ) ) {
				wp_enqueue_script( 'collapse-js', self::$assets_path . 'assets/js/collapse.js', array( 'jquery' ), WP_TRAVEL_VERSION );
			}
			wp_enqueue_script( 'wp-travel-accordion' );
		}

		/**
		 * Registered Scripts to enqueue.
		 *
		 * @since 2.0.7
		 */
		public static function register_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			// Getting Locale to fetch Localized calender js.
			$lang_code            = explode( '-', get_bloginfo( 'language' ) );
			$locale               = $lang_code[0];
			$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
			$default_path         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );

			$wp_content_file_url = WP_CONTENT_URL . '/languages/wp-travel/datepicker/';
			$default_url         = sprintf( '%sassets/js/lib/datepicker/i18n/', self::$assets_path );

			$filename = 'datepicker.' . $locale . '.js';

			if ( file_exists( trailingslashit( $wp_content_file_path ) . $filename ) ) {
				$datepicker_i18n_file = trailingslashit( $wp_content_file_url ) . $filename;
			} elseif ( file_exists( trailingslashit( $default_path ) . $filename ) ) {
				$datepicker_i18n_file = $default_url . $filename;
			} else {
				$datepicker_i18n_file = $default_url . 'datepicker.en.js';
			}

			// General
			$scripts = array(
				'jquery-datepicker-lib' => array(
					'src'       => self::$assets_path . 'assets/js/lib/datepicker/datepicker.js',
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
				'wp-travel-moment' => array(
					'src'       => self::$assets_path . 'assets/js/moment.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'jquery-parsley' => array(
					'src'       => self::$assets_path . 'assets/js/lib/parsley/parsley.min.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => false,
				),
				'wp-travel-widget-scripts' => array(
					'src'       => self::$assets_path . 'assets/js/wp-travel-widgets.js',
					'deps'      => array( 'jquery', 'jquery-ui-slider', 'wp-util' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'wp-travel-accordion' => array(
					'src'       => self::$assets_path . 'assets/js/wp-travel-accordion.js',
					'deps'      => array( 'jquery', 'jquery-ui-accordion' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'wp-travel-modernizer' => array(
					'src'       => self::$assets_path . 'assets/js/lib/modernizer/modernizr.min.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'wp-travel-sticky-kit' => array(
					'src'       => self::$assets_path . 'assets/js/lib/sticky-kit/sticky-kit.min.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'wp-travel-popup' => array(
					'src'       => self::$assets_path . 'assets/js/jquery.magnific-popup.min.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'easy-responsive-tabs' => array(
					'src'       => self::$assets_path . 'assets/js/easy-responsive-tabs.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'collapse-js' => array(
					'src'       => self::$assets_path . 'assets/js/collapse.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
				'wp-travel-slick' => array(
					'src'       => self::$assets_path . 'assets/js/lib/slick/slick.min.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				),
			);

			$styles = array(
				'wp-travel-slick' => array(
					'src'       => self::$assets_path . 'assets/css/lib/slick/slick.min.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'wp-travel-frontend' => array(
					'src'       => self::$assets_path . 'assets/css/wp-travel-front-end.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'wp-travel-popup' => array(
					'src'       => self::$assets_path . 'assets/css/magnific-popup.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'easy-responsive-tabs' => array(
					'src'       => self::$assets_path . 'assets/css/easy-responsive-tabs.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'wp-travel-itineraries' => array(
					'src'       => self::$assets_path . 'assets/css/wp-travel-itineraries.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'font-awesome-css' => array(
					'src'       => self::$assets_path . 'assets/css/lib/font-awesome/css/fontawesome-all' . $suffix . '.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'wp-travel-fa-css' => array(
					'src'       => self::$assets_path . 'assets/css/lib/font-awesome/css/wp-travel-fa-icons' . $suffix . '.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'wp-travel-user-css' => array(
					'src'       => self::$assets_path . 'assets/css/wp-travel-user-styles' . $suffix . '.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
				'jquery-datepicker-lib' => array(
					'src'       => self::$assets_path . 'assets/css/lib/datepicker/datepicker.css',
					'deps'      => array(),
					'ver'       => WP_TRAVEL_VERSION,
					'media' => 'all',
				),
			);

			// Frontend Specific.
			if ( self::is_request( 'frontend' ) ) {
				$scripts['wp-travel-script'] = array(
					'src'       => self::$assets_path . 'assets/js/wp-travel-front-end' . $suffix . '.js',
					'deps'      => array( 'jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);
				$scripts['wp-travel-cart'] = array(
					'src'       => self::$assets_path . 'assets/js/cart.js',
					'deps'      => array( 'jquery', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);

				$scripts['wp-travel-view-mode'] = array(
					'src'       => self::$assets_path . 'assets/js/wp-travel-view-mode.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);

				$scripts['wp-travel-payment-frontend-script'] = array(
					'src'       => self::$assets_path . 'assets/js/payment.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);
				$scripts['wp-travel-booking'] = array(
					'src'       => self::$assets_path . 'assets/js/booking.js',
					'deps'      => array( 'jquery' ),
					'ver'       => WP_TRAVEL_VERSION,
					'in_footer' => true,
				);
			}

			// Admin Specific.
			if ( self::is_request( 'admin' ) ) {
				// admin.
			}

			return apply_filters( 'wp_travel_registered_scripts', array( 'scripts' => $scripts, 'styles' => $styles ) );

		}

		private static function is_request( $type ) {
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

	}
}
