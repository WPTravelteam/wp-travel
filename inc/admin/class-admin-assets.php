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

		// Register styles.
		wp_register_style( 'magnific-popup-css', $this->assets_path . 'assets/css/magnific-popup' . $suffix . '.css' );

		wp_enqueue_media();
		wp_enqueue_style( 'jquery-datepicker', $this->assets_path . 'assets/css/lib/datepicker/datepicker' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
		wp_register_style( 'select2-style', $this->assets_path . 'assets/css/lib/select2/select2' . $suffix . '.css', array(), '4.0.5' );

		wp_enqueue_style( 'wp-travel-tabs', $this->assets_path . 'assets/css/wp-travel-tabs' . $suffix . '.css', array( 'wp-color-picker' ), WP_TRAVEL_VERSION );
		wp_enqueue_style( 'wp-travel-back-end', $this->assets_path . 'assets/css/wp-travel-back-end' . $suffix . '.css', array(), WP_TRAVEL_VERSION );

		// wp_enqueue_style( 'jquery-multiple-select', $this->assets_path . 'assets/css/lib/multiple-select/multiple-select' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
		// fontawesome.
		wp_register_style( 'font-awesome-css', $this->assets_path . 'assets/css/lib/font-awesome/css/fontawesome-all' . $suffix . '.css' );

		$screen = get_current_screen();
		// Tab for settings page.
		$setting_allowed = array( 'itinerary-booking_page_wp-travel-marketplace', 'itinerary-booking_page_settings', 'wp-travel-coupons' );
		if ( in_array( $screen->id, $setting_allowed ) ) {
			wp_enqueue_style( 'font-awesome-css' );
			wp_enqueue_style( 'select2-style' );
		}
	}
	function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts.
		wp_register_script( 'jquery-datepicker-lib', $this->assets_path . 'assets/js/lib/datepicker/datepicker.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
		wp_register_script( 'jquery-datepicker-lib-eng', $this->assets_path . 'assets/js/lib/datepicker/i18n/datepicker.en.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );
		wp_register_script( 'select2-js', $this->assets_path . 'assets/js/lib/select2/select2' . $suffix . '.js', array( 'jquery' ), '4.0.5', true );
		wp_register_script( 'wp-travel-fields-script', $this->assets_path . 'assets/js/wp-travel-fields-scripts' . $suffix . '.js', array( 'select2-js' ), WP_TRAVEL_VERSION, true );
		wp_register_script( 'magnific-popup-script', $this->assets_path . 'assets/js/jquery.magnific-popup.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );
		wp_register_script( 'jquery-parsley', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'assets/js/lib/parsley/parsley.min.js', array( 'jquery' ), WP_TRAVEL_VERSION );

		$screen = get_current_screen();
		// Tab for settings page.
		$setting_allowed = array( 'itinerary-booking_page_wp-travel-marketplace', 'itinerary-booking_page_settings' );
		if ( in_array( $screen->id, $setting_allowed ) ) {
			wp_register_script( 'wp-travel-tabs', $this->assets_path . 'assets/js/wp-travel-tabs' . $suffix . '.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-ui-sortable', 'wp-color-picker', 'select2-js' ), WP_TRAVEL_VERSION, 1 );
			wp_enqueue_script( 'wp-travel-fields-script' );
			wp_enqueue_script( 'wp-travel-tabs' );
		}
		// @since 1.0.5 // booking stat
		if ( 'itinerary-booking_page_booking_chart' === $screen->id ) {
			wp_register_script( 'jquery-chart', $this->assets_path . 'assets/js/lib/chartjs/Chart.bundle' . $suffix . '.js', array( 'jquery' ), WP_TRAVEL_VERSION );
			wp_register_script( 'jquery-chart-util', $this->assets_path . 'assets/js/lib/chartjs/chart-utils.js', array( 'jquery' ), WP_TRAVEL_VERSION );

			wp_register_script( 'jquery-chart-custom', $this->assets_path . 'assets/js/lib/chartjs/chart-custom.js', array( 'jquery', 'jquery-chart', 'jquery-chart-util', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION );
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
			// $wp_travel_stat_data = apply_filters( 'wp_travel_stat_data', $wp_travel_stat_data );
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
			wp_register_script( 'wp-travel-moment', $this->assets_path . 'assets/js/moment.js', array( 'jquery' ), WP_TRAVEL_VERSION, 1 );

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

				wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps' . $suffix . '.js', array( 'jquery', 'google-map-api' ), WP_TRAVEL_VERSION, 1 );
				$depencency[] = 'jquery-gmaps';
			}
			wp_enqueue_script( 'wp-travel-script-2', $this->assets_path . 'assets/js/jquery.wptraveluploader' . $suffix . '.js', array( 'jquery' ), WP_TRAVEL_VERSION, true );

			// wp_enqueue_style( 'wp-travel-rtl-back-end', $this->assets_path . 'assets/css/wp-travel-rtl-back-end' . $suffix . '.css' );
			// wp_enqueue_script( 'multiple-select-js', $this->assets_path . 'assets/js/lib/multiple-select/multiple-select' . $suffix . '.js', array( 'jquery' ), '', 1 );
			wp_register_script( 'wp-travel-script', $this->assets_path . 'assets/js/wp-travel-back-end' . $suffix . '.js', $depencency, WP_TRAVEL_VERSION, 1 );

			wp_register_script( 'wp-travel-media-upload', $this->assets_path . 'assets/js/wp-travel-media-upload' . $suffix . '.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), WP_TRAVEL_VERSION, 1 );

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

			wp_enqueue_script( 'collapse-js', $this->assets_path . 'assets/js/collapse.js', array( 'jquery' ), WP_TRAVEL_VERSION );
		}
	}
}

new WP_Travel_Admin_Assets();
