<?php

Class WP_Travel_API_Settings {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_settings_api_end_points' ) );

	}

	public function wp_travel_settings_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/update-settings',
			array(
				'methods'             => 'put',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_update_settings' ),
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-settings',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_settings' ),
			)
		);
	}

	public function wp_travel_update_settings( WP_REST_Request $request ){

		$settings_data = get_option( 'wp_travel_settings' );

		$update_data = array();

		$currency_setting = array(
			'currency' => ( $request->get_param( 'currency' ) !== null ) ? ( !empty( $request->get_param( 'currency' ) ) ? $request->get_param( 'currency' ) : $settings_data['currency'] ) : $settings_data['currency'],
			'use_currency_name' => ( $request->get_param( 'use_currency_name' ) !== null ) ? ( !empty( $request->get_param( 'use_currency_name' ) ) ? $request->get_param( 'use_currency_name' ) : $settings_data['use_currency_name'] ) : $settings_data['use_currency_name'],
			'currency_position' => ( $request->get_param( 'currency_position' ) !== null ) ? ( !empty( $request->get_param( 'currency_position' ) ) ? $request->get_param( 'currency_position' ) : $settings_data['currency_position'] ) : $settings_data['currency_position'],
			'thousand_separator' => ( $request->get_param( 'thousand_separator' ) !== null ) ? ( !empty( $request->get_param( 'thousand_separator' ) ) ? $request->get_param( 'thousand_separator' ) : $settings_data['thousand_separator'] ) : $settings_data['thousand_separator'],
			'decimal_separator' => ( $request->get_param( 'decimal_separator' ) !== null ) ? ( !empty( $request->get_param( 'decimal_separator' ) ) ? $request->get_param( 'decimal_separator' ) : $settings_data['decimal_separator'] ) : $settings_data['decimal_separator'],
			'number_of_decimals' => ( $request->get_param( 'number_of_decimals' ) !== null ) ? ( !empty( $request->get_param( 'number_of_decimals' ) ) ? $request->get_param( 'number_of_decimals' ) : $settings_data['number_of_decimals'] ) : $settings_data['number_of_decimals'],
		);

		$map_setting = array(
			'wp_travel_map' => ( $request->get_param( 'wp_travel_map' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_map' ) ) ? $request->get_param( 'wp_travel_map' ) : $settings_data['wp_travel_map'] ) : $settings_data['wp_travel_map'],
			'google_map_api_key' => ( $request->get_param( 'google_map_api_key' ) !== null ) ? ( !empty( $request->get_param( 'google_map_api_key' ) ) ? $request->get_param( 'google_map_api_key' ) : $settings_data['google_map_api_key'] ) : $settings_data['google_map_api_key'],
			'google_map_zoom_level' => ( $request->get_param( 'google_map_zoom_level' ) !== null ) ? ( !empty( $request->get_param( 'google_map_zoom_level' ) ) ? $request->get_param( 'google_map_zoom_level' ) : $settings_data['google_map_zoom_level'] ) : $settings_data['google_map_zoom_level'],
			'wpt_here_map_app_id' => ( $request->get_param( 'wpt_here_map_app_id' ) !== null ) ? ( !empty( $request->get_param( 'wpt_here_map_app_id' ) ) ? $request->get_param( 'wpt_here_map_app_id' ) : $settings_data['wpt_here_map_app_id'] ) : $settings_data['wpt_here_map_app_id'],
			'wpt_here_map_app_code' => ( $request->get_param( 'wpt_here_map_app_code' ) !== null ) ? ( !empty( $request->get_param( 'wpt_here_map_app_code' ) ) ? $request->get_param( 'wpt_here_map_app_code' ) : $settings_data['wpt_here_map_app_code'] ) : $settings_data['wpt_here_map_app_code'],
			'markerselector' => ( $request->get_param( 'markerselector' ) !== null ) ? ( !empty( $request->get_param( 'markerselector' ) ) ? $request->get_param( 'markerselector' ) : $settings_data['markerselector'] ) : $settings_data['markerselector'],
			'wpt_here_map_zoom_level' => ( $request->get_param( 'wpt_here_map_zoom_level' ) !== null ) ? ( !empty( $request->get_param( 'wpt_here_map_zoom_level' ) ) ? $request->get_param( 'wpt_here_map_zoom_level' ) : $settings_data['wpt_here_map_zoom_level'] ) : $settings_data['wpt_here_map_zoom_level'],
			'wpt_here_map_map_icon' => ( $request->get_param( 'wpt_here_map_map_icon' ) !== null ) ? ( !empty( $request->get_param( 'wpt_here_map_map_icon' ) ) ? $request->get_param( 'wpt_here_map_map_icon' ) : $settings_data['wpt_here_map_map_icon'] ) : $settings_data['wpt_here_map_map_icon'],
		);



		$page_setting = array(
			'checkout_page_id' => ( $request->get_param( 'checkout_page_id' ) !== null ) ? ( !empty( $request->get_param( 'checkout_page_id' ) ) ? $request->get_param( 'checkout_page_id' ) : $settings_data['checkout_page_id'] ) : $settings_data['checkout_page_id'],
			'dashboard_page_id' => ( $request->get_param( 'dashboard_page_id' ) !== null ) ? ( !empty( $request->get_param( 'dashboard_page_id' ) ) ? $request->get_param( 'dashboard_page_id' ) : $settings_data['dashboard_page_id'] ) : $settings_data['dashboard_page_id'],
			'thank_you_page_id' => ( $request->get_param( 'thank_you_page_id' ) !== null ) ? ( !empty( $request->get_param( 'thank_you_page_id' ) ) ? $request->get_param( 'thank_you_page_id' ) : $settings_data['thank_you_page_id'] ) : $settings_data['thank_you_page_id'],
			'hide_plugin_archive_page_title' => ( $request->get_param( 'hide_plugin_archive_page_title' ) !== null ) ? ( !empty( $request->get_param( 'hide_plugin_archive_page_title' ) ) ? $request->get_param( 'hide_plugin_archive_page_title' ) : $settings_data['hide_plugin_archive_page_title'] ) : $settings_data['hide_plugin_archive_page_title'],
		);

		$facts_setting = array(
			'wp_travel_trip_facts_enable' => ( $request->get_param( 'wp_travel_trip_facts_enable' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_trip_facts_enable' ) ) ? $request->get_param( 'wp_travel_trip_facts_enable' ) : $settings_data['wp_travel_trip_facts_enable'] ) : $settings_data['wp_travel_trip_facts_enable'],
			'wp_travel_trip_facts_settings' => ( $request->get_param( 'wp_travel_trip_facts_settings' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_trip_facts_settings' ) ) ? $request->get_param( 'wp_travel_trip_facts_settings' ) : $settings_data['wp_travel_trip_facts_settings'] ) : $settings_data['wp_travel_trip_facts_settings'],
		);

		$faqs_setting = array(
			'wp_travel_utils_global_faq_question' => ( $request->get_param( 'wp_travel_utils_global_faq_question' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_utils_global_faq_question' ) ) ? $request->get_param( 'wp_travel_utils_global_faq_question' ) : $settings_data['wp_travel_utils_global_faq_question'] ) : $settings_data['wp_travel_utils_global_faq_question'],
			'wp_travel_utils_global_faq_answer' => ( $request->get_param( 'wp_travel_utils_global_faq_answer' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_utils_global_faq_answer' ) ) ? $request->get_param( 'wp_travel_utils_global_faq_answer' ) : $settings_data['wp_travel_utils_global_faq_answer'] ) : $settings_data['wp_travel_utils_global_faq_answer'],
		);
	
		$trip_page_setting = array(
			'enable_custom_trip_code_option' => ( $request->get_param( 'enable_custom_trip_code_option' ) !== null ) ? ( !empty( $request->get_param( 'enable_custom_trip_code_option' ) ) ? $request->get_param( 'enable_custom_trip_code_option' ) : $settings_data['enable_custom_trip_code_option'] ) : $settings_data['enable_custom_trip_code_option'],
			'hide_related_itinerary' => ( $request->get_param( 'hide_related_itinerary' ) !== null ) ? ( !empty( $request->get_param( 'hide_related_itinerary' ) ) ? $request->get_param( 'hide_related_itinerary' ) : $settings_data['hide_related_itinerary'] ) : $settings_data['hide_related_itinerary'],
			'trip_date_listing' => ( $request->get_param( 'trip_date_listing' ) !== null ) ? ( !empty( $request->get_param( 'trip_date_listing' ) ) ? $request->get_param( 'trip_date_listing' ) : $settings_data['trip_date_listing'] ) : $settings_data['trip_date_listing'],
			'enable_expired_trip_option' => ( $request->get_param( 'enable_expired_trip_option' ) !== null ) ? ( !empty( $request->get_param( 'enable_expired_trip_option' ) ) ? $request->get_param( 'enable_expired_trip_option' ) : $settings_data['enable_expired_trip_option'] ) : $settings_data['enable_expired_trip_option'],
			'disable_admin_review' => ( $request->get_param( 'disable_admin_review' ) !== null ) ? ( !empty( $request->get_param( 'disable_admin_review' ) ) ? $request->get_param( 'disable_admin_review' ) : $settings_data['disable_admin_review'] ) : $settings_data['disable_admin_review'],
		);

		$field_editor_setting = array(
			'field_pattern_multiple_traveler' => ( $request->get_param( 'field_pattern_multiple_traveler' ) !== null ) ? ( !empty( $request->get_param( 'field_pattern_multiple_traveler' ) ) ? $request->get_param( 'field_pattern_multiple_traveler' ) : $settings_data['field_pattern_multiple_traveler'] ) : $settings_data['field_pattern_multiple_traveler'],
		);

		$global_tab_setting = array(
			'global_tab_settings' => ( $request->get_param( 'global_tab_settings' ) !== null ) ? ( !empty( $request->get_param( 'global_tab_settings' ) ) ? $request->get_param( 'global_tab_settings' ) : $settings_data['global_tab_settings'] ) : $settings_data['global_tab_settings'],
		);

		$general_email_setting = array(
			'wp_travel_from_email' => ( $request->get_param( 'wp_travel_from_email' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_from_email' ) ) ? $request->get_param( 'wp_travel_from_email' ) : $settings_data['wp_travel_from_email'] ) : $settings_data['wp_travel_from_email'],
			'send_booking_email_to_admin' => ( $request->get_param( 'send_booking_email_to_admin' ) !== null ) ? ( !empty( $request->get_param( 'send_booking_email_to_admin' ) ) ? $request->get_param( 'send_booking_email_to_admin' ) : $settings_data['send_booking_email_to_admin'] ) : $settings_data['send_booking_email_to_admin'],
			'email_template_powered_by_text' => ( $request->get_param( 'email_template_powered_by_text' ) !== null ) ? ( !empty( $request->get_param( 'email_template_powered_by_text' ) ) ? $request->get_param( 'email_template_powered_by_text' ) : $settings_data['email_template_powered_by_text'] ) : $settings_data['email_template_powered_by_text'],
		);

		$email_template_setting = array(
			'booking_admin_template_settings' => ( $request->get_param( 'booking_admin_template_settings' ) !== null ) ? ( !empty( $request->get_param( 'booking_admin_template_settings' ) ) ? $request->get_param( 'booking_admin_template_settings' ) : $settings_data['booking_admin_template_settings'] ) : $settings_data['booking_admin_template_settings'],
			'booking_client_template_settings' => ( $request->get_param( 'booking_client_template_settings' ) !== null ) ? ( !empty( $request->get_param( 'booking_client_template_settings' ) ) ? $request->get_param( 'booking_client_template_settings' ) : $settings_data['booking_client_template_settings'] ) : $settings_data['booking_client_template_settings'],
			'payment_admin_template_settings' => ( $request->get_param( 'payment_admin_template_settings' ) !== null ) ? ( !empty( $request->get_param( 'payment_admin_template_settings' ) ) ? $request->get_param( 'payment_admin_template_settings' ) : $settings_data['payment_admin_template_settings'] ) : $settings_data['payment_admin_template_settings'],
			'payment_client_template_settings' => ( $request->get_param( 'payment_client_template_settings' ) !== null ) ? ( !empty( $request->get_param( 'payment_client_template_settings' ) ) ? $request->get_param( 'payment_client_template_settings' ) : $settings_data['payment_client_template_settings'] ) : $settings_data['payment_client_template_settings'],
			'enquiry_admin_template_settings' => ( $request->get_param( 'enquiry_admin_template_settings' ) !== null ) ? ( !empty( $request->get_param( 'enquiry_admin_template_settings' ) ) ? $request->get_param( 'enquiry_admin_template_settings' ) : $settings_data['enquiry_admin_template_settings'] ) : $settings_data['enquiry_admin_template_settings'],
			'invoice_client_template' => ( $request->get_param( 'invoice_client_template' ) !== null ) ? ( !empty( $request->get_param( 'invoice_client_template' ) ) ? $request->get_param( 'invoice_client_template' ) : $settings_data['invoice_client_template'] ) : $settings_data['invoice_client_template'],
			'partial_payment_admin_template' => ( $request->get_param( 'partial_payment_admin_template' ) !== null ) ? ( !empty( $request->get_param( 'partial_payment_admin_template' ) ) ? $request->get_param( 'partial_payment_admin_template' ) : $settings_data['partial_payment_admin_template'] ) : $settings_data['partial_payment_admin_template'],
			'partial_payment_client_template' => ( $request->get_param( 'partial_payment_client_template' ) !== null ) ? ( !empty( $request->get_param( 'partial_payment_client_template' ) ) ? $request->get_param( 'partial_payment_client_template' ) : $settings_data['partial_payment_client_template'] ) : $settings_data['partial_payment_client_template'],
			'remaining_partial_payment_reminder' => ( $request->get_param( 'remaining_partial_payment_reminder' ) !== null ) ? ( !empty( $request->get_param( 'remaining_partial_payment_reminder' ) ) ? $request->get_param( 'remaining_partial_payment_reminder' ) : $settings_data['remaining_partial_payment_reminder'] ) : $settings_data['remaining_partial_payment_reminder'],
		);

		$account_setting = array(
			'enable_checkout_customer_registration' => ( $request->get_param( 'enable_checkout_customer_registration' ) !== null ) ? ( !empty( $request->get_param( 'enable_checkout_customer_registration' ) ) ? $request->get_param( 'enable_checkout_customer_registration' ) : $settings_data['enable_checkout_customer_registration'] ) : $settings_data['enable_checkout_customer_registration'],
			'enable_my_account_customer_registration' => ( $request->get_param( 'enable_my_account_customer_registration' ) !== null ) ? ( !empty( $request->get_param( 'enable_my_account_customer_registration' ) ) ? $request->get_param( 'enable_my_account_customer_registration' ) : $settings_data['enable_my_account_customer_registration'] ) : $settings_data['enable_my_account_customer_registration'],
			'generate_username_from_email' => ( $request->get_param( 'generate_username_from_email' ) !== null ) ? ( !empty( $request->get_param( 'generate_username_from_email' ) ) ? $request->get_param( 'generate_username_from_email' ) : $settings_data['generate_username_from_email'] ) : $settings_data['generate_username_from_email'],
			'generate_user_password' => ( $request->get_param( 'generate_user_password' ) !== null ) ? ( !empty( $request->get_param( 'generate_user_password' ) ) ? $request->get_param( 'generate_user_password' ) : $settings_data['generate_user_password'] ) : $settings_data['generate_user_password'],
		);

		$checkout_setting = array(
			'price_unavailable_text' => ( $request->get_param( 'price_unavailable_text' ) !== null ) ? ( !empty( $request->get_param( 'price_unavailable_text' ) ) ? $request->get_param( 'price_unavailable_text' ) : $settings_data['price_unavailable_text'] ) : $settings_data['price_unavailable_text'],
			'selected_booking_option' => ( $request->get_param( 'selected_booking_option' ) !== null ) ? ( !empty( $request->get_param( 'selected_booking_option' ) ) ? $request->get_param( 'selected_booking_option' ) : $settings_data['selected_booking_option'] ) : $settings_data['selected_booking_option'],
			'enable_multiple_checkout' => ( $request->get_param( 'enable_multiple_checkout' ) !== null ) ? ( !empty( $request->get_param( 'enable_multiple_checkout' ) ) ? $request->get_param( 'enable_multiple_checkout' ) : $settings_data['enable_multiple_checkout'] ) : $settings_data['enable_multiple_checkout'],
			'enable_multiple_travellers' => ( $request->get_param( 'enable_multiple_travellers' ) !== null ) ? ( !empty( $request->get_param( 'enable_multiple_travellers' ) ) ? $request->get_param( 'enable_multiple_travellers' ) : $settings_data['enable_multiple_travellers'] ) : $settings_data['enable_multiple_travellers'],
		);


		$payment_setting = array(
			'partial_payment' => ( $request->get_param( 'partial_payment' ) !== null ) ? ( !empty( $request->get_param( 'partial_payment' ) ) ? $request->get_param( 'partial_payment' ) : $settings_data['partial_payment'] ) : $settings_data['partial_payment'],
			'minimum_partial_payout' => ( $request->get_param( 'minimum_partial_payout' ) !== null ) ? ( !empty( $request->get_param( 'minimum_partial_payout' ) ) ? $request->get_param( 'minimum_partial_payout' ) : $settings_data['minimum_partial_payout'] ) : $settings_data['minimum_partial_payout'],
			'trip_tax_enable' => ( $request->get_param( 'trip_tax_enable' ) !== null ) ? ( !empty( $request->get_param( 'trip_tax_enable' ) ) ? $request->get_param( 'trip_tax_enable' ) : $settings_data['trip_tax_enable'] ) : $settings_data['trip_tax_enable'],
			'trip_tax_price_inclusive' => ( $request->get_param( 'trip_tax_price_inclusive' ) !== null ) ? ( !empty( $request->get_param( 'trip_tax_price_inclusive' ) ) ? $request->get_param( 'trip_tax_price_inclusive' ) : $settings_data['trip_tax_price_inclusive'] ) : $settings_data['trip_tax_price_inclusive'],
			'trip_tax_percentage' => ( $request->get_param( 'trip_tax_percentage' ) !== null ) ? ( !empty( $request->get_param( 'trip_tax_percentage' ) ) ? $request->get_param( 'trip_tax_percentage' ) : $settings_data['trip_tax_percentage'] ) : $settings_data['trip_tax_percentage'],
			'payment_option_paypal' => ( $request->get_param( 'payment_option_paypal' ) !== null ) ? ( !empty( $request->get_param( 'payment_option_paypal' ) ) ? $request->get_param( 'payment_option_paypal' ) : $settings_data['payment_option_paypal'] ) : $settings_data['payment_option_paypal'],
			'paypal_email' => ( $request->get_param( 'paypal_email' ) !== null ) ? ( !empty( $request->get_param( 'paypal_email' ) ) ? $request->get_param( 'paypal_email' ) : $settings_data['paypal_email'] ) : $settings_data['paypal_email'],
			'payment_option_bank_deposit' => ( $request->get_param( 'payment_option_bank_deposit' ) !== null ) ? ( !empty( $request->get_param( 'payment_option_bank_deposit' ) ) ? $request->get_param( 'payment_option_bank_deposit' ) : $settings_data['payment_option_bank_deposit'] ) : $settings_data['payment_option_bank_deposit'],
			'wp_travel_bank_deposits' => ( $request->get_param( 'wp_travel_bank_deposits' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_bank_deposits' ) ) ? $request->get_param( 'wp_travel_bank_deposits' ) : $settings_data['wp_travel_bank_deposits'] ) : $settings_data['wp_travel_bank_deposits'],
			'wp_travel_bank_deposit_description' => ( $request->get_param( 'wp_travel_bank_deposit_description' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_bank_deposit_description' ) ) ? $request->get_param( 'wp_travel_bank_deposit_description' ) : $settings_data['wp_travel_bank_deposit_description'] ) : $settings_data['wp_travel_bank_deposit_description'],
			'wp_travel_bank_deposits' => ( $request->get_param( 'wp_travel_bank_deposits' ) !== null ) ? ( !empty( $request->get_param( 'wp_travel_bank_deposits' ) ) ? $request->get_param( 'wp_travel_bank_deposits' ) : $settings_data['wp_travel_bank_deposits'] ) : $settings_data['wp_travel_bank_deposits'],


		);

		// $trip_facts_setting = array(
		// 	'checkout_page_id' => !empty( $request->get_param( 'checkout_page_id' ) ) ? $request->get_param( 'checkout_page_id' ) : $settings_data['checkout_page_id'],
		// );
		WP_Travel::verify_nonce();
		$setting_data = array();
		$setting_data['number_of_decimals'] = 4;

		return WP_Travel_Helpers_Settings::update_settings( $setting_data );

	}

	public function wp_travel_get_settings( WP_REST_Request $request ){

		$settins_data = get_option( 'wp_travel_settings' )['payment_admin_template_settings']['email_content'];

		return $settins_data;
	}

}

new WP_Travel_API_Settings();

