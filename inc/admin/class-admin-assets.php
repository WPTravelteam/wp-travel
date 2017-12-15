<?php
class WP_Travel_Admin_Assets {
	var $assets_path;
	public function __construct() {
		$this->assets_path = plugin_dir_url( WP_TRAVEL_PLUGIN_FILE );
		add_action( 'admin_enqueue_scripts', array( $this, 'styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
	}

	function styles() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$screen = get_current_screen();

		// if ( 'itineraries' !== $screen->id ) {
		// 	return;
		// }
		wp_enqueue_media();
		wp_enqueue_style( 'jquery-datepicker', $this->assets_path . 'assets/css/lib/datepicker/datepicker' . $suffix . '.css', array(), '2.2.3' );

		wp_enqueue_style( 'wp-travel-tabs', $this->assets_path . 'assets/css/wp-travel-tabs' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
		wp_enqueue_style( 'wp-travel-back-end', $this->assets_path . 'assets/css/wp-travel-back-end' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
	}
	function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		wp_register_script( 'jquery-datepicker-lib', $this->assets_path . 'assets/js/lib/datepicker/datepicker' . $suffix . '.js', array( 'jquery' ), '2.2.3', true );
		wp_register_script( 'jquery-datepicker-lib-eng', $this->assets_path . 'assets/js/lib/datepicker/i18n/datepicker.en.js', array( 'jquery' ), '', 1 );

		$screen = get_current_screen();
		// Tab for settings page.
		if ( 'itineraries_page_settings' == $screen->id ) {
			wp_register_script( 'wp-travel-tabs', $this->assets_path . 'assets/js/wp-travel-tabs' . $suffix . '.js', array( 'jquery', 'jquery-ui-tabs' ), WP_TRAVEL_VERSION, 1 );
			wp_enqueue_script( 'wp-travel-tabs' );
		}
		// @since 1.0.5 // booking stat
		if ( 'itineraries_page_booking_chart' === $screen->id ) {
			wp_register_script( 'jquery-chart', $this->assets_path . 'assets/js/lib/chartjs/Chart.bundle' . $suffix . '.js', array( 'jquery' ) );
			wp_register_script( 'jquery-chart-util', $this->assets_path . 'assets/js/lib/chartjs/chart-utils.js', array( 'jquery' ) );

			wp_register_script( 'jquery-chart-custom', $this->assets_path . 'assets/js/lib/chartjs/chart-custom.js', array( 'jquery', 'jquery-chart', 'jquery-chart-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ) );
			$booking_data = wp_travel_get_booking_data();

			$data = isset( $booking_data['data'] ) ? $booking_data['data'] : array();
			$labels = isset( $booking_data['labels'] ) ? $booking_data['labels'] : array();

			$max_bookings = isset( $booking_data['max_bookings'] ) ? $booking_data['max_bookings'] : 0;
			$max_pax = isset( $booking_data['max_pax'] ) ? $booking_data['max_pax'] : 0;
			$top_countries = ( isset( $booking_data['top_countries'] ) && count( $booking_data['top_countries'] )  > 0 ) ? $booking_data['top_countries'] : array( 'N/A' );
			$top_itinerary = ( isset( $booking_data['top_itinerary'] ) && count( $booking_data['top_itinerary'] )  > 0 ) ? $booking_data['top_itinerary'] : array( 'name' => esc_html__( 'N/A', 'wp-travel' ), 'url' => '' );

			// $data = array(5,10,3,6,9,8,2,11,13,9,7,15,16,16,13,18,20,21,26,15,13,16,18,7,15,16,16,6,9,8);
			// $labels = array('Dec 01, 2017','Dec 02, 2017','Dec 03, 2017','Dec 04, 2017','Dec 05, 2017','Dec 06, 2017','Dec 07, 2017','Dec 08, 2017','Dec 09, 2017','Dec 10, 2017','Dec 11, 2017','Dec 12, 2017','Dec 13, 2017','Dec 14, 2017','Dec 15, 2017','Dec 16, 2017','Dec 17, 2017','Dec 18, 2017','Dec 19, 2017','Dec 20, 2017','Dec 21, 2017','Dec 22, 2017','Dec 23, 2017','Dec 24, 2017','Dec 25, 2017','Dec 26, 2017','Dec 27, 2017','Dec 28, 2017','Dec 29, 2017','Dec 30, 2017');
			$wp_travel_chart_data = array(
				'ajax_url' => 'admin-ajax.php',
				'chart_title' => esc_html__( ' Chart Stat', 'wp-travel' ),
				'labels' => json_encode( $labels ),
				'datasets' => json_encode(
					array(
						array(
							'label' => esc_html__( 'Bookings', 'wp-travel' ),
							'backgroundColor' => '#00f',
							'borderColor' => '#00f',
							'data' => $data,
							'fill' => false,
						),
					)
				),
				'max_bookings' => $max_bookings,
				'max_pax' => $max_pax,
				'top_countries' => implode( ', ', $top_countries ),
				'top_itinerary' => $top_itinerary,
			);
			$wp_travel_chart_data = apply_filters( 'wp_travel_chart_data', $wp_travel_chart_data );
			wp_localize_script( 'jquery-chart-custom', 'wp_travel_chart_data', $wp_travel_chart_data );
			wp_enqueue_script( 'jquery-chart-custom' );
		}
		$allowed_screen = array( 'itineraries', 'edit-itineraries' );

		if ( in_array( $screen->id, $allowed_screen ) ) {
			$settings = wp_travel_get_settings();
			global $post;

			$map_data = get_wp_travel_map_data();

			$api_key = '';
			if ( isset( $settings['google_map_api_key'] ) ) {
				$api_key = $settings['google_map_api_key'];
			}
			$depencency = array( 'jquery', 'jquery-ui-tabs', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'wp-travel-media-upload' );
			if ( '' != $api_key ) {
				$depencency[] = 'jquery-gmaps';
			}
			wp_enqueue_script( 'travel-door-script-2', $this->assets_path . 'assets/js/jquery.wptraveluploader' . $suffix . '.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'travel-door-script', $this->assets_path . 'assets/js/wp-travel-back-end' . $suffix . '.js', $depencency, '', 1 );
			if ( '' != $api_key ) {
				wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), '', 1 );
				wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps' . $suffix . '.js', array( 'jquery', 'google-map-api' ), '', 1 );
			}
			wp_register_script( 'wp-travel-media-upload', $this->assets_path . 'assets/js/wp-travel-media-upload' . $suffix . '.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), '', 1 );

			$wp_travel_gallery_data = array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'lat' => $map_data['lat'],
				'lng' => $map_data['lng'],
				'loc' => $map_data['loc'],
				'labels' => array(
					'uploader_files_computer' => __( 'Select Files from Your Computer', 'wp-travel' ),
				),
				'drag_drop_nonce' => wp_create_nonce( 'wp-travel-drag-drop-nonce' ),
			);

			$wp_travel_gallery_data = apply_filters( 'wp_travel_localize_gallery_data', $wp_travel_gallery_data );
			wp_localize_script( 'wp-travel-media-upload', 'wp_travel_drag_drop_uploader', $wp_travel_gallery_data );

			// Enqueued script with localized data.
			wp_enqueue_script( 'travel-door-script' );
			wp_enqueue_script( 'wp-travel-media-upload' );
		}
	}
}

new WP_Travel_Admin_Assets();
