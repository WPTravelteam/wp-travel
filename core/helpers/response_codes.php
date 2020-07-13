<?php
class WP_Travel_Helpers_Response_Codes{
    public static function get_success_codes() {
        $codes = array(
            // Trips
            'WP_TRAVEL_UPDATED_TRIP' => array(
                'message' => __( 'Successfully updated trip.', 'wp-travel' ),
            ),

            // Trip Pricings
            'WP_TRAVEL_REMOVED_TRIP_PRICING' => array(
                'message' => __( 'Successfully removed trip pricing.', 'wp-travel' ),
            ),

            // Categories
            'WP_TRAVEL_REMOVED_TRIP_PRICING_CATEGORIES' => array(
                'message' => __( 'Successfully removed trip pricing categories.', 'wp-travel' ),
            ),
            'WP_TRAVEL_REMOVED_TRIP_PRICING_CATEGORY' => array(
                'message' => __( 'Successfully removed trip pricing category.', 'wp-travel' ),
            ),

            // Dates
            'WP_TRAVEL_TRIP_DATES' => array(
                'message' => __( 'Successfully listed trip dates.', 'wp-travel' ),
            ),
            'WP_TRAVEL_REMOVED_TRIP_DATES' => array(
                'message' => __( 'Successfully removed trip dates.', 'wp-travel' ),
            ),
            'WP_TRAVEL_ADDED_TRIP_DATE' => array(
                'message' => __( 'Successfully added trip date.', 'wp-travel' ),
            ),
            'WP_TRAVEL_REMOVED_TRIP_DATE'            => array(
				'message' => __( 'Removed trip date successfully.', 'wp-travel' ),
			),
            // Excluded Dates & Time
            'WP_TRAVEL_TRIP_EXCLUDED_DATES_TIMES' => array(
                'message' => __( 'Successfully listed trip dates & times.', 'wp-travel' ),
            ),
            'WP_TRAVEL_UPDATED_TRIP_EXCLUDED_DATES_TIMES' => array(
                'message' => __( 'Successfully updated trip dates & times.', 'wp-travel' ),
            ),
            'WP_TRAVEL_REMOVED_TRIP_DATES' => array(
                'message' => __( 'Successfully removed trip dates.', 'wp-travel' ),
            ),
            'WP_TRAVEL_ADDED_TRIP_DATE' => array(
                'message' => __( 'Successfully added trip date.', 'wp-travel' ),
            ),

            // Cart
            'WP_TRAVEL_ADDED_TO_CART' =>array(
                'message' => __( 'Successfully added trip to cart.', 'wp-travel' ),
            ),

            // Search
            'WP_TRAVEL_FILTER_RESULTS' => array(
                'message' => __( 'Successfully listed filter result.', 'wp-travel' ),
            ),

            // Media
            'WP_TRAVEL_ATTACHMENT_DATA' => array(
                'message' => __( 'Attachment data.', 'wp-travel' ),
            ),
            'WP_TRAVEL_TRIP_TABS' => array(
                'message' => __( 'Trip Tab loaded successfully.', 'wp-travel' ),
            ),
            'WP_TRAVEL_SETTINGS' => array(
                'message' => __( 'Settings loaded successfully.', 'wp-travel' ),
            ),
            'WP_TRAVEL_UPDATED_SETTINGS' => array(
                'message' => __( 'Settings updated successfully.', 'wp-travel' ),
            ),
            'WP_TRAVEL_TRIP_IDS' => array(
                'message' => __( 'Trip ID loaded successfully.', 'wp-travel' ),
            ),
            'WP_TRAVEL_LICENSE_ACTIVATION' => array(
                'message' => __( 'License activation.', 'wp-travel' ),
            ),
            'WP_TRAVEL_LICENSE_DEACTIVATION' => array(
                'message' => __( 'License deactivation.', 'wp-travel' ),
            ),
        );

        return apply_filters( 'wp_travel_success_codes', $codes );
    }

    public static function get_success_response( $code, $data = array() ) {
        $codes = self::get_success_codes();
        if ( ! empty( $codes[ $code ] ) ) {
            $defaults = array(
                'code' => $code,
                'message' =>  $codes[ $code ]['message']
            );
            return wp_parse_args( $data, $defaults );
        }
    }
}