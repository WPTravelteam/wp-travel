<?php
/**
 * Depricated Functions.
 *
 * @package WP_Travel
 */

/**
 * Deprecated Function.
 */
function WP_Travel() { // @phpcs:ignore
	wptravel_deprecated_function( 'WP_Travel', '4.4.7', 'WPTravel' );
	return WPTravel();
}

/**
 * Deprecated Function.
 */
function wp_travel_get_active_gateways() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_active_gateways', '4.4.7', 'wptravel_get_active_gateways' );
	return wptravel_get_active_gateways();
}

function wp_travel_get_total_amount() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_total_amount', '4.4.7', 'wptravel_get_total_amount' );
	return wptravel_get_total_amount();
}

function wp_travel_payment_booking_message( $message ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_booking_message', '4.4.7', 'wptravel_payment_booking_message' );
	return wptravel_payment_booking_message( $message );
}

function wp_travel_update_payment_status( $booking_id, $amount, $status, $args, $key = '_paypal_args', $payment_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_update_payment_status', '4.4.7', 'wptravel_update_payment_status' );
	return wptravel_update_payment_status( $booking_id, $amount, $status, $args, $key, $payment_id );
}

function wp_travel_send_email_payment( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_send_email_payment', '4.4.7', 'wptravel_send_email_payment' );
	return wptravel_send_email_payment( $booking_id );
}

function wp_travel_update_payment_status_booking_process_frontend( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_update_payment_status_booking_process_frontend', '4.4.7', 'wptravel_update_payment_status_booking_process_frontend' );
	return wptravel_update_payment_status_booking_process_frontend( $booking_id );
}

function wp_travel_update_payment_status_admin( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_update_payment_status_admin', '4.4.7', 'wptravel_update_payment_status_admin' );
	return wptravel_update_payment_status_admin( $booking_id );
}

function wp_travel_is_payment_enabled() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_payment_enabled', '4.4.7', 'wptravel_is_payment_enabled' );
	return wptravel_is_payment_enabled();
}

function wp_travel_enabled_payment_gateways() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enabled_payment_gateways', '4.4.7', 'wptravel_enabled_payment_gateways' );
	return wptravel_enabled_payment_gateways();
}

function wp_travel_test_mode() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_test_mode', '4.4.7', 'wptravel_test_mode' );
	return wptravel_test_mode();
}

function wp_travel_use_global_payout_percent( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_use_global_payout_percent', '4.4.7', 'wptravel_use_global_payout_percent' );
	return wptravel_use_global_payout_percent( $post_id );
}

function wp_travel_initial_partial_payout_unformated( $partial_payout, $force_format = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_initial_partial_payout_unformated', '4.4.7', 'wptravel_initial_partial_payout_unformated' );
	return wptravel_initial_partial_payout_unformated( $partial_payout, $force_format );
}

function wp_travel_get_actual_payout_percent( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_actual_payout_percent', '4.4.7', 'wptravel_get_actual_payout_percent' );
	return wptravel_get_actual_payout_percent( $post_id );
}

function wp_travel_get_payout_percent( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_payout_percent', '4.4.7', 'wptravel_get_payout_percent' );
	return wptravel_get_payout_percent( $post_id );
}

function wp_travel_variable_pricing_minimum_partial_payout( $post_id, $price, $tax_details ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_variable_pricing_minimum_partial_payout', '4.4.7', 'wptravel_variable_pricing_minimum_partial_payout' );
	return wptravel_variable_pricing_minimum_partial_payout( $post_id, $price, $tax_details );
}

function wp_travel_minimum_partial_payout( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_minimum_partial_payout', '4.4.7', 'wptravel_minimum_partial_payout' );
	return wptravel_minimum_partial_payout( $trip_id );
}

function wp_travel_sorted_payment_gateway_lists() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_sorted_payment_gateway_lists', '4.4.7', 'wptravel_sorted_payment_gateway_lists' );
	return wptravel_sorted_payment_gateway_lists();
}

function wp_travel_payment_gateway_lists() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_gateway_lists', '4.4.7', 'wptravel_payment_gateway_lists' );
	return wptravel_payment_gateway_lists();
}

function wp_travel_payment_field_list() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_field_list', '4.4.7', 'wptravel_payment_field_list' );
	return wptravel_payment_field_list();
}

function wp_travel_register_payments( $object ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_payments', '4.4.7', 'wptravel_register_payments' );
	return wptravel_register_payments( $object );
}

function wp_travel_is_partial_payment_enabled() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_partial_payment_enabled', '4.4.7', 'wptravel_is_partial_payment_enabled' );
	return wptravel_is_partial_payment_enabled();
}

function wp_travel_get_currency_symbol( $currency_code = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_currency_symbol', '4.4.7', 'wptravel_get_currency_symbol' );
	return wptravel_get_currency_symbol( $currency_code );
}

function wp_travel_currency_symbols() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_currency_symbols', '4.4.7', 'wptravel_currency_symbols' );
	return wptravel_currency_symbols();
}

function wp_travel_get_currency_list() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_currency_list', '4.4.7', 'wptravel_get_currency_list' );
	return wptravel_get_currency_list();
}

function wp_travel_get_country_by_code( $country_code ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_country_by_code', '4.4.7', 'wptravel_get_country_by_code' );
	return wptravel_get_country_by_code( $country_code );
}

function wp_travel_get_countries() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_countries', '4.4.7', 'wptravel_get_countries' );
	return wptravel_get_countries();
}

function wp_travel_get_booking_chart() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_booking_chart', '4.4.7', 'wptravel_get_booking_chart' );
	return wptravel_get_booking_chart();
}

function wp_travel_book_now() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_book_now', '4.4.7', 'wptravel_book_now' );
	return wptravel_book_now();
}

function wp_travel_post_duplicator_action_row_link( $post ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_post_duplicator_action_row_link', '4.4.7', 'wptravel_post_duplicator_action_row_link' );
	return wptravel_post_duplicator_action_row_link( $post );
}

function wp_travel_post_duplicator_action_row( $actions, $post ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_post_duplicator_action_row', '4.4.7', 'wptravel_post_duplicator_action_row' );
	return wptravel_post_duplicator_action_row( $actions, $post );
}

function wp_travel_get_bank_deposit_form_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_bank_deposit_form_fields', '4.4.7', 'wptravel_get_bank_deposit_form_fields' );
	return wptravel_get_bank_deposit_form_fields();
}

function wp_travel_search_filter_widget_form_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_search_filter_widget_form_fields', '4.4.7', 'wptravel_search_filter_widget_form_fields' );
	return wptravel_search_filter_widget_form_fields();
}

function wp_travel_get_checkout_form_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_checkout_form_fields', '4.4.7', 'wptravel_get_checkout_form_fields' );
	return wptravel_get_checkout_form_fields();
}

function wp_travel_enquiry_form_header() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiry_form_header', '4.4.7', 'wptravel_enquiry_form_header' );
	return wptravel_enquiry_form_header();
}

function wp_travel_save_user_enquiry() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_save_user_enquiry', '4.4.7', 'wptravel_save_user_enquiry' );
	return wptravel_save_user_enquiry();
}

function wp_travel_save_backend_enqueries_data( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_save_backend_enqueries_data', '4.4.7', 'wptravel_save_backend_enqueries_data' );
	return wptravel_save_backend_enqueries_data( $post_id );
}

function wp_travel_enquiries_content_manage_columns( $column_name, $id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiries_content_manage_columns', '4.4.7', 'wptravel_enquiries_content_manage_columns' );
	return wptravel_enquiries_content_manage_columns( $column_name, $id );
}

function wp_travel_enquiries_list_columns( $enquiries_column ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiries_list_columns', '4.4.7', 'wptravel_enquiries_list_columns' );
	return wptravel_enquiries_list_columns( $enquiries_column );
}

function wp_travel_enquiries_info() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiries_info', '4.4.7', 'wptravel_enquiries_info' );
	return wptravel_enquiries_info();
}

function wp_travel_add_enquiries_data_metaboxes() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_add_enquiries_data_metaboxes', '4.4.7', 'wptravel_add_enquiries_data_metaboxes' );
	return wptravel_add_enquiries_data_metaboxes();
}

function wp_travel_get_enquiries_form( $trips_dropdown = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_enquiries_form', '4.4.7', 'wptravel_get_enquiries_form' );
	return wptravel_get_enquiries_form( $trips_dropdown );
}

function wp_travel_enquiries_form_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiries_form_fields', '4.4.7', 'wptravel_enquiries_form_fields' );
	return wptravel_enquiries_form_fields();
}

function wp_travel_core_fontawesome_icons( $settings_options, $settings ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_core_fontawesome_icons', '4.4.7', 'wptravel_core_fontawesome_icons' );
	return wptravel_core_fontawesome_icons( $settings_options, $settings );
}

function wp_travel_enable_cart_page( $enabled, $settings ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enable_cart_page', '4.4.7', 'wptravel_enable_cart_page' );
	return wptravel_enable_cart_page( $enabled, $settings );
}

function wp_travel_can_load_bundled_scripts() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_can_load_bundled_scripts', '4.4.7', 'wptravel_can_load_bundled_scripts' );
	return wptravel_can_load_bundled_scripts();
}

function wp_travel_db_user_privileges() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_db_user_privileges', '4.4.7', 'wptravel_db_user_privileges' );
	return wptravel_db_user_privileges();
}

function wp_travel_get_trip_listing_option( $trip_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_listing_option', '4.4.7', 'wptravel_get_trip_listing_option' );
	return wptravel_get_trip_listing_option( $trip_id );
}

function wp_travel_trip_pricing_sort_by_date( $pricings, $sort = 'asc' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_pricing_sort_by_date', '4.4.7', 'wptravel_trip_pricing_sort_by_date' );
	return wptravel_trip_pricing_sort_by_date( $pricings, $sort );
}

function wp_travel_get_trip_pricing_option( $trip_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_pricing_option', '4.4.7', 'wptravel_get_trip_pricing_option' );
	return wptravel_get_trip_pricing_option( $trip_id );
}

function wp_travel_get_cart_pricing( $cart_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_cart_pricing', '4.4.7', 'wptravel_get_cart_pricing' );
	return wptravel_get_cart_pricing( $cart_id );
}

function wp_travel_get_pricing_by_pricing_id( $trip_id, $pricing_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_by_pricing_id', '4.4.7', 'wptravel_get_pricing_by_pricing_id' );
	return wptravel_get_pricing_by_pricing_id( $trip_id, $pricing_id );
}

function wp_travel_get_cart_item_price_with_extras( $cart_id, $trip_id, $partial = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_cart_item_price_with_extras', '4.4.7', 'wptravel_get_cart_item_price_with_extras' );
	return wptravel_get_cart_item_price_with_extras( $cart_id, $trip_id, $partial );
}

function wp_travel_frontend_tab_gallery( $gallery_ids ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_frontend_tab_gallery', '4.4.7', 'wptravel_frontend_tab_gallery' );
	return wptravel_frontend_tab_gallery( $gallery_ids );
}

function wp_travel_hide_price_per_field( $trip_id = '', $price_key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_hide_price_per_field', '4.4.7', 'wptravel_hide_price_per_field' );
	return wptravel_hide_price_per_field( $trip_id, $price_key );
}

function wp_travel_pricing_date_sort_desc( $a, $b ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_pricing_date_sort_desc', '4.4.7', 'wptravel_pricing_date_sort_desc' );
	return wptravel_pricing_date_sort_desc( $a, $b );
}

function wp_travel_pricing_date_sort_asc( $a, $b ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_pricing_date_sort_asc', '4.4.7', 'wptravel_pricing_date_sort_asc' );
	return wptravel_pricing_date_sort_asc( $a, $b );
}

function wp_travel_filter_expired_date( $dates ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_filter_expired_date', '4.4.7', 'wptravel_filter_expired_date' );
	return wptravel_filter_expired_date( $dates );
}

function wp_travel_date_sort_desc( $a, $b ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_date_sort_desc', '4.4.7', 'wptravel_date_sort_desc' );
	return wptravel_date_sort_desc( $a, $b );
}

function wp_travel_date_sort( $a, $b ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_date_sort', '4.4.7', 'wptravel_date_sort' );
	return wptravel_date_sort( $a, $b );
}

function wp_travel_get_fixed_departure_date( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_fixed_departure_date', '4.4.7', 'wptravel_get_fixed_departure_date' );
	return wptravel_get_fixed_departure_date( $trip_id );
}

function wp_travel_get_submenu() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_submenu', '4.4.7', 'wptravel_get_submenu' );
	return wptravel_get_submenu();
}

function wp_travel_get_bank_deposit_account_table( $show_description = '1' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_bank_deposit_account_table', '4.4.7', 'wptravel_get_bank_deposit_account_table' );
	return wptravel_get_bank_deposit_account_table( $show_description );
}

function wp_travel_get_bank_deposit_account_details( $display_all_row = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_bank_deposit_account_details', '4.4.7', 'wptravel_get_bank_deposit_account_details' );
	return wptravel_get_bank_deposit_account_details( $display_all_row );
}

function wp_travel_pax_alert_message( $min = '', $max = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_pax_alert_message', '4.4.7', 'wptravel_pax_alert_message' );
	return wptravel_pax_alert_message( $min, $max );
}

function wp_travel_get_strings() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_strings', '4.4.7', 'wptravel_get_strings' );
	return wptravel_get_strings();
}

function wp_travel_privacy_link() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_privacy_link', '4.4.7', 'wptravel_privacy_link' );
	return wptravel_privacy_link();
}

function wp_travel_trip_availability( $trip_id, $price_key, $start_date, $sold_out ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_availability', '4.4.7', 'wptravel_trip_availability' );
	return wptravel_trip_availability( $trip_id, $price_key, $start_date, $sold_out );
}

function wp_travel_thankyou_page_url( $trip_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_thankyou_page_url', '4.4.7', 'wptravel_thankyou_page_url' );
	return wptravel_thankyou_page_url( $trip_id );
}

function wp_travel_view_payment_details_table( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_view_payment_details_table', '4.4.7', 'wptravel_view_payment_details_table' );
	return wptravel_view_payment_details_table( $booking_id );
}

function wp_travel_view_booking_details_table( $booking_id, $hide_payment_column = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_view_booking_details_table', '4.4.7', 'wptravel_view_booking_details_table' );
	return wptravel_view_booking_details_table( $booking_id, $hide_payment_column );
}

function wp_travel_get_trip_archive_filter_by( $settings = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_archive_filter_by', '4.4.7', 'wptravel_get_trip_archive_filter_by' );
	return wptravel_get_trip_archive_filter_by( $settings );
}

function wp_travel_get_pricing_option_listing_type( $settings = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_option_listing_type', '4.4.7', 'wptravel_get_pricing_option_listing_type' );
	return wptravel_get_pricing_option_listing_type( $settings );
}

function wp_travel_get_search_filter_form( $args ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_search_filter_form', '4.4.7', 'wptravel_get_search_filter_form' );
	return wptravel_get_search_filter_form( $args );
}

function wp_travel_get_inquiry_link() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_inquiry_link', '4.4.7', 'wptravel_get_inquiry_link' );
	return wptravel_get_inquiry_link();
}

function wp_travel_sort_form_fields( $fields ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_sort_form_fields', '4.4.7', 'wptravel_sort_form_fields' );
	return wptravel_sort_form_fields( $fields );
}

function wp_travel_sort_array_by_priority( $array, $priority_key = 'priority' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_sort_array_by_priority', '4.4.7', 'wptravel_sort_array_by_priority' );
	return wptravel_sort_array_by_priority( $array, $priority_key );
}

function wp_travel_get_trip_pricing_name( $trip_id, $price_key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_pricing_name', '4.4.7', 'wptravel_get_trip_pricing_name' );
	return wptravel_get_trip_pricing_name( $trip_id, $price_key );
}

function wp_travel_get_trip_pricing_name_by_pricing_id( $trip_id, $pricing_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_pricing_name_by_pricing_id', '4.4.7', 'wptravel_get_trip_pricing_name_by_pricing_id' );
	return wptravel_get_trip_pricing_name_by_pricing_id( $trip_id, $pricing_id );
}

function wp_travel_booking_show_end_date() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_show_end_date', '4.4.7', 'wptravel_booking_show_end_date' );
	return wptravel_booking_show_end_date();
}

function wp_travel_payment_data( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_data', '4.4.7', 'wptravel_payment_data' );
	return wptravel_payment_data( $booking_id );
}

function wp_travel_is_ymd_date( $date ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_ymd_date', '4.4.7', 'wptravel_is_ymd_date' );
	return wptravel_is_ymd_date( $date );
}

function wp_travel_moment_date_format( $date_format = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_moment_date_format', '4.4.7', 'wptravel_moment_date_format' );
	return wptravel_moment_date_format( $date_format );
}

function wp_travel_date_format_php_to_js( $date_format = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_date_format_php_to_js', '4.4.7', 'wptravel_date_format_php_to_js' );
	return wptravel_date_format_php_to_js( $date_format );
}

function wp_travel_print_notices() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_print_notices', '4.4.7', 'wptravel_print_notices' );
	return wptravel_print_notices();
}

function wp_travel_get_date_diff( $start_date, $end_date ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_date_diff', '4.4.7', 'wptravel_get_date_diff' );
	return wptravel_get_date_diff( $start_date, $end_date );
}

function wp_travel_is_react_version_enabled() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_react_version_enabled', '4.4.7', 'wptravel_is_react_version_enabled' );
	return wptravel_is_react_version_enabled();
}

function wp_travel_emails_from_name_filter( $from_name ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_emails_from_name_filter', '4.4.7', 'wptravel_emails_from_name_filter' );
	return wptravel_emails_from_name_filter( $from_name );
}

function wp_travel_user_new_account_created( $customer_id, $new_customer_data, $password_generated ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_user_new_account_created', '4.4.7', 'wptravel_user_new_account_created' );
	return wptravel_user_new_account_created( $customer_id, $new_customer_data, $password_generated );
}

function wp_travel_get_notice_count( $notice_type = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_notice_count', '4.4.7', 'wptravel_get_notice_count' );
	return wptravel_get_notice_count( $notice_type );
}

function wp_travel_add_wp_error_notices( $errors ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_add_wp_error_notices', '4.4.7', 'wptravel_add_wp_error_notices' );
	return wptravel_add_wp_error_notices( $errors );
}

function wp_travel_clean_vars( $var ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_clean_vars', '4.4.7', 'wptravel_clean_vars' );
	return wptravel_clean_vars( $var );
}

function wp_travel_post_content_has_shortcode( $tag = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_post_content_has_shortcode', '4.4.7', 'wptravel_post_content_has_shortcode' );
	return wptravel_post_content_has_shortcode( $tag );
}

function wp_travel_get_pricing_variation_start_dates( $post_id, $pricing_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_variation_start_dates', '4.4.7', 'wptravel_get_pricing_variation_start_dates' );
	return wptravel_get_pricing_variation_start_dates( $post_id, $pricing_key );
}

function wp_travel_get_raw_referer() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_raw_referer', '4.4.7', 'wptravel_get_raw_referer' );
	return wptravel_get_raw_referer();
}

function wp_travel_get_pricing_variation( $post_id, $pricing_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_variation', '4.4.7', 'wptravel_get_pricing_variation' );
	return wptravel_get_pricing_variation( $post_id, $pricing_key );
}

function wp_travel_get_pricing_category_by_key( $key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_category_by_key', '4.4.7', 'wptravel_get_pricing_category_by_key' );
	return wptravel_get_pricing_category_by_key( $key );
}

function wp_travel_get_pricing_variation_options() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_variation_options', '4.4.7', 'wptravel_get_pricing_variation_options' );
	return wptravel_get_pricing_variation_options();
}

function wp_travel_can_load_payment_scripts() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_can_load_payment_scripts', '4.4.7', 'wptravel_can_load_payment_scripts' );
	return wptravel_can_load_payment_scripts();
}

function wp_travel_is_itinerary( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_itinerary', '4.4.7', 'wptravel_is_itinerary' );
	return wptravel_is_itinerary( $post_id );
}

function wp_travel_get_checkout_url() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_checkout_url', '4.4.7', 'wptravel_get_checkout_url' );
	return wptravel_get_checkout_url();
}

function wp_travel_get_cart_url() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_cart_url', '4.4.7', 'wptravel_get_cart_url' );
	return wptravel_get_cart_url();
}

function wp_travel_get_page_permalink( $page ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_page_permalink', '4.4.7', 'wptravel_get_page_permalink' );
	return wptravel_get_page_permalink( $page );
}

function wp_travel_get_page_id( $page ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_page_id', '4.4.7', 'wptravel_get_page_id' );
	return wptravel_get_page_id( $page );
}

function wp_travel_get_faqs( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_faqs', '4.4.7', 'wptravel_get_faqs' );
	return wptravel_get_faqs( $post_id );
}

function wp_travel_get_admin_trip_tabs( $post_id, $custom_tab_enabled = '', $frontend_hide_content = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_admin_trip_tabs', '4.4.7', 'wptravel_get_admin_trip_tabs' );
	return wptravel_get_admin_trip_tabs( $post_id, $custom_tab_enabled, $frontend_hide_content );
}

function wp_travel_get_global_tabs( $settings, $custom_tab_enabled = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_global_tabs', '4.4.7', 'wptravel_get_global_tabs' );
	return wptravel_get_global_tabs( $settings, $custom_tab_enabled );
}

/**
 * Default Tabs and its content.
 *
 * This will get all required tabs and its content for trip single page.
 *
 * @since 2.0.7
 * @deprecated 4.4.7 Use wptravel_get_default_trip_tabs() instead.
 */
function wp_travel_get_default_trip_tabs( $is_show_in_menu_query = '', $frontend_hide_content = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_default_trip_tabs', '4.4.7', 'wptravel_get_default_trip_tabs' );
	return wptravel_get_default_trip_tabs( $is_show_in_menu_query, $frontend_hide_content );
}

function wp_travel_get_frontend_tabs( $show_in_menu_query = '', $frontend_hide_content = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_frontend_tabs', '4.4.7', 'wptravel_get_frontend_tabs' );
	return wptravel_get_frontend_tabs( $show_in_menu_query, $frontend_hide_content );
}

function wp_travel_get_permalink_structure() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_permalink_structure', '4.4.7', 'wptravel_get_permalink_structure' );
	return wptravel_get_permalink_structure();
}

function wp_travel_get_image_sizes() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_image_sizes', '4.4.7', 'wptravel_get_image_sizes' );
	return wptravel_get_image_sizes();
}

function wp_travel_get_payment_mode() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_payment_mode', '4.4.7', 'wptravel_get_payment_mode' );
	return wptravel_get_payment_mode();
}

function wp_travel_get_payment_status() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_payment_status', '4.4.7', 'wptravel_get_payment_status' );
	return wptravel_get_payment_status();
}

function wp_travel_get_trip_duration( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_duration', '4.4.7', 'wptravel_get_trip_duration' );
	return wptravel_get_trip_duration( $post_id );
}

function wp_travel_search_form() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_search_form', '4.4.7', 'wptravel_search_form' );
	return wptravel_search_form();
}

function wp_travel_featured_itineraries( $no_of_post_to_show = '3' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_featured_itineraries', '4.4.7', 'wptravel_featured_itineraries' );
	return wptravel_featured_itineraries( $no_of_post_to_show );
}

function wp_travel_get_itinereries_prices_array() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_itinereries_prices_array', '4.4.7', 'wptravel_get_itinereries_prices_array' );
	return wptravel_get_itinereries_prices_array();
}

function wp_travel_get_tour_extras_array() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_tour_extras_array', '4.4.7', 'wptravel_get_tour_extras_array' );
	return wptravel_get_tour_extras_array();
}

function wp_travel_get_itineraries_array() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_itineraries_array', '4.4.7', 'wptravel_get_itineraries_array' );
	return wptravel_get_itineraries_array();
}

function wp_travel_allowed_html( $tags = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_allowed_html', '4.4.7', 'wptravel_allowed_html' );
	return wptravel_allowed_html( $tags );
}

function wp_travel_get_post_placeholder_image_url() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_post_placeholder_image_url', '4.4.7', 'wptravel_get_post_placeholder_image_url' );
	return wptravel_get_post_placeholder_image_url();
}

function wp_travel_get_post_thumbnail_url( $post_id, $size = 'wp_travel_thumbnail' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_post_thumbnail_url', '4.4.7', 'wptravel_get_post_thumbnail_url' );
	return wptravel_get_post_thumbnail_url( $post_id, $size );
}

function wp_travel_get_post_thumbnail( $post_id, $size = 'wp_travel_thumbnail' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_post_thumbnail', '4.4.7', 'wptravel_get_post_thumbnail' );
	return wptravel_get_post_thumbnail( $post_id, $size );
}

function wp_travel_get_related_post( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_related_post', '4.4.7', 'wptravel_get_related_post' );
	return wptravel_get_related_post( $post_id );
}

function wp_travel_get_map_data( $trip_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_map_data', '4.4.7', 'wptravel_get_map_data' );
	return wptravel_get_map_data( $trip_id );
}

function wp_travel_get_maps() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_maps', '4.4.7', 'wptravel_get_maps' );
	return wptravel_get_maps();
}

function wp_travel_sanitize_array( $array ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_sanitize_array', '4.4.7', 'wptravel_sanitize_array' );
	return wptravel_sanitize_array( $array );
}

function wp_travel_get_dropdown_list( $args = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_dropdown_list', '4.4.7', 'wptravel_get_dropdown_list' );
	return wptravel_get_dropdown_list( $args );
}

function wp_travel_get_dropdown_currency_list( $args = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_dropdown_currency_list', '4.4.7', 'wptravel_get_dropdown_currency_list' );
	return wptravel_get_dropdown_currency_list( $args );
}

function wp_travel_get_trip_code( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_code', '4.4.7', 'wptravel_get_trip_code' );
	return wptravel_get_trip_code( $post_id );
}

function wp_travel_get_settings() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_settings', '4.4.7', 'wptravel_get_settings' );
	return wptravel_get_settings();
}

function wp_travel_settings_default_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_settings_default_fields', '4.4.7', 'wptravel_settings_default_fields' );
	return wptravel_settings_default_fields();
}

function wp_travel_get_gallery_ids( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_gallery_ids', '4.4.7', 'wptravel_get_gallery_ids' );
	return wptravel_get_gallery_ids( $post_id );
}

function wp_travel_get_min_pricing_id( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_min_pricing_id', '4.4.7', 'wptravel_get_min_pricing_id' );
	return wptravel_get_min_pricing_id( $trip_id );
}

function wp_travel_get_payment_modes() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_payment_modes', '4.4.7', 'wptravel_get_payment_modes' );
	return wptravel_get_payment_modes();
}

function wp_travel_get_pricing_option_type( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_option_type', '4.4.7', 'wptravel_get_pricing_option_type' );
	return wptravel_get_pricing_option_type( $post_id );
}

function wp_travel_get_formated_price_currency( $price, $regular_price = '', $price_key = '', $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_formated_price_currency', '4.4.7', 'wptravel_get_formated_price_currency' );
	return wptravel_get_formated_price_currency( $price, $regular_price, $price_key, $post_id );
}

function wp_travel_convert_price( $price, $convert = '1' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_convert_price', '4.4.7', 'wptravel_convert_price' );
	return wptravel_convert_price( $price, $convert );
}

function wp_travel_get_formated_price( $price, $format = '1', $number_of_decimals = '2' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_formated_price', '4.4.7', 'wptravel_get_formated_price' );
	return wptravel_get_formated_price( $price, $format, $number_of_decimals );
}

function wp_travel_is_enable_pricing_options( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_enable_pricing_options', '4.4.7', 'wptravel_is_enable_pricing_options' );
	return wptravel_is_enable_pricing_options( $trip_id );
}

function wp_travel_is_price_key_valid( $trip_id, $price_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_price_key_valid', '4.4.7', 'wptravel_is_price_key_valid' );
	return wptravel_is_price_key_valid( $trip_id, $price_key );
}

function wp_travel_get_partial_trip_price( $trip_id, $price_key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_partial_trip_price', '4.4.7', 'wptravel_get_partial_trip_price' );
	return wptravel_get_partial_trip_price( $trip_id, $price_key );
}

function wp_travel_get_cart_attrs( $trip_id, $pax = '0', $price_key = '', $pricing_id = '', $trip_start_date = '', $return_price = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_cart_attrs', '4.4.7', 'wptravel_get_cart_attrs' );
	return wptravel_get_cart_attrs( $trip_id, $pax, $price_key, $pricing_id, $trip_start_date, $return_price );
}

function wp_travel_get_payment_id( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_payment_id', '4.4.7', 'wptravel_get_payment_id' );
	return wptravel_get_payment_id( $booking_id );
}

function wp_travel_booking_data( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_data', '4.4.7', 'wptravel_booking_data' );
	return wptravel_booking_data( $booking_id );
}

function wp_travel_get_pricing_variation_price_per( $post_id, $pricing_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_variation_price_per', '4.4.7', 'wptravel_get_pricing_variation_price_per' );
	return wptravel_get_pricing_variation_price_per( $post_id, $pricing_key );
}

function wp_travel_get_pricing_variation_dates( $post_id, $pricing_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_pricing_variation_dates', '4.4.7', 'wptravel_get_pricing_variation_dates' );
	return wptravel_get_pricing_variation_dates( $post_id, $pricing_key );
}

function wp_travel_taxed_amount( $amount ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_taxed_amount', '4.4.7', 'wptravel_taxed_amount' );
	return wptravel_taxed_amount( $amount );
}

function wp_travel_process_trip_price_tax_by_price( $post_id, $price ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_process_trip_price_tax_by_price', '4.4.7', 'wptravel_process_trip_price_tax_by_price' );
	return wptravel_process_trip_price_tax_by_price( $post_id, $price );
}

function wp_travel_process_trip_price_tax( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_process_trip_price_tax', '4.4.7', 'wptravel_process_trip_price_tax' );
	return wptravel_process_trip_price_tax( $trip_id );
}

function wp_travel_get_price_per_text( $trip_id, $price_key = '', $return_key = '', $category_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_price_per_text', '4.4.7', 'wptravel_get_price_per_text' );
	return wptravel_get_price_per_text( $trip_id, $price_key, $return_key, $category_id );
}

function wp_travel_get_price_per_by_key( $key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_price_per_by_key', '4.4.7', 'wptravel_get_price_per_by_key' );
	return wptravel_get_price_per_by_key( $key );
}

function wp_travel_get_price_per_fields() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_price_per_fields', '4.4.7', 'wptravel_get_price_per_fields' );
	return wptravel_get_price_per_fields();
}

function wp_travel_make_stat_data( $stat_datas, $show_empty = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_make_stat_data', '4.4.7', 'wptravel_make_stat_data' );
	return wptravel_make_stat_data( $stat_datas, $show_empty );
}

function wp_travel_get_booking_status() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_booking_status', '4.4.7', 'wptravel_get_booking_status' );
	return wptravel_get_booking_status();
}

function wp_travel_get_booking_data() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_booking_data', '4.4.7', 'wptravel_get_booking_data' );
	return wptravel_get_booking_data();
}

function wp_travel_fa_icons() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_fa_icons', '4.4.7', 'wptravel_fa_icons' );
	return wptravel_fa_icons();
}

function wp_travel_format_date( $date, $localize = '1', $base_date_format = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_format_date', '4.4.7', 'wptravel_format_date' );
	return wptravel_format_date( $date, $localize, $base_date_format );
}

function wp_travel_format_ymd_date( $date, $date_format = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_format_ymd_date', '4.4.7', 'wptravel_format_ymd_date' );
	return wptravel_format_ymd_date( $date, $date_format );
}

function wp_travel_get_trip_available_dates( $trip_id, $price_key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_available_dates', '4.4.7', 'wptravel_get_trip_available_dates' );
	return wptravel_get_trip_available_dates( $trip_id, $price_key );
}

function wp_travel_get_multiple_pricing_available_dates( $trip_id, $price_key = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_multiple_pricing_available_dates', '4.4.7', 'wptravel_get_multiple_pricing_available_dates' );
	return wptravel_get_multiple_pricing_available_dates( $trip_id, $price_key );
}

function wp_travel_get_total_booked_pax( $trip_id, $including_cart = '1' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_total_booked_pax', '4.4.7', 'wptravel_get_total_booked_pax' );
	return wptravel_get_total_booked_pax( $trip_id, $including_cart );
}

function wp_travel_get_trip_pricings( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_pricings', '4.4.7', 'wptravel_get_trip_pricings' );
	return wptravel_get_trip_pricings( $trip_id );
}

function wp_travel_get_trip_pricings_with_dates( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_trip_pricings_with_dates', '4.4.7', 'wptravel_get_trip_pricings_with_dates' );
	return wptravel_get_trip_pricings_with_dates( $trip_id );
}

function wp_travel_comments( $comment, $args, $depth ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_comments', '4.4.7', 'wptravel_comments' );
	return wptravel_comments( $comment, $args, $depth );
}

function wp_travel_deprecated_hook( $hook, $version, $replacement = '', $message = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_deprecated_hook', '4.4.7', 'wptravel_deprecated_hook' );
	return wptravel_deprecated_hook( $hook, $version, $replacement, $message );
}

function wp_travel_do_deprecated_action( $tag, $args, $version, $replacement = '', $message = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_do_deprecated_action', '4.4.7', 'wptravel_do_deprecated_action' );
	return wptravel_do_deprecated_action( $tag, $args, $version, $replacement, $message );
}

function wp_travel_deprecated_function( $function, $version, $replacement = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_deprecated_function', '4.4.7', 'wptravel_deprecated_function' );
	return wptravel_deprecated_function( $function, $version, $replacement );
}

function wp_travel_raw_output_on_tab_content( $raw, $tab_key ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_raw_output_on_tab_content', '4.4.7', 'wptravel_raw_output_on_tab_content' );
	return wptravel_raw_output_on_tab_content( $raw, $tab_key );
}

function wp_travel_get_header_image_tag( $html ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_header_image_tag', '4.4.7', 'wptravel_get_header_image_tag' );
	return wptravel_get_header_image_tag( $html );
}

function wp_travel_remove_jetpack_related_posts( $options ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_remove_jetpack_related_posts', '4.4.7', 'wptravel_remove_jetpack_related_posts' );
	return wptravel_remove_jetpack_related_posts( $options );
}

function wp_travel_booking_fixed_departure_list_content( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_fixed_departure_list_content', '4.4.7', 'wptravel_booking_fixed_departure_list_content' );
	return wptravel_booking_fixed_departure_list_content( $trip_id );
}

function wp_travel_booking_default_princing_list_content( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_default_princing_list_content', '4.4.7', 'wptravel_booking_default_princing_list_content' );
	return wptravel_booking_default_princing_list_content( $trip_id );
}

function wp_travel_prevent_endpoint_indexing() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_prevent_endpoint_indexing', '4.4.7', 'wptravel_prevent_endpoint_indexing' );
	return wptravel_prevent_endpoint_indexing();
}

function wp_travel_wpkses_post_iframe( $tags, $context ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_wpkses_post_iframe', '4.4.7', 'wptravel_wpkses_post_iframe' );
	return wptravel_wpkses_post_iframe( $tags, $context );
}

function wp_travel_excerpt_more( $more ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_excerpt_more', '4.4.7', 'wptravel_excerpt_more' );
	return wptravel_excerpt_more( $more );
}

function wp_travel_clear_booking_transient( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_clear_booking_transient', '4.4.7', 'wptravel_clear_booking_transient' );
	return wptravel_clear_booking_transient( $post_id );
}

function wp_travel_get_archive_view_mode() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_archive_view_mode', '4.4.7', 'wptravel_get_archive_view_mode' );
	return wptravel_get_archive_view_mode();
}

function wp_travel_tab_show_in_menu( $tab_name ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_tab_show_in_menu', '4.4.7', 'wptravel_tab_show_in_menu' );
	return wptravel_tab_show_in_menu( $tab_name );
}

function wp_travel_posts_filter( $query ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_posts_filter', '4.4.7', 'wptravel_posts_filter' );
	return wptravel_posts_filter( $query );
}

function wp_travel_archive_listing_sidebar() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_archive_listing_sidebar', '4.4.7', 'wptravel_archive_listing_sidebar' );
	return wptravel_archive_listing_sidebar();
}

function wp_travel_archive_wrapper_close() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_archive_wrapper_close', '4.4.7', 'wptravel_archive_wrapper_close' );
	return wptravel_archive_wrapper_close();
}

function wp_travel_archive_toolbar() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_archive_toolbar', '4.4.7', 'wptravel_archive_toolbar' );
	return wptravel_archive_toolbar();
}

function wp_travel_archive_filter_by() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_archive_filter_by', '4.4.7', 'wptravel_archive_filter_by' );
	return wptravel_archive_filter_by();
}

function wp_travel_setup_itinerary_data( $post ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_setup_itinerary_data', '4.4.7', 'wptravel_setup_itinerary_data' );
	return wptravel_setup_itinerary_data( $post );
}

function wp_travel_get_group_size( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_group_size', '4.4.7', 'wptravel_get_group_size' );
	return wptravel_get_group_size( $post_id );
}

function wp_travel_booking_message() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_message', '4.4.7', 'wptravel_booking_message' );
	return wptravel_booking_message();
}

function wp_travel_body_class( $classes, $class ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_body_class', '4.4.7', 'wptravel_body_class' );
	return wptravel_body_class( $classes, $class );
}

function wp_travel_save_offer( $trip_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_save_offer', '4.4.7', 'wptravel_save_offer' );
	return wptravel_save_offer( $trip_id );
}

function wp_travel_pagination( $range = '2', $pages = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_pagination', '4.4.7', 'wptravel_pagination' );
	return wptravel_pagination( $range, $pages );
}

function wp_travel_excerpt_length( $length ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_excerpt_length', '4.4.7', 'wptravel_excerpt_length' );
	return wptravel_excerpt_length( $length );
}

function wp_travel_template_loader( $template ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_template_loader', '4.4.7', 'wptravel_template_loader' );
	return wptravel_template_loader( $template );
}

function wp_travel_comments_template_loader( $template ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_comments_template_loader', '4.4.7', 'wptravel_comments_template_loader' );
	return wptravel_comments_template_loader( $template );
}

function wp_travel_get_rating_count( $value = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_rating_count', '4.4.7', 'wptravel_get_rating_count' );
	return wptravel_get_rating_count( $value );
}

function wp_travel_get_average_rating( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_average_rating', '4.4.7', 'wptravel_get_average_rating' );
	return wptravel_get_average_rating( $post_id );
}

function wp_travel_get_review_count( $post_id = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_review_count', '4.4.7', 'wptravel_get_review_count' );
	return wptravel_get_review_count( $post_id );
}

function wp_travel_verify_comment_meta_data( $commentdata ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_verify_comment_meta_data', '4.4.7', 'wptravel_verify_comment_meta_data' );
	return wptravel_verify_comment_meta_data( $commentdata );
}

function wp_travel_clear_transients( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_clear_transients', '4.4.7', 'wptravel_clear_transients' );
	return wptravel_clear_transients( $post_id );
}

function wp_travel_add_comment_rating( $comment_id, $approve, $comment_data ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_add_comment_rating', '4.4.7', 'wptravel_add_comment_rating' );
	return wptravel_add_comment_rating( $comment_id, $approve, $comment_data );
}

function wp_travel_related_itineraries( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_related_itineraries', '4.4.7', 'wptravel_related_itineraries' );
	return wptravel_related_itineraries( $post_id );
}

function wp_travel_trip_map( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_map', '4.4.7', 'wptravel_trip_map' );
	return wptravel_trip_map( $post_id );
}

function wp_travel_frontend_contents( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_frontend_contents', '4.4.7', 'wptravel_frontend_contents' );
	return wptravel_frontend_contents( $post_id );
}

function wp_travel_frontend_trip_facts( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_frontend_trip_facts', '4.4.7', 'wptravel_frontend_trip_facts' );
	return wptravel_frontend_trip_facts( $post_id );
}

function wp_travel_single_location( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_single_location', '4.4.7', 'wptravel_single_location' );
	return wptravel_single_location( $post_id );
}

function wp_travel_single_keywords( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_single_keywords', '4.4.7', 'wptravel_single_keywords' );
	return wptravel_single_keywords( $post_id );
}

function wp_travel_single_excerpt( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_single_excerpt', '4.4.7', 'wptravel_single_excerpt' );
	return wptravel_single_excerpt( $post_id );
}

function wp_travel_trip_rating( $post_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_rating', '4.4.7', 'wptravel_trip_rating' );
	return wptravel_trip_rating( $post_id );
}

function wp_travel_single_trip_rating( $post_id, $hide_rating = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_single_trip_rating', '4.4.7', 'wptravel_single_trip_rating' );
	return wptravel_single_trip_rating( $post_id, $hide_rating );
}

function wp_travel_trip_price( $trip_id, $hide_rating = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_price', '4.4.7', 'wptravel_trip_price' );
	return wptravel_trip_price( $trip_id, $hide_rating );
}

function wp_travel_wrapper_end() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_wrapper_end', '4.4.7', 'wptravel_wrapper_end' );
	return wptravel_wrapper_end();
}

function wp_travel_get_theme_wrapper_class() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_theme_wrapper_class', '4.4.7', 'wptravel_get_theme_wrapper_class' );
	return wptravel_get_theme_wrapper_class();
}

function wp_travel_wrapper_start() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_wrapper_start', '4.4.7', 'wptravel_wrapper_start' );
	return wptravel_wrapper_start();
}

function wp_travel_content_filter( $content ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_content_filter', '4.4.7', 'wptravel_content_filter' );
	return wptravel_content_filter( $content );
}

function wp_travel_load_template( $path, $args = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_load_template', '4.4.7', 'wptravel_load_template' );
	return wptravel_load_template( $path, $args );
}

function wp_travel_get_template_part( $slug, $name = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_template_part', '4.4.7', 'wptravel_get_template_part' );
	return wptravel_get_template_part( $slug, $name );
}

function wp_travel_get_template_html( $template_name, $args = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_template_html', '4.4.7', 'wptravel_get_template_html' );
	return wptravel_get_template_html( $template_name, $args );
}

function wp_travel_get_template( $template_name, $args = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_template', '4.4.7', 'wptravel_get_template' );
	return wptravel_get_template( $template_name, $args );
}

function wp_travel_posts_clauses_filter( $post_clauses, $object ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_posts_clauses_filter', '4.4.7', 'wptravel_posts_clauses_filter' );
	return wptravel_posts_clauses_filter( $post_clauses, $object );
}

function wp_travel_coupon_pro() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_coupon_pro', '4.4.7', 'wptravel_coupon_pro' );
	return wptravel_coupon_pro();
}

function wp_travel_paypal_ipn_process() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_paypal_ipn_process', '4.4.7', 'wptravel_paypal_ipn_process' );
	return wptravel_paypal_ipn_process();
}

function wp_travel_listen_paypal_ipn() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_listen_paypal_ipn', '4.4.7', 'wptravel_listen_paypal_ipn' );
	return wptravel_listen_paypal_ipn();
}

function wp_travel_get_paypal_redirect_url( $ssl_check = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_paypal_redirect_url', '4.4.7', 'wptravel_get_paypal_redirect_url' );
	return wptravel_get_paypal_redirect_url( $ssl_check );
}

function wp_travel_bank_deposite_content( $booking_id = '', $details = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_bank_deposite_content', '4.4.7', 'wptravel_bank_deposite_content' );
	return wptravel_bank_deposite_content( $booking_id, $details );
}

function wp_travel_bank_deposite_button( $booking_id = '', $details = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_bank_deposite_button', '4.4.7', 'wptravel_bank_deposite_button' );
	return wptravel_bank_deposite_button( $booking_id, $details );
}

function wp_travel_submit_bank_deposit_slip() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_submit_bank_deposit_slip', '4.4.7', 'wptravel_submit_bank_deposit_slip' );
	return wptravel_submit_bank_deposit_slip();
}

function wp_travel_booking_bank_deposit( $booking_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_booking_bank_deposit', '4.4.7', 'wptravel_booking_bank_deposit' );
	return wptravel_booking_bank_deposit( $booking_id );
}

function wp_travel_settings_bank_deposit( $args ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_settings_bank_deposit', '4.4.7', 'wptravel_settings_bank_deposit' );
	return wptravel_settings_bank_deposit( $args );
}

function wp_travel_bank_deposit_default_settings_fields( $settings ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_bank_deposit_default_settings_fields', '4.4.7', 'wptravel_bank_deposit_default_settings_fields' );
	return wptravel_bank_deposit_default_settings_fields( $settings );
}

function wp_travel_payment_email_template_customer() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_email_template_customer', '4.4.7', 'wptravel_payment_email_template_customer' );
	return wptravel_payment_email_template_customer();
}

function wp_travel_payment_email_template_admin() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_payment_email_template_admin', '4.4.7', 'wptravel_payment_email_template_admin' );
	return wptravel_payment_email_template_admin();
}

function wp_travel_enqueries_admin_email_template() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enqueries_admin_email_template', '4.4.7', 'wptravel_enqueries_admin_email_template' );
	return wptravel_enqueries_admin_email_template();
}

function wp_travel_customer_email_template() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_customer_email_template', '4.4.7', 'wptravel_customer_email_template' );
	return wptravel_customer_email_template();
}

function wp_travel_admin_email_template() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_admin_email_template', '4.4.7', 'wptravel_admin_email_template' );
	return wptravel_admin_email_template();
}

function wp_travel_payment_client_default_email_content() { // @phpcs:ignore
	wptravel_deprecated_function( 'wptravel_payment_client_default_email_content', '4.4.7', 'wptravel_payment_client_default_email_content' );
	return wptravel_payment_client_default_email_content();
}

function wp_travel_payment_admin_default_email_content() { // @phpcs:ignore
	wptravel_deprecated_function( 'wptravel_payment_admin_default_email_content', '4.4.7', 'wptravel_payment_admin_default_email_content' );
	return wptravel_payment_admin_default_email_content();
}

function wp_travel_enquiries_admin_default_email_content() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_enquiries_admin_default_email_content', '4.4.7', 'wptravel_enquiries_admin_default_email_content' );
	return wptravel_enquiries_admin_default_email_content();
}

function wp_travel_booking_client_default_email_content() { // @phpcs:ignore
	wptravel_deprecated_function( 'wptravel_booking_client_default_email_content', '4.4.7', 'wptravel_booking_client_default_email_content' );
	return wptravel_booking_client_default_email_content();
}

function wp_travel_booking_admin_default_email_content() { // @phpcs:ignore
	wptravel_deprecated_function( 'wptravel_booking_admin_default_email_content', '4.4.7', 'wptravel_booking_admin_default_email_content' );
	return wptravel_booking_admin_default_email_content();
}

function wp_travel_insert_post_tags() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_insert_post_tags', '4.4.7', 'wptravel_insert_post_tags' );
	return wptravel_insert_post_tags();
}

function wp_travel_insert_common_tags() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_insert_common_tags', '4.4.7', 'wptravel_insert_common_tags' );
	return wptravel_insert_common_tags();
}

function wp_travel_insert_og_tags() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_insert_og_tags', '4.4.7', 'wptravel_insert_og_tags' );
	return wptravel_insert_og_tags();
}

function wp_travel_register_search_widgets() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_search_widgets', '4.4.7', 'wptravel_register_search_widgets' );
	return wptravel_register_search_widgets();
}

function wp_travel_register_featured_widgets() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_featured_widgets', '4.4.7', 'wptravel_register_featured_widgets' );
	return wptravel_register_featured_widgets();
}

function wp_travel_register_location_widgets() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_location_widgets', '4.4.7', 'wptravel_register_location_widgets' );
	return wptravel_register_location_widgets();
}

function wp_travel_register_trip_type_widgets() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_trip_type_widgets', '4.4.7', 'wptravel_register_trip_type_widgets' );
	return wptravel_register_trip_type_widgets();
}

function wp_travel_register_sales_widget() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_register_sales_widget', '4.4.7', 'wptravel_register_sales_widget' );
	return wptravel_register_sales_widget();
}

function wp_travel_lostpassword_url() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_lostpassword_url', '4.4.7', 'wptravel_lostpassword_url' );
	return wptravel_lostpassword_url();
}

function wp_travel_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_get_endpoint_url', '4.4.7', 'wptravel_get_endpoint_url' );
	return wptravel_get_endpoint_url( $endpoint, $value, $permalink );
}

function wp_travel_set_customer_auth_cookie( $customer_id ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_set_customer_auth_cookie', '4.4.7', 'wptravel_set_customer_auth_cookie' );
	return wptravel_set_customer_auth_cookie( $customer_id );
}

function wp_travel_disable_admin_bar( $show_admin_bar ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_disable_admin_bar', '4.4.7', 'wptravel_disable_admin_bar' );
	return wptravel_disable_admin_bar( $show_admin_bar );
}

function wp_travel_create_new_customer( $email, $username = '', $password = '' ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_create_new_customer', '4.4.7', 'wptravel_create_new_customer' );
	return wptravel_create_new_customer( $email, $username, $password );
}

function Wp_Travel_Extras_Frontend() { // @phpcs:ignore
	wptravel_deprecated_function( 'Wp_Travel_Extras_Frontend', '4.4.7', 'wptravel_extras_frontend' );
	return wptravel_extras_frontend();
}

function wp_travel_helpers_license_init() { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_helpers_license_init', '4.4.7', 'wptravel_helpers_license_init' );
	return wptravel_helpers_license_init();
}

function wp_travel_is_admin_page( $pages = array() ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_is_admin_page', '4.4.7', 'wptravel_is_admin_page' );
	return wptravel_is_admin_page( $pages );
}

function wp_travel_trip_callback_cart_checkout( $tabs, $args ) { // @phpcs:ignore
	wptravel_deprecated_function( 'wp_travel_trip_callback_cart_checkout', '4.4.7', 'wptravel_trip_callback_cart_checkout' );
	return wptravel_trip_callback_cart_checkout( $tabs, $args );
}
