<?php
/**
 * Trip helpers class file.
 *
 * @phpcs:disable
 *
 * @package WP_Travel
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Trip helpers class.
 */
class WpTravel_Helpers_Trips {

	/**
	 * Dates table key.
	 *
	 * @var string $date_table
	 */
	private static $date_table = 'wt_dates';

	/**
	 * Pricing table key.
	 *
	 * @var string $pricing_table
	 */
	private static $pricing_table = 'wt_pricings';

	/**
	 * Price category table key.
	 *
	 * @var string $price_category_table
	 */
	private static $price_category_table = 'wt_price_category_relation';

	/**
	 * Returns trip data.
	 *
	 * @param int $trip_id Trip ID.
	 */
	public static function get_trip( $trip_id = false ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$trip                = get_post( $trip_id );
		$wp_travel_itinerary = new WP_Travel_Itinerary( $trip );
		$group_size          = $wp_travel_itinerary->get_group_size();

		if ( ! is_object( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$settings = wptravel_get_settings();

		$extras            = WP_Travel_Helpers_Trip_Extras::get_trip_extras();
		$has_extras        = is_array( $extras ) && isset( $extras['code'] ) && 'WP_TRAVEL_TRIP_EXTRAS' == $extras['code'] && isset( $extras['trip_extras'] ) && count( $extras['trip_extras'] ) > 0 ? true : false;
		$highest_price     = get_post_meta( $trip_id, 'wp_travel_show_highest_price', true );
		$trip_default_data = array(
			'trip_video_code'					=> get_post_meta( $trip_id, 'wp_travel_video_code', true ),
			'pricing_type'                        => 'multiple-price',
			'custom_booking_type'                 => 'custom-link',
			'custom_booking_form'                 => '',
			'custom_booking_link'                 => '',
			'custom_booking_link_text'            => '',
			'custom_booking_link_open_in_new_tab' => '',
			'highest_price'                       => ! empty( $highest_price ) ? $highest_price : '',
			'enable_pax_all_pricing'              => get_post_meta( $trip_id, 'wp_travel_enable_pax_all_pricing', true ),
			'pricings'                            => array(),
			'trip_price'                          => 0,
			'regular_price'                       => 0,
			'enable_sale'                         => false,
			'featured_image_data'                 => false,
			'has_extras'                          => $has_extras,
			'reviews'                             => get_comments_number( $trip_id ), // for blocks.
		);

		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false ); // @phpcs:ignore
		$enable_custom_itinerary_tabs = apply_filters( 'wptravel_custom_itinerary_tabs', $enable_custom_itinerary_tabs );
		$use_global_tabs              = get_post_meta( $trip_id, 'wp_travel_use_global_tabs', true );

		$default_tabs = wptravel_get_default_trip_tabs();
		$trip_tabs    = wptravel_get_admin_trip_tabs( $trip_id, $enable_custom_itinerary_tabs ); // quick fix.
		if ( $enable_custom_itinerary_tabs ) { // If utilities is activated.
			$custom_tabs = get_post_meta( $trip_id, 'wp_travel_itinerary_custom_tab_cnt_', true );
			$custom_tabs = ( $custom_tabs ) ? $custom_tabs : array();

			$default_tabs = array_merge( $default_tabs, $custom_tabs ); // To get Default label of custom tab.
		}

		$i    = 0;
		$tabs = array();
		foreach ( $trip_tabs as $key => $tab ) :
			$default_label               = isset( $default_tabs[ $key ]['label'] ) ? $default_tabs[ $key ]['label'] : $tab['label'];
			$tabs[ $i ]['id']            = $i;
			$tabs[ $i ]['default_label'] = $default_label;
			$tabs[ $i ]['label']         = $tab['label'];
			$tabs[ $i ]['show_in_menu']  = $tab['show_in_menu'];
			$tabs[ $i ]['tab_key']       = ! empty( $tab['tab_key'] ) ? $tab['tab_key'] : $key; // Key is required to save meta value. @todo: can remove this key latter.
			$i++;
		endforeach;

		$trip_facts     = get_post_meta( $trip_id, 'wp_travel_trip_facts', true );
		$settings_facts = $settings['wp_travel_trip_facts_settings'];

		if ( is_string( $trip_facts ) ) {
			$trip_facts = json_decode( $trip_facts, true );
		}
		if ( empty( $trip_facts ) ) {
			$trip_facts = array();
		}

		$new_trip_facts = array();
		// don't display those facts which have been removed from global setting
		if ( is_array( $trip_facts ) && count( $trip_facts ) > 0 ) {
			foreach ( $trip_facts as $f ) {
				$name = strtolower( $f['label'] );
				foreach ( $settings_facts as $s ) {
					$s_name = strtolower( $s['name'] );
					if ( $name == $s_name ) {
						$new_trip_facts[] = $f;
					}
				}
			}
		}

		$trip_facts = $new_trip_facts;

		foreach ( $trip_facts as $key => $trip_fact ) {
			$trip_fact_id = $trip_fact['fact_id'];
			if ( isset( $settings_facts[ $trip_fact_id ] ) ) { // To check if current trip facts id matches the settings trip facts id. If matches then get icon and label.
				$icon_args = $settings_facts[ $trip_fact_id ];
			} else {
				$trip_fact_setting = array_filter(
					$settings_facts,
					function( $setting ) use ( $trip_fact ) {
						return $setting['name'] === $trip_fact['label'];
					}
				); // Gives an array for matches label with its other detail as well.

				if ( empty( $trip_fact_setting ) ) { // If there is empty array that means label doesn't matches. Hence skip that and continue.
					continue;
				}
				foreach ( $trip_fact_setting as $set ) {
					$icon      = $set['icon'];
					$label     = $set['name'];
					$icon_args = $set;
				}
			}
			$icon_args['return']        = true;
			$trip_facts[ $key ]['icon'] = WpTravel_Helpers_Icon::get( $icon_args );
		}

		$use_global_trip_enquiry_option = get_post_meta( $trip_id, 'wp_travel_use_global_trip_enquiry_option', true );
		if ( '' === $use_global_trip_enquiry_option ) {
			$use_global_trip_enquiry_option = 'yes';
		}
		$enable_trip_enquiry_option = get_post_meta( $trip_id, 'wp_travel_enable_trip_enquiry_option', true );

		$trip_overview = get_post_meta( $trip_id, 'wp_travel_overview', true );
		$trip_video_url = get_post_meta( $trip_id, 'wp_travel_video_url', true );
		$trip_include  = get_post_meta( $trip_id, 'wp_travel_trip_include', true );
		$trip_exclude  = get_post_meta( $trip_id, 'wp_travel_trip_exclude', true );
		$trip_outline  = get_post_meta( $trip_id, 'wp_travel_outline', true );
		$itineraries   = get_post_meta( $trip_id, 'wp_travel_trip_itinerary_data', true );
		$faqs          = wptravel_get_faqs( $trip_id );
		$map_data      = wptravel_get_map_data( $trip_id );
		// TODO : Include following map_data inside `wptravel_get_map_data` function.
		$zoomlevel   = ! empty( get_post_meta( $trip_id, 'wp_travel_zoomlevel', true ) ) ? absint( get_post_meta( $trip_id, 'wp_travel_zoomlevel', true ) ) : 10;
		$zoomlevel   = apply_filters( 'wp_travel_trip_zoomlevel', $zoomlevel, $trip_id ); // @phpcs:ignore
		$zoomlevel   = apply_filters( 'wptravel_trip_zoomlevel', $zoomlevel, $trip_id );
		$use_lat_lng = ! empty( get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true ) ) ? get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true ) : 'no';
		$use_lat_lng = apply_filters( 'wp_travel_trip_map_use_lat_lng', $use_lat_lng, $trip_id );
		$use_lat_lng = apply_filters( 'wptravel_trip_map_use_lat_lng', $use_lat_lng, $trip_id );

		$iframe_height           = ! empty( get_post_meta( $trip_id, 'wp_travel_map_iframe_height', true ) ) ? absint( get_post_meta( $trip_id, 'wp_travel_map_iframe_height', true ) ) : 400;
		$map_data['zoomlevel']   = $zoomlevel;
		$map_data['use_lat_lng'] = $use_lat_lng;

		$minimum_partial_payout_use_global = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_use_global', true );
		$minimum_partial_payout_percent    = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_percent', true );
		if ( ! $minimum_partial_payout_percent ) {
			$minimum_partial_payout_percent = $settings['minimum_partial_payout'];
		}

		$days                		= get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
		$night               		= get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );
		$duration_start_date 		= get_post_meta( $trip_id, 'wp_travel_trip_duration_start_date', true );
		$duration_end_date 	 		= get_post_meta( $trip_id, 'wp_travel_trip_duration_end_date', true );
		$trip_duration_formating	= get_post_meta( $trip_id, 'wp_travel_trip_duration_formating', true);
		if ( empty( $trip_duration_formating ) ) {
			$trip_duration = array(
				'days'   		=> $days,
				'nights' 		=> $night,
				// 'start_date' 	=> isset( $duration_start_date ) ? $duration_start_date : '',
				// 'end_date'   	=> isset( $duration_end_date ) ? $duration_end_date : '',
			);
		} else {
			$old_duration_select = isset( $trip_duration_formating['duration_format'] ) ? $trip_duration_formating['duration_format'] : '';
			// if ( ! empty( $old_duration_select ) && $old_duration_select == 'hour' ) {
			// 	$duration_selected_date = $old_duration_select;
			// } else {
			// 	$duration_selected_date = 'day_night';
			// }
			$new_duration_date = array(
				'days'				=> isset( $trip_duration_formating['days'] ) ? $trip_duration_formating['days'] : '',
				'nights'			=> isset( $trip_duration_formating['nights'] ) ? $trip_duration_formating['nights'] : '',
				'hours'				=> isset( $trip_duration_formating['hours'] ) ? $trip_duration_formating['hours'] : '',
				'minute'			=> isset( $trip_duration_formating['minute'] ) ? $trip_duration_formating['minut'] : '',
				'duration_format'	=> $old_duration_select,
			);
			$trip_duration = apply_filters( 'wp_travel_trip_duration_formating_selected', $trip_duration_formating );
		}
		$trip_data     = array(
			'id'                                => $trip->ID,
			'title'                             => $trip->post_title,
			'url'                               => get_permalink( $trip->ID ),
			'trip_code'                         => wptravel_get_trip_code( $trip->ID ),
			'use_global_tabs'                   => $use_global_tabs,
			'trip_tabs'                         => $tabs,
			'trip_overview'                     => $trip_overview,
			'trip_include'                      => $trip_include,
			'trip_exclude'                      => $trip_exclude,
			'trip_outline'                      => $trip_outline,
			'itineraries'                       => is_array( $itineraries ) ? array_values( $itineraries ) : array(),
			'faqs'                              => $faqs,
			'trip_facts'                        => $trip_facts,
			'use_global_trip_enquiry_option'    => $use_global_trip_enquiry_option,
			'enable_trip_enquiry_option'        => $enable_trip_enquiry_option,
			'map_data'                          => $map_data,
			'trip_duration'                     => $trip_duration,
			'group_size'                        => (int) $group_size, // Labeled as Inventory size.
			'minimum_partial_payout_use_global' => $minimum_partial_payout_use_global,
			'minimum_partial_payout_percent'    => $minimum_partial_payout_percent,
			// '_post' => $trip,
		);

		$post_thumbnail_id = get_post_thumbnail_id( $trip->ID );
		$upload_dir        = wp_get_upload_dir();
		if ( ! empty( $post_thumbnail_id ) ) {
			$attachment_meta_data = wp_get_attachment_metadata( $post_thumbnail_id );
			$re                   = '/^(.*\/)+(.*\.+.+\w)/m';
			$attachment_file      = isset( $attachment_meta_data['file'] ) ? $attachment_meta_data['file'] : '';
			preg_match_all( $re, $attachment_file, $matches, PREG_SET_ORDER, 0 );
			$subfolder                                  = ! empty( $matches[0][1] ) ? $matches[0][1] : '';
			$full_attachment                            = trailingslashit( $upload_dir['baseurl'] ) . $attachment_file;
			$trip_data['featured_image_data']['width']  = isset( $attachment_meta_data['width'] ) ? $attachment_meta_data['width'] : '';
			$trip_data['featured_image_data']['height'] = isset( $attachment_meta_data['height'] ) ? $attachment_meta_data['height'] : '';
			$trip_data['featured_image_data']['url']    = $full_attachment;
			$trip_data['featured_image_data']['file']   = $attachment_file;
			$trip_data['featured_image_data']['sizes']  = isset( $attachment_meta_data['sizes'] ) ? $attachment_meta_data['sizes'] : '';
			if ( ! empty( $attachment_meta_data['sizes'] ) ) {
				$size_index = 0;
				foreach ( $attachment_meta_data['sizes'] as $size_key => $size ) {
					$trip_data['featured_image_data']['sizes'][ $size_key ]        = $size;
					$trip_data['featured_image_data']['sizes'][ $size_key ]['url'] = trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $subfolder ) . $size['file'];
					$size_index++;
				}
			}
		}

		$pricings = WP_Travel_Helpers_Pricings::get_pricings( $trip->ID );
		if ( ! is_wp_error( $pricings ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricings['code'] ) {
			$trip_data['pricings'] = (array) $pricings['pricings'];

			$args                             = array( 'trip_id' => $trip_id );
			$args_regular                     = $args;
			$args_regular['is_regular_price'] = true;
			$trip_price                       = WP_Travel_Helpers_Pricings::get_price( $args );
			$regular_price                    = WP_Travel_Helpers_Pricings::get_price( $args_regular );
			$enable_sale                      = self::is_sale_enabled(
				array(
					'trip_id'                => $trip_id,
					'from_price_sale_enable' => true,
				)
			);

			$trip_data['trip_price']    = $trip_price;
			$trip_data['regular_price'] = $regular_price;
			$trip_data['enable_sale']   = $enable_sale;
		}

		$dates = WP_Travel_Helpers_Trip_Dates::get_dates( $trip->ID );
		if ( ! is_wp_error( $dates ) && 'WP_TRAVEL_TRIP_DATES' === $dates['code'] ) {
			$trip_data['dates']         = (array) $dates['dates'];
			$trip_data['display_dates'] = wptravel_get_fixed_departure_date( $trip->ID ); // This date is only for display in Blocks component.
		}

		$excluded_dates_times = WP_Travel_Helpers_Trip_Excluded_Dates_Times::get_dates_times( $trip->ID );
		if ( ! is_wp_error( $excluded_dates_times ) && 'WP_TRAVEL_TRIP_EXCLUDED_DATES_TIMES' === $excluded_dates_times['code'] ) {
			$trip_data['excluded_dates_times'] = (array) $excluded_dates_times['dates_times'];
		}

		$trip_meta = get_post_meta( $trip_id );

		// $is_fixed_departure              = ! empty( $trip_meta['wp_travel_fixed_departure'][0] ) && 'yes' === $trip_meta['wp_travel_fixed_departure'][0] ? true : false;
		// $trip_data['is_fixed_departure'] = $is_fixed_departure;
		$trip_data['is_fixed_departure'] = WP_Travel_Helpers_Trip_Dates::is_fixed_departure( $trip_id );

		// Gallery Data.
		$gallery_items_ids = get_post_meta( $trip_id, 'wp_travel_itinerary_gallery_ids', true );
		$gallery_data      = array();
		if ( is_array( $gallery_items_ids ) ) {
			foreach ( $gallery_items_ids as $index => $item_id ) {
				$attachment = wp_get_attachment_image_src( (int) $item_id, 'large' );

				$gallery_data[ $index ]['id']        = $item_id;
				$gallery_data[ $index ]['thumbnail'] = isset( $attachment[0] ) ? $attachment[0] : '';
			}
		} else {
			$gallery_data = json_decode( $gallery_items_ids );
		}
		$trip_data['gallery']       = $gallery_data;
		$trip_data['_thumbnail_id'] = (int) get_post_meta( $trip_id, '_thumbnail_id', true );

		$trip_data = wp_parse_args( $trip_data, $trip_default_data );
		$trip_data = apply_filters( 'wp_travel_trip_data', $trip_data, $trip->ID ); // @phpcs:ignore
		$trip_data = apply_filters( 'wptravel_trip_data', $trip_data, $trip->ID ); // Filters to add custom data.
		return array(
			'code' => 'WP_TRAVEL_TRIP_INFO',
			'trip' => $trip_data,
		);
	}

	/**
	 * Update the trip.
	 *
	 * @param int $trip_id Trip ID.
	 * @param int $trip_data Other data related to that particular trip.
	 */
	public static function update_trip( $trip_id, $trip_data ) {

		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$trip = get_post( $trip_id );

		if ( ! is_object( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip_data = (object) $trip_data;
		if ( ! empty( $trip_data->pricings ) ) {
			WP_Travel_Helpers_Pricings::update_pricings( $trip_id, $trip_data->pricings );
		}

		$highest_price = ! empty( $trip_data->highest_price ) ? 'yes' : '';
		update_post_meta( $trip_id, 'wp_travel_show_highest_price', $highest_price );

		$is_fixed_departure = ! empty( $trip_data->is_fixed_departure ) ? 'yes' : 'no';
		update_post_meta( $trip_id, 'wp_travel_fixed_departure', $is_fixed_departure );
		
		update_post_meta( $trip_id, 'wp_travel_enable_pax_all_pricing', $trip_data->enable_pax_all_pricing );

		$dates = ( 'no' === $is_fixed_departure ) ? array() : $trip_data->dates;
		if ( ! empty( $dates ) ) {
			WP_Travel_Helpers_Trip_Dates::update_dates( $trip_id, $trip_data->dates );
		} else {
			WP_Travel_Helpers_Trip_Dates::remove_dates( $trip_id );
		}

		$excluded_dates_times = empty( $trip_data->excluded_dates_times ) ? array() : $trip_data->excluded_dates_times;
		if ( ! empty( $excluded_dates_times ) ) {
			WP_Travel_Helpers_Trip_Excluded_Dates_Times::update_dates_times( $trip_id, $trip_data->excluded_dates_times );
		} else {
			WP_Travel_Helpers_Trip_Excluded_Dates_Times::remove_dates_times( $trip_id );
		}

		if ( ! empty( $trip_data->use_global_tabs ) ) {
			update_post_meta( $trip_id, 'wp_travel_use_global_tabs', sanitize_text_field( $trip_data->use_global_tabs ) );
		}

		if ( ! empty( $trip_data->trip_tabs ) ) {
			$trip_tabs = array();
			foreach ( $trip_data->trip_tabs as  $trip_tab ) {
				$tab_key                               = $trip_tab['tab_key']; // quick fix.
				$trip_tabs[ $tab_key ]['label']        = $trip_tab['label'];
				$trip_tabs[ $tab_key ]['show_in_menu'] = $trip_tab['show_in_menu'] == 'yes' ? true : ( $trip_tab['show_in_menu'] == 'no' || $trip_tab['show_in_menu'] == false || empty( $trip_tab['show_in_menu'] ) ? false : true );
			}
			update_post_meta( $trip_id, 'wp_travel_tabs', $trip_tabs );

		}

		$itineraries = array();
		if ( ! empty( $trip_data->itineraries ) ) {
			foreach ( $trip_data->itineraries as $itinerary_id => $trip_tab ) {
				$itineraries[ $itinerary_id ]['label'] = $trip_tab['label'];
				$itineraries[ $itinerary_id ]['title'] = $trip_tab['title'];
				$itineraries[ $itinerary_id ]['date']  = $trip_tab['date'];
				$itineraries[ $itinerary_id ]['time']  = $trip_tab['time'];
				$itineraries[ $itinerary_id ]['desc']  = $trip_tab['desc'];
				if ( isset( $trip_tab['image'] ) ) {
					$itineraries[ $itinerary_id ]['image'] = $trip_tab['image'];
				}
			}
		}

		update_post_meta( $trip_id, 'wp_travel_trip_itinerary_data', $itineraries );
		$faqs = array();
		if ( ! empty( $trip_data->faqs ) ) {
			foreach ( $trip_data->faqs as $faq_id => $faq ) {
				$faqs['question'][] = $faq['question'];
				$faqs['answer'][]   = $faq['answer'];
			}
		}
		$question = isset( $faqs['question'] ) ? $faqs['question'] : array();
		$answer   = isset( $faqs['answer'] ) ? $faqs['answer'] : array();
		update_post_meta( $trip_id, 'wp_travel_faq_question', $question );
		update_post_meta( $trip_id, 'wp_travel_faq_answer', $answer );

		// new meta since wp travel 5.2.3
		update_post_meta( $trip_id, 'wptravel_trip_faqs', $trip_data->faqs );

		// trip duration.
		if ( ! empty( $trip_data->trip_duration ) ) {
			$days   = isset( $trip_data->trip_duration['days'] ) ? $trip_data->trip_duration['days'] : 0;
			$nights = isset( $trip_data->trip_duration['nights'] ) ? $trip_data->trip_duration['nights'] : 0;
			$duration_start_date = isset( $trip_data->trip_duration['start_date'] ) ? $trip_data->trip_duration['start_date'] : '';
			$duration_end_date = isset( $trip_data->trip_duration['end_date'] ) ? $trip_data->trip_duration['end_date'] : '';
			update_post_meta( $trip_id, 'wp_travel_trip_duration', $days );
			update_post_meta( $trip_id, 'wp_travel_trip_duration_night', $nights );

			update_post_meta( $trip_id, 'wp_travel_trip_duration_start_date', $duration_start_date );
			update_post_meta( $trip_id, 'wp_travel_trip_duration_end_date', $duration_end_date );
			update_post_meta( $trip_id, 'wp_travel_trip_duration_formating', $trip_data->trip_duration );
		}
		$trip_facts = array();
		if ( ! empty( $trip_data->trip_facts ) ) {
			foreach ( $trip_data->trip_facts as $trip_fact_id => $trip_fact ) {
				$trip_facts[ $trip_fact_id ]['label']   = $trip_fact['label'];
				$trip_facts[ $trip_fact_id ]['value']   = $trip_fact['value'];
				$trip_facts[ $trip_fact_id ]['fact_id'] = $trip_fact['fact_id'];
				$trip_facts[ $trip_fact_id ]['icon']    = $trip_fact['icon'];
				$trip_facts[ $trip_fact_id ]['type']    = $trip_fact['type'];
			}
			$trip_facts = array_filter( array_filter( array_values( $trip_facts ), 'array_filter' ), 'count' );
		}
		update_post_meta( $trip_id, 'wp_travel_trip_facts', $trip_facts );

		if ( ! empty( $trip_data->trip_overview ) || empty( $trip_data->trip_overview ) ) {
			/**
			 * Save trip outline.
			 *
			 * @todo Need escaping in wp_travel_overview
			 */
			update_post_meta( $trip_id, 'wp_travel_overview', wp_kses_post( $trip_data->trip_overview ) );
		}

		if ( ! empty( $trip_data->trip_video_code ) || empty( $trip_data->trip_video_code ) ) {
			/**
			 * Save trip outline.
			 *
			 * @todo Need escaping in wp_travel_overview
			 */
			update_post_meta( $trip_id, 'wp_travel_video_code', sanitize_url( $trip_data->trip_video_code ) );
		}

		if ( ! empty( $trip_data->trip_outline ) || empty( $trip_data->trip_outline ) ) {
			/**
			 * Save trip outline.
			 *
			 * @todo Need escaping in wp_travel_outline
			 */
			update_post_meta( $trip_id, 'wp_travel_outline', wp_kses_post( $trip_data->trip_outline ) );
		}
		if ( ! empty( $trip_data->trip_include ) || empty( $trip_data->trip_include ) ) {
			/**
			 * Save trip includes.
			 *
			 * @todo Need escaping in wp_travel_trip_include
			 */
			update_post_meta( $trip_id, 'wp_travel_trip_include', wp_kses_post( $trip_data->trip_include ) );
		}
		if ( ! empty( $trip_data->trip_exclude ) || empty( $trip_data->trip_exclude ) ) {
			/**
			 * Save trip excludes.
			 *
			 * @todo Need escaping in wp_travel_trip_exclude.
			 */
			update_post_meta( $trip_id, 'wp_travel_trip_exclude', wp_kses_post( $trip_data->trip_exclude ) );
		}

		if ( ! empty( $trip_data->use_global_trip_enquiry_option ) ) {
			update_post_meta( $trip_id, 'wp_travel_use_global_trip_enquiry_option', sanitize_text_field( $trip_data->use_global_trip_enquiry_option ) );
		}
		if ( ! empty( $trip_data->enable_trip_enquiry_option ) ) {
			update_post_meta( $trip_id, 'wp_travel_enable_trip_enquiry_option', sanitize_text_field( $trip_data->enable_trip_enquiry_option ) );
		}
		// Group size post value is not accurate. @todo need to update group size in state on pricing/category changes.
		$trip                = get_post( $trip_id );
		$wp_travel_itinerary = new WP_Travel_Itinerary( $trip );
		$wp_travel_itinerary->update_group_size();

		$minimum_partial_payout_use_global = '';
		if ( ! empty( $trip_data->minimum_partial_payout_use_global ) ) {
			$minimum_partial_payout_use_global = $trip_data->minimum_partial_payout_use_global;
		}
		update_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_use_global', sanitize_text_field( $minimum_partial_payout_use_global ) );

		if ( ! empty( $trip_data->minimum_partial_payout_percent ) ) {
			update_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_percent', $trip_data->minimum_partial_payout_percent );
		}

		// Update trip gallery meta.
		if ( isset( $trip_data->gallery ) ) {
			$data = array();
			if ( $trip_data->gallery ) {
				$data = (array) $trip_data->gallery;
				$data = array_map(
					function( $el ) {
						$el = (object) $el;
						return (int) $el->id;
					},
					$data
				);
			}
			if ( ! empty( $trip_data->_thumbnail_id && ! empty( $data ) ) ) {
				$_thumbnail_id = ( in_array( (int) $trip_data->_thumbnail_id, $data ) || wp_attachment_is( 'image', $trip_data->_thumbnail_id ) ) ? (int) $trip_data->_thumbnail_id : 0;
				foreach ( $data as $datas ) {
					if ( $datas === $_thumbnail_id ) {
						update_post_meta( $trip_id, '_thumbnail_id', $_thumbnail_id );
						break;
					} else {
						update_post_meta( $trip_id, '_thumbnail_id', isset( $data[0] ) ? $data[0] : 0 );
					}
				}
			} else {
				update_post_meta( $trip_id, '_thumbnail_id', isset( $data[0] ) ? $data[0] : 0 );
			}
			update_post_meta( $trip_id, 'wp_travel_itinerary_gallery_ids', wp_unslash( $data ) );
		}

		if ( ! empty( $trip_data->map_data ) ) {
			$data = (array) $trip_data->map_data;
			update_post_meta( $trip_id, 'wp_travel_location', wp_unslash( $data['loc'] ) );
			update_post_meta( $trip_id, 'wp_travel_lat', wp_unslash( $data['lat'] ) );
			update_post_meta( $trip_id, 'wp_travel_lng', wp_unslash( $data['lng'] ) );
			update_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', wp_unslash( $data['use_lat_lng'] ) );
		}

		/**
		 * Update meta with min price for sorting.
		 *
		 * @since 4.0.4
		 */
		$prev_min_price = get_post_meta( $trip_id, 'wp_travel_trip_price', true );
		$args           = array(
			'trip_id' => $trip_id,
		);
		$min_price      = WP_Travel_Helpers_Pricings::get_price( $args );
		update_post_meta( $trip_id, 'wp_travel_trip_price', $min_price, $prev_min_price );

		/**
		 * Update Meta for sale widget.
		 * if trip has enable sale for any one price then the trip is assume as sale enabled trip.
		 */
		$args         = array( 'trip_id' => $trip_id );
		$sale_enabled = self::is_sale_enabled( $args );
		update_post_meta( $trip_id, 'wptravel_enable_sale', $sale_enabled );

		do_action( 'wp_travel_update_trip_data', $trip_data, $trip_id ); // @phpcs:ignore
		do_action( 'wptravel_update_trip_data', $trip_data, $trip_id );
		$trip = self::get_trip( $trip_id );

		self::clear_data(); // Clear required transient

		if ( is_wp_error( $trip ) || 'WP_TRAVEL_TRIP_INFO' !== $trip['code'] ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$settings = wptravel_get_settings();
		if ( isset( $settings['wpml_migrations'] ) && $settings['wpml_migrations'] ) {
			if ( isset( $trip['trip']['pricings'] ) && ! empty( $trip['trip']['pricings'] ) ) {
				update_post_meta( $trip_id, 'wp_travel_trip_price_categorys', $trip['trip']['pricings'] );
			} else {
				update_post_meta( $trip_id, 'wp_travel_trip_price_categorys', array() );
			}
			if ( ! empty( $dates ) && isset( $trip['trip']['dates'] ) && ! empty( $trip['trip']['dates'] ) ) {
				$new_dates = $trip['trip']['dates'];
				foreach ( $trip_data->dates as $keys => $new_date ) {
					$new_dates[$keys]['years'] = isset( $new_date['years'] ) ? $new_date['years'] : '';
					$new_dates[$keys]['months'] = isset( $new_date['months'] ) ? $new_date['months'] : '';
				}
				
				update_post_meta( $trip_id, 'wp_travel_trips_dates', $new_dates );
			} else {
				update_post_meta( $trip_id, 'wp_travel_trips_dates', array() );
			}
		}
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_TRIP',
			array(
				'trip' => $trip['trip'],
			)
		);
	}

	/**
	 * Delete site transient when trip updated.
	 *
	 * @since 5.1.0
	 */
	public static function clear_data() {
		delete_site_transient( 'wptravel_min_max_prices' ); // it consist the price including all trips and filter the min and max price among them.
	}

	/**
	 * Return list of trip as per arguments provided.
	 *
	 * @param array $args Arguments to filter trips.
	 *
	 * @since 3.0.0
	 * @since 5.0.8 Meta query added to filter sale trips & featured trip
	 * @return Array
	 */
	public static function filter_trips( $args = array() ) {

		// $permission = WP_Travel::verify_nonce();

		// if ( ! $permission || is_wp_error( $permission ) ) {
		// WP_Travel_Helpers_REST_API::response( $permission );
		// }

		global $wpdb;

		$post_ids      = array();
		$post_ids_data = self::get_trip_ids( $args );
		if ( is_array( $post_ids_data ) && isset( $post_ids_data['code'] ) && 'WP_TRAVEL_TRIP_IDS' == $post_ids_data['code'] ) {
			$post_ids = $post_ids_data['trip_ids'];
		}
		$query_args = array();
		if ( count( $post_ids ) > 0 ) {
			$query_args['post__in'] = $post_ids;
		}

		// WP Parameters.
		$parameter_mappings = array(
			'exclude'  => 'post__not_in',
			'include'  => 'post__in',
			'offset'   => 'offset',
			'order'    => 'order',
			'orderby'  => 'orderby',
			'page'     => 'paged',
			'slug'     => 'post_name__in',
			'status'   => 'post_status',
			'per_page' => 'posts_per_page',
		);

		/*
		 * For each known parameter which is both registered and present in the request,
		 * set the parameter's value on the query $args.
		 *
		 * We are already checking nonce above using WP_Travel::verify_nonce();
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $_GET[ $api_param ] ) ) {
				$query_args[ $wp_param ] = sanitize_text_field( wp_unslash( $_GET[ $api_param ] ) );
			}
		}
		/**
		 * WP Travel Post-Type.
		 */
		$query_args['post_type'] = WP_TRAVEL_POST_TYPE;

		if ( ! empty( $query_args['post__in'] ) && ! is_array( $query_args['post__in'] ) ) {
			$query_args['post__in'] = implode( ',', $query_args['post__in'] );
		}

		$travel_locations = isset( $args['travel_locations'] ) ? $args['travel_locations'] : '';
		$itinerary_types  = isset( $args['itinerary_types'] ) ? $args['itinerary_types'] : '';
		$activity         = isset( $args['activity'] ) ? $args['activity'] : '';
		$travel_keywords  = isset( $args['travel_keywords'] ) ? $args['travel_keywords'] : '';

		// Tax Query Args.
		if ( ! empty( $travel_locations ) || ! empty( $itinerary_types ) || ! empty( $activity ) || ! empty( $travel_keywords ) ) {

			$query_args['tax_query'] = array();

			if ( ! empty( $travel_locations ) ) {
				// $query_args['tax_query']['relation'] = 'AND';
				$query_args['tax_query'][] = array(
					'taxonomy' => 'travel_locations',
					'field'    => 'slug',
					'terms'    => $travel_locations,
				);
			}
			if ( ! empty( $itinerary_types ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'itinerary_types',
					'field'    => 'slug',
					'terms'    => $itinerary_types,
				);
			}
			if ( ! empty( $activity ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'activity',
					'field'    => 'slug',
					'terms'    => $activity,
				);
			}
			if ( ! empty( $travel_keywords ) ) {
				$query_args['tax_query'][] = array(
					'taxonomy' => 'travel_keywords',
					'field'    => 'slug',
					'terms'    => $travel_keywords,
				);
			}
		}
		// Meta Query args. @since 5.0.8
		$display_sale_trip     = isset( $args['sale_trip'] ) ? $args['sale_trip'] : false;
		$display_featured_trip = isset( $args['featured_trip'] ) ? $args['featured_trip'] : false;
		if ( $display_sale_trip || $display_featured_trip ) {
			$query_args['meta_query'] = array();

			if ( $display_sale_trip ) {
				$query_args['meta_query'][] = array(
					'key'     => 'wptravel_enable_sale',
					'value'   => '1',
					'compare' => '=',
				);
			}
			if ( $display_featured_trip ) {
				$query_args['meta_query'][] = array(
					'key'     => 'wp_travel_featured',
					'value'   => 'yes',
					'compare' => '=',
				);
			}
		}

		if ( isset( $args['numberposts'] ) ) {
			$query_args['posts_per_page'] = $args['numberposts'];
		}

		if ( isset( $args['orderby'] ) ) {
			$order                 = $args['order'] ? $args['order'] : 'desc';
			$query_args['orderby'] = $args['orderby'];
			$query_args['order']   = $order;
		}
		$the_query = new WP_Query( $query_args );
		$trips     = array();
		// The Loop.
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				$trip_info = self::get_trip( get_the_ID() );
				$trips[]   = $trip_info['trip'];
			} // end while
		} // endif

		// Reset Post Data.
		wp_reset_postdata();

		if ( empty( $trips ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
		}
	
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_FILTER_RESULTS',
			array(
				'trip' => $trips,
			)
		);
	}

	/**
	 * Return list of trip IDs as per arguments provided.
	 *
	 * @param array $args Arguments for data to retrive.
	 *
	 * @since 3.0.0
	 * @return Array
	 */
	public static function get_trip_ids( $args = array() ) {
		global $wpdb;

		$date_table = $wpdb->prefix . self::$date_table;

		if ( is_multisite() ) {
			/**
			 * If this is multisite.
			 *
			 * @todo Get Table name on Network Activation.
			 */
			$blog_id       = get_current_blog_id();
			$date_table    = $wpdb->base_prefix . $blog_id . '_' . self::$date_table;
			$pricing_table = $wpdb->base_prefix . $blog_id . '_' . self::$pricing_table;
		}
		// Filter Arguments.
		$start_date = isset( $args['start_date'] ) ? $args['start_date'] : '';
		$end_date   = isset( $args['end_date'] ) ? $args['end_date'] : '';

		$max_pax   = isset( $args['max_pax'] ) ? $args['max_pax'] : '';
		$min_price = isset( $args['min_price'] ) ? $args['min_price'] : '';
		$max_price = isset( $args['max_price'] ) ? $args['max_price'] : '';

		// List all trip ids as per filter arguments.
		$sql = "select distinct DATES.trip_id from {$date_table}";

		// Order By qyery.
		$orderby_sql = ' ORDER BY post_date desc';
		if ( isset( $args['orderby'] ) && $args['orderby'] ) {
			$orderby     = $args['orderby'];
			$order       = isset( $args['order'] ) && $args['order'] ? $args['order'] : 'desc';
			$orderby_sql = " ORDER BY ${orderby} {$order}";
		}

		$year      = '';
		$month     = ''; // 1,2,3 ... 12.
		$date_days = ''; // 1,2,3 ... 28, 29.
		$day       = ''; // Sun, Mon.

		if ( ! empty( $start_date ) ) {

			$year      = gmdate( 'Y', strtotime( $start_date ) );
			$month     = gmdate( 'n', strtotime( $start_date ) ); // 1,2,3 ... 12.
			$date_days = gmdate( 'j', strtotime( $start_date ) ); // 1,2,3 ... 28, 29.
			$day       = gmdate( 'D', strtotime( $start_date ) ); // Sun, Mon.
			$day       = strtoupper( substr( $day, 0, 2 ) ); // SU, MO.
		}

		if ( ! empty( $start_date ) || ! empty( $end_date ) ) {
			$sql .= ' where ';

			if ( ! empty( $start_date ) ) {
				$sql .= "
					(
						( '' = IFNULL(start_date,'') || start_date >= '{$start_date}' )
						OR
						(
							( FIND_IN_SET( '{$year}', years)  || '' = IFNULL(years,'' ) || 'every_year' = years ) AND
							( FIND_IN_SET( '{$month}', months) || '' = IFNULL(months,'' ) || 'every_month' = months )
						)
					)";
			}

			if ( ! empty( $end_date ) ) {
				if ( ! empty( $start_date ) ) {
					$sql .= 'AND  ';
				}
				$sql .= "
					(
						( '' = IFNULL(end_date,'') || end_date <= '{$end_date}' )
					)";
			}
		}
		$sql .= ' DATES'; // table alias.
		$sql .= " JOIN {$wpdb->prefix}wt_pricings PRICINGS on DATES.trip_id=PRICINGS.trip_id";

		// Second query for group size if max_pax param.
		if ( $max_pax && $max_pax > 0 ) {
			$sql .= " and ( PRICINGS.max_pax = 0 or ( {$max_pax} >= PRICINGS.min_pax and {$max_pax} <= PRICINGS.max_pax  ) )";
		}

		// Query 2 for trip duration dates.
		$duration_query = "Select META.post_id as trip_id, TRIPS.post_date, TRIPS.post_title from {$wpdb->prefix}postmeta META join {$wpdb->prefix}posts TRIPS on META.post_id=TRIPS.ID where META.meta_key='wp_travel_fixed_departure' and META.meta_value!= 'yes' and TRIPS.post_status IN ( 'publish' ) {$orderby_sql}";

		// Filter as per min and max price.
		if ( ( $min_price && $min_price > 0 ) || ( $max_price && $max_price > 0 ) ) {
			// Trip ID's From Dates table.
			$results  = $wpdb->get_results( $wpdb->prepare( "select pricing_id, pricing_category_id,regular_price,is_sale,sale_price, trip_id, TRIPS.post_date, TRIPS.post_title from {$wpdb->prefix}wt_price_category_relation PC join {$wpdb->prefix}wt_pricings P on PC.pricing_id=P.id join {$wpdb->prefix}posts TRIPS on P.trip_id = TRIPS.ID where P.trip_id IN(%s) {$orderby_sql}", $sql ) );
			$results2 = $wpdb->get_results( $duration_query );

			if ( empty( $results && empty( $results2 ) ) ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
			}

			$results = array_unique( array_merge( $results, $results2 ) );

			// return form here if min and max price.
			$post_ids = array();
			foreach ( $results as $result ) {
				$price = $result->is_sale && $result->sale_price ? $result->sale_price : $result->regular_price;
				if ( $min_price && $max_price ) {
					if ( $price >= $min_price && $price <= $max_price ) {
						$post_ids[] = $result->trip_id;
					}
				} elseif ( $min_price ) {
					if ( $price >= $min_price ) {
						$post_ids[] = $result->trip_id;
					}
				} else {
					if ( $price <= $max_price ) {
						$post_ids[] = $result->trip_id;
					}
				}
			}
			if ( empty( $post_ids ) ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
			}

			return WP_Travel_Helpers_Response_Codes::get_success_response(
				'WP_TRAVEL_TRIP_IDS',
				array(
					'trip_ids' => $post_ids,
				)
			);

		}
		// SQL for Trip ids from dates table.
		$sql      = "select TRIPS.ID as trip_id, TRIPS.post_date, TRIPS.post_title from {$wpdb->prefix}posts TRIPS where TRIPS.ID IN({$sql}) {$orderby_sql}";
		$results  = $wpdb->get_results( $sql ); // @phpcs:ignore
		$results2 = $wpdb->get_results( $duration_query );

		if ( empty( $results && empty( $results2 ) ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
		}

		$results  = array_merge( $results, $results2 );
		$post_ids = array();
		foreach ( $results as $result ) {
			$post_ids[] = $result->trip_id;
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_IDS',
			array(
				'trip_ids' => array_unique( $post_ids ),
			)
		);
	}

	/**
	 * Return True if sale enable for the trip as per arguments provided.
	 *
	 * @param array $args Arguments.
	 *
	 * @since 4.4.0
	 * @return Boolean
	 */
	public static function is_sale_enabled( $args = array() ) {
		// Extracting arguments.
		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();
		if ( ! $trip_id ) {
			return false;
		}
		$from_price_sale_enable = isset( $args['from_price_sale_enable'] ) ? $args['from_price_sale_enable'] : false; // This will check sale enable for From Price.
		$pricing_id             = isset( $args['pricing_id'] ) ? $args['pricing_id'] : '';
		$category_id            = isset( $args['category_id'] ) ? $args['category_id'] : '';
		$price_key              = isset( $args['price_key'] ) ? $args['price_key'] : '';

		$enable_sale         = false;
		$settings            = wptravel_get_settings();
		$pricing_option_type = wptravel_get_pricing_option_type( $trip_id );
		$pricing_options     = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( 'single-price' === $pricing_option_type ) {
			$enable_sale = get_post_meta( $trip_id, 'wp_travel_enable_sale', true ); // Legacy verson Below WP Travel 4.0.0.
		} elseif ( 'multiple-price' === $pricing_option_type ) {

			$pricings_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id ); // New Pricing option since WP Travel v4.0.0.
			if ( $from_price_sale_enable ) {
				// Get min price to check whether min price has sale enabled of not.
				$args       = array( 'trip_id' => $trip_id );
				$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
			}
			$switch_to_v4 = wptravel_is_react_version_enabled();
			if ( $switch_to_v4 && ! is_wp_error( $pricings_data ) && is_array( $pricings_data ) && isset( $pricings_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricings_data['code'] ) {
				$pricings         = $pricings_data['pricings'];
				$args['pricings'] = $pricings;

				$enable_sale = self::is_sale_enabled_v4( $args );

			} else {
				$enable_sale = self::is_sale_enabled_legacy( $args ); // Enable sale for less than WP Travel 4.0.0.
			}
		}

		$enable_sale = apply_filters( 'wp_travel_enable_sale', $enable_sale, $trip_id, $pricing_options, $price_key ); // @phpcs:ignore.
		return apply_filters( 'wptravel_enable_sale', $enable_sale, $trip_id, $pricing_options, $price_key ); // Filter since WP Travel 2.0.5.

	}

	/**
	 * Note: It is only for Below WP Travel 4.0. Return True if sale enable for the trip as per arguments provided.
	 *
	 * @param array $args Argument.
	 *
	 * @since 4.4.0
	 * @return Array
	 */
	public static function is_sale_enabled_legacy( $args = array() ) {
		// Extracting arguments.
		$trip_id                = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();
		$from_price_sale_enable = isset( $args['from_price_sale_enable'] ) ? $args['from_price_sale_enable'] : false; // This will check sale enable for From Price.
		$pricing_id             = isset( $args['pricing_id'] ) ? $args['pricing_id'] : '';
		$category_id            = isset( $args['category_id'] ) ? $args['category_id'] : '';
		$price_key              = isset( $args['price_key'] ) ? $args['price_key'] : '';

		if ( ! $trip_id ) {
			return false;
		}
		$enable_sale     = false;
		$pricing_options = get_post_meta( $trip_id, 'wp_travel_pricing_options', true );

		if ( $from_price_sale_enable ) {
			// get min price to check whether min price has sale enabled of not.
			$args       = array(
				'trip_id'     => $trip_id,
				'pricing_id'  => $pricing_id,
				'category_id' => $category_id,
				'price_key'   => $price_key,
			);
			$trip_price = WP_Travel_Helpers_Pricings::get_price( $args );
		}
		if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
			if ( ! empty( $pricing_id ) ) {
				$pricing_option = isset( $pricing_options[ $pricing_id ] ) ? $pricing_options[ $pricing_id ] : array();

				if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0.
					if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
						foreach ( $pricing_options as $pricing_key => $option ) {
							if ( isset( $option['enable_sale'] ) && 'yes' === $option['enable_sale'] ) {
								$enable_sale = true;
								break;
							}
						}
					}
				} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
					if ( ! empty( $category_id ) ) {
						$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
					}
				}
			} else {

				foreach ( $pricing_options as $pricing_id => $pricing_option ) {

					if ( ! isset( $pricing_option['categories'] ) ) { // Old Listing upto WP Travel @since 3.0.0.
						if ( $price_key && ! empty( $price_key ) ) { // checks in indivicual pricing key [specific pricing is enabled in trip].
							if ( isset( $pricing_options[ $price_key ]['enable_sale'] ) && 'yes' === $pricing_options[ $price_key ]['enable_sale'] ) {
								$enable_sale = true;
							}
						} else { // Checks as a whole. if any pricing is enabled then return true.
							if ( is_array( $pricing_options ) && count( $pricing_options ) > 0 ) {
								foreach ( $pricing_options as $pricing_key => $option ) {
									if ( isset( $option['enable_sale'] ) && 'yes' === $option['enable_sale'] ) {
										if ( $from_price_sale_enable ) {
											$sale_price = apply_filters( 'wp_travel_price', $option['sale_price'] ); // @phpcs:ignore
											$sale_price = apply_filters( 'wptravel_price', $sale_price );
											if ( $sale_price === $trip_price ) {
												$enable_sale = true;
												break;
											}
										} else {
											$enable_sale = true;
											break;
										}
									}
								}
							}
						}
					} elseif ( is_array( $pricing_option['categories'] ) && count( $pricing_option['categories'] ) > 0 ) {
						if ( ! empty( $category_id ) ) {
							$category_option = isset( $pricing_option['categories'][ $category_id ] ) ? $pricing_option['categories'][ $category_id ] : array();
						} else {
							$min_catetory_id       = '';
							$catetory_id_max_price = '';
							foreach ( $pricing_option['categories'] as $category_option ) {
								$pricing_enable_sale = isset( $category_option['enable_sale'] ) ? $category_option['enable_sale'] : 'no';
								if ( 'yes' === $pricing_enable_sale ) {
									if ( $from_price_sale_enable ) {
										$sale_price = apply_filters( 'wp_travel_price', $category_option['sale_price'] ); // @phpcs:ignore
										$sale_price = apply_filters( 'wptravel_price', $sale_price );

										if ( $sale_price === $trip_price ) {
											$enable_sale = true;
											break;
										}
									} else {
										$enable_sale = true;
										break;
									}
								}
							}
						}
					}
				}
			}
		}
		return $enable_sale;
	}

	/**
	 * Note: It is only for Greater than or equel to WP Travel 4.0. Return True if sale enable for the trip as per arguments provided.
	 *
	 * @param array $args Arguments.
	 *
	 * @since 4.4.0
	 * @return Array
	 */
	public static function is_sale_enabled_v4( $args = array() ) {
		// Extracting arguments.
		$trip_id = isset( $args['trip_id'] ) ? $args['trip_id'] : get_the_ID();

		if ( ! $trip_id ) {
			return false;
		}

		$from_price_sale_enable = isset( $args['from_price_sale_enable'] ) ? $args['from_price_sale_enable'] : false; // This will check sale enable for From Price.
		$pricing_id             = isset( $args['pricing_id'] ) ? $args['pricing_id'] : '';
		$category_id            = isset( $args['category_id'] ) ? $args['category_id'] : '';
		$pricings               = isset( $args['pricings'] ) ? $args['pricings'] : array();

		if ( empty( $pricings ) ) {
			$pricings_data = WP_Travel_Helpers_Pricings::get_pricings( $trip_id );
			if ( ! is_wp_error( $pricings_data ) && is_array( $pricings_data ) && isset( $pricings_data['code'] ) && 'WP_TRAVEL_TRIP_PRICINGS' === $pricings_data['code'] ) {
				$pricings = $pricings_data['pricings'];
			}
		}

		// get min price to check whether min price has sale enabled of not.
		$args       = array( 'trip_id' => $trip_id );
		$trip_price = WP_Travel_Helpers_Pricings::get_price( $args ); // Only usable in case of $from_price_sale_enable.

		$enable_sale = false;

		if ( ! empty( $pricing_id ) ) {
			$pricing_array_key = array_search( $pricing_id, array_column( $pricings, 'id' ) );
			if ( $pricing_array_key && isset( $pricings[ $pricing_array_key ] ) && isset( $pricings[ $pricing_array_key ]['categories'] ) && is_array( $pricings[ $pricing_array_key ]['categories'] ) && count( $pricings[ $pricing_array_key ]['categories'] ) > 0 ) {
				$pricing_categories = $pricings[ $pricing_array_key ]['categories'];
				foreach ( $pricing_categories as $pricing_category ) {
					if ( isset( $pricing_category['is_sale'] ) && $pricing_category['is_sale'] ) {
						$enable_sale = true;
						break;
					}
				}
			}
		} else {
			foreach ( $pricings as $pricing ) {
				if ( isset( $pricing['categories'] ) && count( $pricing['categories'] ) > 0 ) {
					foreach ( $pricing['categories'] as $pricing_category ) {
						if ( isset( $pricing_category['is_sale'] ) && $pricing_category['is_sale'] ) {

							if ( $from_price_sale_enable ) {
								$sale_price = apply_filters( 'wp_travel_price', $pricing_category['sale_price'] ); // @phpcs:ignore
								$sale_price = apply_filters( 'wptravel_price', $sale_price );
								if ( $sale_price === $trip_price ) {
									$enable_sale = true;
									break;
								}
							} else {
								$enable_sale = true;
								break;
							}
						}
					}
				}
			}
		}

		return $enable_sale;

	}

	/**
	 * Return True if tax enable in settings.
	 *
	 * @since 4.4.0
	 * @return Boolean
	 */
	public static function is_tax_enabled() {
		$settings = wptravel_get_settings();
		return isset( $settings['trip_tax_enable'] ) && 'yes' === $settings['trip_tax_enable'];
	}

	/**
	 * Return True Percent if tax is applicable otherwise return false.
	 *
	 * @since 4.4.0
	 * @return Mixed
	 */
	public static function get_tax_rate() {
		$tax_percentage = false;
		if ( self::is_tax_enabled() ) {
			$settings            = wptravel_get_settings();
			$tax_inclusive_price = $settings['trip_tax_price_inclusive'];
			$tax_percentage      = isset( $settings['trip_tax_percentage'] ) ? $settings['trip_tax_percentage'] : '';

			if ( '' == $tax_percentage || 'yes' == $tax_inclusive_price ) {
				$tax_percentage = false;
			}
		}
		return $tax_percentage;
	}

	public static function wp_travel_trip_date_price() {
		$settings = wptravel_get_settings();
		global $wpdb;
		$db_prefix = $wpdb->prefix;
		$date_table = $db_prefix . 'wt_dates';
		$price_table = $db_prefix . 'wt_pricings';
		$price_cat_table = $db_prefix . 'wt_price_category_relation';
		if ( isset( $settings['wpml_migrations'] ) && $settings['wpml_migrations'] ) {
			$post = new WP_Query(
				array( 
					'post_type' => WP_TRAVEL_POST_TYPE,
					'posts_per_page' => -1,	
				)
			);
			while ( $post->have_posts() ) {
				$post->the_post();
				$trip_id = get_the_ID();
				$price_d = $wpdb->get_results( "select * from {$price_table} where trip_id={$trip_id}" );
				$date_w = $wpdb->get_results( "select * from {$date_table} where trip_id={$trip_id}" );
				$new_price_id = array();
				$new_date_id = array();
				$price_cat = get_post_meta( $trip_id, 'wp_travel_trip_price_categorys', true ); 
				$new_pr_id = get_post_meta( $trip_id, 'wp_trivel_new_price_id', true );
				if ( empty( $price_d ) ) {
					if ( ! empty( $price_cat ) ) {
						foreach ( $price_cat as $key => $val ) {
							$result = WpTravel_Helpers_Pricings::add_individual_pricing( $trip_id, $val );
							if ( ! is_wp_error( $result ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $result['code'] && ! empty( $val['categories'] ) ) {
								WP_Travel_Helpers_Trip_Pricing_Categories::update_pricing_categories( $result['pricing_id'], $val['categories'] );
							}
							$new_price_id[] = array( $val['id'] => isset( $result['pricing_id'] ) ? $result['pricing_id'] : 0 );
							update_post_meta( $trip_id, 'wp_travel_fixed_departure', 'yes' );
							
						}
						update_post_meta( $trip_id, 'wp_trivel_new_price_id', $new_price_id );
					}
				} else { 
					if ( ! empty( $new_pr_id ) ) {
						if ( ! empty( $price_cat ) ) {
							foreach ( $price_cat as $keys => $values ) {
								foreach (  $new_pr_id as $key => $value ) {
									foreach ( $value as $old_id => $new_id ) {
										if ( $old_id == $values['id'] ) {
											if ( $new_id > 0 ) {
												$values['id']  = $new_id;
												$values['sort_order'] = $keys + 1 ;
												$result = WpTravel_Helpers_Pricings::update_individual_pricing( $new_id, $values );
												if ( ! is_wp_error( $result ) && 'WP_TRAVEL_UPDATED_TRIP_PRICING' === $result['code'] && ! empty( $values['categories'] ) ) {
													WP_Travel_Helpers_Trip_Pricing_Categories::update_pricing_categories( $new_id, $values['categories'] );
												} elseif ( empty( $values['categories'] ) ) {
													WP_Travel_Helpers_Trip_Pricing_Categories::remove_trip_pricing_categories( $new_id );
												}
												$new_price_id[] = array( $old_id => $new_id );
											}
										} else {
											if ( ! empty( $new_id ) && $new_id > 0 ) {
												WpTravel_Helpers_Pricings::remove_individual_pricing( $new_id );
											}
											if ( ! empty( $values ) ) {
												$result = WpTravel_Helpers_Pricings::add_individual_pricing( $trip_id, $values );
												if ( ! is_wp_error( $result ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $result['code'] && ! empty( $values['categories'] ) ) {
													WP_Travel_Helpers_Trip_Pricing_Categories::update_pricing_categories( $result['pricing_id'], $values['categories'] );
												}
												if ( ! is_wp_error( $result ) && 'WP_TRAVEL_ADDED_TRIP_PRICING' === $result['code'] ) {
													$new_price_id[] = array( $values['id'] => isset( $result['pricing_id'] ) ? $result['pricing_id'] : 0 );
												}
											}
										}
									}
								} 
								update_post_meta( $trip_id, 'wp_trivel_new_price_id', $new_price_id );
							} 
						} else {
							update_post_meta( $trip_id, 'wp_travel_fixed_departure', 'no' );
						}
					}
				}
				$date_departure = get_post_meta( $trip_id, 'wp_travel_trips_dates', true ); 
				if ( empty( $date_w ) ) {
					if ( ! empty( $date_departure ) ) {
						foreach ( $date_departure as $key => $val ) {
							$pricing_ids      = isset( $val['pricing_ids'] ) ? $val['pricing_ids'] : '';
							$price_id_array = ! empty( $pricing_ids ) ? explode( ',', $pricing_ids ) : array();
							$migrate_pr_id_array = array();
							if ( ! empty( $new_price_id ) ) {
								foreach ( $new_price_id as $pri => $pr ) {
									foreach ( $pr as $final => $mr_pr_id ) {
										if ( ! empty( $price_id_array ) ) {
											foreach ( $price_id_array as $index => $pr_id ) {
												if ( $pr_id == $final ) {
													$migrate_pr_id_array[] = $mr_pr_id;
												}
											}
										}
									}
								}
							}
							$migrate_pr_id = ! empty( $migrate_pr_id_array ) ? implode( ',', $migrate_pr_id_array ) : '0';
							$date_id = $val['id'] ? $val['id'] : 0;
							$val['id'] = '';
							$val['pricing_ids'] = $migrate_pr_id ? $migrate_pr_id : 0;
							$res = WpTravel_Helpers_Trip_Dates::add_individual_date( $trip_id, $val ); 
							$new_date_id[] = array( $date_id => isset( $res['date'] ) && isset( $res['date']['ids'] ) ? $res['date']['ids'] : 0 );
						}
						update_post_meta( $trip_id, 'wp_travel_new_date_id', $new_date_id );
					}
				} else { 
					$date_ids = get_post_meta( $trip_id, 'wp_travel_new_date_id', true );
					$new_date_ids = array();
					if ( ! empty( $date_ids ) ) {
						if ( ! empty( $date_departure ) ) {
							foreach ( $date_departure as $index => $data ) {
								foreach ( $date_ids as $indx => $val ) {
									foreach ( $val as $old_id => $new_id ) {
										$ids = isset( $data['id'] ) ? $data['id'] : 0;
										if ( $old_id == $ids ) {
											if ( $new_id > 0 ) {
												$data['id'] = $new_id;
												$pricing_ids      = isset( $data['pricing_ids'] ) ? $data['pricing_ids'] : '';
												$price_id_array = ! empty( $pricing_ids ) ? explode( ',', $pricing_ids ) : array();
												$migrate_pr_id_array = array();
												if ( ! empty( $new_price_id ) ) {
													foreach ( $new_price_id as $pri => $pr ) {
														foreach ( $pr as $final => $mr_pr_id ) {
															if ( ! empty( $price_id_array ) ) {
																foreach ( $price_id_array as $index => $pr_id ) {
																	if ( $pr_id == $final ) {
																		$migrate_pr_id_array[] = $mr_pr_id;
																	}
																}
															}
														}
													}
												}
												$migrate_pr_id = ! empty( $migrate_pr_id_array ) ? implode( ',', $migrate_pr_id_array ) : '0';
												$data['pricing_ids'] = $migrate_pr_id;
												$res = WpTravel_Helpers_Trip_Dates::add_individual_date( $trip_id, $data ); 
												$new_date_ids[] = array( $old_id =>  $new_id );
											}
										} else {
											if ( $new_id > 0 ) {
												WpTravel_Helpers_Trip_Dates::remove_dates( $new_id );
											}
										}
									}
								}
							}
							update_post_meta( $trip_id, 'wp_travel_new_date_id', $new_date_ids );
						}
					}
				}
			}
			register_taxonomy( 'wp_travel_custom_filters', apply_filters( 'wp_travel_itinerary_filters', array( 'itinerary-booking' ) ), array( 'show_in_menu' => false , 'label' => 'Custom Filter') );
		} else {
			$posts = new WP_Query(
				array( 
					'post_type' => WP_TRAVEL_POST_TYPE,
					'posts_per_page' => -1,	
				)
			);
			while ( $posts->have_posts() ) {
				$posts->the_post();
				$trip_id = get_the_ID();
				update_post_meta( $trip_id, 'wp_travel_trip_price_categorys', array() );
			}
		}
	}
}
