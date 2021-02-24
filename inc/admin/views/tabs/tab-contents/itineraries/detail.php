<?php
/**
 * Callback function of detail tab.
 *
 * @package WP Travel
 */

/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */
function wptravel_trip_callback_detail() {

	global $post;

	$settings                = wptravel_get_settings();
	$enable_custom_trip_code = isset( $settings['enable_custom_trip_code_option'] ) ? $settings['enable_custom_trip_code_option'] : 'no';

	$trip_code            = wptravel_get_trip_code( $post->ID );
	$trip_code_disabled   = '';
	$trip_code_input_name = 'name=wp_travel_trip_code';
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) || 'yes' !== $enable_custom_trip_code ) :
		$trip_code_disabled   = 'disabled=disabled';
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
					<?php printf( '%1$s<a href="https://wptravel.io/downloads/wp-travel-utilities/" target="_blank" class="wp-travel-upsell-badge">%2$s</a>', esc_html__( 'Need Custom Trip Code? Check', 'wp-travel' ), esc_html__( 'Utilities addons', 'wp-travel' ) ); ?>
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
				<textarea name="excerpt" id="excerpt" cols="30" rows="10"><?php echo wp_kses_post( $post->post_excerpt ); ?></textarea>
				<p class="description">
					<?php printf( '%1$s<a href="https://codex.wordpress.org/Excerpt" target="_blank" class="wp-travel-upsell-badge">%2$s</a>', esc_html__( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme.', 'wp-travel' ), esc_html__( 'Learn more about manual excerpts', 'wp-travel' ) ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
	wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
}
