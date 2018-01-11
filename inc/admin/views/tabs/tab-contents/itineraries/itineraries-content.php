<?php
/**
 * Trip Itineraries Tab Content.
 */
global $post;
$trip_itinerary_data_arr = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data' );
?>

                    <div class="itinerary_wrap" id="itinerary_wrap_">

                    <table class="form-table">
                        <tbody>
                            <tr>
                                <td>
                                     <label>Label</label>
                                </td>
                                <td>
                                    <input type="text" name="wp_travel_trip_itinerary_data[wp_travel_itinerary_data_89179][label]" value="">
                                </td>
                            </tr>
                            <tr>
                                <td>
                                     <label>Title</label>
                                </td>
                                <td>
                                    <input type="text" name="wp_travel_trip_itinerary_data[wp_travel_itinerary_data_89179][title]" value="">
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <label for="wp-travel-detail">Description</label>
                                    <?php // add editor ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="text-align: right;">
                                    <a href="javascript:void(null);" class="button button-small remove_itinery"> Remove</a>
                                </td>
                            </tr>
                        </tbody>

                    </table>

                    </div>
                    
    <div id="itinerary">
        <div class="itinerary_block">
            <?php
            if( isset( $trip_itinerary_data_arr['0'] ) && ! empty( $trip_itinerary_data_arr['0'] ) ) {
            $cnt = 0;

                foreach($trip_itinerary_data_arr['0'] as $key => $itinerary){

                        $cnt++;

                        if (array_key_exists("label",$itinerary) && $itinerary['label'] != ''){
                            $itinerary_txt = stripslashes( $itinerary['label'] ); 
                        } else{
                            $itinerary_txt = __('Day', 'wp_travel') .$cnt;
    					}
    					$itinerary_label = @$itinerary['label'];
    					$itinerary_title = @$itinerary['title'];
                        ?>
                    <div class="itinerary_wrap" id="itinerary_wrap_<?php echo $cnt;?>">
                        <div class="itinerary_row">
                            <div class="itinerary_col">
                                <label><?php _e('Label','wp_travel');?></label>
                                    <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>"> 
                            </div>
                            <div class="itinerary_col">
                                <label><?php _e('Title','wp_travel');?></label>
                                <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
                            </div>
                        </div>	

                        <div class="itinerary_row">
                            <label><?php _e('Description','wp_travel');?></label>
                            <div class="itinerary-editor">
                                <?php
                                $itinerary_settings = array(
                                'quicktags' 	=> array('buttons' => 'em,strong,link'),
                                'quicktags' 	=> true,
                                'tinymce' 		=> true,
                                'textarea_rows'	=> 10,
                                'textarea_name' => 'wp_travel_trip_itinerary_data['.$cnt.'][desc]'
                            );
                                $itinerary_desc = stripslashes( @$itinerary['desc'] );
        						
        						wp_editor($itinerary_desc,'itinerary_desc'.$cnt, $itinerary_settings); ?>					
                            </div>
                        </div>
                        <div class="itinerary_row"> 
                            <a href="javascript:void(null);" class="button button-small remove_itinery"> <?php _e('Remove', 'wp_travel'); ?></a> 
                        </div>
                    </div>
                <?php
                }		
            }  ?>					
        </div>
    </div>
</table>
<h4> 
    <a href="javascript:void(null);" id="add_itinerary_row" class="button button-primary button-small" > <?php _e('Add Day', 'wp_travel'); ?> </a> 
</h4>