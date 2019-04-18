<?php
/**
 * Detail Tab HTML.
 *
 * @package wp-travel\inc\admin\views\tabs\tab-contents\itineraries
 */
function trip_callback_detail() {

	global $post;
	$trip_code = wp_travel_get_trip_code( $post->ID );
	$trip_code_disabled = '';
	$trip_code_input_name = 'name=wp_travel_trip_code';
	if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) :
		$trip_code_disabled = 'disabled=disabled';
		$trip_code_input_name = '';
	endif;
	?>

	<div class="form_field">
		<label class="label_title" for="wp-travel-trip-code"><?php echo esc_html__( 'Trip Code', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<input type="text" id="wp-travel-trip-code" <?php echo esc_html( $trip_code_input_name ); ?> <?php echo esc_html( $trip_code_disabled ); ?> value="<?php echo esc_attr( $trip_code ); ?>" />
			<?php if ( ! class_exists( 'WP_Travel_Utilities_Core' ) ) : ?>
				<figcaption>
					<?php printf( __( 'Need Custom Trip Code? Check %s Utilities addons%s', 'wp-travel' ), '<a href="https://wptravel.io/downloads/wp-travel-utilities/" target="_blank" class="wp-travel-upsell-badge">', '</a>' ); ?>
				</figcaption>
			<?php endif; ?>
		</div>
	</div>

	<div class="form_field">
		<label class="label_title" for="content"><?php echo esc_html__( 'Overview', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<?php wp_editor( $post->post_content, 'content' ); ?>
		</div>		
	</div>
	<div class="form_field">
		<label class="label_title" for="excerpt"><?php echo esc_html__( 'Short Description', 'wp-travel' ); ?></label>
		<div class="subject_input">
			<textarea name="excerpt" id="excerpt" cols="30" rows="10"><?php echo $post->post_excerpt ?></textarea>
			
			<figcaption>
				<?php printf( __( 'Excerpts are optional hand-crafted summaries of your content that can be used in your theme.%s Learn more about manual excerpts%s.', 'wp-travel' ), '<a href="https://codex.wordpress.org/Excerpt" target="_blank">', '<a>' ); ?>
			</figcaption>
		</div>
	</div>	
	<?php
	wp_nonce_field( 'wp_travel_save_data_process', 'wp_travel_save_data' );
}
