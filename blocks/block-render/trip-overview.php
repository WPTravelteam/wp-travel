<?php
/**
 * 
 * Render Callback For Trip Overview
 * 
 */

function wptravel_block_trip_overview_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();
	$content = is_array( $tab_data ) && isset( $tab_data['overview'] ) && isset( $tab_data['overview']['content'] ) ? $tab_data['overview']['content'] : '';
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	
	echo '<div id="wptravel-block-trip-overview" class="wptravel-block-wrapper wptravel-block-trip-overview '.$class.'">'; 
	echo wpautop( do_shortcode( $content ) ); 
	echo '</div>';

	return ob_get_clean();
}
