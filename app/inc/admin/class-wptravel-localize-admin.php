<?php
/**
 * Admin Localize file.
 *
 * @package WP_Travel
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
                    'name'       => 'Travel Knock',
                    'slug'       => 'travel-knock',
                    'theme_page' => 'https://wensolutions.com/themes/travel-knock/',
                    'screenshot' => 'http://wpdemo.wensolutions.com/wp-content/uploads/2023/12/Travel-Knock-2-min.png',
                ),
                array(
                    'name'       => 'Travelaero',
                    'slug'       => 'travelaero',
                    'theme_page' => 'https://wensolutions.com/themes/travelaero/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/09/description-banner.png',
                ),
                array(
                    'name'       => 'WP Yatra',
                    'slug'       => 'wp-yatra',
                    'theme_page' => 'https://wensolutions.com/themes/wp-yatra/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/elementor/thumbs/hero-qbkhhc4a7nv7rej5i8ckxpsgrxhtscltq568froge8.png',
                ),
                array(
                    'name'       => 'Travelin',
                    'slug'       => 'travelin',
                    'theme_page' => 'https://wensolutions.com/themes/travelin/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Travelin-min.png',
                ),
                array(
                    'name'       => 'Travelvania',
                    'slug'       => 'travelvania',
                    'theme_page' => 'https://wensolutions.com/themes/travelvania/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Travelvania-min.png',
                ),
                array(
                    'name'       => 'WP Travel Fse', 
                    'slug'       => 'wp-travel-fse',
                    'theme_page' => 'https://wensolutions.com/themes/wp-travel-fse/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/07/WebCapture.local_-1024x950.jpeg',
                ),
                array(
                    'name'       => 'Travel Init',
                    'slug'       => 'travel-init',
                    'theme_page' => 'https://wensolutions.com/themes/travel-init/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/09/Travel-Init-2.png',
                ),
                array(
                    'name'       => 'Travel Log',
                    'slug'       => 'travel-log',
                    'theme_page' => 'https://wensolutions.com/themes/travel-log-pro/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/08/Travel-Log-Pro1.png',
                ),
                array(
                    'name'       => 'Travel Buzz',
                    'slug'       => 'travel-buzz',
                    'theme_page' => 'https://wensolutions.com/themes/travel-buzz-pro/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/08/Travel-Buzz.png',
                ),
                array(
                    'name'       => 'Travel Joy',
                    'slug'       => 'travel-joy',
                    'theme_page' => 'https://wensolutions.com/themes/travel-joy-pro/ ',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/12/Travel-joy.png',
                ),
                array(
                    'name'       => 'Travel One',
                    'slug'       => 'travel-one',
                    'theme_page' => 'https://wensolutions.com/themes/travel-one/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/08/Travel-Ocean-1.png',
                ),
                array(
                    'name'       => 'Travelstore',
                    'slug'       => 'travelstore',
                    'theme_page' => 'https://wensolutions.com/themes/travelstore/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/07/Travelstore-2-1529x1536.png',
                ),
                array(
                    'name'       => 'Travel Ocean',
                    'slug'       => 'travel-ocean',
                    'theme_page' => 'https://wensolutions.com/themes/travel-ocean/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/07/Travel-ocean-1-1309x1536.png',
                ),
                array(
                    'name'       => 'Gotrip',
                    'slug'       => 'gotrip',
                    'theme_page' => 'https://www.eaglevisionit.com/downloads/gotrip/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Gotrip-–-Discover-amazing-things-to-do-everywhere-you-go-min.png',
                ),
                array(
                    'name'       => 'Travel Store',
                    'slug'       => 'travel-store',
                    'theme_page' => 'https://www.eaglevisionit.com/downloads/travel-store/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Travel-Store-min.png',
                ),
                array(
                    'name'       => 'Travel FSE',
                    'slug'       => 'travel-fse',
                    'theme_page' => 'https://www.eaglevisionit.com/downloads/travel-fse/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Travel-FSE-min.png',
                ),
                array(
                    'name'       => 'Travel Ride',
                    'slug'       => 'travel-ride',
                    'theme_page' => 'https://www.eaglevisionit.com/downloads/travel-ride/',
                    'screenshot' => 'https://wpdemo.wensolutions.com/wp-content/uploads/2023/11/Travel-Ride-min.png',
                ),
                array(
                    'name'       => 'Travel Escape',
                    'slug'       => 'travel-escape',
                    'theme_page' => 'https://wensolutions.com/themes/travel-escape-pro/',
                    'screenshot' => 'https://wensolutions.com/wp-content/uploads/2023/08/Travel-Escape-1536x1343.png',
                ),
                array(
                    'name'       => 'Bloguide',
                    'slug'       => 'bloguide',
                    'theme_page' => 'https://themepalace.com/downloads/bloguide/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2022/11/bloguide-large.jpg',
                ),
                array(
                    'name'       => 'Ultravel',
                    'slug'       => 'ultravel',
                    'theme_page' => 'https://themepalace.com/downloads/ultravel/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2022/07/ultravel-free.jpg',
                ),
                array(
                    'name'       => 'Travelism',
                    'slug'       => 'travelism',
                    'theme_page' => 'https://themepalace.com/downloads/travelism/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2022/04/travelism-large.jpg',
                ),
                array(
                    'name'       => 'Swingpress',
                    'slug'       => 'swingpress',
                    'theme_page' => 'https://themepalace.com/downloads/swingpress/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2022/02/swingpress-large.jpg',
                ),
                array(
                    'name'       => 'Wen Travel',
                    'slug'       => 'wen-travel',
                    'theme_page' => 'https://themepalace.com/downloads/wen-travel/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2021/07/wen-travel-free-large.jpg',
                ),
                array(
                    'name'       => 'Travel Life',
                    'slug'       => 'travel-life',
                    'theme_page' => 'https://themepalace.com/downloads/travel-life/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2021/07/travel-life-large.jpg',
                ),
                array(
                    'name'       => 'Top Travel',
                    'slug'       => 'top-travel',
                    'theme_page' => 'https://themepalace.com/downloads/top-travel/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2021/05/top-travel-large.jpg',
                ),
                array(
                    'name'       => 'Next Travel',
                    'slug'       => 'next-travel',
                    'theme_page' => 'https://themepalace.com/downloads/next-travel/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/edd/2021/04/next-travel-free-large.jpg',
                ),
                array(
                    'name'       => 'Travel Master',
                    'slug'       => 'travel-master',
                    'theme_page' => 'https://themepalace.com/downloads/travel-master/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2019/12/travel-master-large.jpg',
                ),
                array(
                    'name'       => 'Tale Travel',
                    'slug'       => 'tale-travel',
                    'theme_page' => 'https://themepalace.com/downloads/tale-travel/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2019/02/tale-travel-large.jpg',
                ),
                array(
                    'name'       => 'Travel Ultimate',
                    'slug'       => 'travel-ultimate',
                    'theme_page' => 'https://themepalace.com/downloads/travel-ultimate/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2018/12/travel-route-large-1.jpg',
                ),
                array(
                    'name'       => 'Travel Gem',
                    'slug'       => 'travel-gem',
                    'theme_page' => 'https://themepalace.com/downloads/travel-gem/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2018/11/travel-gem-large.jpg',
                ),
                array(
                    'name'       => 'Tourable',
                    'slug'       => 'tourable',
                    'theme_page' => 'https://themepalace.com/downloads/tourable/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2018/11/tourable-large.jpg',
                ),
                array(
                    'name'       => 'Travel Base',
                    'slug'       => 'travel-base',
                    'theme_page' => 'https://themepalace.com/downloads/travel-base/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2019/01/travel-base-large.jpg',
                ),
                array(
                    'name'       => 'Pleased',
                    'slug'       => 'pleased',
                    'theme_page' => 'https://themepalace.com/downloads/pleased/',   
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2018/10/pleased-large.jpg',                 
                ),
                array(
                    'name'       => 'Travel Insight',
                    'slug'       => 'travel-insight',
                    'theme_page' => 'https://themepalace.com/downloads/travel-insight/',
                    'screenshot' => 'https://themepalace.com/wp-content/uploads/2017/08/travel-insight-large.jpg',  
                ),
            );

			$theme_datas = array();

			foreach ( $theme_lists as $data ) {

				$theme_data['title']          = $data['name'];
				$theme_data['theme_page']     = $data['theme_page'];
				$theme_data['slug']           = $data['slug'];
				$theme_data['screenshot_url'] = $data['screenshot'];

				array_push( $theme_datas, $theme_data );
			}			
		}

		function get_pro_version() {
			$all_plugins = get_plugins();
			$pro_version = $all_plugins['wp-travel-pro/wp-travel-pro.php']['Version'];
			return (float) $pro_version;
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		$translation_array = array(
			'_nonce'                        => wp_create_nonce( 'wp_travel_nonce' ),
			'admin_url'                     => admin_url(),
			'site_url'                      => site_url(),
			'plugin_url'                    => plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ),
			'is_pro_enable'                 => class_exists( 'WP_Travel_Pro' ) ? 'yes' : 'no',
			'is_conditional_payment_enable' => class_exists( 'WP_Travel_Conditional_Payment_Core' ) ? 'yes' : 'no',
			'is_conditional_payment_active' => is_plugin_active( 'wp-travel-conditional-payment/wp-travel-conditional-payment.php' ) ? 'yes' : 'no',
			'pro_version'                   => class_exists( 'WP_Travel_Pro' ) ? get_pro_version() : null,
			'plugin_name'                   => 'WP Travel',
			'is_blocks_enable'              => class_exists( 'WPTravel_Blocks' ) ? true : false,
			'dev_mode'                      => wptravel_dev_mode(),
			'theme_datas'                   => $theme_datas,
			'currency'                      => $settings['currency'],
			'currency_position'             => $settings['currency_position'],
			'currency_symbol'               => wptravel_get_currency_symbol(),
			'number_of_decimals'            => $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0,
			'decimal_separator'             => $settings['decimal_separator'] ? $settings['decimal_separator'] : '.',
			'thousand_separator'            => $settings['thousand_separator'] ? $settings['thousand_separator'] : ',',
			'activated_plugins'             => get_option( 'active_plugins' ),
			'wpml_migratio_dicription'      => __( 'Use to migrate WP Travel compatible with WPML. After enable please save setting and then click migration button.', 'wp-travel' ),
			'wpml_label'                    => __( 'WPML Migrations', 'wp-travel' ),
			'wpml_btn_label'                => __( 'Migrate', 'wp-travel' ),
			'diable_wpml_text'              => __( 'Please save setting before migrate.', 'wp-travel' ),
			'wp_settings'                   => WP_Travel_Helpers_Settings::get_settings(),
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
