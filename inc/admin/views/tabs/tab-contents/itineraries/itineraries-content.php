<?php
/**
 * Trip Itineraries Tab Content.
 */
global $post;
// $trip_itinerary_data_arr = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data' );
$outline 	= get_post_meta( $post->ID, 'wp_travel_outline', true ); ?>

    <table class="form-table">
        <tr>
            <td><?php wp_editor( $outline, 'wp_travel_outline' ); ?></td>
        </tr>        
    </table>
                 
<?php if ( isset( $trip_itinerary_data_arr['0'] ) && ! empty( $trip_itinerary_data_arr['0'] ) ) :
	$cnt = 0; ?>
        <div class="itinerary_block">    
        <?php
		foreach ( $trip_itinerary_data_arr['0'] as $key => $itinerary ) {
			if ( array_key_exists ( 'label', $itinerary ) && $itinerary['label'] != '' ) {
				$itinerary_txt = stripslashes( $itinerary['label'] );
			} else {
				$itinerary_txt = __( 'Day', 'wp-travel' ) . $cnt;
			}
			$itinerary_label = @$itinerary['label'];
			$itinerary_title = @$itinerary['title']; ?>

            <div class="itinerary_wrap" id="itinerary_wrap_<?php echo $cnt; ?>">

                <div id="itinerary-accordion">
                  <h3>Day 1</h3>
                  <div>
                    <p>
                    Mauris mauris ante, blandit et, ultrices a, suscipit eget, quam. Integer
                    ut neque. Vivamus nisi metus, molestie vel, gravida in, condimentum sit
                    amet, nunc. Nam a nibh. Donec suscipit eros. Nam mi. Proin viverra leo ut
                    odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.
                    </p>
                  </div>
                  <h3>Day 2</h3>
                  <div>
                    <p>
                    Sed non urna. Donec et ante. Phasellus eu ligula. Vestibulum sit amet
                    purus. Vivamus hendrerit, dolor at aliquet laoreet, mauris turpis porttitor
                    velit, faucibus interdum tellus libero ac justo. Vivamus non quam. In
                    suscipit faucibus urna.
                    </p>
                  </div>
                  
                
                </div>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <td>
                                <label><?php _e('Label','wp-travel');?></label>
                            </td>
                            <td>
                                <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <label><?php _e('Title','wp-travel');?></label>
                            </td>
                            <td>
                                <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <label><?php _e('Description','wp-travel');?></label>
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
                                <a href="javascript:void(null);" class="button button-small remove_itinery"><?php _e('Remove', 'wp-travel'); ?></a>
                            </td>
                        </tr>
                    </tbody>

                </table>

            </div>            
        <?php $cnt++;
		} ?>
    </div>
<?php endif; ?>
<!-- <h4> 
    <a href="javascript:void(null);" id="add_itinerary_row" class="button button-primary button-small" >
        <?php _e('Add Day', 'wp-travel'); ?> 
    </a> 
</h4> -->
