<?php
/**
 * 
 * Render Callback For Trip Excludes
 * 
 */

function wptravel_block_trip_excludes_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();
	$content = is_array( $tab_data ) && isset( $tab_data['trip_excludes'] ) && isset( $tab_data['trip_excludes']['content'] ) ? $tab_data['trip_excludes']['content'] : '';
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	echo '<div id="wptravel-block-trip-excludes" class="wptravel-block-wrapper wptravel-block-trip-excludes '.$class.'">'; //@phpcs:ignore
	echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
	echo '</div>'; //@phpcs:ignore

	return ob_get_clean();
}
