<?php
	global $post;
	$group_size = get_post_meta( $post->ID, 'wp_travel_group_size', true );
	$start_date	= get_post_meta( $post->ID, 'wp_travel_start_date', true );
	$end_date 	= get_post_meta( $post->ID, 'wp_travel_end_date', true );

	$fixed_departure = get_post_meta( $post->ID, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	$trip_duration = get_post_meta( $post->ID, 'wp_travel_trip_duration', true );
	$trip_duration = ( $trip_duration ) ? $trip_duration : 0;
	$trip_duration_night = get_post_meta( $post->ID, 'wp_travel_trip_duration_night', true );
	$trip_duration_night = ( $trip_duration_night ) ? $trip_duration_night : 0;?>
<table class="form-table">
	
	<tr>
		<td><label for="wp-travel-detail"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label></td>
		<td><input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" /></td>
	</tr>
	<tr>
		<td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
		<td><input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ) ?> /></td>
	</tr>
	<tr class="wp-travel-trip-duration-row" style="display:<?php echo ( 'no' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
		<td>
			<input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Day(s)', 'wp-travel' ) ?>
			<input type="number" min="0" step="1" name="wp_travel_trip_duration_night" id="wp-travel-trip-duration-night" value="<?php echo esc_attr( $trip_duration_night ); ?>" /> <?php esc_html_e( 'Night(s)', 'wp-travel' ) ?>                
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
