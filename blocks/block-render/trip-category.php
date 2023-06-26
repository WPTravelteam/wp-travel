<?php

/**
 * 
 * Render Callback For Trip Category
 * 
 */
function wptravel_block_trip_category_render( $attributes ) {

	$trip_id  = get_the_ID();
	$taxonomy = isset( $attributes['tripTaxonomy'] ) ? $attributes['tripTaxonomy'] : 'itinerary_types';
	$terms    = get_the_term_list( $trip_id, $taxonomy, '', ', ', '' );
	$align = ! empty( $attributes['textAlign'] ) ? $attributes['textAlign'] : 'left';
	$class = sprintf( ' has-text-align-%s', $align );
	$extra_class = $attributes['extraClass'];
	ob_start();
	?>
	<style>
		<?php if( !empty( $attributes['textColor'] ) ): ?>
			.wptravel-block-<?php echo esc_attr($attributes['extraClass']) ?> a{
				color: <?php echo esc_attr($attributes['textColor']); ?> !important;
			}
		<?php endif; ?>
		
	</style>
	<div id="wptravel-block-trip-categories" class="wptravel-block-wrapper wptravel-block-trip-categories wptravel-block-<?php echo esc_attr( $extra_class ); ?> <?php echo esc_attr( $class ); ?>">
		<div class="travel-info <?php echo esc_attr( $taxonomy ); ?>">
			<span class="value">

				<?php
					if( !get_the_id() ){
					?>
						<a href="#" rel="tag"><?php echo __( 'Trip Type', 'wp-travel-block-pro' ); ?></a>
					<?php
						
					}else{
						if( get_post()->post_type == 'itineraries' ){
							if ( $terms ) {
								echo wp_kses( $terms, wptravel_allowed_html( array( 'a' ) ) );
							} else {
								echo 'N/A';
							}
						}else{ ?>
							<a href="#" rel="tag"><?php echo __( 'Trip Type', 'wp-travel-block-pro' ); ?></a>
						<?php }
						
					}
				
				?>
			</span>
		</div>
	</div>
	<?php
	$html = ob_get_clean();

	return $html;
}