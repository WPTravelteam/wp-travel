<?php
/**
 * Trip Itineraries Tab Content.
 */
global $post;
$trip_itinerary_data_arr = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data' );
$outline 	= get_post_meta( $post->ID, 'wp_travel_outline', true );

$start_date	= get_post_meta( $post->ID, 'wp_travel_start_date', true );
$end_date 	= get_post_meta( $post->ID, 'wp_travel_end_date', true );

$fixed_departure = get_post_meta( $post->ID, 'wp_travel_fixed_departure', true );
$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

$trip_duration = get_post_meta( $post->ID, 'wp_travel_trip_duration', true );
$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
$trip_duration_night = get_post_meta( $post->ID, 'wp_travel_trip_duration_night', true );
$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;

?>

    <table class="form-table">
        <tr>
            <td><label for="wp_travel_outline"><?php esc_html_e( 'Outline', 'wp-travel' ); ?></label></td>
            <td><?php wp_editor( $outline, 'wp_travel_outline' ); ?></td>
        </tr>
        <tr>
            <td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
            <td><input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ) ?> /></td>
        </tr>
        <tr class="wp-travel-trip-duration-row" style="display:<?php echo ( 'no' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
            <td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
            <td>
                <input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Days', 'wp-travel' ) ?>
                <input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night', 'wp-travel' ) ?>                
            </td>
        </tr>        
        
        <tr class="wp-travel-fixed-departure-row" style="display:<?php echo ( 'yes' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
            <td><label for="wp-travel-start-date"><?php esc_html_e( 'Starting date', 'wp-travel' ); ?></label></td>
            <td><input type="text" name="wp_travel_start_date" id="wp-travel-start-date" value="<?php echo esc_attr( $start_date ); ?>" /></td>
        </tr>
        <tr class="wp-travel-fixed-departure-row" style="display:<?php echo ( 'yes' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
            <td><label for="wp_travel_end_date"><?php esc_html_e( 'Ending date', 'wp-travel' ); ?></label></td>
            <td><input type="text" name="wp_travel_end_date" id="wp-travel-end-date" value="<?php echo esc_attr( $end_date ); ?>" /></td>
        </tr>
    </table>
                 
<?php
    if( isset( $trip_itinerary_data_arr['0'] ) && ! empty( $trip_itinerary_data_arr['0'] ) ) {

    $cnt = 0;

?>
<div class="itinerary_block">
    
    <?php foreach ( $trip_itinerary_data_arr['0'] as $key => $itinerary ) {

    if (array_key_exists("label",$itinerary) && $itinerary['label'] != ''){
        $itinerary_txt = stripslashes( $itinerary['label'] ); 
    } else{
        $itinerary_txt = __('Day', 'wp_travel') .$cnt;
    }
    $itinerary_label = @$itinerary['label'];
    $itinerary_title = @$itinerary['title'];
?>
    <div class="itinerary_wrap" id="itinerary_wrap_<?php echo $cnt; ?>">

        <table class="form-table">
            <tbody>
                <tr>
                    <td>
                        <label><?php _e('Label','wp_travel');?></label>
                    </td>
                    <td>
                        <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
                    </td>
                </tr>
                <tr>
                    <td>
                        <label><?php _e('Title','wp_travel');?></label>
                    </td>
                    <td>
                        <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <label><?php _e('Description','wp_travel');?></label>
                        <?php
                            $itinerary_settings = array(
                            'quicktags' 	=> array('buttons' => 'em,strong,link'),
                            'quicktags' 	=> true,
                            'tinymce' 		=> true,
                            'textarea_rows'	=> 10,
                            'textarea_name' => 'wp_travel_trip_itinerary_data['.$cnt.'][desc]'
                        );
                            $itinerary_desc = stripslashes( @$itinerary['desc'] );
                            
                            wp_editor($itinerary_desc,'itinerary_desc'.$cnt, $itinerary_settings); 
                        ?>		
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="text-align: right;">
                        <a href="javascript:void(null);" class="button button-small remove_itinery"><?php _e('Remove', 'wp_travel'); ?></a>
                    </td>
                </tr>
            </tbody>

        </table>

    </div>            
<?php
$cnt++;
    }
?>
</div>
<?php 	
}  
?>
<h4> 
    <a href="javascript:void(null);" id="add_itinerary_row" class="button button-primary button-small" > <?php _e('Add Day', 'wp_travel'); ?> </a> 
</h4>
