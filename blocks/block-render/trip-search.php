<?php
/**
 * 
 * Render Callback For Trip Search
 * 
 */

function wptravel_block_trip_search_render( $attributes ){

	$show_input     = isset( $attributes['showInput'] ) ? $attributes['showInput'] : true; 
	$show_trip_type = isset( $attributes['showTripType'] ) ? $attributes['showTripType'] : true; 
	$show_location  = isset( $attributes['showLocation'] ) ? $attributes['showLocation'] : true; 

	$args = array(
		'show_input'     => $show_input,
		'show_trip_type' => $show_trip_type,
		'show_location'  => $show_location,
	);

	ob_start(); ?>
	<div id="wptravel-block-trip-search" class="wptravel-block-wrapper  wptravel-block-trip-search wptravel-block-preview">
		<?php wptravel_search_form( $args ); ?>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;

}