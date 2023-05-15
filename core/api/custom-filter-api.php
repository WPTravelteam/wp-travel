<?php

Class WP_Travel_API_Custom_Filter {

	public $api_version = 'v1';
	
	public function __construct(){
		add_action( 'rest_api_init', array( $this, 'wp_travel_custom_filter_api_end_points' ) );
	}

	public function wp_travel_custom_filter_api_end_points(){

		register_rest_route(
			'wptravel/v1',
			'/add-custom-filter',
			array(
				'methods'             => 'post',
				'callback'            => array( $this, 'wp_travel_add_custom_filter' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/delete-custom-filter',
			array(
				'methods'             => 'delete',
				'callback'            => array( $this, 'wp_travel_delete_custom_filter' ),
				'permission_callback' => function ($request) {
                        if (current_user_can('edit_others_posts'))
                        return true;
                }
			)
		);

		register_rest_route(
			'wptravel/v1',
			'/get-custom-filters',
			array(
				'methods'             => 'get',				
				'callback'            => array( $this, 'wp_travel_get_custom_filter' ),
				'permission_callback' => '__return_true',
			)
		);

	}



	public function wp_travel_add_custom_filter( WP_REST_Request $request ){

		global $wpdb;

		$get_data = $request->get_body_params();

		$prepared_option_name = $wpdb->prepare( "SELECT option_name FROM wp_options" );
		$get_option_value = $wpdb->get_col( $prepared_option_name );

		if ( in_array( 'wp_travel_custom_filters_option', $get_option_value ) ) {

			$prepared_statement = $wpdb->prepare( "SELECT option_value FROM wp_options WHERE option_name='wp_travel_custom_filters_option'" );
			$custom_filter_data = unserialize  ( $wpdb->get_col( $prepared_statement )[0] );

			$custom_filter_data[$get_data['filter_label']] = array(
				'label' => str_replace( ' ', '-', strtolower( $get_data['filter_label'] ) ) ,
				'hierarchical' => !empty( $get_data['hierarchical'] ) ? $get_data['hierarchical'] : 0,
				'show_admin_column' => !empty( $get_data['show_admin_column'] ) ? $get_data['show_admin_column'] : 0,
			);

		}else{
			$custom_filter_data[$get_data['filter_label']] = array(
				'label' => str_replace( ' ', '-', strtolower( $get_data['filter_label'] ) ) ,
				'hierarchical' => 1,
				'show_admin_column' => 1,
			);
		}		

		if ( update_option( 'wp_travel_custom_filters_option', $custom_filter_data ) ) {
			return "Filter Added Sucessfully";
		}else{
			return "Failed to add filter";
		}
	}

	public function wp_travel_delete_custom_filter ( WP_REST_Request $request ){

		global $wpdb;

		$prepared_statement = $wpdb->prepare( "SELECT option_value FROM wp_options WHERE option_name='wp_travel_custom_filters_option'" );
		$custom_filter_data = unserialize  ( $wpdb->get_col( $prepared_statement )[0] );

		unset( $custom_filter_data[$request->get_param( 'filter_label' )] );

		if ( update_option( 'wp_travel_custom_filters_option', $custom_filter_data ) ) {
			return "Filter delete Sucessfully";
		}else{
			return "Failed to delete filter";
		}


	}

	public function wp_travel_get_custom_filter( WP_REST_Request $request ){

		global $wpdb;

		$prepared_option_name = $wpdb->prepare( "SELECT option_name FROM wp_options" );
		$get_option_value = $wpdb->get_col( $prepared_option_name );

		if ( in_array( 'wp_travel_custom_filters_option', $get_option_value ) ) {

			$prepared_statement = $wpdb->prepare( "SELECT option_value FROM wp_options WHERE option_name='wp_travel_custom_filters_option'" );
			$option_value = unserialize  ( $wpdb->get_col( $prepared_statement )[0] );

			return $option_value;
		}else{
			return "No custom filter added to show";
		}
		
	}

}

new WP_Travel_API_Custom_Filter();