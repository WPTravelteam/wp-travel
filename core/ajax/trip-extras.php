<?php
class WP_Travel_Ajax_Trip_Extras{
    public static function init(){
        add_action( 'wp_ajax_wp_travel_get_trip_extras', array( __CLASS__, 'get_trip_extras' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_get_trip_extras', array( __CLASS__, 'get_trip_extras' ) );

        add_action( 'wp_ajax_wp_travel_search_trip_extras', array( __CLASS__, 'search_trip_extras' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_search_trip_extras', array( __CLASS__, 'search_trip_extras' ) );
    }

    public static function get_trip_extras() {
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $response = WP_Travel_Helpers_Trip_Extras::get_trip_extras();
        WP_Travel_Helpers_REST_API::response( $response );
    }

    public static function search_trip_extras() {
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $args = array();
        $args['s'] = ! empty( $_GET['keyword'] ) ? esc_attr( $_GET['keyword'] ) : '';
        $response = WP_Travel_Helpers_Trip_Extras::get_trip_extras( $args );
        WP_Travel_Helpers_REST_API::response( $response );
    }

}

WP_Travel_Ajax_Trip_Extras::init();