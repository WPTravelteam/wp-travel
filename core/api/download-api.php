<?php

Class WP_Travel_API_Download {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_download_api_end_points' ) );

	}

	public function wp_travel_download_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-trip-download',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_download' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-trip-download/(?P<id>\w+)',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_download' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/update-trip-download/(?P<id>\w+)',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_update_download' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-trip-download/(?P<id>\w+)',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_download' ),
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-all-trip-download',
			array(
				'methods'             => 'get',
				'permission_callback' => '__return_true',
				'callback'            => array( $this, 'wp_travel_get_all_download' ),
			)
		);
	}



	public function wp_travel_add_download( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		$my_download = array( 
			'post_title' => !empty( $get_data['download_title'] ) ? $get_data['download_title'] : '',
			'post_type'    => "wp_travel_downloads",
			'post_status'   => 'publish',
			'meta_input'    => array( 
				'_thumbnail_id' => !empty( $get_data['download_featured_image'] ) ? $get_data['download_featured_image'] : '',
				'wpt_downloads_cpt_meta' => array(
					'media_id' => !empty( $get_data['download_media_id'] ) ? $get_data['download_media_id'] : '',
					'caption' => !empty( $get_data['download_caption'] ) ? $get_data['download_caption'] : '',
				),

			),
		);

		if( wp_insert_post( $my_download ) ){
			return "Download Added Sucessfully";
		}else{
			return "Failed to add Download";
		}
	}

	public function wp_travel_delete_download ( WP_REST_Request $request ){

		if ( get_post( $request->get_param( 'id' ) )->post_type == 'wp_travel_downloads' ) {
			wp_delete_post( $request->get_param( 'id' ) );
			return "Download Deleted Sucessfully";
		}else{
			return "Failed to delete download. ID not exist";
		}

	}

	public function wp_travel_update_download( WP_REST_Request $request ){
		
		$get_data = $request->get_body_params();
		// Create post object
		$download_data = array(
		  	'ID' => $request->get_param( 'id' ),
		  	'post_type'    => "wp_travel_downloads",
			'post_status'   => 'publish',
		);

		if( !empty( $get_data['download_title'] ) ){ 
			$download_data['post_title'] = $get_data['download_title'];
		}

		if( !empty( $get_data['download_featured_image'] ) ){ 
			$download_data['meta_input']['_thumbnail_id'] = $get_data['download_featured_image'];
		}

		$download_data['meta_input']['wpt_downloads_cpt_meta'] = array(
			'media_id' => !empty( $get_data['download_media_id'] ) ? $get_data['download_media_id'] : get_post_meta( $request->get_param( 'id' ), 'wpt_downloads_cpt_meta', true )['media_id'],

			'caption' => !empty( $get_data['download_caption'] ) ? $get_data['download_caption'] : get_post_meta( $request->get_param( 'id' ), 'wpt_downloads_cpt_meta', true )['caption'],
		);

		$new_id = wp_update_post( $download_data );

		if ( $new_id ) {
			return "Download Update Sucessfully";
		}else{
			return "Failed Update Download";
		}

	}

	public function wp_travel_get_download( WP_REST_Request $request ){


		if (  get_post( $request->get_param( 'id' ) )->ID ) {
			$download_data = array(
				'download_id' => get_post( $request->get_param( 'id' ) )->ID,
				'download_title' => get_post( $request->get_param( 'id' ) )->post_title,
				'download_status' => get_post( $request->get_param( 'id' ) )->post_status,
				'download_author' => get_post( $request->get_param( 'id' ) )->post_author,
				'download_date' => get_post( $request->get_param( 'id' ) )->post_date,
				'download_date_gmt' => get_post( $request->get_param( 'id' ) )->post_date_gmt,
				'download_featured_image' => get_post_meta( $request->get_param( 'id' ), '_thumbnail_id', true ),
				'download_metas' => get_post_meta( $request->get_param( 'id' ), 'wpt_downloads_cpt_meta', true ),
			);

			return $download_data;
		}else{
			return "Download ID not found";
		}
		
	}

	public function wp_travel_get_all_download( WP_REST_Request $request ){

		$download_datas = array();
		$get_enquiries = get_posts( array( 'post_type'  => 'wp_travel_downloads' ) );

		foreach ( $get_enquiries as $value ) {

			$download_datas[] = array(
				'download_id' => $value->ID,
				'download_title' => $value->post_title,
				'download_status' => $value->post_status,
				'download_author' => $value->post_author,
				'download_date' => $value->post_date,
				'download_date_gmt' => $value->post_date_gmt,
				'download_featured_image' => get_post_meta( $value->ID, '_thumbnail_id', true ),
				'download_metas' => get_post_meta( $value->ID, 'wpt_downloads_cpt_meta', true ),
			);

		}

		return $download_datas;
	}



}

new WP_Travel_API_Download();