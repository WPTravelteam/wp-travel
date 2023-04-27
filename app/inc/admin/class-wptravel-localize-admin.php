<?php
/**
 * Admin Localize file.
 *
 * @package WP_Travel
 *
 */

/**
 * WpTravel_Localize_Admin class.
 */
class WpTravel_Localize_Admin {
	/**
	 * Init.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'localize_data' ) );
	}

	/**
	 * Localize data function.
	 *
	 * // @todo Need to Move this into into WpTravel_Helpers_Localize::get(); of WpTravel_Frontend_Assets class.
	 *
	 * @return void
	 */
	public static function localize_data() {
		$screen         = get_current_screen();
		$allowed_screen = array( WP_TRAVEL_POST_TYPE, 'edit-' . WP_TRAVEL_POST_TYPE, 'itinerary-enquiries', 'wptravel_template', 'edit-wptravel_template' );
		$settings       = wptravel_get_settings();
		$theme_datas    = array();

		/**
		 * @since 6.1.0
		 * added condition for loading theme only setup page
		 */
		if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
			$theme_lists = array(
				array(
					'slug'       => 'travel-log',
					'theme_page' => 'https://wensolutions.com/themes/travel-log/',
				),
				array(
					'slug'       => 'travel-buzz',
					'theme_page' => 'https://wensolutions.com/themes/travel-buzz/',
				),
				array(
					'slug'       => 'travel-joy',
					'theme_page' => 'https://wensolutions.com/themes/travel-joy/',
				),
				array(
					'slug'       => 'travel-one',
					'theme_page' => 'https://wensolutions.com/themes/travel-one/',
				),
				array(
					'slug'       => 'travelstore',
					'theme_page' => 'https://wensolutions.com/themes/travelstore/',
				),
				array(
					'slug'       => 'travel-ocean',
					'theme_page' => 'https://wensolutions.com/themes/travel-ocean/',
				),
				array(
					'slug'       => 'travel-escape',
					'theme_page' => 'https://wensolutions.com/themes/travel-escape/',
				),
				array(
					'slug'       => 'bloguide',
					'theme_page' => 'https://themepalace.com/downloads/bloguide/',
				),
				array(
					'slug'       => 'ultravel',
					'theme_page' => 'https://themepalace.com/downloads/ultravel/',
				),
				array(
					'slug'       => 'travelism',
					'theme_page' => 'https://themepalace.com/downloads/travelism/',
				),
				array(
					'slug'       => 'swingpress',
					'theme_page' => 'https://themepalace.com/downloads/swingpress/',
				),
				array(
					'slug'       => 'wen-travel',
					'theme_page' => 'https://themepalace.com/downloads/wen-travel/',
				),
				array(
					'slug'       => 'travel-life',
					'theme_page' => 'https://themepalace.com/downloads/travel-life/',
				),
				array(
					'slug'       => 'top-travel',
					'theme_page' => 'https://themepalace.com/downloads/top-travel/',
				),
				array(
					'slug'       => 'next-travel',
					'theme_page' => 'https://themepalace.com/downloads/next-travel/',
				),
				array(
					'slug'       => 'travel-master',
					'theme_page' => 'https://themepalace.com/downloads/travel-master/',
				),
				array(
					'slug'       => 'tale-travel',
					'theme_page' => 'https://themepalace.com/downloads/tale-travel/',
				),
				array(
					'slug'       => 'travel-ultimate',
					'theme_page' => 'https://themepalace.com/downloads/travel-ultimate/',
				),
				array(
					'slug'       => 'travel-gem',
					'theme_page' => 'https://themepalace.com/downloads/travel-gem/',
				),
				array(
					'slug'       => 'tourable',
					'theme_page' => 'https://themepalace.com/downloads/tourable/',
				),
				array(
					'slug'       => 'travel-base',
					'theme_page' => 'https://themepalace.com/downloads/travel-base/',
				),
				array(
					'slug'       => 'pleased',
					'theme_page' => 'https://themepalace.com/downloads/pleased/',
				),
				array(
					'slug'       => 'travel-insight',
					'theme_page' => 'https://themepalace.com/downloads/travel-insight/',
				),
			);

			$theme_datas = array();
			if ( property_exists( themes_api( 'theme_information', array( 'slug' => 'travel-joy' ) ), 'errors' ) == false ) {
				foreach ( $theme_lists as $data ) {

					$is_installed = in_array( $data['slug'], array_keys( wp_get_themes() ) ) ? 'yes' : 'no';
					$is_active    = $data['slug'] == wp_get_theme()->get( 'TextDomain' ) ? 'yes' : 'no';

					$get_theme_data = themes_api(
						'theme_information',
						array(
							'slug'   => $data['slug'],
							'fields' => array( 'sections' => false ),
						)
					);

					$theme_data['title']          = $get_theme_data->name;
					$theme_data['theme_page']     = $data['theme_page'];
					$theme_data['slug']           = str_replace( '-', '_', $get_theme_data->slug );
					$theme_data['screenshot_url'] = $get_theme_data->screenshot_url;
					$theme_data['download_link']  = $get_theme_data->download_link;
					$theme_data['is_installed']   = $is_installed;
					$theme_data['is_active']      = $is_active;
					
					array_push( $theme_datas, $theme_data );
				}
			} else {
				$theme_datas = 1;
			}
		}

		function get_pro_version() {
			$all_plugins = get_plugins();
			$pro_version = $all_plugins['wp-travel-pro/wp-travel-pro.php']['Version'];
			return (float)$pro_version;
		}
		
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		$translation_array = array(
			'_nonce'             => wp_create_nonce( 'wp_travel_nonce' ),
			'admin_url'          => admin_url(),
			'plugin_url'         => plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ),
			'is_pro_enable'      => class_exists( 'WP_Travel_Pro' ) ? 'yes' : 'no',
			'pro_version'		 => class_exists( 'WP_Travel_Pro') ? get_pro_version() : null,
			'plugin_name'        => 'WP Travel',
			'is_blocks_enable'	 => class_exists( 'WPTravel_Blocks' ) ? true : false,
			'dev_mode'           => wptravel_dev_mode(),
			'theme_datas'        => $theme_datas,
			'currency'           => $settings['currency'],
			'currency_position'  => $settings['currency_position'],
			'currency_symbol'    => wptravel_get_currency_symbol(),
			'number_of_decimals' => $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0,
			'decimal_separator'  => $settings['decimal_separator'] ? $settings['decimal_separator'] : '.',
			'thousand_separator' => $settings['thousand_separator'] ? $settings['thousand_separator'] : ',',
			'activated_plugins'			=> get_option( 'active_plugins' ),
			'wpml_migratio_dicription'	=> __( 'Use to migrate WP Travel compatible with WPML. After enable please save setting and then click migration button.', 'wp-travel' ),
			'wpml_label'				=> __( 'WPML Migrations', 'wp-travel' ),
			'wpml_btn_label'			=> __( 'Migrate', 'wp-travel' ),
			'diable_wpml_text'			=> __( 'Please save setting before migrate.', 'wp-travel' ),
		);

		// trip edit page.
		if ( in_array( $screen->id, $allowed_screen, true ) ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-admin-trip-options', '_wp_travel', $translation_array );
		}

		// Coupon Page.
		if ( 'wp-travel-coupons' === $screen->id ) {
			$translation_array['postID'] = get_the_ID();
			wp_localize_script( 'wp-travel-coupons-backend-js', '_wp_travel', $translation_array );
		}

		$react_settings_enable = apply_filters( 'wp_travel_settings_react_enabled', true ); // @phpcs:ignore
		$react_settings_enable = apply_filters( 'wptravel_settings_react_enabled', $react_settings_enable );
		if ( $react_settings_enable && WP_Travel::is_page( 'settings', true ) ) { // settings page.
		}
		wp_localize_script( 'wp-travel-admin-settings', '_wp_travel', $translation_array );  // temp fixes to use localized data.

		if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
			wp_localize_script( 'wp-travel-setup-page-js', '_wp_travel', $translation_array );  // temp fixes to use
		}
	}
}

WpTravel_Localize_Admin::init();
