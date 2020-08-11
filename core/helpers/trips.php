<?php
class WP_Travel_Helpers_Trips {
	private static $date_table    = 'wt_dates';
	private static $pricing_table = 'wt_pricings';
	private static $price_category_table = 'wt_price_category_relation';
	public static function get_trip( $trip_id = false ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( $trip_id );

		if ( ! is_object( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		$settings = wp_travel_get_settings();

		$extras            = WP_Travel_Helpers_Trip_Extras::get_trip_extras();
		$has_extras        = is_array( $extras ) && isset( $extras['code'] ) && 'WP_TRAVEL_TRIP_EXTRAS' == $extras['code'] && isset( $extras['trip_extras'] ) && count( $extras['trip_extras'] ) > 0 ? true : false;
		$trip_default_data = array(
			'pricing_type'                        => 'multiple-price',
			'custom_booking_type'                 => 'custom-link',
			'custom_booking_form'                 => '',
			'custom_booking_link'                 => '',
			'custom_booking_link_text'            => '',
			'custom_booking_link_open_in_new_tab' => '',
			'pricings'                            => array(),
			'featured_image_data'                 => false,
			'has_extras'                          => $has_extras,
		);

		$enable_custom_itinerary_tabs = apply_filters( 'wp_travel_custom_itinerary_tabs', false );
		$use_global_tabs              = get_post_meta( $trip_id, 'wp_travel_use_global_tabs', true );

		$default_tabs = wp_travel_get_default_trip_tabs();
		$trip_tabs    = wp_travel_get_admin_trip_tabs( $trip_id, $enable_custom_itinerary_tabs ); // quick fix.
		if ( $enable_custom_itinerary_tabs ) { // If utilities is activated.
			$custom_tabs = get_post_meta( $trip_id, 'wp_travel_itinerary_custom_tab_cnt_', true );
			$custom_tabs = ( $custom_tabs ) ? $custom_tabs : array();

			$default_tabs = array_merge( $default_tabs, $custom_tabs ); // To get Default label of custom tab.
		}

		$i    = 0;
		$tabs = array();
		foreach ( $trip_tabs as $key => $tab ) :
			$default_label               = isset( $default_tabs[ $key ]['label'] ) ? $default_tabs[ $key ]['label'] : $tab['label'];
			$tabs[ $i ]['default_label'] = $default_label;
			$tabs[ $i ]['label']         = $tab['label'];
			$tabs[ $i ]['show_in_menu']  = $tab['show_in_menu'];
			$tabs[ $i ]['tab_key']       = $key; // Key is required to save meta value. @todo: can remove this key latter.
			$i++;
		endforeach;

		$trip_facts = get_post_meta( $trip_id, 'wp_travel_trip_facts', true );
		if ( is_string( $trip_facts ) ) {
			$trip_facts = json_decode( $trip_facts, true );
		}

		$use_global_trip_enquiry_option = get_post_meta( $trip_id, 'wp_travel_use_global_trip_enquiry_option', true );
		if ( '' === $use_global_trip_enquiry_option ) {
			$use_global_trip_enquiry_option = 'yes';
		}
		$enable_trip_enquiry_option = get_post_meta( $trip_id, 'wp_travel_enable_trip_enquiry_option', true );

		$trip_include = get_post_meta( $trip_id, 'wp_travel_trip_include', true );
		$trip_exclude = get_post_meta( $trip_id, 'wp_travel_trip_exclude', true );
		$trip_outline = get_post_meta( $trip_id, 'wp_travel_outline', true );
		$itineraries  = get_post_meta( $trip_id, 'wp_travel_trip_itinerary_data', true );
		$faqs         = wp_travel_get_faqs( $trip_id );
		$map_data     = get_wp_travel_map_data( $trip_id );
		// TODO : Include following map_data inside `get_wp_travel_map_data` function.
		$zoomlevel             = ! empty( get_post_meta( $trip_id, 'wp_travel_zoomlevel', true ) ) ? absint( get_post_meta( $trip_id, 'wp_travel_zoomlevel', true ) ) : 10;
		$iframe_height         = ! empty( get_post_meta( $trip_id, 'wp_travel_map_iframe_height', true ) ) ? absint( get_post_meta( $trip_id, 'wp_travel_map_iframe_height', true ) ) : 400;
		$use_lat_lng           = ! empty( get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true ) ) ? get_post_meta( $trip_id, 'wp_travel_trip_map_use_lat_lng', true ) : 'no';
		$map_data['zoomlevel'] = apply_filters( 'wp_travel_trip_zoomlevel', $zoomlevel, $trip_id );
		// $map_data['iframe_height'] = apply_filters( 'wp_travel_trip_map_iframe_height', $iframe_height, $trip_id );
		$map_data['use_lat_lng'] = apply_filters( 'wp_travel_trip_map_use_lat_lng', $use_lat_lng, $trip_id );

		$trip_facts = get_post_meta( $trip_id, 'wp_travel_trip_facts', true );
		$group_size = get_post_meta( $trip_id, 'wp_travel_group_size', true );

		$minimum_partial_payout_use_global = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_use_global', true );
		$minimum_partial_payout_percent    = get_post_meta( $trip_id, 'wp_travel_minimum_partial_payout_percent', true );
		if ( ! $minimum_partial_payout_percent ) {
			$minimum_partial_payout_percent = $settings['minimum_partial_payout'];
		}

		$days  = get_post_meta( $trip_id, 'wp_travel_trip_duration', true );
		$night = get_post_meta( $trip_id, 'wp_travel_trip_duration_night', true );

		$trip_duration = array(
			'days'   => $days,
			'nights' => $night,
		);
		$trip_data     = array(
			'id'                                => $trip->ID,
			'title'                             => $trip->post_title,
			'url'                               => get_permalink( $trip->ID ),
			'trip_code'                         => wp_travel_get_trip_code( $trip->ID ),
			'use_global_tabs'                   => $use_global_tabs,
			'trip_tabs'                         => $tabs,
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
		}

		$dates = WP_Travel_Helpers_Trip_Dates::get_dates( $trip->ID );
		if ( ! is_wp_error( $dates ) && 'WP_TRAVEL_TRIP_DATES' === $dates['code'] ) {
			$trip_data['dates'] = (array) $dates['dates'];
		}

		$excluded_dates_times = WP_Travel_Helpers_Trip_Excluded_Dates_Times::get_dates_times( $trip->ID );
		if ( ! is_wp_error( $excluded_dates_times ) && 'WP_TRAVEL_TRIP_EXCLUDED_DATES_TIMES' === $excluded_dates_times['code'] ) {
			$trip_data['excluded_dates_times'] = (array) $excluded_dates_times['dates_times'];
		}

		$trip_meta = get_post_meta( $trip_id );

		$is_fixed_departure              = ! empty( $trip_meta['wp_travel_fixed_departure'][0] ) && 'yes' === $trip_meta['wp_travel_fixed_departure'][0] ? true : false;
		$trip_data['is_fixed_departure'] = $is_fixed_departure;

		// Gallery Data.
		$gallery_items_ids = get_post_meta( $trip_id, 'wp_travel_itinerary_gallery_ids', true ); // isset( $trip_meta['wp_travel_itinerary_gallery_ids'] ) ? maybe_unserialize( $trip_meta['wp_travel_itinerary_gallery_ids'] ) : [];
		$gallery_data      = array();
		if ( is_array( $gallery_items_ids ) ) {
			foreach ( $gallery_items_ids as $index => $item_id ) {
				$attachment = wp_get_attachment_image_src( (int) $item_id, 'large' );

				$gallery_data[ $index ]['id']        = $item_id;
				$gallery_data[ $index ]['thumbnail'] = isset( $attachment[0] ) ? $attachment[0] : '';
			}
		}
		$trip_data['gallery']       = $gallery_data;
		$trip_data['_thumbnail_id'] = (int) get_post_meta( $trip_id, '_thumbnail_id', true );

		$trip_data = wp_parse_args( $trip_data, $trip_default_data );
		$trip_data = apply_filters( 'wp_travel_trip_data', $trip_data, $trip->ID ); // Filters to add custom data.

		return array(
			'code' => 'WP_TRAVEL_TRIP_INFO',
			'trip' => $trip_data,
		);
	}

	public static function update_trip( $trip_id, $trip_data ) {
		if ( empty( $trip_id ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		$trip = get_post( $trip_id );

		if ( ! is_object( $trip ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}
		// if ( $trip->post_status == 'auto-draft' ) {
		// wp_transition_post_status( 'publish', $trip->post_status, $trip );
		// }
		$trip_data = (object) $trip_data;
		if ( ! empty( $trip_data->pricings ) ) {
			WP_Travel_Helpers_Pricings::update_pricings( $trip_id, $trip_data->pricings );
		}

		$is_fixed_departure = ! empty( $trip_data->is_fixed_departure ) ? 'yes' : 'no';
		update_post_meta( $trip_id, 'wp_travel_fixed_departure', $is_fixed_departure );

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
				$trip_tabs[ $tab_key ]['show_in_menu'] = $trip_tab['show_in_menu'];
			}
			update_post_meta( $trip_id, 'wp_travel_tabs', $trip_tabs );
			// if ( ! empty( $trip_tabs ) && is_array( $trip_tabs ) ) {
			// }
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

		// trip duration.
		if ( ! empty( $trip_data->trip_duration ) ) {
			$days   = isset( $trip_data->trip_duration['days'] ) ? $trip_data->trip_duration['days'] : 0;
			$nights = isset( $trip_data->trip_duration['nights'] ) ? $trip_data->trip_duration['nights'] : 0;
			update_post_meta( $trip_id, 'wp_travel_trip_duration', $days );
			update_post_meta( $trip_id, 'wp_travel_trip_duration_night', $nights );
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

		if ( ! empty( $trip_data->trip_outline ) ) {
			/**
			 * @todo Need escaping in wp_travel_outline
			 */
			update_post_meta( $trip_id, 'wp_travel_outline', wp_kses_post( $trip_data->trip_outline ) );
		}
		if ( ! empty( $trip_data->trip_include ) ) {
			/**
			 * @todo Need escaping in wp_travel_trip_include
			 */
			update_post_meta( $trip_id, 'wp_travel_trip_include', wp_kses_post( $trip_data->trip_include ) );
		}
		if ( ! empty( $trip_data->trip_exclude ) ) {
			/**
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
		if ( ! empty( $trip_data->group_size ) ) {
			update_post_meta( $trip_id, 'wp_travel_group_size', sanitize_text_field( $trip_data->group_size ) );
		}

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
			$data = (array) $trip_data->gallery;
			$data = array_map(
				function( $el ) {
					$el = (object) $el;
					return (int) $el->id;
				},
				$data
			);
			if ( ! empty( $trip_data->_thumbnail_id ) ) {
				$_thumbnail_id = in_array( (int) $trip_data->_thumbnail_id, $data ) ? (int) $trip_data->_thumbnail_id : 0;
				update_post_meta( $trip_id, '_thumbnail_id', $_thumbnail_id );
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
			// update_post_meta( $trip_id, 'wp_travel_zoomlevel', wp_unslash( $data['zoomlevel'] ) );
			// update_post_meta( $trip_id, 'wp_travel_map_iframe_height', wp_unslash( $data['iframe_height'] ) );
		}

		/**
		 * Update meta with min price for sorting.
		 * 
		 * @since 4.0.4
		 */
		$prev_min_price = get_post_meta( $trip_id, 'wp_travel_trip_price', true );
		$min_price      = wp_travel_get_price( $trip_id );
		update_post_meta( $trip_id, 'wp_travel_trip_price', $min_price, $prev_min_price );

		do_action( 'wp_travel_update_trip_data', $trip_data, $trip_id );
		$trip = self::get_trip( $trip_id );

		if ( is_wp_error( $trip ) || 'WP_TRAVEL_TRIP_INFO' !== $trip['code'] ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIP_ID' );
		}

		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_UPDATED_TRIP',
			array(
				'trip' => $trip['trip'],
			)
		);
	}

	public static function filter_trips( $args = array() ) {

		global $wpdb;
		
		$post_ids = array();
		$post_ids_data = self::get_trip_ids( $args );
		if ( isset( $post_ids_data['code'] ) && 'WP_TRAVEL_TRIP_IDS' == $post_ids_data['code'] ) {
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
		 */
		foreach ( $parameter_mappings as $api_param => $wp_param ) {
			if ( isset( $_GET[ $api_param ] ) ) {
				$query_args[ $wp_param ] = $_GET[ $api_param ];
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

		// Tax Query Args.
		if ( ! empty( $travel_locations ) || ! empty( $itinerary_types ) ) {

			$query_args['tax_query'] = array();

			if ( ! empty( $travel_locations ) ) {
				$query_args['tax_query']['relation'] = 'AND';
				$query_args['tax_query']             = array(
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

	public static function get_trip_ids( $args = array() ) {
		global $wpdb;

		$date_table    = $wpdb->prefix . self::$date_table;
		$pricing_table = $wpdb->prefix . self::$pricing_table;
		$price_category_table = $wpdb->prefix . self::$price_category_table;

		if ( is_multisite() ) {
			/**
			 * @todo Get Table name on Network Activation.
			 */
			$blog_id       = get_current_blog_id();
			$date_table    = $wpdb->base_prefix . $blog_id . '_' . self::$date_table;
			$pricing_table = $wpdb->base_prefix . $blog_id . '_' . self::$pricing_table;
		}
		// Filter Arguments.
		$start_date       = isset( $args['start_date'] ) ? $args['start_date'] : '';
		$end_date         = isset( $args['end_date'] ) ? $args['end_date'] : '';

		$max_pax          = isset( $args['max_pax'] ) ? $args['max_pax'] : '';
		$min_price        = isset( $args['min_price'] ) ? $args['min_price'] : '';
		$max_price        = isset( $args['max_price'] ) ? $args['max_price'] : '';

		// List all trip ids as per filter arguments.
		$sql = "select trip_id from {$date_table}";

		$year      = '';
		$month     = ''; // 1,2,3 ... 12.
		$date_days = ''; // 1,2,3 ... 28, 29.
		$day       = ''; // Sun, Mon.

		if ( ! empty( $start_date ) ) {

			$year      = date( 'Y', strtotime( $start_date ) );
			$month     = date( 'n', strtotime( $start_date ) ); // 1,2,3 ... 12.
			$date_days = date( 'j', strtotime( $start_date ) ); // 1,2,3 ... 28, 29.
			$day       = date( 'D', strtotime( $start_date ) ); // Sun, Mon.
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
		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
		}


		// Get Distinct post ids.
		$post_ids = array();
		foreach ( $results as $result ) {
			$post_ids[] = $result->trip_id;
		}
		$sql     = "select distinct trip_id from {$pricing_table} where trip_id IN(" . implode( ',', $post_ids ) . ") ";

		// Second query for group size if max_pax param.
		if ( $max_pax && $max_pax > 0 ) {
			$sql .= " and max_pax >= {$max_pax}";
		}
		$results = $wpdb->get_results( $sql );

		if ( empty( $results ) ) {
			return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
		}
		
		$post_ids = array();
		foreach ( $results as $result ) {
			$post_ids[] = $result->trip_id;
		}
		
		// Filter as per min and max price @todo:need query enhancement.
		if ( ( $min_price && $min_price > 0 ) || ( $max_price && $max_price > 0 ) ) {
			$sql     = "select pricing_id, pricing_category_id,regular_price,is_sale,sale_price, trip_id from {$price_category_table} PC join {$pricing_table} P on PC.pricing_id=P.id  where P.trip_id IN(" . implode( ',', $post_ids ) . ") ";
			$results = $wpdb->get_results( $sql );
	
			if ( empty( $results ) ) {
				return WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_NO_TRIPS' );
			}

			// return form here if min and max price.
			$post_ids = array();
			foreach ( $results as $result ) {
				$price = $result->is_sale && $result->sale_price ? $result->sale_price : $result->regular_price;
				if ( $min_price && $max_price ) {
					if ( $price >= $min_price && $price <= $max_price ) {
						$post_ids[] = $result->trip_id;
					}
				} elseif( $min_price ) {
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

		
		$post_ids = array();
		foreach ( $results as $result ) {
			$post_ids[] = $result->trip_id;
		}

// return $post_ids;
		return WP_Travel_Helpers_Response_Codes::get_success_response(
			'WP_TRAVEL_TRIP_IDS',
			array(
				'trip_ids' => $post_ids,
			)
		);
	}
}
