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
		wp_enqueue_style( 'wp-travel-back-end', $this->assets_path . 'assets/css/wp-travel-back-end' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
		wp_enqueue_style( 'wp-travel-tabs', $this->assets_path . 'assets/css/wp-travel-tabs' . $suffix . '.css', array(), WP_TRAVEL_VERSION );
	}
	function scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$screen = get_current_screen();
		wp_register_script( 'wp-travel-tabs', $this->assets_path . 'assets/js/wp-travel-tabs.js', array( 'jquery', 'jquery-ui-tabs' ), WP_TRAVEL_VERSION, 1 );
		wp_enqueue_script( 'wp-travel-tabs' );
		if ( 'itineraries' === $screen->id ) {
			$settings = wp_traval_get_settings();
			global $post;

			$post_meta_lat = get_post_meta( $post->ID, 'wp_traval_lat', true );
			$post_meta_lng = get_post_meta( $post->ID, 'wp_traval_lng', true );
			$post_meta_loc = get_post_meta( $post->ID, 'wp_traval_location', true );

			$lat = -12.043333;
			if ( isset( $post_meta_lat ) && '' != $post_meta_lat ) {
				$lat = $post_meta_lat;
			}

			$lng = -77.028333;
			if ( isset( $post_meta_lng ) && '' != $post_meta_lng ) {
				$lng = $post_meta_lng;
			}

			$loc = __( 'Lima' );
			if ( isset( $post_meta_loc ) && '' != $post_meta_loc ) {
				$loc = $post_meta_loc;
			}

			$api_key = '';
			if ( isset( $settings['google_map_api_key'] ) ) {
				$api_key = $settings['google_map_api_key'];
			}
			wp_enqueue_script( 'traval-door-script-2', $this->assets_path . 'assets/js/jquery.wptraveluploader.js', array( 'jquery' ), '1.0.0', true );
			wp_register_script( 'traval-door-script', $this->assets_path . 'assets/js/wp-travel-back-end.js', array( 'jquery', 'jquery-ui-tabs', 'jquery-gmaps', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng', 'wp-travel-tabs', 'wp-travel-media-upload' ), '', 1 );
			wp_register_script( 'jquery-datepicker-lib', $this->assets_path . 'assets/js/lib/datepicker/datepicker.min.js', array( 'jquery' ), '2.2.3', true );
			wp_register_script( 'google-map-api', 'https://maps.google.com/maps/api/js?libraries=places&key=' . $api_key, array(), '', 1 );
			wp_register_script( 'jquery-gmaps', $this->assets_path . 'assets/js/lib/gmaps/gmaps.min.js', array( 'jquery', 'google-map-api' ), '', 1 );
			wp_register_script( 'jquery-datepicker-lib-eng', $this->assets_path . 'assets/js/lib/datepicker/i18n/datepicker.en.js', array( 'jquery' ), '', 1 );
			wp_register_script( 'wp-travel-media-upload', $this->assets_path . 'assets/js/wp-travel-media-upload.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'jquery-datepicker-lib', 'jquery-datepicker-lib-eng' ), '', 1 );

			$wp_travel_gallery_data = array(
				'ajax' => admin_url( 'admin-ajax.php' ),
				'lat' => $lat,
				'lng' => $lng,
				'loc' => $loc,
				'labels' => array(
					'uploader_files_computer' => __( 'Select Files from Your Computer', 'wp-travel' ),
				),
				'drag_drop_nonce' => wp_create_nonce( 'wp-travel-drag-drop-nonce' ),
			);

			$wp_travel_gallery_data = apply_filters( 'wp_travel_localize_gallery_data', $wp_travel_gallery_data );
			wp_localize_script( 'wp-travel-media-upload', 'wp_travel_drag_drop_uploader', $wp_travel_gallery_data );

			// Enqueued script with localized data.
			wp_enqueue_script( 'traval-door-script' );
			wp_enqueue_script( 'wp-travel-media-upload' );
		}
	}
}

new WP_Travel_Admin_Assets();
