<?php
/**
 * Tour extras General Tab Contents
 * 
 * @package WP Travel
 */

function wp_travel_tour_extras_general_tab_callback() {

	global $post;
	$post_id = $post->ID;

	$trip_extras_data = get_post_meta( $post_id, 'wp_travel_tour_extras_metas', true );

	if ( ! $trip_extras_data )
		$trip_extras_data = array();

	/**
	 * Set Vars.
	 */
	$item_desc       = isset( $trip_extras_data['extras_item_description'] ) && ! empty( $trip_extras_data['extras_item_description'] ) ? $trip_extras_data['extras_item_description']  : '';

	?>
	<table class="form-table">
		<tbody>
			<tr>
				<td><label for="extras-item-description"><?php esc_html_e( 'Description', 'wp-travel' ); ?></label>
					<span class="tooltip-area" title="<?php esc_html_e( 'Description for the service/item', 'wp-travel' ); ?>">
						<i class="wt-icon wt-icon-question-circle" aria-hidden="true"></i>
					</span>
				</td>
				<td>
					<textarea name="wp_travel_extras[extras_item_description]" id="extras-item-description" cols="50" rows="5"><?php echo esc_html( $item_desc ); ?></textarea>
				</td>
			</tr>
			<?php do_action( 'wp_travel_extras_pro_options' ); ?>
		</tbody>
	</table>
	<?php
 }