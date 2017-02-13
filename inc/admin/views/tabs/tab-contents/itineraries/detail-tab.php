<?php
/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

global $post;
$trip_code = wp_traval_get_trip_code( $post->ID ); ?>
<table class="form-table">
	<tr>
		<td><label for="wp-travel-detail"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></label></td>
		<td><input type="text" id="wp-travel-trip-code" disabled="disabled" value="<?php echo esc_html( $trip_code ); ?>" /></td>
	</tr>
	<tr>
		<td colspan="2"><label for="wp-travel-detail"><?php esc_html_e( 'Detail', 'wp-travel' ); ?></label><?php wp_editor( $post->post_content, 'wp_travel_editor' ); ?></td>
	</tr>	
</table>
<?php
wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
