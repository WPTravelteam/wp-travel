<?php

Class WP_Travel_API_Coupon {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_coupon_api_end_points' ) );
	}

	public function wp_travel_coupon_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-coupon',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_coupon' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-coupon/(?P<id>\w+)',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_coupon' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/update-coupon/(?P<id>\w+)',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_update_coupon' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-coupon/(?P<id>\w+)',
			array(
				'methods'             => 'get',
				'callback'            => array( $this, 'wp_travel_get_coupon' ),
				'permission_callback' => '__return_true',
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-coupons',
			array(
				'methods'             => 'get',
				'callback'            => array( $this, 'wp_travel_get_coupons' ),
				'permission_callback' => '__return_true',
			)
		);
	}



	public function wp_travel_add_coupon( WP_REST_Request $request ){

		$get_data = $request->get_body_params();


		$my_coupon = array( 
			'post_title' => $get_data['coupon_title'],
			'post_type'    => "wp-travel-coupons",
			'post_status'   => 'publish',
			'meta_input'    => array( 
				'wp_travel_coupon_code' =>  !empty( $get_data['coupon_code'] ) ? $get_data['coupon_code'] : '',
				'wp_travel_coupon_metas' => array(
					'general' 		=> array(
						'coupon_code' 	=> !empty( $get_data['coupon_code'] ) ? $get_data['coupon_code'] : '',
						'coupon_value'	=> !empty( $get_data['coupon_value'] ) ? $get_data['coupon_value'] : '',
						'coupon_expiry_date' 	=> !empty( $get_data['coupon_expiry_date'] ) ? $get_data['coupon_expiry_date'] : '',
					),
					'restriction' 	=> array(
						'restricted_trips' => !empty( $get_data['restricted_trips'] ) ? $get_data['restricted_trips'] : '',
						'coupon_limit_number' => !empty( $get_data['coupon_limit_number'] ) ? $get_data['coupon_limit_number'] : '',
					),
				),

			),
		);

		if( wp_insert_post( $my_coupon ) ){
			return $my_coupon;
		}else{
			return "Failed to add coupon";
		}

	}

	public function wp_travel_delete_coupon ( WP_REST_Request $request ){

		if ( get_post( $request->get_param( 'id' ) )->post_type == 'wp-travel-coupons' ) {
			wp_delete_post( $request->get_param( 'id' ) );
			return "Coupon Deleted Sucessfully";
		}else{
			return "Failed to delete coupon. ID not exist";
		}

	}

	public function wp_travel_update_coupon( WP_REST_Request $request ){

		$get_data = $request->get_body_params();

		// Create post object
		$coupon_data = array(
		  	'ID' => $request->get_param( 'id' ),
		  	'post_type'    => "wp-travel-coupons",
			'post_status'   => 'publish',
		);

		if( !empty( $get_data['coupon_title'] ) ){ 
			$coupon_data['post_title'] = $get_data['coupon_title'];
		}

		if( !empty( $get_data['coupon_code'] ) ){ 
			$coupon_data['meta_input']['wp_travel_coupon_code'] = $get_data['coupon_code'];
		}


		$coupon_data['meta_input']['wp_travel_coupon_metas'] = array(

			'general' 		=> array(
				'coupon_code' 	=> !empty( $get_data['coupon_code'] ) ? $get_data['coupon_code'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true )['coupon_code'],
				'coupon_value'	=> !empty( $get_data['coupon_value'] ) ? $get_data['coupon_value'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true )['coupon_value'],
				'coupon_expiry_date' 	=> !empty( $get_data['coupon_expiry_date'] ) ? $get_data['coupon_expiry_date'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true )['coupon_expiry_date'],
			),

			'restriction' 	=> array(
				'restricted_trips' => !empty( $get_data['restricted_trips'] ) ? $get_data['restricted_trips'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true )['restricted_trips'],
				'coupon_limit_number' => !empty( $get_data['coupon_limit_number'] ) ? $get_data['coupon_limit_number'] : get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true )['coupon_limit_number'],
			),

		);

		$new_id = wp_update_post( $coupon_data );

		if ( $new_id ) {
			return "Coupon Update Sucessfully";
		}else{
			return "Failed Update coupon";
		}

	}

	public function wp_travel_get_coupon( WP_REST_Request $request ){


		if (  get_post( $request->get_param( 'id' ) )->ID ) {
			$coupon_data = array(
				'coupon_id' => get_post( $request->get_param( 'id' ) )->ID,
				'coupon_title' => get_post( $request->get_param( 'id' ) )->post_title,
				'coupon_status' => get_post( $request->get_param( 'id' ) )->post_status,
				'coupon_author' => get_post( $request->get_param( 'id' ) )->post_author,
				'coupon_date' => get_post( $request->get_param( 'id' ) )->post_date,
				'coupon_date_gmt' => get_post( $request->get_param( 'id' ) )->post_date_gmt,
				'coupon_metas' => get_post_meta( $request->get_param( 'id' ), 'wp_travel_coupon_metas', true ),
			);

			return $coupon_data;
		}else{
			return "coupon ID not found";
		}
		
	}

	public function wp_travel_get_coupons( WP_REST_Request $request ){

		$coupon_datas = array();
		$get_enquiries = get_posts( array( 'post_type'  => 'wp-travel-coupons', 'posts_per_page' => -1 ) );

		foreach ( $get_enquiries as $value ) {

			$coupon_datas[] = array(
				'coupon_id' => $value->ID,
				'coupon_title' => $value->post_title,
				'coupon_status' => $value->post_status,
				'coupon_author' => $value->post_author,
				'coupon_date' => $value->post_date,
				'coupon_date_gmt' => $value->post_date_gmt,
				'coupon_metas' => get_post_meta( $value->ID, 'wp_travel_coupon_metas', true ),
			);

		}

		return $coupon_datas;
	}



}

new WP_Travel_API_Coupon();