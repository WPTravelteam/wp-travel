<?php

Class WP_Travel_API_Itinerary {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_itinerary_api_end_points' ) );

	}

	public function wp_travel_itinerary_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-itinerary',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_itinerary' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-itinerary/(?P<id>\w+)',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_itinerary' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/update-itinerary/(?P<id>\w+)',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_update_itinerary' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-itinerary/(?P<id>\w+)',
			array(
				'methods'             => 'get',
				'callback'            => array( $this, 'wp_travel_get_itinerary' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-all-itinerary',
			array(
				'methods'             => 'get',
				'callback'            => array( $this, 'wp_travel_get_all_itinerary' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	public function wp_travel_add_itinerary( WP_REST_Request $request ){
		$get_data = $request->get_body_params();

		$my_post = array(
		  'post_type'    => "itineraries",
		  'post_title'    => !empty( $get_data['trip_title'] ) ? $get_data['trip_title'] : __( 'WP Travel Trip', 'wp-travel' ),
		  'post_status'   => 'publish',
		  'post_excerpt'  => !empty( $get_data['trip_excerpt'] ) ? $get_data['trip_excerpt'] : '',
		  'post_author'   => 1,
		  'meta_input'    => array(
		  	'wp_travel_overview' => !empty( $get_data['trip_overview'] ) ? $get_data['trip_overview'] : '',

		  	'wp_travel_outline'		=> !empty( $get_data['trip_outline'] ) ? $get_data['trip_outline'] : '',

		  	'wp_travel_fixed_departure'	=> $get_data['trip_enable_fixed_departure'],

		  	'wp_travel_trip_duration'	=> !empty( $get_data['trip_duration'] ) ? $get_data['trip_duration'] : '',

		  	'wp_travel_trip_duration_night'	=> !empty( $get_data['trip_duration_night'] ) ? $get_data['trip_duration_night'] : '',

		  	'wp_travel_trip_include'	=> !empty( $get_data['trip_include'] ) ? $get_data['trip_include'] : '',

		  	'wp_travel_trip_exclude'	=> !empty( $get_data['trip_exclude'] ) ? $get_data['trip_exclude'] : '',

		  	'wp_travel_itinerary_gallery_ids'	=> !empty( $get_data['trip_gallery'] ) ? $get_data['trip_gallery'] : '',

		  	'_thumbnail_id'	=> !empty( $get_data['trip_featured_image'] ) ? $get_data['trip_featured_image'] : '',

		  	'wp_travel_trip_map_use_lat_lng' => $get_data['trip_enable_location_lan_lat'],

		  	'wp_travel_location' => !empty( $get_data['trip_location'] ) ? $get_data['trip_location'] : '',

		  	'wp_travel_lat' => !empty( $get_data['trip_lat'] ) ? $get_data['trip_lat'] : '',

		  	'wp_travel_lng' => !empty( $get_data['trip_lng'] ) ? $get_data['trip_lng'] : '',

		  	'wp_travel_trip_itinerary_data' => !empty( $get_data['trip_itinerary'] ) ? $get_data['trip_itinerary'] : '',

		  	'wptravel_trip_faqs' => !empty( $get_data['trip_faqs'] ) ? $get_data['trip_faqs'] : '',

		  	'wp_travel_use_global_tabs' => $get_data['trip_enable_global_tabs'],

		  	'wp_travel_tabs'	 		=> array(
		  		'overview' => array(
		  			'label' => !empty( $get_data['trip_tab_overview_label'] ) ? $get_data['trip_tab_overview_label'] : 'overview',
		  			'show_in_menu' => !empty( $get_data['trip_tab_overview_enable'] ) ? $get_data['trip_tab_overview_enable'] : 1,
		  		),
		  		'trip_outline' => array(
		  			'label' => !empty( $get_data['trip_tab_trip_outline_label'] ) ? $get_data['trip_tab_trip_outline_label'] : 'Trip Outline',
		  			'show_in_menu' => !empty( $get_data['trip_tab_trip_outline_enable'] ) ? ( $get_data['trip_tab_trip_outline_enable'] == 'true' ? true : false  ): true,
		  		),

		  		'trip_includes' => array(
		  			'label' => !empty( $get_data['trip_tab_trip_includes_label'] ) ? $get_data['trip_tab_trip_includes_label'] : 'Trip Includes',
		  			'show_in_menu' => !empty( $get_data['trip_tab_trip_includes_enable'] ) ? ( $get_data['trip_tab_trip_includes_enable'] == 'true' ? true : false  ) : true,
		  		),

		  		'trip_excludes' => array(
		  			'label' => !empty( $get_data['trip_tab_trip_excludes_label'] ) ? $get_data['trip_tab_trip_excludes_label'] : 'Trip Excludes',
		  			'show_in_menu' => !empty( $get_data['trip_tab_trip_excludes_enable'] ) ? ( $get_data['trip_tab_trip_excludes_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'gallery' => array(
		  			'label' => !empty( $get_data['trip_tab_gallery_label'] ) ? $get_data['trip_tab_gallery_label'] : 'Gallery',
		  			'show_in_menu' => !empty( $get_data['trip_tab_gallery_enable'] ) ? ( $get_data['trip_tab_gallery_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'reviews' => array(
		  			'label' => !empty( $get_data['trip_tab_reviews_label'] ) ? $get_data['trip_tab_reviews_label'] : 'Reviews',
		  			'show_in_menu' => !empty( $get_data['trip_tab_reviews_enable'] ) ? ( $get_data['trip_tab_reviews_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'booking' => array(
		  			'label' => !empty( $get_data['trip_tab_booking_label'] ) ? $get_data['trip_tab_booking_label'] : 'Booking',
		  			'show_in_menu' => !empty( $get_data['trip_tab_booking_enable'] ) ? ( $get_data['trip_tab_booking_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'faq' => array(
		  			'label' => !empty( $get_data['trip_tab_faq_label'] ) ? $get_data['trip_tab_faq_label'] : 'FAQ',
		  			'show_in_menu' => !empty( $get_data['trip_tab_faq_enable'] ) ? ( $get_data['trip_tab_faq_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'downloads' => array(
		  			'label' => !empty( $get_data['trip_tab_downloads_label'] ) ? $get_data['trip_tab_downloads_label'] : 'Downloads',
		  			'show_in_menu' => !empty( $get_data['trip_tab_downloads_enable'] ) ? ( $get_data['trip_tab_downloads_enable'] == 'true' ? true : false  ) : ture,
		  		),

		  		'guide' => array(
		  			'label' => !empty( $get_data['trip_tab_guide_label'] ) ? $get_data['trip_tab_guide_label'] : 'Guide',
		  			'show_in_menu' => !empty( $get_data['trip_tab_guide_enable'] ) ? ( $get_data['trip_tab_guide_enable'] == 'true' ? true : false  ) : ture,
		  		),


		  	),

		  	'wp_travel_trip_checkout_page_info_label'	=> !empty( $get_data['trip_checkout_page_info_label'] ) ? $get_data['trip_checkout_page_info_label'] : '',

		  	'wp_travel_trip_checkout_page_info'	=>!empty( $get_data['trip_checkout_page_info'] ) ? $get_data['trip_checkout_page_info'] : '',


		  	'enable_trip_inventory' => $get_data['enable_trip_inventory'],

		  	'wp_travel_inventory_pax_limit_type' => !empty( $get_data['trip_inventory_pax_limit_type'] ) ? $get_data['trip_inventory_pax_limit_type'] : 'use_group_size',

		  	'wp_travel_inventory_sold_out_action' => !empty( $get_data['trip_inventory_sold_out_action'] ) ? $get_data['trip_inventory_sold_out_action'] : 'show_sold_out_msg_only',

		  	'wp_travel_inventory_size' => !empty( $get_data['trip_inventory_size'] ) ? $get_data['trip_inventory_size'] : '',

		  	'wp_travel_inventory_sold_out_message' => !empty( $get_data['trip_inventory_sold_out_message'] ) ? $get_data['trip_inventory_sold_out_message'] : '',

		  	'inventory_counts_paid_only' => !empty( $get_data['inventory_counts_paid_only'] ) ? $get_data['inventory_counts_paid_only'] : 'yes',

		  	'wp_travel_downloads' => !empty( $get_data['trip_downloads'] ) ? $get_data['trip_downloads'] : '',

		  	'wp_travel_use_global_trip_enquiry_option' => !empty( $get_data['trip_enable_global_enquiry'] ) ? $get_data['trip_enable_global_enquiry'] : 'yes',

		  	'wp_travel_enable_trip_enquiry_option' => !empty( $get_data['trip_enable_enquiry'] ) ? $get_data['trip_enable_enquiry'] : 'no',

		  	'wp_travel_minimum_partial_payout_use_global' => !empty( $get_data['trip_partial_payout'] ) ? $get_data['trip_partial_payout'] : 'no',

		  	'wp_travel_minimum_partial_payout_percent' => !empty( $get_data['minimum_partial_payout_percent'] ) ? $get_data['minimum_partial_payout_percent'] : '',
		  ),
		);

		// Insert the post into the database
		$new_id = wp_insert_post( $my_post );

		if ( $new_id ) {

			global $wpdb;

			if ( !empty( $get_data['trip_tax_activity'] ) ) {
				wp_set_object_terms($new_id, array_map( 'intval', $get_data['trip_tax_activity'] ), 'activity');
			}

			if ( !empty( $get_data['trip_tax_type'] ) ) {
				wp_set_object_terms($new_id, array_map( 'intval', $get_data['trip_tax_type'] ), 'itinerary_types');
			}


			if ( !empty( $get_data['trip_tax_location'] ) ) {
				wp_set_object_terms($new_id, array_map( 'intval', $get_data['trip_tax_location'] ), 'travel_locations');
			}

			$price_departure = array();
			foreach ( $get_data['trip_pricing'] as $value ) {
				$pricing_table = $wpdb->prefix.'wt_pricings';
				$pricing_data = array(
					'id' 		=> NULL, 
					'title' 	=> $value['trip_pricing_name'],
					'trip_id' 	=> $new_id,
					'min_pax' 	=> $value['trip_min_pax'],
					'max_pax' 	=> $value['trip_max_pax'],
					'has_group_price' 	=> 0,
					'group_prices' 	=> array(),
					'trip_extras' 	=> '',
					'dates' 	=> '',
					'sort_order' 	=> 1,
				);

				$wpdb->insert($pricing_table,$pricing_data);

				$new_price_id = $wpdb->insert_id;

				$price_category_table = $wpdb->prefix.'wt_price_category_relation';
				$price_category_data = array(
					'id' 		=> NULL, 
					'pricing_id' 	=> $new_price_id,
					'pricing_category_id' 	=> $value['trip_pricing_category_id'],
					'price_per' 	=> $value['trip_price_per'],
					'regular_price' 	=> $value['trip_regular_price'],
					'is_sale' 	=> $value['trip_is_sale'],
					'sale_price' 	=> $value['trip_sale_price'],
					'has_group_price' 	=> '',
					'group_prices' 	=> '',
					'default_pax' 	=> $value['trip_default_pax'],
				);

			 	$wpdb->insert($price_category_table,$price_category_data);

			 	$price_departure[$value['trip_price_key']] = $new_price_id;


			}
			 
			$departure_date = $get_data['trip_departure'];
			foreach ($departure_date as $key => $date ) {

				$assign_price = $date['trip_price_key'];

				$pricing_ids = '';

				$i=1;

				foreach ( $assign_price as $value ) {

					if ( $i == 1 ) {
						$pricing_ids .= $price_departure[$value];
					}else{
						$pricing_ids .= ','.$price_departure[$value];
					}

					$i++;

				}				
					
				$date_table = $wpdb->prefix.'wt_dates';

				$date_data = array(  
					'id' => NULL,
					'trip_id' => $new_id,
					'title' => $date['label'],
					'recurring' => '',
					'years' => '',
					'months' => '',
					'weeks' => '',
					'days' => '',
					'date_days' => '',
					'start_date' => $date['start_date'],
					'end_date' => $date['end_date'],
					'trip_time' => '',
					'pricing_ids' => $pricing_ids,

				);

				$wpdb->insert($date_table,$date_data);

			}


			return "Trip Added Sucessfully";
		}else{
			return "Failed to add Trip";
		}

	}

	public function wp_travel_delete_itinerary ( WP_REST_Request $request ){

		global $wpdb;

		if ( wp_delete_post( $request->get_param( 'id' ) ) ) {

			$pricing_table = $wpdb->prefix.'wt_pricings';
			$pricing_table_trip_id = 'id';

			$prepared_statement = $wpdb->prepare( "SELECT {$pricing_table_trip_id} FROM {$pricing_table} WHERE  trip_id = %d", $request->get_param( 'id' ) );
			$price_ids = $wpdb->get_col( $prepared_statement );


			$wpdb->delete($pricing_table, array( 'trip_id' => $request->get_param( 'id' ) ) );

			$price_category_table = $wpdb->prefix.'wt_price_category_relation';
			

			foreach ( $price_ids as $value ) {
				$wpdb->delete($price_category_table, array( 'pricing_id' => $value ) );
			}

			$date_table = $wpdb->prefix.'wt_dates';
			$wpdb->delete($pricing_table, array( 'trip_id' => $request->get_param( 'id' ) ) );

			return "Trip Deleted Sucessfully";
		}else{
			return "Failed to delete trip. Trip id not exist";
		}

	}

	public function wp_travel_update_itinerary( WP_REST_Request $request ){
		$get_data = $request->get_body_params();

		// Create post object
		$my_post = array(
		  	'ID' => $request->get_param( 'id' ),
		  	'post_type'    => "itineraries",
			'post_status'   => 'publish',

		);

		if( !empty( $get_data['trip_title'] ) ){
			$my_post['post_title'] = $get_data['trip_title'];
		}

		if( !empty( $get_data['trip_excerpt'] ) ){
			$my_post['post_excerpt'] = $get_data['trip_excerpt'];
		}

		if( !empty( $get_data['trip_overview'] ) ){ 
			$my_post['meta_input']['wp_travel_overview'] = $get_data['trip_overview'];
		}

		if( !empty( $get_data['trip_outline'] ) ){ 
			$my_post['meta_input']['wp_travel_outline'] = $get_data['trip_outline'];
		}

		if( !empty( $get_data['trip_enable_fixed_departure'] ) ){ 
			$my_post['meta_input']['wp_travel_fixed_departure'] = $get_data['trip_enable_fixed_departure'];
		}

		if( !empty( $get_data['trip_duration'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_duration'] = $get_data['trip_duration'];
		}

		if( !empty( $get_data['trip_duration_night'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_duration_night'] = $get_data['trip_duration_night'];
		}

		if( !empty( $get_data['trip_include'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_include'] = $get_data['trip_include'];
		}

		if( !empty( $get_data['trip_exclude'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_exclude'] = $get_data['trip_exclude'];
		}

		if( !empty( $get_data['trip_gallery'] ) ){ 
			$my_post['meta_input']['wp_travel_itinerary_gallery_ids'] = $get_data['trip_gallery'];
		}

		if( !empty( $get_data['trip_featured_image'] ) ){ 
			$my_post['meta_input']['_thumbnail_id'] = $get_data['trip_featured_image'];
		}

		if( !empty( $get_data['trip_enable_location_lan_lat'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_map_use_lat_lng'] = $get_data['trip_enable_location_lan_lat'];
		}

		if( !empty( $get_data['trip_location'] ) ){ 
			$my_post['meta_input']['wp_travel_location'] = $get_data['trip_location'];
		}

		if( !empty( $get_data['trip_lat'] ) ){ 
			$my_post['meta_input']['wp_travel_lat'] = $get_data['trip_lat'];
		}

		if( !empty( $get_data['trip_lng'] ) ){ 
			$my_post['meta_input']['wp_travel_lng'] = $get_data['trip_lng'];
		}

		if( !empty( $get_data['trip_itinerary'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_itinerary_data'] = $get_data['trip_itinerary'];
		}

		if( !empty( $get_data['trip_faqs'] ) ){ 
			$my_post['meta_input']['wptravel_trip_faqs'] = $get_data['trip_faqs'];
		}

		if( !empty( $get_data['trip_enable_global_tabs'] ) ){ 
			$my_post['meta_input']['wp_travel_use_global_tabs'] = $get_data['trip_enable_global_tabs'];
		}

		if( !empty( $get_data['trip_tab_overview_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['overview']['label'] = $get_data['trip_tab_overview_label'];
		}

		if( !empty( $get_data['trip_tab_overview_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['overview']['show_in_menu'] = ( $get_data['trip_tab_overview_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_outline_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['outline']['label'] = $get_data['trip_tab_outline_label'];
		}

		if( !empty( $get_data['trip_tab_outline_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['outline']['show_in_menu'] = ( $get_data['trip_tab_outline_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_includes_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['includes']['label'] = $get_data['trip_tab_includes_label'];
		}

		if( !empty( $get_data['trip_tab_includes_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['includes']['show_in_menu'] = ( $get_data['trip_tab_includes_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_excludes_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['excludes']['label'] = $get_data['trip_tab_excludes_label'];
		}

		if( !empty( $get_data['trip_tab_excludes_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['excludes']['show_in_menu'] = ( $get_data['trip_tab_excludes_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_gallery_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['gallery']['label'] = $get_data['trip_tab_gallery_label'];
		}

		if( !empty( $get_data['trip_tab_gallery_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['gallery']['show_in_menu'] = ( $get_data['trip_tab_gallery_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_reviews_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['reviews']['label'] = $get_data['trip_tab_reviews_label'];
		}

		if( !empty( $get_data['trip_tab_reviews_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['reviews']['show_in_menu'] = ( $get_data['trip_tab_reviews_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_booking_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['booking']['label'] = $get_data['trip_tab_booking_label'];
		}

		if( !empty( $get_data['trip_tab_booking_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['booking']['show_in_menu'] = ( $get_data['trip_tab_booking_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_faq_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['faq']['label'] = $get_data['trip_tab_faq_label'];
		}

		if( !empty( $get_data['trip_tab_faq_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['faq']['show_in_menu'] = ( $get_data['trip_tab_faq_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_downloads_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['downloads']['label'] = $get_data['trip_tab_downloads_label'];
		}

		if( !empty( $get_data['trip_tab_downloads_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['downloads']['show_in_menu'] = ( $get_data['trip_tab_downloads_enable'] == 'true' ) ? true : false;
		}

		if( !empty( $get_data['trip_tab_guide_label'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['guide']['label'] = $get_data['trip_tab_guide_label'];
		}

		if( !empty( $get_data['trip_tab_guide_enable'] ) ){ 
			$my_post['meta_input']['wp_travel_tabs']['guide']['show_in_menu'] = ( $get_data['trip_tab_guide_enable'] == 'true' ) ? true : false;
		}


		if( !empty( $get_data['trip_enable_global_enquiry'] ) ){ 
			$my_post['meta_input']['wp_travel_use_global_trip_enquiry_option'] = $get_data['trip_enable_global_enquiry'];
		}

		if( !empty( $get_data['trip_enable_enquiry'] ) ){ 
			$my_post['meta_input']['wp_travel_enable_trip_enquiry_option'] = $get_data['trip_enable_enquiry'];
		}

		if( !empty( $get_data['trip_checkout_page_info_label'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_checkout_page_info_label'] = $get_data['trip_checkout_page_info_label'];
		}

		if( !empty( $get_data['trip_checkout_page_info'] ) ){ 
			$my_post['meta_input']['wp_travel_trip_checkout_page_info'] = $get_data['trip_checkout_page_info'];
		}

		if( !empty( $get_data['enable_trip_inventory'] ) ){ 
			$my_post['meta_input']['enable_trip_inventory'] = $get_data['enable_trip_inventory'];
		}

		if( !empty( $get_data['trip_inventory_pax_limit_type'] ) ){ 
			$my_post['meta_input']['wp_travel_inventory_pax_limit_type'] = $get_data['trip_inventory_pax_limit_type'];
		}

		if( !empty( $get_data['trip_inventory_sold_out_action'] ) ){ 
			$my_post['meta_input']['wp_travel_inventory_sold_out_action'] = $get_data['trip_inventory_sold_out_action'];
		}

		if( !empty( $get_data['trip_inventory_size'] ) ){ 
			$my_post['meta_input']['wp_travel_inventory_size'] = $get_data['trip_inventory_size'];
		}

		if( !empty( $get_data['trip_inventory_sold_out_message'] ) ){ 
			$my_post['meta_input']['wp_travel_inventory_sold_out_message'] = $get_data['trip_inventory_sold_out_message'];
		}

		if( !empty( $get_data['inventory_counts_paid_only'] ) ){ 
			$my_post['meta_input']['inventory_counts_paid_only'] = $get_data['inventory_counts_paid_only'];
		}

		if( !empty( $get_data['trip_enable_global_enquiry'] ) ){ 
			$my_post['meta_input']['wp_travel_use_global_trip_enquiry_option'] = $get_data['trip_enable_global_enquiry'];
		}

		if( !empty( $get_data['trip_enable_enquiry'] ) ){ 
			$my_post['meta_input']['wp_travel_enable_trip_enquiry_option'] = $get_data['trip_enable_enquiry'];
		}

		if( !empty( $get_data['trip_partial_payout'] ) ){ 
			$my_post['meta_input']['wp_travel_minimum_partial_payout_use_global'] = $get_data['trip_partial_payout'];
		}

		if( !empty( $get_data['minimum_partial_payout_percent'] ) ){ 
			$my_post['meta_input']['wp_travel_minimum_partial_payout_percent'] = $get_data['minimum_partial_payout_percent'];
		}

		if( !empty( $get_data['trip_downloads'] ) ){ 
			$my_post['meta_input']['wp_travel_downloads'] = $get_data['trip_downloads'];
		}


		// Insert the post into the database
		$new_id = wp_update_post( $my_post );

		if ( $new_id ) {
			return "Trip Update Sucessfully";
		}else{
			return "Failed Update Trip";
		}

	}

	public function wp_travel_get_itinerary( WP_REST_Request $request ){

		// $trip_data = array(
		// 	'trip_id' => get_post( $request->get_param( 'id' ) )->ID,
		// 	'trip_title' => get_post( $request->get_param( 'id' ) )->post_title,
		// 	'trip_excerpt' => get_post( $request->get_param( 'id' ) )->post_excerpt,
		// 	'trip_status' => get_post( $request->get_param( 'id' ) )->post_status,
		// 	'trip_comment_status' => get_post( $request->get_param( 'id' ) )->comment_status,
		// 	'trip_author' => get_post( $request->get_param( 'id' ) )->post_author,
		// 	'trip_date' => get_post( $request->get_param( 'id' ) )->post_date,
		// 	'trip_date_gmt' => get_post( $request->get_param( 'id' ) )->post_date_gmt,
		// 	'trip_overview' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_overview', true ),
		// 	'trip_outline' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_outline', true ),
		// 	'trip_enable_fixed_depature' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_fixed_departure', true ),
		// 	'trip_duration' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_duration', true ),
		// 	'trip_duration_night' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_duration_night', true ),
		// 	'trip_include' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_include', true ),
		// 	'trip_exclude' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_exclude', true ),
		// 	'trip_gallery' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_itinerary_gallery_ids', true ),
		// 	'trip_featured_image' => get_post_meta( $request->get_param( 'id' ), '_thumbnail_id', true ),
		// 	'trip_enable_location_lan_lat' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_map_use_lat_lng', true ),
		// 	'trip_location' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_location', true ),
		// 	'trip_lat' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_lat', true ),
		// 	'trip_lng' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_lng', true ),
		// 	'trip_itinerary' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_itinerary_data', true ),
		// 	'trip_faqs' => get_post_meta( $request->get_param( 'id' ), 'wptravel_trip_faqs', true ),
		// 	'trip_enable_global_tabs' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_use_global_tabs', true ),
		// 	'trip_tabs' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_tabs', true ),
		// 	'trip_enable_global_enquiry' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_use_global_trip_enquiry_option', true ),
		// 	'trip_enable_enquiry' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_enable_trip_enquiry_option', true ),

		// 	'trip_checkout_page_info_label' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_checkout_page_info_label', true ),

		// 	'trip_checkout_page_info' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_checkout_page_info', true ),

		// 	'enable_trip_inventory' => get_post_meta( $request->get_param( 'id' ), 'enable_trip_inventory', true ),

		// 	'trip_inventory_pax_limit_type' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_pax_limit_type', true ),

		// 	'trip_inventory_sold_out_action' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_sold_out_action', true ),

		// 	'trip_inventory_sold_out_action' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_sold_out_action', true ),

		// 	'trip_inventory_size' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_size', true ),

		// 	'trip_inventory_sold_out_message' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_sold_out_message', true ),

		// 	'inventory_counts_paid_only' => get_post_meta( $request->get_param( 'id' ), 'inventory_counts_paid_only', true ),

		// 	'trip_downloads' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_downloads', true ),

		// 	'trip_partial_payout' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_minimum_partial_payout_use_global', true ),

		// 	'trip_partial_payout_percent' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_minimum_partial_payout_percent', true ),

		// 	'minimum_partial_payout_percent' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_minimum_partial_payout_percent', true ),

		// 	'trip_booking_count' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_booking_count', true ),

		// 	'trip_faq_question' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_faq_question', true ),

		// 	'trip_faq_answer' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_faq_answer', true ),

		// 	'trip_start_date' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_duration_start_date', true ),

		// 	'trip_end_date' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_duration_end_date', true ),

		// 	'trip_facts' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_facts', true ),

		// 	'trip_group_size' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_group_size', true ),

		// 	'trip_price' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_price', true ),

		// 	'trip_enable_sale' => get_post_meta( $request->get_param( 'id' ), 'wptravel_enable_sale', true ),


		// 	'trip_cuttOffTime' => get_post_meta( $request->get_param( 'id' ), 'cuttOffTime', true ),

		// 	'trip_guide' => get_post_meta( $request->get_param( 'id' ), 'selected_guides', true ),

		// 	'trip_pricing_type' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_pricing_option_type', true ),

		// 	'trip_booking_form' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_custom_booking_form', true ),

		// 	'trip_booking_link' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_custom_booking_link', true ),

		// 	'trip_booking_link_open_in_new_tab' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_custom_booking_link_open_in_new_tab', true ),

		// 	'trip_max_inventory' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_inventory_custom_max_pax', true ),

		// 	'trip_global_faq' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_utils_use_global_faq_for_trip', true ),

		// 	'trip_faq' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_utils_use_trip_faq_for_trip', true ),

		// 	'trip_code' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_code', true ),

		// );

		return WpTravel_Helpers_Trips::get_trip( $request->get_param( 'id' ) );
	}

	public function wp_travel_get_all_itinerary( WP_REST_Request $request ){

		$trip_datas = array();
		$get_enquiries = get_posts( array( 'post_type'  => 'itineraries' ) );

		foreach ( $get_enquiries as $value ) {

			$trip_datas[] = WpTravel_Helpers_Trips::get_trip( $value->ID );

		}

		return $trip_datas;
	}

}

new WP_Travel_API_Itinerary();