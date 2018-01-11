<?php
	global $post;
	$group_size = get_post_meta( $post->ID, 'wp_travel_group_size', true );
	$outline 	= get_post_meta( $post->ID, 'wp_travel_outline', true );
	$trip_include = get_post_meta( $post->ID, 'wp_travel_trip_include', true );
	$trip_exclude = get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
	$start_date	= get_post_meta( $post->ID, 'wp_travel_start_date', true );
	$end_date 	= get_post_meta( $post->ID, 'wp_travel_end_date', true );
	
	$fixed_departure = get_post_meta( $post->ID, 'wp_travel_fixed_departure', true );
	$fixed_departure = ( $fixed_departure ) ? $fixed_departure : 'yes';
	$fixed_departure = apply_filters( 'wp_travel_fixed_departure_defalut', $fixed_departure );

	$trip_duration = get_post_meta( $post->ID, 'wp_travel_trip_duration', true );
	$trip_duration = ( $trip_duration ) ? $trip_duration : 0;

	echo '<div class="trip-type-wrap itineraries-tax-wrap">';
	post_categories_meta_box( $post, array( 'args' => array( 'taxonomy' => 'itinerary_types' ) ) );
	printf( '<div class="tax-edit"><a href="' . esc_url( admin_url( 'edit-tags.php?taxonomy=itinerary_types&post_type=itineraries' ) ) . '">%s</a></div>', esc_html__( 'Edit All Trip Type', 'wp-travel' ) );
	echo '</div>';	
?>
<table class="form-table">
	
	<tr>
		<td><label for="wp-travel-detail"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label></td>
		<td><input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" /></td>
	</tr>
	<tr>
		<td><label for="wp_travel_outline"><?php esc_html_e( 'Outline', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $outline, 'wp_travel_outline' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_trip_include"><?php esc_html_e( 'Trip Includes', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_include, 'wp_travel_trip_include' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp_travel_trip_exclude"><?php esc_html_e( 'Trip Excludes', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_exclude, 'wp_travel_trip_exclude' ); ?></td>
	</tr>
	<tr>
		<td><label for="wp-travel-fixed-departure"><?php esc_html_e( 'Fixed Departure', 'wp-travel' ); ?></label></td>
		<td><input type="checkbox" name="wp_travel_fixed_departure" id="wp-travel-fixed-departure" value="yes" <?php checked( 'yes', $fixed_departure ) ?> /></td>
	</tr>
	<tr class="wp-travel-trip-duration-row" style="display:<?php echo ( 'no' === $fixed_departure ) ? 'table-row' : 'none'; ?>">
		<td><label for="wp-travel-trip-duration"><?php esc_html_e( 'Trip Duration', 'wp-travel' ); ?></label></td>
		<td><input type="number" min="0" step="1" name="wp_travel_trip_duration" id="wp-travel-trip-duration" value="<?php echo esc_attr( $trip_duration ); ?>" /> <?php esc_html_e( 'Days', 'wp-travel' ) ?></td>
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
