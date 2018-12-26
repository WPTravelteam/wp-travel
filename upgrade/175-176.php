<?php
global $wpdb;

$datepicker_default_format = 'm/d/Y';
$date_format            = get_option('date_format');

$date_migrated = get_option( 'wp_travel_date_migrate_176' );

if ( $date_migrated && 'yes' === $date_migrated ) {
    return;
}
                                            
$post_type = WP_TRAVEL_POST_TYPE;
$query1 = "SELECT ID from {$wpdb->posts}  where post_type='$post_type' and post_status in( 'publish', 'draft' )";
$post_ids = $wpdb->get_results( $query1 );
if ( is_array( $post_ids ) && count( $post_ids ) > 0 ) {
    foreach ( $post_ids as $post ) {
        $post_id = $post->ID;
        $query2 = "SELECT meta_key, meta_value from {$wpdb->postmeta} where post_id=$post_id and meta_key in ( 'wp_travel_start_date', 'wp_travel_end_date', 'wp_travel_multiple_trip_dates', 'wp_travel_trip_itinerary_data' )";
        $all_dates = $wpdb->get_results( $query2 );
        if ( is_array( $all_dates ) && count( $all_dates ) > 0 ) {
            foreach ( $all_dates as $date_data ) {
                // echo $date_data->meta_key;
                if ( 'wp_travel_start_date' === $date_data->meta_key || 'wp_travel_end_date' === $date_data->meta_key  ) {
                    // Do Nothing. Single Starting and end date is compatible.
                } else {
                    $dates = $date_data->meta_value;
                    $dates = ( $dates ) ? maybe_unserialize( $dates ) : '';
                    if ( is_array( $dates ) && count( $dates ) > 0 ) {
                        // Multiple Dates
                        if ( 'wp_travel_multiple_trip_dates' === $date_data->meta_key ) {
                            foreach ( $dates as $key => $date ) {
                                if ( '' != $date['start_date'] ) {
                                    $start_date = $date['start_date'];

                                    $date1 = DateTime::createFromFormat( $datepicker_default_format, $start_date );
                                    // Converting Date format to WP Date format.
                                    if ( $date1 )
                                        $start_date = $date1->format( $date_format );

                                    $dates[ $key ]['start_date'] = $start_date;
                                }
                                if ( '' != $date['end_date'] ) {
                                    $end_date = $date['end_date'];

                                    $date1 = DateTime::createFromFormat( $datepicker_default_format, $end_date );
                                    // Converting Date format to WP Date format.
                                    if ( $date1 )
                                        $end_date = $date1->format( $date_format );

                                    $dates[ $key ]['end_date'] = $end_date;
                                }
                            }
                        }
                        // Trip Itineraries.
                        if ( 'wp_travel_trip_itinerary_data' === $date_data->meta_key ) {
                            foreach ( $dates as $key => $date ) {
                                if ( '' != $date['date'] ) {

                                    $start_date = $date['date'];

                                    $date1 = DateTime::createFromFormat( $datepicker_default_format, $start_date );
                                    // Converting Date format to WP Date format.
                                    if ( $date1 )
                                        $start_date = $date1->format( $date_format );

                                    $dates[ $key ]['date'] = $start_date;
                                }
                                
                            }
                        }
                    }
                    $dates = maybe_serialize( $dates );
                    $update_query = "UPDATE {$wpdb->postmeta}  SET meta_value = '$dates' where post_id=$post_id and meta_key='$date_data->meta_key'";
                    $wpdb->get_results( $update_query );
                    
                }
            }
        }
    }
    update_option( 'wp_travel_date_migrate_176', 'yes' );
}
