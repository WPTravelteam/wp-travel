<?php
	global $post;
	$group_size = get_post_meta( $post->ID, 'wp_travel_group_size', true );
	?>
<table class="form-table">
	
	<tr>
		<td><label for="wp-travel-detail"><?php esc_html_e( 'Group Size', 'wp-travel' ); ?></label></td>
		<td><input min="1" type="number" id="wp-travel-group-size" name="wp_travel_group_size" placeholder="<?php esc_attr_e( 'No of PAX', 'wp-travel' ); ?>" value="<?php echo esc_attr( $group_size ); ?>" /></td>
	</tr>
	
</table>
