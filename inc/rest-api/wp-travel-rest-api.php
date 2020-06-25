<?php
/**
 * @package WP_Travel
 * @subpackage REST_API
 * 
 * @since 4.0.5
 */

require_once 'class-wp-travel-rest-controller.php';
require_once 'class-wp-travel-rest-trips-controller.php';

add_action( 'rest_api_init', 'wp_travel_init_api_controllers' );
/**
 * Inititalizes API Controllers.
 * 
 * @since 4.0.5
 */
function wp_travel_init_api_controllers() {
    // Trips Routes.
    $controller = new WP_Travel_REST_Trips_Controller( WP_TRAVEL_POST_TYPE );
    $controller->register_routes();
}