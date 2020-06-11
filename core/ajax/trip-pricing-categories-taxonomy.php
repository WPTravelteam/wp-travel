<?php
class WP_Travel_Ajax_Trip_Pricing_Categories_Taxonomy{
    public static function init(){
        // Remove item from trip
        add_action( 'wp_ajax_wp_travel_get_trip_pricing_categories_terms', array( __CLASS__, 'get_trip_pricing_categories_terms' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_get_trip_pricing_categories_terms', array( __CLASS__, 'get_trip_pricing_categories_terms' ) );

        add_action( 'wp_ajax_wp_travel_get_trip_pricing_categories_term', array( __CLASS__, 'get_trip_pricing_categories_term' ) );
        add_action( 'wp_ajax_nopriv_wp_travel_get_trip_pricing_categories_term', array( __CLASS__, 'get_trip_pricing_categories_term' ) );
    }

    public static function get_trip_pricing_categories_terms() {
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $response = WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy::get_trip_pricing_categories_terms();
        WP_Travel_Helpers_REST_API::response( $response );
    }

    public static function get_trip_pricing_categories_term() {
        $nonce = $_REQUEST['_nonce'];
        if( ! wp_verify_nonce( $nonce, 'wp_travel_nonce' )){
            $error = WP_Travel_Helpers_Error_Codes::get_error( 'WP_TRAVEL_INVALID_NONCE' );
            WP_Travel_Helpers_REST_API::response( $error );
        }
        $category_id = ! empty( $_GET['pricing_category_id'] ) ? $_GET['pricing_category_id'] : 0;
        $response = WP_Travel_Helpers_Trip_Pricing_Categories_Taxonomy::get_trip_pricing_categories_term( $category_id );
        WP_Travel_Helpers_REST_API::response( $response );
    }
}

WP_Travel_Ajax_Trip_Pricing_Categories_Taxonomy::init();