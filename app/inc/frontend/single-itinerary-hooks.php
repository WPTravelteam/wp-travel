<?php
class WP_Travel_Single_Itinerary_Hooks{
    public static function init(){
        add_action( 'wp_travel_single_trip_after_booknow', array( __CLASS__, 'replace_booknow_button' ) );
    }

    public static function replace_booknow_button(){
        echo '<div id="wp-travel-booking-widget"></div>';
    }
}

// WP_Travel_Single_Itinerary_Hooks::init();