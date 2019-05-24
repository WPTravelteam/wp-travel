<?php
/**
 * Admin Settings.
 *
 * @package inc/admin
 */

/**
 * Class for admin settings.
 */
class WP_Travel_Network_Settings {
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
		
		$settings_fields['license']                       = array(
			'tab_label'     => __( 'License', 'wp-travel' ),
			'content_title' => __( 'License Details', 'wp-travel' ),
			'priority'      => 10,
			'callback'      => 'wp_travel_settings_callback_license',
			'icon'          => 'fa-id-badge',
		);
		
		$tabs[ self::$collection ] = wp_travel_sort_array_by_priority( apply_filters( 'wp_travel_network_settings_tabs', $settings_fields ) );
		return $tabs;
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
			
			$settings = array();

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

new WP_Travel_Network_Settings();
