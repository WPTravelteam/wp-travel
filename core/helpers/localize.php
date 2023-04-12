<?php
/**
 * Helpers Localize.
 *
 * @package WP_Travel
 * @since 5.0.1
 */

defined( 'ABSPATH' ) || exit;
/**
 * WpTravel_Helpers_Localize class.
 *
 * @since 5.0.1
 */
class WpTravel_Helpers_Localize {

	/**
	 * Get All Localized data
	 *
	 * @since 5.0.1
	 *
	 * @return array
	 */
	public static function get() {
		global $post;
		$localized_data = array();
		$settings       = wptravel_get_settings();

		// Getting Locale to fetch Localized calender js.
		$lang_code            = explode( '-', get_bloginfo( 'language' ) );
		$locale               = $lang_code[0];
		$wp_content_file_path = WP_CONTENT_DIR . '/languages/wp-travel/datepicker/';
		$default_path         = sprintf( '%s/app/assets/js/lib/datepicker/i18n/', plugin_dir_path( WP_TRAVEL_PLUGIN_FILE ) );
		$filename             = 'datepicker.' . $locale . '.js';
		if ( ! file_exists( trailingslashit( $wp_content_file_path ) . $filename ) && ! file_exists( trailingslashit( $default_path ) . $filename ) ) {
			$locale = 'en';
		}

		$rdp_locale       = get_locale();
		$rdp_locale_array = explode( '_', $rdp_locale );
		if ( is_array( $rdp_locale_array ) && count( $rdp_locale_array ) > 1 && strtoupper( $rdp_locale_array[0] ) === strtoupper( $rdp_locale_array[1] ) ) {
			$rdp_locale = $rdp_locale_array[0];
		}
		$rdp_locale = str_replace( '_', '', $rdp_locale ); // React date picker locale.
		// Frontend Localized Strings for React block.
		if ( self::is_request( 'frontend' ) ) {
			$_wp_travel                       = array();
			$_wp_travel['_nonce']             = wp_create_nonce( 'wp_travel_nonce' );
			$_wp_travel['ajax_url']           = admin_url( 'admin-ajax.php' );
			$_wp_travel['build_path']         = esc_url( trailingslashit( plugin_dir_url( WP_TRAVEL_PLUGIN_FILE ) . 'app/build' ) );
			$_wp_travel['cart_url']           = wptravel_get_cart_url();
			$_wp_travel['currency_symbol']    = wptravel_get_currency_symbol();
			$_wp_travel['date_format_moment'] = wptravel_php_to_moment_format( get_option( 'date_format' ) );
			$_wp_travel['rdp_locale']         = $rdp_locale;
			$_wp_travel['time_format']        = get_option( 'time_format' );
			$_wp_travel['date_format']        = get_option( 'date_format' );
			$_wp_travel['currency']           = $settings['currency'];
			$_wp_travel['currency_position']  = $settings['currency_position'];
			$_wp_travel['decimal_separator']  = $settings['decimal_separator'] ? $settings['decimal_separator'] : '.';
			$_wp_travel['number_of_decimals'] = $settings['number_of_decimals'] ? $settings['number_of_decimals'] : 0;
			$_wp_travel['thousand_separator'] = $settings['thousand_separator'] ? $settings['thousand_separator'] : ',';
			$_wp_travel['trip_date_listing']  = $settings['trip_date_listing'];
			$_wp_travel['strings']            = WpTravel_Helpers_Strings::get();
			$_wp_travel['itinerary_v2']       = wptravel_use_itinerary_v2_layout();

			// Localized varialble for old trips less than WP Travel 4.0. // Need to migrate in _wp_travel.
			$wp_travel = array(
				'currency_symbol'    => wptravel_get_currency_symbol(),
				'currency_position'  => $settings['currency_position'],
				'thousand_separator' => $settings['thousand_separator'],
				'decimal_separator'  => $settings['decimal_separator'],
				'number_of_decimals' => $settings['number_of_decimals'],

				'prices'             => wptravel_get_itinereries_prices_array(), // Used to get min and max price to use it in range slider filter widget.
				'locale'             => $locale,
				'nonce'              => wp_create_nonce( 'wp_travel_frontend_security' ),
				'_nonce'             => wp_create_nonce( 'wp_travel_nonce' ),
				'ajaxUrl'            => admin_url( 'admin-ajax.php' ),
				'strings'            => WpTravel_Helpers_Strings::get(),
				'zoom'               => $settings['google_map_zoom_level'],
				'cartUrl'            => wptravel_get_cart_url(),
				'checkoutUrl'        => wptravel_get_checkout_url(), // @since 4.3.2
				'isEnabledCartPage'  => WP_Travel_Helpers_Cart::is_enabled_cart_page(), // @since 4.3.2
			);
			if ( wptravel_can_load_payment_scripts() ) {

				global $wt_cart;
				if ( WP_Travel::is_page( 'checkout' ) ) {
					$trip_item = $wt_cart->getitems();
					$invent = 'no';
					foreach ( $trip_item as $key => $val ) {
						$id_trip = $val['trip_id'];
						$inventorys  = get_post_meta( $id_trip, 'enable_trip_inventory' );
						if ( $inventorys[0] == 'yes' ) {
							$invent = 'yes';
						}
					}
					$wp_travel['items'] = $trip_item;
					$wp_travel['inventory'] = $invent;
				}
				$cart_amounts   = $wt_cart->get_total();
				$trip_price     = isset( $cart_amounts['total'] ) ? $cart_amounts['total'] : '';
				$payment_amount = isset( $cart_amounts['total_partial'] ) ? $cart_amounts['total_partial'] : '';

				$wp_travel['payment']['currency_code']   = $settings['currency'];
				$wp_travel['payment']['currency_symbol'] = wptravel_get_currency_symbol();
				$wp_travel['payment']['trip_price']      = $trip_price;
				$wp_travel['payment']['payment_amount']  = $payment_amount;
			}

			// Add Post specific data for wp_travel and _wp_travel var.
			if ( $post && WP_Travel::is_page( 'single' ) ) { // There will be no $post for 404, search and other pages.
				$trip_id    = $post->ID;
				$map_data   = wptravel_get_map_data( $trip_id ); // Only Google map data.
				$maps       = array(
					'google_map' => array(
						'lat' => $map_data['lat'],
						'lng' => $map_data['lng'],
						'loc' => $map_data['loc'],
					),
				);
				$maps       = apply_filters( 'wptravel_maps_data', $maps, $settings ); // @since 4.6.5
				$trip       = WP_Travel_Helpers_Trips::get_trip( $trip_id );

				if ( ! is_wp_error( $trip ) && 'WP_TRAVEL_TRIP_INFO' === $trip['code'] ) {
					// _wp_travel vars.
					$_wp_travel['maps']               = $maps;
					$_wp_travel['trip_data']          = $trip['trip'];

					// wp_travel vars.
					// Need map data enhancement.
					$wp_travel['lat'] = ! empty( $map_data['lat'] ) ? ( $map_data['lat'] ) : '';
					$wp_travel['lng'] = ! empty( $map_data['lng'] ) ? ( $map_data['lng'] ) : '';
					$wp_travel['loc'] = ! empty( $map_data['loc'] ) ? ( $map_data['loc'] ) : '';

					$wp_travel['payment']['price_per']       = wptravel_get_price_per_text( $trip_id, '', true );
				}
			}

			$wp_travel                    = apply_filters( 'wptravel_frontend_data', $wp_travel, $settings );
			$localized_data['wp_travel']  = $wp_travel;
			$localized_data['_wp_travel'] = $_wp_travel;
		}

		if ( self::is_request( 'admin' ) ) {
			// Booking Chart Data. Need to merge in wp_travel or _wp_travel.
			$booking_data      = wptravel_get_booking_data();
			$stat_data         = isset( $booking_data['stat_data'] ) ? $booking_data['stat_data'] : array();
			$labels            = isset( $stat_data['stat_label'] ) ? $stat_data['stat_label'] : array();
			$datas             = isset( $stat_data['data'] ) ? $stat_data['data'] : array();
			$data_label        = isset( $stat_data['data_label'] ) ? $stat_data['data_label'] : array();
			$data_bg_color     = isset( $stat_data['data_bg_color'] ) ? $stat_data['data_bg_color'] : array();
			$data_border_color = isset( $stat_data['data_border_color'] ) ? $stat_data['data_border_color'] : array();

			$max_bookings  = isset( $booking_data['max_bookings'] ) ? $booking_data['max_bookings'] : 0;
			$max_pax       = isset( $booking_data['max_pax'] ) ? $booking_data['max_pax'] : 0;
			$top_countries = ( isset( $booking_data['top_countries'] ) && count( $booking_data['top_countries'] ) > 0 ) ? $booking_data['top_countries'] : array( 'N/A' );
			$top_itinerary = ( isset( $booking_data['top_itinerary'] ) && count( $booking_data['top_itinerary'] ) > 0 ) ? $booking_data['top_itinerary'] : array(
				'name' => esc_html__( 'N/A', 'wp-travel' ),
				'url'  => '',
			);

			$booking_stat_from = isset( $booking_data['booking_stat_from'] ) ? $booking_data['booking_stat_from'] : '';
			$booking_stat_to   = isset( $booking_data['booking_stat_to'] ) ? $booking_data['booking_stat_to'] : '';

			$wp_travel_stat_data = array();
			foreach ( $datas as $key => $data ) {
				$wp_travel_stat_data[] = array(
					'label'           => $data_label[ $key ],
					'backgroundColor' => $data_bg_color[ $key ],
					'borderColor'     => $data_border_color[ $key ],
					'data'            => $data,
					'fill'            => false,
				);
			}
			$wp_travel_chart_data = array(
				'ajax_url'          => 'admin-ajax.php',
				'chart_title'       => esc_html__( 'Chart Stat', 'wp-travel' ),
				'labels'            => wp_json_encode( $labels ),
				'datasets'          => wp_json_encode( $wp_travel_stat_data ),
				'max_bookings'      => $max_bookings,
				'max_pax'           => $max_pax,
				'top_countries'     => implode( ', ', $top_countries ),
				'top_itinerary'     => $top_itinerary,
				// Show more / less top countries.
				'show_more_text'    => __( 'More', 'wp-travel' ),
				'show_less_text'    => __( 'Less', 'wp-travel' ),
				'show_char'         => 18,

				'booking_stat_from' => $booking_stat_from,
				'booking_stat_to'   => $booking_stat_to,
				'compare_stat'      => false,
			);
			if ( isset( $_REQUEST['compare_stat'] ) && 'yes' === $_REQUEST['compare_stat'] ) { // phpcs:ignore
				$compare_stat_from = isset( $booking_data['compare_stat_from'] ) ? $booking_data['compare_stat_from'] : '';
				$compare_stat_to   = isset( $booking_data['compare_stat_to'] ) ? $booking_data['compare_stat_to'] : '';

				$compare_max_bookings  = isset( $booking_data['compare_max_bookings'] ) ? $booking_data['compare_max_bookings'] : 0;
				$compare_max_pax       = isset( $booking_data['compare_max_pax'] ) ? $booking_data['compare_max_pax'] : 0;
				$compare_top_countries = ( isset( $booking_data['compare_top_countries'] ) && count( $booking_data['compare_top_countries'] ) > 0 ) ? $booking_data['compare_top_countries'] : array( 'N/A' );
				$compare_top_itinerary = ( isset( $booking_data['compare_top_itinerary'] ) && count( $booking_data['compare_top_itinerary'] ) > 0 ) ? $booking_data['compare_top_itinerary'] : array(
					'name' => esc_html__( 'N/A', 'wp-travel' ),
					'url'  => '',
				);

				$wp_travel_chart_data['compare_stat_from']     = $compare_stat_from;
				$wp_travel_chart_data['compare_stat_to']       = $compare_stat_to;
				$wp_travel_chart_data['compare_max_bookings']  = $compare_max_bookings;
				$wp_travel_chart_data['compare_max_pax']       = $compare_max_pax;
				$wp_travel_chart_data['compare_top_countries'] = implode( ', ', $compare_top_countries );
				$wp_travel_chart_data['compare_top_itinerary'] = $compare_top_itinerary;
				$wp_travel_chart_data['compare_stat']          = true;
				$wp_travel_chart_data['total_sales_compare']   = $booking_data['total_sales_compare'];
			}
			$wp_travel_chart_data                   = apply_filters( 'wptravel_chart_data', $wp_travel_chart_data );
			$localized_data['wp_travel_chart_data'] = $wp_travel_chart_data;
			// End of Booking Chart Data.

			// Map & Gallery Data. Need to merge in wp_travel or _wp_travel.
			$wp_travel_gallery_data                       = array(
				'ajax'            => admin_url( 'admin-ajax.php' ),
				'lat'             => isset( $map_data['lat'] ) ? $map_data['lat'] : '', // May be these map data are not required because it always return empty due to no trip id param will work in admin.
				'lng'             => isset( $map_data['lng'] ) ? $map_data['lng'] : '',
				'loc'             => isset( $map_data['loc'] ) ? $map_data['loc'] : '',
				'labels'          => array(
					'uploader_files_computer' => __( 'Select Files from Your Computer', 'wp-travel' ),
				),
				'drag_drop_nonce' => wp_create_nonce( 'wp-travel-drag-drop-nonce' ),
			);
			$date_format                                  = get_option( 'date_format' );
			$js_date_format                               = wptravel_date_format_php_to_js();
			$moment_date_format                           = wptravel_moment_date_format( $date_format );
			$wp_travel_gallery_data['js_date_format']     = $js_date_format;
			$wp_travel_gallery_data['moment_date_format'] = $moment_date_format;

			$wp_travel_gallery_data = apply_filters( 'wp_travel_localize_gallery_data', $wp_travel_gallery_data ); // phpcs:ignore
			$wp_travel_gallery_data = apply_filters( 'wptravel_localize_gallery_data', $wp_travel_gallery_data );
			// end of Map & Gallery Data.
			$localized_data['wp_travel_drag_drop_uploader'] = $wp_travel_gallery_data;

			// @since 4.6.4
			$_wp_travel_admin = array(
				'strings' => WpTravel_Helpers_Strings::get(),
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'_nonce'  => wp_create_nonce( 'wp_travel_nonce' ),
			);

			if ( $post && WP_Travel::is_page( 'itineraries', true ) ) {
				$wp_travel_itinerary          = new WP_Travel_Itinerary( $post );
				$_wp_travel_admin['overview'] = $wp_travel_itinerary->get_content();
			}

			$localized_data['_wp_travel_admin'] = $_wp_travel_admin;

		}
		return apply_filters( 'wptravel_localized_data', $localized_data );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string $type admin, ajax, cron or frontend.
	 *
	 * @since 5.0.1
	 * @return bool
	 */
	private static function is_request( $type ) {
		switch ( $type ) {
			case 'admin':
				return is_admin();
			case 'ajax':
				return defined( 'DOING_AJAX' );
			case 'cron':
				return defined( 'DOING_CRON' );
			case 'frontend':
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}
}
