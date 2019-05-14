<?php
/**
 * Template file for WP Travel Misc tab.
 *
 * @package WP Travel
 */

/**
 * Callback Function For Misc Content Tabs
 *
 * @param string $tab  tab name 'misc'.
 * @param array  $args arguments function arugments.
 * @return Mixed
 */
function wp_travel_trip_callback_misc_options() {
	global $post;

	$enable_trip_enquiry_option = get_post_meta( $post->ID, 'wp_travel_enable_trip_enquiry_option', true );

		$use_global_trip_enquiry_option = get_post_meta( $post->ID, 'wp_travel_use_global_trip_enquiry_option', true );
	if ( '' === $use_global_trip_enquiry_option ) {
		$use_global_trip_enquiry_option = 'yes';
	}
	?>
	<h3><?php _e( 'Trip Enquiry', 'wp-travel' ); ?></h3>
	<table class="form-table">
		<tr>
			<td><label for="wp-travel-use-global-trip-enquiry"><?php esc_html_e( 'Global Trip Enquiry Option', 'wp-travel' ); ?></label></td>
			<td>
				<input name="wp_travel_use_global_trip_enquiry_option" type="hidden"  value="no">
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input name="wp_travel_use_global_trip_enquiry_option" type="checkbox" id="wp-travel-use-global-trip-enquiry" <?php checked( $use_global_trip_enquiry_option, 'yes' ); ?> value="yes" />							
						<span class="switch">
					</span>
					
					</label>
				</span>
				<p class="description"><?php esc_html_e( 'Use Global Trip Enquiry Option from setting.', 'wp-travel' ); ?></a>
			</td>
		</tr>
		<tr id="wp-travel-enable-trip-enquiry-option-row" >
			<td><label for="wp-travel-enable-trip-enquiry-option"><?php esc_html_e( 'Trip Enquiry', 'wp-travel' ); ?></label></td>
			<td>
				<span class="show-in-frontend checkbox-default-design">
					<label data-on="ON" data-off="OFF">
						<input name="wp_travel_enable_trip_enquiry_option" type="checkbox" id="wp-travel-enable-trip-enquiry-option" <?php checked( $enable_trip_enquiry_option, 'yes' ); ?> value="yes" />			
						<span class="switch">
					</span>
					
					</label>
				</span>
				<p class="description"><?php esc_html_e( 'Check to enable trip enquiry for this trip.', 'wp-travel' ); ?></p>
				
			</td>
		</tr>
	</table>
	<?php
}
