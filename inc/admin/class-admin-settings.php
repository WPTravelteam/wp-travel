<?php
/**
 * Admin Settings.
 *
 * @package inc/admin
 */

/**
 * Class for admin settings.
 */
class WP_Travel_Admin_Settings {
	/**
	 * Parent slug.
	 *
	 * @var string
	 */
	private static $parent_slug = 'edit.php?post_type=itineraries';
	/**
	 * Page.
	 *
	 * @var string
	 */
	static $collection = 'settings';
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_itinerary' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_booking' ), 11, 2 );
		add_action( 'load-itineraries_page_settings', array( $this, 'save_settings' ) );
	}

	/**
	 * Call back function for page.
	 */
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

	/**
	 * Add Tabs to settings page.
	 *
	 * @param array $tabs Tabs array list.
	 */
	function add_tabs( $tabs ) {
		$settings_fields['general'] = array(
			'tab_label' => __( 'General', 'wp-travel' ),
			'content_title' => __( 'General Settings', 'wp-travel' ),
		);

		$settings_fields['itinerary'] = array(
			'tab_label' => __( 'Itinerary', 'wp-travel' ),
			'content_title' => __( 'Itinerary Settings', 'wp-travel' ),
		);

		$settings_fields['email'] = array(
			'tab_label' => __( 'Email', 'wp-travel' ),
			'content_title' => __( 'Email Settings', 'wp-travel' ),
		);

		$tabs[ self::$collection ] = $settings_fields;
		return $tabs;
	}

	/**
	 * Callback for General tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function call_back( $tab, $args ) {
		if ( 'general' !== $tab ) {
			return;
		}
		$currency_list = wp_traval_get_currency_list();
		$currency = ( isset( $args['settings']['currency'] ) && '' != $args['settings']['currency'] ) ? $args['settings']['currency'] : 'USD';
		$google_map_api_key = isset( $args['settings']['google_map_api_key'] ) ? $args['settings']['google_map_api_key'] : '';
		$currency_args = array(
			'id'		=> 'currency',
			'class'		=> 'currency',
			'name'		=> 'currency',
			'selected'	=> $currency,
			'option'	=> __( 'Select Currency', 'wp-travel' ),
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

	/**
	 * Callback for Itinerary tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function call_back_tab_itinerary( $tab, $args ) {
		if ( 'itinerary' !== $tab ) {
			return;
		}
		$hide_related_itinerary = isset( $args['settings']['hide_related_itinerary'] )  ? $args['settings']['hide_related_itinerary'] : 'no';
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Hide related itinerary', 'wp-travel' ); ?></label>
				</th>
				<td>
					<input type="checkbox" <?php checked( $hide_related_itinerary , 'yes' ); ?> value="1" name="hide_related_itinerary" id="hide_related_itinerary"/>
					<p class="description"><?php esc_html_e( 'This will hide your related itineraries.', 'wp-travel' ) ?></p>
				</td>
			<tr>
		</table>
	<?php
	}

	/**
	 * Callback for Email tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function call_back_tab_booking( $tab, $args ) {
		if ( 'email' !== $tab ) {
			return;
		}
		$send_booking_email_to_admin = isset( $args['settings']['send_booking_email_to_admin'] ) ? $args['settings']['send_booking_email_to_admin'] : 'yes';
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Send Booking mail to admin', 'wp-travel' ); ?></label>
				</th>
				<td>
					<input type="checkbox" <?php checked( $send_booking_email_to_admin , 'yes' ); ?> value="1" name="send_booking_email_to_admin" id="send_booking_email_to_admin"/>
					
				</td>
			<tr>
		</table>
	<?php
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

			$currency 				= ( isset( $_POST['currency'] ) && '' !== $_POST['currency'] ) ? $_POST['currency'] : '';
			$google_map_api_key 	= ( isset( $_POST['google_map_api_key'] ) && '' !== $_POST['google_map_api_key'] ) ? $_POST['google_map_api_key'] : '';

			$hide_related_itinerary = ( isset( $_POST['hide_related_itinerary'] ) && '' !== $_POST['hide_related_itinerary'] ) ? 'yes' : 'no';
			$send_booking_email_to_admin = ( isset( $_POST['send_booking_email_to_admin'] ) && '' !== $_POST['send_booking_email_to_admin'] ) ? 'yes' : 'no';
			$settings['currency'] = $currency;
			$settings['google_map_api_key'] = $google_map_api_key;
			$settings['hide_related_itinerary'] = $hide_related_itinerary;
			$settings['send_booking_email_to_admin'] = $send_booking_email_to_admin;

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
