<?php
/**
 * Admin Info Pointers
 *
 * @package WP Travel
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

		// add_filter( 'wp_travel_admin_pointers-plugins', array( $this, 'add_plugin_pointers' ) );
		// add_filter( 'wp_travel_admin_pointers-'. WP_TRAVEL_POST_TYPE, array( $this, 'add_single_post_edit_screen_pointers' ) );
		// add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'add_dashboard_screen_pointers' ) );
		add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'menu_order_changed' ) );
		add_filter( 'wp_travel_admin_pointers-dashboard', array( $this, 'new_trips_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_pointers' ), 999 );

		// Admin General Notices.
		add_action( 'admin_notices', array( $this, 'wp_travel_paypal_merge_notice' ) );
		add_action( 'admin_notices', array( $this, 'wp_travel_update_payment_gateways_notice' ) );
		add_action( 'admin_notices', array( $this, 'wp_travel_importer_upsell_notice' ) );
		add_action( 'admin_init', array( $this, 'wp_travel_get_dismissied_nag_messages' ) );

		add_filter( 'wp_travel_display_general_admin_notices', array( $this, 'display_general_admin_notices' ) );
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
		wp_register_script( 'wp-travel-admin-pointers-js', plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . '/assets/js/wp-travel-backend-pointers.js', array( 'wp-pointer' ) );

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

	function paypal_addon_admin_notice() {

		if ( ! is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {

			$class = 'notice notice-info is-dismissible'; ?>

			<div class="<?php echo esc_attr( $class ); ?>">
			<p>
			<strong><?php printf( __( 'Want to add payment gateway in WP Travel booking? %1$1sDownload "Standard PayPal"%2$2s addon for free!!', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/downloads/standard-paypal/">', '</a>' ); ?></strong>
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
						<strong><?php printf( __( 'You are using older version of WP Travel Standard paypal. Please %1$1sDownload version 1.0.1 Now %2$3s.', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/downloads/standard-paypal/">', '</a>' ); ?></strong>
						</p>
						<p>
						<strong><?php printf( __( 'Need help With the update ? %1$1sClick here%2$2s for detailed instructions on updating the plugin.', 'wp-travel' ), '<a target="_blank" href="http://wptravel.io/documentations/standard-paypal/updating-wp-travel-standard-paypal/">', '</a>' ); ?></strong>
						</p>
					</div>

					<?php
				}
			}
		}
	}

	/**
	 * wp_travel_paypal_merge_notice
	 *
	 * WP Travel Standard paypal merge info.
	 *
	 * @since 1.2
	 */
	function wp_travel_paypal_merge_notice() {

		if ( is_plugin_active( 'wp-travel-standard-paypal/wp-travel-paypal.php' ) ) {
			$user_id = get_current_user_id();

			if ( ! get_user_meta( $user_id, 'wp_travel_dismissied_nag_messages' ) ) {
				?>
				<div class="notice notice-info is-dismissible">
					<p>
					<strong><?php printf( __( 'WP Travel Standard Paypal plugin will be merged to WP Travel in the next update of WP Travel Plugin( v.1.2.1 ). Please make sure to deactivate the WP Travel Standard Paypal plugin before updating to next WP Travel Release.  %1$1sDismiss this Message%2$2s', 'wp-travel' ), '<a href="?wp-travel-dismissed-nag">', '</a>' ); ?></strong>
					</p>
				</div>
				<?php
			}
		}
	}


	/**
	 * wp_travel_update_payment_gateways_notice
	 *
	 * WP Travel Standard paypal merge info.
	 *
	 * @since 1.4.0
	 */
	function wp_travel_update_payment_gateways_notice() {

		$addons = array( 'wp-travel-instamojo/wp-travel-instamojo-checkout.php', 'wp-travel-paypal-express-checkout/wp-travel-paypal-express-checkout.php', 'wp-travel-razor-pay/wp-travel-razorpay-checkout.php', 'wp-travel-stripe/wp-travel-stripe.php' );

		foreach ( $addons as $addon ) {

			if ( is_plugin_active( $addon ) ) {

				$addon_data = @get_plugin_data( WP_PLUGIN_DIR . '/' . $addon );

				if ( version_compare( $addon_data['Version'], '1.0.1', '<' ) ) {

					?>
						<div class="notice notice-warning">
							<p>
							<strong><?php printf( __( 'With the update to WP Travel version 1.4.0 <strong>%1$1s addon </strong> needs to be updated to work, for more information, %2$2sClick Here%3$3s', 'wp-travel' ), $addon_data['Name'], '<a target="_blank" href="http://wptravel.io/category/release-notes/">', '</a>' ); ?></strong>
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
	function wp_travel_get_dismissied_nag_messages() {

		$user_id = get_current_user_id();

		if ( isset( $_GET['wp-travel-dismissed-nag'] ) ) {
			add_user_meta( $user_id, 'wp_travel_dismissied_nag_messages', 'true', true );
		}
	}

	function wp_travel_importer_upsell_notice() {

		if ( class_exists( 'WP_Travel_Import_Export_Core' ) ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'import' === $screen->id ) {
			?>
			<div style="margin:34px 20px 10px 10px">
				<?php
					$args = array(
						'title'      => __( 'WP Travel Importer', 'wp-travel' ),
						'content'    => __( 'Import and Export Trips, Bookings, Enquiries, Coupons, Trip Extras and Payments data with portable CSV file.', 'wp-travel' ),
						'link'       => 'https://wptravel.io/wp-travel-pro/',
        				'link_label' => __( 'Get WP Travel Pro', 'wp-travel' ),
						'link2'       => 'https://wptravel.io/downloads/wp-travel-import-export/',
						'link2_label' => __( 'Get WP Travel Import/Export Addon', 'wp-travel' ),
					);
					wp_travel_upsell_message( $args );
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
		if ( wp_travel_test_mode() ) {
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
				<p><strong><?php printf( __( 'WP Travel is %1$s GDPR %2$scompatible now. Please go to %3$s Settings > Privacy %4$s to select Privacy Policy page. %5$sDismiss this Message%6$s', 'wp-travel' ), '<b>', '</b>', '<a href="' . admin_url( 'privacy.php' ) . '">', '</a>', '<a href="?wp-travel-dismissed-nag">', '</a>' ); ?></strong></p>
			</div>
			<?php
		}
		// GDPR Ends.
		// Test Mode.
		if ( wp_travel_test_mode() ) {
			?>
			<div>
				<p><strong><?php printf( __( '"WP Travel" plugin is currently in test mode. <a href="%1$s">Click here</a> to disable test mode.', 'wp-travel' ), esc_url( admin_url( 'edit.php?post_type=itinerary-booking&page=settings#wp-travel-tab-content-debug' ) ) ); ?></strong></p>
			</div>
			<?php
		}
		// Test Mode Ends.
	}

}

new WP_Travel_Admin_Info_Pointers();
