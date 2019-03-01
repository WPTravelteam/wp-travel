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
	public static $parent_slug;

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

		self::$parent_slug = 'edit.php?post_type=itinerary-booking';
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_itinerary' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_booking' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_account_settings_tab_callback' ), 12, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_global_settings' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'misc_options_tab_callback' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_facts' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_payment_tab_call_back' ), 12, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_debug_tab_call_back' ), 12, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_license_tab_call_back' ), 12, 2 );

		add_action( 'load-itinerary-booking_page_settings', array( $this, 'save_settings' ) );
	}

	public function call_back_tab_facts( $tab ) {
		if ( 'facts' !== $tab ) {
			return;
		}
		require_once 'views/tabs/tab-contents/itineraries/fact-setting-tab.php';
	}

	/**
	 * Call back function for page.
	 */
	public static function setting_page_callback() {
		$args['settings']       = get_option( 'wp_travel_settings' );
		$url_parameters['page'] = self::$collection;
		$url                    = admin_url( self::$parent_slug );
		$url                    = add_query_arg( $url_parameters, $url );
		$sysinfo_url            = add_query_arg( array( 'page' => 'sysinfo' ), $url );
		echo '<div class="wrap wp-trave-settings-warp">';
				echo '<h1>' . __( 'WP Travel Settings', 'wp-travel' ) . '</h1>';
				echo '<div class="wp-trave-settings-form-warp">';
				// print_r( WP_Travel()->notices->get() );
				do_action( 'wp_travel_before_admin_setting_form' );
				echo '<form method="post" action="' . esc_url( $url ) . '">';
					echo '<div class="wp-travel-setting-buttons">';
					submit_button( __( 'Save Settings', 'wp-travel' ), 'primary', 'save_settings_button', false, array( 'id' => 'save_settings_button_top' ) );
					echo '</div>';
					WP_Travel()->tabs->load( self::$collection, $args );
					echo '<div class="wp-travel-setting-buttons">';
					echo '<div class="wp-travel-setting-system-info">';
						echo '<a href="' . esc_url( $sysinfo_url ) . '" title="' . __( 'View system information', 'wp-travel' ) . '"><span class="dashicons dashicons-info"></span>';
							esc_html_e( 'System Information', 'wp-travel' );
						echo '</a>';
					echo '</div>';
					echo '<input type="hidden" name="current_tab" id="wp-travel-settings-current-tab">';
					wp_nonce_field( 'wp_travel_settings_page_nonce' );
					submit_button( __( 'Save Settings', 'wp-travel' ), 'primary', 'save_settings_button', false );
					echo '</div>';
				echo '</form>';
				do_action( 'wp_travel_after_admin_setting_form' );
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
			'tab_label'     => __( 'General', 'wp-travel' ),
			'content_title' => __( 'General Settings', 'wp-travel' ),
			'priority'			=> 10
		);

		$settings_fields['itinerary'] = array(
			'tab_label'     => ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ),
			'content_title' => __( ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ) . ' Settings', 'wp-travel' ),
			'priority'			=> 20
		);

		$settings_fields['email'] = array(
			'tab_label'     => __( 'Email', 'wp-travel' ),
			'content_title' => __( 'Email Settings', 'wp-travel' ),
		);

		$settings_fields['account_options_global'] = array(
			'tab_label'     => __( 'Account Settings', 'wp-travel' ),
			'content_title' => __( 'Account Settings', 'wp-travel' ),
			'priority'			=> 30
		);

		$settings_fields['tabs_global']         = array(
			'tab_label'     => __( 'Tabs', 'wp-travel' ),
			'content_title' => __( 'Global Tabs Settings', 'wp-travel' ),
			'priority'			=> 40
		);
		$settings_fields['payment']             = array(
			'tab_label'     => __( 'Payment', 'wp-travel' ),
			'content_title' => __( 'Payment Settings', 'wp-travel' ),
			'priority'			=> 50
		);
		$settings_fields['facts']               = array(
			'tab_label'     => __( 'Facts', 'wp-travel' ),
			'content_title' => __( 'Facts Settings', 'wp-travel' ),
			'priority'			=> 60
		);
		$settings_fields['license']             = array(
			'tab_label'     => __( 'License', 'wp-travel' ),
			'content_title' => __( 'License Details', 'wp-travel' ),
			'priority'			=> 70
		);
		$settings_fields['misc_options_global'] = array(
			'tab_label'     => __( 'Misc. Options', 'wp-travel' ),
			'content_title' => __( 'Miscellanaous Options', 'wp-travel' ),
			'priority'			=> 80
		);
		$settings_fields['debug']               = array(
			'tab_label'     => __( 'Debug', 'wp-travel' ),
			'content_title' => __( 'Debug Options', 'wp-travel' ),
			'priority'			=> 90
		);

		$tabs[ self::$collection ] = wp_travel_sort_array_by_priority( apply_filters( 'wp_travel_settings_tabs', $settings_fields ) );
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
		$currency_list      = wp_travel_get_currency_list();
		$currency           = ( isset( $args['settings']['currency'] ) && '' != $args['settings']['currency'] ) ? $args['settings']['currency'] : 'USD';
		$google_map_api_key = isset( $args['settings']['google_map_api_key'] ) ? $args['settings']['google_map_api_key'] : '';

		$google_map_zoom_level = isset( $args['settings']['google_map_zoom_level'] ) ? $args['settings']['google_map_zoom_level'] : 15;

		$selected_cart_page = isset( $args['settings']['cart_page_id'] ) ? $args['settings']['cart_page_id'] : wp_travel_get_page_id( 'wp-travel-cart' );

		$selected_checkout_page  = isset( $args['settings']['checkout_page_id'] ) ? $args['settings']['checkout_page_id'] : wp_travel_get_page_id( 'wp-travel-checkout' );
		$selected_dashboard_page = isset( $args['settings']['dashboard_page_id'] ) ? $args['settings']['dashboard_page_id'] : wp_travel_get_page_id( 'wp-travel-dashboard' );

		$currency_args = array(
			'id'         => 'currency',
			'class'      => 'currency wp-travel-select2',
			'name'       => 'currency',
			'selected'   => $currency,
			'option'     => __( 'Select Currency', 'wp-travel' ),
			'options'    => $currency_list,
			'attributes' => array(
				'style' => 'width: 300px;',
			),
		);

		$map_data       = wp_travel_get_maps();
		$wp_travel_maps = $map_data['maps'];
		$selected_map   = $map_data['selected'];

		$map_dropdown_args = array(
			'id'           => 'wp-travel-map-select',
			'class'        => 'wp-travel-select2',
			'name'         => 'wp_travel_map',
			'option'       => '',
			'options'      => $wp_travel_maps,
			'selected'     => $selected_map,
			'before_label' => '',
			'after_label'  => '',
			'attributes'   => array(
				'style' => 'width: 300px;',
			),
		);
		$map_key           = 'google-map';
		?>
		<table class="form-table">
			<tr>
				<th><label for="currency"><?php echo esc_html__( 'Currency', 'wp-travel' ); ?></label></th>
				<td>
					<?php echo wp_travel_get_dropdown_currency_list( $currency_args ); ?>
					<p class="description"><?php echo esc_html__( 'Choose your currency', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th clospan="2">
					<h3><?php esc_html_e( 'Maps', 'wp-travel' ); ?></h3>
				</th>
			</tr>
			<tr>
				<th><label for="wp-travel-map"><?php echo esc_html__( 'Select Map', 'wp-travel' ); ?></label></th>
				<td>
					<?php echo wp_travel_get_dropdown_list( $map_dropdown_args ); ?>
					<p class="description"><?php echo esc_html__( 'Choose your map', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<?php do_action( 'wp_travel_settings_after_currency', $tab, $args ); ?>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_api_key"><?php echo esc_html__( 'Google Map API Key', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $google_map_api_key ); ?>" name="google_map_api_key" id="google_map_api_key"/>
					<p class="description"><?php echo sprintf( 'Don\'t have api key <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">click here</a>', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr class="wp-travel-map-option <?php echo esc_attr( $map_key ); ?>">
				<th><label for="google_map_zoom_level"><?php echo esc_html__( 'Map Zoom Level', 'wp-travel' ); ?></label></th>
				<td>
					<input step="1" min="1" type="number" value="<?php echo esc_attr( $google_map_zoom_level ); ?>" name="google_map_zoom_level" id="google_map_zoom_level"/>
				</td>
			</tr>
		</table>
		<div class="wp-travel-upsell-message">
			<div class="wp-travel-pro-feature-notice">
				<h4><?php esc_html_e( 'Need alternative maps ?', 'wp-travel' ); ?></h4>
				<p><?php printf( __( 'If you need alternative to current map then you can get free or pro maps for WP Travel.  %1$sView WP Travel Map addons%2$s', 'wp-travel' ), '<br><a target="_blank" href="https://wptravel.io/downloads/category/map/">', '</a>' ); ?></p>
			</div>
		</div>
		<br>
		<h3 class="wp-travel-tab-content-title"><?php echo esc_html__( 'Pages', 'wp-travel' ); ?></h3>

		<table class="form-table">
			<tr>
				<th><label for="cart-page-id"><?php echo esc_html__( 'Cart Page', 'wp-travel' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'depth'                 => 0,
							'child_of'              => 0,
							'selected'              => $selected_cart_page,
							'echo'                  => 1,
							'name'                  => 'cart_page_id',
							'id'                    => 'cart-page-id', // string
							'class'                 => 'wp-travel-select2', // string
							'show_option_none'      => null, // string
							'show_option_no_change' => null, // string
							'option_none_value'     => null, // string
						)
					);
					?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as cart page for trip bookings which contents cart page shortcode [wp_travel_cart]', 'wp-travel' ); ?></p>
				</td>
			<tr>

			<tr>
				<th><label for="checkout-page-id"><?php echo esc_html__( 'Checkout Page', 'wp-travel' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'depth'                 => 0,
							'child_of'              => 0,
							'selected'              => $selected_checkout_page,
							'echo'                  => 1,
							'name'                  => 'checkout_page_id',
							'id'                    => 'checkout-page-id', // string
							'class'                 => 'wp-travel-select2', // string
							'show_option_none'      => null, // string
							'show_option_no_change' => null, // string
							'option_none_value'     => null, // string
						)
					);
					?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as checkout page for booking which contents checkout page shortcode [wp_travel_checkout]', 'wp-travel' ); ?></p>
				</td>
			<tr>
			<tr>
				<th><label for="dashboard-page-id"><?php echo esc_html__( 'Dashboard Page', 'wp-travel' ); ?></label></th>
				<td>
					<?php
					wp_dropdown_pages(
						array(
							'depth'                 => 0,
							'child_of'              => 0,
							'selected'              => $selected_dashboard_page,
							'echo'                  => 1,
							'name'                  => 'dashboard_page_id',
							'id'                    => 'dashboard-page-id', // string
							'class'                 => 'wp-travel-select2', // string
							'show_option_none'      => null, // string
							'show_option_no_change' => null, // string
							'option_none_value'     => null, // string
						)
					);
					?>
					<p class="description"><?php echo esc_html__( 'Choose the page to use as dashboard page which contents dashboard page shortcode [wp_travel_user_account].', 'wp-travel' ); ?></p>
				</td>
			<tr>
			<?php
			/**
			 * Hook.
			 *
			 * @since 1.8.0
			 */
			do_action( 'wp_travel_after_page_settings', $tab, $args )
			?>
		</table>
		<?php
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
		$hide_related_itinerary      = isset( $args['settings']['hide_related_itinerary'] ) ? $args['settings']['hide_related_itinerary'] : 'no';
		$enable_multiple_travellers  = isset( $args['settings']['enable_multiple_travellers'] ) ? $args['settings']['enable_multiple_travellers'] : 'no';
		$trip_pricing_options_layout = wp_travel_get_pricing_option_listing_type( $args['settings'] );
		?>
		<?php do_action( 'wp_travel_tab_content_before_trips', $args ); ?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency">
					<?php
					esc_html_e( 'Hide related ', 'wp-travel' );
					echo esc_attr( WP_TRAVEL_POST_TITLE );
					?>
					</label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $hide_related_itinerary, 'yes' ); ?> value="1" name="hide_related_itinerary" id="hide_related_itinerary" type="checkbox" />
							<span class="switch"></span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( sprintf( 'This will hide your related %s.', WP_TRAVEL_POST_TITLE ), 'wp-travel' ); ?></p>
				</td>
			<tr>
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Enable multiple travelers', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $enable_multiple_travellers, 'yes' ); ?> value="1" name="enable_multiple_travellers" id="enable_multiple_travellers" type="checkbox" />
							<span class="switch"></span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( sprintf( 'Check to enable.' ), 'wp-travel' ); ?></p>
				</td>
			<tr>
			<tr id="wp-travel-tax-price-options" >
				<th><label><?php esc_html_e( 'Trip Pricing Options Listing', 'wp-travel' ); ?></label></th>
				<td>
					<label><input <?php checked( 'by-pricing-option', $trip_pricing_options_layout ); ?> name="trip_pricing_options_layout" value="by-pricing-option" type="radio">
					<?php esc_html_e( 'List by pricing options ( Default )', 'wp-travel' ); ?></label>

					<label> <input <?php checked( 'by-date', $trip_pricing_options_layout ); ?> name="trip_pricing_options_layout" value="by-date" type="radio">
					<?php esc_html_e( 'List by fixed departure dates', 'wp-travel' ); ?></label>

					<p class="description"><?php esc_html_e( 'This options will control how you display trip dates and prices.', 'wp-travel' ); ?></p>

				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_tab_content_after_trips', $args ); ?>

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

		// Booking Admin Defaults.
		$booking_admin_email_defaults = array(

			'admin_subject'      => __( 'New Booking', 'wp-travel' ),
			'admin_title'        => __( 'New Booking', 'wp-travel' ),
			'admin_header_color' => '',
			'email_content'      => '',
			'from_email'         => get_option( 'admin_email' ),

		);

		// Booking client Defaults.
		$booking_client_email_defaults = array(

			'client_subject'      => __( 'Booking Recieved', 'wp-travel' ),
			'client_title'        => __( 'Booking Recieved', 'wp-travel' ),
			'client_header_color' => '',
			'email_content'       => '',
			'from_email'          => get_option( 'admin_email' ),

		);

		// Payment Admin Defaults.
		$payment_admin_email_defaults = array(

			'admin_subject'      => __( 'New Booking', 'wp-travel' ),
			'admin_title'        => __( 'New Booking', 'wp-travel' ),
			'admin_header_color' => '',
			'email_content'      => '',
			'from_email'         => get_option( 'admin_email' ),

		);

		// Payment client Defaults.
		$payment_client_email_defaults = array(

			'client_subject'      => __( 'Payment Recieved', 'wp-travel' ),
			'client_title'        => __( 'Payment Recieved', 'wp-travel' ),
			'client_header_color' => '',
			'email_content'       => '',
			'from_email'          => get_option( 'admin_email' ),

		);

		// emquiry Admin Defaults.
		$enquiry_admin_email_defaults = array(

			'admin_subject'      => __( 'New Enquiry', 'wp-travel' ),
			'admin_title'        => __( 'New Enquiry', 'wp-travel' ),
			'admin_header_color' => '',
			'email_content'      => '',
			'from_email'         => get_option( 'admin_email' ),

		);
		// Booking Admin Email.
		$booking_admin_email_settings = isset( $args['settings']['booking_admin_template_settings'] ) ? $args['settings']['booking_admin_template_settings'] : $booking_admin_email_defaults;

		// Booking Client Email.
		$booking_client_email_settings = isset( $args['settings']['booking_client_template_settings'] ) ? $args['settings']['booking_client_template_settings'] : $booking_client_email_defaults;

		// Payment Admin Email.
		$payment_admin_email_settings = isset( $args['settings']['payment_admin_template_settings'] ) ? $args['settings']['payment_admin_template_settings'] : $payment_admin_email_defaults;

		// Payment Client Email.
		$payment_client_email_settings = isset( $args['settings']['payment_client_template_settings'] ) ? $args['settings']['payment_client_template_settings'] : $payment_client_email_defaults;

		// Enquiry Admin Email.
		$enquiry_admin_email_settings = isset( $args['settings']['enquiry_admin_template_settings'] ) ? $args['settings']['enquiry_admin_template_settings'] : $enquiry_admin_email_defaults;

		?>
		<?php do_action( 'wp_travel_tab_content_before_email', $args ); ?>
		<?php if ( ! class_exists( 'WP_Travel_Utilities' ) ) : ?>
			<div class="wp-travel-upsell-message">
				<div class="wp-travel-pro-feature-notice">
					<h4><?php esc_html_e( 'Want to get more e-mail customization options ?', 'wp-travel' ); ?></h4>
					<p><?php esc_html_e( 'By upgrading to Pro, you can get features like multiple email notifications, email footer powered by text removal options and more !', 'wp-travel' ); ?></p>
					<a target="_blank" href="https://themepalace.com/downloads/wp-travel-utilites/"><?php esc_html_e( 'Get WP Travel Utilities Addon', 'wp-travel' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
		<table class="form-table">
			<tr><td colspan="2" ><h4 class="wp-travel-tab-content-title"><?php esc_html_e( 'General Options', 'wp-travel' ); ?></h4></td></tr>

			<tr>
				<th>
					<label for="wp_travel_global_from_email"><?php esc_html_e( 'From Email', 'wp-travel' ); ?></label>
				</th>
				<td>
					<input value="<?php echo isset( $args['settings']['wp_travel_from_email'] ) ? $args['settings']['wp_travel_from_email'] : get_option( 'admin_email' ); ?>" type="email" name="wp_travel_from_email" id="wp_travel_global_from_email">
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_tab_content_before_booking_tamplate', $args ); ?>
		<div class="wp-collapse-open clearfix">
			<a href="#" class="open-all-link"><span class="open-all" id="open-all"><?php esc_html_e( 'Open All', 'wp-travel' ); ?></span></a>
			<a style="display:none;" href="#" class="close-all-link"><span class="close-all" id="close-all"><?php esc_html_e( 'Close All', 'wp-travel' ); ?></span></a>
		</div>

		<div id="wp-travel-email-global-accordion" class="email-global-accordion tab-accordion">
			<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingOne">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								<?php esc_html_e( 'Booking Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseOne" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne">
						<div class="panel-body">
							<div class="panel-wrap">

							<div class="wp-travel-email-template-options">

							<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

								<table class="form-table">
									<tr>
										<th>
											<label for="send_booking_email_to_admin"><?php esc_html_e( 'Send Booking mail to admin', 'wp-travel' ); ?></label>
										</th>
										<td>
											<span class="show-in-frontend checkbox-default-design">
												<label data-on="ON" data-off="OFF">
													<input <?php checked( $send_booking_email_to_admin, 'yes' ); ?> value="1" name="send_booking_email_to_admin" id="send_booking_email_to_admin" type="checkbox" />
													<span class="switch"></span>
												</label>
											</span>
										</td>
									</tr>
									<?php do_action( 'wp_travel_utils_booking_notif' ); ?>
									<tr>
										<th>
											<label for="booking-admin-email-sub"><?php esc_html_e( 'Booking Email Subject', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input value="<?php echo $booking_admin_email_settings['admin_subject']; ?>" type="text" name="booking_admin_template[admin_subject]" id="booking-admin-email-sub">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input type="text" value="<?php echo $booking_admin_email_settings['admin_title']; ?>" name="booking_admin_template[admin_title]" id="booking-admin-email-title">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input class="wp-travel-color-field" value = "<?php echo $booking_admin_email_settings['admin_header_color']; ?>" type="text" name="booking_admin_template[admin_header_color]" id="booking-admin-email-header-color">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
										</th>
										<td>
											<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $booking_admin_email_settings['email_content'] ) && '' !== $booking_admin_email_settings['email_content'] ? $booking_admin_email_settings['email_content'] : wp_travel_booking_admin_default_email_content();
												wp_editor( $content, 'booking_admin_email_content', $settings = array( 'textarea_name' => 'booking_admin_template[email_content]' ) );
												?>
											</div>
										</td>
									</tr>

									<?php
										/**
										 * Add Support Multiple Booking admin Template.
										 */
										do_action( 'wp_travel_multiple_booking_admin_template', $booking_admin_email_settings );
									?>

								</table>

							<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

								<table class="form-table">
									<tr>
										<th>
											<label for="booking-client-email-sub"><?php esc_html_e( 'Booking Client Email Subject', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input value="<?php echo $booking_client_email_settings['client_subject']; ?>" type="text" name="booking_client_template[client_subject]" id="booking-client-email-sub">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input type="text" value="<?php echo $booking_client_email_settings['client_title']; ?>" name="booking_client_template[client_title]" id="booking-client-email-title">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input class="wp-travel-color-field" value = "<?php echo $booking_client_email_settings['client_header_color']; ?>" type="text" name="booking_client_template[client_header_color]" id="booking-client-email-header-color">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
										</th>
										<td>
											<div class="wp_travel_admin_editor">
												<?php
												$content = isset( $booking_client_email_settings['email_content'] ) && '' !== $booking_client_email_settings['email_content'] ? $booking_client_email_settings['email_content'] : wp_travel_booking_client_default_email_content();
												wp_editor( $content, 'booking_client_email_content', $settings = array( 'textarea_name' => 'booking_client_template[email_content]' ) );
												?>
											</div>
										</td>
									</tr>

									<?php
										/**
										 * Add Support Multiple Booking client Template.
										 */
										do_action( 'wp_travel_multiple_booking_client_template', $booking_client_email_settings );
									?>

								</table>

							</div>

							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingTwo">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="collapsed" aria-expanded="true" aria-controls="collapseTwo">
								<?php esc_html_e( 'Payment Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="wp-travel-email-template-options">

								<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
									<?php do_action( 'wp_travel_utils_payment_notif' ); ?>
										<tr>
											<th>
												<label for="payment-admin-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_admin_email_settings['admin_subject']; ?>" type="text" name="payment_admin_template[admin_subject]" id="payment-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_admin_email_settings['admin_title']; ?>" name="payment_admin_template[admin_title]" id="payment-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $payment_admin_email_settings['admin_header_color']; ?>" type="text" name="payment_admin_template[admin_header_color]" id="payment-admin-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
													<?php
													$content = isset( $payment_admin_email_settings['email_content'] ) && '' !== $payment_admin_email_settings['email_content'] ? $payment_admin_email_settings['email_content'] : wp_travel_payment_admin_default_email_content();
													wp_editor( $content, 'payment_admin_email_content', $settings = array( 'textarea_name' => 'payment_admin_template[email_content]' ) );
													?>
												</div>
											</td>
										</tr>

										<?php
										/**
										 * Add Support Multiple payment admin Template.
										 */
										do_action( 'wp_travel_multiple_payment_admin_template', $payment_admin_email_settings );
										?>

									</table>

									<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="payment-client-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_client_email_settings['client_subject']; ?>" type="text" name="payment_client_template[client_subject]" id="payment-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_client_email_settings['client_title']; ?>" name="payment_client_template[client_title]" id="payment-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $payment_client_email_settings['client_header_color']; ?>" type="text" name="payment_client_template[client_header_color]" id="payment-client-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
													<?php
													$content = isset( $payment_client_email_settings['email_content'] ) && '' !== $payment_client_email_settings['email_content'] ? $payment_client_email_settings['email_content'] : wp_travel_payment_client_default_email_content();
													wp_editor( $content, 'payment_client_email_content', $settings = array( 'textarea_name' => 'payment_client_template[email_content]' ) );
													?>
												</div>
											</td>
										</tr>

										<?php
										/**
										 * Add Support Multiple Payment client Template.
										 */
										do_action( 'wp_travel_multiple_payment_client_template', $payment_client_email_settings );
										?>

									</table>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingThree">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" class="collapsed" aria-expanded="true" aria-controls="collapseThree">
								<?php esc_html_e( 'Enquiry Email Templates', 'wp-travel' ); ?>
								<span class="collapse-icon"></span>
							</a>
						</h4>
					</div>
					<div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
						<div class="panel-body">
							<div class="panel-wrap">
								<div class="wp-travel-email-template-options">

								<h3 class="section-heading"><?php esc_html_e( 'Admin Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<?php do_action( 'wp_travel_utils_enquiries_notif' ); ?>
										<tr>
											<th>
												<label for="enquiry-admin-email-sub"><?php esc_html_e( 'Enquiry Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $enquiry_admin_email_settings['admin_subject']; ?>" type="text" name="enquiry_admin_template[admin_subject]" id="enquiry-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-title"><?php esc_html_e( 'Enquiry Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $enquiry_admin_email_settings['admin_title']; ?>" name="enquiry_admin_template[admin_title]" id="enquiry-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-header-color"><?php esc_html_e( 'Enquiry Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $enquiry_admin_email_settings['admin_header_color']; ?>" type="text" name="enquiry_admin_template[admin_header_color]" id="enquiry-admin-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<div class="wp_travel_admin_editor">
													<?php
													$content = isset( $enquiry_admin_email_settings['email_content'] ) && '' !== $enquiry_admin_email_settings['email_content'] ? $enquiry_admin_email_settings['email_content'] : wp_travel_enquiries_admin_default_email_content();
													wp_editor( $content, 'enquiry_admin_email_content', $settings = array( 'textarea_name' => 'enquiry_admin_template[email_content]' ) );
													?>
												</div>
											</td>
										</tr>

									</table>

									<?php do_action( 'wp_travel_enquiry_customer_email_settings' ); ?>

								</div>

							</div>
						</div>
					</div>
				</div>

				<?php
				// @since 1.8.0
				do_action( 'wp_travel_email_template_settings_after_enquiry', $tab, $args )
				?>
			</div>
		</div>

		<?php
	}

	/**
	 * Callback for Global tabs settings.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function call_back_tab_global_settings( $tab, $args ) {
		if ( 'tabs_global' !== $tab ) {
			return;
		}
		$global_tabs = isset( $args['settings']['global_tab_settings'] ) ? $args['settings']['global_tab_settings'] : '';

		if ( empty( $global_tabs ) ) {

			// Fallback to default Tabs.
			$global_tabs = wp_travel_get_default_frontend_tabs();

		}

		$custom_tab_enabled = apply_filters( 'wp_travel_is_custom_tabs_support_enabled', false );
		?>

		<?php if ( ! class_exists( 'WP_Travel_Utilities' ) ) : ?>
			<div class="wp-travel-upsell-message">
				<div class="wp-travel-pro-feature-notice">
					<h4><?php esc_html_e( 'Need Additional Tabs ?', 'wp-travel' ); ?></h4>
					<p><?php esc_html_e( 'By upgrading to Pro, you can get global custom tab addition options with customized content and sorting options !', 'wp-travel' ); ?></p>
					<a target="_blank" href="https://themepalace.com/downloads/wp-travel-utilites/"><?php esc_html_e( 'Get WP Travel Utilities Addon', 'wp-travel' ); ?></a>
				</div>
			</div>
		<?php endif; ?>
		<?php
		if ( is_array( $global_tabs ) && count( $global_tabs ) > 0 && ! $custom_tab_enabled ) {
			echo '<table class="wp-travel-sorting-tabs form-table">';
			?>
				<thead>
					<th width="50px"><?php esc_html_e( 'Sorting', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Global Trip Title', 'wp-travel' ); ?></th>
					<th width="35%"><?php esc_html_e( 'Custom Trip Title', 'wp-travel' ); ?></th>
					<th width="20%"><?php esc_html_e( 'Display', 'wp-travel' ); ?></th>
				</thead>
				<tbody>
			<?php
			foreach ( $global_tabs as $key => $tab ) :
				?>
				<tr>
					<td width="50px">
						<div class="wp-travel-sorting-handle">
						</div>
					</td>
					<td width="35%">
						<div class="wp-travel-sorting-tabs-wrap">
						<span class="wp-travel-tab-label wp-travel-accordion-title"><?php echo esc_html( $tab['label'] ); ?></span>
					</div>
					</td>
					<td width="35%">
						<div class="wp-travel-sorting-tabs-wrap">
						<input type="text" class="wp_travel_tabs_input-field section_title" name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['label'] ); ?>" />
						<input type="hidden" name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" value="no" />

					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design"><label data-on="ON" data-off="OFF"><input name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ); ?> /><?php // esc_html_e( 'Display', 'wp-travel' ); ?>
						<span class="switch">
						  </span>
						</label></span>
					</td>
				</tr>
				<?php
				endforeach;

			echo '<tbody></table>';
		}

		// Add custom Tabs Support.
		do_action( 'wp_travel_custom_global_tabs' );
	}

	/**
	 * Callback for Options Tab
	 */
	function misc_options_tab_callback( $tab, $args ) {

		if ( 'misc_options_global' !== $tab ) {
			return;
		}
		$enable_trip_enquiry_option = isset( $args['settings']['enable_trip_enquiry_option'] ) ? $args['settings']['enable_trip_enquiry_option'] : 'yes';
		$enable_og_tags             = isset( $args['settings']['enable_og_tags'] ) ? $args['settings']['enable_og_tags'] : 'no';
		$open_gdpr_in_new_tab       = isset( $args['settings']['open_gdpr_in_new_tab'] ) ? $args['settings']['open_gdpr_in_new_tab'] : 'no';
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Enable Trip Enquiry', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $enable_trip_enquiry_option, 'yes' ); ?> value="1" name="enable_trip_enquiry_option" id="enable_trip_enquiry_option" type="checkbox" />
							<span class="switch">
						  </span>
						</label>
					</span>
				</td>
			<tr>
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Enable OG Tags', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $enable_og_tags, 'yes' ); ?> value="1" name="enable_og_tags" id="enable_og_tags" type="checkbox" />
							<span class="switch">
						  </span>
						</label>
					</span>
				</td>
			<tr>
			<tr>
				<th>
					<label for="wp_travel_gdpr_message"><?php _e( 'GDPR Message : ', 'wp-travel' ); ?></label>
				</th>
				<td>
					<textarea rows="4" cols="30" id="wp_travel_gdpr_message" name="wp_travel_gdpr_message"><?php echo isset( $args['settings']['wp_travel_gdpr_message'] ) ? esc_attr( $args['settings']['wp_travel_gdpr_message'] ) : 'By contacting us, you agree to our '; ?></textarea>
				</td>
			</tr>
			<tr>
				<th>
					<label for="wp_travel_gdpr_message"><?php _e( 'Open GDPR in new tab: ', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $open_gdpr_in_new_tab, 'yes' ); ?> value="1" name="open_gdpr_in_new_tab" id="open_gdpr_in_new_tab" type="checkbox" />
							<span class="switch">
						  </span>
						</label>
					</span>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Callback for Payment tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function wp_travel_payment_tab_call_back( $tab, $args ) {
		if ( 'payment' !== $tab ) {
			return;
		}
		$partial_payment          = isset( $args['settings']['partial_payment'] ) ? $args['settings']['partial_payment'] : '';
		$minimum_partial_payout   = isset( $args['settings']['minimum_partial_payout'] ) ? $args['settings']['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
		$paypal_email             = ( isset( $args['settings']['paypal_email'] ) ) ? $args['settings']['paypal_email'] : '';
		$payment_option_paypal    = ( isset( $args['settings']['payment_option_paypal'] ) ) ? $args['settings']['payment_option_paypal'] : '';
		$trip_tax_enable          = ( isset( $args['settings']['trip_tax_enable'] ) ) ? $args['settings']['trip_tax_enable'] : '';
		$trip_tax_percentage      = isset( $args['settings']['trip_tax_percentage'] ) ? $args['settings']['trip_tax_percentage'] : '';
		$trip_tax_price_inclusive = isset( $args['settings']['trip_tax_price_inclusive'] ) ? $args['settings']['trip_tax_price_inclusive'] : 'yes';
		?>

		<table class="form-table">
			<tr>
				<th><label for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ); ?></label></th>
				<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" value="yes" <?php checked( 'yes', $partial_payment ); ?> name="partial_payment" id="partial_payment"/>
						<span class="switch">
					</span>

					</label>
				</span>
					<p class="description"><?php esc_html_e( 'Enable partial payment while booking.', 'wp-travel' ); ?>
					</p>
				</td>
			</tr>
			<tr id="wp-travel-minimum-partial-payout">
				<th><label for="minimum_partial_payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ); ?></label></th>
				<td>
					<input type="range" min="1" max="100" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout" class="wt-slider" />
					<label><input type="number" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ); ?>" name="minimum_partial_payout" id="minimum_partial_payout_output" />%</label>
					<p class="description"><?php esc_html_e( 'Minimum percent of amount to pay while booking.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_payment_gateway_fields', $args ); ?>
		<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Standard Paypal', 'wp-travel' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="payment_option_paypal"><?php esc_html_e( 'Enable Paypal', 'wp-travel' ); ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_paypal ); ?> name="payment_option_paypal" id="payment_option_paypal"/>
						<span class="switch">
					</span>

					</label>
				</span>
					<p class="description"><?php esc_html_e( 'Check to enable standard PayPal payment.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr id="wp-travel-paypal-email" >
				<th><label for="paypal_email"><?php esc_html_e( 'Paypal Email', 'wp-travel' ); ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $paypal_email ); ?>" name="paypal_email" id="paypal_email"/>
					<p class="description"><?php esc_html_e( 'PayPal email address that receive payment.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</table>
		<div class="wp-travel-upsell-message">
			<div class="wp-travel-pro-feature-notice">
				<h4><?php esc_html_e( 'Need more payment gateway options ?', 'wp-travel' ); ?></h4>
				<p><?php printf( __( '%1$sCheck All Payment Gateways %2$s OR %3$sRequest a new one%4$s', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/downloads">', '</a>', '<a target="_blank" href="http://wptravel.io/contact">', '</a>' ); ?></p>
			</div>
		</div>
		<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'TAX Options', 'wp-travel' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="trip_tax_enable"><?php esc_html_e( 'Enable Tax for Trip Price', 'wp-travel' ); ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" value="yes" <?php checked( 'yes', $trip_tax_enable ); ?> name="trip_tax_enable" id="trip_tax_enable"/>
						<span class="switch">
					</span>

					</label>
				</span>
					<p class="description"><?php esc_html_e( 'Check to enable Tax options for trips.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr id="wp-travel-tax-price-options" >
				<th><label><?php esc_html_e( 'Trip prices entered with tax', 'wp-travel' ); ?></label></th>
				<td>
						<label><input <?php checked( 'yes', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="yes" type="radio">
						<?php esc_html_e( 'Yes, I will enter trip prices inclusive of tax', 'wp-travel' ); ?></label>

						<label> <input <?php checked( 'no', $trip_tax_price_inclusive ); ?> name="trip_tax_price_inclusive" value="no" type="radio">
						<?php esc_html_e( 'No, I will enter trip prices exclusive of tax', 'wp-travel' ); ?></label>

						<p class="description"><?php esc_html_e( 'This option will affect how you enter trip prices.', 'wp-travel' ); ?></p>

				</td>
			</tr>
			<tr id="wp-travel-tax-percentage" <?php echo 'yes' == $trip_tax_price_inclusive ? 'style="display:none;"' : 'style="display:table-row;"'; ?> >
				<th><label for="trip_tax_percentage_output"><?php esc_html_e( 'Tax Percentage', 'wp-travel' ); ?></label></th>
				<td>

					<label><input type="number" min="0" max="100" step="0.01" value="<?php echo esc_attr( $trip_tax_percentage ); ?>" name="trip_tax_percentage" id="trip_tax_percentage_output" />%</label>
					<p class="description"><?php esc_html_e( 'Trip Tax percentage added to trip price.', 'wp-travel' ); ?></p>

				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Callback for Debug tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function wp_travel_debug_tab_call_back( $tab, $args ) {
		if ( 'debug' !== $tab ) {
			return;
		}

		$wt_test_mode  = ( isset( $args['settings']['wt_test_mode'] ) ) ? $args['settings']['wt_test_mode'] : 'yes';
		$wt_test_email = ( isset( $args['settings']['wt_test_email'] ) ) ? $args['settings']['wt_test_email'] : '';
		?>
		<h4 class="wp-travel-tab-content-title"><?php esc_html_e( 'Test Payment', 'wp-travel' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wt_test_mode"><?php esc_html_e( 'Test Mode', 'wp-travel' ); ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input type="checkbox" value="yes" <?php checked( 'yes', $wt_test_mode ); ?> name="wt_test_mode" id="wt_test_mode"/>
							<span class="switch">
						</span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( 'Enable test mode to make test payment.', 'wp-travel' ); ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="wt_test_email"><?php esc_html_e( 'Test Email', 'wp-travel' ); ?></label></th>
				<td><input type="text" value="<?php echo esc_attr( $wt_test_email ); ?>" name="wt_test_email" id="wt_test_email"/>
				<p class="description"><?php esc_html_e( 'Test email address will get test mode payment emails.', 'wp-travel' ); ?></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_below_debug_tab_fields' ); ?>
		<?php
	}

	/**
	 * Callback for License tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	function wp_travel_license_tab_call_back( $tab, $args ) {
		if ( 'license' !== $tab ) {
			return;
		}
		do_action( 'wp_travel_license_tab_fields', $args );
	}

	/**
	 * Callback for the User Accounts Tab.
	 *
	 * @param Array $tab List of tabs.
	 * @param Array $args Settings arg List.
	 */
	function wp_travel_account_settings_tab_callback( $tab, $args ) {

		if ( 'account_options_global' !== $tab ) {
			return;
		}

		$selected_account_page = isset( $args['settings']['myaccount_page_id'] ) ? $args['settings']['myaccount_page_id'] : wp_travel_get_page_id( 'wp-travel-dashboard' );

		$enable_checkout_customer_registration = isset( $args['settings']['enable_checkout_customer_registration'] ) ? $args['settings']['enable_checkout_customer_registration'] : 'no';

		$enable_my_account_customer_registration = isset( $args['settings']['enable_my_account_customer_registration'] ) ? $args['settings']['enable_my_account_customer_registration'] : 'yes';

		$generate_username_from_email = isset( $args['settings']['generate_username_from_email'] ) ? $args['settings']['generate_username_from_email'] : 'no';

		$generate_user_password = isset( $args['settings']['generate_user_password'] ) ? $args['settings']['generate_user_password'] : 'no';

			echo '<table class="form-table">';
				// echo '<tr>';
				// echo '<th>';
				// echo '<label for="cart-page-id">' . esc_html__( 'My Account Page', 'wp-travel' ) . '</label>';
				// echo '</th>';
				// echo '<td>';
				// wp_dropdown_pages(array(
				// 'depth'                 => 0,
				// 'child_of'              => 0,
				// 'selected'              => $selected_account_page,
				// 'echo'                  => 1,
				// 'name'                  => 'myaccount_page_id',
				// 'id'                    => 'my-account-page-id', // string
				// 'class'                 => null, // string
				// 'show_option_none'      => null, // string
				// 'show_option_no_change' => null, // string
				// 'option_none_value'     => null, // string
				// ));
				// echo '<p class="description">' . esc_html__( 'Choose the page to use as account dashboard for registered users', 'wp-travel' ) . '</p>';
				// echo '</td>';
				// echo '<tr>';
				echo '<tr>';
					echo '<th>';
						echo '<label for="currency">' . esc_html_e( 'Customer Registration', 'wp-travel' ) . '</label>';
					echo '</th>';
					echo '<td>';
						echo '<span class="show-in-frontend checkbox-default-design">';
							echo '<label data-on="ON" data-off="OFF">';
								echo '<input' . checked( $enable_checkout_customer_registration, 'yes', false ) . ' value="1" name="enable_checkout_customer_registration" id="enable_checkout_customer_registration" type="checkbox" />';
								echo '<span class="switch">';
								echo '</span>';
							echo '</label>';
						echo '</span>';
						echo '<p class="description"><label for="enable_checkout_customer_registration">' . esc_html__( 'Require Customer login before booking.', 'wp-travel' ) . '</label></p>';
					echo '</td>';
					echo '<td>';
						echo '<span class="show-in-frontend checkbox-default-design">';
							echo '<label data-on="ON" data-off="OFF">';
								echo '<input' . checked( $enable_my_account_customer_registration, 'yes', false ) . ' value="1" name="enable_my_account_customer_registration" id="enable_my_account_customer_registration" type="checkbox" />';
								echo '<span class="switch">';
								echo '</span>';
							echo '</label>';
						echo '</span>';
						echo '<p class="description"><label for="enable_my_account_customer_registration">' . esc_html__( 'Enable customer registration on the "My Account" page.', 'wp-travel' ) . '</label></p>';
					echo '</td>';
				echo '<tr>';
				echo '<tr>';
					echo '<th>';
						echo '<label for="currency">' . esc_html_e( 'Account Creation', 'wp-travel' ) . '</label>';
					echo '</th>';
					echo '<td>';
						echo '<span class="show-in-frontend checkbox-default-design">';
							echo '<label data-on="ON" data-off="OFF">';
								echo '<input' . checked( $generate_username_from_email, 'yes', false ) . ' value="1" name="generate_username_from_email" id="generate_username_from_email" type="checkbox" />';
								echo '<span class="switch">';
								echo '</span>';
							echo '</label>';
						echo '</span>';
						echo '<p class="description"><label for="generate_username_from_email">' . esc_html__( ' Automatically generate username from customer email.', 'wp-travel' ) . '</label></p>';
					echo '</td>';
					echo '<td>';
						echo '<span class="show-in-frontend checkbox-default-design">';
							echo '<label data-on="ON" data-off="OFF">';
								echo '<input' . checked( $generate_user_password, 'yes', false ) . ' value="1" name="generate_user_password" id="generate_user_password" type="checkbox" />';
								echo '<span class="switch">';
								echo '</span>';
							echo '</label>';
						echo '</span>';
						echo '<p class="description"><label for="generate_user_password">' . esc_html__( ' Automatically generate customer password', 'wp-travel' ) . '</label></p>';
					echo '</td>';
				echo '</tr>';

			echo '</table>';

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

			$currency           = ( isset( $_POST['currency'] ) && '' !== $_POST['currency'] ) ? $_POST['currency'] : '';
			$google_map_api_key = ( isset( $_POST['google_map_api_key'] ) && '' !== $_POST['google_map_api_key'] ) ? $_POST['google_map_api_key'] : '';
			$wp_travel_map      = ( isset( $_POST['wp_travel_map'] ) && '' !== $_POST['wp_travel_map'] ) ? $_POST['wp_travel_map'] : '';

			$google_map_zoom_level = ( isset( $_POST['google_map_zoom_level'] ) && '' !== $_POST['google_map_zoom_level'] ) ? $_POST['google_map_zoom_level'] : '';

			$hide_related_itinerary      = ( isset( $_POST['hide_related_itinerary'] ) && '' !== $_POST['hide_related_itinerary'] ) ? 'yes' : 'no';
			$enable_multiple_travellers  = ( isset( $_POST['enable_multiple_travellers'] ) && '' !== $_POST['enable_multiple_travellers'] ) ? 'yes' : 'no';
			$trip_pricing_options_layout = ( isset( $_POST['trip_pricing_options_layout'] ) && '' !== $_POST['trip_pricing_options_layout'] ) ? $_POST['trip_pricing_options_layout'] : 'by-pricing-option';

			$send_booking_email_to_admin = ( isset( $_POST['send_booking_email_to_admin'] ) && '' !== $_POST['send_booking_email_to_admin'] ) ? 'yes' : 'no';

			// Email Templates
			// Booking Admin Email Settings.
			$booking_admin_email_template_settings = ( isset( $_POST['booking_admin_template'] ) && '' !== $_POST['booking_admin_template'] ) ? stripslashes_deep( $_POST['booking_admin_template'] ) : '';

			// Booking Client Email Settings.
			$booking_client_email_template_settings = ( isset( $_POST['booking_client_template'] ) && '' !== $_POST['booking_client_template'] ) ? stripslashes_deep( $_POST['booking_client_template'] ) : '';

			// Payment Admin Email Settings.
			$payment_admin_email_template_settings = ( isset( $_POST['payment_admin_template'] ) && '' !== $_POST['payment_admin_template'] ) ? stripslashes_deep( $_POST['payment_admin_template'] ) : '';

			// Payment Client Email Settings.
			$payment_client_email_template_settings = ( isset( $_POST['payment_client_template'] ) && '' !== $_POST['payment_client_template'] ) ? stripslashes_deep( $_POST['payment_client_template'] ) : '';

			// Enquiry Admin Email Settings.
			$enquiry_admin_email_template_settings = ( isset( $_POST['enquiry_admin_template'] ) && '' !== $_POST['enquiry_admin_template'] ) ? stripslashes_deep( $_POST['enquiry_admin_template'] ) : '';

			$enable_trip_enquiry_option = ( isset( $_POST['enable_trip_enquiry_option'] ) && '' !== $_POST['enable_trip_enquiry_option'] ) ? 'yes' : 'no';
			$enable_og_tags             = ( isset( $_POST['enable_og_tags'] ) && '' !== $_POST['enable_og_tags'] ) ? 'yes' : 'no';

			// Account Page.
			$myaccount_page_id             = isset( $_POST['myaccount_page_id'] ) ? $_POST['myaccount_page_id'] : '';
			$settings['myaccount_page_id'] = $myaccount_page_id;

			// Checkout Page customer registration.
			$enable_checkout_customer_registration             = ( isset( $_POST['enable_checkout_customer_registration'] ) && '' !== $_POST['enable_checkout_customer_registration'] ) ? 'yes' : 'no';
			$settings['enable_checkout_customer_registration'] = $enable_checkout_customer_registration;

			// My Account Page customer registration.
			$enable_my_account_customer_registration             = ( isset( $_POST['enable_my_account_customer_registration'] ) && '' !== $_POST['enable_my_account_customer_registration'] ) ? 'yes' : 'no';
			$settings['enable_my_account_customer_registration'] = $enable_my_account_customer_registration;

			// Generate Username from email.
			$generate_username_from_email             = ( isset( $_POST['generate_username_from_email'] ) && '' !== $_POST['generate_username_from_email'] ) ? 'yes' : 'no';
			$settings['generate_username_from_email'] = $generate_username_from_email;

			// Generate User Password.
			$generate_user_password             = ( isset( $_POST['generate_user_password'] ) && '' !== $_POST['generate_user_password'] ) ? 'yes' : 'no';
			$settings['generate_user_password'] = $generate_user_password;

			$settings['currency']                    = $currency;
			$settings['wp_travel_map']               = $wp_travel_map;
			$settings['google_map_api_key']          = $google_map_api_key;
			$settings['google_map_zoom_level']       = $google_map_zoom_level;
			$settings['hide_related_itinerary']      = $hide_related_itinerary;
			$settings['enable_multiple_travellers']  = $enable_multiple_travellers;
			$settings['trip_pricing_options_layout'] = $trip_pricing_options_layout;
			$settings['send_booking_email_to_admin'] = $send_booking_email_to_admin;

			// Save Admin Email Options.
			$settings['booking_admin_template_settings'] = $booking_admin_email_template_settings;
			$settings['payment_admin_template_settings'] = $payment_admin_email_template_settings;
			$settings['enquiry_admin_template_settings'] = $enquiry_admin_email_template_settings;

			// Save Client Email Options.
			$settings['booking_client_template_settings'] = $booking_client_email_template_settings;
			$settings['payment_client_template_settings'] = $payment_client_email_template_settings;

			// @since 1.1.1 Global tabs settings.
			$settings['global_tab_settings'] = ( isset( $_POST['wp_travel_global_tabs_settings'] ) && '' !== $_POST['wp_travel_global_tabs_settings'] ) ? $_POST['wp_travel_global_tabs_settings'] : '';

			// @since 1.2 Misc. Options
			$settings['enable_trip_enquiry_option'] = $enable_trip_enquiry_option;
			// @since 1.7.6 Misc. Option
			$settings['enable_og_tags'] = $enable_og_tags;

			// Merged Standard paypal Addons @since 1.2.1
			$wt_test_mode  = ( isset( $_POST['wt_test_mode'] ) && '' !== $_POST['wt_test_mode'] ) ? $_POST['wt_test_mode'] : '';
			$wt_test_email = ( isset( $_POST['wt_test_email'] ) && '' !== $_POST['wt_test_email'] ) ? $_POST['wt_test_email'] : '';

			$partial_payment        = ( isset( $_POST['partial_payment'] ) && '' !== $_POST['partial_payment'] ) ? $_POST['partial_payment'] : '';
			$minimum_partial_payout = ( isset( $_POST['minimum_partial_payout'] ) && '' !== $_POST['minimum_partial_payout'] ) ? $_POST['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

			// Trip TAX Options
			$trip_tax_enable          = ( isset( $_POST['trip_tax_enable'] ) && '' !== $_POST['trip_tax_enable'] ) ? $_POST['trip_tax_enable'] : '';
			$trip_tax_percentage      = ( isset( $_POST['trip_tax_percentage'] ) && '' !== $_POST['trip_tax_percentage'] ) ? $_POST['trip_tax_percentage'] : '';
			$trip_tax_price_inclusive = ( isset( $_POST['trip_tax_price_inclusive'] ) ) && '' !== $_POST['trip_tax_price_inclusive'] ? $_POST['trip_tax_price_inclusive'] : 'yes';

			$paypal_email          = ( isset( $_POST['paypal_email'] ) && '' !== $_POST['paypal_email'] ) ? $_POST['paypal_email'] : '';
			$payment_option_paypal = ( isset( $_POST['payment_option_paypal'] ) && '' !== $_POST['payment_option_paypal'] ) ? $_POST['payment_option_paypal'] : '';

			$gdpr_message = ( isset( $_POST['wp_travel_gdpr_message'] ) && '' !== $_POST['wp_travel_gdpr_message'] ) ? $_POST['wp_travel_gdpr_message'] : '';
			$open_gdpr_in_new_tab = ( isset( $_POST['open_gdpr_in_new_tab'] ) && '' !== $_POST['open_gdpr_in_new_tab'] ) ? 'yes': 'no';

			$wp_travel_from_email = ( isset( $_POST['wp_travel_from_email'] ) && '' !== $_POST['wp_travel_from_email'] ) ? $_POST['wp_travel_from_email'] : '';

			$cart_page_id = ( isset( $_POST['cart_page_id'] ) && '' !== $_POST['cart_page_id'] ) ? $_POST['cart_page_id'] : '';
			if ( '' !== $cart_page_id ) {
				update_option( 'wp_travel_wp-travel-cart_page_id', $cart_page_id );
			}

			$checkout_page_id = ( isset( $_POST['checkout_page_id'] ) && '' !== $_POST['checkout_page_id'] ) ? $_POST['checkout_page_id'] : '';
			if ( '' !== $checkout_page_id ) {
				update_option( 'wp_travel_wp-travel-checkout_page_id', $checkout_page_id );
			}
			$dashboard_page_id = ( isset( $_POST['dashboard_page_id'] ) && '' !== $_POST['dashboard_page_id'] ) ? $_POST['dashboard_page_id'] : '';
			if ( '' !== $dashboard_page_id ) {
				update_option( 'wp_travel_wp-travel-dashboard_page_id', $dashboard_page_id );
			}

			$settings['wt_test_mode']           = $wt_test_mode;
			$settings['wt_test_email']          = $wt_test_email;
			$settings['partial_payment']        = $partial_payment;
			$settings['minimum_partial_payout'] = $minimum_partial_payout;

			// Trip Tax Values.
			$settings['trip_tax_enable']          = $trip_tax_enable;
			$settings['trip_tax_percentage']      = $trip_tax_percentage;
			$settings['trip_tax_price_inclusive'] = $trip_tax_price_inclusive;

			$settings['paypal_email']          = $paypal_email;
			$settings['payment_option_paypal'] = $payment_option_paypal;
			// Merged Standard paypal Addons ends @since 1.2.1
			$wp_travel_trip_facts_enable = ( isset( $_POST['wp_travel_trip_facts_enable'] ) && '' !== $_POST['wp_travel_trip_facts_enable'] ) ? 'yes' : 'no';

			$settings['wp_travel_trip_facts_enable'] = $wp_travel_trip_facts_enable;

			$indexed = $_POST['wp_travel_trip_facts_settings'];
			if ( array_key_exists( '$index', $indexed ) ) {
				unset( $indexed['$index'] );
			}
			foreach ( $indexed as $key => $index ) {
				if ( empty( $index['name'] ) ) {
					unset( $indexed[ $key ] );
				}
			}

			$settings['wp_travel_trip_facts_settings'] = $indexed;

			// Cart and Checkout pages options
			$settings['cart_page_id']      = $cart_page_id;
			$settings['checkout_page_id']  = $checkout_page_id;
			$settings['dashboard_page_id'] = $dashboard_page_id;

			// GDPR Message
			$settings['wp_travel_gdpr_message'] = $gdpr_message;
			$settings['open_gdpr_in_new_tab'] = $open_gdpr_in_new_tab;

			// Save Global From E-mail Setting
			$settings['wp_travel_from_email'] = $wp_travel_from_email;

			// @since 1.0.5 Used this filter below.
			$settings = apply_filters( 'wp_travel_before_save_settings', $settings );

			update_option( 'wp_travel_settings', $settings );
			WP_Travel()->notices->add( 'error ' );
			$url_parameters['page']    = self::$collection;
			$url_parameters['updated'] = 'true';
			$redirect_url              = admin_url( self::$parent_slug );
			$redirect_url              = add_query_arg( $url_parameters, $redirect_url ) . '#' . $current_tab;
			// do_action( 'wp_travel_price_listing_save', $redirect_url );
			wp_redirect( $redirect_url );
			exit();
		}
	}

	static function get_system_info() {
		require_once sprintf( '%s/inc/admin/views/status.php', WP_TRAVEL_ABSPATH );
	}

	public function get_files() {

		if ( $_FILES ) {

			print_r( $_FILES );

		}

	}
}

new WP_Travel_Admin_Settings();
