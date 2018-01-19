<?php
/**
 * Trip Itineraries Tab Content.
 */
global $post;
$trip_itinerary_data_arr = get_post_meta( $post->ID, 'wp_travel_trip_itinerary_data' );

$outline 	= get_post_meta( $post->ID, 'wp_travel_outline', true ); ?>

    <table class="form-table">
        <tr>
            <td><?php wp_editor( $outline, 'wp_travel_outline' ); ?></td>
        </tr>        
    </table>

    <div id="tab-accordion-itineraries" class="tab-accordion">
        <div class="itinerary_block panel-group wp-travel-sorting-tabs" id="accordion-itinerary-data" role="tablist" aria-multiselectable="true">
            <h3 class="wp-travel-tab-content-title"><?php esc_html_e( 'Itineraries', 'wp-travel' ) ?></h3> 
            <?php if ( isset( $trip_itinerary_data_arr[0] ) && ! empty( $trip_itinerary_data_arr[0] ) ) : ?>
                <?php $cnt = 0; ?>
                <?php foreach ( $trip_itinerary_data_arr[0] as $key => $itinerary ) : ?>
                    <?php

					$itinerary_label = __( 'Day', 'wp-travel' ) . $cnt;
					$itinerary_title = __( 'Untitled', 'wp-travel' );
					$itinerary_desc  = __( '', 'wp-travel' );
					if ( isset( $itinerary['label'] ) && '' !== $itinerary['label'] ) {
						$itinerary_label = stripslashes( $itinerary['label'] );
					}
					if ( isset( $itinerary['title'] ) && '' !== $itinerary['title'] ) {
						$itinerary_title = stripslashes( $itinerary['title'] );
					}
					if ( isset( $itinerary['desc'] ) && '' !== $itinerary['desc'] ) {
						$itinerary_desc = stripslashes( $itinerary['desc'] );
					}
					?>
                    <div class="panel panel-default">
                        <div class="panel-heading" role="tab" id="heading-<?php echo esc_attr( $cnt ) ?>">
                            <h4 class="panel-title">
                                <div class="wp-travel-sorting-handle"></div>
                                <a role="button" data-toggle="collapse" data-parent="#accordion-itinerary-data" href="#collapse-<?php echo esc_attr( $cnt ) ?>" aria-expanded="true" aria-controls="collapse-<?php echo esc_attr( $cnt ) ?>">

								<span bind="itinerary_label_<?php echo esc_attr( $cnt ) ?>" class="itinerary-label"><?php echo esc_html( $itinerary_label ); ?></span>, 
								<span bind="itinerary_title_<?php echo esc_attr( $cnt ) ?>" class="itinerary-label"><?php echo esc_html( $itinerary_title ); ?></span>
                                <span class="collapse-icon"></span>
                                </a>
                                <span class="dashicons dashicons-no-alt hover-icon wt-accordion-close"></span>
                            </h4>
                        </div>
                        <div id="collapse-<?php echo esc_attr( $cnt ) ?>" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading-<?php echo esc_attr( $cnt ) ?>">
                        <div class="panel-body">
                            <div class="panel-wrap">
                                <label><?php esc_html_e( 'Label', 'wp-travel' ); ?></label>
                                <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][label]" value="<?php echo esc_html( $itinerary_label ); ?>">
                            </div>
                            <div class="panel-wrap">
                                <label><?php esc_html_e( 'Title', 'wp-travel' ); ?></label>
                                <input type="text" name="wp_travel_trip_itinerary_data[<?php echo $cnt; ?>][title]" value="<?php echo esc_html( $itinerary_title ); ?>">
                            </div>
							<div class="wp-travel-itinerary" style="padding:10px">
								<?php
									$itinerary_settings = array(
									'quicktags' 	=> array( 'buttons' => 'em, strong, link' ),
									'quicktags' 	=> true,
									'tinymce' 		=> true,
									'textarea_rows'	=> 10,
									'textarea_name' => 'wp_travel_trip_itinerary_data[' . $cnt . '][desc]',
								);
								$itinerary_desc = stripslashes( $itinerary_desc );
								wp_editor( $itinerary_desc, 'itinerary_desc' . $cnt, $itinerary_settings ); 
								?>
							</div>
                        </div>
                        </div>
                    </div>
                <?php $cnt++; ?>
                <?php endforeach; ?>
            <?php endif; ?>  
        </div>
    </div>
    <h4> 
        <a href="javascript:void(null);" id="add_itinerary_row" class="button button-primary button-small" >
            <?php _e('Add Day', 'wp-travel'); ?> 
        </a> 
    </h4>
