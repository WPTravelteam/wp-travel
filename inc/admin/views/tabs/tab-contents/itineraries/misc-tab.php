<?php
	global $post;
	
    $enable_trip_enquiry_option = get_post_meta( $post->ID, 'wp_travel_enable_trip_enquiry_option', true );

		$use_global_trip_enquiry_option = get_post_meta( $post->ID, 'wp_travel_use_global_trip_enquiry_option', true );
		if ( '' ===  $use_global_trip_enquiry_option ) {
			$use_global_trip_enquiry_option = 'yes';
		}
?>
<table class="form-table">
    <tr>
		<td width="40%"><label for="wp-travel-use-global-trip-enquiry"><?php esc_html_e( 'Use Global Trip Enquiry Option', 'wp-travel' ); ?></label></td>
		<td width="60%">
			<input name="wp_travel_use_global_trip_enquiry_option" type="hidden"  value="no">
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_use_global_trip_enquiry_option" type="checkbox" id="wp-travel-use-global-trip-enquiry" <?php checked( $use_global_trip_enquiry_option, 'yes' ); ?> value="yes" />							
					<span class="switch">
				  </span>
				 
				</label>
			</span>
		</td>
	</tr>
	<tr id="wp-travel-enable-trip-enquiry-option-row" >
		<td width="40%"><label for="wp-travel-enable-trip-enquiry-option"><?php esc_html_e( 'Trip Enquiry', 'wp-travel' ); ?></label></td>
		<td width="60%">
			<span class="show-in-frontend checkbox-default-design">
				<label data-on="ON" data-off="OFF">
					<input name="wp_travel_enable_trip_enquiry_option" type="checkbox" id="wp-travel-enable-trip-enquiry-option" <?php checked( $enable_trip_enquiry_option, 'yes' ); ?> value="yes" />			
					<span class="switch">
				  </span>
				 
				</label>
			</span>
			 <span class="wp-travel-enable-trip-enquiry checkbox-with-label"><?php esc_html_e( 'Check to enable trip enquiry for this trip.', 'wp-travel' ); ?></span>
			
		</td>
	</tr>
</table>
