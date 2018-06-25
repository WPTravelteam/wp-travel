<?php
/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */

global $post; ?>
<table class="form-table">	
	<tr>
		<td colspan="2"><?php wp_editor( $post->post_content, 'content' ); ?></td>
	</tr>	
</table>
<?php
wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
