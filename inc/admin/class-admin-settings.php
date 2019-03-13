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
	public static $collection = 'settings';
	/**
	 * Constructor.
	 */
	public function __construct() {

		self::$parent_slug = 'edit.php?post_type=itinerary-booking';
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );

		$collection      = self::$collection;
		$tab_hook_prefix = "wp_travel_tabs_content_{$collection}";

		// Tab Callbacks [ $tab_hook_prefix . '_' . $tab_key ] // Need enhancement with loop.
		add_action( "{$tab_hook_prefix}_general", array( $this, 'settings_callback_general' ), 12, 2 );
		add_action( "{$tab_hook_prefix}_itinerary", array( $this, 'settings_callback_itinerary' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_email", array( $this, 'settings_callback_email' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_account_options_global", array( $this, 'settings_callback_account_options_global' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_tabs_global", array( $this, 'settings_callback_tabs_global' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_payment", array( $this, 'settings_callback_payment' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_facts", array( $this, 'settings_callback_facts' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_license", array( $this, 'settings_callback_license' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_misc_options_global", array( $this, 'settings_callback_misc_options_global' ), 11, 2 );
		add_action( "{$tab_hook_prefix}_debug", array( $this, 'settings_callback_debug' ), 11, 2 );

		// Save Settings.
		add_action( 'load-itinerary-booking_page_settings', array( $this, 'save_settings' ) );
	}

	/**
	 * Call back function for page.
	 */
	public static function setting_page_callback() {
		$args['settings']       = wp_travel_get_settings();
		$url_parameters['page'] = self::$collection;
		$url                    = admin_url( self::$parent_slug );
		$url                    = add_query_arg( $url_parameters, $url );
		$sysinfo_url            = add_query_arg( array( 'page' => 'sysinfo' ), $url );

		echo '<div class="wrap wp-trave-settings-warp">';
			echo '<h1>' . __( 'WP Travel Settings', 'wp-travel' ) . '</h1>';
			echo '<div class="wp-trave-settings-form-warp">';
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
	public function add_tabs( $tabs ) {
		$settings_fields['general'] = array(
			'tab_label'     => __( 'General', 'wp-travel' ),
			'content_title' => __( 'General Settings', 'wp-travel' ),
			'priority'      => 10,
		);

		$settings_fields['itinerary'] = array(
			'tab_label'     => ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ),
			'content_title' => __( ucfirst( WP_TRAVEL_POST_TITLE_SINGULAR ) . ' Settings', 'wp-travel' ),
			'priority'      => 20,
		);

		$settings_fields['email'] = array(
			'tab_label'     => __( 'Email', 'wp-travel' ),
			'content_title' => __( 'Email Settings', 'wp-travel' ),
			'priority'      => 25,
		);

		$settings_fields['account_options_global'] = array(
			'tab_label'     => __( 'Account Settings', 'wp-travel' ),
			'content_title' => __( 'Account Settings', 'wp-travel' ),
			'priority'      => 30,
		);

		$settings_fields['tabs_global']         = array(
			'tab_label'     => __( 'Tabs', 'wp-travel' ),
			'content_title' => __( 'Global Tabs Settings', 'wp-travel' ),
			'priority'      => 40,
		);
		$settings_fields['payment']             = array(
			'tab_label'     => __( 'Payment', 'wp-travel' ),
			'content_title' => __( 'Payment Settings', 'wp-travel' ),
			'priority'      => 50,
		);
		$settings_fields['facts']               = array(
			'tab_label'     => __( 'Facts', 'wp-travel' ),
			'content_title' => __( 'Facts Settings', 'wp-travel' ),
			'priority'      => 60,
		);
		$settings_fields['license']             = array(
			'tab_label'     => __( 'License', 'wp-travel' ),
			'content_title' => __( 'License Details', 'wp-travel' ),
			'priority'      => 70,
		);
		$settings_fields['misc_options_global'] = array(
			'tab_label'     => __( 'Misc. Options', 'wp-travel' ),
			'content_title' => __( 'Miscellanaous Options', 'wp-travel' ),
			'priority'      => 80,
		);
		$settings_fields['debug']               = array(
			'tab_label'     => __( 'Debug', 'wp-travel' ),
			'content_title' => __( 'Debug Options', 'wp-travel' ),
			'priority'      => 90,
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
	public function settings_callback_general( $tab, $args ) {
		$settings = $args['settings'];

		$currency_list         = wp_travel_get_currency_list();
		$currency              = $settings['currency'];
		$google_map_api_key    = $settings['google_map_api_key'];
		$google_map_zoom_level = $settings['google_map_zoom_level'];

		// Pages.
		$selected_cart_page      = $settings['cart_page_id'];
		$selected_checkout_page  = $settings['checkout_page_id'];
		$selected_dashboard_page = $settings['dashboard_page_id'];

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
				<th><label for="wp-travel-map-select"><?php echo esc_html__( 'Select Map', 'wp-travel' ); ?></label></th>
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
	 * Callback for Trip tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_itinerary( $tab, $args ) {
		$settings = $args['settings'];

		$hide_related_itinerary      = $settings['hide_related_itinerary'];
		$enable_multiple_travellers  = $settings['enable_multiple_travellers'];
		$trip_pricing_options_layout = wp_travel_get_pricing_option_listing_type( $settings );
		do_action( 'wp_travel_tab_content_before_trips', $args );
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="hide_related_itinerary">
				<?php
				esc_html_e( 'Hide related ', 'wp-travel' );
				echo esc_attr( WP_TRAVEL_POST_TITLE );
				?>
					</label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="hide_related_itinerary" type="hidden" />
							<input <?php checked( $hide_related_itinerary, 'yes' ); ?> value="yes" name="hide_related_itinerary" id="hide_related_itinerary" type="checkbox" />
							<span class="switch"></span>
						</label>
					</span>
					<p class="description"><label for="hide_related_itinerary"><?php esc_html_e( sprintf( 'This will hide your related %s.', WP_TRAVEL_POST_TITLE ), 'wp-travel' ); ?></label></p>
				</td>
			<tr>
			<tr>
				<th>
					<label for="enable_multiple_travellers"><?php esc_html_e( 'Enable multiple travelers', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="enable_multiple_travellers" type="hidden" />
							<input <?php checked( $enable_multiple_travellers, 'yes' ); ?> value="yes" name="enable_multiple_travellers" id="enable_multiple_travellers" type="checkbox" />
							<span class="switch"></span>
						</label>
					</span>
					<p class="description"><label for="enable_multiple_travellers"><?php esc_html_e( sprintf( 'Check to enable.' ), 'wp-travel' ); ?></label></p>
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
			<?php
			do_action( 'wp_travel_tab_content_after_trips', $args );
	}

	/**
	 * Callback for Email tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_email( $tab, $args ) {
		$settings = $args['settings'];

		$send_booking_email_to_admin = $settings['send_booking_email_to_admin'];

		// Booking Admin Email.
		$booking_admin_email_settings = $settings['booking_admin_template_settings'];
		// Booking Client Email.
		$booking_client_email_settings = $settings['booking_client_template_settings'];

		// Payment Admin Email.
		$payment_admin_email_settings = $settings['payment_admin_template_settings'];
		// Payment Client Email.
		$payment_client_email_settings = $settings['payment_client_template_settings'];

		// Enquiry Admin Email.
		$enquiry_admin_email_settings = $settings['enquiry_admin_template_settings'];

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
														<input value="no" name="send_booking_email_to_admin" type="hidden" />
														<input <?php checked( $send_booking_email_to_admin, 'yes' ); ?> value="yes" name="send_booking_email_to_admin" id="send_booking_email_to_admin" type="checkbox" />
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
												<input value="<?php echo $booking_admin_email_settings['admin_subject']; ?>" type="text" name="booking_admin_template_settings[admin_subject]" id="booking-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-admin-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $booking_admin_email_settings['admin_title']; ?>" name="booking_admin_template_settings[admin_title]" id="booking-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-admin-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $booking_admin_email_settings['admin_header_color']; ?>" type="text" name="booking_admin_template_settings[admin_header_color]" id="booking-admin-email-header-color">
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
												wp_editor( $content, 'booking_admin_email_content', $settings = array( 'textarea_name' => 'booking_admin_template_settings[email_content]' ) );
												?>
												</div>
											</td>
										</tr>

											<?php
											/**
											 * Add Support Multiple Booking admin Template.
											 */
											do_action( 'wp_travel_multiple_booking_admin_template_settings', $booking_admin_email_settings );
											?>

									</table>

									<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="booking-client-email-sub"><?php esc_html_e( 'Booking Client Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $booking_client_email_settings['client_subject']; ?>" type="text" name="booking_client_template_settings[client_subject]" id="booking-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-client-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $booking_client_email_settings['client_title']; ?>" name="booking_client_template_settings[client_title]" id="booking-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="booking-client-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $booking_client_email_settings['client_header_color']; ?>" type="text" name="booking_client_template_settings[client_header_color]" id="booking-client-email-header-color">
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
												wp_editor( $content, 'booking_client_email_content', $settings = array( 'textarea_name' => 'booking_client_template_settings[email_content]' ) );
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
												<input value="<?php echo $payment_admin_email_settings['admin_subject']; ?>" type="text" name="payment_admin_template_settings[admin_subject]" id="payment-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_admin_email_settings['admin_title']; ?>" name="payment_admin_template_settings[admin_title]" id="payment-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $payment_admin_email_settings['admin_header_color']; ?>" type="text" name="payment_admin_template_settings[admin_header_color]" id="payment-admin-email-header-color">
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
												wp_editor( $content, 'payment_admin_email_content', $settings = array( 'textarea_name' => 'payment_admin_template_settings[email_content]' ) );
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
												<input value="<?php echo $payment_client_email_settings['client_subject']; ?>" type="text" name="payment_client_template_settings[client_subject]" id="payment-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_client_email_settings['client_title']; ?>" name="payment_client_template_settings[client_title]" id="payment-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $payment_client_email_settings['client_header_color']; ?>" type="text" name="payment_client_template_settings[client_header_color]" id="payment-client-email-header-color">
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
												wp_editor( $content, 'payment_client_email_content', $settings = array( 'textarea_name' => 'payment_client_template_settings[email_content]' ) );
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
												<input value="<?php echo $enquiry_admin_email_settings['admin_subject']; ?>" type="text" name="enquiry_admin_template_settings[admin_subject]" id="enquiry-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-title"><?php esc_html_e( 'Enquiry Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $enquiry_admin_email_settings['admin_title']; ?>" name="enquiry_admin_template_settings[admin_title]" id="enquiry-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-header-color"><?php esc_html_e( 'Enquiry Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = "<?php echo $enquiry_admin_email_settings['admin_header_color']; ?>" type="text" name="enquiry_admin_template_settings[admin_header_color]" id="enquiry-admin-email-header-color">
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
												wp_editor( $content, 'enquiry_admin_email_content', $settings = array( 'textarea_name' => 'enquiry_admin_template_settings[email_content]' ) );
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
	 * Callback for Account Settings Tab.
	 *
	 * @param Array $tab List of tabs.
	 * @param Array $args Settings arg List.
	 */
	public function settings_callback_account_options_global( $tab, $args ) {

		$settings                                = $args['settings'];
		$enable_checkout_customer_registration   = $settings['enable_checkout_customer_registration'];
		$enable_my_account_customer_registration = $settings['enable_my_account_customer_registration'];
		$generate_username_from_email            = $settings['generate_username_from_email'];
		$generate_user_password                  = $settings['generate_user_password'];
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Customer Registration', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="enable_checkout_customer_registration" type="hidden" />
							<input <?php checked( $enable_checkout_customer_registration, 'yes' ); ?> value="yes" name="enable_checkout_customer_registration" id="enable_checkout_customer_registration" type="checkbox" />
							<span class="switch">
							</span>
						</label>
					</span>
					<p class="description"><label for="enable_checkout_customer_registration"><?php echo esc_html__( 'Require Customer login before booking.', 'wp-travel' ); ?></label></p>
				</td>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="enable_my_account_customer_registration" type="hidden" />
							<input <?php checked( $enable_my_account_customer_registration, 'yes' ); ?> value="yes" name="enable_my_account_customer_registration" id="enable_my_account_customer_registration" type="checkbox" />
							<span class="switch">
							</span>
						</label>
					</span>
					<p class="description"><label for="enable_my_account_customer_registration"><?php echo esc_html__( 'Enable customer registration on the "My Account" page.', 'wp-travel' ); ?></label></p>
				</td>
			<tr>
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Account Creation', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="generate_username_from_email" type="hidden" />
							<input <?php checked( $generate_username_from_email, 'yes' ); ?> value="yes" name="generate_username_from_email" id="generate_username_from_email" type="checkbox" />
							<span class="switch">
							</span>
						</label>
					</span>
					<p class="description"><label for="generate_username_from_email"><?php echo esc_html__( ' Automatically generate username from customer email.', 'wp-travel' ); ?></label></p>
				</td>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="generate_user_password" type="hidden" />
							<input <?php checked( $generate_user_password, 'yes' ); ?> value="yes" name="generate_user_password" id="generate_user_password" type="checkbox" />
							<span class="switch">
							</span>
						</label>
					</span>
					<p class="description"><label for="generate_user_password"><?php echo esc_html__( ' Automatically generate customer password', 'wp-travel' ); ?></label></p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Callback for Tabs Settings.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_tabs_global( $tab, $args ) {
		$settings = $args['settings'];

		$global_tabs = $settings['global_tab_settings'];
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
						<input type="text" class="wp_travel_tabs_input-field section_title" name="global_tab_settings[<?php echo esc_attr( $key ); ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['label'] ); ?>" />
						<input type="hidden" name="global_tab_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" value="no" />

					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design"><label data-on="ON" data-off="OFF"><input name="global_tab_settings[<?php echo esc_attr( $key ); ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ); ?> /><?php // esc_html_e( 'Display', 'wp-travel' ); ?>
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
	 * Callback for Payment tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_payment( $tab, $args ) {
		$settings = $args['settings'];

		$partial_payment          = $settings['partial_payment'];
		$minimum_partial_payout   = $settings['minimum_partial_payout'];
		$paypal_email             = $settings['paypal_email'];
		$payment_option_paypal    = $settings['payment_option_paypal'];
		$trip_tax_enable          = $settings['trip_tax_enable'];
		$trip_tax_percentage      = $settings['trip_tax_percentage'];
		$trip_tax_price_inclusive = $settings['trip_tax_price_inclusive'];
		?>

		<table class="form-table">
			<tr>
				<th><label for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ); ?></label></th>
				<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input value="no" name="partial_payment" type="hidden" />
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
					<input value="no" name="payment_option_paypal" type="hidden" />
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
						<input value="no" name="trip_tax_enable" type="hidden" />
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
	 * Callback for Facts tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_facts( $tab ) {
		require_once 'views/tabs/tab-contents/itineraries/fact-setting-tab.php';
	}

	/**
	 * Callback for License tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_license( $tab, $args ) {
		do_action( 'wp_travel_license_tab_fields', $args );
	}

	/**
	 * Callback for Misc Options Tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_misc_options_global( $tab, $args ) {
		$settings = $args['settings'];
		$enable_trip_enquiry_option = $settings['enable_trip_enquiry_option'];
		$enable_og_tags             = $settings['enable_og_tags'];
		$wp_travel_gdpr_message     = $settings['wp_travel_gdpr_message'];
		$open_gdpr_in_new_tab       = $settings['open_gdpr_in_new_tab'];
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="enable_trip_enquiry_option"><?php esc_html_e( 'Enable Trip Enquiry', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="enable_trip_enquiry_option" type="hidden" />
							<input <?php checked( $enable_trip_enquiry_option, 'yes' ); ?> value="yes" name="enable_trip_enquiry_option" id="enable_trip_enquiry_option" type="checkbox" />
							<span class="switch">
						  </span>
						</label>
					</span>
				</td>
			<tr>
			<tr>
				<th>
					<label for="enable_og_tags"><?php esc_html_e( 'Enable OG Tags', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="enable_og_tags" type="hidden" />
							<input <?php checked( $enable_og_tags, 'yes' ); ?> value="yes" name="enable_og_tags" id="enable_og_tags" type="checkbox" />
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
					<textarea rows="4" cols="30" id="wp_travel_gdpr_message" name="wp_travel_gdpr_message"><?php echo $wp_travel_gdpr_message; ?></textarea>
				</td>
			</tr>
			<tr>
				<th>
					<label for="open_gdpr_in_new_tab"><?php _e( 'Open GDPR in new tab: ', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
						<input value="no" name="open_gdpr_in_new_tab" type="hidden" />
							<input <?php checked( $open_gdpr_in_new_tab, 'yes' ); ?> value="yes" name="open_gdpr_in_new_tab" id="open_gdpr_in_new_tab" type="checkbox" />
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
	 * Callback for Debug tab.
	 *
	 * @param  Array $tab  List of tabs.
	 * @param  Array $args Settings arg list.
	 */
	public function settings_callback_debug( $tab, $args ) {
		$settings = $args['settings'];

		$wt_test_mode  = $settings['wt_test_mode'];
		$wt_test_email = $settings['wt_test_email'];
		?>
		<h4 class="wp-travel-tab-content-title"><?php esc_html_e( 'Test Payment', 'wp-travel' ); ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wt_test_mode"><?php esc_html_e( 'Test Mode', 'wp-travel' ); ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input value="no" name="wt_test_mode" type="hidden" />
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
		<?php do_action( 'wp_travel_below_debug_tab_fields', $args ); ?>
		<?php
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		if ( isset( $_POST['save_settings_button'] ) ) {
			$current_tab = isset( $_POST['current_tab'] ) ? $_POST['current_tab'] : '';
			check_admin_referer( 'wp_travel_settings_page_nonce' );
			// Getting saved settings first.
			// $settings = wp_travel_get_settings();

			$settings_fields = array_keys( wp_travel_settings_default_fields() );

			foreach ( $settings_fields as $settings_field ) {
				if ( isset( $_POST[ $settings_field ] ) ) {
					$settings[ $settings_field ] = $_POST[ $settings_field ];

					// Default pages settings. [only to get page in - wp_travel_get_page_id()] // Need enhanchement.
					$page_ids = array( 'cart_page_id', 'checkout_page_id', 'dashboard_page_id' );
					if ( in_array( $settings_field, $page_ids ) && ! empty( $_POST[ $settings_field ] ) ) {
						update_option( 'wp_travel_' . $settings_field, $_POST[ $settings_field ]  );
					}
				}
			}

			// Email Templates
			// Booking Admin Email Settings.
			if ( isset( $_POST['booking_admin_template_settings'] ) && '' !== $_POST['booking_admin_template_settings'] ) {
				$settings['booking_admin_template_settings'] = stripslashes_deep( $_POST['booking_admin_template_settings'] );
			}

			// Booking Client Email Settings.
			if ( isset( $_POST['booking_client_template_settings'] ) && '' !== $_POST['booking_client_template_settings'] ) {
				$settings['booking_client_template_settings'] = stripslashes_deep( $_POST['booking_client_template_settings'] );
			}

			// Payment Admin Email Settings.
			if ( isset( $_POST['payment_admin_template_settings'] ) && '' !== $_POST['payment_admin_template_settings'] ) {
				$settings['payment_admin_template_settings'] = stripslashes_deep( $_POST['payment_admin_template_settings'] );
			}

			// Payment Client Email Settings.
			if ( isset( $_POST['payment_client_template_settings'] ) && '' !== $_POST['payment_client_template_settings'] ) {
				$settings['payment_client_template_settings'] = stripslashes_deep( $_POST['payment_client_template_settings'] );
			}

			// Enquiry Admin Email Settings.
			if ( isset( $_POST['enquiry_admin_template_settings'] ) && '' !== $_POST['enquiry_admin_template_settings'] ) {
				$settings['enquiry_admin_template_settings'] = stripslashes_deep( $_POST['enquiry_admin_template_settings'] );
			}

			// Trip Fact.
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

			// @since 1.0.5 Used this filter below.
			$settings = apply_filters( 'wp_travel_before_save_settings', $settings );

			update_option( 'wp_travel_settings', $settings );
			WP_Travel()->notices->add( 'error ' );
			$url_parameters['page']    = self::$collection;
			$url_parameters['updated'] = 'true';
			$redirect_url              = admin_url( self::$parent_slug );
			$redirect_url              = add_query_arg( $url_parameters, $redirect_url ) . '#' . $current_tab;
			wp_redirect( $redirect_url );
			exit();
		}
	}

	/**
	 * System info.
	 */
	public static function get_system_info() {
		require_once sprintf( '%s/inc/admin/views/status.php', WP_TRAVEL_ABSPATH );
	}

	public function get_files() {
		if ( $_FILES ) {
			print_r( $_FILES );
		}
	}
}

new WP_Travel_Admin_Settings();
