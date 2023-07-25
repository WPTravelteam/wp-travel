<?php
class WP_Travel_Helpers_Settings {
	private static $date_table           = 'wt_dates';
	private static $pricing_table        = 'wt_pricings';
	private static $price_category_table = 'wt_price_category_relation';

	public static function get_settings() {

		$settings = wptravel_get_settings();

		$settings_options = array(); // Additional option values.

		// currency option.
		$currency_options        = wptravel_get_currency_list();
		$mapped_currency_options = array();
		$i                       = 0;
		foreach ( $currency_options as $value => $label ) {
			// $mapped_currency_options[ $i ]['label'] = $label;
			$mapped_currency_options[ $i ]['label'] = $label . ' (' . html_entity_decode( wptravel_get_currency_symbol( $value ) ) . ')';
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
		$map_data           = wptravel_get_maps();
		$maps               = $map_data['maps'];
		$i                  = 0;
		$mapped_map_options = array();
		foreach ( $maps as $value => $label ) {
			$mapped_map_options[ $i ]['label'] = $label;
			$mapped_map_options[ $i ]['value'] = $value;
			$i++;
		}
		$settings_options['maps']  = $mapped_map_options;
		$settings['wp_travel_map'] = $map_data['selected']; // override fallback map if addons map is selected in option and deactivate addon map.

		// Global Tabs override
		$custom_tab_enabled = apply_filters( 'wp_travel_is_custom_tabs_support_enabled', false );

		$default_tabs = wptravel_get_default_trip_tabs();

		// Global tab.
		$global_tabs = wptravel_get_global_tabs( $settings, $custom_tab_enabled );
		if ( $custom_tab_enabled ) { // If utilities is activated.
			$custom_tabs  = isset( $settings['wp_travel_custom_global_tabs'] ) ? $settings['wp_travel_custom_global_tabs'] : array();
			$default_tabs = array_merge( $default_tabs, $custom_tabs ); // To get Default label of custom tab.
		}
		$mapped_global_tabs = array();
		foreach ( $global_tabs as $key => $tab ) {
			$default_label             = isset( $default_tabs[ $key ]['label'] ) ? $default_tabs[ $key ]['label'] : $tab['label'];
			$tab_data                  = $tab;
			$tab_data['tab_key']       = $key;
			$tab_data['default_label'] = $default_label;

			$mapped_global_tabs[] = $tab_data;
		}
		$settings['global_tab_settings'] = $mapped_global_tabs; // override values.

		// trip facts.
		$facts        = $settings['wp_travel_trip_facts_settings'];
		$mapped_facts = array();
		if ( is_array( $facts ) && count( $facts ) > 0 ) {
			foreach ( $facts as $key => $fact ) {
				$new_fact = array(
					'key'                => $key,
					'name'               => isset( $facts[ $key ]['name'] ) ? $facts[ $key ]['name'] : '',
					'type'               => isset( $facts[ $key ]['type'] ) ? $facts[ $key ]['type'] : '',
					'options'            => isset( $facts[ $key ]['options'] ) && is_array( $facts[ $key ]['options'] ) ? array_values( $facts[ $key ]['options'] ) : array(),
					'icon'               => isset( $facts[ $key ]['icon'] ) ? $facts[ $key ]['icon'] : '',
					'icon_img'           => isset( $facts[ $key ]['icon_img'] ) ? $facts[ $key ]['icon_img'] : '',
					// 'fa_icon'            => isset( $facts[ $key ]['fa_icon'] ) ? $facts[ $key ]['fa_icon'] : '',
					'selected_icon_type' => isset( $facts[ $key ]['selected_icon_type'] ) ? $facts[ $key ]['selected_icon_type'] : 'icon-class',
				);
				$mapped_facts[] = $new_fact;
			}
		}
		$settings['wp_travel_trip_facts_settings'] = $mapped_facts; // override values.

		// Mapped sorted gateways.
		$sorted_gateways        = wptravel_sorted_payment_gateway_lists();
		$mapped_sorted_gateways = array();
		foreach ( $sorted_gateways as $key => $label ) {
			$gateway                  = array(
				'key'   => $key,
				'label' => $label,
			);
			$mapped_sorted_gateways[] = $gateway;
		}
		$settings['sorted_gateways'] = $mapped_sorted_gateways; // override values.

		// Bank Deposit
		$bank_deposits        = $settings['wp_travel_bank_deposits'];
		$mapped_bank_deposite = array();
		if ( isset( $bank_deposits['account_name'] ) && is_array( $bank_deposits['account_name'] ) && count( $bank_deposits['account_name'] ) > 0 ) {
			foreach ( $bank_deposits['account_name'] as $key => $account_name ) {
				$bank_data = array(
					'account_name'   => $account_name,
					'account_number' => isset( $bank_deposits['account_number'][ $key ] ) ? $bank_deposits['account_number'][ $key ] : '',
					'bank_name'      => isset( $bank_deposits['bank_name'][ $key ] ) ? $bank_deposits['bank_name'][ $key ] : '',
					'sort_code'      => isset( $bank_deposits['sort_code'][ $key ] ) ? $bank_deposits['sort_code'][ $key ] : '',
					'iban'           => isset( $bank_deposits['iban'][ $key ] ) ? $bank_deposits['iban'][ $key ] : '',
					'swift'          => isset( $bank_deposits['swift'][ $key ] ) ? $bank_deposits['swift'][ $key ] : '',
					'routing_number' => isset( $bank_deposits['routing_number'][ $key ] ) ? $bank_deposits['routing_number'][ $key ] : '',
					'enable'         => isset( $bank_deposits['enable'][ $key ] ) ? $bank_deposits['enable'][ $key ] : '',
				);

				$mapped_bank_deposite[] = $bank_data;
			}
		}
		$settings['wp_travel_bank_deposits'] = $mapped_bank_deposite; // override values.

		// Page Lists.
		$lists     = get_posts(
			array(
				'numberposts' => -1,
				'post_type'   => 'page',
				'orderby'     => 'title',
				'order'       => 'asc',
			)
		);
		$page_list = array();
		$i         = 0;
		foreach ( $lists as $page_data ) {
			$page_list[ $i ]['label'] = $page_data->post_title;
			$page_list[ $i ]['value'] = $page_data->ID;
			$i++;
		}
		$settings_options['page_list'] = $page_list;

		$settings_options['wp_travel_user_since'] = get_option( 'wp_travel_user_since', '3.0.0' );

		// fact options.
		$settings_options['fact_options'] = array(
			array(
				'label' => __( 'Plain Text', 'wp-travel' ),
				'value' => 'text',
			),
			array(
				'label' => __( 'Single Select', 'wp-travel' ),
				'value' => 'single',
			),
			array(
				'label' => __( 'Multiple Select', 'wp-travel' ),
				'value' => 'multiple',
			),
		);
		// is multisite.
		$settings_options['is_multisite']     = is_multisite();
		$settings_options['default_settings'] = wptravel_settings_default_fields(); // default settings values. [this will help to display only available values rather than saved values.]
		$settings_options['saved_settings']   = get_option( 'wp_travel_settings', array() ); // default settings values.
		$settings['enable_one_page_booking'] = isset( $settings['enable_one_page_booking'] ) ? $settings['enable_one_page_booking'] : false;
		$settings         = apply_filters( 'wp_travel_settings_values', $settings ); // main settings value filter.
		$settings_options = apply_filters( 'wp_travel_settings_options', $settings_options, $settings ); // additional values like dropdown options etc.
		// Asign Additional option values.
		$settings['options'] = $settings_options;
		$settings['wpml_enable'] = isset( $settings['wpml_migrations'] ) ? $settings['wpml_migrations'] : '';
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_SETTINGS',
			array(
				'settings' => $settings,
			)
		);
	}

	public static function update_settings( $settings_data ) {

		$settings_data = (array) $settings_data;

		$settings        = wptravel_get_settings();
		$settings_fields = array_keys( wptravel_settings_default_fields() );

		$ignore_fields = array( 'wp_travel_trip_facts_settings', 'global_tab_settings', 'sorted_gateways', 'wp_travel_bank_deposits' );
		foreach ( $settings_fields as $settings_field ) {
			if ( in_array( $settings_field, $ignore_fields ) ) {
				continue;
			}
			if ( isset( $settings_data[ $settings_field ] ) ) {
				// Default pages settings. [only to get page in - wptravel_get_page_id()] // Need enhanchement.
				$page_ids = array( 'cart_page_id', 'checkout_page_id', 'dashboard_page_id', 'thank_you_page_id' );

				if ( in_array( $settings_field, $page_ids ) && ! empty( $settings_data[ $settings_field ] ) ) {
					$page_id = $settings_data[ $settings_field ];
					/**
					 * @since 3.1.8.
					 * 
					 * @remove ICL_LANGUAGE_CODE in @since 6.4.0
					 */ 
					// if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
					// 	update_option( 'wp_travel_' . $settings_field . '_' . ICL_LANGUAGE_CODE, $page_id );
					// 	continue;
					// } else {
					update_option( 'wp_travel_' . $settings_field, $page_id );
					// }
				}

				$settings[ $settings_field ] = wp_unslash( $settings_data[ $settings_field ] );
			}
		}

		if ( isset( $settings_data['wp_travel_bank_deposits'] ) && is_array( $settings_data['wp_travel_bank_deposits'] ) ) {
			$i             = 0;
			$bank_deposits = array();
			foreach ( $settings_data['wp_travel_bank_deposits'] as $bank_deposit ) {

				if ( ! $bank_deposit['account_name'] && ! $bank_deposit['account_number'] ) {
					continue; // Not save if no account name and number.
				}
				$bank_deposits['account_name'][ $i ]   = $bank_deposit['account_name'];
				$bank_deposits['account_number'][ $i ] = $bank_deposit['account_number'];
				$bank_deposits['bank_name'][ $i ]      = $bank_deposit['bank_name'];
				$bank_deposits['sort_code'][ $i ]      = $bank_deposit['sort_code'];
				$bank_deposits['iban'][ $i ]           = $bank_deposit['iban'];
				$bank_deposits['swift'][ $i ]          = $bank_deposit['swift'];
				$bank_deposits['routing_number'][ $i ] = $bank_deposit['routing_number'] ? $bank_deposit['routing_number'] : '';
				$bank_deposits['enable'][ $i ]         = $bank_deposit['enable'];
				$i++;
			}
			$settings['wp_travel_bank_deposits'] = $bank_deposits;

		}

		if ( isset( $settings_data['global_tab_settings'] ) && is_array( $settings_data['global_tab_settings'] ) ) {

			$global_tabs = array();
			foreach ( $settings_data['global_tab_settings'] as  $global_tab ) {
				$tab_key                                 = $global_tab['tab_key']; // quick fix.
				$global_tabs[ $tab_key ]['label']        = $global_tab['label'];
				$global_tabs[ $tab_key ]['show_in_menu'] = $global_tab['show_in_menu'];
			}
			$settings['global_tab_settings'] = $global_tabs;
		}
		if ( isset( $settings_data['sorted_gateways'] ) && is_array( $settings_data['sorted_gateways'] ) ) {

			$sorted_gateways = array();
			foreach ( $settings_data['sorted_gateways'] as  $gateway ) {
				$sorted_gateways[] = $gateway['key']; // quick fix.
				// $sorted_gateways[ $key ]        = $gateway['label'];
			}
			$settings['sorted_gateways'] = $sorted_gateways;
		}

		// Facts
		if ( isset( $settings_data['wp_travel_trip_facts_settings'] ) && is_array( $settings_data['wp_travel_trip_facts_settings'] ) ) {

			$facts_settings = array();
			$facts          = array();
			foreach ( $settings_data['wp_travel_trip_facts_settings'] as $index => $fact ) {
				$name    = $fact['name'];
				$type    = $fact['type'];
				$options = $fact['options'];
				if ( ! is_array( $options ) ) {
					$options = explode( ',', $options );
				}
				$icon = $fact['icon'];
				$key  = isset( $fact['key'] ) && ! empty( $fact['key'] ) ? $fact['key'] : $index;

				$facts_settings[ $key ] = array(
					'name'    => $name,
					'type'    => $type,
					'options' => $options,
					'icon'    => $icon,
				);
			}
			$settings['wp_travel_trip_facts_settings'] = $facts_settings;
		}
		if ( is_array( $facts ) && count( $facts ) > 0 ) {
			foreach ( $facts as $key => $fact ) {
				$new_fact       = array(
					'key'     => $key,
					'name'    => isset( $facts[ $key ]['name'] ) ? $facts[ $key ]['name'] : '',
					'type'    => isset( $facts[ $key ]['type'] ) ? $facts[ $key ]['type'] : '',
					'options' => isset( $facts[ $key ]['options'] ) && is_array( $facts[ $key ]['options'] ) ? array_values( $facts[ $key ]['options'] ) : array(),
					'icon'    => isset( $facts[ $key ]['icon'] ) ? $facts[ $key ]['icon'] : '',
				);
				$mapped_facts[] = $new_fact;
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
		/**
		 * @since 6.4.0
		 * set pdf primary color
		 */
		$settings['set_trip_itinerary_pdf_primary_color'] = isset( $settings_data['set_trip_itinerary_pdf_primary_color'] ) ? $settings_data['set_trip_itinerary_pdf_primary_color'] : '#28B951';

		$settings['set_trip_itinerary_pdf_secondary_color'] = isset( $settings_data['set_trip_itinerary_pdf_secondary_color'] ) ? $settings_data['set_trip_itinerary_pdf_secondary_color'] : '#FF9671';

		$settings = apply_filters( 'wp_travel_block_before_save_settings', $settings, $settings_data );

		/**
		 * set setting wpml_migration for compatible with wpml
		 *
		 * @since 6.4.0
		 */
		if ( isset( $settings_data['wpml_migrations'] ) ) {
			$settings['wpml_migrations'] = $settings_data['wpml_migrations'];
		}
		$settings['enable_one_page_booking'] = isset( $settings_data['enable_one_page_booking'] ) ? $settings_data['enable_one_page_booking'] : false;
		// unset( $settings['modules'] );
		update_option( 'wp_travel_settings', $settings );
		/**
		 * Hook to trigger after settings saved.
		 *
		 * @since 5.2.0
		 */
		do_action( 'wptravel_action_after_settings_saved', $settings, $settings_data );
		$settings['wpml_enable'] = isset( $settings['wpml_migrations'] ) ? $settings['wpml_migrations'] : '';
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_SETTINGS',
			array(
				'settings' => $settings,
			)
		);
	}
}
