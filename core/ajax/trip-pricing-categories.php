<?php
class WP_Travel_Ajax_Trip_Pricings_Categories{
    public static function init(){
        // Get Cart items
        add_action( 'wp_ajax_wp_travel_remove_pricing_categories', array( __CLASS__, 'remove_pricing_categories' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_remove_pricing_categories', array( __CLASS__, 'remove_pricing_categories' ) );

        add_action( 'wp_ajax_wp_travel_remove_pricing_category', array( __CLASS__, 'remove_pricing_category' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_remove_pricing_category', array( __CLASS__, 'remove_pricing_category' ) );
    }

    public static function remove_pricing_categories(){
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }

        if( ! current_user_can( 'manage_options' )) {
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $pricing_id = ! empty( $_GET['pricing_id'] ) ? $_GET['pricing_id'] : 0;
        $response = WP_Travel_Helpers_Trip_Pricing_Categories::remove_trip_pricing_categories( $pricing_id );
        WP_Travel_Helpers_REST_API::response( $response );
    }

    public static function remove_pricing_category(){
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }

        if( ! current_user_can( 'manage_options' )) {
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_PERMISSION' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $pricing_id = ! empty( $_GET['pricing_id'] ) ? $_GET['pricing_id'] : 0;
        $category_id = ! empty( $_GET['category_id'] ) ? $_GET['category_id'] : 0;
        $response = WP_Travel_Helpers_Trip_Pricing_Categories::remove_individual_pricing_category( $pricing_id, $category_id );
        WP_Travel_Helpers_REST_API::response( $response );
    }
}

WP_Travel_Ajax_Trip_Pricings_Categories::init();