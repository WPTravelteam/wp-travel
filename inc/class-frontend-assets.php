<?php
class WP_Travel_Frontend_Assets {
	var $assets_path;
	public function __construct() {
		$this->assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

	function styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		wp_enqueue_style( 'wp-travel-style-front-end', $this->assets_path . 'assets/css/wp-travel-front-end.css' );
		wp_enqueue_style( 'wp-travel-style-popup', $this->assets_path . 'assets/css/magnific-popup.css' );
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( 'easy-responsive-tabs', $this->assets_path . 'assets/css/easy-responsive-tabs.css' );
		wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Inconsolata' );
		wp_enqueue_style( 'Inconsolata', 'https://fonts.googleapis.com/css?family=Play' );
		wp_enqueue_style( 'wp-travel-itineraries', $this->assets_path . 'assets/css/wp-travel-itineraries.css' );
		// fontawesome.
		wp_enqueue_style( 'font-awesome-css', $this->assets_path . 'assets/css/lib/font-awesome/css/font-awesome' . $suffix . '.css' );
		wp_enqueue_style( 'wp-travel-user-css', $this->assets_path . 'assets/css/wp-travel-user-styles' . $suffix . '.css' );
	}
	function scripts() {
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

		wp_enqueue_style( 'jquery-datepicker', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/css/lib/datepicker/datepicker.css', array(), '2.2.3' );

		if ( wp_travel_is_checkout_page() ) {

			wp_enqueue_script( 'wp-travel-modernizer', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/modernizer/modernizr.min.js', array('jquery'), WP_TRAVEL_VERSION, true );
			wp_enqueue_script( 'wp-travel-sticky-kit', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/sticky-kit/sticky-kit.min.js', array('jquery'), WP_TRAVEL_VERSION, true );
		}

		$lang_code = explode( '-', get_bloginfo('language') );
		$locale = $lang_code[0];

		wp_register_script( 'jquery-datepicker-lib', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/datepicker/datepicker.min.js', array( 'jquery' ), '2.2.3', true );
		wp_register_script( 'jquery-datepicker-lib-eng', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/datepicker/i18n/datepicker.' . $locale . '.js', array( 'jquery' ), '', 1 );
		wp_register_script( 'wp-travel-view-mode', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-view-mode.js', array( 'jquery' ), WP_TRAVEL_VERSION, 1 );
		wp_enqueue_script( 'jquery-datepicker-lib' );
		wp_enqueue_script( 'jquery-datepicker-lib-eng' );
		wp_enqueue_script( 'wp-travel-view-mode' );

		wp_register_script( 'wp-travel-widget-scripts', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-widgets.js', array( 'jquery', 'jquery-ui-slider', 'wp-util' ), WP_TRAVEL_VERSION, 1 );

		$trip_prices_data = array( 
			'currency_symbol' => wp_travel_get_currency_symbol(),
			'prices' => wp_reavel_get_itinereries_prices_array(),
			'locale' => $locale,
		);

		wp_localize_script( 'wp-travel-widget-scripts', 'trip_prices_data', $trip_prices_data );

		wp_enqueue_script( 'wp-travel-widget-scripts' );

		wp_enqueue_script( 'travel-door-booking', $this->assets_path . 'assets/js/booking.js', array( 'jquery' ) );
		// Script only for single itineraries.
		if ( is_singular( WP_TRAVEL_POST_TYPE ) || wp_travel_is_cart_page() || wp_travel_is_checkout_page() || wp_travel_is_account_page() ) {
			$map_data = get_wp_travel_map_data();

			$map_zoom_level = isset( $settings['google_map_zoom_level'] ) && '' != $settings['google_map_zoom_level'] ? $settings['google_map_zoom_level'] : 15;

			$api_key = '';
			if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
				$api_key = $settings['google_map_api_key'];
			}

			if ( ! wp_script_is( 'jquery-parsley', 'enqueued' ) ) {
				// Parsley For Frontend Single Trips.
				wp_enqueue_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ) );
			}

			wp_enqueue_script( 'travel-door-popup', $this->assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ) );
			wp_register_script( 'travel-door-script', $this->assets_path . 'assets/js/wp-travel-front-end.js', array( 'jquery', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion' ), WP_TRAVEL_VERSION, true );
			if ( '' != $api_key ) {
				wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), '', 1 );
				wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps.min.js', array( 'jquery', 'google-map-api' ), '0.4.24', 1 );

				wp_register_script( 'wp-travel-maps', $this->assets_path . 'assets/js/wp-travel-front-end-map.js', array( 'jquery', 'jquery-gmaps' ), WP_TRAVEL_VERSION, 1 );

				$wp_travel = array(
					'lat'  => $map_data['lat'],
					'lng'  => $map_data['lng'],
					'loc'  => $map_data['loc'],
					'zoom' => $map_zoom_level,
				);

				$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel );
				wp_localize_script( 'wp-travel-maps', 'wp_travel', $wp_travel );
				// Enqueued script with localized data.
				wp_enqueue_script( 'wp-travel-maps' );
			}
			// Add vars.
			$frontend_vars = array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'wp_travel_frontend_enqueries' ),
				'cartUrl' => wp_travel_get_cart_url(),
				'text_array' => array(
					'pricing_select' => __( 'Select', 'wp-travel' ),
					'pricing_close'  => __( 'Close', 'wp-travel' ),
				),
				'locale' => $locale,
			);

			$frontend_vars = apply_filters( 'wp_travel_js_frontend_vars', $frontend_vars );

			wp_localize_script( 'travel-door-script', 'wp_travel_frontend_vars', $frontend_vars );

			// Enqueued script.
			wp_enqueue_script( 'travel-door-script' );

			wp_enqueue_script( 'easy-responsive-tabs', $this->assets_path . 'assets/js/easy-responsive-tabs.js', array( 'jquery' ) );
			wp_enqueue_script( 'collapse-js', $this->assets_path . 'assets/js/collapse.js', array( 'jquery' ), WP_TRAVEL_VERSION );

			wp_enqueue_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ) );
			// Return if payment is not enabled.
			if ( ! wp_travel_is_payment_enabled() ) {
				return;
			}
			$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : 'USD';

			global $wt_cart;

			$cart_amounts   = $wt_cart->get_total();			
			$trip_price     = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : '';
			$payment_amount = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : '';

			$wt_payment = array(
				'book_now' 	      => __( 'Book Now', 'wp-travel' ),
				'book_n_pay'      => __( 'Book and Pay', 'wp-travel' ),
				'currency_code'   => $currency_code,
				'currency_symbol' => wp_travel_get_currency_symbol(),
				'price_per'       => wp_travel_get_price_per_text( $trip_id, true ),
				'trip_price'      => $trip_price,
				'payment_amount'  => $payment_amount,
			);

			$wt_payment = apply_filters( 'wt_payment_vars_localize', $wt_payment, $settings );
			wp_register_script( 'wp-travel-payment-frontend-script', $this->assets_path . 'assets/js/payment.js', array( 'jquery' ) );

			wp_localize_script( 'wp-travel-payment-frontend-script', 'wt_payment', $wt_payment );
			wp_enqueue_script( 'wp-travel-payment-frontend-script' );

			wp_enqueue_script( 'wp-travel-cart', $this->assets_path . 'assets/js/cart.js', array( 'jquery', 'wp-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ) );

		}
	}
}

new WP_Travel_Frontend_Assets();
