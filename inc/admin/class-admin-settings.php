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

		self::$parent_slug = 'edit.php?post_type=' . WP_TRAVEL_POST_TYPE;
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back' ), 10, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_itinerary' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_booking' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'call_back_tab_global_settings' ), 11, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'misc_options_tab_callback' ), 11, 2 );
		
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_payment_tab_call_back' ), 12, 2 );
		add_action( 'wp_travel_tabs_content_settings', array( $this, 'wp_travel_debug_tab_call_back' ), 12, 2 );		
		
		add_action( 'load-' . WP_TRAVEL_POST_TYPE . '_page_settings', array( $this, 'save_settings' ) );
	}

	/**
	 * Call back function for page.
	 */
	public static function setting_page_callback() {
		$args['settings'] = get_option( 'wp_travel_settings' );
		$url_parameters['page'] = self::$collection;
		$url = admin_url( self::$parent_slug );
		$url = add_query_arg( $url_parameters, $url );
		$sysinfo_url = add_query_arg( array( 'page' => 'sysinfo' ), $url );
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
			'tab_label' => __( WP_TRAVEL_POST_TITLE_SINGULAR, 'wp-travel' ),
			'content_title' => __( WP_TRAVEL_POST_TITLE_SINGULAR . ' Settings', 'wp-travel' ),
		);

		$settings_fields['email'] = array(
			'tab_label' => __( 'Email', 'wp-travel' ),
			'content_title' => __( 'Email Settings', 'wp-travel' ),
		);

		$settings_fields['tabs_global'] = array(
			'tab_label' => __( 'Tabs', 'wp-travel' ),
			'content_title' => __( 'Global Tabs Settings', 'wp-travel' ),
		);
		$settings_fields['payment'] = array(
			'tab_label' => __( 'Payment', 'wp-travel' ),
			'content_title' => __( 'Payment Settings', 'wp-travel' ),
		);
		$settings_fields['misc_options_global'] = array(
			'tab_label' => __( 'Misc. Options', 'wp-travel' ),
			'content_title' => __( 'Miscellanaous Options', 'wp-travel' ),
		);
		$settings_fields['debug'] = array(
			'tab_label' => __( 'Debug', 'wp-travel' ),
			'content_title' => __( 'Debug Options', 'wp-travel' ),
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
		$currency_list = wp_travel_get_currency_list();
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
					echo '<label for="currency">' . esc_html__( 'Currency', 'wp-travel' ) . '</label>';
				echo '</th>';
				echo '<td>';
					echo wp_travel_get_dropdown_currency_list( $currency_args );
					echo '<p class="description">' . esc_html__( 'Choose your currency', 'wp-travel' ) . '</p>';
				echo '</td>';
			echo '<tr>';

			echo '<tr>';
				echo '<th>';
					echo '<label for="google_map_api_key">' . esc_html__( 'Google Map API Key', 'wp-travel' ) . '</label>';
				echo '</th>';
				echo '<td>';
					echo '<input type="text" value="' . esc_attr( $google_map_api_key ) . '" name="google_map_api_key" id="google_map_api_key"/>';
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
					<label for="currency"><?php esc_html_e( 'Hide related ', 'wp-travel' ); echo esc_attr( WP_TRAVEL_POST_TITLE ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $hide_related_itinerary , 'yes' ); ?> value="1" name="hide_related_itinerary" id="hide_related_itinerary" type="checkbox" />						
							<span class="switch">
						  </span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( sprintf( 'This will hide your related %s.', WP_TRAVEL_POST_TITLE ), 'wp-travel' );  ?></p>
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

		//Booking Admin Defaults.
		$booking_admin_email_defaults = array(

			'admin_subject' => '',
			'admin_title'  => '',
			'admin_header_color' => '',
			'email_content' => '',

		);

		//Booking client Defaults.
		$booking_client_email_defaults = array(

			'client_subject' => '',
			'client_title'  => '',
			'client_header_color' => '',
			'email_content' => '',

		);


		//Payment Admin Defaults.
		$payment_admin_email_defaults = array(

			'admin_subject' => '',
			'admin_title'  => '',
			'admin_header_color' => '',
			'email_content' => '',

		);

		//Payment client Defaults.
		$payment_client_email_defaults = array(

			'client_subject' => '',
			'client_title'  => '',
			'client_header_color' => '',
			'email_content' => '',

		);


		//emquiry Admin Defaults.
		$enquiry_admin_email_defaults = array(

			'admin_subject' => '',
			'admin_title'  => '',
			'admin_header_color' => '',
			'email_content' => '',

		);
		//Booking Admin Email.
		$booking_admin_email_settings = isset( $args['settings']['booking_admin_template_settings'] ) ? $args['settings']['booking_admin_template_settings'] : $booking_admin_email_defaults;

		//Booking Client Email.
		$booking_client_email_settings = isset( $args['settings']['booking_client_template_settings'] ) ? $args['settings']['booking_client_template_settings'] : $booking_client_email_defaults;

		//Payment Admin Email.
		$payment_admin_email_settings = isset( $args['settings']['payment_admin_template_settings'] ) ? $args['settings']['payment_admin_template_settings'] : $payment_admin_email_defaults;

		//Payment Client Email.
		$payment_client_email_settings = isset( $args['settings']['payment_client_template_settings'] ) ? $args['settings']['payment_client_template_settings'] : $payment_client_email_defaults;

		//Enquiry Admin Email.
		$enquiry_admin_email_settings = isset( $args['settings']['enquiry_admin_template_settings'] ) ? $args['settings']['enquiry_admin_template_settings'] : $enquiry_admin_email_defaults;

		?>

		<div class="wp-travel-tab-content-email-header clearfix">
			<div class="wp-collapse-open">
				<a href="#" class="open-all-link"><span class="open-all" id="open-all">Open All</span></a>
				<a href="#" class="close-all-link"><span class="close-all" id="close-all">Close All</span></a>
			</div>
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
					<div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
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
													<input <?php checked( $send_booking_email_to_admin , 'yes' ); ?> value="1" name="send_booking_email_to_admin" id="send_booking_email_to_admin" type="checkbox" />						
													<span class="switch">
												  </span>
												</label>
											</span>
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-sub"><?php esc_html_e( 'Booking Email Subject', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input value="<?php echo $booking_admin_email_settings['admin_subject'] ?>" type="text" name="booking_admin_template[admin_subject]" id="booking-admin-email-sub">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input type="text" value="<?php echo $booking_admin_email_settings['admin_title'] ?>" name="booking_admin_template[admin_title]" id="booking-admin-email-title">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input class="wp-travel-color-field" value = <?php echo $booking_admin_email_settings['admin_header_color'] ?> type="text" name="booking_admin_template[admin_header_color]" id="booking-admin-email-header-color">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
										</th>
										<td>
											<?php 
											$content = isset( $booking_admin_email_settings['email_content'] ) && '' !== $booking_admin_email_settings['email_content']  ?  $booking_admin_email_settings['email_content'] : wp_travel_booking_admin_default_email_content();
											wp_editor( $content, 'booking_admin_email_content', $settings = array( 'textarea_name' => 'booking_admin_template[email_content]'  ) ); ?>
										</td>
									</tr>

								</table>

							<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

								<table class="form-table">
									<tr>
										<th>
											<label for="booking-client-email-sub"><?php esc_html_e( 'Booking Client Email Subject', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input value="<?php echo $booking_client_email_settings['client_subject'] ?>" type="text" name="booking_client_template[client_subject]" id="booking-client-email-sub">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-title"><?php esc_html_e( 'Booking Email Title', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input type="text" value="<?php echo $booking_client_email_settings['client_title'] ?>" name="booking_client_template[client_title]" id="booking-client-email-title">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-header-color"><?php esc_html_e( 'Booking Email Header Color', 'wp-travel' ); ?></label>
										</th>
										<td>
											<input class="wp-travel-color-field" value = <?php echo $booking_client_email_settings['client_header_color'] ?> type="text" name="booking_client_template[client_header_color]" id="booking-client-email-header-color">
										</td>
									</tr>
									<tr>
										<th>
											<label for="booking-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
										</th>
										<td>
											<?php 
											$content = isset( $booking_client_email_settings['email_content'] ) && '' !== $booking_client_email_settings['email_content']  ?  $booking_client_email_settings['email_content'] : wp_travel_booking_client_default_email_content();
											wp_editor( $content, 'booking_client_email_content', $settings = array( 'textarea_name' => 'booking_client_template[email_content]'  ) ); ?>
										</td>
									</tr>

								</table>
							
							</div>

							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingTwo">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
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
										<tr>
											<th>
												<label for="payment-admin-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_admin_email_settings['admin_subject'] ?>" type="text" name="payment_admin_template[admin_subject]" id="payment-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_admin_email_settings['admin_title'] ?>" name="payment_admin_template[admin_title]" id="payment-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = <?php echo $payment_admin_email_settings['admin_header_color'] ?> type="text" name="payment_admin_template[admin_header_color]" id="payment-admin-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<?php 
												$content = isset( $payment_admin_email_settings['email_content'] ) && '' !== $payment_admin_email_settings['email_content']  ?  $payment_admin_email_settings['email_content'] : wp_travel_payment_admin_default_email_content();
												wp_editor( $content, 'payment_admin_email_content', $settings = array( 'textarea_name' => 'payment_admin_template[email_content]'  ) ); ?>
											</td>
										</tr>

									</table>

									<h3 class="section-heading"><?php esc_html_e( 'Client Email Template Options', 'wp-travel' ); ?></h3>

									<table class="form-table">
										<tr>
											<th>
												<label for="payment-client-email-sub"><?php esc_html_e( 'Payment Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $payment_client_email_settings['client_subject'] ?>" type="text" name="payment_client_template[client_subject]" id="payment-client-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-title"><?php esc_html_e( 'Payment Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $payment_client_email_settings['client_title'] ?>" name="payment_client_template[client_title]" id="payment-client-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-header-color"><?php esc_html_e( 'Payment Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = <?php echo $payment_client_email_settings['client_header_color'] ?> type="text" name="payment_client_template[client_header_color]" id="payment-client-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="payment-client-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<?php 
												$content = isset( $payment_client_email_settings['email_content'] ) && '' !== $payment_client_email_settings['email_content']  ?  $payment_client_email_settings['email_content'] : wp_travel_payment_client_default_email_content();
												wp_editor( $content, 'payment_client_email_content', $settings = array( 'textarea_name' => 'payment_client_template[email_content]'  ) ); ?>
											</td>
										</tr>

									</table>

								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading" role="tab" id="headingThree">
						<h4 class="panel-title">
							<a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
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
										<tr>
											<th>
												<label for="enquiry-admin-email-sub"><?php esc_html_e( 'Enquiry Email Subject', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input value="<?php echo $enquiry_admin_email_settings['admin_subject'] ?>" type="text" name="enquiry_admin_template[admin_subject]" id="enquiry-admin-email-sub">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-title"><?php esc_html_e( 'Enquiry Email Title', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input type="text" value="<?php echo $enquiry_admin_email_settings['admin_title'] ?>" name="enquiry_admin_template[admin_title]" id="enquiry-admin-email-title">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-header-color"><?php esc_html_e( 'Enquiry Email Header Color', 'wp-travel' ); ?></label>
											</th>
											<td>
												<input class="wp-travel-color-field" value = <?php echo $enquiry_admin_email_settings['admin_header_color'] ?> type="text" name="enquiry_admin_template[admin_header_color]" id="enquiry-admin-email-header-color">
											</td>
										</tr>
										<tr>
											<th>
												<label for="enquiry-admin-email-content"><?php esc_html_e( 'Email Content', 'wp-travel' ); ?></label>
											</th>
											<td>
												<?php 
												$content = isset( $enquiry_admin_email_settings['email_content'] ) && '' !==$enquiry_admin_email_settings['email_content']  ?  $enquiry_admin_email_settings['email_content'] : wp_travel_enquiries_admin_default_email_content();
												wp_editor( $content, 'enquiry_admin_email_content', $settings = array( 'textarea_name' => 'enquiry_admin_template[email_content]'  ) ); ?>
											</td>
										</tr>

									</table>

								</div>

							</div>
						</div>
					</div>
				</div>
			
			
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
		$global_tabs = isset ( $args['settings']['global_tab_settings'] ) ? $args['settings']['global_tab_settings'] : '';

		if( empty( $global_tabs ) ) {

			//Fallback to default Tabs.
			$global_tabs = wp_travel_get_default_frontend_tabs();

		}
		
		if ( is_array( $global_tabs ) && count( $global_tabs ) > 0 ) {
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
			foreach ( $global_tabs as $key => $tab ) : ?>
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
						<input type="text" class="wp_travel_tabs_input-field section_title" name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ) ?>][label]" value="<?php echo esc_html( $tab['label'] ); ?>" placeholder="<?php echo esc_html( $tab['label'] ); ?>" />
						<input type="hidden" name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ) ?>][show_in_menu]" value="no" />
						
					</div>
					</td>
					<td width="20%">
						<span class="show-in-frontend checkbox-default-design"><label data-on="ON" data-off="OFF"><input name="wp_travel_global_tabs_settings[<?php echo esc_attr( $key ) ?>][show_in_menu]" type="checkbox" value="yes" <?php checked( 'yes', $tab['show_in_menu'] ) ?> /><?php //esc_html_e( 'Display', 'wp-travel' ); ?>
						<span class="switch">
						  </span>
						</label></span>
					</td>
				</tr>
			<?php
				endforeach;
			echo'<tbody></table>';
		}
	}

	/**
	 * Callback for Options Tab
	 *
	 */
	function misc_options_tab_callback( $tab, $args ){

		if ( 'misc_options_global' !== $tab ) {
			return;
		}
		$enable_trip_enquiry_option = isset( $args['settings']['enable_trip_enquiry_option'] ) ? $args['settings']['enable_trip_enquiry_option'] : 'yes';
		?>
		<table class="form-table">
			<tr>
				<th>
					<label for="currency"><?php esc_html_e( 'Enable Trip Enquiry', 'wp-travel' ); ?></label>
				</th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input <?php checked( $enable_trip_enquiry_option , 'yes' ); ?> value="1" name="enable_trip_enquiry_option" id="enable_trip_enquiry_option" type="checkbox" />						
							<span class="switch">
						  </span>
						</label>
					</span>
				</td>
			<tr>
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
		$partial_payment = isset( $args['settings']['partial_payment'] ) ? $args['settings']['partial_payment'] : '';
		$minimum_partial_payout = isset( $args['settings']['minimum_partial_payout'] ) ? $args['settings']['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;
		$paypal_email = ( isset( $args['settings']['paypal_email'] ) ) ? $args['settings']['paypal_email'] : '';
		$payment_option_paypal = ( isset( $args['settings']['payment_option_paypal'] ) ) ? $args['settings']['payment_option_paypal'] : ''; ?>
		
		<table class="form-table">
			<tr>
				<th><label for="partial_payment"><?php esc_html_e( 'Partial Payment', 'wp-travel' ) ?></label></th>
				<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" value="yes" <?php checked( 'yes', $partial_payment ) ?> name="partial_payment" id="partial_payment"/>		
						<span class="switch">
					</span>
					
					</label>
				</span>
					<p class="description"><?php esc_html_e( 'Enable partial payment while booking.', 'wp-travel' ) ?>
					</p>
				</td>
			</tr>
			<tr id="wp-travel-minimum-partial-payout">
				<th><label for="minimum_partial_payout"><?php esc_html_e( 'Minimum Payout (%)', 'wp-travel' ) ?></label></th>
				<td>
					<input type="range" min="1" max="100" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ) ?>" name="minimum_partial_payout" id="minimum_partial_payout" class="wt-slider" />
					<label><input type="number" step="0.01" value="<?php echo esc_attr( $minimum_partial_payout ) ?>" name="minimum_partial_payout" id="minimum_partial_payout_output" />%</label>
					<p class="description"><?php esc_html_e( 'Minimum percent of amount to pay while booking.', 'wp-travel' ) ?></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_payment_gateway_fields', $args ); ?>
		<h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Standard Paypal', 'wp-travel' )?></h3>
		<table class="form-table">
			<tr>
				<th><label for="payment_option_paypal"><?php esc_html_e( 'Enable Paypal', 'wp-travel' ) ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input type="checkbox" value="yes" <?php checked( 'yes', $payment_option_paypal ) ?> name="payment_option_paypal" id="payment_option_paypal"/>
						<span class="switch">
					</span>
					
					</label>
				</span>
					<p class="description"><?php esc_html_e( 'Check to enable standard PayPal payment.', 'wp-travel' ) ?></p>
				</td>
			</tr>
			<tr id="wp-travel-paypal-email" >
				<th><label for="paypal_email"><?php esc_html_e( 'Paypal Email', 'wp-travel' ) ?></label></th>
				<td>
					<input type="text" value="<?php echo esc_attr( $paypal_email ) ?>" name="paypal_email" id="paypal_email"/>
					<p class="description"><?php esc_html_e( 'PayPal email address that receive payment.', 'wp-travel' ) ?></p>
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

		$wt_test_mode = ( isset( $args['settings']['wt_test_mode'] ) ) ? $args['settings']['wt_test_mode'] : 'yes';
		$wt_test_email = ( isset( $args['settings']['wt_test_email'] ) ) ? $args['settings']['wt_test_email'] : '';
		?>
		<h4 class="wp-travel-tab-content-title"><?php esc_html_e( 'Test Payment', 'wp-travel' ) ?></h4>
		<table class="form-table">
			<tr>
				<th><label for="wt_test_mode"><?php esc_html_e( 'Test Mode', 'wp-travel' ) ?></label></th>
				<td>
					<span class="show-in-frontend checkbox-default-design">
						<label data-on="ON" data-off="OFF">
							<input type="checkbox" value="yes" <?php checked( 'yes', $wt_test_mode ) ?> name="wt_test_mode" id="wt_test_mode"/>					
							<span class="switch">
						</span>
						</label>
					</span>
					<p class="description"><?php esc_html_e( 'Enable test mode to make test payment.', 'wp-travel' ) ?></p>
				</td>
			</tr>
			<tr>
				<th><label for="wt_test_email"><?php esc_html_e( 'Test Email', 'wp-travel' ) ?></label></th>
				<td><input type="text" value="<?php echo esc_attr( $wt_test_email ) ?>" name="wt_test_email" id="wt_test_email"/>
				<p class="description"><?php esc_html_e( 'Test email address will get test mode payment emails.', 'wp-travel' ) ?></p>
				</td>
			</tr>
		</table>
		<?php do_action( 'wp_travel_below_debug_tab_fields' ); ?>
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

			//Email Templates
			//Booking Admin Email Settings.
			$booking_admin_email_template_settings = ( isset( $_POST['booking_admin_template'] ) && '' !== $_POST['booking_admin_template'] ) ? stripslashes_deep( $_POST['booking_admin_template'] ) : '';

			//Booking Client Email Settings.
			$booking_client_email_template_settings = ( isset( $_POST['booking_client_template'] ) && '' !== $_POST['booking_client_template'] ) ? stripslashes_deep( $_POST['booking_client_template'] ) : '';

			//Payment Admin Email Settings.
			$payment_admin_email_template_settings = ( isset( $_POST['payment_admin_template'] ) && '' !== $_POST['payment_admin_template'] ) ? stripslashes_deep( $_POST['payment_admin_template'] ) : '';

			//Payment Client Email Settings.
			$payment_client_email_template_settings = ( isset( $_POST['payment_client_template'] ) && '' !== $_POST['payment_client_template'] ) ? stripslashes_deep( $_POST['payment_client_template'] ) : '';


			//Enquiry Admin Email Settings.
			$enquiry_admin_email_template_settings = ( isset( $_POST['enquiry_admin_template'] ) && '' !== $_POST['enquiry_admin_template'] ) ? stripslashes_deep( $_POST['enquiry_admin_template'] ) : '';

			$enable_trip_enquiry_option = ( isset( $_POST['enable_trip_enquiry_option'] ) && '' !== $_POST['enable_trip_enquiry_option'] ) ? 'yes' : 'no';

			$settings['currency'] = $currency;
			$settings['google_map_api_key'] = $google_map_api_key;
			$settings['hide_related_itinerary'] = $hide_related_itinerary;
			$settings['send_booking_email_to_admin'] = $send_booking_email_to_admin;
			
			// Save Admin Email Options.
			$settings['booking_admin_template_settings'] = $booking_admin_email_template_settings;
			$settings['payment_admin_template_settings'] = $payment_admin_email_template_settings;
			$settings['enquiry_admin_template_settings'] = $enquiry_admin_email_template_settings;

			//Save Client Email Options.
			$settings['booking_client_template_settings'] = $booking_client_email_template_settings;
			$settings['payment_client_template_settings'] = $payment_client_email_template_settings;

			// @since 1.1.1 Global tabs settings.
			$settings['global_tab_settings'] = ( isset( $_POST['wp_travel_global_tabs_settings'] ) && '' !== $_POST['wp_travel_global_tabs_settings'] ) ? $_POST['wp_travel_global_tabs_settings'] : '';

			// @since 1.2 Misc. Options
			$settings['enable_trip_enquiry_option'] = $enable_trip_enquiry_option;

			// Merged Standard paypal Addons @since 1.2.1
			$wt_test_mode = ( isset( $_POST['wt_test_mode'] ) && '' !== $_POST['wt_test_mode'] ) ? $_POST['wt_test_mode'] : '';
			$wt_test_email = ( isset( $_POST['wt_test_email'] ) && '' !== $_POST['wt_test_email'] ) ? $_POST['wt_test_email'] : '';

			$partial_payment = ( isset( $_POST['partial_payment'] ) && '' !== $_POST['partial_payment'] ) ? $_POST['partial_payment'] : '';
			$minimum_partial_payout = ( isset( $_POST['minimum_partial_payout'] ) && '' !== $_POST['minimum_partial_payout'] ) ? $_POST['minimum_partial_payout'] : WP_TRAVEL_MINIMUM_PARTIAL_PAYOUT;

			$paypal_email = ( isset( $_POST['paypal_email'] ) && '' !== $_POST['paypal_email'] ) ? $_POST['paypal_email'] : '';
			$payment_option_paypal = ( isset( $_POST['payment_option_paypal'] ) && '' !== $_POST['payment_option_paypal'] ) ? $_POST['payment_option_paypal'] : '';

			$settings['wt_test_mode'] = $wt_test_mode;
			$settings['wt_test_email'] = $wt_test_email;
			$settings['partial_payment'] = $partial_payment;
			$settings['minimum_partial_payout'] = $minimum_partial_payout;

			$settings['paypal_email'] = $paypal_email;
			$settings['payment_option_paypal'] = $payment_option_paypal;
			// Merged Standard paypal Addons ends @since 1.2.1

			// @since 1.0.5 Used this filter below.
			$settings = apply_filters( 'wp_travel_before_save_settings', $settings );

			update_option( 'wp_travel_settings', $settings );
			WP_Travel()->notices->add( 'error ' );
			$url_parameters['page'] = self::$collection;
			$url_parameters['updated'] = 'true';
			$redirect_url = admin_url( self::$parent_slug );
			$redirect_url = add_query_arg( $url_parameters, $redirect_url ) . '#' . $current_tab;
			// do_action( 'wp_travel_price_listing_save', $redirect_url );
			wp_redirect( $redirect_url );
			exit();
		}
	}

	static function get_system_info() {
		require_once sprintf( '%s/inc/admin/views/status.php', WP_TRAVEL_ABSPATH );
	}
}

new WP_Travel_Admin_Settings();
