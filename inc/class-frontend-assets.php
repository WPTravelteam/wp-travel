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
	}
	function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$settings = wp_travel_get_settings();

		global $post;

		wp_enqueue_style( 'jquery-datepicker', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/css/lib/datepicker/datepicker.css', array(), '2.2.3' );

		wp_register_script( 'jquery-datepicker-lib', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/datepicker/datepicker.min.js', array( 'jquery' ), '2.2.3', true );
		wp_register_script( 'jquery-datepicker-lib-eng', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/datepicker/i18n/datepicker.en.js', array( 'jquery' ), '', 1 );
		wp_register_script( 'wp-travel-view-mode', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-view-mode.js', array( 'jquery' ), '', 1 );
		wp_enqueue_script('jquery-datepicker-lib');
		wp_enqueue_script('jquery-datepicker-lib-eng');
		wp_enqueue_script('wp-travel-view-mode');

		wp_enqueue_script( 'wp-travel-widget-scripts', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/wp-travel-widgets.js', array( 'jquery', 'jquery-ui-slider' ), '', 1 );
		
		wp_enqueue_script( 'travel-door-booking', $this->assets_path . 'assets/js/booking.js', array( 'jquery' ) );
		// Script only for single itineraries.
		if ( ! is_singular( WP_TRAVEL_POST_TYPE ) ) {
			return;
		}
		$map_data = get_wp_travel_map_data();

		$api_key = '';
		if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
			$api_key = $settings['google_map_api_key'];
		}

		wp_enqueue_script( 'travel-door-popup', $this->assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ) );
		wp_register_script( 'travel-door-script', $this->assets_path . 'assets/js/wp-travel-front-end.js', array( 'jquery','jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'jquery-ui-accordion' ), '', true );
		if ( '' != $api_key ) {
			wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), '', 1 );
			wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps.min.js', array( 'jquery', 'google-map-api' ), '', 1 );

			wp_register_script( 'wp-travel-maps', $this->assets_path . 'assets/js/wp-travel-front-end-map.js', array( 'jquery', 'jquery-gmaps' ), '', 1 );

			$wp_travel = array(
				'lat' => $map_data['lat'],
				'lng' => $map_data['lng'],
				'loc' => $map_data['loc'],
			);

			$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel );
			wp_localize_script( 'wp-travel-maps', 'wp_travel', $wp_travel );
			// Enqueued script with localized data.
			wp_enqueue_script( 'wp-travel-maps' );
		}
		//add vars.
		$frontend_vars = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'wp_travel_frontend_enqueries' ),
		);

		wp_localize_script( 'travel-door-script', 'wp_travel_frontend_vars', $frontend_vars );

		// Enqueued script.
		wp_enqueue_script( 'travel-door-script' );

		wp_enqueue_script( 'easy-responsive-tabs', $this->assets_path . 'assets/js/easy-responsive-tabs.js', array( 'jquery' ) );
		wp_enqueue_script( 'collapse-js',  $this->assets_path . 'assets/js/collapse.js', array('jquery'));

		$currency_code = ( isset( $settings['currency'] ) ) ? $settings['currency'] : 'USD';
		$trip_price = $payment_amount = wp_travel_get_actual_trip_price( $post->ID );
		$minimum_partial_payout = wp_travel_get_minimum_partial_payout( $post->ID );
		if ( isset( $settings['partial_payment'] ) && 'yes' === $settings['partial_payment'] ) {
			$payment_amount = $minimum_partial_payout;
		}
		$wt_payment = array(
			'book_now' 	 => __( 'Book Now' ),
			'book_n_pay' => __( 'Book and Pay' ),
			'currency_code' => $currency_code,
			'currency_symbol' => wp_travel_get_currency_symbol(),
			'price_per'		=> wp_travel_get_price_per_text( $post->ID ),
			'trip_price'	=> $trip_price,
			'payment_amount' => $payment_amount,
		);

		$wt_payment = apply_filters( 'wt_payment_vars_localize', $wt_payment, $settings );
		wp_register_script( 'wp-travel-payment-frontend-script', $this->assets_path . 'assets/js/payment.js', array( 'jquery' ) );

		wp_localize_script( 'wp-travel-payment-frontend-script', 'wt_payment', $wt_payment );
		wp_enqueue_script( 'wp-travel-payment-frontend-script' );
	}
}

new WP_Travel_Frontend_Assets();
