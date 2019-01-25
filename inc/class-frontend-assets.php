<?php
class WP_Travel_Frontend_Assets {
	var $assets_path;
	public function __construct() {
		$this->assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	/**
	 * WP Travel Frontend Styles.
	 */
	public function styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'wp-travel-style-front-end', $this->assets_path . 'assets/css/wp-travel-front-end.css' );
		wp_enqueue_style( 'wp-travel-style-popup', $this->assets_path . 'assets/css/magnific-popup.css' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'easy-responsive-tabs', $this->assets_path . 'assets/css/easy-responsive-tabs.css' );
		wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Inconsolata' );
		wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Play' );
		wp_enqueue_style( 'wp-travel-itineraries', $this->assets_path . 'assets/css/wp-travel-itineraries.css' );
		// fontawesome.
		wp_enqueue_style( 'font-awesome-css', $this->assets_path . 'assets/css/lib/font-awesome/css/fontawesome-all' . $suffix . '.css' );
		wp_enqueue_style( 'wp-travel-fa-css', $this->assets_path . 'assets/css/lib/font-awesome/css/wp-travel-fa-icons' . $suffix . '.css' );
		wp_enqueue_style( 'wp-travel-user-css', $this->assets_path . 'assets/css/wp-travel-user-styles' . $suffix . '.css' );

		wp_enqueue_style( 'jquery-datepicker', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/css/lib/datepicker/datepicker.css', array(), WP_TRAVEL_VERSION );

		// wp_enqueue_style( 'wp-travel-rtl-frontend', $this->assets_path . 'assets/css/wp-travel-rtl-front-end' . $suffix . '.css' );
	}

	/**
	 * WP Travel Frontend Scripts.
	 */
	public function scripts() {
		$suffix   = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$settings = wp_travel_get_settings();

		global $post;

		$trip_id = '';

		if ( ! is_null( $post ) ) {
			$trip_id = $post->ID;
		}
		if ( ! is_singular( WP_TRAVEL_POST_TYPE ) && isset( $_GET['trip_id'] ) ) {
			$trip_id = $_GET['trip_id'];
		}
		// Init array for localized variables.
		$wp_travel         = array();
		$frontend_vars     = array();
		$localized_strings = array(
			'confirm'    => __( 'Are you sure you want to remove?', 'wp-travel' ),
			'book_now'   => __( 'Book Now', 'wp-travel' ),
			'book_n_pay' => __( 'Book and Pay', 'wp-travel' ),
		);
		$wt_payment        = array();

		if ( wp_travel_is_checkout_page() ) {
			wp_enqueue_script( 'wp-travel-modernizer', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/modernizer/modernizr.min.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
			wp_enqueue_script( 'wp-travel-sticky-kit', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/sticky-kit/sticky-kit.min.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
		}

		// Getting Locale to fetch Localized calender js.
		$lang_code            = explode( '-', get_bloginfo( 'language' ) );
		$locale               = $lang_code[0];
		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );

		$wp_content_file_url = WP_CONTENT_URL . '/languages/wp-travel/datepicker/';
		$default_url         = sprintf( '%sassets/js/lib/datepicker/i18n/', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) );

		$filename = 'datepicker.' . $locale . '.js';

		if ( file_exists( trailingslashit( $wp_content_file_path ) . $filename ) ) {
			$datepicker_i18n_file = trailingslashit( $wp_content_file_url ) . $filename;
		} elseif ( file_exists( trailingslashit( $default_path ) . $filename ) ) {
			$datepicker_i18n_file = $default_url . $filename;
		} else {
			$datepicker_i18n_file = $default_url . 'datepicker.en.js';
			$locale               = 'en';
		}

		wp_register_script( 'jquery-datepicker-lib', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/datepicker/datepicker.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
		wp_register_script( 'wp-travel-moment', $this->assets_path . 'assets/js/moment.js', array( 'jquery' ), WP_TRAVEL_VERSION, 1 );
		wp_register_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );

		wp_register_script( 'jquery-datepicker-lib-eng', $datepicker_i18n_file, array( 'jquery' ), WP_TRAVEL_VERSION, 1 );

		wp_register_script( 'wp-travel-view-mode', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-view-mode.js', array( 'jquery' ), WP_TRAVEL_VERSION, 1 );

		wp_enqueue_script( 'wp-travel-view-mode' );

		wp_register_script( 'wp-travel-widget-scripts', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-widgets.js', array( 'jquery', 'jquery-ui-slider', 'wp-util' ), WP_TRAVEL_VERSION, 1 );

		$trip_prices_data = array(
			'currency_symbol' => wp_travel_get_currency_symbol(),
			'prices'          => wp_reavel_get_itinereries_prices_array(),
			'locale'          => $locale,
			'nonce'           => wp_create_nonce( 'wp_travel_frontend_enqueries' ),
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
		);

		// Localized Script Depricated and will be removed in future version. [trip_prices_data].
		wp_localize_script( 'wp-travel-widget-scripts', 'trip_prices_data', $trip_prices_data );

		wp_enqueue_script( 'wp-travel-widget-scripts' );

		wp_enqueue_script( 'wp-travel-booking', $this->assets_path . 'assets/js/booking.js', array( 'jquery' ), WP_TRAVEL_VERSION );
		// Script only for single itineraries.
		if ( is_singular( WP_TRAVEL_POST_TYPE ) || wp_travel_is_cart_page() || wp_travel_is_checkout_page() || wp_travel_is_account_page() ) {
			wp_enqueue_script( 'wp-travel-moment' );
			if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
				// Parsley For Frontend Single Trips.
				wp_enqueue_script( 'jquery-parsley' );
			}

			wp_enqueue_script( 'wp-travel-popup', $this->assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );
			wp_register_script( 'wp-travel-script', $this->assets_path . 'assets/js/wp-travel-front-end' . $suffix . '.js', array( 'jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion' ), WP_TRAVEL_VERSION, true );

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
				wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps.min.js', $gmap_dependency, WP_TRAVEL_VERSION, 1 );
				wp_register_script( 'wp-travel-maps', $this->assets_path . 'assets/js/wp-travel-front-end-map.js', array( 'jquery', 'jquery-gmaps' ), WP_TRAVEL_VERSION, 1 );
				wp_enqueue_script( 'wp-travel-maps' );
			}
			// Add vars.
			$frontend_vars = array(
				'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'wp_travel_frontend_enqueries' ),
				'cartUrl'    => wp_travel_get_cart_url(),
				'text_array' => array(
					'pricing_select' => __( 'Select', 'wp-travel' ), // Strings
					'pricing_close'  => __( 'Close', 'wp-travel' ),  // Strings.
				),
				'locale'     => $locale,
			);

			$frontend_vars = apply_filters( 'wp_travel_js_frontend_vars', $frontend_vars );
			// Localized Script Depricated and will be removed in future version. [wp_travel_frontend_vars].
			wp_localize_script( 'wp-travel-script', 'wp_travel_frontend_vars', $frontend_vars );

			// Enqueued script.
			wp_enqueue_script( 'wp-travel-script' );

			wp_enqueue_script( 'easy-responsive-tabs', $this->assets_path . 'assets/js/easy-responsive-tabs.js', array( 'jquery' ), WP_TRAVEL_VERSION );

			wp_enqueue_script( 'collapse-js', $this->assets_path . 'assets/js/collapse.js', array( 'jquery' ), WP_TRAVEL_VERSION );

			wp_enqueue_script( 'jquery-parsley' );

			wp_register_script( 'wp-travel-cart', $this->assets_path . 'assets/js/cart.js', array( 'jquery', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION );

			// Localized Script Depricated and will be removed in future version. [cart_texts].
			wp_localize_script( 'wp-travel-cart', 'cart_texts', $localized_strings );

			wp_enqueue_script( 'wp-travel-cart' );

			// Load if payment is enabled.
			if ( wp_travel_is_payment_enabled() ) {
				$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : 'USD';

				global $wt_cart;

				$cart_amounts   = $wt_cart->get_total();
				$trip_price     = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : '';
				$payment_amount = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : '';

				$wt_payment = array(
					'book_now'        => __( 'Book Now', 'wp-travel' ),
					'book_n_pay'      => __( 'Book and Pay', 'wp-travel' ),
					'currency_code'   => $currency_code,
					'currency_symbol' => wp_travel_get_currency_symbol(),
					'price_per'       => wp_travel_get_price_per_text( $trip_id, true ),
					'trip_price'      => $trip_price,
					'payment_amount'  => $payment_amount,
				);

				$wt_payment = apply_filters( 'wt_payment_vars_localize', $wt_payment, $settings );
				wp_register_script( 'wp-travel-payment-frontend-script', $this->assets_path . 'assets/js/payment.js', array( 'jquery' ), WP_TRAVEL_VERSION );

				wp_localize_script( 'wp-travel-payment-frontend-script', 'wt_payment', $wt_payment );
				wp_enqueue_script( 'wp-travel-payment-frontend-script' );
			}
		}

		// Localized vars into datepicker. because datepicker is in all pages.
		$map_data       = get_wp_travel_map_data();
		$map_zoom_level = isset( $settings['google_map_zoom_level'] ) && '' != $settings['google_map_zoom_level'] ? $settings['google_map_zoom_level'] : 15;

		$wp_travel['lat']  = $map_data['lat'];
		$wp_travel['lng']  = $map_data['lng'];
		$wp_travel['loc']  = $map_data['loc'];
		$wp_travel['zoom'] = $map_zoom_level;

		// Merging $trip_prices_data. [trip_prices_data].
		$wp_travel = array_merge( $wp_travel, $trip_prices_data );
		// Merging $frontend_vars. [wp_travel_frontend_vars].
		if ( isset( $frontend_vars['text_array'] ) ) {
			// Assigning strings in frontend vars to new localized var wp_travel.strings.
			$strings = $frontend_vars['text_array'];
			unset( $frontend_vars['text_array'] );

			$localized_strings['select'] = $strings['pricing_select'];
			$localized_strings['close']  = $strings['pricing_close'];
		}

		$wp_travel = array_merge( $wp_travel, $frontend_vars );

		// Merging $localized_strings. [cart_texts] and other.
		$localized_strings = apply_filters( 'wp_travel_strings', $localized_strings );
		$text              = array( 'strings' => $localized_strings );
		$wp_travel         = array_merge( $wp_travel, $text );

		// Merging $wt_payment. [wt_payment].
		if ( isset( $wt_payment['book_now'] ) ) {
			unset( $wt_payment['book_now'], $wt_payment['book_n_pay'] );
		}
		$wp_travel = array_merge( $wp_travel, array( 'payment' => $wt_payment ) );

		$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel, $settings );
		wp_localize_script( 'jquery-datepicker-lib', 'wp_travel', $wp_travel );

		wp_enqueue_script( 'jquery-datepicker-lib' );
		wp_enqueue_script( 'jquery-datepicker-lib-eng' );
	}
}

new WP_Travel_Frontend_Assets();
