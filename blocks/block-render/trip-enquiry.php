<?php
/**
 * 
 * Render Callback For Trip Enquiry
 * 
 */

function wptravel_block_trip_enquiry_render( $attributes) {
	global $post;
	$show_trip_dropdown = WP_TRAVEL_POST_TYPE === $post->post_type ? false : true;
	ob_start();
	?>
	<style>
		<?php if($attributes['textColor']): ?>
			.wptravel-block-<?php echo esc_attr( $attributes['extraClass'] ); ?>{
				color: <?php echo esc_attr( $attributes['textColor'] ); ?> !important;
			}
		<?php endif; ?>
		
		<?php if( !empty( $attributes['inputBackgroundColor'] ) ||  !empty( $attributes['inputBackgroundColor']) ): ?>
			
			.wptravel-block-<?php echo esc_attr( $attributes['extraClass'] ); ?> textarea,
			.wptravel-block-<?php echo esc_attr( $attributes['extraClass'] ); ?> input:not(.button){
				background-color: <?php echo esc_attr( $attributes['inputBackgroundColor'] ); ?>!important;
				border-color: <?php echo esc_attr( $attributes['inputBackgroundColor'] ); ?>!important;
			}
		<?php endif; ?>

		<?php if( !empty( $attributes['btnTextColor'] ) ||  !empty( $attributes['btnBorderColor']) || !empty( $attributes['btnBackgroundColor']) || !empty( $attributes['btnBorderRadius']) ): ?>
			.wptravel-block-<?php echo esc_attr( $attributes['extraClass'] ); ?> input.button{
				color: <?php echo esc_attr( $attributes['btnTextColor'] ); ?> !important;
				background-color: <?php echo esc_attr( $attributes['btnBackgroundColor'] ); ?> !important;
				border: 1px solid;
				border-color: <?php echo esc_attr( $attributes['btnBorderColor'] ); ?> !important;
				border-radius: <?php echo esc_attr( $attributes['btnBorderRadius'] ); ?>px !important;
			}
		<?php endif; ?>
		
	</style>
	<?php
	echo '<div id="wptravel-block-trip-enquiry" class="wptravel-block-wrapper wptravel-block-trip-enquiry wptravel-block-'.$attributes['extraClass'].'">';
	if ( function_exists( 'wptravel_get_enquiries_form' ) ) {
		wptravel_get_enquiries_form( $show_trip_dropdown );
	} else {
		wp_travel_get_enquiries_form( $show_trip_dropdown );
	}
	echo '</div>';
	$html = ob_get_clean();

	return $html;
}
