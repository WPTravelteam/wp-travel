<?php
/**
 * 
 * Render Callback For Trip Includes
 * 
 */

function wptravel_block_trip_includes_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();
	$content = is_array( $tab_data ) && isset( $tab_data['trip_includes'] ) && isset( $tab_data['trip_includes']['content'] ) ? $tab_data['trip_includes']['content'] : '';
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	echo '<div id="wptravel-block-trip-includes" class="wptravel-block-wrapper wptravel-block-trip-includes '.$class.'">'; //@phpcs:ignore
	echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
	echo '</div>'; //@phpcs:ignore

	return ob_get_clean();
}
