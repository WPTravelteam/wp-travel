<?php

Class WP_Travel_API_Enquiry {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_enquiry_api_end_points' ) );

	}

	public function wp_travel_enquiry_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-enquiry',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_enquiry' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-enquiry/(?P<id>\w+)',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_enquiry' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/update-enquiry/(?P<id>\w+)',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_update_enquiry' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-enquiry/(?P<id>\w+)',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_enquiry' ),
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-enquiries',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_enquiries' ),
			)
		);
	}



	public function wp_travel_add_enquiry( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		$my_enquiry = array( 
			'post_title' => 'Enquiry - WT - ' . $get_data['enquiry_name'],
			'post_type'    => "itinerary-enquiries",
			'post_status'   => 'publish',
			'meta_input'    => array( 
				'wp_travel_post_id' =>  !empty( $get_data['enquiry_trip_id'] ) ? $get_data['enquiry_trip_id'] : '',
				'wp_travel_enquiry_name' => !empty( $get_data['enquiry_name'] ) ? $get_data['enquiry_name'] : '',
				'wp_travel_enquiry_email' => !empty( $get_data['enquiry_email'] ) ? $get_data['enquiry_email'] : '',
				'wp_travel_enquiry_query' => !empty( $get_data['enquiry_query'] ) ? $get_data['enquiry_query'] : '',
				'wp_travel_trip_enquiry_data' => array(
					'post_id' => $get_data['enquiry_trip_id'],
					'wp_travel_enquiry_name' => !empty( $get_data['enquiry_name'] ) ? $get_data['enquiry_name'] : '',
					'wp_travel_enquiry_email' => !empty( $get_data['enquiry_email'] ) ? $get_data['enquiry_email'] : '',
					'wp_travel_enquiry_query' => !empty( $get_data['enquiry_query'] ) ? $get_data['enquiry_query'] : '',
				),

			),
		);

		if( wp_insert_post( $my_enquiry ) ){
			return "Enquiry Added Sucessfully";
		}else{
			return "Failed to add enquiry";
		}
	}

	public function wp_travel_delete_enquiry ( WP_REST_Request $request ){

		if ( get_post( $request->get_param( 'id' ) )->post_type == 'itinerary-enquiries' ) {
			wp_delete_post( $request->get_param( 'id' ) );
			return "Enquiry Deleted Sucessfully";
		}else{
			return "Failed to delete enquiry. ID not exist";
		}

	}

	public function wp_travel_update_enquiry( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		// Create post object
		$enquiry_data = array(
		  	'ID' => $request->get_param( 'id' ),
		  	'post_title' => 'Enquiry - WT - ' . $get_data['enquiry_name'],
		  	'post_type'    => "itinerary-enquiries",
			'post_status'   => 'publish',
		);

		if( !empty( $get_data['enquiry_trip_id'] ) ){ 
			$enquiry_data['meta_input']['wp_travel_post_id'] = $get_data['enquiry_trip_id'];
		}

		if( !empty( $get_data['enquiry_name'] ) ){ 
			$enquiry_data['meta_input']['wp_travel_enquiry_name'] = $get_data['enquiry_name'];
		}

		if( !empty( $get_data['enquiry_email'] ) ){ 
			$enquiry_data['meta_input']['wp_travel_enquiry_email'] = $get_data['enquiry_email'];
		}

		if( !empty( $get_data['enquiry_query'] ) ){ 
			$enquiry_data['meta_input']['wp_travel_enquiry_query'] = $get_data['enquiry_query'];
		}

		$enquiry_data['meta_input']['wp_travel_trip_enquiry_data'] = array(
			'post_id' => $get_data['enquiry_trip_id'],
			'wp_travel_enquiry_name' => !empty( $get_data['enquiry_name'] ) ? $get_data['enquiry_name'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_enquiry_data', true )['wp_travel_enquiry_name'],
			'wp_travel_enquiry_email' => !empty( $get_data['enquiry_email'] ) ? $get_data['enquiry_email'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_enquiry_data', true )['wp_travel_enquiry_email'],
			'wp_travel_enquiry_query' =>  !empty( $get_data['enquiry_query'] ) ? $get_data['enquiry_query'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_enquiry_data', true )['wp_travel_enquiry_query'],
		);

		$new_id = wp_update_post( $enquiry_data );

		if ( $new_id ) {
			return "Enquiry Update Sucessfully";
		}else{
			return "Failed Update enquiry";
		}

	}

	public function wp_travel_get_enquiry( WP_REST_Request $request ){


		if (  get_post( $request->get_param( 'id' ) )->ID ) {
			$enquiry_data = array(
				'enquiry_id' => get_post( $request->get_param( 'id' ) )->ID,
				'enquiry_name' => get_post( $request->get_param( 'id' ) )->post_title,
				'enquiry_date' => get_post( $request->get_param( 'id' ) )->post_date,
				'enquiry_date_gmt' => get_post( $request->get_param( 'id' ) )->post_date_gmt,
				'enquiry_metas' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_trip_enquiry_data', true ),
			);

			return $enquiry_data;
		}else{
			return "Enquiry ID not found";
		}
		
	}

	public function wp_travel_get_enquiries( WP_REST_Request $request ){

		$enquiry_datas = array();
		$get_enquiries = get_posts( array( 'post_type'  => 'itinerary-enquiries' ) );

		foreach ( $get_enquiries as $value ) {

			$enquiry_datas[] = array(
				'enquiry_id' => $value->ID,
				'enquiry_name' => $value->post_title,
				'enquiry_date' => $value->post_date,
				'enquiry_date_gmt' => $value->post_date_gmt,
				'enquiry_metas' => get_post_meta( $value->ID, 'wp_travel_trip_enquiry_data', true ),
			);

		}

		return $enquiry_datas;
	}



}

new WP_Travel_API_Enquiry();