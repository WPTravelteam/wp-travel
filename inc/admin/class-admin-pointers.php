<?php
/**
 * Admin Info Pointers
 *
 * @package WP_Travel
 * @author WEN Solutions
 */

/**
 * Admin Info Pointers Class
 */
class WP_Travel_Admin_Info_Pointers {

	/**
	 * Constructor.
	 */
	function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pointers' ), 999 );

		$after_multiple_pricing = get_option( 'wp_travel_user_after_multiple_pricing_category' );
		$user_since             = get_option( 'wp_travel_user_since', '1.0.0' );
		if ( 'yes' === $after_multiple_pricing && version_compare( $user_since, '3.0.0', '<=' ) ) {
			add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'menu_order_changed' ) );
			add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'new_trips_menu' ) );
		}
		$switch_to_react = wptravel_is_react_version_enabled();

		if ( version_compare( $user_since, '4.0.0', '<' ) && ! $switch_to_react ) {
			add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'enable_v4_pointer' ) );
			add_filter( 'wp_travel_admin_pointers-edit-itinerary-booking', array( $this, 'enable_v4_pointer' ) );
			add_filter( 'wp_travel_admin_pointers-edit-itineraries', array( $this, 'enable_v4_pointer' ) );
			add_filter( 'wp_travel_admin_pointers-plugins', array( $this, 'enable_v4_pointer' ) );
			add_filter( 'wp_travel_admin_pointers-itinerary-booking_page_settings', array( $this, 'enable_v4_pointer' ) );
		}

		// Admin General Notices.
		add_action( 'admin_notices', array( $this, 'paypal_merge_notice' ) );
		add_action( 'admin_notices', array( $this, 'update_payment_gateways_notice' ) );
		add_action( 'admin_notices', array( $this, 'importer_upsell_notice' ) );
		add_action( 'admin_init', array( $this, 'get_dismissied_nag_messages' ) );

		add_action( 'wp_travel_general_admin_notice', array( $this, 'general_admin_notices' ) );
	}

	/**
	 * Main function for pointers.
	 *
	 * @param String $hook_suffix Suffix of hook.
	 *
	 * @since  1.1.0
	 */
	function load_pointers( $hook_suffix ) {

		// Don't run on WP < 3.3.
		if ( get_bloginfo( 'version' ) < '3.3' ) {
			return;
		}

		$screen    = get_current_screen();
		$screen_id = $screen->id;

		// Get pointers for this screen.
		$pointers = apply_filters( 'wp_travel_admin_pointers-' . $screen_id, array() );

		if ( ! $pointers || ! is_array( $pointers ) ) {
			return;
		}

		// Get dismissed pointers.
		$dismissed = get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true );
		$dismissed = explode( ',', $dismissed );

		$valid_pointers = array();

		// Check pointers and remove dismissed ones.
		foreach ( $pointers as $pointer_id => $pointer ) {

			// Sanity check.
			if ( in_array( $pointer_id, $dismissed ) || empty( $pointer ) || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) ) {
				continue;
			}

			$pointer['pointer_id'] = $pointer_id;

			// Add the pointer to $valid_pointers array.
			$valid_pointers['pointers'][] = $pointer;

		}

		// No valid pointers? Stop here.
		if ( empty( $valid_pointers ) ) {
			return;
		}

		// Add pointers style to queue.
		wp_enqueue_style( 'wp-pointer' );

		// Add pointers script to queue. Add custom script.
		// wp_register_script( 'wp-travel-admin-pointers-js', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/assets/js/wp-travel-backend-pointers.js', array( 'wp-pointer' ) );

		// Add pointer options to script.
		wp_localize_script( 'wp-travel-admin-pointers-js', 'wpctgPointer', $valid_pointers );
		wp_enqueue_script( 'wp-travel-admin-pointers-js' );
	}

	/**
	 * Pointer for Appearance on plugin activation.
	 *
	 * @param Array $q Array.
	 * @since    1.1.0
	 */
	function add_plugin_pointers( $q ) {

		$pointer_1_content = '<ul class="changes-list">
		<li>Itineraries menu changed to Trips.</li>
		<li>Locations menu changed to Destinations.</li>
		<li>Trips can be group by activities.</li>
		<li>Marketplace: Check WP travel addons &amp; Themes.</li>
		<li>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>.</li>
		</ul>';

		$q['wp_travel_post_type_chges'] = array(
			'target'  => '#menu-posts-' . WP_TRAVEL_POST_TYPE,
			'options' => array(
				'content'  => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>', __( 'New in WP Travel v.1.1.0', 'wp-travel' ), $pointer_1_content ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);
		return $q;
	}

	/**
	 * Pointer for Appearance on plugin activation.
	 *
	 * @since    1.1.0
	 */
	function add_single_post_edit_screen_pointers( $q ) {

		$q['wp_travel_post_edit_page_cngs'] = array(
			'target'  => '#wp-travel-trip-info',
			'options' => array(
				'content'  => sprintf(
					'<h3 class="update-notice"> %s </h3> <p> %s </p>',
					__( 'New in WP Travel v.1.1.0', 'wp-travel' ),
					__( '"Trip Code" has been moved to sidebar "Trip Info" metabox. ', 'wp-travel' )
				),
				'position' => array(
					'edge'  => 'right',
					'align' => 'center',
				),
			),
		);

		$content = '<ul class="changes-list">
		<li><strong>"Group Size"</strong> has been moved <strong>"Additional info"</strong> tab.</li>
		<li><strong>"Outline"</strong> has been moved <strong>"Itinerary"</strong> tab.</li>
		<li><strong>"Trip Includes" & "Trip Excludes" </strong> has been moved <strong>"Includes / Excludes"</strong> tab.</li>
		<li>Number of Nights added in <strong>"Trip Duration"</strong></li>
		<li>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>.</li>
		</ul>';

		$q['wp_travel_post_edit_page_cngs_2'] = array(
			'target'  => '#wp-travel-tab-additional_info',
			'options' => array(
				'content'  => sprintf(
					'<h3 class="update-notice"> %s </h3> <p> %s </p>',
					__( 'New in WP Travel v.1.1.0', 'wp-travel' ),
					$content
				),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);
		return $q;
	}

	/**
	 * Pointer for Appearance on plugin activation.
	 *
	 * @since    1.1.0
	 */
	function add_dashboard_screen_pointers( $q ) {

		$pointer_content = 'WP travel archive slugs for Trips, Destinations, Trip Types & Activities can be changed from Permalinks page.
		View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-1-0-release-note/">here</a>';

		$q['wp_travel_post_type_chges'] = array(
			'target'  => '#menu-settings',
			'options' => array(
				'content'  => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>', __( 'WP Travel permalink options', 'wp-travel' ), $pointer_content ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);

		return $q;
	}

	function menu_order_changed( $q ) {
		$pointer_content = '<p>We have splited trips menu in two parts: <b>WP Travel</b> & <b>Trips</b> for proper organization of admin links and to make user friendly. Under WP Travel you can find Bookings, Enquiries, Coupons, Trip Extras, Reports, settings.
		<br>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-8-0-release-note/">here</a></p>';

		$q['wp_travel_menu_order_changes'] = array(
			'target'  => '#menu-posts-itinerary-booking',
			'options' => array(
				'content'  => sprintf( '<h3 class="update-notice"> %s </h3> <p> %s </p>', __( 'WP Travel Menu Changed', 'wp-travel' ), $pointer_content ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);

		return $q;
	}

	function new_trips_menu( $q ) {
		$pointer_content = '<p>We have splited trips menu in two parts: <b>WP Travel</b> & <b>Trips</b> for proper organization of admin links and to make user friendly. Under Trips you can find All Trips, New Trip, Trip Types, Destinations, Keywords and Activities. <br>View other changes <a target="_blank" href="http://wptravel.io/wp-travel-1-8-0-release-note/">here</a></p>';

		$q['wp_travel_new_trips_menu'] = array(
			'target'  => '#menu-posts-itineraries',
			'options' => array(
				'content'  => sprintf( '<h3 class = "update-notice"> %s </h3> <p> %s </p>', __( 'WP Travel New Trips Menu', 'wp-travel' ), $pointer_content ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);

		return $q;
	}
	function enable_v4_pointer( $q ) {
		$pointer_content = '<p>Please go to WP Travel > Settings > General. Now enable switch to V4 option and save settings to enable WP Travel version 4.0.0 layout. <a href="https://wptravel.io/wp-travel-version-4-0-0-release/" target="_blank">Learn More</a></p>';

		$q['wp_travel_enable_v4_pointer'] = array(
			'target'  => '#menu-posts-itinerary-booking',
			'options' => array(
				'content'  => sprintf( '<h3 class = "update-notice"> %s </h3> <p> %s </p>', __( 'Enable WP Travel Version 4.0.0', 'wp-travel' ), $pointer_content ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'center',
				),
			),
		);

		return $q;
	}



	function paypal_addon_admin_notice() {

		if ( ! is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {

			$class = 'notice notice-info is-dismissible'; ?>

			<div class="<?php echo esc_attr( $class ); ?>">
			<p>
			<strong>
				<?php echo esc_html__( 'Want to add payment gateway in WP Travel booking? ', 'wp-travel' ); ?><a target="_blank" href="http://wptravel.io/downloads/standard-paypal/"><?php echo esc_html__( ' Download "Standard PayPal" ', 'wp-travel' ); ?></a><?php echo esc_html__( '  addon for free!!', 'wp-travel' ); ?>
			</strong>
			</p>
			</div>
			<?php
		} elseif ( is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {

			$plugin_data = get_plugin_data( WP_TRAVEL_PAYPAL_PLUGIN_FILE );

			if ( isset( $plugin_data['Version'] ) ) {
				if ( version_compare( $plugin_data['Version'], '1.0.1', '<' ) ) {
					?>
					<div class="notice notice-warning">
						<p>
						<strong>
							<?php echo esc_html__( 'You are using older version of WP Travel Standard paypal. Please', 'wp-travel' ); ?><a target="_blank" href="http://wptravel.io/downloads/standard-paypal/"><?php echo esc_html__( ' Download version 1.0.1 Now ', 'wp-travel' ); ?></a>
						</strong>
						</p>
						<p>
						<strong>
							<?php echo esc_html__( 'Need help With the update ? ', 'wp-travel' ); ?><a target="_blank" href="http://wptravel.io/documentations/standard-paypal/updating-wp-travel-standard-paypal/"><?php echo esc_html__( ' Click here ', 'wp-travel' ); ?></a><?php echo esc_html__( ' for detailed instructions on updating the plugin.', 'wp-travel' ); ?>
						</strong>
						</p>
					</div>

					<?php
				}
			}
		}
	}

	/**
	 * paypal_merge_notice
	 *
	 * WP Travel Standard paypal merge info.
	 *
	 * @since 1.2
	 */
	function paypal_merge_notice() {

		if ( is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {
			$user_id = get_current_user_id();

			if ( ! get_user_meta( $user_id, 'wp_travel_dismissied_nag_messages' ) ) {
				?>
				<div class="notice notice-info is-dismissible">
					<p>
					<strong>
						<?php echo esc_html__( 'WP Travel Standard Paypal plugin will be merged to WP Travel in the next update of WP Travel Plugin( v.1.2.1 ). Please make sure to deactivate the WP Travel Standard Paypal plugin before updating to next WP Travel Release.  ', 'wp-travel' ); ?><a href="?wp-travel-dismissed-nag"><?php echo esc_html__( ' Dismiss this Message ', 'wp-travel' ); ?></a>
					</strong>
					</p>
				</div>
				<?php
			}
		}
	}


	/**
	 * update_payment_gateways_notice
	 *
	 * WP Travel Standard paypal merge info.
	 *
	 * @since 1.4.0
	 */
	function update_payment_gateways_notice() {

		$addons = array( 'wp-travel-instamojo/wp-travel-instamojo-checkout.php', 'wp-travel-paypal-express-checkout/wp-travel-paypal-express-checkout.php', 'wp-travel-razor-pay/wp-travel-razorpay-checkout.php', 'wp-travel-stripe/wp-travel-stripe.php' );

		foreach ( $addons as $addon ) {

			if ( is_plugin_active( $addon ) ) {

				$addon_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $addon );

				if ( version_compare( $addon_data['Version'], '1.0.1', '<' ) ) {

					?>
						<div class="notice notice-warning">
							<p>
							<strong>
								<?php echo esc_html__( 'With the update to WP Travel version 1.4.0 ', 'wp-travel' ); ?><strong> <?php echo esc_html( $addon_data['Name'] ) . esc_html__( ' addon', 'wp-travel' ); ?> </strong><?php echo esc_html__( ' needs to be updated to work, for more information, ', 'wp-travel' ); ?><a target="_blank" href="http://wptravel.io/category/release-notes/"><?php echo esc_html__( 'Click Here', 'wp-travel' ); ?></a>
							</strong>
							</p>
						</div>
					<?php
				}
			}
		}
	}

	/**
	 * Dismiss info nag message.
	 */
	function get_dismissied_nag_messages() {

		if ( ! WP_Travel::verify_nonce( true ) ) {
			return;
		}

		$user_id = get_current_user_id();

		if ( isset( $_GET['wp-travel-dismissed-nag'] ) ) {
			add_user_meta( $user_id, 'wp_travel_dismissied_nag_messages', 'true', true );
		}
	}

	function importer_upsell_notice() {

		if ( class_exists( 'WP_Travel_Import_Export_Core' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'import' === $screen->id ) {
			?>
			<div style="margin:34px 20px 10px 10px">
				<?php
					$args = array(
						'title'       => __( 'WP Travel Importer', 'wp-travel' ),
						'content'     => __( 'Import and Export Trips, Bookings, Enquiries, Coupons, Trip Extras and Payments data with portable CSV file.', 'wp-travel' ),
						'link'        => 'https://wptravel.io/wp-travel-pro/',
						'link_label'  => __( 'Get WP Travel Pro', 'wp-travel' ),
						'link2'       => 'https://wptravel.io/downloads/wp-travel-import-export/',
						'link2_label' => __( 'Get WP Travel Import/Export Addon', 'wp-travel' ),
					);
					wptravel_upsell_message( $args );
					?>
			</div>
			<?php
		}

	}
	function display_general_admin_notices( $display ) {

		// Show notices channel if gdpr isn't dismissed.
		global $wp_version;
		$user_id = get_current_user_id();
		if ( version_compare( $wp_version, '4.9.6', '>' ) && ! get_user_meta( $user_id, 'wp_travel_dismissied_nag_messages' ) ) {
			$display = true;
		}
		// End of Show notices channel if gdpr isn't dismissed.
		// Test Mode.
		if ( wptravel_test_mode() ) {
			$display = true;
		}
		// Test Mode Ends.
		return $display;
	}

	function general_admin_notices() {
		// GDPR.
		global $wp_version;
		$user_id = get_current_user_id();
		if ( version_compare( $wp_version, '4.9.6', '>' ) && ! get_user_meta( $user_id, 'wp_travel_dismissied_nag_messages' ) ) {
			?>
			<div>
				<p>
					<strong>
						<?php echo esc_html__( 'WP Travel is ', 'wp-travel' ); ?><b> <?php echo esc_html__( ' GDPR ', 'wp-travel' ); ?> </b><?php echo esc_html__( ' compatible now. Please go to ', 'wp-travel' ); ?><a href="<?php echo esc_url( admin_url( 'privacy.php' ) ); ?>"><?php echo esc_html__( ' Settings > Privacy ', 'wp-travel' ); ?></a><?php echo esc_html__( ' to select Privacy Policy page. ', 'wp-travel' ); ?><a href="?wp-travel-dismissed-nag"><?php echo esc_html__( 'Dismiss this Message', 'wp-travel' ); ?></a>
					</strong>
				</p>
			</div>
			<?php
		}
		// GDPR Ends.
		// Test Mode.
		if ( wptravel_test_mode() ) {
			?>
			<div>
				<p>
				<strong>
					<?php echo esc_html__( '"WP Travel" plugin is currently in test mode. ', 'wp-travel' ); ?><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-debug' ) );?>"><?php echo esc_html__( ' Click here ', 'wp-travel' ); ?></a><?php echo esc_html__( ' to disable test mode.', 'wp-travel' ); ?>
				</strong>
			</div>
			<?php
		}
		// Test Mode Ends.
	}

}

new WP_Travel_Admin_Info_Pointers();
