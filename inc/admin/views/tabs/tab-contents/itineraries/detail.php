<?php
/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */
function wp_travel_trip_callback_detail() {

	global $post;

	$settings = wp_travel_get_settings();

	$enable_custom_trip_code_option = isset( $settings['enable_custom_trip_code_option'] ) ? $settings['enable_custom_trip_code_option'] : 'no';

	$trip_code = wp_travel_get_trip_code( $post->ID );
	$trip_code_disabled = '';
	$trip_code_input_name = 'name=wp_travel_trip_code';
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) || 'yes' !== $enable_custom_trip_code_option ) :
		$trip_code_disabled = 'disabled=disabled';
		$trip_code_input_name = '';
	endif;
	?>
	<table class="form-table">
		<tr>
			<td><label for="wp-travel-detail"><?php esc_html_e( 'Trip Code', 'wp-travel' ); ?></label></td>
			<td>
				<input type="text" id="wp-travel-trip-code" <?php echo esc_html( $trip_code_input_name ); ?> <?php echo esc_html( $trip_code_disabled ); ?> value="<?php echo esc_attr( $trip_code ); ?>" />
				<?php if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) : ?>
				<p class="description">
					<?php printf( __( 'Need Custom Trip Code? Check %s Utilities addons%s', 'wp-travel' ), '<a href="https://wptravel.io/downloads/wp-travel-utilities/" target="_blank" class="wp-travel-upsell-badge">', '<a>' ); ?>
				</p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h4><?php esc_html_e( 'Overview', 'wp-travel' ); ?></h4>
				<?php wp_editor( $post->post_content, 'content' ); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<h4><label for="excerpt"><?php esc_html_e( 'Short Description', 'wp-travel' ); ?></label></h4>
				<textarea name="excerpt" id="excerpt" cols="30" rows="10"><?php echo $post->post_excerpt ?></textarea>
				<p class="description">
					<?php printf( __( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme.%s Learn more about manual excerpts%s.', 'wp-travel' ), '<a href="https://codex.wordpress.org/Excerpt" target="_blank">', '<a>' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
	wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
}
