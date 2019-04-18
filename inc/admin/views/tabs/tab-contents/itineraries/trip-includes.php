<?php

/**
 * Trip Include /Exclude Tab meta Contents.
 *
 * @package WP_Travel
 */

if ( ! function_exists( 'trip_callback_trip_includes' ) ) {
	function trip_callback_trip_includes() {
		global $post;
		$trip_include = get_post_meta( $post->ID, 'wp_travel_trip_include', true );
		$trip_exclude = get_post_meta( $post->ID, 'wp_travel_trip_exclude', true );
		?>

		<div class="form_field">
			<label class="label_title" for="wp_travel_trip_include"><?php echo esc_html__( 'Trip Includes', 'wp-travel' ); ?></label>
			<div class="subject_input">
				<?php wp_editor( $trip_include, 'wp_travel_trip_include' ); ?>
			</div>		
		</div>
		<div class="form_field">
			<label class="label_title" for="wp_travel_trip_exclude"><?php echo esc_html__( 'Trip Excludes', 'wp-travel' ); ?></label>
			<div class="subject_input">
				<?php wp_editor( $trip_include, 'wp_travel_trip_exclude' ); ?>
			</div>		
		</div>
		<?php
	}
}
	