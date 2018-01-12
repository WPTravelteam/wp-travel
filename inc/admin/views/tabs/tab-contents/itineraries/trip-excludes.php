<?php
	global $post;	
	$trip_exclude = get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
?>
<table class="form-table">	
	<tr>
		<td><label for="wp_travel_trip_exclude"><?php esc_html_e( 'Trip Excludes', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_exclude, 'wp_travel_trip_exclude' ); ?></td>
	</tr>	
</table>
