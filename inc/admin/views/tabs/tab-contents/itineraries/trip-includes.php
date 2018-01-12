<?php
	global $post;
	$trip_include = get_post_meta( $post->ID, 'wp_travel_trip_include', true );
?>
<table class="form-table">	
	<tr>
		<td><label for="wp_travel_trip_include"><?php esc_html_e( 'Trip Includes', 'wp-travel' ); ?></label></td>
		<td><?php wp_editor( $trip_include, 'wp_travel_trip_include' ); ?></td>
	</tr>
</table>
