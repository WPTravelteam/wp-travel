<?php

Class WP_Travel_API_Trip_Extras {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_trip_extras_api_end_points' ) );

	}

	public function wp_travel_trip_extras_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-trip-extras',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_trip_extras' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-trip-extras/(?P<id>\w+)',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_trip_extras' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/update-trip-extras/(?P<id>\w+)',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_update_trip_extras' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-trip-extras/(?P<id>\w+)',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_trip_extras' ),
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-all-trip-extras',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_all_trip_extras' ),
			)
		);
	}



	public function wp_travel_add_trip_extras( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		$my_trip_extras = array( 
			'post_title' => !empty( $get_data['trip_extras_title'] ) ? $get_data['trip_extras_title'] : '',
			'post_type'    => "tour-extras",
			'post_status'   => 'publish',
			'meta_input'    => array( 
				'_thumbnail_id' => !empty( $get_data['trip_extras_featured_image'] ) ? $get_data['trip_extras_featured_image'] : '',
				'wp_travel_extras_gallery_ids' =>  !empty( $get_data['trip_extras_gallery'] ) ? $get_data['trip_extras_gallery'] : '',
				'wp_travel_tour_extras_metas' => array(
					'extras_item_description' => !empty( $get_data['trip_extras_item_description'] ) ? $get_data['trip_extras_item_description'] : '',
					'extras_item_price' => !empty( $get_data['trip_extras_item_price'] ) ? $get_data['trip_extras_item_price'] : '',
					'extras_item_sale_price' => !empty( $get_data['trip_extras_sale_price'] ) ? $get_data['trip_extras_sale_price'] : '',
					'extras_is_required' => !empty( $get_data['trip_extras_is_required'] ) ? $get_data['trip_extras_is_required'] : '',
					'extras_item_unit' => !empty( $get_data['trip_extras_unit'] ) ? $get_data['trip_extras_unit'] : '',
				),

			),
		);

		if( wp_insert_post( $my_trip_extras ) ){
			return "Trip Extras Added Sucessfully";
		}else{
			return "Failed to add trip extras";
		}
	}

	public function wp_travel_delete_trip_extras ( WP_REST_Request $request ){

		if ( get_post( $request->get_param( 'id' ) )->post_type == 'tour-extras' ) {
			wp_delete_post( $request->get_param( 'id' ) );
			return "Trip Extras Deleted Sucessfully";
		}else{
			return "Failed to delete trip extras. ID not exist";
		}

	}

	public function wp_travel_update_trip_extras( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		// Create post object
		$trip_extras_data = array(
		  	'ID' => $request->get_param( 'id' ),
		  	'post_type'    => "tour-extras",
			'post_status'   => 'publish',
		);

		if( !empty( $get_data['trip_extras_title'] ) ){ 
			$trip_extras_data['post_title'] = $get_data['trip_extras_title'];
		}

		if( !empty( $get_data['trip_extras_featured_image'] ) ){ 
			$trip_extras_data['meta_input']['_thumbnail_id'] = $get_data['trip_extras_featured_image'];
		}


		if( !empty( $get_data['trip_extras_gallery'] ) ){ 
			$trip_extras_data['meta_input']['wp_travel_extras_gallery_ids'] = $get_data['trip_extras_gallery'];
		}

		$trip_extras_data['meta_input']['wp_travel_tour_extras_metas'] = array(
			'extras_item_description' => !empty( $get_data['trip_extras_item_description'] ) ? $get_data['trip_extras_item_description'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_tour_extras_metas', true )['extras_item_description'],

			'extras_item_price' => !empty( $get_data['trip_extras_item_price'] ) ? $get_data['trip_extras_item_price'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_tour_extras_metas', true )['extras_item_price'],

			'extras_item_sale_price' => !empty( $get_data['trip_extras_sale_price'] ) ? $get_data['trip_extras_sale_price'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_tour_extras_metas', true )['extras_item_sale_price'],

			'extras_is_required' => !empty( $get_data['trip_extras_is_required'] ) ? $get_data['trip_extras_is_required'] : '',

			'extras_item_unit' => !empty( $get_data['trip_extras_unit'] ) ? $get_data['trip_extras_unit'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_tour_extras_metas', true )['extras_item_unit'],
		);

		$new_id = wp_update_post( $trip_extras_data );

		if ( $new_id ) {
			return "Trip Extras Update Sucessfully";
		}else{
			return "Failed Update trip extras";
		}

	}

	public function wp_travel_get_trip_extras( WP_REST_Request $request ){


		if (  get_post( $request->get_param( 'id' ) )->ID ) {
			$trip_extras_data = array(
				'trip_extras_id' => get_post( $request->get_param( 'id' ) )->ID,
				'trip_extras_title' => get_post( $request->get_param( 'id' ) )->post_title,
				'trip_extras_status' => get_post( $request->get_param( 'id' ) )->post_status,
				'trip_extras_author' => get_post( $request->get_param( 'id' ) )->post_author,
				'trip_extras_date' => get_post( $request->get_param( 'id' ) )->post_date,
				'trip_extras_date_gmt' => get_post( $request->get_param( 'id' ) )->post_date_gmt,
				'trip_extras_featured_image' => get_post_meta( $request->get_param( 'id' ), '_thumbnail_id', true ),
				'trip_extras_gallery' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_extras_gallery_ids', true ),
				'trip_extras_metas' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_tour_extras_metas', true ),
			);

			return $trip_extras_data;
		}else{
			return "Trip Extras ID not found";
		}
		
	}

	public function wp_travel_get_all_trip_extras( WP_REST_Request $request ){

		$trip_extras_datas = array();
		$get_enquiries = get_posts( array( 'post_type'  => 'tour-extras' ) );

		foreach ( $get_enquiries as $value ) {

			$trip_extras_datas[] = array(
				'trip_extras_id' => $value->ID,
				'trip_extras_title' => $value->post_title,
				'trip_extras_status' => $value->post_status,
				'trip_extras_author' => $value->post_author,
				'trip_extras_date' => $value->post_date,
				'trip_extras_date_gmt' => $value->post_date_gmt,
				'trip_extras_featured_image' => get_post_meta( $value->ID, '_thumbnail_id', true ),
				'trip_extras_gallery' => get_post_meta( $value->ID, 'wp_travel_extras_gallery_ids', true ),
				'trip_extras_metas' => get_post_meta( $value->ID, 'wp_travel_tour_extras_metas', true ),
			);

		}

		return $trip_extras_datas;
	}



}

new WP_Travel_API_Trip_Extras();