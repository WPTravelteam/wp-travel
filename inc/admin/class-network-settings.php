<?php
/**
 * Admin Settings.
 *
 * @package WP_Travel
 */

/**
 * Class for admin settings.
 */
class WpTravel_Network_Settings {
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
	public static $collection = 'wp_travel_network_settings';
	/**
	 * Constructor.
	 */
	public function __construct() {

		self::$parent_slug = 'network/admin.php?page=wp_travel_network_settings';
		add_filter( 'wp_travel_admin_tabs', array( $this, 'add_tabs' ) );

		$collection      = self::$collection;
		$tab_hook_prefix = "wp_travel_tabs_content_{$collection}";
		$wp_travel_tabs  = new WP_Travel_Admin_Tabs();
		$tabs            = $wp_travel_tabs->list_by_collection( $collection );
		if ( is_array( $tabs ) && count( $tabs ) > 0 ) {
			foreach ( $tabs as $tab_key => $tab ) {
				$filename          = str_replace( '_', '-', $tab_key ) . '.php';
				$callback_file     = sprintf( '%sinc/admin/views/tabs/tab-contents/settings/%s', WP_TRAVEL_ABSPATH, $filename );
				$callback_function = isset( $tab['callback'] ) ? $tab['callback'] : '';
				if ( file_exists( $callback_file ) ) {
					require_once $callback_file;
				}
				if ( ! empty( $callback_function ) && function_exists( $callback_function ) ) {
					add_action( "{$tab_hook_prefix}_{$tab_key}", $callback_function, 12, 2 );
				}
			}
		}

		// Save Settings.
		add_action( 'load-toplevel_page_wp_travel_network_settings', array( $this, 'save_settings' ) );
	}

	/**
	 * Call back function for Settings menu page. [ inc > admin > class-admin-menu.php]
	 */
	public static function setting_page_callback() {

		$args['settings']       = wptravel_get_settings();
		$url_parameters['page'] = self::$collection;
		$url                    = admin_url( self::$parent_slug );
		$url                    = add_query_arg( $url_parameters, $url );
		$sysinfo_url            = add_query_arg( array( 'page' => 'sysinfo' ), $url );

		echo '<div class="wrap wp-trave-settings-warp">';
			echo '<h1>' . esc_html__( 'WP Travel Settings', 'wp-travel' ) . '</h1>';
			echo '<div class="wp-trave-settings-form-warp">';
			do_action( 'wp_travel_before_admin_setting_form' );
			echo '<form method="post" action="' . esc_url( $url ) . '">';
				echo '<div class="wp-travel-setting-buttons">';
				submit_button( __( 'Save Settings', 'wp-travel' ), 'primary', 'save_settings_button', false, array( 'id' => 'save_settings_button_top' ) );
				echo '</div>';
				WPTravel()->tabs->load( self::$collection, $args );
				echo '<div class="wp-travel-setting-buttons">';
				echo '<div class="wp-travel-setting-system-info">';
					echo '<a href="' . esc_url( $sysinfo_url ) . '" title="' . esc_attr__( 'View system information', 'wp-travel' ) . '"><span class="dashicons dashicons-info"></span>';
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

	public static function setting_page_callback_new() {

		?>
			<div id="wp-travel-settings-block-wrapper">
				<div id="wp-travel-network-settings-block"></div>
				<div id="aside-wrap-container">
					<div id="aside-wrap" class="single-module-side">
						<div class="aside-wrap-buttons-container">
							<h2 class="wp-travel-aside-wrap-block-title">
								<span><?php esc_html_e( 'Need Help?', 'wp-travel' ); ?></span>
							</h2>
							<div class="wp-travel-aside-help-block">
							<?php
							wptravel_meta_box_support();
							wptravel_meta_box_documentation();
							?>
							</div>
						</div>
						<?php
							wptravel_meta_box_review();
						?>
					</div>
				</div>
			</div>
		<?php
	}

	/**
	 * Add Tabs to settings page.
	 *
	 * @param array $tabs Tabs array list.
	 */
	public function add_tabs( $tabs ) {

		$settings_fields['license'] = array(
			'tab_label'     => __( 'License', 'wp-travel' ),
			'content_title' => __( 'License Details', 'wp-travel' ),
			'priority'      => 10,
			'callback'      => 'wptravel_settings_callback_license',
			'icon'          => 'fa-id-badge',
		);

		$tabs[ self::$collection ] = wptravel_sort_array_by_priority( apply_filters( 'wp_travel_network_settings_tabs', $settings_fields ) );
		return $tabs;
	}

	/**
	 * Save settings.
	 *
	 * @return void
	 */
	public function save_settings() {
		if ( isset( $_POST['save_settings_button'] ) ) {
			check_admin_referer( 'wp_travel_settings_page_nonce' );
			$current_tab = isset( $_POST['current_tab'] ) ? sanitize_text_field( wp_unslash( $_POST['current_tab'] ) ) : '';

			$settings = array();

			// @since 1.0.5 Used this filter below.
			$settings = apply_filters( 'wp_travel_before_save_settings', $settings );

			update_option( 'wp_travel_settings', $settings );
			WPTravel()->notices->add( 'error ' );
			$url_parameters['page']    = self::$collection;
			$url_parameters['updated'] = 'true';
			$redirect_url              = admin_url( self::$parent_slug );
			$redirect_url              = add_query_arg( $url_parameters, $redirect_url ) . '#' . $current_tab;
			wp_safe_redirect( $redirect_url );
			exit();
		}
	}

	/**
	 * System info.
	 */
	public static function get_system_info() {
		require_once sprintf( '%s/inc/admin/views/status.php', WP_TRAVEL_ABSPATH );
	}
}

new WpTravel_Network_Settings();

add_action( 'wp_travel_license_tab_fields', 'wptravel_license_tab_content', 100 );

/**
 * License content.
 */
function wptravel_license_tab_content( $args ) {
	$settings = WPTravel_Helpers_Modules::settings_data( $args );
	$modules  = $settings['premium_addons_data'];

	foreach ( $modules as $module ) {
		?>
		<div class="license_grid">
				<div class="form_field">
					<h3>
						<label class="control-label label_title">
							<?php echo esc_html( $module['item_name'] ); ?>
						</label>
					</h3>
					<div class="subject_input">
						<a href=<?php echo esc_url( $module['license_link'] ); ?> title="Add License">Add License</a> |
						<a href="<?php echo esc_url( $module['account_link'] ); ?>" title="Manage License">Manage License</a>  
					</div>
				</div>
			</div>
		<?php
	}
}



