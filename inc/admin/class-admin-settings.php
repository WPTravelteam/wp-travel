<?php
class WP_Travel_Admin_Settings {
	/**
	 * Parent slug.
	 *
	 * @var string
	 */
	private static $parent_slug = 'edit.php?post_type=itineraries';

	static $collection = 'settings';
	public function __construct() {
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_3' ), 11, 2 );
		add_action( 'load-itineraries_page_settings', array( $this, 'save_settings' ) );
	}

	public static function setting_page_callback() {
		$args['settings'] = get_option( 'wp_travel_settings' );
		$url_parameters['page'] = self::$collection;
		$url = admin_url( self::$parent_slug );
		$url = add_query_arg( $url_parameters, $url );
		echo '<div class="wrap wp-trave-settings-warp">';
				echo '<h1>' . __( 'WP Travel Settings', 'wp-travel' ) . '</h1>';
				echo '<div class="wp-trave-settings-form-warp">';
				// print_r( WP_Travel()->notices->get() );
				echo '<form method="post" action="' . esc_url( $url ) . '">';
					echo '<div class="wp-travel-setting-buttons">';
					submit_button( __( 'Save Settings', 'wp-travel' ), 'primary', 'save_settings_button', false );
					echo '</div>';
					WP_Travel()->tabs->load( self::$collection, $args );
					echo '<div class="wp-travel-setting-buttons">';
					echo '<input type="hidden" name="current_tab" id="wp-travel-settings-current-tab">';
					wp_nonce_field( 'wp_travel_settings_page_nonce' );
					submit_button( __( 'Save Settings', 'wp-travel' ), 'primary', 'save_settings_button', false );
					echo '</div>';
				echo '</form>';
			echo '</div>';
		echo '</div>';
	}

	function add_tabs( $tabs ) {
		$settings_fields['general'] = array(
			'tab_label' => __( 'General', 'wp_travel' ),
			'content_title' => __( 'General Settings', 'wp_travel' )
		);

		$settings_fields['currency'] = array(
			'tab_label' => __( 'Additional Info', 'wp_travel' ),
			'content_title' => __( 'Additional Info', 'wp_travel' ),
		);

		$tabs[ self::$collection ] = $settings_fields;
		return $tabs;
	}

	function call_back( $tab, $args ) {
		if ( 'general' !== $tab ) {
			return;
		}
		$currency_list = wp_traval_get_currency_list();
		$currency = isset( $args['settings']['currency'] ) ? $args['settings']['currency'] : '';
		$google_map_api_key = isset( $args['settings']['google_map_api_key'] ) ? $args['settings']['google_map_api_key'] : '';
		$currency_args = array(
			'id'		=> 'currency',
			'class'		=> 'currency',
			'name'		=> 'currency',
			'selected'	=> $currency,
			'option'	=> __( 'Select Currency' ),
			'options'	=> $currency_list,	
		);
		echo '<table class="form-table">';
			echo '<tr>';
				echo '<th>';
					echo '<label for="currency">Currency</label>';
				echo '</th>';
				echo '<td>';
					echo wp_traval_get_dropdown_currency_list( $currency_args );
					echo '<p class="description">' . esc_html( 'Choose your currency', 'wp-travel' ) . '</p>';
				echo '</td>';
			echo '<tr>';

			echo '<tr>';
				echo '<th>';
					echo '<label for="google_map_api_key">Google Map API Key</label>';
				echo '</th>';
				echo '<td>';
					echo '<input type="text" value="' . $google_map_api_key . '" name="google_map_api_key" id="google_map_api_key"/>';
					echo '<p class="description">' . sprintf( 'Don\'t have api key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a>', 'wp-travel' ) . '</p>';
				echo '</td>';
			echo '<tr>';
		echo '</table>';
	}
	function call_back_3( $tab ) {
		// echo $tab . "<br>3333";
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	function save_settings() {
		if ( isset( $_POST['save_settings_button'] ) ) {
			$current_tab = isset( $_POST['current_tab'] ) ? $_POST['current_tab'] : '';
			check_admin_referer( 'wp_travel_settings_page_nonce' );

			$currency = ( isset( $_POST['currency'] ) && '' !== $_POST['currency'] ) ? $_POST['currency'] : '';
			$settings['currency'] = $currency;

			$google_map_api_key = ( isset( $_POST['google_map_api_key'] ) && '' !== $_POST['google_map_api_key'] ) ? $_POST['google_map_api_key'] : '';
			$settings['google_map_api_key'] = $google_map_api_key;

			update_option( 'wp_travel_settings', $settings );
			WP_Travel()->notices->add( 'Aerror ' );
			$url_parameters['page'] = self::$collection;
			$url_parameters['updated'] = 'true';
			$redirect_url = admin_url( self::$parent_slug );
			$redirect_url = add_query_arg( $url_parameters, $redirect_url ) . '#' . $current_tab;
			// do_action( 'wp_travel_price_listing_save', $redirect_url );
			wp_redirect( $redirect_url );
			exit();
		}
	}
}

new WP_Travel_Admin_Settings();
