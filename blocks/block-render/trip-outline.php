<?php
/**
 * 
 * Render Callback For Trip Outline
 * 
 */

function wptravel_block_trip_outline_render( $attributes ) {
	ob_start();
	$tab_data = wptravel_get_frontend_tabs();
	$content = is_array( $tab_data ) && isset( $tab_data['trip_outline'] ) && isset( $tab_data['trip_outline']['content'] ) ? $tab_data['trip_outline']['content'] : '';
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';

	echo '<div id="wptravel-block-trip-outline" class="wptravel-block-wrapper wptravel-block-trip-outline">'; //@phpcs:ignore
	echo wpautop( do_shortcode( $content ) ); // @phpcs:ignore
    load_template( wptravel_get_for_block_template( 'itineraries-list.php' ));
	echo '</div>'; //@phpcs:ignore
	echo '<style>';
	echo '.wptravel-block-trip-outline p{';
	echo 'text-align: '.$align.';';
	echo '}';
	echo '.wptravel-block-trip-outline .tc-content p{';
	echo 'text-align: left;';
	echo '}';
	echo '</style>';

	return ob_get_clean();
}
