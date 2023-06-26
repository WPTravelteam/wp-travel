<?php
/**
 * 
 * Render Callback For Trip Map
 * 
 */

function wptravel_block_trip_map_render( $attributes ) {

	$trip_id     = get_the_ID();
	$get_maps    = wptravel_get_maps(); 
	$current_map = $get_maps['selected'];
	$align       = isset( $attributes['align'] ) ? $attributes['align'] : 'center';
	$align_class = ' align' . $align;
	
	ob_start();
	do_action( 'wptravel_trip_map_' . $current_map, $trip_id, $get_maps );
	$content   = ob_get_clean();
	$class     = ! empty( $attributes['className'] ) ? $attributes['className'] : '';
	$class     = 'wptravel-block-map ' . $class;
	$class    .= $align_class;

	return '<div id="wptravel-block-trip-map" class="wptravel-block-wrapper ' . esc_attr( $class ) . '">' . $content . '</div>';
}