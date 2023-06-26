<?php
/**
 * 
 * Render Callback For Trip Group Size
 * 
 */

function wptravel_block_trip_group_size_render( $attributes = array() ) {
	$trip_id    = get_the_ID();
	$group_size = wptravel_get_group_size( $trip_id );

	$strings               = WpTravel_Helpers_Strings::get();
	$pax_text              = isset( $strings['bookings']['pax'] ) ? $strings['bookings']['pax'] : __( 'Pax', 'wp-travel-block-pro' );
	$empty_group_size_text = isset( $strings['empty_results']['group_size'] ) ? $strings['empty_results']['group_size'] : __( 'No Size Limit', 'wp-travel-block-pro' );

	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	ob_start();
	echo '<div data-align="' . esc_attr( $align ) . '" class="wptravel-block-wrapper wptravel-block-group-size wptravel-block-preview' . $class . '">';
	if( !get_the_ID() ){
		printf( apply_filters( 'wp_travel_template_group_size_text', __( '%1$d %2$s', 'wp-travel-block-pro' ) ), 3, 'pax' );
	}else{
		if( get_post()->post_type == 'itineraries' ){
			if ( (int) $group_size && $group_size < 999 ) {
				printf( apply_filters( 'wp_travel_template_group_size_text', __( '%1$d %2$s', 'wp-travel-block-pro' ) ), esc_html( $group_size ), esc_html( ( $pax_text ) ) );
			} else {
				echo esc_html( apply_filters( 'wp_travel_default_group_size_text', $empty_group_size_text ) ); 		
			}
		}else{
			printf( apply_filters( 'wp_travel_template_group_size_text', __( '%1$d %2$s', 'wp-travel-block-pro' ) ), 3, 'pax' );
		}
	}

	echo '</div>'; // @phpcs:ignore
	$html = ob_get_clean();

	return $html;
}