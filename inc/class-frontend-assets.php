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
		
	}
	function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$settings = wp_traval_get_settings();

		global $post;

		wp_enqueue_script( 'travel-door-booking', $this->assets_path . 'assets/js/booking.js', array( 'jquery' ) );
		// Script only for single itineraries.
		if ( ! is_singular( 'itineraries' ) ) {
			return;
		}
		$map_data = get_wp_travel_map_data();

		$api_key = '';
		if ( isset( $settings['google_map_api_key'] ) && '' != $settings['google_map_api_key'] ) {
			$api_key = $settings['google_map_api_key'];
		}

		wp_enqueue_script( 'travel-door-popup', $this->assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ) );
		wp_register_script( 'traval-door-script', $this->assets_path . 'assets/js/wp-travel-front-end.js', array( 'jquery', 'jquery-gmaps' ), '', true );
		if ( '' != $api_key ) {
			wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), '', 1 );
			wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps.min.js', array( 'jquery', 'google-map-api' ), '', 1 );
		}

		$wp_travel = array(
			'lat' => $map_data['lat'],
			'lng' => $map_data['lng'],
			'loc' => $map_data['loc'],
		);

		$wp_travel = apply_filters( 'wp_travel_frontend_data', $wp_travel );
		wp_localize_script( 'traval-door-script', 'wp_travel', $wp_travel );

		// Enqueued script with localized data.
		wp_enqueue_script( 'traval-door-script' );

		wp_enqueue_script( 'easy-responsive-tabs', $this->assets_path . 'assets/js/easy-responsive-tabs.js', array( 'jquery' ) );
	}
}

new WP_Travel_Frontend_Assets();
