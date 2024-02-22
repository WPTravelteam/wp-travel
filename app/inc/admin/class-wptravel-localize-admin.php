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
            'is_woo_enable'                 => class_exists( 'WooCommerce' ) ? 'yes' : 'no',
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

        $translation_array['setting_strings'] = array( 
            'unsaved_changes' => __( 'Unsaved changes', 'wp-travel' ),
            'tab_name' => array(
                'general' => __( 'General', 'wp-travel' ),
                'trips' => __( 'Trips', 'wp-travel' ),
                'email' => __( 'Email', 'wp-travel' ),
                'account' => __( 'Account', 'wp-travel' ),
                'checkout' => __( 'Checkout', 'wp-travel' ),
                'payment' => __( 'Payment', 'wp-travel' ),
                'invoice' => __( 'Invoice', 'wp-travel' ),
                'miscellaneous' => __( 'Miscellaneous', 'wp-travel' ),
                'advanced' => __( 'Advanced', 'wp-travel' ),
            ),
            'sub_tab_name' => array(
                'currency' => __( 'Currency', 'wp-travel' ),
                'maps' => __( 'Maps', 'wp-travel' ),
                'pages' => __( 'Pages', 'wp-travel' ),
                'archive_page_title' => __( 'Archive Page Title', 'wp-travel' ),
                'facts' => __( 'Facts', 'wp-travel' ),
                'faqs' => __( 'FAQs', 'wp-travel' ),
                'trip_settings' => __( 'Trips Settings', 'wp-travel' ),
                'field_editor' => __( 'Field Editor', 'wp-travel' ),
                'tabs' => __( 'Tabs', 'wp-travel' ),
                'general_email_settings' => __( 'General Email Settings', 'wp-travel' ),
                'email_templates' => __( 'Email Templates', 'wp-travel' ),
                'account' => __( 'Account', 'wp-travel' ),
                'checkout' => __( 'Checkout', 'wp-travel' ),
                'payment' => __( 'Payment', 'wp-travel' ),
                'conditional_payment' => __( 'Conditional Payment', 'wp-travel' ),
                'invoice' => __( 'Invoice', 'wp-travel' ),
                'misc_options' => __( 'Miscellaneous Options', 'wp-travel' ),
                'advanced_gallery' => __( 'Advanced Gallery', 'wp-travel' ),
                'recaptcha_v2' => __( 'reCaptcha V2', 'wp-travel' ),
                'third_party' => __( 'Third Party Integrations', 'wp-travel' ),
                'modules_settings' => __( 'Modules Settings', 'wp-travel' ),
                'pwa' => __( 'PWA', 'wp-travel' ),
                'debug' => __( 'Debug', 'wp-travel' )
            ),
            'currency' => array(
                'currency_settings' => __( 'Currency Settings', 'wp-travel' ),
                'currency' => __( 'Currency', 'wp-travel' ),
                'currency_note' => __( 'Choose currency you accept payments in.', 'wp-travel' ),
                'use_currency_name' => __( 'Use Currency Name', 'wp-travel' ),
                'use_currency_name_tooltip' => __( 'This option will display currency name instead of symbol in frontend. ( E.g USD instead of $. )', 'wp-travel' ),
                'currency_position' => __( 'Currency Position', 'wp-travel' ),
                'currency_position_note' => __( 'Choose currency position.', 'wp-travel' ),
                'thousand_separator' => __( 'Thousand separator', 'wp-travel' ),
                'thousand_separator_note' => __( 'This sets the thousand separator of displayed prices.', 'wp-travel' ),
                'decimal_separator' => __( 'Decimal separator', 'wp-travel' ),
                'decimal_separator_note' => __( 'This sets the decimal separator of displayed prices.', 'wp-travel' ),
                'number_decimals' => __( 'Number of decimals', 'wp-travel' ),
                'number_decimals_note' => __( 'This sets the number of decimal of displayed prices.', 'wp-travel' )
            ),
            'maps' => array(
                'maps_settings' => __('Maps Settings', 'wp-travel'),
                'select_map' => __('Select Map', 'wp-travel'),
                'select_map_note' => __('Choose your map provider to display map in site.', 'wp-travel'),
                'api_key'   => __('API Key', 'wp-travel'),
                'api_key_tooltip'   => __('If you dont have API Key, you can use Map by using Lat/Lng or Location from location tab under trip edit page', 'wp-travel'),
                'api_key_note'   => __('To get your Google map V3 API keys', 'wp-travel'),
                'api_key_link_label'   => __('click here', 'wp-travel'),
                'zoom_level' => __('Zoom Level', 'wp-travel'),
                'zoom_level_note' => __('Set default zoom level of map.', 'wp-travel'),
                'use_api_key' => __('Use APP Key', 'wp-travel'),
                'use_api_key_tooltip' => __('With the new version of Here Map, API APP Code is depreciate, And App Key is introduce', 'wp-travel'),
                'app_key' => __('App Key', 'wp-travel'),
                'app_id' => __('App ID', 'wp-travel'),
                'app_id_note' => __('Dont have App ID and App Code', 'wp-travel'),
                'app_code' => __('App Code', 'wp-travel'),
                'select_marker_style' => __('Select a marker style', 'wp-travel'),
                'select_marker_style_note' => __('Here Maps default marker will be used', 'wp-travel'),
                'map_icon' => __('Map Icon', 'wp-travel'),
                'map_icon_note' => __('You can upload your custom map icon from here. If no image found, default will be used.', 'wp-travel'),
                'map_zoom_level' => __('Map Zoom Level', 'wp-travel')
            ),
            'pages' => array(
                'pages_setting' => __( 'Pages Settings', 'wp-travel' ),
                'checkout_page' => __( 'Checkout Page', 'wp-travel' ),
                'checkout_page_tooltip' => __( 'Choose the page to use as checkout page for booking which contents checkout page shortcode [wp_travel_checkout].', 'wp-travel' ),
                'dashboard_page' => __( 'Dashboard Page', 'wp-travel' ),
                'dashboard_page_tooltip' => __( 'Choose the page to use as dashboard page which contents dashboard page shortcode [wp_travel_user_account].', 'wp-travel' ),
                'thankyou_page' => __( 'Thank You Page', 'wp-travel' ),
                'thankyou_page_tooltip' => __( 'Choose the page to use as thankyou page which contents thankyou page shortcode [wp_travel_thankyou].', 'wp-travel' ),
            ),
            'archive_page_title' => array(
                'archive_page_title_settings' => __('Archive Page Title Settings', 'wp-travel'),
                'hide_plugin_archive_page_title' => __('Hide Plugin Archive Page Title', 'wp-travel'),
                'hide_plugin_archive_page_title_note' => __('This option will hide archive title displaying from plugin.', 'wp-travel'),
            ),
            'license' => array(
                'license' => __('License', 'wp-travel'),
                'wp_travel_pro' => __('', 'wp-travel'),
            ),

            'facts' => array(
                'facts_settings' => __('Facts Settings', 'wp-travel'),
                'trip_facts' => __('Trip Fact', 'wp-travel'),
                'trip_facts_note' => __('Enable Trip Facts display on trip single page.', 'wp-travel'),
                'add_new' => __('+ Add New', 'wp-travel'),
                'field_name' => __('Field Name', 'wp-travel'),
                'field_name_label' => __('Enter Field name', 'wp-travel'),
                'field_type' => __('Field Type', 'wp-travel'),
                'values' => __('Values', 'wp-travel'),
                'values_label' => __('Add an option and press Enter', 'wp-travel'),
                'values_note' => __('Separate with commas or the Enter key.', 'wp-travel'),
                'icon' => __('Icon', 'wp-travel'),
                'choose_icon' => __('Choose Icon', 'wp-travel'),
                'remove_note' => __('Are you sure to delete Fact?', 'wp-travel'),
                'remove_fact' => __('- Remove Fact', 'wp-travel'),
            ),
            'faqs' => array(
                'global_faqs' => __('Global FAQs', 'wp-travel'),
                'global_faqs_note' => __('Use the shortcode below to display the global FAQs data.', 'wp-travel'),
                'no_faq_found' => __('No FAQ found.', 'wp-travel'),
                'add_faq' => __('Add Faq', 'wp-travel'),
                'add_new' => __('+ Add New', 'wp-travel'),
                'remove_faq' => __('- Remove FAQ', 'wp-travel'),
                'remove_faq_note' => __('Are you sure to delete FAQ?', 'wp-travel'),
                'enter_your_question' => __('Enter Your Question', 'wp-travel'),
                'enter_your_answer' => __('Enter Your Answer', 'wp-travel'),
            ),
            'trip_settings' => array(
                'trips_settings' => __('Trips Settings', 'wp-travel'),
                'custom_trip_codes' => __('Custom Trip Codes', 'wp-travel'),
                'custom_trip_codes_note' => __('Enable Custom Trip Code Support for Trips.', 'wp-travel'),
                'enable_trip_enquiry' => __('Enable Trip Enquiry', 'wp-travel'),
                'hide_related_trips' => __('Hide related trips', 'wp-travel'),
                'hide_related_trips_note' => __('This will hide your related trips.', 'wp-travel'),
                'trip_date_listing' => __('Trip date listing', 'wp-travel'),
                'trip_date_listing_tooltip'   => __('List date while booking or display calendar with available dates. Note: Date option only works for fixed departure trips.', 'wp-travel'),
                'enable_expired_trip_option' => __('Enable Expired Trip Option', 'wp-travel'),
                'enable_expired_trip_option_note' => __('This will enable expired trip set as Expired or delete.', 'wp-travel'),
                'if_expired_trip_set_to_expired_delete' => __('If expired, trip set to expired/delete', 'wp-travel'),
                'disable_star_rating_for_admin' => __('Disable Star Rating For Admin', 'wp-travel'),
                'disable_star_rating_for_admin_note' => __('Enable to not allow star rating to admin', 'wp-travel'),
            ),
            'field_editor' => array(
                'field_editor' => __('Field Editor', 'wp-travel'),
                'trip_enquiry_fields' => __('Trip Enquiry Fields', 'wp-travel'),
                'billing_fields' => __('Billing Fields', 'wp-travel'),
                'traveler_info_fields' => __('Traveler Info Fields', 'wp-travel'),
                'add_field' => __('Add Field', 'wp-travel'),
                'reset_field' => __('Reset Field', 'wp-travel'),
                'field_editor_label' => __('Label', 'wp-travel'),
                'field_editor_name' => __('Name', 'wp-travel'),
                'field_editor_type' => __('Type', 'wp-travel'),
                'field_editor_action' => __('Action', 'wp-travel'),
                'field_editor_edit' => __('Edit', 'wp-travel'),
                'field_editor_delete' => __('Delete', 'wp-travel'),
                'field_editor_cancel' => __('Cancel', 'wp-travel'),
                'field_editor_save' => __('Save', 'wp-travel'),
                'billing_field' => __('Billing Field', 'wp-travel'),
                'enquiry_field' => __('Enquiry Field', 'wp-travel'),
                'travelers_field' => __('Travelers Field', 'wp-travel'),
                'remove_field' => __('Remove Field', 'wp-travel'),
                'remove_field_note' => __('Note: To enable Remove Fields for all Travelers option, you have to uncheck Required for all travelers option.', 'wp-travel'),
                'required_for_all_travelers' => __('Required for all travelers', 'wp-travel'),
                'for_specific_trips' => __('For specific trips.', 'wp-travel'),
                'enable_to_show_on_specific_trips_only' => __('Enable to show on specific trips only.', 'wp-travel'),
                'validation' => __('Validation', 'wp-travel'),
                'required' => __('Required', 'wp-travel'),
                'field_editor_options' => __('Field Editor Options', 'wp-travel'),
                'multiple_traveler' => __('Multiple Traveler', 'wp-travel'),
                'multiple_traveler_tooltip' => __('Enable to use field pattern (Like required or not required) set in "Traveler info fields" and disable to make multiple traveler fields unrequired except first one.', 'wp-travel'),
                'trip_ids'  => __( 'Trip IDs', 'wp-travel'  ),
                'remove_field_travelers' => __( 'Remove Field All Travelers', 'wp-travel' ),
            ),
            'tabs' => array(
                'tabs_settings' => __('Tabs Settings', 'wp-travel'),
                'no_custom_itineraries_tab_found' => __('No custom itineraries tab found.', 'wp-travel'),
                'add_new_tab' => __('Add New Tab', 'wp-travel'),
                'tab_label' => __('Tab Label', 'wp-travel'),
                'tab_content' => __('Tab Contents:', 'wp-travel'),
                'reset_tab_label' => __('Reset Tab Label', 'wp-travel'),
                'default_tab_title' => __('Default Tab Title', 'wp-travel'),
                'custom_tab_title' => __('Custom Tab Title', 'wp-travel'),
                'display' => __('Display', 'wp-travel'),
            ),
            'downloads' => array(
                'downloads' => __('Downloads','wp-travel'),
                'itinerary_downloads_options' => __('Itinerary Downloads Options','wp-travel'),
                'enable_itinerary_downloads' => __('Enable Itinerary Downloads','wp-travel'),
                'path_settings' => __('Path Settings','wp-travel'),
                'use_relative_path' => __('Use Relative Path','wp-travel'),
                'use_relative_path_tooltip' => __('Use image path as var/www/html... instead of http to generate pdf itinerary.','wp-travel'),
                'display_settings' => __('Display Settings','wp-travel'),
                'show_trip_date_in_pdf' => __('Show Trip Date in PDF','wp-travel'),
                'show_trip_pricing_in_pdf' => __('Show Trip pricing in PDF','wp-travel'),
                'show_trip_includes_and_excludes_in_pdf' => __('Show Trip Includes and Excludes in PDF','wp-travel'),
                'show_trip_overview_in_pdf' => __('Show Trip Overview in PDF','wp-travel'),
                'show_trip_outline_in_pdf' => __('Show Trip Outline in PDF','wp-travel'),
                'show_trip_itineraries_in_pdf' => __('Show Trip Itineraries in PDF','wp-travel'),
                'color_scheme' => __('Color Scheme','wp-travel'),
                'set_pdf_primary_color' => __('Set PDF primary color','wp-travel'),
                'set_pdf_secondary_color' => __('Set PDF secondary color','wp-travel'),
            ),
            'email' => array(
                'general_email_settings' => __('General Email Settings','wp-travel'),
                'from_email' => __('From Email','wp-travel'),
                'from_email_tooltip' => __('Preferred to use webmail like: sales@yoursite.com','wp-travel'),
                'remove_powered_by_text' => __('Remove Powered By Text','wp-travel'),
                'remove_powered_by_text_note' => __('Remove "Powered By" text for all email.','wp-travel'),
                'custom_powered_by_text' => __('Custom Powered By Text','wp-travel'),
                'custom_powered_by_text_note' => __('Custom footer "Powered By" text for all email.','wp-travel'),
            ),        
            'email_tempaltes' => array(
                'to_emails'=> __('To Emails','wp-travel'),
                'email_templates_settings'=> __('Email Templates Settings','wp-travel'),
                'attached_itinerary_pdf' => __('Attached itinerary PDF','wp-travel'),
                'attached_itinerary_pdf_note' => __('Enable to send booking email attached with itinerary PDF.','wp-travel'),
                'admin_email_template_options' => __('Admin Email Template Options','wp-travel'),
                'admin_email_template_options_send_email' => __('Send Email','wp-travel'),
                'admin_email_template_options_send_email_note' => __('Enable or disable Email notification to admin.','wp-travel'),
                'admin_email_template_options_to_emails' => __('To Emails','wp-travel'),
                'admin_email_template_options_to_emails_note' => __('Separate with commas or the Enter key.','wp-travel'),
                'booking_email_subject' => __('Booking Email Subject','wp-travel'),
                'booking_email_title' => __('Booking Email Title','wp-travel'),
                'booking_email_header_color' => __('Booking Email Header Color','wp-travel'),
                'email_content' => __('Email Content','wp-travel'),
                'client_email_template_options' => __('Client Email Template Options','wp-travel'),
                'booking_email_templates' => __('Booking Email Templates','wp-travel'),
                'payment_email_templates' => __('Payment Email Templates','wp-travel'),
                'enquiry_email_templates' => __('Enquiry Email Templates','wp-travel'),
                'enquiry_email_subject' => __('Enquiry Email Subject','wp-travel'),
                'enquiry_email_title' => __('Enquiry Email Title','wp-travel'),
                'enquiry_email_header_color' => __('Enquiry Email Header Color','wp-travel'),
                'invoice_email_templates' => __('Invoice Email Templates','wp-travel'),
                'invoice_email_subject' => __('Invoice Email Subject','wp-travel'),
                'invoice_email_title' => __('Invoice Email Title','wp-travel'),
                'partial_payment_email_templates' => __('Partial Payment Email Templates','wp-travel'),
                'payment_email_subject' => __('Payment Email Subject','wp-travel'),
                'payment_email_title' => __('Payment Email Title','wp-travel'),
                'payment_email_header_color' => __('Payment Email Header Color','wp-travel'),
                'remaining_partial_payment_remainder' => __('Remaining Partial Payment Reminder','wp-travel'),
                'enable_remainder_notification' => __('Enable Remainder Notification','wp-travel'),
                'enable_remainder_notification_note' => __('Enable to send remaining payment notifications','wp-travel'),
                'email_cycle' => __('Email Cycle','wp-travel'),
                'reminder_title' => __('Reminder Title','wp-travel'),
            ),
            'account' => array(
                'account' => __('Account', 'wp-travel'),
                'require_login' => __('Require Login', 'wp-travel'),
                'require_login_note' => __('Require Customer login or register before booking.', 'wp-travel'),
                'enable_registration' => __('Enable Registration', 'wp-travel'),
                'enable_registration_note' => __('Enable customer registration on the "My Account" page.', 'wp-travel'),
                'create_customer_on_booking' => __('Create customer on booking', 'wp-travel'),
                'create_customer_on_booking_note' => __('This will create WP Travel Customer once the booking has been done.', 'wp-travel'),
                'automatically_generate_username' => __('Automatically generate username', 'wp-travel'),
                'automatically_generate_username_note' => __('Automatically generate username from customer email.', 'wp-travel'),
                'automatically_generate_password' => __('Automatically generate password', 'wp-travel'),
                'automatically_generate_password_note' => __('Automatically generate customer password.', 'wp-travel'),
        
            ),
            'checkout' => array(
                'checkout' => __('Checkout', 'wp-travel'),
                'enable_woo_checkout_label' => __('Enable Woocommerce Checkout','wp-travel'),
                'enable_woo_checkout_note' => __('By enabling WooCommerce Checkout default checkout function will replaced by WooCommerce Checkout.','wp-travel'),
                'price_unavailable_text' => __('Price Unavailable Text', 'wp-travel'),
                'price_unavailable_text_note' => __('Price unavailable text for trips with empty or 0 prices.', 'wp-travel'),
                'select_booking_option' => __('Select Booking Option','wp-travel'),
                'select_booking_option_note' => __('Note: Any payment gateway must be enable inorder to work this options. If payment is not enabled then Booking only option will work.','wp-travel'),
                'enable_multiple_checkout_add_to_cart' => __('Enable multiple checkout (Add to cart)','wp-travel'),
                'enable_multiple_checkout_add_to_cart_note' => __('Add multiple trips on checkout page.)','wp-travel'),
                'enable_multiple_travelers' => __('Enable multiple travelers','wp-travel'),
                'enable_multiple_travelers_note' => __('Collect multiple travelers information from checkout page.','wp-travel'),
                'enable_on_page_booking' => __('Enable On-Page Booking','wp-travel'),
                'enable_on_page_booking_tooltip' => __('To enable On-Page booking, make sure you have disabled multiple checkout (Add to cart) option.','wp-travel'),
                'enable_on_page_booking_note' => __('To book a trip without redirecting to the checkout page.','wp-travel'),
            ),
            'invoice' => array(
                'invoice' => __('Invoice', 'wp-travel'),
                'use_relative_path' => __('Use Relative Path', 'wp-travel'),
                'use_relative_path_note' => __('Use image path as var/www/html... instead of http to generate pdf invoice.', 'wp-travel'),
                'invoice_logo' => __('Invoice Logo', 'wp-travel'),
                'change_image' => __('Change Image', 'wp-travel'),
                'select_image' => __('Select Image', 'wp-travel'),
                'invoice_address' => __('Invoice Address', 'wp-travel'),
                'invoice_contact' => __('Invoice Contact', 'wp-travel'),
                'invoice_website' => __('Invoice Website', 'wp-travel'),
            ),
            'payment' => array(
                'payment' => __('Payment', 'wp-travel'),
                'test_mode_active'=> __('Test Mode Active', 'wp-travel'),
                'live_mode_active'=> __('Live Mode Active', 'wp-travel'),
                'partial_payment' => __('Partial Payment', 'wp-travel'),
                'partial_payment_note' => __('Enable Partial Payment while booking.', 'wp-travel'),
                'partial_payout_note' => __('Minimum percent of amount to pay while booking.', 'wp-travel'),
                'in_amount' => __('In Amount', 'wp-travel'),
                'partial_amount' => __('Partial Amount', 'wp-travel'),
                'minimum_payout' => __('Minimum Payout', 'wp-travel'),
                'partial_payout_one' => __('Partial Payout 1 (%)', 'wp-travel'),
                'partial_payout_two' => __('Partial Payout 2 (%)', 'wp-travel'),
                'partial_payout_three' => __('Partial Payout 3 (%)', 'wp-travel'),
                'partial_payout_four' => __('Partial Payout 4 (%)', 'wp-travel'),
                'remove_partial_payment' => __('Remove Partial Payment', 'wp-travel'),
                'remove_partial_payment' => __('Remove Partial Payment', 'wp-travel'),
                'enable_disable' => __('Enable/Disable All', 'wp-travel'),
                'add_partial_payment' => __('Add Partial payment', 'wp-travel'),
                'remove_partial_payment' => __('Remove Partial payment', 'wp-travel'),
                'partial_error_notice' => __('Error: Total payout percent must be equals to 100%', 'wp-travel'),

                'payment_gateways' => __('Payment gateways', 'wp-travel'),

                'standard_paypal' => __('Standard Paypal', 'wp-travel'),
                'enable_paypal' => __('Enable Paypal', 'wp-travel'),
                'enable_paypal_note' => __('Check to enable Standard PayPal payment.', 'wp-travel'),
                'paypal_email' => __('Paypal Email', 'wp-travel'),
                'paypal_email_note' => __('PayPal email address that receive payment.', 'wp-travel'),

                'bank_deposit' => __('Bank Deposit', 'wp-travel'),
                'bank_deposit_enable' => __('Enable', 'wp-travel'),
                'bank_deposit_enable_note' => __('Check to enable Bank deposit.', 'wp-travel'),
                'bank_deposit_enable_description' => __('Description.', 'wp-travel'),
                'account_detail' => __('Account Detail.', 'wp-travel'),
                'add_new' => __('+ Add New', 'wp-travel'),
                'remove_bank' => __('- Remove Bank', 'wp-travel'),
                'remove_bank_note' => __('Are you sure to delete Bank Detail?', 'wp-travel'),

                'enable_instamojo_checkout' => __('Enable Instamojo Checkout', 'wp-travel'),
                'enable_instamojo_checkout_note' => __('Check to enable Instamojo Checkout.', 'wp-travel'),
                'private_api_key' => __('Private API Key', 'wp-travel'),
                'private_auth_token' => __('Private Auth Token', 'wp-travel'),
                'private_auth_token_note' => __('Get your credentials ', 'wp-travel'),
                'instamojo_checkout_note' => __('Note: This payment gateway works only with Indian (INR) currency.', 'wp-travel'),

                'enable_khalti' => __('Enable Khalti','wp-travel'),
                'enable_khalti_note' => __('Check to enable Khalti Checkout','wp-travel'),
                'test_public_key' => __('Test Public Key','wp-travel'),
                'test_secret_key' => __('Test Secret Key','wp-travel'),
                'live_public_key' => __('Live Public Key','wp-travel'),
                'live_secret_key' => __('Live Secret Key','wp-travel'),

                'enable_payu' => __('Enable PayU','wp-travel'),
                'enable_payu_note' => __('Check to enable PayU Checkout.','wp-travel'),
                'merchant_pos_id' => __('Merchant POS ID','wp-travel'),
                'merchant_pos_id_note' => __('Please register here to get Merchant Account and credentials needed here.','wp-travel'),
                'second_key_md_5' => __('Second Key (MD5)','wp-travel'),
                'cliend_id' => __('Client ID','wp-travel'),
                'cliend_secret' => __('Client Secret','wp-travel'),
        
                'enable_payu_latam' => __('Enable PayU Latam','wp-travel'),
                'enable_payu_latam_note' => __('Check to enable PayU Latam Checkout.','wp-travel'),
                'merchant_id' => __('Merchant ID','wp-travel'),
                'merchant_id_note' => __('Please open your here to get Merchant Id and the other credentials needed here.','wp-travel'),
                'account_id' => __('Account ID','wp-travel'),
                'api_login' => __('API Login','wp-travel'),
                'api_key' => __('API Key','wp-travel'),
                'tax_amount' => __('Tax Amount','wp-travel'),
                'tax_amount_note' => __('This is the transaction VAT. If VAT zero is sent the system, 19% will be applied automatically. It can contain two decimals. Eg 19000.00. In the where you do not charge VAT, it should should be set as 0.','wp-travel'),
                'tax_retrun_base_amount' => __('Tax Return Base Amount','wp-travel'),
                'tax_retrun_base_amount_note' => __('This is the base value upon which VAT is calculated. If you do not charge VAT, it should be sent as 0.','wp-travel'),
        
                'enable_payfast' => __('Enable PayFast','wp-travel'),
                'enable_payfast_note' => __('Check to enable PayFast Checkout.','wp-travel'),
                'merchant_id' => __('Merchant ID','wp-travel'),
                'merchant_key' => __('Merchant Key','wp-travel'),
                'passphrase' => __('Passphrase','wp-travel'),
        
                'enable_payhere' => __('Enable PayHere','wp-travel'),
                'enable_payhere_note' => __('Check to enable PayHere Checkout.','wp-travel'),
                'payhere_merchant_id' => __('Merchant ID','wp-travel'),
                'payhere_merchant_seceret' => __('Merchant Secret','wp-travel'),
                'payhere_merchant_id_note' => __('Please register here to get Merchant ID and Secret Key.','wp-travel'),
        
                'enable_paypal_express_checkout' => __('Enable Paypal Express Checkout','wp-travel'),
                'enable_paypal_express_checkout_note' => __('Check to enable PayPal Express Checkout','wp-travel'),
                'sandbox_client_id' => __('Sandbox Client ID','wp-travel'),
                'production_client_id' => __('Production Client ID','wp-travel'),
                'sandbox_client_id_note' => __('Get API credentials from here','wp-travel'),
                'paypal_express_checkout_advance_options' => __('Advanced Options','wp-travel'),
                'paypal_express_checkout_advance_color' => __('Color','wp-travel'),
                'paypal_express_checkout_advance_shape' => __('Shape','wp-travel'),
                'paypal_express_checkout_advance_size' => __('Size','wp-travel'),
                'paypal_express_checkout_advance_label' => __('Label','wp-travel'),
                'paypal_express_checkout_allow_payments_using_cards' => __('Allow payments using cards','wp-travel'),
        
        
                'enable_razorpay_checkout' => __('Enable Razorpay Checkout','wp-travel'),
                'enable_razorpay_checkout_note' => __('Check to enable Razorpay Checkout.','wp-travel'),
                'razorpay_checkout_key_id' => __('Key ID','wp-travel'),
                'razorpay_checkout_key_secret' => __('Key Secret','wp-travel'),
                'razorpay_checkout_info' => __('Note: This payment gateway works only with Indian (INR) currency.','wp-travel'),
        
                'enable_squareup_checkout' => __('Enable Squareup Checkout','wp-travel'),
                'enable_squareup_checkout_note' => __('Check to enable Squareup Checkout','wp-travel'),
                'squareup_checkout_info' => __('Note:USD, GBP, AUD, CAD and YEN are the accepted curriencies by Squareup Checkout so please make sure to match the currency of WP Travel currency settings and the currency of your account for Squareup Checkout.','wp-travel'),
                'squareup_checkout_access_token' => __('Access Token','wp-travel'),
                'squareup_checkout_access_token_info' => __('Please register here to get Access Token, Application ID and Location ID.','wp-travel'),
                'squareup_checkout_access_application_id' => __('Application ID','wp-travel'),
                'squareup_checkout_access_location_id' => __('Location ID','wp-travel'),
        
        
                'enable_stripe' => __('Enable Stripe','wp-travel'),
                'enable_stripe_note' => __('Check to enable Stripe Checkout','wp-travel'),
                'stripe_checkout_test_publishable_key' => __('Test Publishable Key','wp-travel'),
                'stripe_checkout_test_secret_key' => __('Test Secret Key','wp-travel'),
                'stripe_checkout_live_publishable_key' => __('Live Publishable Key','wp-travel'),
                'stripe_checkout_live_secret_key' => __('Live Secret Key','wp-travel'),
        
                'enable_stripe_ideal' => __('Enable Stripe iDeal','wp-travel'),
                'enable_stripe_ideal_note' => __('Check to enable Stripe iDEAL Checkout.','wp-travel'),
                'stripe_webhook' => __('Stripe Webhook','wp-travel'),
                'stripe_webhook_note' => __('The webhook URLs are required for Stripe to notify any time an event happens in your account and helps you to track the transaction status. Without webhook transaction status cannot be updated. The plugin automatically registers a webhook after updating the settings with the correct credentials.','wp-travel'),
                'stripe_webhook_test_webhook' => __('Test Webhook','wp-travel'),
                'stripe_webhook_test_webhook_note' => __('(Make sure to save changes after api key is changed before testing.)','wp-travel'),
                'stripe_ideal_test_publishable_key' => __('Test Publishable Key','wp-travel'),
                'stripe_ideal_test_secret_key' => __('Test Secret Key','wp-travel'),
                'stripe_ideal_live_publishable_key' => __('Live Publishable Key','wp-travel'),
                'stripe_ideal_live_secret_key' => __('Live Secret Key','wp-travel'),
        
                'enable_authorize_net' => __('Enable Authorize.Net','wp-travel'),
                'enable_authorize_net_note' => __('Check to enable Authorize.net Checkout.','wp-travel'),
                'authorize_net_login_id' => __('Login ID ','wp-travel'),
                'authorize_net_login_id_note' => __('Get api credentialas ','wp-travel'),
                'authorize_net_transaction_key' => __('Transaction Key','wp-travel'),
                'authorize_net_transaction_key_validate_acc_and_currency' => __('Validate Account And Currency','wp-travel'),
                'authorize_net_transaction_key_validate_acc_and_currency_note' => __('(Make sure to save changes after key is changed before validation.)','wp-travel'),
                'authorize_net_note' => __('Note: Please Revalidate the account and currency if the key is changed.','wp-travel'),
                
                'enable_paystack' => __('Enable Paystack','wp-travel'),
                'enable_paystack_note' => __('Check to enable Paysatck Checkout','wp-travel'),
                'paystack_test_public_key' => __('Test Public Key','wp-travel'),
                'paystack_live_public_key' => __('Live Public Key','wp-travel'),
                'paystack_note' => __('Note: Go To Modules Settings To Enable Other Payment Gateways.','wp-travel'),
                
                'tax_options' => __('Tax Options','wp-travel'),
                'tax_options_enable_tax' => __('Enable Tax','wp-travel'),
                'tax_options_enable_tax_note' => __('Check to enable Tax options for trips.','wp-travel'),
                'tax_options_tax_on_trip_prices' => __('Tax on Trip prices','wp-travel'),
                'tax_options_tax_on_trip_prices_option_1' => __('Yes, I will enter trip prices inclusive of tax','wp-travel'),
                'tax_options_tax_on_trip_prices_option_2' => __('No, I will enter trip prices exclusive of tax','wp-travel'),
                'tax_options_tax_on_trip_prices_note' => __('This option will affect how you enter trip prices.','wp-travel'),
                'tax_options_tax_percentage' => __('Tax Percentage','wp-travel'),
                'tax_options_tax_percentage_note' => __('Trip Tax percentage added to trip price.','wp-travel'),
            ),
            'conditional_payment' => array(
                'conditional_payment' => __('Conditional Payment','wp-travel'),
                'select_billing_country' => __( 'Select Billing Country', 'wp-travel' ),
                'select_trip_location' => __( 'Select Trip Location', 'wp-travel' ),
                'select_payment_gateway' => __( 'Select Payment Gateway', 'wp-travel' ),
                'enable' => __( 'Enable', 'wp-travel' ),
                'enable_note' => __( 'Enable to apply conditional Payment.', 'wp-travel' ),
                'enable_by_billing_address' => __( 'Enable By Billing Address', 'wp-travel' ),
                'remove' => __( '- Remove Conditional Payment', 'wp-travel' ),
                'remove_note' => __( 'Are you sure to delete Conditional Payment?', 'wp-travel' ),
            ),
            'advance' => array(
                'modules_settings' => __('Modules Settings', 'wp-travel'),
                'modules' => __('Modules', 'wp-travel'),
                'pro_modules' => __('Pro Modules','wp-travel'),
                'enable_disable_all_pro_modules' => __('Enable/Disable All Pro Modules','wp-travel'),
                'pro_modules_advance_gallery' => __('Advance Gallery','wp-travel'),
                'pro_modules_note' => __('Show all your ','wp-travel'),
                'pro_modules_note2' => __('  settings and enable its feature','wp-travel'),

                'payment_modules' => __('Payment Modules','wp-travel'),
                'enable_disable_all_payment_modules' => __('Enable/Disable All Payment Modules','wp-travel'),

                'map_modules' => __('Map Modules','wp-travel'),
                'advance_modules_note' => __('Note: Please reload page after enabling/disabling modules.','wp-travel'),
            ),
            'debug' => array(
                'debug' => __('Debug', 'wp-travel'),
                'debug_test_payment' => __('Test Payment', 'wp-travel'),
                'debug_test_mode' => __('Test Mode','wp-travel'),
                'debug_test_mode_note' => __('Enable test mode to make test payment.','wp-travel'),
                'debug_test_email' => __('Test Email','wp-travel'),
                'debug_test_email_note' => __('Test email address will get test mode payment emails.','wp-travel'),
        
                'optimized_scripts_styles' => __('Optimized Scripts and Styles','wp-travel'),
                'load_combined_scripts' => __('Load Combined Scripts','wp-travel'),
                'load_combined_scripts_note' => __('Enabling this will load the bundled scripts files.','wp-travel'),
                'load_minified_scripts' => __('Load Minified Scripts','wp-travel'),
                'load_minified_scripts_note' => __('Enabling this will load minified scripts.','wp-travel'),           
            ),
            'pwa' => array(
                'pwa' => __('PWA', 'wp-travel'),
                'pwa_settings' => __('PWA Settings', 'wp-travel'),
                'enable_pwa' => __('Enable PWA','wp-travel'),
                'enable_pwa_note' => __('Enable to activate PWA on your site','wp-travel'),
                'app_fullname' => __('App Fullname','wp-travel'),
                'app_fullname_note' => __('This sets the App fullname','wp-travel'),
                'app_short_name' => __('App short name','wp-travel'),
                'app_short_name_note' => __('This sets the App short name','wp-travel'),
                'start_url' => __('Start Url','wp-travel'),
                'start_url_note' => __('This sets the start url for the app','wp-travel'),
                'app_logo' => __('APP Logo','wp-travel'),
                'app_logo_note' => __('The image must be of size 192px*192px','wp-travel'),
            ),
            'miscellaneous' => array(
                'miscellaneous_settings' => __('Miscellaneous Settings', 'wp-travel'),
                'gdpr_message' => __('GDPR Message', 'wp-travel'),
                'open_gdpr_new_tab' => __('Open GDPR in new tab', 'wp-travel'),
            ),
            'advanced_gallery' => array(
                'advanced_gallery' => __('Advanced Gallery', 'wp-travel'),
                'advanced_gallery_settings' => __('Advanced Gallery Settings', 'wp-travel'),
                'gallery_display_style' => __('Gallery Display Style', 'wp-travel'),
                'autoplay_slides' => __('Autoplay Slides', 'wp-travel'),
                'show_dots' => __('Show Dots', 'wp-travel'),
                'show_arrows' => __('Show Arrows', 'wp-travel'),
                'num_of_slides' => __('Number of Slides', 'wp-travel'),
            ),
            'recaptcha_v2' => array(
                'recaptcha_v2' => __('reCaptcha V2', 'wp-travel'),
                'recaptcha_checkbox' => __('reCAPTCHA (V2 Checkbox)','wp-travel'),
                're_site_key' => __('Site Key','wp-travel'),
                're_site_note' => __('Enter your ','wp-travel'),
                're_secret_key' => __('Secret Key','wp-travel'),
            ),
            'third_party_integrations' => array(
                'third_party_integrations' => __('Third Party Integrations', 'wp-travel'),
                'fixer_api' => __('Fixer API','wp-travel'),
                'save_changes' => __('Save changes after key is changed.','wp-travel'),
        
        
                'use_api_layer_fixer_api' => __('Use API Layer Fixer API','wp-travel'),
                'use_api_layer_fixer_api_tooltip' => __('Requires API Layer Fixer API Key instead of regular Fixer API Key','wp-travel'),
        
                'enter_api_access_key' => __('Enter Your API Access Key','wp-travel'),
                'enter_api_access_key_note' => __('Click here to get your API Access key.','wp-travel'),
                'premium_api_key_subscription' => __('Premium API Key Subscription','wp-travel'),
        
                'currency_exchange' => __('Currency Exchange','wp-travel'),
                'set_api_timer_reset' => __('Set API Timer Reset','wp-travel'),
                'set_api_timer_reset_note' => __('Refresh after:','wp-travel'),
                'purge_api_cache' => __('Purge API Cache','wp-travel'),
                'purge_api_cache_note' => __('Purge cache to force refresh the API.','wp-travel'),
                'purge_button' => __('Purge','wp-travel'),
        
        
                'google_calendar' => __('Google Calendar','wp-travel'),
                'google_calendar_note' => __('You need to create google console project from ','wp-travel'),
                'google_calendar_note2' => __(' for Google Api Client id & Google Api Client Secret','wp-travel'),
        
                'gcalendar_client_id' => __('Cliend ID','wp-travel'),
                'gcalendar_client_id_note' => __('Enter your google api client id','wp-travel'),
                'gcalendar_client_secret_key' => __('Client Secret','wp-travel'),
                'gcalendar_client_secret_key_note' => __('Enter your google api client secret','wp-travel'),
                'redorect_url' => __('Redirect URL','wp-travel'),
                'redorect_url_tooltip' => __('Your redirect url i.e: https://wpdemo.wensolutions.com/travel-buzz-pro/wp-admin/edit.php?post_type=itinerary-booking&page=settings','wp-travel'),
        
                'weather_forecast_api' => __('Weather Forecast API','wp-travel'),
                'enter_your_api_key' => __('Enter Your API Key','wp-travel'),
                'enter_your_api_key_note' => __('Click here to get an APP ID','wp-travel'),
        
                'wishlists_options' => __('Wishlists Options','wp-travel'),
                'add_to_wishlist_icon' => __('"Add to wishlist" Icon','wp-travel'),
                'remove_from_wishlist_icon' => __('"Remove from wishlist" Icon','wp-travel'),
        
                'add_to_wishlist_text' => __('"Add to wishlist" Text','wp-travel'),
                'add_to_wishlist_text_note' => __('Text to replace "Add to wishlist"','wp-travel'),
                'remove_from_wishlist_text' => __('"Remove from wishlist" Text','wp-travel'),
                'remove_from_wishlist_text_note' => __('Text to replace "Remove from wishlist"','wp-travel'),
                'wishlists_options_icon_color' => __('Icon Color','wp-travel'),
                'wishlists_options_icon_color_note' => __('Choose a color for icon.','wp-travel'),
        
                'zapier_automation' => __('Zapier Automation','wp-travel'),
                'enable_enquiry_automation' => __('Enable Enquiry Automation','wp-travel'),
                'enter_enquiry_webhook' => __('Enter Enquiry Webhook','wp-travel'),
                'enquiry_webhook_test_enquiry_zap_button' => __('Test Enquiry Zap','wp-travel'),
                'enable_bookings_automation' => __('Enable Bookings Automation','wp-travel'),
                'enter_bookings_webhook' => __('Enter Bookings Webhook','wp-travel'),
                'enquiry_webhook_test_booking_zap_button' => __('Test Booking Zap','wp-travel'),
        
                'multiple_currency' => __('Multiple Currency','wp-travel'),
        
                'base_currency' => __('Base Currency','wp-travel'),
                'use_geo_location' => __('Use Geo Location','wp-travel'),
                'use_geo_location_tooltip' => __('If enabled, the manual currency selector option will be disabled from the frontend.','wp-travel'),
        
                'menu_location' => __('Menu Location','wp-travel'),
                'menu_location_tooltip' => __('Select the menu location where you want to display the currency selector.','wp-travel'),
                'select_currencies' => __('Select Currencies','wp-travel'),
                'select_currencies_note' => __('Select only those currencies which are supported by your payment gateway.','wp-travel'),
                'reset_cache' => __('Reset Cache','wp-travel'),
                'reset_cache_tooltip' => __('Cache automatically replaced with new data in every 4 hours, to force reset click "Reset" button.','wp-travel'),
                'reset_cache_btn' => __('Reset','wp-travel'),
        
                'mailchimp_settings' => __('Mailchimp Settings','wp-travel'),
                'mailchimp_settings_api' => __('API Key','wp-travel'),
                'mailchimp_settings_api_note' => __('Enter your Mailchimp API Key.','wp-travel'),
                'generate_lists' => __('Generate Lists','wp-travel'),
                'generate_lists_note' => __('Save changes after the key is changed.','wp-travel'),
        
                'mailchimp_select_list' => __('Select List','wp-travel'),
        
                'mailchimp_form' => __('Form','wp-travel'),
        
                'mailchimp_opt_in' => __('Opt-in','wp-travel'),
                'mailchimp_opt_in_tooltip' => __('Enabling this option will enable the Mailchimp double opt-in option i.e sends contact an opt-in confirmation email when they subscribe. For details ','wp-travel'),
                'mailchimp_opt_in_note' => __('For more details:','wp-travel'),
                'mailchimp_subscribe_label' => __('Subscribe Label','wp-travel'),
                'mailchimp_subscribe_label_note' => __('This text will used as label to Opt-In checkbox','wp-travel'),
                'mailchimp_description' => __('Subscribe Description','wp-travel'),
                'mailchimp_description_note' => __('You can tell why you want your customers to subscribe.','wp-travel'),
            ),  
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
        wp_localize_script( 'wptravel_google_calendar_admin-settings', '_wp_travel', $translation_array );
		if ( get_current_screen()->base == 'dashboard_page_wp-travel-setup-page' ) {
			wp_localize_script( 'wp-travel-setup-page-js', '_wp_travel', $translation_array );  // temp fixes to use
		}

	}
}

WpTravel_Localize_Admin::init();
