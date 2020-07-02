<?php
class WP_Travel_Helpers_Settings {
	private static $date_table           = 'wt_dates';
	private static $pricing_table        = 'wt_pricings';
	private static $price_category_table = 'wt_price_category_relation';

	public static function get_settings() {

		$settings = wp_travel_get_settings();

		$settings_options = array(); // Additional option values.

		// currency option.
		$currency_options        = wp_travel_get_currency_list();
		$mapped_currency_options = array();
		$i                       = 0;
		foreach ( $currency_options as $value => $label ) {
			// $mapped_currency_options[ $i ]['label'] = $label;
			$mapped_currency_options[ $i ]['label'] = $label . ' (' . html_entity_decode( wp_travel_get_currency_symbol( $value ) ) . ')';
			$mapped_currency_options[ $i ]['value'] = $value;
			$i++;
		}
		$settings_options['currencies'] = $mapped_currency_options;

		// currency position option.
		$currency_positions                     = array(
			array(
				'label' => __( 'Left', 'wp-travel' ),
				'value' => 'left',
			),
			array(
				'label' => __( 'Right', 'wp-travel' ),
				'value' => 'right',
			),
			array(
				'label' => __( 'Left with space', 'wp-travel' ),
				'value' => 'left_with_space',
			),
			array(
				'label' => __( 'Right with space', 'wp-travel' ),
				'value' => 'right_with_space',
			),
		);
		$settings_options['currency_positions'] = $currency_positions;

		// map Options
		$map_data = wp_travel_get_maps();
		$maps     = $map_data['maps'];
		$i        = 0;
		$mapped_map_options = array();
		foreach ( $maps as $value => $label ) {
			$mapped_map_options[ $i ]['label'] = $label;
			$mapped_map_options[ $i ]['value'] = $value;
			$i++;
		}
		$settings_options['maps'] = $mapped_map_options;
		$settings['wp_travel_map'] = $map_data['selected']; // override fallback map if addons map is selected in option and deactivate addon map.


		// Page Lists.
		$lists = get_posts( array( 'numberposts' => -1, 'post_type' => 'page', 'orderby' => 'title', 'order' => 'asc' ) );
		$page_list = array();
		$i        = 0;
		foreach ( $lists as $page_data ) {
			$page_list[ $i ]['label'] = $page_data->post_title;
			$page_list[ $i ]['value'] = $page_data->ID;
			$i++;
		}
		$settings_options['page_list'] = $page_list;

		$settings_options = apply_filters( 'wp_travel_settings_options', $settings_options );
		// Asign Additional option values.
		$settings['options'] = $settings_options;

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_SETTINGS',
			array(
				'settings' => $settings,
			)
		);
	}

	public static function update_settings( $settings_data ) {

		$settings_data = (array) $settings_data;

		$settings        = wp_travel_get_settings();
		$settings_fields = array_keys( wp_travel_settings_default_fields() );

		foreach ( $settings_fields as $settings_field ) {
			if ( 'wp_travel_trip_facts_settings' === $settings_field ) {
				continue;
			}
			// error_log( print_r( $settings_data[ $settings_field ], true ) );
			if ( isset( $settings_data[ $settings_field ] ) ) {
				// Default pages settings. [only to get page in - wp_travel_get_page_id()] // Need enhanchement.
				$page_ids = array( 'cart_page_id', 'checkout_page_id', 'dashboard_page_id', 'thank_you_page_id' );

				if ( in_array( $settings_field, $page_ids ) && ! empty( $settings_data[ $settings_field ] ) ) {
					$page_id = $settings_data[ $settings_field ];
					/**
					 * @since 3.1.8 WPML configuration.
					 */
					if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
						update_option( 'wp_travel_' . $settings_field . '_' . ICL_LANGUAGE_CODE, $page_id );
						continue;
					} else {
						update_option( 'wp_travel_' . $settings_field, $page_id );
					}
				}

				$settings[ $settings_field ] = wp_unslash( $settings_data[ $settings_field ] );
			}
		}

		// Email Templates
		// Booking Admin Email Settings.
		if ( isset( $settings_data['booking_admin_template_settings'] ) && '' !== $settings_data['booking_admin_template_settings'] ) {
			$settings['booking_admin_template_settings'] = stripslashes_deep( $settings_data['booking_admin_template_settings'] );
		}

		// Booking Client Email Settings.
		if ( isset( $settings_data['booking_client_template_settings'] ) && '' !== $settings_data['booking_client_template_settings'] ) {
			$settings['booking_client_template_settings'] = stripslashes_deep( $settings_data['booking_client_template_settings'] );
		}

		// Payment Admin Email Settings.
		if ( isset( $settings_data['payment_admin_template_settings'] ) && '' !== $settings_data['payment_admin_template_settings'] ) {
			$settings['payment_admin_template_settings'] = stripslashes_deep( $settings_data['payment_admin_template_settings'] );
		}

		// Payment Client Email Settings.
		if ( isset( $settings_data['payment_client_template_settings'] ) && '' !== $settings_data['payment_client_template_settings'] ) {
			$settings['payment_client_template_settings'] = stripslashes_deep( $settings_data['payment_client_template_settings'] );
		}

		// Enquiry Admin Email Settings.
		if ( isset( $settings_data['enquiry_admin_template_settings'] ) && '' !== $settings_data['enquiry_admin_template_settings'] ) {
			$settings['enquiry_admin_template_settings'] = stripslashes_deep( $settings_data['enquiry_admin_template_settings'] );
		}

		// Trip Fact.
		$indexed = $settings_data['wp_travel_trip_facts_settings'];
		if ( array_key_exists( '$index', $indexed ) ) {
			unset( $indexed['$index'] );
		}
		foreach ( $indexed as $key => $index ) {
			if ( ! empty( $index['name'] ) ) {
				$index['id']      = $key;
				$index['initial'] = ! empty( $index['initial'] ) ? $index['initial'] : $index['name'];
				if ( is_array( $index['options'] ) ) {
					$options = array();
					$i       = 1;
					foreach ( $index['options'] as $option ) {
						$options[ 'option' . $i ] = $option;
						$i++;
					}
					$index['options'] = $options;
				}
				$indexed[ $key ] = $index;
				continue;
			}
			unset( $indexed[ $key ] );
		}
		$settings['wp_travel_trip_facts_settings'] = $indexed;

		if ( ! isset( $settings_data['wp_travel_bank_deposits'] ) ) {
			$settings['wp_travel_bank_deposits'] = array();
		}

		// @since 1.0.5 Used this filter below.
		$settings = apply_filters( 'wp_travel_before_save_settings', $settings );

		update_option( 'wp_travel_settings', $settings );
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_SETTINGS',
			array(
				'settings' => $settings,
			)
		);
	}
}
